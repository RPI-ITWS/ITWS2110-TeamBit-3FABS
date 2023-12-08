<?php
require "./helpers/heading.php";
require "./helpers/apihelper.php";
require_once "./helpers/db.php";
loginGated();
if (!isset($_POST['content'])) {
    badRequest("No content provided");
} else if ($_POST['content'] == "" || $_POST['content'] == null) {
    badRequest("Content cannot be empty");
}
if (!isset($_POST['post_id'])) {
    badRequest("No post_id provided");
} else if ($_POST['post_id'] == "" || $_POST['post_id'] == null) {
    badRequest("post_id cannot be empty");
} else if (!is_numeric($_POST['post_id'])) {
    badRequest("post_id must be a number");
}
$content = htmlspecialchars(trim($_POST['content']));
$postId = $_POST['post_id'];
$userId = $_SESSION['userId'];
$parentCommentId = isset($_POST['parent_comment_id']) ? $_POST['parent_comment_id'] : null;
if ($parentCommentId !== null) {
    $parentCommentQuery = $db->prepare("SELECT COUNT(*) FROM comments WHERE id = :parentCommentId AND post_id = :postId");
    $parentCommentQuery->execute([
        'parentCommentId' => $parentCommentId,
        'postId' => $postId
    ]);
    $parentCommentExists = $parentCommentQuery->fetch(PDO::FETCH_NUM)[0] > 0;
    if (!$parentCommentExists) {
        badRequest("Parent comment does not exist");
    }
}
$db->prepare("INSERT INTO comments (content, post_id, author_id, parent_comment_id) VALUES (:content, :postId, :userId, :parentCommentId)")
    ->execute([
        'content' => $content,
        'postId' => $postId,
        'userId' => $userId,
        'parentCommentId' => $parentCommentId
    ]);
http_response_code(303);
header("Location: " . urlFor("/posts/$postId"));
?>