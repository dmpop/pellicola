<?php
include('config.php');
?>

<!DOCTYPE html>
<html lang="en">

<!--
	 Author: Dmitri Popov
	 License: GPLv3 https://www.gnu.org/licenses/gpl-3.0.txt
	 Source code: https://github.com/dmpop/mejiro
	-->

<head>
    <title><?php echo $title; ?></title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="favicon.png" />
    <link rel="stylesheet" href="leaflet/leaflet.css" />
    <script src="leaflet/leaflet.js"></script>
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

<script type="text/javascript">
    var init = function() {
        var map = L.map('map').setView([<?php echo $_GET["lat"]; ?>, <?php echo $_GET["lon"]; ?>], 18);
        L.tileLayer(
            'http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://openstreetmap.org">OpenStreetMap</a> contributors',
                maxZoom: 19,
            }).addTo(map);

        var posPin = L.icon({
            iconUrl: 'svg/pin-map.png'
        });

        // Add a marker
        <?php
        echo "L.marker([" . $_GET["lat"] . ", " . $_GET["lon"] . "], {";
        echo  'icon: posPin';
        echo "}).addTo(map)";
        ?>
    }
</script>

<body onload="init()">
    <div id="map"></div>
</body>

</html>