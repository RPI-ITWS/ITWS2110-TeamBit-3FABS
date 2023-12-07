<?php
require_once './helpers/db.php';
require_once './helpers/heading.php';

$username = $_GET['username'];

$userQuery = $db->prepare("SELECT * FROM users WHERE username = :username");
$userQuery->execute(['username' => $username]);
$userInfo = $userQuery->fetch(PDO::FETCH_ASSOC);

$postQuery = $db->prepare("SELECT * FROM posts WHERE author_id = :userId ORDER BY created_at");
$postQuery->execute(['userId' => $userInfo['id']]);
$posts = $postQuery->fetchAll(PDO::FETCH_ASSOC);

generate_header();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>1-Bit</title>
    <link rel="stylesheet" href="./style.css">
    <link rel="icon" href="./favicon.ico" type="image/x-icon">
</head>

<body>
    <main class="content">
        <!--<p>Where you can either log in or see your profile, and the top "login" thing would become your name if you sign in.</p>
        <p>You should also be able to copy the link up top to share your profile.</p>-->
        <section class="profile">
            <article class="about">
                <img src="./images/download.png" alt="User Avatar">
                <h1>@Ihrther</h1>
                <p>Hi! My name's Rei Ayanami! How's it hanging? I love dithering algorithms!</p>
            </article>
            <aside class="posts">
                <img src="./images/g1.png">
                <img src="./images/g2.png">
                <img src="./images/download (2).png">
                <img src="./images/g3.png">
                <img src="./images/g9.png">
                <img src="./images/g10.png">
                <img src="./images/download (3).png">
                <img src="./images/g4.png">
                <img src="./images/g5.png">
                <img src="./images/g6.png">
                <img src="./images/g7.png">
                <img src="./images/g8.png">
            </aside>
        </section>
    </main>
</body>

</html>