<?php
require "./helpers/sessions.php";
require "./helpers/apihelper.php";
require_once "./helpers/db.php";
loginGated();
if (!isset($_GET['post_id']) || !isset($_GET['comment_id'])) {
    badRequest("No post_id or comment_id provided");
} elseif (isset($_GET['post_id']) && isset($_GET['comment_id'])) {
    badRequest("Only one of post_id or comment_id can be provided");
}
if (!isset($_GET['direction'])) {
    badRequest("No direction provided (1=like, 0=remove like)");
}
$direction = $_GET['direction'];
$userId = $_SESSION['userId'];
if (isset($_GET["post_id"])) {
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
http_response_code(204);
?>