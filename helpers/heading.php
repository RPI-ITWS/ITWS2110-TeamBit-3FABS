<?php
require_once "./helpers/db.php";
require "./helpers/urls.php";
require "./helpers/sessions.php";

function generate_header(string $title = "TeamBit-3FABS") {
    $rootURL = urlFor('/');
    $browseURL = urlFor('/browse');
    $shareURL = urlFor('/share');
    $loginURL = urlFor('/login');
    $loggedInUser = getCurrentUserInfo();
    $accountURL = urlFor('/profile/' . $loggedInUser['username'] ?? '');
    $accountLink = $loggedInUser !== null ? '<a href="' . $loginURL . '" class="navi">LOGIN</a>' : '<a href="' . $accountURL . '" class="navi">' . $loggedInUser["display_name"] .'</a>';
    $header = <<<EOT
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>$title</title>
        <link rel="stylesheet" href="./style.css">
        <link rel="icon" href="./favicon.ico" type="image/x-icon">
    </head>

    <body>
        <header>
            <ul>
            <li><a href="$rootURL" class="navi">HOME</a></li>
            <li><a href="$browseURL" class="navi">BROWSE</a></li>
            <li><a href="$shareURL" class="navi">SHARE</a></li>
            <li>$accountLink</li>
            </ul>
        </header>
        <main class="content">
EOT;
    echo $header;
}

function generate_footer() {
    $footer = <<<EOT
        </main>
        <footer>
            <p class="center">By Team 5 at ITWS 2110</p>
        </footer>
    </body>
    </html>
    EOT;
    echo $footer;
}