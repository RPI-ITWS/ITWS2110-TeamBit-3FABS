<?php
require "./helpers/heading.php";
require "./helpers/apihelper.php";
require_once "./helpers/db.php";
loginGated();
$postIdIsSet = isset($_GET['post_id']);
$commentIdIsSet = isset($_GET['comment_id']);
if (!$postIdIsSet && !$commentIdIsSet) {
    badRequest("No post_id or comment_id provided");
} else if ($postIdIsSet && $commentIdIsSet) {
    badRequest("Only one of post_id or comment_id can be provided");
}
if (!isset($_GET['direction'])) {
    badRequest("No direction provided (1=like, 0=remove like)");
}
$direction = $_GET['direction'];
$userId = $_SESSION['userId'];
if ($postIdIsSet) {
    $postId = $_GET['post_id'];
    if (!is_numeric($postId)) {
        badRequest("post_id must be a number");
    }
    if ($direction == "0") {
        $db->prepare("DELETE FROM likes WHERE post_id = :postId AND author_id = :userId")
            ->execute([
                'postId' => $postId,
                'userId' => $userId
            ]);
    } else {
        $db->prepare("INSERT IGNORE INTO likes (post_id, author_id) VALUES (:postId, :userId)")
            ->execute([
                'postId' => $postId,
                'userId' => $userId
            ]);
    }
} else {
    $commentId = $_GET['comment_id'];
    if (!is_numeric($commentId)) {
        badRequest("comment_id must be a number");
    }
    if ($direction == "0") {
        $db->prepare("DELETE FROM comment_likes WHERE comment_id = :commentId AND author_id = :userId")
            ->execute([
                'commentId' => $commentId,
                'userId' => $userId
            ]);
    } else {
        $db->prepare("INSERT IGNORE INTO comment_likes (comment_id, author_id) VALUES (:commentId, :userId)")
            ->execute([
                'commentId' => $commentId,
                'userId' => $userId
            ]);
    }
}
?>