<?php
$title = "Mejiro";
$tagline = "Responsive open-source photo grid";
$protect = true; // Enable password protection
$password = array("monkey", "banana"); // If $protect is set to true, specify one or more passwords.
$public_albums = array("Album 1", "Album 2"); // Albums to exclude from being password-protected.
$columns = 4; // Specify the number of columns in the grid layout (2, 3, or 4)
$per_page = 12; // Number of images per page for pagination
$footer = "<a style='color: white' href='https://gumroad.com/l/linux-photography'>Linux Photography</a>";
$base_photo_dir = "photos"; // Directory for storing photos
$r_sort = false;	// Set to true to show tims in the reverse order (oldest ot newest)
$google_maps = false;	// Set to true to use Google Maps instead of OpenStreetMap
$links = true;	// Enable the link box
// If the link box is enabled, specify the desired URLs
$urls = array(
    array('https://www.eyeem.com/u/dmpop', 'Photos'),
    array('https://tokyoma.de/', 'Website'),
    array('https://github.com/dmpop', 'GitHub')
);
$raw_formats = '.{ARW,arw,NEF,nef,ORF,orf,CR2,cr2,DNG,dng}'; // Supported RAW formats. Add other formats, if needed.
$show_raw = true; // Display links to RAW files.
