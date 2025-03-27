<?php
include('config.php');

function globRecursiveWithBrace($directory, $pattern) {
    $files = glob($directory . DIRECTORY_SEPARATOR . $pattern, GLOB_BRACE);
    foreach (glob($directory . DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR) as $subDirectory) {
        $files = array_merge($files, globRecursiveWithBrace($subDirectory, $pattern));
    }
    return $files;
}

$files = globRecursiveWithBrace($ROOT_PHOTO_DIR, "*.{" . $IMG_FORMATS . "}");

$tims = glob($TIMS_DIR . '*');

$array1 = [];
$array2 = [];

foreach ($tims as $tim_path) {
    $tim = basename($tim_path);
    $array1[] = $tim;
}

foreach ($files as $file_path) {
    $file = basename($file_path);
    $array2[] = $file;
}

$result = array_diff($array1, $array2);

foreach ($result as $tim) {
    unlink($TIMS_DIR . $tim);
}
