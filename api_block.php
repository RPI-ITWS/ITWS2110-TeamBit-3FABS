<?php
require "./helpers/heading.php";
require "./helpers/apihelper.php";
require_once "./helpers/db.php";
loginGated();
if (!isset($_GET['blockee_id'])) {
    badRequest("No blockee_id provided");
} else if ($_GET['blockee_id'] == "" || $_GET['blockee_id'] == null) {
    badRequest("blockee_id cannot be empty");
} else if (!is_numeric($_GET['blockee_id'])) {
    badRequest("blockee_id must be a number");
}
$blockeeId = $_GET['blockee_id'];
$blockerId = $_SESSION['userId'];
if ($blockeeId == $blockerId) {
    badRequest("You cannot block yourself");
}
$stmt = $db->prepare("INSERT IGNORE INTO blocks (blocker_id, blockee_id) VALUES (:blockerId, :blockeeId)");
$stmt->execute(['blockerId' => $blockerId, 'blockeeId' => $blockeeId]);
http_response_code(303);
header("Location: " . urlFor("/")); // No point going back to the profile of the person you blocked
?>