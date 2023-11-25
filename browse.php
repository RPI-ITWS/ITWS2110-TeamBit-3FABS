<!DOCTYPE html>
<html lang="en">
<?php require './helpers/urls.php' ?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo $_GET["sort"] . ' Posts' ?></title>
    <link rel="stylesheet" href="<?php echo urlFor('/style.css') ?>">
    <!-- <link rel="icon" href="./favicon.ico" type="image/x-icon"> -->
    <link rel="icon" href="<?php echo urlFor('/images/favicon.ico') ?>" type="image/x-icon">
</head>
<body>
    <header>
        <ul>
            <li><a href="<?php echo urlFor('/') ?>" class="navi">HOME</a></li>
            <li><a href="<?php echo urlFor('/browse') ?>" class="navi selected">BROWSE</a></li>
            <li><a href="<?php echo urlFor('/share') ?>" class="navi">SHARE</a></li>
            <li><a href="<?php echo urlFor('/login') ?>" class="navi">LOGIN</a></li>
        </ul>
    </header>
    <div class="contentFeed">
        <p>There should be a feed of content here, wouldnt be suprised if this becomes browse.php. You should also be able to switch between friends and general feeds</p>
        <?php
        require_once './helpers/db.php';
        global $db;
        global $assetURLs;
        $sort = $_GET["sort"];
        // $time = $_GET["time"]; Not used yet
        $sortMode = ($_GET["sortMode"] ?? "desc") == "desc" ? "desc" : "asc";
        $limit = 50;
        $sortColumn = "post_created_at";
        $preparedPostQuery = $db->prepare('
            SELECT
                posts.id AS "post_id",
                image_url,
                alt_text,
                users.username AS "username",
                users.display_name AS "display_name",
                posts.created_at AS "post_created_at",
                comments.content AS "title",
                comments.updated_at AS "post_updated_at",
                COALESCE(like_subquery.num_likes, 0) AS "num_likes",
                -- COALESCE(like_logged_in_user_subquery.is_liked, false) "logged_in_user_liked", -- Needed in the future
                (COALESCE(num_comments_subquery.num_comments, 1) - 1) AS "num_comments" -- This has to be - 1 because we use a comment to make the post title.
            FROM
                posts
            INNER JOIN users ON posts.author_id = users.id
            INNER JOIN comments ON posts.primary_comment_id = comments.id
            LEFT JOIN (
                SELECT 
                    likes.post_id "like_post_id",
                    COUNT(likes.author_id) "num_likes" 
                FROM 
                    likes
                GROUP BY likes.post_id
            ) like_subquery ON like_subquery.like_post_id = post_id
            LEFT JOIN (
                SELECT 
                    comments.post_id "comment_post_id",
                    COUNT(comments.id) "num_comments"
                FROM
                    comments
                GROUP BY comments.post_id
            ) num_comments_subquery ON num_comments_subquery.comment_post_id = post_id
            ORDER BY :sortColumn ' . $sortMode . '
            LIMIT '. $limit .';
        ');
        // Look, I know we're not supposed to do this but I cannot find a better way to get PHP to stop yelling at me about both the sort direction (asc/desc) or the limit

        switch ($sort) {
            case "new":
                $sortColumn = "post_created_at";
                break;
            case "top":
                $sortColumn = "num_likes";
                break;
            default:
                $sortColumn = "post_created_at";
                break;
        }

//        $preparedPostQuery->bind_param("si", $sortColumn, $limit);
        $preparedPostQuery->execute(["sortColumn" => $sortColumn]);
        $posts = $preparedPostQuery->fetchAll(PDO::FETCH_ASSOC);
        $preparedPostQuery->closeCursor();
        ?>

        <?php 
        foreach($posts as $post){
            $altText = $post["alt_text"] ?? ($post["title"] . " by " . $post["display_name"]);
            echo '<div class="post">
                <h1 class="postTitle">'. $post["title"] . '</h1>
                <img class="postImage" src="'. urlFor('/images/'. $post['image_url']) .'" alt="'. $altText . '">
                <div class="postFooter">
                    <img class="like" src="'. $assetURLs['heart'] .'" onclick="like(this)" alt="Like button">
                    <p class="tag"><a href="'. urlFor('/profile/' . $post['username']) .'">'. $post['display_name'] .'</a></p>
                    <p class="postDate">'. $post["post_created_at"] .'</p>
                </div>
            </div>';
        }
        ?>

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
    <script src="<?php urlFor('/Javascript/Functions.js') ?>"></script>
</body>

</html>