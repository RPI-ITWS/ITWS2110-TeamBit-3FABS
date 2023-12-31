<?php
require_once "./helpers/db.php";
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);

function createSession(int $userId)
{
    global $db;
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    $_SESSION['userId'] = $userId;
    $id = session_id();
    $timestamp = new DateTimeImmutable();
    $expires = $timestamp->add(new DateInterval('P1D'));
    $db->prepare("INSERT INTO sessions (user_id, token, expires_at) VALUES (:userId, :token, :expires_at)")
        ->execute([
            'userId' => $userId,
            'token' => $id,
            'expires_at' => $expires->format('Y-m-d H:i:s')
        ]);
    $_SESSION['expires'] = $expires;
}

function checkSessionValidity()
{
    global $db;
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    if (!isset($_SESSION['userId'])) {
        return false;
    }
    if (!isset($_SESSION['expires'])) {
        return false;
    }
    $expires = $_SESSION['expires'];
    if ($expires < new DateTimeImmutable()) {
        return false;
    }
    $token = session_id();
    $userId = $_SESSION['userId'];
    $result = $db->prepare("SELECT * FROM sessions WHERE user_id = :userId AND token = :token AND expires_at > NOW()")
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
function refreshValidity()
{
    global $db;
    $newExpires = (new DateTimeImmutable())->add(new DateInterval('P1D'));
    $_SESSION['expires'] = $newExpires;
    $db->prepare("UPDATE sessions SET expires_at = :expires_at WHERE user_id = :userId AND token = :token")
        ->execute([
            'expires_at' => $newExpires->format('Y-m-d H:i:s'),
            'userId' => $_SESSION['userId'],
            'token' => session_id()
        ]);
}

function getCurrentUserInfo(): ?array
{
    global $db;
    if (!checkSessionValidity()) {
        return null;
    }
    $userId = $_SESSION['userId'];
    $prepped = $db->prepare("SELECT * FROM users WHERE id = :userId");
    $success = $prepped->execute(['userId' => $userId]);
    if (!$success) {
        return null;
    }
    return $prepped->fetch(PDO::FETCH_ASSOC);
}

function loginGated()
{
    if (!checkSessionValidity()) {
        header("Location: " . urlFor("/login"));
        exit();
    }
}

function logoutCurrentSession() {
    global $db;
    if (session_status() !== PHP_SESSION_ACTIVE) {
        return;
    }
    $userId = $_SESSION['userId'];
    $token = session_id();
    $db->prepare("DELETE FROM sessions WHERE user_id = :userId AND token = :token")
        ->execute([
            'userId' => $userId,
            'token' => $token
        ]);
    session_destroy();
}
