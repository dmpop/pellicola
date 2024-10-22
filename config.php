<?php
$base_url = "https://domain.tld"; // Domain name or IP address of the server Pellicola runs on. Include the full path, if necessary (for example, https://domain.tld/pellicola)
$title = "Pellicola"; // Name of the Pellicola instance.
$subtitle = "Responsive open-source photo grid"; // Tagline displayed under the title.
$delete_password = "secret"; // Password to delete photos
$per_page = 12; // Number of images per page for pagination
$r_sort = true; // Set to true to show tims in the newest to oldest order
$footer = "<a style='color: white' href='https://cameracode.coffee'>Camera, code, coffee</a>";
$links = true;	// Enable links in the footer
// If $links is set to "true", specify the desired URLs
$urls = array(
    array('https://github.com/dmpop/pellicola', 'Pellicola'),
    array('https://www.paypal.com/paypalme/dmpop', 'I 🧡 coffee')
);
$img_formats = 'jpg,jpeg,JPG,JPEG'; // File types Pellicola displays. Add other file extensions (for example, PNG, HEIC, etc.), if needed.
$raw_formats = 'ARW,arw,NEF,nef,ORF,orf,DNG,dng'; // Supported RAW formats. Add other file extensions, if needed
$download = false; // Toggle photo download
$download_password = "secret"; // Password to protect downloads. Leave empty to disable password protection.
$f_length_threshold = 3; // Focal lengths with count lower that the specified number are not shown in stats.
$rss_feed_limit = 31; // Show only items in the RSS feed that are newer than the specified number of days.
$show_map = false; // If set to "true", shows a Leaflet-based map marking the position of the geotagged photo. 
$openstreetmap = true; // If set to "true", the application generates a URL that shows the position of the geotagged photo in OpenStreetMap. Otherwise, the application generates a geo URI that shows the position in the default map application.

/* DO NOT CHANGE THE SETTINGS BELOW */
$base_photo_dir = "photos"; // Directory for storing photos
$stats_dir = "stats"; // Directory for storing downloads and views stats
$tims_dir = $base_photo_dir . "/.tims/"; // Directory for storing tims
$tim_size = 1200; //Tim size
