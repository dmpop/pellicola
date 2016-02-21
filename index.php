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
		body { font-family: 'Fira Sans', sans-serif; font-size: 2.0vh; text-align: justify; background-color: #303030; }
		a { color: #e3e3e3; }
		a.superscript { position: relative; top: -0.7em; font-size: 51%; text-decoration: none; }
		h1 { color: #e3e3e3; font-family: 'Quicksand', sans-serif; font-size: 5.7vh; font-weight: 700; text-align: center; margin-top: 0.3em; margin-bottom: 0.5em; line-height: 100%; letter-spacing: 9px; }
		h2 { color: #e3e3e3; font-family: 'Quicksand', sans-serif; font-size: 3.0vh; font-weight: 700; text-align: center; margin-top: 1em; margin-bottom: 0.5em; line-height: 100%; letter-spacing: 9px; }
		h3 { color: #e3e3e3; font-family: 'Quicksand', sans-serif; font-size: 2.0vh; font-weight: 700; text-align: center; margin-top: 1em; margin-bottom: 0.5em; line-height: 100%; letter-spacing: 2px; }
		p { font-size: 2.0vh; text-align: justify; }
		p.msg { margin-left: auto; margin-right: auto; margin-bottom: 0px; margin-top: 0.5em; border-radius: 5px; width: auto; border-width: 1px; font-size: 2.0vh; letter-spacing: 3px; padding: 5px; color: #ffffff; background: #3399ff; text-align: center; width:500px; }
		p.center { font-size: 2.0vh; margin-bottom: 2em; padding: 1px; text-align: center; }
                p.box { border-style: dotted; border-width: 1px; font-size: 2.0vh; padding: 5px; color: #e3e3e3; margin-bottom: 0px; margin-left: auto; margin-right: auto; line-height: 2.0em; text-align: center; }
		#content { color: #e3e3e3; }
		.text { text-align: center; padding: 0px; color: inherit; float: left; }
		.center { height: auto; text-align: center; padding: 0px; margin-left: auto; margin-right: auto; margin-bottom: 2em; }
		.footer { line-height: 5em; text-align: center; font-family: monospace; font-size: 1.5vh; }
		/* Responsive grid based on http://alijafarian.com/responsive-image-grids-using-css/ */
                ul.rig { list-style: none; font-size: 0px; margin-left: -5.7%; /* should match li left margin */ }
                ul.rig li { display: inline-block; padding: 10px; margin: 0 0 2.5% 2.5%; background: #fff; font-size: 16px; font-size: 1rem; vertical-align: top; box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box; }
                ul.rig li img { max-width: 100%; height: auto; }
                ul.rig li h3 { margin: 0 0 1px; }
                ul.rig li p { font-size: .9em; line-height: 2.0em; color: #999; }
                /* class for 1 column */
                ul.rig.column-1 li { width: 52.5%; /* this value + 2.5 should = 50% */ }
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
                }
	</style>

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
		echo "<ul class='rig columns-4'>";
		// Check whether the reversed order option is enabled and sort the array accordingly.
		if($r_sort) {
			rsort($files);
		}
		for ($i=($fileCount-1); $i>=0; $i--) {
			$file = $files[$i];
			$tim = $photo_dir.'tims/'.basename($file);
			$filepath = pathinfo($file);
			echo '<li><a href="index.php?photo='.$file.'&d='.$sub_photo_dir.'"><img src="'.$tim.'" alt="'.$filepath['filename'].'" title="'.$filepath['filename'].'"></a><h3>'.$filepath['filename'].'</h3></li>';
		}
		echo "</ul>";
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
		// Check whether the localized description file matching the browser language exists.
		if (file_exists($photo_dir.$language.'-'.$filepath['filename'].'.txt')) {
			$description = @file_get_contents($photo_dir.$language.'-'.$filepath['filename'].'.txt');
			// If the localized description file doesn't exist, use the default one
			} else {
			$description = @file_get_contents($photo_dir.$filepath['filename'].'.txt');
		}
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
			      $info = "<span style='word-spacing:1em'>".$fnumber.$exposuretime.$iso.$datetime.$shortened_link."<br /><i class='fa fa-tags'></i> </span>".$keyword;
		}
		else {
		        $info = "<span style='word-spacing:1em'>".$fnumber.$exposuretime.$iso.$datetime.$shortened_link.$map_url."<br /><i class='fa fa-tags'></i> </span>".$keyword;
		}
		
		echo '<div class="center"><ul class="rig column-1"><li><a href="'.$file.'"><img src="'.$tim.'" alt=""></a><p>'.$description.' '.$exif['COMPUTED']['UserComment'].'</p><p class="box">'.$info.'</p></li></ul></div>';

		// If there is only one photo in the album, show the home navigation link.
		if ($fileCount == 1) {
                    echo "<div class='center'><a href='".basename($_SERVER['PHP_SELF']).'?d='.$sub_photo_dir."' accesskey='h'>Grid</a> &bull; </div>";
		}
		// Disable the Previous link if this is the last photo.
		elseif (empty($files[$key+1])) {
                    echo "<div class='center'><a href='".basename($_SERVER['PHP_SELF']).'?d='.$sub_photo_dir."' accesskey='g'>Grid</a> &bull; <a href='".basename($_SERVER['PHP_SELF'])."?photo=".$files[$key-1].'&d='.$sub_photo_dir."' accesskey='n'>Next</a> &bull; <a href='".basename($_SERVER['PHP_SELF'])."?photo=".min($files).'&d='.$sub_photo_dir."' accesskey='l'>Last</a></div>";
		}
		// Disable the Next link if this is the first photo.
		elseif (empty($files[$key-1])) {
                    echo "<div class='center'><a href='".basename($_SERVER['PHP_SELF']).'?d='.$sub_photo_dir."' accesskey='h'>Grid</a> &bull; <a href='".basename($_SERVER['PHP_SELF'])."?photo=".max($files).'&d='.$sub_photo_dir."' accesskey='h'>First</a> &bull; <a href='".basename($_SERVER['PHP_SELF'])."?photo=".$files[$key+1].'&d='.$sub_photo_dir."' accesskey='p'>Previous</a></div>";
		}
		// Show all navigation links.
		else {
                    echo "<div class='center'><a href='".basename($_SERVER['PHP_SELF']).'?d='.$sub_photo_dir."' accesskey='h'>Grid</a> &bull; <a href='".basename($_SERVER['PHP_SELF'])."?photo=".max($files).'&d='.$sub_photo_dir."' accesskey='f'>First</a> &bull; <a href='".basename($_SERVER['PHP_SELF'])."?photo=".$files[$key+1].'&d='.$sub_photo_dir."' accesskey='p'>Previous</a> &bull; <a href='".basename($_SERVER['PHP_SELF'])."?photo=".$files[$key-1].'&d='.$sub_photo_dir."' accesskey='n'>Next</a> &bull; <a href='".basename($_SERVER['PHP_SELF'])."?photo=".min($files).'&d='.$sub_photo_dir."' accesskey='l'>Last</a></div>";
		}
	}
	
	// Show link box.
	if ($link_box) {
            $array_length = count($links);
            echo '<div class="center">';
            for($i = 0; $i < $array_length; $i++) {
            echo '<span style="word-spacing:1.5em;"><a href="'.$links[$i][0].'"><i class="'.$links[$i][1].'"></i></a> </span>';
            }
            echo "</div>";
	}

	echo '<div class="footer">'.$footer.'</div>';

	if ($stats) {
	echo '<div class="center">';
	if (file_exists($crazystat) && is_readable($crazystat)) {
		include_once($crazystat);
			} else {
			echo '<p class="msg">CrazyStat is not installed.</p>';
		}
	echo '</div>';
	}
	?>
	<div>
    </body>
</html>
