<?php
require_once "./db.php";

function createSession(int $userId) {
    global $db;
    session_start();
    $_SESSION['userId'] = $userId;
    $id = session_id();
    $timestamp = new DateTimeImmutable();
    $expires = $timestamp->add(new DateInterval('P1D'));
    $db->prepare("INSERT INTO SESSIONS (user_id, token, expires) VALUES (:userId, :token, :expires)")
        ->execute([
            'userId' => $userId,
            'token' => $id,
            'expires' => $expires->format('Y-m-d H:i:s')
        ]);
    $_SESSION['expires'] = $expires;
}

function checkSessionValidity() {
    global $db;
    session_start();
    if (!isset($_SESSION['userId'])) {
        return false;
    }
    if (!isset($_SESSION['expires'])) {
        return false;
    }
    $expires = new DateTimeImmutable($_SESSION['expires']);
    if ($expires < new DateTimeImmutable()) {
        return false;
    }
    $token = session_id();
    $userId = $_SESSION['userId'];
    $result = $db->prepare("SELECT * FROM SESSIONS WHERE user_id = :userId AND token = :token AND expires > NOW()")
        ->execute([
            'userId' => $userId,
            'token' => $token
        ]);
    if (!$result) {
        return false;
    }
    return true;
}

/**
 * Refreshes the validity of the current session
 * Precondition: The session is valid
 */
function refreshValidity() {
    global $db;
    $newExpires = (new DateTimeImmutable())->add(new DateInterval('P1D'));
    $_SESSION['expires'] = $newExpires;
    $db->prepare("UPDATE SESSIONS SET expires = :expires WHERE user_id = :userId AND token = :token")
        ->execute([
            'expires' => $newExpires->format('Y-m-d H:i:s'),
            'userId' => $_SESSION['userId'],
            'token' => session_id()
        ]);
}

function getCurrentUserInfo(): ?array {
    global $db;
    if (!checkSessionValidity()) {
        return null;
    }
    $userId = $_SESSION['userId'];
    $prepped = $db->prepare("SELECT * FROM USERS WHERE id = :userId");
    $success = $prepped->execute(['userId' => $userId]);
    if (!$success) {
        return null;
    }
    return $prepped->fetch(PDO::FETCH_ASSOC);
}