<?php
require "./helpers/heading.php";
require "./helpers/apihelper.php";
require_once "./helpers/db.php";
loginGated();
if (!isset($_POST['content'])) {
    badRequest("No content provided");
}
if (!isset($_POST['post_id'])) {
    badRequest("No post_id provided");
}
$content = $_POST['content'];
$postId = $_POST['post_id'];
$userId = $_SESSION['userId'];
$parentCommentId = isset($_POST['parent_comment_id']) ? $_POST['parent_comment_id'] : null;
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