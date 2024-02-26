<?php
include('config.php');
// Check whether the php-exif library is installed
if (!extension_loaded('exif')) {
    exit("<center><code style='color: red;'>php-exif is not installed</code></center>");
}
if (!$show_map) {
    exit('<code><center>¯\_(ツ)_/¯</code></center>');
}

// Start a session to keep track of albums
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!empty($_SESSION['album'])) {
    $album = $_SESSION['album'];
} else {
    $album = NULL;
}
$photo_dir = htmlentities(str_replace(DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $base_photo_dir . DIRECTORY_SEPARATOR . $album . DIRECTORY_SEPARATOR));
$photos = glob($photo_dir . "*.{" . $img_formats . "}", GLOB_BRACE);

// Count all photos in $photo_dir 
$total_count = count($photos);
// Check if $photo_dir is empty
if ($total_count === 0) {
    exit("<center><code style='color: red;'>No photos found</code></center>");
} else {
    // Find the most recent photo to center the map on
    // $total_count-1 because arrays start with 0
    $last_photo = $photos[$total_count - 1];
};

/* EXTRACT LATITUDE AND LONGITUDE ---START---
	   * https://stackoverflow.com/a/16437888
	   */
function gps($coordinate, $hemisphere)
{
    if (is_string($coordinate)) {
        $coordinate = array_map("trim", explode(",", $coordinate));
    }
    for ($i = 0; $i < 3; $i++) {
        $part = explode('/', $coordinate[$i]);
        if (count($part) == 1) {
            $coordinate[$i] = $part[0];
        } else if (count($part) == 2) {
            $coordinate[$i] = floatval($part[0]) / floatval($part[1]);
        } else {
            $coordinate[$i] = 0;
        }
    }
    list($degrees, $minutes, $seconds) = $coordinate;
    $sign = ($hemisphere == 'W' || $hemisphere == 'S') ? -1 : 1;
    return $sign * ($degrees + $minutes / 60 + $seconds / 3600);
}
/* EXTRACT LATITUDE AND LONGITUDE ---END--- */
?>

<!DOCTYPE html>

<!--
Author: Dmitri Popov
License: GPLv3 https://www.gnu.org/licenses/gpl-3.0.txt
Source code: https://github.com/dmpop/pellicola
-->

<html>

<head>
    <title><?php echo $title; ?></title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="favicon.png" />
    <link rel="stylesheet" href="leaflet/leaflet.css" />
    <script src="leaflet/leaflet.js"></script>
    <link rel="stylesheet" href="leaflet/MarkerCluster.css" />
    <link rel="stylesheet" href="leaflet/MarkerCluster.Default.css" />
    <script src="leaflet/leaflet.markercluster.js"></script>
    <style>
        html,
        body,
        #map {
            margin: 0;
            height: 100%;
            width: 100%;
        }
    </style>
</head>

<body>
    <div id="map"></div>

    <script type="text/javascript">
        var tiles = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 18,
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors,  This is <a href="https://github.com/dmpop/pellicola">Pellicola</a>. Photos: <?php echo $total_count; ?>'
        });

        var map = L.map('map', {
            zoom: 9,
            layers: [tiles]
        });

        var markers = L.markerClusterGroup();
        <?php
        foreach ($photos as $file) {
            // Get latitude and longitude values
            $exif = @exif_read_data($file, 0, true);
            if (array_key_exists('GPS', $exif)) {
				$lat = gps($exif['GPS']['GPSLatitude'], $exif['GPS']['GPSLatitudeRef']);
				$lon = gps($exif['GPS']['GPSLongitude'], $exif['GPS']['GPSLongitudeRef']);
			} else {
				$lat = NULL;
				$lon = NULL;
			}
            if (empty($exif['COMMENT']['0'])) {
                $caption = "";
            } else {
                $caption = $exif['COMMENT']['0'];
                $caption = str_replace(array("\r", "\n"), '', $caption);
            }
            if (isset($lat) && isset($lon)) {
                echo 'var marker = L.marker(new L.LatLng(' . $lat . ', ' . $lon . '));';
                echo "marker.bindPopup('<a href=\"index.php?file=" . bin2hex($file) . "\"  target=\"_blank\"><img src=\"tim.php?image=" . $file . "\" width=300px /></a>" . $caption . "');";
                echo 'markers.addLayer(marker);';
            }
        }
        ?>
        map.addLayer(markers);

        <?php
        // Get latitude and longitude values og the last photo
        $exif = @exif_read_data($last_photo, 0, true);
        if (array_key_exists('GPS', $exif)) {
            $lat = gps($exif['GPS']['GPSLatitude'], $exif['GPS']['GPSLatitudeRef']);
            $lon = gps($exif['GPS']['GPSLongitude'], $exif['GPS']['GPSLongitudeRef']);
        } else {
            $lat = NULL;
            $lon = NULL;
        }
        ?>
        map.panTo(new L.LatLng(<?php echo $lat; ?>, <?php echo $lon; ?>));
    </script>
</body>

</html>