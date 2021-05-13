<?php
include('config.php');
if (empty($_COOKIE['password']) || !in_array($_COOKIE['password'], $password, true)) {
    // Password not set or incorrect. Send to login.php
    header('Location: login.php');
    exit;
}
?>
