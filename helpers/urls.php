<?php
require "config.php";
/**
 * Returns the root path of the project given a relative path
 * @param string $script_path the path to the script
 * @return string the absolute path
 */
function urlFor(string $script_path) {
    global $root;
    // add the leading '/' if not present
    if($script_path[0] != '/') {
        $script_path = "/" . $script_path;
    }
    return $root . $script_path;
}

function authorURL(string $username, string $displayName) {
    return '<a href="' . urlFor('/profile/' . $username) . '" class="author">' . htmlspecialchars($displayName) . '</a>';
}

$assetURLs = array(
    "style" => urlFor('/style.css'),
    "heart" => urlFor('/images/heart-black.png'),
    "liked" => urlFor('/images/heart-white.png'),
    "reply" => urlFor('/images/reply.svg'),
)
?>