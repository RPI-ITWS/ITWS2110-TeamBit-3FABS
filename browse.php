<?php
require './helpers/heading.php';
generate_header();
?>
<div class="contentFeed">
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
    $preparedPostQuery = $db->prepare('
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
                -- COALESCE(like_logged_in_user_subquery.is_liked, false) "logged_in_user_liked", -- Needed in the future
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
            ORDER BY :sortColumn ' . $sortMode . '
            LIMIT ' . $limit . ';
        ');
    // Look, I know we're not supposed to do this but I cannot find a better way to get PHP to stop yelling at me about both the sort direction (asc/desc) or the limit

    $preparedPostQuery->execute(["sortColumn" => $sortColumn]);
    $posts = $preparedPostQuery->fetchAll(PDO::FETCH_ASSOC);
    $preparedPostQuery->closeCursor();
    ?>

    <?php
    foreach ($posts as $post): { ?> 
        <?$altText = $post["alt_text"] ?? ($post["title"] . " by " . $post["display_name"]);?>
        <div class="post">
                <h1 class="postTitle"> <?=$post["title"]?> </h1>
                <img class="postImage" src= <?urlFor('/' . $post['image_url']) ?> alt="' . $altText . '">
                <div class="postDetails">
                    <p class="author"><a href="<?urlFor('/profile/' . $post['username'])?>">'<? $post['display_name'] ?></a></p>
                    <p class="postDate"><? $post["post_created_at"]?></p>
                    <p class="likes">Likes: <?=$post["num_likes"]?>'</p>
                    <p class="comments">Comments: <?=$post["num_comments"] ?></p>
                </div>
                <div class="postFooter">
                    <img class="like" src=" <?=$assetURLs['heart']?>" onclick="like(this)" alt="Like button">
                </div>
            </div>
    <?php } endforeach; ?>



    <!-- <div class="post">
            <h1 class="postTitle">End of the Dither-Day</h1>
            <img class="postImage" src="./images/sunset.png">
            <div class="postFooter ">
                <img class="like" src="./images/heart.png" onclick="like(this)">
                <p class="tag"><a href="./linktoaccount">@myfriend</a></p>
                <p class="postDate">07/23/2010</p>
            </div>
        </div>
        <div class="post">
            <h1 class="postTitle">My New Friend!</h1>
            <img class="postImage" src="./images/newfriend.png">
            <div class="postFooter ">
                <img class="like" src="./images/heart.png" onclick="like(this)">
                <p class="tag"><a href="./linktoaccount">@splatlands</a></p>
                <p class="postDate">09/08/2005</p>
            </div>
        </div>
        <div class="post">
            <h1 class="postTitle">I love HRT</h1>
            <img class="postImage" src="./images/download.png">
            <div class="postFooter ">
                <img class="like" src="./images/heart.png" onclick="like(this)">
                <p class="tag"><a href="./linktoaccount">@chickoon</a></p>
                <p class="postDate">03/16/2077</p>
            </div>
        </div>
        <div class="post">
            <h1 class="postTitle">Mimikyuuuu</h1>
            <img class="postImage" src="./images/mimikyu.png">
            <div class="postFooter ">
                <img class="like" src="./images/heart.png" onclick="like(this)">
                <p class="tag"><a href="./linktoaccount">@chii28</a></p>
                <p class="postDate">08/31/2018</p>
            </div>
        </div> -->
</div>
<!-- <script src="<?php urlFor('/Javascript/Functions.js') ?>"></script> -->
<?php generate_footer(); ?>