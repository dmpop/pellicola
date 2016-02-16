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
	<link href='http://fonts.googleapis.com/css?family=Fira+Sans&subset=cyrillic,latin' rel='stylesheet' type='text/css'>
	<link href='http://fonts.googleapis.com/css?family=Quicksand:300,400,700' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
	<link rel="shortcut icon" href="favicon.ico" />

	<?php

	// User-defined settings
	$title = "Mejiro";
	$tagline = "No-frills open source photo grid";
	$footer="<a href='http://dmpop.github.io/mejiro/'>Mejiro</a> &mdash; pastebin for your photos";
	$expire = false;	// Set to true to enable the expiration feature.
	$days = 15;	// Expiration period.
	$stats = false;	// Enable web statistics (requires CrazyStat).
	$photo_dir = "photos"; // Directory for storing photos.
	$crazystat = "../crazystat/src/include.php"; //Path to the CrazyStat installation.
	$r_sort = false;	// Set to true to show tims in the reverse order (oldest ot newest).
	$google_maps = false;	// Set to true to use Google Maps instead of OpenStreetMap.
	$link_box = true;	// Enable the link box.
	// If the link box is enabled, specify the desired links and their icons in the array below.
	$links = array (
	array('https://www.flickr.com/photos/dmpop/','fa fa-flickr fa-lg'),
	array('http://scribblesandsnaps.com/','fa fa-wordpress fa-lg'),
	array('https://github.com/dmpop','fa fa-github fa-lg')
	);
	$raw_formats = '.{ARW,arw,NEF,nef,CR2,cr2,PNG,png}'; // Supported RAW formats. Specify other formats, if needed.
	?>

	<style>
		body { font: 15px/25px 'Fira Sans', sans-serif; text-align: justify; background-color: #303030; }
		a { color: #e3e3e3; }
		a.superscript { position: relative; top: -0.7em; font-size: 51%; text-decoration: none; }
		h1 { color: #e3e3e3; font: 39px/50% 'Quicksand', sans-serif; font-weight: 700; text-align: center; margin-top: 13px; margin-bottom: 7px; line-height: 100%; letter-spacing: 9px; }
		h2 { color: #e3e3e3; font: 19px/50% 'Quicksand', sans-serif; font-weight: 700; text-align: center; margin-top: 13px; margin-bottom: 7px; line-height: 100%; letter-spacing: 9px; }
		p { width: 800px; text-align: justify; }
		p.box { border-style: dotted; border-radius: 5px; width: 790px; border-width: 1px; font-size: 13px; padding: 4px; color: #e3e3e3; margin-bottom: 0px; text-align: center; }
		p.msg { margin-left: auto; margin-right: auto; margin-bottom: 0px; margin-top: 19px; border-radius: 5px; width: auto; border-width: 1px; font-size: 15px; letter-spacing: 3px; padding: 5px; color: #ffffff; background: #3399ff; text-align: center; width:500px; }
		p.center { font-size: 15px; padding: 1px; text-align: center; }
		img { vertical-align: middle; padding-right: 1px; }
		img.tim { max-width: 132px; max-height: 88px; width: auto; height: auto; }
		#content { margin: auto; width: 800px; color: #e3e3e3; }
		.text { text-align: center; padding: 0px; color: inherit; float: left; }
		.center { height: auto; text-align: center; padding: 0px; margin-left: auto; margin-right: auto; }
		.footer { text-align: center; font-family: monospace; font-size: 11px; }
	</style>
	
	<!--GitHub corner -->
	<a href="https://github.com/dmpop/mejiro" class="github-corner"><svg width="80" height="80" viewBox="0 0 250 250" style="fill:#64CEAA; color:#fff; position: absolute; top: 0; border: 0; left: 0; transform: scale(-1, 1);"><path d="M0,0 L115,115 L130,115 L142,142 L250,250 L250,0 Z"></path><path d="M128.3,109.0 C113.8,99.7 119.0,89.6 119.0,89.6 C122.0,82.7 120.5,78.6 120.5,78.6 C119.2,72.0 123.4,76.3 123.4,76.3 C127.3,80.9 125.5,87.3 125.5,87.3 C122.9,97.6 130.6,101.9 134.4,103.2" fill="currentColor" style="transform-origin: 130px 106px;" class="octo-arm"></path><path d="M115.0,115.0 C114.9,115.1 118.7,116.5 119.8,115.4 L133.7,101.6 C136.9,99.2 139.9,98.4 142.2,98.6 C133.8,88.0 127.5,74.4 143.8,58.0 C148.5,53.4 154.0,51.2 159.7,51.0 C160.3,49.4 163.2,43.6 171.4,40.1 C171.4,40.1 176.1,42.5 178.8,56.2 C183.1,58.6 187.2,61.8 190.9,65.4 C194.5,69.0 197.7,73.2 200.1,77.6 C213.8,80.2 216.3,84.9 216.3,84.9 C212.7,93.1 206.9,96.0 205.4,96.6 C205.1,102.4 203.0,107.8 198.3,112.5 C181.9,128.9 168.3,122.5 157.7,114.1 C157.9,116.9 156.7,120.9 152.7,124.9 L141.0,136.5 C139.8,137.7 141.6,141.9 141.8,141.8 Z" fill="currentColor" class="octo-body"></path></svg></a><style>.github-corner:hover .octo-arm{animation:octocat-wave 560ms ease-in-out}@keyframes octocat-wave{0%,100%{transform:rotate(0)}20%,60%{transform:rotate(-25deg)}40%,80%{transform:rotate(10deg)}}@media (max-width:500px){.github-corner:hover .octo-arm{animation:none}.github-corner .octo-arm{animation:octocat-wave 560ms ease-in-out}}</style>

	<?php

	// Detect browser language.
	$language = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
	
	// The $d parameter is used to detect a subdirectory.
	// basename and str_replace are used to prevent the path traversal attacks. Not very elegant, but it should do the trick.
        $sub_photo_dir = basename($_GET['d']).DIRECTORY_SEPARATOR;
	$photo_dir = str_replace(DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $photo_dir.DIRECTORY_SEPARATOR.$sub_photo_dir);

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

	// Create the required directories if they don't exist.
		if (!file_exists('photos')) {
		mkdir('photos', 0744, true);
	}
	if (!file_exists($photo_dir.'/tims')) {
		mkdir($photo_dir.'/tims', 0744, true);
	}

	// Get file info.
	$files = glob($photo_dir.'*.{jpg,jpeg,JPG,JPEG}', GLOB_BRACE);
	$fileCount = count($files);

	function createTim($original, $tim, $timWidth)
	{
		// Load image.
		$img = @imagecreatefromjpeg($original);
		if(!$img) return false; // Abort if the image couldn't be read

		// Get image size.
		$width = imagesx($img);
		$height = imagesy($img);

		// Calculate tim size.
		$new_width  = $timWidth;
		$new_height = floor($height * ($timWidth / $width));

		// Create a new temporary image.
		$tmp_img = imagecreatetruecolor($new_width, $new_height);

		// Copy and resize old image into new image
		imagecopyresampled($tmp_img, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

		// Save tim into a file.
		$ok = @imagejpeg($tmp_img, $tim);

		// Cleanup.
		imagedestroy($img);
		imagedestroy($tmp_img);

		// Return bool true if tim creation worked
		return $ok;
	}

	// Generate any missing tim and check expiration.
	for($i = 0; $i < $fileCount; $i++) {
		$file  = $files[$i];
		$tim = $photo_dir.'tims/'.basename($file);

		if(!file_exists($tim)) {
			//Display a message while the function generates a tim.
			ob_implicit_flush(true);
			echo '<p class="msg">Generating a tim for '.basename($file).'</p>';
			ob_end_flush();
			if(createTim($file, $tim, 800)) {
				// This is a new file, update last modification date for the expiration feature.
				touch($file);
			} else {
				// We couldn't create a tim, remove the image from our list.
				unset($files[$i]);
			}
		// A JavaScript hack to reload the page in order to clear the messages.
		echo '<script>parent.window.location.reload(true);</script>';
		}

		if($expire && (time() - filemtime($file) >= $days * 24 * 60 * 60) ) {
			unlink($file);
			unlink($tim);
			unset($files[$i]);
		}
	}

	// Update count (we might have removed some files).
	$fileCount = count($files);

	echo "<title>$title ($fileCount)</title>";
	echo "</head>";
	echo "<body>";
	echo "<div id='content'>";

	// The $grid parameter is used to show the main grid.
	$grid = (isset($_GET['photo']) ? $_GET['photo'] : null);
	if (!isset($grid)) {
		echo "<a style='text-decoration:none;' href='".basename($_SERVER['PHP_SELF'])."'><h1>".$title."</h1></a>";
		echo "<p class ='center'>".$tagline."</p>";
		echo "<p class='center'>";
		// Check whether the reversed order option is enabled and sort the array accordingly.
		if($r_sort) {
			rsort($files);
		}
		for ($i=($fileCount-1); $i>=0; $i--) {
			$file = $files[$i];
			$tim = $photo_dir.'tims/'.basename($file);
			$filepath = pathinfo($file);
			echo '<a href="index.php?photo='.$file.'&d='.$sub_photo_dir.'"><img class="tim" src="'.$tim.'" alt="'.$filepath['filename'].'" title="'.$filepath['filename'].'"></a>';
		}
		echo "</p>";
	}

	// The $photo parameter is used to show an individual photo.
	$file = (isset($_GET['photo']) ? $_GET['photo'] : null);
	if (isset($file)) {
		$key = array_search($file, $files); // Determine the array key of the current item (we need this for generating the Next and Previous links).
		$tim = $photo_dir.'tims/'.basename($file);
		$exif = exif_read_data($file, 0, true);
		$filepath = pathinfo($file);
		// Generate a short link using is.dg 
		$short_link = exec("curl 'https://is.gd/create.php?format=simple&url=http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]'");
		//Check if the related RAW file exists and link to it.
		$rawfile=glob($photo_dir.$filepath['filename'].$raw_formats, GLOB_BRACE);
		if (!empty($rawfile)) {
			echo "<h1>".$filepath['filename']." <a class='superscript' href=".$rawfile[0].">RAW</a></h1>";
		}
		else {
			echo "<h1>".$filepath['filename']."</h1>";
		}
		echo "<p>";
		// Check whether the localized description file matching the browser language exists.
		if (file_exists($photo_dir.$language.'-'.$filepath['filename'].'.txt')) {
			echo @file_get_contents($photo_dir.$language.'-'.$filepath['filename'].'.txt');
			// If the localized description file doesn't exist, use the default one
			} else {
			echo @file_get_contents($photo_dir.$filepath['filename'].'.txt');
		}
		echo $exif['COMPUTED']['UserComment'];
		echo "</p>";
		echo '<a href="'.$file.'"><img src="'.$tim.'" alt=""></a>';
		$gps = read_gps_location($file);
		$shortened_link = "<a href='".$short_link."'><i class='fa fa-link'></i></a> ";

		$fnumber_array = explode("/", $exif['EXIF']['FNumber']);
		$fnumber = $fnumber_array[0]/$fnumber_array[1];
		if (empty($fnumber_array[0]) ) {
			$fnumber = "";
		} else {
			$fnumber = $fnumber_array[0]/$fnumber_array[1];
			$fnumber = "&fnof;/".$fnumber." &bull; ";
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
		} else {
			$datetime=$datetime." &bull; ";
		}
		// Parse IPTC metadata and extract keywords.
		// http://stackoverflow.com/questions/9050856/finding-keywords-in-image-data
		$size = getimagesize($file, $info);
		if(isset($info['APP13'])) {
			$iptc = iptcparse($info['APP13']);
				if(isset($iptc['2#025'])) {
					$keywords = $iptc['2#025'];
				} else {
					$keywords = array();
				}
		}
		$keyword = implode(", ", $keywords);

		//Generate map URL. Choose between Google Maps and OpenStreetmap.
		if ($google_maps){
			$map_url = " &bull; <a href='http://maps.google.com/maps?q=".$gps[lat].",".$gps[lon]."' target='_blank'><i class='fa fa-map-marker fa-lg'></i></a>";
		} else {
			$map_url = " &bull; <a href='http://www.openstreetmap.org/index.html?mlat=".$gps[lat]."&mlon=".$gps[lon]."&zoom=18' target='_blank'><i class='fa fa-map-marker fa-lg'></i></a>";
		}

		// Disable the Map link if the photo has no geographical coordinates.
		if (empty($gps[lat])) {
			      echo "<p class='box'><span style='word-spacing:9px'>".$fnumber.$exposuretime.$iso.$datetime.$shortened_link."<br /><i class='fa fa-tags'></i> </span>".$keyword."</p>";
		}
		else {
		        echo "<p class='box'><span style='word-spacing:9px'>".$fnumber.$exposuretime.$iso.$datetime.$shortened_link.$map_url."<br /><i class='fa fa-tags'></i> </span>".$keyword."</p>";
		}

		// If there is only one photo in the album, show the home navigation tim.
		if ($fileCount == 1) {
                    echo "<p class='center'><a href='".basename($_SERVER['PHP_SELF']).'?d='.$sub_photo_dir."' accesskey='h'>Grid</a> &bull; </p>";
		}
		// Disable the Previous link if this is the last photo.
		elseif (empty($files[$key+1])) {
                    echo "<p class='center'><a href='".basename($_SERVER['PHP_SELF']).'?d='.$sub_photo_dir."' accesskey='g'>Grid</a> &bull; <a href='".basename($_SERVER['PHP_SELF'])."?photo=".$files[$key-1].'&d='.$sub_photo_dir."' accesskey='n'>Next</a> &bull; <a href='".basename($_SERVER['PHP_SELF'])."?photo=".min($files).'&d='.$sub_photo_dir."' accesskey='l'>Last</a></p>";
		}
		// Disable the Next link if this is the first photo.
		elseif (empty($files[$key-1])) {
                    echo "<p class='center'><a href='".basename($_SERVER['PHP_SELF'])."?photo=".max($files).'&d='.$sub_photo_dir."' accesskey='h'>First</a> &bull; <a href='".basename($_SERVER['PHP_SELF'])."?photo=".$files[$key+1].'&d='.$sub_photo_dir."' accesskey='p'>Previous</a></p>";
		}
		// Show all navigation tims.
		else {
                    echo "<p class='center'><a href='".basename($_SERVER['PHP_SELF'])."?photo=".max($files).'&d='.$sub_photo_dir."' accesskey='f'>First</a> &bull; <a href='".basename($_SERVER['PHP_SELF'])."?photo=".$files[$key+1].'&d='.$sub_photo_dir."' accesskey='p'>Previous</a> &bull; <a href='".basename($_SERVER['PHP_SELF'])."?photo=".$files[$key-1].'&d='.$sub_photo_dir."' accesskey='n'>Next</a> &bull; <a href='".basename($_SERVER['PHP_SELF'])."?photo=".min($files).'&d='.$sub_photo_dir."' accesskey='l'>Last</a></p>";
		}
	}
	
	// Show link box.
	if ($link_box) {
            $array_length = count($links);
            echo '<div class="center">';
            for($i = 0; $i < $array_length; $i++) {
            echo '<span style="word-spacing:9px;"><a href="'.$links[$i][0].'"><i class="'.$links[$i][1].'"></i></a> </span>';
            }
            echo "</div>";
	}

	// The $menu parameter is used to show the menu.
	$menu = (isset($_GET['menu']) ? $_GET['menu'] : null);
	if (isset($menu)) {
		echo '<p class="box"><a href="'.$_SERVER['PHP_SELF'].'?upload&d='.$sub_photo_dir.'"><i class="fa fa-upload fa-lg"></i></a> Show upload form --- <a href="'.$_SERVER['PHP_SELF'].'?d='.$sub_photo_dir.'"><i class="fa fa-times fa-lg"></i></a> Close menu</p>';
	}

	echo '<div class="footer">'.$footer.'</div>';

	if ($stats) {
	echo '<p class="center">';
	if (file_exists($crazystat) && is_readable($crazystat)) {
		include_once($crazystat);
			} else {
			echo '<p class="msg">CrazyStat is not installed.</p>';
		}
	echo '</p>';
	}

	?>
	</div>
	</body>
</html>
