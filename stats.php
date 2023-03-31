<?php
include('config.php');
// include i18n class and initialize it
require_once 'i18n.class.php';
$i18n = new i18n('lang/{LANGUAGE}.ini', 'cache/', 'en');
$i18n->init();
// Check whether the php-exif library is installed
if (!extension_loaded('exif')) {
	exit("<center><code style='color: red;'>" . L::warning_php_exif . "</code></center>");
}
?>

<!DOCTYPE html>
<html lang="en">

<!--
	 Author: Dmitri Popov
	 License: GPLv3 https://www.gnu.org/licenses/gpl-3.0.txt
	 Source code: https://github.com/dmpop/mejiro
	-->

<head>
	<meta charset="utf-8">
	<link rel="shortcut icon" href="favicon.png" />
	<meta name="viewport" content="width=device-width">
	<link rel="stylesheet" href="styles.css" />

	<title><?php echo $title; ?></title>
</head>

<body>
	<div id="content">
        <p>

        </p>
    </div>
</body>
</html>