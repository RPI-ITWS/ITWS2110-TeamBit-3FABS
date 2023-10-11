<?php
require "config.php";
function urlFor($script_path) {
    // add the leading '/' if not present
    if($script_path[0] != '/') {
        $script_path = "/" . $script_path;
    }
    return $root . $script_path;
}
?>