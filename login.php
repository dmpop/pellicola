<?php
include('config.php');
/* Redirects here after login */
$redirect_after_login = 'index.php';

/* Set timezone to UTC */

date_default_timezone_set('UTC');

/* Will not ask password again for */
$remember_password = strtotime('+30 days'); // 30 days

if (isset($_POST['passwd']) && in_array($_POST['passwd'], $password, true)) {
    setcookie("password", $_POST['passwd'], $remember_password);
    header('Location: ' . $redirect_after_login);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title><?php echo $title; ?></title>
    <meta charset="utf-8">
    <link rel="shortcut icon" href="favicon.png" />
    <meta name="viewport" content="width=device-width">
    <link rel="stylesheet" href="styles.css" />
    <style>
        p {
            font-size: 1em;
            text-align: center;
        }

        form {
            margin-left: auto;
            margin-right: auto;
            margin-bottom: 0px;
            margin-top: 0.5em;
            border-radius: 5px;
            width: 15em;
            border-width: 1px;
            font-size: 1em;
            letter-spacing: 3px;
            padding: 5px;
            background: #3973ac;
            text-align: center;
        }
    </style>
</head>

<body>
    <div id="content">
        <div class='center'>
            <div style="text-align:center; margin-bottom: 1.5em; margin-top: 1.5em;">
                <img style="display: inline; height: 3em; vertical-align: middle;" src="favicon.svg" alt="logo" />
                <h1 style="display: inline; font-size: 3em; margin-left: 0.19em; vertical-align: middle; letter-spacing: 3px; color: #ffc03fff;"><?php echo $title; ?></h1>
            </div>
            <div class="center"><?php echo $tagline; ?></div>
            <hr style="margin-left:15%; margin-right:15%; margin-bottom: 2em;">
            <form method="POST">
                <input style="margin-top: 1em;" type="password" name="passwd">
                <input style="margin-top: 1em; margin-bottom: 1em;" type="submit" name="submit" value="Log in">
            </form>
        </div>
    </div>
</body>

</html>