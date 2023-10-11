<!DOCTYPE html>
<html lang="en">
<?php require './helpers/urls.php' ?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo $_GET["sort"] . ' Posts' ?></title>
    <link rel="stylesheet" href="./style.css">
    <link rel="icon" href="./favicon.ico" type="image/x-icon">
</head>
<body>
    <header>
        <ul>
            <li><a href="<?php urlFor('/') ?>" class="navi">HOME</a></li>
            <li><a href="<?php urlFor('/browse') ?>" class="navi selected">BROWSE</a></li>
            <li><a href="<?php urlFor('/share') ?>" class="navi">SHARE</a></li>
            <li><a href="<?php urlFor('/login') ?>" class="navi">LOGIN</a></li>
        </ul>
    </header>
    <div class="contentFeed">
        <p>There should be a feed of content here, wouldnt be suprised if this becomes browse.php. You should also be able to switch between friends and general feeds</p>
        <div class="post">
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
        </div>
    </div>
    <script src="./Javascript/Functions.js"></script>
</body>

</html>