<?php
require_once "./helpers/db.php";
require "./helpers/urls.php";
require "./helpers/sessions.php";

$userInfo = null;

function generate_header(string $title = "TeamBit-3FABS") {
    global $userInfo;
    $rootURL = urlFor('/');
    $browseURL = urlFor('/browse');
    $shareURL = urlFor('/share');
    $loginURL = urlFor('/login');
    $accountCreationURL = urlFor('/create_acc');
    $loggedInUser = getCurrentUserInfo();
    if ($loggedInUser === null && ($_SERVER['REQUEST_URI'] !== $loginURL || $_SERVER['REQUEST_URI'] !== $accountCreationURL)) {
        // Make it easier for users to login
        $_SESSION['login_redirect'] = $_SERVER['REQUEST_URI'];
    } else {
        $userInfo = $loggedInUser;
    }
    $accountURL = urlFor('/profile/' . $loggedInUser['username'] ?? '');
    $accountText = "";
    if ($loggedInUser !== null) {
        $accountText = '<li><a href="' . $accountURL . '" class="navi">' . $loggedInUser["display_name"] .'</a></li>';
    } else {
        $accountText = '<li><a href="' . $loginURL . '" class="navi">LOGIN</a></li>';
        $accountText .= '<li><a href="' . $accountCreationURL . '" class="navi">CREATE ACCOUNT</a></li>';
    }
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
            $accountText
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