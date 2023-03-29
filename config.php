<?php
$title = "Mejiro";
$subtitle = "Responsive open-source photo grid";
$base_photo_dir = "photos"; // Directory for storing photos
$per_page = 12; // Number of images per page for pagination
$r_sort = false; // Set to true to show photos in the reversed order (oldest ot newest)
$footer = "<a style='color: white' href='https://cameracode.coffee'>Camera, code, coffee</a>";
$links = true;	// Enable links in the footer
// If $links is set to true, specify the desired URLs
$urls = array(
    array('https://github.com/dmpop/mejiro', 'Mejiro'),
    array('https://www.paypal.com/paypalme/dmpop', 'I 🧡 coffee')
);
$img_formats = 'jpg,jpeg,JPG,JPEG'; // File types Mejiro show display. Add other file extensions (for example, PNG, HEIC, etc.), if needed.
$show_raw = true; // Toggle links to RAW files
$raw_formats = 'ARW,arw,NEF,nef,ORF,orf,DNG,dng'; // Supported RAW formats. Add other file extensions, if needed
$download = false; // Toggle photo download
$goatcounter = false; // Toggle integration with GoatCounter web analytics
$goatcounter_code = "helloxyz"; // Unique GoatCounter code