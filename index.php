<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">

<html>

<!--
	Author: Dmitri Popov
	License: GPLv3 https://www.gnu.org/licenses/gpl-3.0.txt
	Source code: https://github.com/dmpop/mejiro
-->

	<head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	<link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
	<link rel="shortcut icon" href="favicon.ico" />

	<?php

	// User-defined settings
	$title = "Mejiro &mdash; 目白";
	$footer="Powered by <a href='https://github.com/dmpop/mejiro'>Mejiro</a> &mdash; pastebin for your photos";
	$expire = false; //set to true to enable the expiration feature
	$days = 15; // expiration period
	$log = false; //set to true to enable IP logging
	// ----------------------------

	/**
 * Returns an array of latitude and longitude from the Image file
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
            $lng_degrees_a = explode('/',$info['GPSLongitude'][0]);
            $lng_minutes_a = explode('/',$info['GPSLongitude'][1]);
            $lng_seconds_a = explode('/',$info['GPSLongitude'][2]);

            $lat_degrees = $lat_degrees_a[0] / $lat_degrees_a[1];
            $lat_minutes = $lat_minutes_a[0] / $lat_minutes_a[1];
            $lat_seconds = $lat_seconds_a[0] / $lat_seconds_a[1];
            $lng_degrees = $lng_degrees_a[0] / $lng_degrees_a[1];
            $lng_minutes = $lng_minutes_a[0] / $lng_minutes_a[1];
            $lng_seconds = $lng_seconds_a[0] / $lng_seconds_a[1];

            $lat = (float) $lat_degrees+((($lat_minutes*60)+($lat_seconds))/3600);
            $lng = (float) $lng_degrees+((($lng_minutes*60)+($lng_seconds))/3600);

            //If the latitude is South, make it negative.
            //If the longitude is west, make it negative
            $GPSLatitudeRef  == 's' ? $lat *= -1 : '';
            $GPSLongitudeRef == 'w' ? $lng *= -1 : '';

            return array(
                'lat' => $lat,
                'long' => $lng
            );
        }
    }
    return false;
}

	// Create the required directories if they don't exist
		if (!file_exists('photos')) {
		mkdir('photos', 0777, true);
	}
	if (!file_exists('photos/thumbs')) {
		mkdir('photos/thumbs', 0777, true);
	}

	// get file info
	$files = glob("photos/*.{jpg,jeg,JPG,JPEG}", GLOB_BRACE);
	$fileCount = count($files);

	function createThumb($original, $thumb, $thumbWidth)
	{
		// load image
		$img = @imagecreatefromjpeg($original);
		if(!$img) return false; // we couldn't read the image, abort

		// get image size
		$width = imagesx($img);
		$height = imagesy($img);

		// calculate thumbnail size
		$new_width  = $thumbWidth;
		$new_height = floor($height * ($thumbWidth / $width));

		// create a new temporary image
		$tmp_img = imagecreatetruecolor($new_width, $new_height);

		// copy and resize old image into new image
		imagecopyresampled($tmp_img, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

		// save thumbnail into a file
		$ok = @imagejpeg($tmp_img, $thumb);

		// cleanup
		imagedestroy($img);
		imagedestroy($tmp_img);

		// return bool true if thumbnail creation worked
		return $ok;
	}

	// Generate any missing thumbnails and check expiration
	for($i = 0; $i < $fileCount; $i++) {
		$file  = $files[$i];
		$thumb = "photos/thumbs/".basename($file);

		if(!file_exists($thumb)) {
			if(createThumb($file, $thumb, 800)) {
				// this is a new file, update last mod for expiration feature
				touch($file);
			} else {
				// we couldn't create a thumbnail remove the image from our list
				unset($files[$i]);
			}
		}

		if($expire && (time() - filemtime($file) >= $days * 24 * 60 * 60) ) {
			unlink($file);
			unlink($thumb);
			unset($files[$i]);
		}
	}

	// update count - we might have removed some files
	$fileCount = count($files);

	echo "<title>$title</title>";
	echo "</head>";
	echo "<body>";
	echo "<div id='content'>";

	// The $t parameter is used to hide the thumbnails
	$view = $_GET['t'];
	if (empty($view)) {
		echo "<h1>".$title."</h1>";
		echo "<p>";
		for ($i=($fileCount-1); $i>=0; $i--) {
			$file = $files[$i];
			$thumb = "photos/thumbs/".basename($file);
			$filepath = pathinfo($file);
			echo '<a href="index.php?p='.$file.'&t=1"><img src="'.$thumb.'" alt="'.$filepath['filename'].'" title="'.$filepath['filename'].'" width=128 hspace="1"></a>';
		}
	}

	// The $p parameter is used to display an individual photo
	$file = $_GET['p'];
	if (!empty($file)) {
		$key = array_search($file, $files); // determine the array key of the current item (we need this for generating the Next and Previous links)
		$thumb = "photos/thumbs/".basename($file);
		$exif = exif_read_data($file, 0, true);
		$filepath = pathinfo($file);
		echo "<h1>".$filepath['filename']."</h1>";
		echo "<p>";
		echo file_get_contents('photos/'.$filepath['filename'].'.txt');
		echo $exif['COMPUTED']['UserComment'];
		echo "</p>";
		echo '<a href="'.$file.'"><img class="dropshadow" src="'.$thumb.'" alt=""></a>';
		$gps = read_gps_location($file);
		$fstop = explode("/", $exif['EXIF']['FNumber']);
		$fstop = $fstop[0] / $fstop[1];
		if (empty($fstop)) {
			$fstop = "n/a";
		}
		$exposuretime=$exif['EXIF']['ExposureTime'];
		if (empty($exposuretime)) {
			$exposuretime="n/a";
		}
		$iso=$exif['EXIF']['ISOSpeedRatings'];
		if (empty($iso)) {
			$iso="n/a";
		}
		$datetime=$exif['EXIF']['DateTimeOriginal'];
		if (empty($datetime)) {
			$datetime="n/a";
		}
		// Parse IPTC metadata and extract keywords
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

		echo "<p class='box'>f/".$fstop." | " .$exposuretime. " | ".$iso. " | ".$datetime." | <a href='http://www.openstreetmap.org/index.html?mlat=".$gps[lat]."&mlon=".$gps[long]."&zoom=18' target='_blank'>Map</a><br />".$keyword."</p>";
		echo "<p class='center'><a href='".basename($_SERVER['PHP_SELF'])."'>Home</a> | <a href='".basename($_SERVER['PHP_SELF'])."?p=".$files[$key+1]."&t=1'>Next</a> | <a href='".basename($_SERVER['PHP_SELF'])."?p=".$files[$key-1]."&t=1'>Previous</a></p>";
	}
	echo "<div class='footer'>$footer</div>";

	if ($log) {
		$ip=$_SERVER['REMOTE_ADDR'];
		$date = $date = date('Y-m-d H:i:s');
		$file = fopen("ip.log", "a+");
		fputs($file, " $ip  $page $date \n");
		fclose($file);
	}

	?>
	</div>

		<style>
		body {
			font: 15px/25px 'Open Sans', sans-serif;
			text-align: justify;
			background-color: #777777;
			}
		a {
			color: #e3e3e3;
			}
		a.title {
			text-decoration: none;
			color: #FFFFFF;
			}
		h1 {
			color: #E3E3E3;
			font: 29px/50% 'Open Sans', sans-serif;
			font-weight: 400;
			text-align: center;
			margin-top: 13px;
			margin-bottom: 7px;
			line-height: 100%;
			text-shadow: 1px 1px 1px #585858;
			letter-spacing: 5px;
			}
		p.box {
			border-style: dashed;
			width: 788px;
			border-width: 1px;
			font-size: 12px;
			padding: 5px;
			color: #e3e3e3;
			margin-bottom: 0px;
			text-align: center;
			}
		p.center {
			font-size: 12px;
			padding: 1px;
			text-align: center;
			}
		p {
			width: 800px;
			text-align: justify;
			}
		img {
			vertical-align: text-bottom;
			}
		#content {
			margin: 0px auto;
			width: 800px;
			color: #E3E3E3;
			}
		.text {
			text-align: left;
			padding: 0px;
			margin-right: 20px;
			color: inherit;
			float: left;
			}
		.center {
			height: auto;
			text-align: center;
			padding: 0px;
			margin-left: auto;
			margin-right: auto;
			}
		.footer {
			text-align: center;
			font-family: monospace;
			font-size: 11px;
			}
		</style>

	</body>
</html>
