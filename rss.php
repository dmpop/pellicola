<?php

include('config.php');

header("Content-Type: application/xml; charset=utf-8");
echo "<?xml version='1.0' encoding='utf-8'?>" . PHP_EOL;
echo "<rss version='2.0'>" . PHP_EOL;
echo "<channel>" . PHP_EOL;

echo "<title>" . $title  . "</title>" . PHP_EOL;
echo "<link>" . $_SERVER["SERVER_NAME"] . "</link>" . PHP_EOL;
echo "<description>" . $title . " " . $subtitle . "</description>" . PHP_EOL;

$date = date("Y-m-d", strtotime('-365 day'));

$dir = new RecursiveDirectoryIterator($base_photo_dir);
$iterator = new RecursiveIteratorIterator($dir);

foreach ($iterator as $fileinfo) {
    if (!$fileinfo->isDir() && !str_contains($fileinfo->getPathname(), ".tims") && file_exists($tims_dir . basename($fileinfo->getPathname())) && date("Y-m-d", $fileinfo->getMTime()) > $date) {
        $album = str_replace($base_photo_dir, "", dirname($fileinfo->getPathname()));
        //echo $album . " <a href='index.php?file=" . bin2hex($fileinfo->getPathname()) . "&album=" . ltrim($album, "/") . "'>" . "<img src='" . $tims_dir . basename($fileinfo->getPathname()) . "' height=64 /></a><br>";
        echo "<item>" . PHP_EOL;
        echo "<title>" . htmlspecialchars(pathinfo(basename($fileinfo->getPathname()), PATHINFO_FILENAME), ENT_QUOTES) . "</title>" . PHP_EOL;
        echo "<link>" . htmlspecialchars("index.php?file=" . bin2hex($fileinfo->getPathname()) . "&album=" . ltrim($album, "/"), ENT_QUOTES) . "</link>" . PHP_EOL;
        echo "<description>" . htmlspecialchars("<img src='" . $tims_dir . basename($fileinfo->getPathname()) . "' width=128 />", ENT_QUOTES) . "</description>" . PHP_EOL;
        echo "<pubDate>" . htmlspecialchars(date("Y-m-d H:i:s", $fileinfo->getMTime()), ENT_QUOTES) . "</pubDate>" . PHP_EOL;
        echo "</item>" . PHP_EOL;
    }
}

echo "</channel>" . PHP_EOL;
echo "</rss>";
