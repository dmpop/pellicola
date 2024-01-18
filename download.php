<?php
include('config.php');
// Include i18n class and initialize it
require_once 'i18n.class.php';
$i18n = new i18n();
$i18n->setCachePath('cache');
$i18n->setFilePath('lang/{LANGUAGE}.ini');
$i18n->setFallbackLang('en');
$i18n->init();

$file = $_GET["file"] ?? NULL;

if (!empty($file)) {
    // https://www.kavoir.com/2010/05/simplest-php-hit-counter-or-download-counter-count-the-number-of-times-of-access-visits-or-downloads.html
    // https://stackoverflow.com/questions/8485886/force-file-download-with-php-using-header
    $current = @file_get_contents('downloads.txt');
    $current .= basename($file) . PHP_EOL;
    @file_put_contents('downloads.txt', $current);
    header("Content-Disposition: attachment; filename=$file");
    header('Content-Type: application/octet-stream'); // Downloading on Android might fail without this
    ob_clean();
    readfile($file);
}
