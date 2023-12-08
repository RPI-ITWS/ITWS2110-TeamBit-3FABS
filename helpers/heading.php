<?php
require_once "./helpers/db.php";
require "./helpers/urls.php";
require "./helpers/sessions.php";

$userInfo = null;

function generate_header(string $title = "TeamBit-3FABS", bool $isLoginPage = false) {
    global $userInfo;
    $iconURL = urlFor('/favicon.ico');
    $cssURL = urlFor('/style.css');
    $rootURL = urlFor('/');
    $browseURL = urlFor('/browse');
    $shareURL = urlFor('/login');
    $loginURL = urlFor('/login');
    $logoutURL = urlFor('/logout');
    $accountCreationURL = urlFor('/create_acc');
    $loggedInUser = getCurrentUserInfo();
    if ($loggedInUser !== NULL) {
        $shareURL = urlFor('/share.php');
    }
    if (!$isLoginPage && $loggedInUser === null) {
        // Make it easier for users to login
        $_SESSION['login_redirect'] = $_SERVER['REQUEST_URI'];
    } else {
        $userInfo = $loggedInUser;
        refreshValidity();
    }
    $accountURL = urlFor('/profile/' . ($loggedInUser !== null ? $loggedInUser['username'] : ''));
    $accountText = "";
if ($loggedInUser !== null) {
    $accountText = '<li><a href="' . htmlspecialchars($accountURL) . '" class="navi">' . htmlspecialchars($loggedInUser["display_name"]) .'</a></li>';
    $accountText .= '<li><a href="' . htmlspecialchars($logoutURL) . '" class="navi">LOGOUT</a></li>';
} else {
    $accountText = '<li><a href="' . htmlspecialchars($loginURL) . '" class="navi">LOGIN</a></li>';
    $accountText .= '<li><a href="' . htmlspecialchars($accountCreationURL) . '" class="navi">CREATE ACCOUNT</a></li>';
}

    $header = <<<EOT
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>$title</title>
        <link rel="stylesheet" href="$cssURL">
        <link rel="icon" href="$iconURL" type="image/x-icon">
        <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
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
    $JSURL = urlFor("/Javascript/Functions.js");
    $footer = <<<EOT
        </main>
        <script src="$JSURL"></script>
        <footer>
            <p class="center">By Team 5 at ITWS 2110</p>
        </footer>
    </body>
    </html>
    EOT;
    echo $footer;
}