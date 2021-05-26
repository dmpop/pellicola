<?php
unset($_COOKIE['password']);
setcookie('password', null, -1, '/');
header('Location: index.php');
?>