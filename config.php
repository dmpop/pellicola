<?php
$BASE_URL = 'https://domain.tld'; // Domain name or IP address of the server Pellicola runs on. Include the full path, if necessary (for example, https://domain.tld/pellicola)
$TITLE = 'Pellicola'; // Name of the Pellicola instance.
$SUBTITLE = 'Responsive open-source photo grid'; // Tagline displayed under the title.
$DELETE_PASSWORD = '$2y$12$7U709KLbqR1vdGuWAfcvZ.SKVk46OJE4qyfcUcAH0oXcoRrFiAIc6'; // Hashed password to delete photos
$PER_PAGE = 12; // Number of images per page for pagination
$REVERSED_SORT = true; // Set to true to show tims in the newest to oldest order
$FOOTER = 'This is Pellicola';
$LINKS = true;	// Enable links in the footer
// If $LINKS is set to "true", specify the desired URLs
$URLS = array(
    array('https://github.com/dmpop/pellicola', 'Pellicola'),
    array('https://www.paypal.com/paypalme/dmpop', 'I 🧡 coffee')
);
$IMG_FORMATS = 'jpg,jpeg,JPG,JPEG'; // File types Pellicola displays. Add other file extensions (for example, PNG, HEIC, etc.), if needed.
$RAW_FORMATS = 'ARW,arw,NEF,nef,ORF,orf,DNG,dng'; // Supported RAW formats. Add other file extensions, if needed
$DOWNLOAD = false; // Toggle photo download
$DOWNLOAD_PASSWORD = '$2y$12$7U709KLbqR1vdGuWAfcvZ.SKVk46OJE4qyfcUcAH0oXcoRrFiAIc6'; // Hashed password to protect downloads. Leave empty to disable password protection.
$RSS_LIMIT = 31; // Show only items in the RSS feed that are newer than the specified number of days.
$SHOW_MAP = false; // If set to "true", shows a Leaflet-based map marking the position of the geotagged photo. 
$OPENSTREETMAP = true; // If set to "true", the application generates a URL that shows the position of the geotagged photo in OpenStreetMap. Otherwise, the application generates a geo URI that shows the position in the default map application.
$AUTO_REFRESH = 180; // Interval in seconds between random photo page refreshes.
$KEY = 'secret_key'; // A key to protect the random photo page. Leave empty to disable the key prompt.

/* DO NOT CHANGE THE SETTINGS BELOW */
$ROOT_PHOTO_DIR = 'photos'; // Directory for storing photos
$STATS_DIR = 'stats'; // Directory for storing downloads and views stats
$TIMS_DIR = $ROOT_PHOTO_DIR . '/.tims/'; // Directory for storing tims
$TIM_SIZE = 1200; //Tim size
