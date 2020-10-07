<?php
// Password
$PASSWORD = "monkey";
/* Redirects here after login */
$redirect_after_login = 'index.php';

/* Set timezone to UTC */

date_default_timezone_set('UTC');

/* Will not ask password again for */
$remember_password = strtotime('+30 days'); // 30 days

if (isset($_POST['password']) && $_POST['password'] == $PASSWORD) {
    setcookie("password", $PASSWORD, $remember_password);
    header('Location: ' . $redirect_after_login);
    exit;
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Log in</title>
    <meta charset="utf-8">
    <link rel="shortcut icon" href="favicon.png" />
    <style>
        body {
            font-family: 'Barlow', sans-serif;
            font-size: 1em;
            text-align: justify;
            background-color: #303030;
        }

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
            width: auto;
            border-width: 1px;
            font-size: 1em;
            letter-spacing: 3px;
            padding: 5px;
            color: #ffffff;
            background: #3399ff;
            text-align: center;
            width: 500px;
        }

        #content {
            color: #e3e3e3;
        }

        .text {
            text-align: center;
            padding: 0px;
            color: inherit;
            float: left;
        }

        .center {
            font-size: 1em;
            padding: 1px;
            height: auto;
            text-align: center;
            padding: 0px;
            margin-bottom: 2em;
        }
    </style>
</head>

<body>
    <div id='content'>
        <div class='center'>
            <form method="POST">
                <input style="margin-top: 1em;" type="password" name="password">
                <p>Type password and press ENTER</p>
            </form>
        </div>
    </div>
</body>

</html>