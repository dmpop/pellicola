<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">

<html>

    <!--
	 Author: Dmitri Popov
	 License: GPLv3 https://www.gnu.org/licenses/gpl-3.0.txt
	 Source code: https://github.com/dmpop/mejiro
    -->

    <head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	<meta name="viewport" content="width=device-width">
	<link href='http://fonts.googleapis.com/css?family=Fira+Sans' rel='stylesheet' type='text/css'>
	<link href='http://fonts.googleapis.com/css?family=Harmattan' rel='stylesheet' type='text/css'>
	<script src="https://use.fontawesome.com/b4c062efea.js"></script>
	<link rel="shortcut icon" href="favicon-inliner.png" />

	<?php

	// User-defined settings
	$title = "目白 Mejiro";
	$tagline = "Responsive single-file open source photo grid";
	$columns = 4; // Specify the number of columns in the grid layout (2, 3, or 4)
	$per_page = 25; // Number of images per page for pagination
	$footer="<a style='color: white' href='http://dmpop.github.io/mejiro/'>Mejiro</a> &mdash; pastebin for your photos";
	$photo_dir = "photos"; // Directory for storing photos
	$r_sort = true;	// Set to true to show tims in the reverse order (oldest ot newest)
	$google_maps = false;	// Set to true to use Google Maps instead of OpenStreetMap
	$use_shortLink = true; // Set to false if you do not want to use short URLs or is.gd is inaccessible at your location
	// Change this next line if you wish to use a different short URL provider (bit.ly, goo.gl, mcaf.ee)
	$shortLink_API = "https://is.gd/create.php?format=simple&url=http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	$links = true;	// Enable the link box
	// If the link box is enabled, specify the desired links and their icons in the array below
	$links = array (
	    array('https://www.eyeem.com/u/dmpop','fa fa-instagram fa-lg'),
	    array('https://scribblesandsnaps.com/','fa fa-wordpress fa-lg'),
	    array('https://github.com/dmpop','fa fa-github fa-lg')
	);
	$raw_formats = '.{ARW,arw,NEF,nef,ORF,orf,CR2,cr2,PNG,png}'; // Supported RAW formats. Add other formats, if needed.
	?>

	<style>
	 body { font-family: 'Fira Sans', sans-serif; font-size: 2.0vh; text-align: justify; background-color: #303030; }
	 a { color: #999; }
	 a.superscript { position: relative; top: -0.7em; font-size: 51%; text-decoration: none; }
	 h1 { color: #e3e3e3; font-family: 'Harmattan', sans-serif; font-size: 7.0vh; font-weight: 400; text-align: center; margin-top: 0.3em; margin-bottom: 0.5em; line-height: 100%; letter-spacing: 1px; }
	 h2 { color: #e3e3e3; font-family: 'Harmattan', sans-serif; font-size: 3.0vh; font-weight: 400; text-align: center; margin-top: 1em; margin-bottom: 0.5em; line-height: 100%; letter-spacing: 1 }
	 h3 { color: #e3e3e3; font-family: 'Harmattan', sans-serif; font-size: 2.0vh; font-weight: 400; text-align: center; margin-top: 1em; margin-bottom: 0.5em; line-height: 100%; letter-spacing: 1 }
	 p { font-size: 2.0vh; text-align: left; }
	 p.msg { margin-left: auto; margin-right: auto; margin-bottom: 0px; margin-top: 0.5em; border-radius: 5px; width: auto; border-width: 1px; font-size: 2.0vh; letter-spacing: 3px; padding: 5px; color: #ffffff; background: #3399ff; text-align: center; width:500px; }
	 p.caption { border-style: none; border-width: 1px; font-size: 2.0vh; padding: 5px; color: #303030 !important; margin-bottom: 0px; margin-left: auto; margin-right: auto; line-height: 2.0em; text-align: center; }
	 p.box { width: auto; border-style: dotted; border-width: 1px; font-size: 2.0vh; padding: 5px; color: #e3e3e3; margin-bottom: 0px; margin-left: auto; margin-right: auto; line-height: 2.0em; text-align: center; }
	 #content { color: #e3e3e3; }
	 .text { text-align: center; padding: 0px; color: inherit; float: left; }
	 .center { font-size: 2.0vh; padding: 1px; height: auto; text-align: center; padding: 0px; margin-bottom: 2em; }
	 .footer { line-height: 3.0em; text-align: center; font-family: monospace; font-size: 1.5vh; position:fixed; left:0px; bottom:0px; height:3em; width:100%; background:#3973ac; }
	 /* Responsive grid based on http://alijafarian.com/responsive-image-grids-using-css/ */
	 ul.rig { list-style: none; font-size: 0px; margin-left: -5.6%; /* should match li left margin */ }
	 ul.rig li { display: inline-block; padding: 10px; margin: 0 0 2.5% 2.5%; background: #fff; font-size: 16px; font-size: 1rem; vertical-align: top; box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box; }
	 ul.rig li img { max-width: 100%; height: auto; }
	 ul.rig li h3 { margin: 0 0 1px; }
	 ul.rig li p { font-size: .9em; line-height: 2.0em; color: #999; }
	 /* class for 1 column */
	 ul.rig.column-1 li { width: 43.3%; /* this value + 2.5 should = 50% */ }
	 /* class for 2 columns */
	 ul.rig.columns-2 li { width: 47.5%; /* this value + 2.5 should = 50% */ }
	 /* class for 3 columns */
	 ul.rig.columns-3 li { width: 30.83%; /* this value + 2.5 should = 33% */ }
	 /* class for 4 columns */
	 ul.rig.columns-4 li { width: 22.5%; /* this value + 2.5 should = 25% */ }
	 @media (max-width: 480px) {
	     ul.grid-nav li { display: block; margin: 0 0 5px; }
	     ul.grid-nav li a { display: block; }
	     ul.rig { margin-left: 0; }
	     ul.rig li { width: 100% !important; /* over-ride all li styles */ margin: 0 0 20px; }
	     * { margin: 0; padding: 0; }
	</style>

	<?php

	// Detect browser language
	$language = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);

	// basename and str_replace are used to prevent the path traversal attacks. Not very elegant, but it should do the trick.
	$photo_dir = str_replace(DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $photo_dir.DIRECTORY_SEPARATOR);

	/*
	 * Returns an array of latitude and longitude from the image file.
	 * @param image $file
	 * @return multitype:number |boolean
	 * http://stackoverflow.com/questions/5449282/reading-geotag-data-from-image-in-php
	 */
	function read_gps_location($file){
	    if (is_file($file)) {
		$info = exif_read_data($file);
		if (isset($info['GPSLatitude']) && isset($info['GPSLongitude']) &&
		    isset($info['GPSLatitudeRef']) && isset($info['GPSLongitudeRef']) &&
		    in_array($info['GPSLatitudeRef'], array('E','W','N','S')) && in_array($info['GPSLongitudeRef'], array('E','W','N','S'))) {

		    $GPSLatitudeRef  = strtolower(trim($info['GPSLatitudeRef']));
		    $GPSLongitudeRef = strtolower(trim($info['GPSLongitudeRef']));

		    $lat_degrees_a = explode('/',$info['GPSLatitude'][0]);
		    $lat_minutes_a = explode('/',$info['GPSLatitude'][1]);
		    $lat_seconds_a = explode('/',$info['GPSLatitude'][2]);
		    $lon_degrees_a = explode('/',$info['GPSLongitude'][0]);
		    $lon_minutes_a = explode('/',$info['GPSLongitude'][1]);
		    $lon_seconds_a = explode('/',$info['GPSLongitude'][2]);

		    $lat_degrees = $lat_degrees_a[0] / $lat_degrees_a[1];
		    $lat_minutes = $lat_minutes_a[0] / $lat_minutes_a[1];
		    $lat_seconds = $lat_seconds_a[0] / $lat_seconds_a[1];
		    $lon_degrees = $lon_degrees_a[0] / $lon_degrees_a[1];
		    $lon_minutes = $lon_minutes_a[0] / $lon_minutes_a[1];
		    $lon_seconds = $lon_seconds_a[0] / $lon_seconds_a[1];

		    $lat = (float) $lat_degrees+((($lat_minutes*60)+($lat_seconds))/3600);
		    $lon = (float) $lon_degrees+((($lon_minutes*60)+($lon_seconds))/3600);

		    // If the latitude is South, make it negative
		    // If the longitude is west, make it negative
		    $GPSLatitudeRef  == 's' ? $lat *= -1 : '';
		    $GPSLongitudeRef == 'w' ? $lon *= -1 : '';

		    return array(
			'lat' => $lat,
			'lon' => $lon
		    );
		}
	    }
	    return false;
	}

	// Check whether the required directories exist
	if (!file_exists($photo_dir) || !file_exists($photo_dir.'tims')) {
	    exit ('<p class="msg"><u>'.$photo_dir. '</u> or <u>'. $photo_dir.'tims</u> directory doesn\'t exist. You must create it manually. <a href="'.basename($_SERVER['PHP_SELF']).'">Back</a></p>');

	}

	// Get file info
	$files = glob($photo_dir.'*.{jpg,jpeg,JPG,JPEG}', GLOB_BRACE);
	$fileCount = count($files);

	function createTim($original, $tim, $timWidth)
	{
	    // Load image
	    $img = @imagecreatefromjpeg($original);
	    if(!$img) return false; // Abort if the image couldn't be read

	    // Get image size
	    $width = imagesx($img);
	    $height = imagesy($img);

	    // Calculate tim size
	    $new_width  = $timWidth;
	    $new_height = floor($height * ($timWidth / $width));

	    // Create a new temporary image
	    $tmp_img = imagecreatetruecolor($new_width, $new_height);

	    // Copy and resize old image into new image
	    imagecopyresampled($tmp_img, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

	    // Save tim into a file
	    $ok = @imagejpeg($tmp_img, $tim);

	    // Cleanup
	    imagedestroy($img);
	    imagedestroy($tmp_img);

	    // Return bool true if tim creation worked
	    return $ok;
	}

	// Generate missing tims
	for($i = 0; $i < $fileCount; $i++) {
	    $file  = $files[$i];
	    $tim = $photo_dir.'tims/'.basename($file);

	    if(!file_exists($tim)) {
		//Display a message while the function generates tims
		ob_implicit_flush(true);
		echo '<p class="msg">Generating missing tims...';
		ob_end_flush();
		createTim($file, $tim, 800);
		// A JavaScript hack to reload the page in order to clear the messages
		echo '<script>parent.window.location.reload(true);</script>';
	    }
	}

	// Update count (we might have removed some files)
	$fileCount = count($files);

	// Check whether the reversed order option is enabled and sort the array accordingly
	if($r_sort) {
	    rsort($files);
	}

	echo "<title>$title ($fileCount)</title>";
	echo "</head>";
	echo "<body>";
	echo "<div id='content'>";
	
	// Prepare pagination. Calculate total items per page * START
	$filetype = '*.*';
	$files = glob($photo_dir.$filetype);
	$total = count($files);
	$last_page = ceil($total / $per_page);
	if (isset($_GET["photo"]) == '')
	{
	    if(isset($_GET["page"]) && ($_GET["page"] <=$last_page) && ($_GET["page"] > 0) && ($_GET["all"] != 1) )
	    {
		$page = $_GET["page"];
		$offset = ($per_page + 1)*($page - 1); 
		echo "Page ".$_GET["page"]." of ".$last_page." (".$per_page." photos per page)."." Total of ".$fileCount." photos. ";
		echo '<a style="color: yellow;" href=//'.$_SERVER[HTTP_HOST].DIRECTORY_SEPARATOR.basename(__DIR__).'/index.php?all=1>Show all</a>';
	    }
	    else
	    {
		if(isset($_GET["all"]) != 1)
		{
		    echo "Page 1 of ".$last_page." (".$per_page." photos per page)."."  Total of ".$fileCount." photos. ";
		    echo '<a style="color: yellow;" href=//'.$_SERVER[HTTP_HOST].DIRECTORY_SEPARATOR.basename(__DIR__).'/index.php?all=1>Show all</a>';
		}
		$page=1;
		$offset=0;
	    }
	    if (isset($_GET['all']) == 1)
	    {$all = 1;}
	}
	$max = $offset + $per_page;
	if($max>$total)
	{
	    $max = $total; 
	}
	// Pagination. Calculate total items per page * END

	// The $grid parameter is used to show the main grid
	$grid = (isset($_GET['photo']) ? $_GET['photo'] : null);
	if (!isset($grid)) {
	    echo "<a style='text-decoration:none;' href='".basename($_SERVER['PHP_SELF'])."'><h1>".$title."</h1></a>";
	    echo "<div class ='center'>".$tagline."</div>";
	    echo "<ul class='rig columns-".$columns."'>";

	    if ($all == 1)
	    {
		for ($i=($fileCount-1); $i>=0; $i--) {
		    $file = $files[$i];
		    $tim = $photo_dir.'tims/'.basename($file);
		    $filepath = pathinfo($file);
		    echo '<li><a href="index.php?photo='.$file.'"><img src="'.$tim.'" alt="'.$filepath['filename'].'" title="'.$filepath['filename'].'"></a><h3>'.$filepath['filename'].'</h3></li>';
		}
	    }
	    else
	    {
		for($i = $offset; $i< $max; $i++){
		    if($r_sort) {
			rsort($files);
		    }
		    $file = $files[$i];
		    $tim = $photo_dir.'tims/'.basename($file);
		    $filepath = pathinfo($file);
		    echo '<li><a href="index.php?photo='.$file.'"><img src="'.$tim.'" alt="'.$filepath['filename'].'" title="'.$filepath['filename'].'"></a><h3>'.$filepath['filename'].'</h3></li>';
		}
	    }
	    
	    echo "</ul>";
	}
	
	if(isset($_GET["all"]) != 1)
	    
	{
	    show_pagination($page, $last_page); // Pagination. Show navigation on bottom of page
	}
	
	
	//Pagination. Create the navigation links * START
	function show_pagination($current_page, $last_page)
	{
	    echo '<div class="center">';
	    if( $current_page != 1 && isset($_GET["photo"]) == ''  )
	    {
		echo '<a style="color: #e3e3e3;" href="?page='."1".'">First</a> &bull; ';
	    }
	    if( $current_page > 1 && isset($_GET["photo"]) == '' )
	    {
		echo '<a style="color: #e3e3e3;" href="?page='.($current_page-1).'">Previous</a> &bull; ';
	    } 
	    if( $current_page < $last_page && isset($_GET["photo"]) == '' )
	    {
		echo '<a style="color: #e3e3e3;" href="?page='.($current_page+1).'">Next</a>'; 
	    }
	    if( $current_page != $last_page && isset($_GET["photo"]) == '' )
	    {
		echo ' &bull; <a style="color: #e3e3e3;" href="?page='.($last_page).'">Last</a>';
	    } 
	    echo '</div>';
	}
	//Pagination. Create the navigation links * END
	
	// The $photo parameter is used to show an individual photo
	$file = (isset($_GET['photo']) ? $_GET['photo'] : null);
	if (isset($file)) {
	    $key = array_search($file, $files); // Determine the array key of the current item (we need this for generating the Next and Previous links)
	    $tim = $photo_dir.'tims/'.basename($file);
	    $exif = exif_read_data($file, 0, true);
	    $filepath = pathinfo($file);

	    // Generate an optional short link
	    if($use_shortLink) {
		$short_link = exec("curl '".$shortLink_API."'");
            }else{
		$short_link = "#";
	    }

	    //Check if the related RAW file exists and link to it
	    $rawfile=glob($photo_dir.$filepath['filename'].$raw_formats, GLOB_BRACE);
	    if (!empty($rawfile)) {
		echo "<h1>".$filepath['filename']." <a class='superscript' href=".$rawfile[0].">RAW</a></h1>";
	    }
	    else {
		echo "<h1>".$filepath['filename']."</h1>";
	    }

	    // NAVIGATION LINKS
	    // Set first and last photo navigation links according to specified  sort order
	    $firstphoto = $files[count($files)-1];
	    $lastphoto = $files[0];

	    // If there is only one photo in the album, show the home navigation link
	    if ($fileCount == 1) {
		echo "<div class='center'><a href='".basename($_SERVER['PHP_SELF'])."' accesskey='g'>Grid</a> &bull; </div>";
	    }
	    // Disable the Previous link if this is the last photo
	    elseif (empty($files[$key+1])) {
		echo "<div class='center'><a href='".basename($_SERVER['PHP_SELF'])."' accesskey='g'>Grid</a> &bull; <a href='".basename($_SERVER['PHP_SELF'])."?photo=".$files[$key-1]."' accesskey='n'> Next</a> &bull; <a href='".basename($_SERVER['PHP_SELF'])."?photo=".$lastphoto."' accesskey='l'> Last</a></div>";
	    }
	    // Disable the Next link if this is the first photo
	    elseif (empty($files[$key-1])) {
		echo "<div class='center'><a href='".basename($_SERVER['PHP_SELF'])."' accesskey='g'>Grid</a> &bull; <a href='".basename($_SERVER['PHP_SELF'])."?photo=".$firstphoto."' accesskey='f'> First </a> &bull; <a href='".basename($_SERVER['PHP_SELF'])."?photo=".$files[$key+1]."' accesskey='p'>Previous</a></div>";
	    }
	    // Show all navigation links
	    else {
		
		echo "<div class='center'>
			<a href='".basename($_SERVER['PHP_SELF'])."' accesskey='g'>Grid</a> &bull; <a href='".basename($_SERVER['PHP_SELF'])."?photo=".$firstphoto."' accesskey='f'>First</a> &bull; <a href='".basename($_SERVER['PHP_SELF'])."?photo=".$files[$key+1]."' accesskey='p'>Previous</a> &bull; <a href='".basename($_SERVER['PHP_SELF'])."?photo=".$files[$key-1]."' accesskey='n'>Next</a> &bull; <a href='".basename($_SERVER['PHP_SELF'])."?photo=".$lastphoto."' accesskey='l'>Last</a></div>";
		
	    }
	    
	    // Check whether the localized description file matching the browser language exists
	    if (file_exists($photo_dir.$language.'-'.$filepath['filename'].'.txt')) {
		$description = @file_get_contents($photo_dir.$language.'-'.$filepath['filename'].'.txt');
		// If the localized description file doesn't exist, use the default one
	    } else {
		$description = @file_get_contents($photo_dir.$filepath['filename'].'.txt');
	    }
	    $gps = read_gps_location($file);
	    $shortened_link = " &bull; <a href='".$short_link."'><i class='fa fa-link'></i></a> ";

	    $fnumber = $exif['COMPUTED']['ApertureFNumber'];
	    if (empty($fnumber) ) {
		$fnumber = "";
	    } else {
		$fnumber = $fnumber." &bull; ";
	    }
	    $exposuretime=$exif['EXIF']['ExposureTime'];
	    if (empty($exposuretime)) {
		$exposuretime="";
	    } else {
		$exposuretime=$exposuretime." &bull; ";
	    }
	    $iso=$exif['EXIF']['ISOSpeedRatings'];
	    if (empty($iso)) {
		$iso="";
	    } else {
		$iso=$iso." &bull; ";
	    }
	    $datetime=$exif['EXIF']['DateTimeOriginal'];
	    if (empty($datetime)) {
		$datetime="";
	    }

	    //Generate map URL. Choose between Google Maps and OpenStreetmap
	    if ($google_maps){
		$map_url = " &bull; <a href='http://maps.google.com/maps?q=".$gps[lat].",".$gps[lon]."' target='_blank'><i class='fa fa-map-marker fa-lg'></i></a>";
	    } else {
		$map_url = " &bull; <a href='http://www.openstreetmap.org/index.html?mlat=".$gps[lat]."&mlon=".$gps[lon]."&zoom=18' target='_blank'><i class='fa fa-map-marker fa-lg'></i></a>";
	    }

	    $photo_info = $fnumber.$exposuretime.$iso.$datetime;
	    // Enable the short link anchor if short link is being used
	    if($use_shortLink){
		$photo_info = $photo_info.$shortened_link;
	    }
	    // Enable the Map anchor if the photo contains geographical coordinate
	    if (!empty($gps[lat])) {
		$photo_info = $photo_info.$map_url;
	    }

	    $info = "<span style='word-spacing:1em'>".$photo_info."</span>";
	    // Show photo, EXIF data, description, and info
	    echo '<div class="center"><ul class="rig column-1"><li><a href="'.$file.'"><img src="'.$tim.'" alt=""></a><p class="caption">'.$exif['COMMENT']['0'].' '.$description.'</p><p class="box">'.$info.'</p></li></ul></div>';
	}

	// Show links
	if ($links) {
	    $array_length = count($links);
	    echo '<div class="footer">';
	    for($i = 0; $i < $array_length; $i++) {
		echo '<span style="word-spacing:0.5em;"><a style="color: white" href="'.$links[$i][0].'"><i class="'.$links[$i][1].'"></i></a> </span>';
	    }
	    echo $footer.'</div>';
	} else {
	    echo '<div class="footer">'.$footer.'</div>';
	}
	?>
	<div>
	    <br>
	    <br>
	    <br>
	</body>
</html>
