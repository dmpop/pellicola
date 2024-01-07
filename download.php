<?php
// https://www.kavoir.com/2010/05/simplest-php-hit-counter-or-download-counter-count-the-number-of-times-of-access-visits-or-downloads.html
// https://stackoverflow.com/questions/8485886/force-file-download-with-php-using-header
$file = $_GET['file'];
$current = @file_get_contents('downloads.txt');
$current .= "$file\n";
@file_put_contents('downloads.txt', $current);
header("Content-Disposition: attachment; filename=$file");
header('Content-Type: application/octet-stream'); // Downloading on Android might fail without this
ob_clean();
readfile($file);
