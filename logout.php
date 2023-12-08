<?php
require "./helpers/sessions.php";
loginGated();
$lastLocation = $_SESSION['login_redirect'] ?? urlFor("/");
logoutCurrentSession();
http_response_code(303);
header("Location: " . $lastLocation);
?>