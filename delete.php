<?php
include('config.php');
// include i18n class and initialize it
require_once 'i18n.class.php';
$i18n = new i18n('lang/{LANGUAGE}.ini', 'cache/', $language);
$i18n->init();
$i18n->setFallbackLang('en');
$file = $_GET['file'];
$raw = $_GET['raw'] ?? null;
?>

<!DOCTYPE html>
<html lang="<?php echo $language; ?>">

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
            <a style="text-decoration:none;" href="index.php"><img style="display: inline; height: 3.5em; vertical-align: middle;" src="favicon.png" alt="Mejiro" /></a>
            <a style="text-decoration:none;" href="index.php">
                <h1 style="display: inline; font-size: 2.3em; margin-left: 0.19em; vertical-align: middle; letter-spacing: 3px; color: #619b8a;"><?php echo $title ?></h1>
            </a>
        </div>
        <div class='center' style='color: gray;'><?php echo $subtitle ?></div>
        <hr>
        <div class='c'>
            <div class='card' style="text-align: center;">
                <form style="margin-top: .7em; display: inline;" action=" " method="POST">
                    <label for="password"><?php echo L::password; ?></label>
                    <input style="vertical-align: middle;" class="card" type='password' name='password' value=''>
                    <button style="display: inline; vertical-align: middle;" class="btn warning" type="submit" name="delete"><?php echo L::btn_delete; ?></button>
                </form>
                <button onclick="history.back();" style="vertical-align: middle; " class="btn primary" type="submit" name="back"><?php echo L::btn_back; ?></button>
            </div>
        </div>
    </div>
    <?php
    if (isset($_POST['delete']) && ($_POST['password'] == $password)) {
        $file_path = pathinfo($file);
        unlink($file);
        if ($raw) {
            unlink($raw);
        }
        unlink($file_path['dirname'] . DIRECTORY_SEPARATOR . 'tims' . DIRECTORY_SEPARATOR . $file_path['basename']);
        header('Location: index.php');
    }
    if ($links) {
        $array_length = count($urls);
        echo '<div class="footer">';
        for ($i = 0; $i < $array_length; $i++) {
            echo '<span style="word-spacing:0.1em;"><a style="color: white" href="' . $urls[$i][0] . '">' . $urls[$i][1] . '</a> &bull; </span>';
        }
        echo $footer . '</div>';
    } else {
        echo '<div class="footer">' . $footer . '</div>';
    }
    if ($goatcounter) {
        echo "<script data-goatcounter='https://" . $goatcounter_code . ".goatcounter.com/count' async src='//gc.zgo.at/count.js'></script>";
    }
    ?>
    </div>
</body>

</html>