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
    exit('<center><code style="color: red;">' . L::warning_php_exif . '</code></center>');
}

function find_files($directory)
{
    $files = [];
    $file_extensions = ['jpg', 'JPG', 'jpeg', 'JPEG'];
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
    <link rel="stylesheet" href="css/normalize.css">
    <link rel="stylesheet" href="css/styles.css">
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
            float: left;
            color: #ffffff;
            background-color: #000000;
            opacity: 0.65;
            border-radius: 5px;
            padding: .5em;
            margin-left: 1em;
            max-width: 25em;
        }
    </style>
    <script type="text/javascript">
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>
    <script type="text/javascript">
        // Function to get a cookie by name
        function getCookie(name) {
            const cookieName = name + "=";
            const decodedCookies = decodeURIComponent(document.cookie);
            const cookieArray = decodedCookies.split(';');

            for (let i = 0; i < cookieArray.length; i++) {
                let cookie = cookieArray[i].trim();
                if (cookie.startsWith(cookieName)) {
                    return cookie.substring(cookieName.length, cookie.length);
                }
            }
            return null;
        }

        // Check if the cookie is already set
        const cookieName = "key";
        const savedInput = getCookie(cookieName);

        if (!savedInput) {
            // Prompt the user for input if the cookie is not set
            const key = prompt("Key:");

            if (key) {
                // Save the user input as a cookie
                const cookieValue = encodeURIComponent(key); // Encode the input to handle special characters
                const daysToExpire = 7; // Set the cookie to expire in 7 days

                // Calculate the expiration date
                const date = new Date();
                date.setTime(date.getTime() + (daysToExpire * 24 * 60 * 60 * 1000));
                const expires = "expires=" + date.toUTCString();

                // Set the cookie
                document.cookie = `${cookieName}=${cookieValue}; ${expires}; path=/`;

                console.log("User input saved as a cookie.");
            } else {
                console.log("No input provided. Cookie not set.");
            }
        } else {
            console.log("Cookie already exists. Saved input:", savedInput);
        }
    </script>
</head>

<?php
if (isset($_COOKIE['key'])) {
    $key = $_COOKIE['key'];
} else {
    // Reload the page until the correct key is provided as saves as a cookie
    echo '<script type="text/javascript">location.reload();</script>';
}

if (isset($_COOKIE['key']) && ($_COOKIE['key'] == $KEY) || empty($KEY)) {
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
    <body style='background-color:#000000; background: url(" . $background . "); background-size: cover; background-position: no-repeat center center fixed;'>
    <div class='flexbox'>" . $exif_info . $image_description . "</div>
    </body>
    ";
} else {
    setcookie('key', "", time() - 3600, "/");
    echo "¯\_(ツ)_/¯";
}
?>

</html>