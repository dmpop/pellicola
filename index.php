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

	<style>
		body {
			font: 15px/175% 'Open Sans', sans-serif;
			text-align: justify;
			background-color: #777777 ;
		}
		h1 {
			color: #cfcfcf;
			font: 41px 'Open Sans', sans-serif;
			font-weight: 700;
			text-align: center;
			margin-top: 35px;
			margin-bottom: 11px;
			padding-bottom: 11px;
			padding-left: 0px;
			border-style: dashed;
			border-top: none;
			border-left: none;
			border-right: none;
			border-bottom: thick dotted;
			text-shadow: 1px 1px 1px #585858;
			letter-spacing: 26px;
		}
		a {
			color: #e3e3e3;
		}
		a.title {
			text-decoration: none;
		}
		h2 {
			color: #e3e3e3;
			font: 29px/50% 'Open Sans', sans-serif;
			font-weight: 400;
			text-align: left;
			margin-top: 39px;
			margin-bottom: 7px;
			line-height: 100%;
			text-shadow: 1px 1px 1px #585858;
			letter-spacing: 5px;
		}
		p.box {
			border-style: dashed;
			width: 589px;
			border-width: 1px;
			font-size: 12px;
			padding: 5px;
			color: #e3e3e3;
			margin-bottom: 0px;
			text-align: center;
		}
		p {
			width: 600px;
			text-align: justify;
		}
		img.dropshadow {
			box-shadow: 5px 5px 25px -2px #585858;
		}
		img {
			vertical-align: text-bottom;
		}
		#content {
			position: absolute;
			top: 10%;
			left: 50%;
			margin-top: -75px;
			margin-left: -300px;
			width: 600px;
			height: auto;
			color: #e3e3e3;
		}
		.text {
			width: 530px;
			height: auto;
			text-align: left;
			padding: 0px;
			margin: 0px;
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
			margin: 0px;
			margin-top: 15px;
		}
	</style>

	<?php

	// User-defined settings
	$title = 'PHOTOCRUMBS';
	$tagline=" -- Uncomplicated photo publishing --";
	$basedir='photos/';
	$footer='Powered by <a href="https://github.com/dmpop/photocrumbs">Photocrumbs</a>';
	$expire = 'false'; //set to 'true' to enable the expiration feature
	$days = 15; // expiration period
	// ----------------------------

	// Create the required directories if it don't exist
		if (!file_exists($basedir)) {
		mkdir($basedir, 0777, true);
	}
	if (!file_exists($basedir.'thumbs')) {
		mkdir($basedir.'thumbs', 0777, true);
	}

	// http://webcheatsheet.com/php/create_thumbnail_images.php
	function createThumbs($pathToImages, $pathToThumbs, $thumbWidth)
	{
		// open the directory
		$dir = opendir($pathToImages);

		// loop through it, looking for any/all JPG files:
		while (false !== ($fname = readdir($dir))) {
			// parse path for the extension
			$info = pathinfo($pathToImages . $fname);
			// continue only if this is a JPEG image
			if (strtolower($info['extension']) == 'jpg')
			{

				// load image and get image size
				$img = imagecreatefromjpeg("{$pathToImages}{$fname}");
				$width = imagesx($img);
				$height = imagesy($img);

				// calculate thumbnail size
				$new_width = $thumbWidth;
				$new_height = floor( $height * ($thumbWidth / $width));

				// create a new temporary image
				$tmp_img = imagecreatetruecolor($new_width, $new_height);

				// copy and resize old image into new image
				imagecopyresampled($tmp_img, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

				// save thumbnail into a file
				imagejpeg($tmp_img, "{$pathToThumbs}{$fname}");
			}
		}
		// close the directory
		closedir($dir);
	}
	// call createThumb function and pass to it as parameters the path
	// to the directory that contains images, the path to the directory
	// in which thumbnails will be placed and the thumbnail's width.
	// We are assuming that the path will be a relative path working
	// both in the filesystem, and through the web for links
	// createThumbs($basedir,$basedir."thumbs/",500);

	// http://stackoverflow.com/questions/9673866/checking-if-thumbnail-exists-in-php
// Open directory, and proceed to read its contents
	if (is_dir($basedir)) {
		if ($dh = opendir($basedir)) {
		// Walk through directory, $file by $file
		while (($file = readdir($dh)) !== false) {
			// Make sure we're dealing with jpegs
			if (preg_match('/\.jpg$/i', $file)) {
			// don't bother processing things that already have thumbnails
			if (!file_exists($basedir . "thumbs/" . $file)) {
				createThumbs($basedir,$basedir."thumbs/",600);
				touch ($file);
				}
			}
		}
		// clean up after ourselves
		closedir($dh);
		}
	}

	// If $expire set to 'true', remove file older than specified number of $days
	if ($expire == 'true')
	{
		$files = glob($basedir.'*');
		foreach ($files as $file)
		{
			if(is_file($file)
			&& time() - filemtime($file) >= $days*24*60*60) {
				unlink($file);
				unlink($basedir.'thumbs/'.basename($file));
			}
		}
	}

	echo "<title>$title</title>";
	echo "</head>";
	echo "<body>";

	echo "<div id='content'><h1>$title</h1>";
	echo "<div class='center'>$tagline</div>";

	$dir=$basedir."/";
	$files = glob($dir.'*.jpg', GLOB_BRACE);
	$thumbs = glob($dir.'thumbs/*.jpg', GLOB_BRACE);
	$fileCount = count(glob($dir.'*.jpg'));

	for ($i=($fileCount-1); $i>=0; $i--) {
		$exif = exif_read_data($files[$i], 0, true);
		$filepath = pathinfo($files[$i]);
		echo "<h2>".$filepath['filename']."</h2>";
		echo "<p>";
		include $dir.$filepath['filename'].'.php';
		echo $exif['COMPUTED']['UserComment'];
		echo "</p>";
		echo '<a href="'.$files[$i].'"><img class="dropshadow" src="'.$thumbs[$i].'" alt=""></a>';
		$Fnumber = explode("/", $exif['EXIF']['FNumber']);
		$Fnumber = $Fnumber[0] / $Fnumber[1];
		echo "<p class='box'>Aperture: <strong>f/".$Fnumber."</strong> Shutter speed: <strong>" .$exif['EXIF']['ExposureTime']. "</strong> ISO: <strong>".$exif['EXIF']['ISOSpeedRatings']. "</strong> Timestamp: <strong>".$exif['EXIF']['DateTimeOriginal']."</strong></p>";
	}
		echo "<div class='footer'>$footer</div>";

	?>
	</div>
	</body>
</html>
