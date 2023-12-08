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
$isUnblock = isset($_GET['unblock']);
$targetUsernameStmt = $db->prepare("SELECT username FROM users WHERE id = :blockeeId");
$targetUsernameStmt->execute(['blockeeId' => $blockeeId]);
$targetUsername = $targetUsernameStmt->fetch(PDO::FETCH_ASSOC)['username'];
if ($isUnblock) {
    $db->prepare("DELETE FROM blocks WHERE blocker_id = :blockerId AND blockee_id = :blockeeId")
        ->execute(['blockerId' => $blockerId, 'blockeeId' => $blockeeId]);
    http_response_code(303);
    header("Location: " . urlFor("/profile/$targetUsername"));
} else {
    $db->prepare("INSERT IGNORE INTO blocks (blocker_id, blockee_id) VALUES (:blockerId, :blockeeId)")
        ->execute(['blockerId' => $blockerId, 'blockeeId' => $blockeeId]);
    http_response_code(303);
    header("Location: " . urlFor("/profile/$targetUsername"));
}
?>