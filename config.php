<?php
$title = "Mejiro";
$subtitle = "Responsive open-source photo grid";
$per_page = 12; // Number of images per page for pagination
$footer = "<a style='color: white' href='https://cameracode.coffee'>Camera, code, coffee</a>";
$base_photo_dir = "photos"; // Directory for storing photos
$r_sort = false;	// Set to true to show tims in the reverse order (oldest ot newest)
$links = true;	// Enable the link box
// If the link box is enabled, specify the desired URLs
$urls = array(
    array('https://github.com/dmpop/mejiro', 'Mejiro'),
    array('https://www.paypal.com/paypalme/dmpop', 'I 🧡 coffee')
);
$download = false; // Toggle photo download
$raw_formats = 'ARW,arw,NEF,nef,ORF,orf,CR2,cr2,DNG,dng'; // Supported RAW formats. Add other formats, if needed
$img_formats = 'jpg,jpeg,JPG,JPEG';
$show_raw = true; // Display links to RAW files
$goatcounter = false; // Toggle integration with GoatCounter web analytics
$goatcounter_code = "helloxyz"; // Unique GoatCounter code