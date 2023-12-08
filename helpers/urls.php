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

$assetURLs = array(
    "style" => urlFor('/style.css'),
    "heart" => urlFor('/images/heart-black.png'),
    "liked" => urlFor('/images/heart-white.png'),
)
?>