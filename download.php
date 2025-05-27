<?php
include('config.php');
// Include i18n class and initialize it
require_once 'i18n.class.php';
$i18n = new i18n();
$i18n->setCachePath('cache');
$i18n->setFilePath('lang/{LANGUAGE}.ini');
$i18n->setFallbackLang('en');
$i18n->init();

// Check if $_GET['file'] is empty
if (!empty($_GET['file'])) {
    $file = $_GET['file'] ?? NULL;
} else {
    exit('<div style="text-align: center;"><code>¯\_(ツ)_/¯</code></div>');
}
$filename = pathinfo($file, PATHINFO_FILENAME);
$downloads_file = $STATS_DIR . DIRECTORY_SEPARATOR . $filename . '.downloads';
if (file_exists($downloads_file)) {
    $downloads_count = fgets(fopen($downloads_file, 'r'));
    // Increment count only if $_COOKIE['count'] is not set
    if (!isset($_COOKIE['count'])) {
        $downloads_count++;
        @file_put_contents($downloads_file, $downloads_count);
    }
} else {
    @file_put_contents($downloads_file, '1');
}
header("Content-Disposition: attachment; filename=" . basename($file) . "");
header("Content-Type: application/octet-stream"); // Downloading on Android might fail without this
ob_clean();
readfile($file);
