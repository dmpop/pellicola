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
	$title = 'PHOTOCRUMBS';
	$tagline=" -- Uncomplicated photo publishing --";
	$basedir='photos/';
	$footer='Powered by <a href="https://github.com/dmpop/photocrumbs">Photocrumbs</a>';
	$expire = false; //set to true to enable the expiration feature
	$log = false; //set to true to enable ip logging
	$days = 15; // expiration period
	// ----------------------------

	// Create the required directories if they don't exist
		if (!file_exists($basedir)) {
		mkdir($basedir, 0777, true);
	}
	if (!file_exists($basedir.'thumbs')) {
		mkdir($basedir.'thumbs', 0777, true);
	}

    // get file info
    $files = glob($basedir."*.{jpg,jeg,JPG,JPEG}", GLOB_BRACE);
    $fileCount = count($files);

    /**
     * Creates a thumbnail for the given file
     *
     * @param string $original path to the original image
     * @param string $thumb
     * @param int $thumbWidth
     * @return bool true if thumbnail creation worked
     */
    function createThumb($original, $thumb, $thumbWidth)
	{
        // load image
        $img = @imagecreatefromjpeg($original);
        if($img) return false; // we couldn't read the image, abort

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

        return $ok;
	}

    // Generate any missing thumbnails and check expiration
    for($i = 0; $i < $fileCount; $i++) {
        $file  = $files[$i];
        $thumb = $basedir."thumbs/".basename($file);

        if(!file_exists($thumb)) {
            if(createThumb($file, $thumb, 600)) {
                // this is a new file, update last mod for expiry feature
                touch($file);
            } else {
                // we couldn't create a thumbnail remove the image from our list
                unset($files[$i]);
            }
        }

        if($expire && (time() - filemtime($file) >= $days * 24 * 60 * 60) ) {
            unlink($file);
            unlink($tumb);
        }
    }

    // update count - we might have removed some files
    $fileCount = count($files);


    echo "<title>$title</title>";
	echo "</head>";
	echo "<body>";

	echo "<div id='content'><h1>$title</h1>";
	echo "<div class='center'>$tagline</div>";

	for ($i=($fileCount-1); $i>=0; $i--) {
        $file  = $files[$i];
        $thumb = $basedir."thumbs/".basename($file);

        $exif = exif_read_data($file, 0, true);
		$filepath = pathinfo($file);
		echo "<h2>".$filepath['filename']."</h2>";
		echo "<p>";
		@include $basedir.$filepath['filename'].'.php';
		echo $exif['COMPUTED']['UserComment'];
		echo "</p>";
		echo '<a href="'.$file.'"><img class="dropshadow" src="'.$thumb.'" alt=""></a>';
		$fstop = explode("/", $exif['EXIF']['FNumber']);
		$fstop = $fstop[0] / $fstop[1];
		echo "<p class='box'>Aperture: <strong>f/".$fstop."</strong> Shutter speed: <strong>" .$exif['EXIF']['ExposureTime']. "</strong> ISO: <strong>".$exif['EXIF']['ISOSpeedRatings']. "</strong> Timestamp: <strong>".$exif['EXIF']['DateTimeOriginal']."</strong></p>";
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
			border-style: dashed;
			border-top: none;
			border-left: none;
			border-right: none;
			border-bottom: dotted;
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

	</body>
</html>
