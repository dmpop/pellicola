<?php
$title = "Pellicola";
$subtitle = "Responsive open-source photo grid";
$delete_password = "secret"; // Password to delete photos
$per_page = 12; // Number of images per page for pagination
$r_sort = true; // Set to true to show tims in the newest to oldest order
$footer = "<a style='color: white' href='https://cameracode.coffee'>Camera, code, coffee</a>";
$links = true;	// Enable links in the footer
// If $links is set to true, specify the desired URLs
$urls = array(
    array('https://github.com/dmpop/pellicola', 'Pellicola'),
    array('https://www.paypal.com/paypalme/dmpop', 'I 🧡 coffee')
);
$img_formats = 'jpg,jpeg,JPG,JPEG'; // File types Pellicola displays. Add other file extensions (for example, PNG, HEIC, etc.), if needed.
$raw_formats = 'ARW,arw,NEF,nef,ORF,orf,DNG,dng, heic,HEIC'; // Supported RAW formats. Add other file extensions, if needed
$download = false; // Toggle photo download
$download_password = "secret"; // Password to protect downloads. Leave empty to disable password protection.
$f_length_threshold = 3; // Focal lengths with count lower that the specified number are not shown in stats.

/* DO NOT CHANGE SETTINGS BELOW */
$base_photo_dir = "photos"; // Directory for storing photos
$tims_dir = $base_photo_dir . "/.tims/"; // Directory for storing tims
$tim_size = 1200; //Tim size
