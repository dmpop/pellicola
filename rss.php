<?php

include('config.php');

header("Content-Type: application/xml; charset=utf-8");
echo "<?xml version='1.0' encoding='utf-8'?>" . PHP_EOL;
echo "<rss version='2.0'>" . PHP_EOL;
echo "<channel>" . PHP_EOL;

echo "<title>" . $TITLE  . "</title>" . PHP_EOL;
echo "<image><url>" . $BASE_URL . DIRECTORY_SEPARATOR . "favicon.png</url><title>$TITLE</title><link>$BASE_URL</link></image>" . PHP_EOL;
echo "<link>$BASE_URL</link>" . PHP_EOL;
echo "<description>" . $TITLE . " " . $SUBTITLE . "</description>" . PHP_EOL;

$date = date("Y-m-d", strtotime("-" . $RSS_LIMIT . " day"));

$dir = new RecursiveDirectoryIterator($ROOT_PHOTO_DIR);
$iterator = new RecursiveIteratorIterator($dir);

$rss_items = array();

foreach ($iterator as $file) {
    if (!$file->isDir() && !str_contains($file->getPathname(), ".tims") && file_exists($TIMS_DIR . basename($file->getPathname()))) {
        $exif = exif_read_data($file, 0, true);
        $date_time_original = isset($exif['EXIF']['DateTimeOriginal']) ? strtotime($exif['EXIF']['DateTimeOriginal']) : NULL;
        $datestamp = htmlentities(date('Y-m-d', $date_time_original)) ?? NULL;
        if ($datestamp > $date) {
            array_push($rss_items, $file);
        }
    }
}
foreach ($rss_items as $item) {
    $album = str_replace($ROOT_PHOTO_DIR, "", dirname($item->getPathname()));
    echo "<item>" . PHP_EOL;
    echo "<title>" . htmlspecialchars(pathinfo(basename($item->getPathname()), PATHINFO_FILENAME), ENT_QUOTES) . "</title>" . PHP_EOL;
    echo "<link>" . htmlspecialchars("$BASE_URL/index.php?file=" . bin2hex($item->getPathname()) . "&album=" . ltrim($album, "/"), ENT_QUOTES) . "</link>" . PHP_EOL;
    echo "<description>" . htmlspecialchars("<img src='" . $BASE_URL . DIRECTORY_SEPARATOR . $TIMS_DIR . basename($item->getPathname()) . "' width=128 />", ENT_QUOTES) . "</description>" . PHP_EOL;
    echo "<pubDate>" . htmlspecialchars(date("Y-m-d H:i:s", $item->getMTime()), ENT_QUOTES) . "</pubDate>" . PHP_EOL;
    echo "</item>" . PHP_EOL;
}

echo "</channel>" . PHP_EOL;
echo "</rss>";
