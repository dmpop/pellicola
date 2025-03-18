<?php
session_start();
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
    exit('<center><code style="color: red;">' . L::warning_php_exif . '</code></center>');
}

function find_files($directory)
{
    global $IMG_FORMATS;
    $file_extensions = explode(",", $IMG_FORMATS);
    $files = [];
    $exclude_dir = '.tims';

    // Create a RecursiveDirectoryIterator to iterate through the directory and its subdirectories
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );

    // Loop through each file/directory
    foreach ($iterator as $file) {
        // Skip if the file is inside the excluded directory
        if (strpos($file->getPathname(), $exclude_dir) !== false) {
            continue;
        }
        // Check if the file is a file and has an allowed extension
        if ($file->isFile() && in_array(strtolower($file->getExtension()), $file_extensions)) {
            $files[] = $file->getPathname();
        }
    }

    return $files;
}
?>

<!DOCTYPE html>
<html lang="<?php echo $i18n->getAppliedLang(); ?>">

<head>
    <title><?php echo $TITLE; ?></title>
    <meta charset="utf-8">
    <link rel="shortcut icon" href="favicon.png" />
    <link rel="stylesheet" href="styles.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php
    echo "<meta http-equiv='refresh' content='" . $AUTO_REFRESH . "' >";
    ?>
    <style>
        body {
            -webkit-background-size: cover;
            -moz-background-size: cover;
            -o-background-size: cover;
            background-size: cover;
        }

        .flexbox {
            font-family: "Inter", sans-serif;
            font-size: 8pt;
            float: left;
            color: #ffffff;
            background-color: #000000;
            opacity: 0.65;
            border-radius: 5px;
            padding: .5em;
            margin-left: 1em;
            max-width: 26em;
        }
    </style>
    <script type="text/javascript">
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>
</head>

<?php if (!isset($_SESSION['random_photo'])) : ?>
    <div class="c">
        <div class="card" style="text-align: center;">
            <form style="margin-top: .7em; display: inline;" action=" " method="POST">
                <label for="password"><?php echo L::password; ?></label>
                <input style="vertical-align: middle;" class="card" type="password" name="key" value="">
                <button style="display: inline; vertical-align: middle; margin-left: 0.2em;" class="btn green" type="submit" name="submit"><?php echo L::btn_confirm; ?></button>
            </form>
            <a class="btn primary" style="text-decoration: none; vertical-align: middle; margin-left: 0.2em;" href="index.php"><?php echo L::btn_back; ?></a>
        </div>
    </div>
    <?php
    if (isset($_POST['key'])) {
        if (password_verify($_POST['key'], $KEY)) {
            $_SESSION['random_photo'] = 1;
            echo '<script type="text/javascript">location.reload();</script>';
        }
    }
    ?>
<?php endif; ?>

<?php

if (isset($_SESSION['random_photo']) || empty($KEY)) {
    $files = find_files($ROOT_PHOTO_DIR);
    $file = array_rand($files);
    $background = $BASE_URL . "/tim.php?image=" . bin2hex($files[$file]);
    // Get aperture, exposure, iso, and datetime from EXIF
    $exif = exif_read_data($files[$file], true);
    $aperture = (!isset($exif['COMPUTED']['ApertureFNumber']) ? NULL : htmlentities($exif['COMPUTED']['ApertureFNumber']));
    $exposure = (!isset($exif['ExposureTime']) ? NULL : htmlentities($exif['ExposureTime']));
    $f_length = (!isset($exif['FocalLength']) ? NULL : ' • ' . eval('return ' . htmlentities($exif['FocalLength']) . ';') . 'mm');
    $image_description = (!isset($exif['ImageDescription']) ? NULL : "<hr>" . htmlentities($exif['ImageDescription']));
    // Normalize exposure
    // https://stackoverflow.com/questions/3049998/parsing-exifs-exposuretime-using-php
    if (!is_null($exposure)) {
        $parts = explode('/', $exposure);
        if (($parts[1] % $parts[0]) == 0 || $parts[1] == 1000000) {
            $exposure = htmlentities(' • 1/' . round($parts[1] / $parts[0], 0) . 's');
        } else {
            if ($parts[1] == 1) {
                $exposure = htmlentities(' • ' . $parts[0] . 's');
            } else {
                $exposure = htmlentities(' • ' . $parts[0] . '/' . $parts[1] . 's');
            }
        }
    }
    $iso = !isset($exif['ISOSpeedRatings']) ? NULL : ' • ISO' . htmlentities($exif['ISOSpeedRatings']);
    $datetime = !isset($exif['DateTimeOriginal']) ? NULL : ' • ' . htmlentities((date('Y-m-d H:i', strtotime($exif['DateTimeOriginal']))));

    // Concatenate $exif_info
    if (!is_null($aperture) || !is_null($exposure) || !is_null($f_length) || !is_null($iso) || !is_null($datetime)) {
        $exif_info = $aperture . $f_length . $exposure . $iso . $datetime;
    } else {
        $exif_info = NULL;
    }
    echo "
    <body style='background-color:#000000; background: url(" . $background . "); background-size: cover; background-position: center;  background-repeat: no-repeat; background-attachment: fixed;'>
    <div class='flexbox'>" . $exif_info . $image_description . "</div>
    </body>
    ";
}
?>

</html>