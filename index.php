<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">

<html>

<!--
	Author: Dmitri Popov
	License: GPLv3 https://www.gnu.org/licenses/gpl-3.0.txt
	Source code: https://github.com/dmpop/photocrumbs
-->

	<head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	<link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
	<link rel="shortcut icon" href="favicon.ico" />

	<?php

	// User-defined settings
	$title = 'Photocrumbs';
	$footer='Powered by <a href="https://github.com/dmpop/photocrumbs">Photocrumbs</a>';
	$quote='Which of my photographs is my favourite? The one I\'m going to take tomorrow. --Imogen Cunningham (1883 – 1976)';
	$expire = false; //set to true to enable the expiration feature
	$days = 15; // expiration period
	$log = false; //set to true to enable ip logging
	// ----------------------------

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
				// this is a new file, update last mod for expiry feature
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

	function showPhoto($file) {
		$thumb = "photos/thumbs/".basename($file);
		$exif = exif_read_data($file, 0, true);
		$filepath = pathinfo($file);
		echo "<h1>".$filepath['filename']."</h1>";
		echo "<p>";
		@include 'photos/'.$filepath['filename'].'.php';
		echo $exif['COMPUTED']['UserComment'];
		echo "</p>";
		echo '<a href="'.$file.'"><img class="dropshadow" src="'.$thumb.'" alt=""></a>';
		$fstop = explode("/", $exif['EXIF']['FNumber']);
		$fstop = $fstop[0] / $fstop[1];
		echo "<p class='box'>Aperture: <strong>f/".$fstop."</strong> Shutter speed: <strong>" .$exif['EXIF']['ExposureTime']. "</strong> ISO: <strong>".$exif['EXIF']['ISOSpeedRatings']. "</strong></p>";
	}

// Version of the showPhoto function that displays all thumbnails as a pseudo table
	function showPhotoTbl($file) {
		$thumb = "photos/thumbs/".basename($file);
		$filepath = pathinfo($file);
		echo '<a href="index.php?id='.$file.'&view=1"><img src="'.$thumb.'" alt="" width=128 hspace="1"></a>';
	}

	echo "<title>$title</title>";
	echo "</head>";
	echo "<body>";

	echo "<a class='title' href='".basename($_SERVER['PHP_SELF'])."'><img class='logo' src='photocrumbs.svg' width='175' /></a>";
	echo "<p class='quote'><em>".$quote."</em></p>";
	echo "<div id='content'>";

// Get the $view parameter from the URL. If $view is not empty, show thumbnails as a pseudo table.
	$view = $_GET['view'];
	if (empty($view)) {
		echo "<h1>".$fileCount." ".$title."</h1>";
		echo "<p>";
		for ($i=($fileCount-1); $i>=0; $i--) {
			$file = $files[$i];
			showPhotoTbl($file);
		}
	}

	// Get the $id parameter from the URL. If $id is not empty, then show only the specified photo
	$file = $_GET['id'];
	if (!empty($file)) {
		showPhoto($file);
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
			font: 15px/175% 'Open Sans', sans-serif;
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
			text-align: left;
			margin-top: 25px;
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
		p {
			width: 800px;
			text-align: justify;
			}
		p.quote {
			font: 11px/175% 'Open Sans', sans-serif;
			text-align: center;
			color: #e3e3e3;
			margin-top: 171px;
			margin-left: -1px;
			width:175px;
			padding-bottom: 3px;
			padding-top: 3px;
			padding-left: 5px;
			padding-right: 7px;
			position:fixed;
		}
		img.logo {
			margin-top: 21px;
			margin-left: -1px;
			padding-bottom: 3px;
			padding-top: 3px;
			padding-left: 5px;
			padding-right: 7px;
			position:fixed;
			}
		img {
			vertical-align: text-bottom;
			}
		#content {
			position: absolute;
			left: 235px;
			width: 800px;
			color: #E3E3E3;
			}
		.text {
			width: 530px;
			text-align: left;
			padding: 0px;
			margin-right: 20px;
			color: inherit;
			float: left;
			}
		.center {
			width: 530px;
			height: auto;
			text-align: center;
			padding: 0px;
			margin-left: auto;
			margin-right: auto;
			}
		.footer {
			width: 615px;
			text-align: center;
			font-family: monospace;
			font-size: 11px;
			margin-top: 15px;
			}
		</style>

	</body>
</html>
