<?php

include('config.php');

function globRecursiveWithBrace($directory, $pattern)
{
    $files = glob($directory . DIRECTORY_SEPARATOR . $pattern, GLOB_BRACE);
    foreach (glob($directory . DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR) as $subDirectory) {
        $files = array_merge($files, globRecursiveWithBrace($subDirectory, $pattern));
    }
    return $files;
}

if (!isset($argv[1])) {
    $photo_dir = $ROOT_PHOTO_DIR;
} else {
    $photo_dir = $argv[1];
}

if (!file_exists($TIMS_DIR)) {
    mkdir($TIMS_DIR);
}

$files = globRecursiveWithBrace($photo_dir, "*.{" . $IMG_FORMATS . "}");

/* CREATE TIMS ---START--- */
function create_tim($original, $tim, $tim_size)
{
    global $TIM_QUALITY;
    // Load image
    $img = @imagecreatefromjpeg($original);
    if (!$img) return false;
    // Rotate $original based on orientation EXIF data
    $exif = exif_read_data($original);
    if (!empty($exif['Orientation'])) {
        switch ($exif['Orientation']) {
            case 3:
                $img = imagerotate($img, -180, 0);
                break;
            case 6:
                $img = imagerotate($img, -90, 0);
                break;
            case 8:
                $img = imagerotate($img, 90, 0);
                break;
        }
    }

    // Get image size
    $width = imagesx($img);
    $height = imagesy($img);

    // Calculate tim size
    $new_width    = $tim_size;
    $new_height = floor($height * ($tim_size / $width));

    // Create a new temporary image
    $tmp_img = imagecreatetruecolor($new_width, $new_height);

    // Copy and resize old image into new image
    imagecopyresampled($tmp_img, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

    // Save tim into a file
    $ok = @imagejpeg($tmp_img, $tim, $TIM_QUALITY);

    // Cleanup
    imagedestroy($img);
    imagedestroy($tmp_img);

    // Return bool true if tim creation worked
    return $ok;
}
/* CREATE TIMS ---END--- */

$file_count = count($files);

foreach ($files as $file) {
    $tim = $photo_dir . DIRECTORY_SEPARATOR . ".tims" . DIRECTORY_SEPARATOR . basename($file);
    if (!file_exists($tim)) {
        create_tim($file, $tim, $TIM_SIZE);
    }
}
