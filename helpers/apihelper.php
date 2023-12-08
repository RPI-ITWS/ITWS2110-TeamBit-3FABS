<?php
function badRequest($message)
{
    http_response_code(400);
    echo $message;
    die();
}
?>