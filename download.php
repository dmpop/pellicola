<?php
include('config.php');
// Include i18n class and initialize it
require_once 'i18n.class.php';
$i18n = new i18n();
$i18n->setCachePath('cache');
$i18n->setFilePath('lang/{LANGUAGE}.ini');
$i18n->setFallbackLang('en');
$i18n->init();

$file = $_GET['file'];

session_start();
$previous_page = $_SESSION['page'];
?>

<!DOCTYPE html>
<html lang="<?php echo $i18n->getAppliedLang(); ?>">

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
        <div style="text-align:center; margin-bottom: 1.5em; margin-top: 1.5em;">
            <a style="text-decoration:none;" href="index.php"><img style="display: inline; height: 3.5em; vertical-align: middle;" src="favicon.png" alt="<?php echo $title; ?>" /></a>
            <a style="text-decoration:none;" href="index.php">
                <h1 style="display: inline; font-size: 2.3em; margin-left: 0.19em; vertical-align: middle; letter-spacing: 3px;"><?php echo $title ?></h1>
            </a>
        </div>
        <div class='center' style='color: gray;'><?php echo $subtitle ?></div>
        <hr>
        <div class='c'>
            <div class='card' style="text-align: center;">
                <form style="margin-top: .7em; display: inline;" action=" " method="POST">
                    <label for="password"><?php echo L::password; ?></label>
                    <input style="vertical-align: middle;" class="card" type='password' name='password' value=''>
                    <button style="display: inline; vertical-align: middle; margin-left: 0.2em;" class="btn green" type="submit" name="download"><?php echo L::download_btn; ?></button>
                </form>
                <a class="btn primary" style="text-decoration: none; vertical-align: middle; margin-left: 0.2em;" href="<?php echo $previous_page; ?>"><?php echo L::btn_back; ?></a>
            </div>
        </div>
    </div>

    <?php
    if (isset($_POST['download']) && ($_POST['password'] == $download_password) || (empty($download_password))) {
        // https://www.kavoir.com/2010/05/simplest-php-hit-counter-or-download-counter-count-the-number-of-times-of-access-visits-or-downloads.html
        // https://stackoverflow.com/questions/8485886/force-file-download-with-php-using-header
        $current = @file_get_contents('downloads.txt');
        $current .= basename($file) . PHP_EOL;
        @file_put_contents('downloads.txt', $current);
        header("Content-Disposition: attachment; filename=$file");
        header('Content-Type: application/octet-stream'); // Downloading on Android might fail without this
        ob_clean();
        readfile($file);
    } elseif (isset($_POST['download']) && ($_POST['password'] !== $download_password)) {
        echo "<h3><img style='vertical-align: middle; margin-right: .5em;' src='svg/denied.svg'/> " . L::warning_wrong_password . "</h3>";
    }
    if ($links) {
        $array_length = count($urls);
        echo '<div class="footer">';
        for ($i = 0; $i < $array_length; $i++) {
            echo '<span style="word-spacing:0.1em;"><a href="' . $urls[$i][0] . '">' . $urls[$i][1] . '</a> • </span>';
        }
        echo $footer . '</div>';
    } else {
        echo '<div class="footer">' . $footer . '</div>';
    }
    ?>