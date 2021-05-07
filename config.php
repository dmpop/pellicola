<?php
$title = "目白 Mejiro";
$tagline = "Responsive open-source photo grid";
// Enable password protection
$protect = true;
// Password
$password = "monkey";
$columns = 4; // Specify the number of columns in the grid layout (2, 3, or 4)
$per_page = 12; // Number of images per page for pagination
$footer = "<a style='color: white' href='http://dmpop.github.io/mejiro/'>Mejiro</a> &mdash; pastebin for your photos";
$photo_dir = "photos"; // Directory for storing photos
$r_sort = false;	// Set to true to show tims in the reverse order (oldest ot newest)
$google_maps = false;	// Set to true to use Google Maps instead of OpenStreetMap
$links = true;	// Enable the link box
// If the link box is enabled, specify the desired links and their icons in the array below
$links = array(
    array('https://www.eyeem.com/u/dmpop', 'Photos'),
    array('https://tokyoma.de/', 'Website'),
    array('https://github.com/dmpop', 'GitHub')
);
$raw_formats = '.{ARW,arw,NEF,nef,ORF,orf,CR2,cr2,DNG,dng}'; // Supported RAW formats. Add other formats, if needed.
?>