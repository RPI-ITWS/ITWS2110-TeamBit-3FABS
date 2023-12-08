<?php
require './helpers/heading.php';
generate_header();
?>
<?php
require_once './helpers/db.php';
global $db;
global $assetURLs;
$sort = $_GET["sort"];
// $time = $_GET["time"]; Not used yet
$sortMode = "desc";
$limit = 50;
$sortColumn = "post_created_at";
switch ($sort) {
    case "new":
        $sortColumn = "post_created_at";
        $sortMode = "desc";
        break;
    case "top":
        $sortColumn = "num_likes";
        $sortMode = "desc";
        break;
    case "old":
        $sortColumn = "post_created_at";
        $sortMode = "asc";
        break;
    case "hated":
        $sortColumn = "num_likes";
        $sortMode = "asc";
        break;
    default:
        $sortColumn = "post_created_at";
        break;
}
$userLoggedInSelect = "";
$userLoggedInJoin = "";
$userLoggedInWhere = "";
$userLoggedInParams = [];
if ($userInfo !== null) {
    $userLoggedInSelect = 'COALESCE(like_logged_in_user_subquery.is_liked, false) "logged_in_user_liked",';
    $userLoggedInJoin = 'LEFT JOIN (
        SELECT likes.post_id "like_post_id",
            TRUE "is_liked"
        FROM
            likes
        WHERE
            likes.author_id = :userId
    ) like_logged_in_user_subquery
    ON
        like_logged_in_user_subquery.like_post_id = posts.id';
    $userLoggedInWhere = 'WHERE posts.author_id NOT IN (SELECT blocks.blocker_id FROM blocks WHERE blocks.blockee_id = :userId)
    AND posts.author_id NOT IN (SELECT blocks.blockee_id FROM blocks WHERE blocks.blocker_id = :userId)';
    $userLoggedInParams = ['userId' => $userInfo['id']];
}
$postSQL = '
            SELECT
                posts.id AS "post_id",
                image_url,
                alt_text,
                users.username AS "username",
                users.display_name AS "display_name",
                posts.created_at AS "post_created_at",
                caption as "title",
                posts.updated_at AS "post_updated_at",
                COALESCE(like_subquery.num_likes, 0) AS "num_likes",
                ' . $userLoggedInSelect . '
                COALESCE(num_comments_subquery.num_comments, 0) AS "num_comments"
            FROM
                posts
            INNER JOIN users ON posts.author_id = users.id
            LEFT JOIN (
                SELECT 
                    likes.post_id "like_post_id",
                    COUNT(likes.author_id) "num_likes" 
                FROM 
                    likes
                GROUP BY likes.post_id
            ) like_subquery ON like_subquery.like_post_id = posts.id
            LEFT JOIN (
                SELECT 
                    comments.post_id "comment_post_id",
                    COUNT(comments.id) "num_comments"
                FROM
                    comments
                GROUP BY comments.post_id
            ) num_comments_subquery ON num_comments_subquery.comment_post_id = posts.id
            ' . $userLoggedInJoin . '
            ' . $userLoggedInWhere . '
            ORDER BY ' . $sortColumn . ' ' . $sortMode . '
            LIMIT ' . $limit . ';
        ';
$preparedPostQuery = $db->prepare($postSQL);
// Look, I know we're not supposed to do this but I cannot find a better way to get PHP to stop yelling at me about both the sort direction (asc/desc) or the limit

$preparedPostQuery->execute($userLoggedInParams);
$posts = $preparedPostQuery->fetchAll(PDO::FETCH_ASSOC);
$preparedPostQuery->closeCursor();
?>
<div class="posts">
<?php
foreach ($posts as $post) {
    $altText = $post["alt_text"] ?? ($post["title"] . " by " . $post["display_name"]);
    $userLiked = false;
    if (array_key_exists('logged_in_user_liked', $post)) {
        $userLiked = $post['logged_in_user_liked'];
    }
    echo '<div class="post">
                <a class="post-url" href="' . urlFor("/posts/" . $post["post_id"]) . '"><h1 class="postTitle">' . $post["title"] . '</h1>
                <img class="postImage" src="' . urlFor('/' . $post['image_url']) . '" alt="' . $altText . '">
                </a>
                <div class="footerwrap">
                    <div class="postDetails">
                        <p class="author"><a href="' . urlFor('/profile/' . $post['username']) . '">' . $post['display_name'] . '</a></p>
                        <p class="postDate">' . $post["post_created_at"] . '</p>
                        <p class="likes">Likes: ' . $post["num_likes"] . '</p>
                        <p class="comments">Comments: ' . $post["num_comments"] . '</p>
                    </div>
                    <div class="postFooter">
                        <img class="like ' . ($userLiked ? 'active' : '') . '" src="' . $assetURLs[$userLiked ? 'liked' : 'heart'] . '" onclick="like(this, ' . $post['post_id'] . ')" alt="Like button">
                    </div>
                </div>
            </div>';
}
?>
</div>
<?php generate_footer(); ?>