<?php
// Password
$PASSWORD = "monkey";

if (empty($_COOKIE['password']) || $_COOKIE['password'] !== $PASSWORD) {
    // Password not set or incorrect. Send to login.php.
    header('Location: login.php');
    exit;
}
?>
