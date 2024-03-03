<?php

include('config.php');

header("Content-Type: application/xml; charset=utf-8");
echo "<?xml version='1.0' encoding='utf-8'?>" . PHP_EOL;
echo "<rss version='2.0'>" . PHP_EOL;
echo "<channel>" . PHP_EOL;

echo "<title>" . $title  . "</title>" . PHP_EOL;
echo "<image><url>" . $server . DIRECTORY_SEPARATOR . "favicon.png</url><title>$title</title><link>$server</link></image>" . PHP_EOL;
echo "<link>$server</link>" . PHP_EOL;
echo "<description>" . $title . " " . $subtitle . "</description>" . PHP_EOL;

$date = date("Y-m-d", strtotime("-" . $rss_feed_limit . " day"));

$dir = new RecursiveDirectoryIterator($base_photo_dir);
$iterator = new RecursiveIteratorIterator($dir);

$rss_items = array();

foreach ($iterator as $file) {
    if (!$file->isDir() && !str_contains($file->getPathname(), ".tims") && file_exists($tims_dir . basename($file->getPathname()))) {
        $exif = @exif_read_data($file, 0, true);
        $datestamp = htmlentities(date('Y-m-d', strtotime($exif['EXIF']['DateTimeOriginal']))) ?? NULL;
        if ($datestamp > $date) {
            array_push($rss_items, $file);
        }
    }
}
foreach ($rss_items as $item) {
    $album = str_replace($base_photo_dir, "", dirname($item->getPathname()));
    echo "<item>" . PHP_EOL;
    echo "<title>" . htmlspecialchars(pathinfo(basename($item->getPathname()), PATHINFO_FILENAME), ENT_QUOTES) . "</title>" . PHP_EOL;
    echo "<link>" . htmlspecialchars("$server/index.php?file=" . bin2hex($item->getPathname()) . "&album=" . ltrim($album, "/"), ENT_QUOTES) . "</link>" . PHP_EOL;
    echo "<description>" . htmlspecialchars("<img src='" . $server . DIRECTORY_SEPARATOR . $tims_dir . basename($item->getPathname()) . "' width=128 />", ENT_QUOTES) . "</description>" . PHP_EOL;
    echo "<pubDate>" . htmlspecialchars(date("Y-m-d H:i:s", $item->getMTime()), ENT_QUOTES) . "</pubDate>" . PHP_EOL;
    echo "</item>" . PHP_EOL;
}

echo "</channel>" . PHP_EOL;
echo "</rss>";
