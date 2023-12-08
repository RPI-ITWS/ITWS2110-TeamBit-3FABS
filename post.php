<?php
require "./helpers/heading.php";
require_once "./helpers/db.php";
$postQuery = $db->prepare("SELECT * FROM posts WHERE id = :postId");
$postQuery->execute(['postId' => $_GET['post_id']]);
$post = $postQuery->fetch(PDO::FETCH_ASSOC);
if ($post === false) {
    http_response_code(404);
    die("Post not found");
}
$authorQuery = $db->prepare("SELECT * FROM users WHERE id = :userId");
$authorQuery->execute(['userId' => $post['author_id']]);
$author = $authorQuery->fetch(PDO::FETCH_ASSOC);
if ($author === false) {
    http_response_code(404);
    die("Author not found");
}
$likeQuery = $db->prepare("SELECT COUNT(*) FROM likes WHERE post_id = :postId");
$likeQuery->execute(['postId' => $post['id']]);
$numLikes = $likeQuery->fetch(PDO::FETCH_NUM)[0];
$userLiked = false;
$loggedInUserInfo = getCurrentUserInfo();
$commentsLoggedInSelect = "";
$commentsLoggedInJoin = "";
$commentsLoggedInWhere = "";
$commentsLoggedInParams = array();
if ($loggedInUserInfo !== null) {
    // We are logged in as a user
    $userBlockedPosterQuery = $db->prepare("SELECT COUNT(*) FROM blocks WHERE blocker_id = :blockerId AND blockee_id = :blockeeId");
    $userBlockedPosterQuery->execute(['blockerId' => $_SESSION["userId"], 'blockeeId' => $author['id']]);
    $userBlockedPoster = $userBlockedPosterQuery->fetch(PDO::FETCH_NUM)[0] > 0;
    $posterBlockedUserQuery = $db->prepare("SELECT COUNT(*) FROM blocks WHERE blocker_id = :blockerId AND blockee_id = :blockeeId");
    $posterBlockedUserQuery->execute(['blockerId' => $author['id'], 'blockeeId' => $_SESSION["userId"]]);
    $posterBlockedUser = $posterBlockedUserQuery->fetch(PDO::FETCH_NUM)[0] > 0;
    if ($userBlockedPoster || $posterBlockedUser) {
        http_response_code(403);
        die("You are not allowed to view this post");
    }
    $userLikedQuery = $db->prepare("SELECT COUNT(*) FROM likes WHERE post_id = :postId AND author_id = :userId");
    $userLikedQuery->execute(['postId' => $post['id'], 'userId' => $loggedInUserInfo['id']]);
    $userLiked = $userLikedQuery->fetch(PDO::FETCH_NUM)[0] > 0;
    $commentsLoggedInSelect = 'COALESCE(like_logged_in_user_subquery.is_liked, false) "logged_in_user_liked",';
    $commentsLoggedInJoin = 'LEFT JOIN (
        SELECT comment_likes.comment_id "like_comment_id",
            TRUE "is_liked"
        FROM
            comment_likes
        WHERE
            comment_likes.author_id = :userId
    ) like_logged_in_user_subquery
    ON
        like_logged_in_user_subquery.like_comment_id = comments.id';
    $commentsLoggedInWhere = 'AND posts.author_id NOT IN (SELECT blocks.blocker_id FROM blocks WHERE blocks.blockee_id = :userId)
    AND posts.author_id NOT IN (SELECT blocks.blockee_id FROM blocks  WHERE blocks.blocker_id = :userId)';
    $commentsLoggedInParams = ['userId' => $_SESSION["userId"]];
}
$commentQuery = $db->prepare('SELECT 
    comments.id AS "comment_id",
    comments.created_at AS "created_at",
    comments.content AS "comment_text",
    comments.parent_comment_id AS "parent_comment_id",
    users.username AS "username",
    users.display_name AS "display_name",
    ' . $commentsLoggedInSelect . '
    COALESCE(like_subquery.num_likes, 0) AS "num_likes"
    FROM comments
    INNER JOIN users ON comments.author_id = users.id
    LEFT JOIN(
        SELECT
            comment_likes.comment_id "like_comment_id",
            COUNT(likes.author_id) "num_likes"
        FROM
            comment_likes
        GROUP BY
        comment_likes.comment_id
    ) like_subquery
    ON
        like_subquery.like_comment_id = comments.id
    ' . $commentsLoggedInJoin . '
    WHERE comments.post_id = :postId
');
$commentQuery->execute(array_merge(['postId' => $post['id']], $commentsLoggedInParams));
$comments = $commentQuery->fetchAll(PDO::FETCH_ASSOC);
$commentQuery->closeCursor();
$commentMapping = array(null => array()); // Maps comment ID to any replies
$commentCount = 0;
foreach ($comments as $comment) {
    if (!array_key_exists($comment['parent_comment_id'], $commentMapping)) {
        $commentMapping[$comment['parent_comment_id']] = array();
    }
    $commentMapping[$comment['parent_comment_id']][] = $comment;
    $commentCount++;
}
generate_header($post['caption']);
?>
<section class="post">
    <h1><?php echo htmlspecialchars($post["caption"]) ?></h1>
    <img src="<?php echo urlFor($post["image_url"]) ?>" alt="<?php echo htmlspecialchars($post["alt_text"]) ?>">
    <p>Photo by <?php echo authorURL($author["username"], $author["display_name"]) ?></p>
    <p>Posted on <?php echo $post["created_at"] ?></p>
    <p>Likes: <?php echo $numLikes ?></p>
    <div class="postFooter">
        <img class="like <?php echo ($userLiked ? 'active' : '') ?>" src="<?php echo $assetURLs['heart'] ?>" onclick="like(this, <?php echo $post['id'] ?>)" alt="Like button">
    </div>
</section>
<section class="comments">
    <h2>Comments (<?php echo $commentCount ?>)</h2>
    <?php
    function renderComment(array $comment) {
        global $assetURLs;
        global $commentMapping;
        echo '<div class="comment">
                <div class="comment-author>'. authorURL($comment['username'], $comment['display_name']) . '</div>
                <p class="comment-text">' . htmlspecialchars($comment['comment_text']) . '</p>
                <div class="comment-footer">
                    <span class="comment-date">' . $comment['created_at'] . '</span>
                    <img class="like ' . ($comment['logged_in_user_liked'] ? 'active' : '') . '" src="' . $assetURLs['heart'] . '" onclick="like(this, ' . $comment['comment_id'] . ', true)" alt="Like button"><span class="comment-likes">' . $comment['num_likes'] . 'likes </span>
                </div>';
        if (array_key_exists($comment['comment_id'], $commentMapping)) {
            echo '<div class="comment-replies">';
            foreach ($commentMapping[$comment['comment_id']] as $reply) {
                renderComment($reply);
            }
        }
        echo '</div>';
    }
    ?>
</section>
<?php generate_footer(); ?>