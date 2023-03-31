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
        $count = array_count_values($stats);
        foreach ($count as $key => $value) {
            echo "$key: $value <br>";
        }
        ?>
    </div>
</body>

</html>