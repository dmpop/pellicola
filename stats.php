<?php
include('config.php');
// include i18n class and initialize it
require_once 'i18n.class.php';
$i18n = new i18n();
$i18n->setCachePath('cache');
$i18n->setFilePath('lang/{LANGUAGE}.ini');
$i18n->setFallbackLang('en');
$i18n->init();
// Check whether the php-exif library is installed
if (!extension_loaded('exif')) {
    exit("<center><code style='color: red;'>" . L::warning_php_exif . "</code></center>");
}
// Detect browser language
$language = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
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
        <?php
        function rsearch($dir, $excluded, $pattern_array)
        {
            $return = array();
            $iti = new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS);
            foreach (new RecursiveIteratorIterator($iti) as $file => $details) {
                if (!is_file($iti->getBasename()) && ($iti->getBasename() != $excluded)) {
                    $file_ext = pathinfo($file, PATHINFO_EXTENSION);
                    if (in_array(strtolower($file_ext), $pattern_array)) {
                        $return[] = $file;
                    }
                }
            }
            return $return;
        }

        $files = rsearch($base_photo_dir, 'tims', array('jpg', 'jpeg', 'jpeg', 'JPEG'));

        $stats = array();
        foreach ($files as $file) {
            $exif = @exif_read_data($file);
            //print_r($exif);
            array_push($stats, $exif['Model']);
        }
        echo "
        <div class='c'>
        <h2 style='text-align: left;'>" . L::camera_models . "</h2>
        <div class='card'>
        <table>
        ";
        $count = array_count_values($stats);
        ksort($count);
        foreach ($count as $key => $value) {
            echo "<tr><td>$key</td><td>$value</td></tr>";
        }
        echo "
        </table>
        </div>
        </div>
        ";
        if ($links) {
            $array_length = count($urls);
            echo '<div class="footer">';
            for ($i = 0; $i < $array_length; $i++) {
                echo '<span style="word-spacing:0.1em;"><a style="color: white" href="' . $urls[$i][0] . '">' . $urls[$i][1] . '</a> &bull; </span>';
            }
            echo  $footer . '</div>';
        } else {
            echo '<div class="footer">' . $footer . '</div>';
        }
        if ($goatcounter) {
            echo "<script data-goatcounter='https://" . $goatcounter_code . ".goatcounter.com/count' async src='//gc.zgo.at/count.js'></script>";
        }
        ?>
        <div class="center">
            <button onclick="history.back();" style="vertical-align: middle; " class="btn primary" type="submit" name="back"><?php echo L::btn_back; ?></button>
        </div>
    </div>
</body>

</html>