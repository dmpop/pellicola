<?php
include('config.php');
include('parsedown.php');
if ($protect && !in_array($_GET['d'], $public_albums)) {
	include('protect.php');
}
?>

<html lang="en">

<!--
	 Author: Dmitri Popov
	 License: GPLv3 https://www.gnu.org/licenses/gpl-3.0.txt
	 Source code: https://github.com/dmpop/mejiro
	-->

<head>
	<meta charset="utf-8">
	<link rel="shortcut icon" href="favicon.png" />
	<meta name="viewport" content="width=device-width">
	<link rel="stylesheet" href="styles.css" />

	<title><?php echo $title; ?></title>
</head>

<body>
	<div id="content">

		<?php

		// Time allowed the script to run. Generating tims can take time,
		// and increasing the time limit prevents the script from ending prematurely
		set_time_limit(600);

		// Detect browser language
		$language = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);

		// basename and str_replace are used to prevent the path traversal attacks. Not very elegant, but it should do the trick.
		//  The $d parameter is used to detect a subdirectory
		if (isset($_GET['d'])) {
			$sub_photo_dir = $_GET['d'];
		} else {
			$sub_photo_dir = null;
		}
		$photo_dir = str_replace(DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $base_photo_dir . DIRECTORY_SEPARATOR . $sub_photo_dir . DIRECTORY_SEPARATOR);

    /*
	 * Returns an array of latitude and longitude from the image file.
	 * @param image $file
	 * @return multitype:number |boolean
	 * http://stackoverflow.com/questions/5449282/reading-geotag-data-from-image-in-php
	 */
		function read_gps_location($file)
		{
			if (is_file($file)) {
				$info = exif_read_data($file);
				if (
					isset($info['GPSLatitude']) && isset($info['GPSLongitude']) &&
					isset($info['GPSLatitudeRef']) && isset($info['GPSLongitudeRef']) &&
					in_array($info['GPSLatitudeRef'], array('E', 'W', 'N', 'S')) && in_array($info['GPSLongitudeRef'], array('E', 'W', 'N', 'S'))
				) {

					$GPSLatitudeRef	 = strtolower(trim($info['GPSLatitudeRef']));
					$GPSLongitudeRef = strtolower(trim($info['GPSLongitudeRef']));

					$lat_degrees_a = explode('/', $info['GPSLatitude'][0]);
					$lat_minutes_a = explode('/', $info['GPSLatitude'][1]);
					$lat_seconds_a = explode('/', $info['GPSLatitude'][2]);
					$lon_degrees_a = explode('/', $info['GPSLongitude'][0]);
					$lon_minutes_a = explode('/', $info['GPSLongitude'][1]);
					$lon_seconds_a = explode('/', $info['GPSLongitude'][2]);

					$lat_degrees = $lat_degrees_a[0] / $lat_degrees_a[1];
					$lat_minutes = $lat_minutes_a[0] / $lat_minutes_a[1];
					$lat_seconds = $lat_seconds_a[0] / $lat_seconds_a[1];
					$lon_degrees = $lon_degrees_a[0] / $lon_degrees_a[1];
					$lon_minutes = $lon_minutes_a[0] / $lon_minutes_a[1];
					$lon_seconds = $lon_seconds_a[0] / $lon_seconds_a[1];

					$lat = (float) $lat_degrees + ((($lat_minutes * 60) + ($lat_seconds)) / 3600);
					$lon = (float) $lon_degrees + ((($lon_minutes * 60) + ($lon_seconds)) / 3600);

					// If the latitude is South, make it negative
					// If the longitude is west, make it negative
					$GPSLatitudeRef	 == 's' ? $lat *= -1 : '';
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
		if (!file_exists($photo_dir) || !file_exists($photo_dir . 'tims')) {
            mkdir($photo_dir, 0777, true);
			mkdir($photo_dir . 'tims', 0777, true);
			echo ('<h3>Add photos to the <strong>photos</strong> directory, then refresh this page.</h3>');
		}

		// Get file info
		$files = glob($photo_dir . '*.{jpg,jpeg,JPG,JPEG}', GLOB_BRACE);

		// Check whether the reversed order option is enabled and sort the array accordingly
		if ($r_sort) {
			rsort($files);
		}

        // Update count (we might have removed some files)
        $file_count = count($files);

        // Generate missing tims
		for ($i = 0; $i < $file_count; $i++) {
			$file  = $files[$i];
			$tim = $photo_dir . 'tims/' . basename($file);

			if (!file_exists($tim)) {
				// Generate tims using php-imagick
				$t = new Imagick($file);
				$t->resizeImage(800, 0, Imagick::FILTER_LANCZOS, 1);
				$t->writeImage($tim);
				$t->destroy();
			}
		}

		// Prepare pagination. Calculate total items per page * START
		$total = count($files);
		$last_page = ceil($total / $per_page);

		if (isset($_GET["photo"]) == '') {

			if (isset($_GET["page"]) && ($_GET["page"] <= $last_page) && ($_GET["page"] > 0) && ($_GET["all"] != 1)) {
				$page = $_GET["page"];
				$offset = ($per_page) * ($page - 1);
			} else {
				$page = 1;
				$offset = 0;
			}

			if (isset($_GET['all']) == 1) {
				$all = 1;
			}
			$max = $offset + $per_page;
		}
		if (!isset($max)) {
			$max = null;
		}
		if ($max > $total) {
			$max = $total;
		}

		// Pagination. Calculate total items per page * END

		// The $grid parameter is used to show the main grid
		$grid = (isset($_GET['photo']) ? $_GET['photo'] : null);

		if (!isset($grid)) {
			echo '<div style="text-align:center; margin-bottom: 1.5em; margin-top: 1.5em;">';
			echo '<img style="display: inline; height: 3em; vertical-align: middle;" src="favicon.svg" alt="logo" />';
			echo '<a style="text-decoration:none;" href="' . basename($_SERVER['PHP_SELF']) . '"><h1 style="display: inline; font-size: 3em; margin-left: 0.19em; vertical-align: middle; letter-spacing: 3px; color: #ffc03fff;">' . $title . '</h1></a>';
			echo '</div>';
			echo "<div class ='center'>" . $tagline . "</div>";
			echo '<hr style="margin-left:15%; margin-right:15%; margin-bottom: 2em;">';
			// Create an array with all subdirectories
			$all_sub_dirs = array_filter(glob($photo_dir . '*'), 'is_dir');
			$sub_dirs = array_diff($all_sub_dirs, array($photo_dir . "tims"));
			// Populate a drop-down list with subdirectories
			if ((count($sub_dirs)) > 0 or (!empty($sub_photo_dir))) {

				echo "<noscript>";
				echo "<h3>Make sure that JavaScript is enabled.</h3>";
				echo "</noscript>";
				echo '<div class="center">';

				echo "<a href='"  . basename($_SERVER['PHP_SELF']) . "'>Root</a> /&nbsp;";

				$higher_dirs = explode("/", $sub_photo_dir);
				$higher_dir_cascade = "";
				foreach ($higher_dirs as $higher_dir) {
                    if (!empty($higher_dir)) {
                        if (!empty($higher_dir_cascade)) {
                            $higher_dir_cascade = $higher_dir_cascade . DIRECTORY_SEPARATOR;
                        }
                        $higher_dir_cascade = $higher_dir_cascade . $higher_dir;
                        echo "<a href='"  . basename($_SERVER['PHP_SELF']) . "?d=" . $higher_dir_cascade . "'>" . $higher_dir . "</a> /&nbsp;";
                    }
				}

				echo '<select style="width: 15em;" name="" onchange="javascript:location.href = this.value;">';
				echo '<option value="Default">Choose album</option>';
				foreach ($sub_dirs as $dir) {
					$dir_name = basename($dir);
					$dir_option=str_replace('\'', '&apos;', $sub_photo_dir . DIRECTORY_SEPARATOR . $dir_name);
					echo "<option value='?d=" . $dir_option . "'>" . $dir_name . "</option>";
				}
				echo "</select>";
				if ($protect && isset($_COOKIE['password'])) {
					echo '<a style="margin-left: 1em;" href="logout.php">Log out</a>';
				}
				echo "</div>";
			}

            if ($file_count < 1) {
                echo ('<h3>Add photos to the <strong>photos</strong> directory, then refresh this page.</h3>');
            }

			if (!isset($_GET["all"])) {
				$all = null;
			}
			if (isset($_GET["all"]) != 1 && $file_count > $per_page) {
				echo '<div class="center"><a href=?all=1' . '&d=' . urlencode($sub_photo_dir) . '><img src="svg/display-grid.svg"/></a></div>';
			}
			echo "<ul class='rig columns-" . $columns . "'>";

			if ($all == 1) {
				for ($i = 0; $i < $file_count; $i++) {
					$file = $files[$i];
					$tim = $photo_dir . 'tims/' . basename($file);
					$file_path = pathinfo($file);
					echo '<li><a href="index.php?all=1&photo=' . $file . '&d=' . $sub_photo_dir . '"><img src="' . $tim . '" alt="' . $file_path['filename'] . '" title="' . $file_path['filename'] . '"></a></li>';
				}
			} else {
				for ($i = $offset; $i < $max; $i++) {
					$file = $files[$i];
					$tim = $photo_dir . 'tims/' . basename($file);
					$file_path = pathinfo($file);
					echo '<li><a href="index.php?all=1&photo=' . $file . '&d=' . $sub_photo_dir . '"><img src="' . $tim . '" alt="' . $file_path['filename'] . '" title="' . $file_path['filename'] . '"></a></li>';
				}
			}

			echo "</ul>";
		}

		if (isset($_GET["all"]) != 1) {
			show_pagination($page, $last_page, $sub_photo_dir); // Pagination. Show navigation on bottom of page
		}

		//Pagination. Create the navigation links * START
		function show_pagination($current_page, $last_page, $sub_photo_dir)
		{
			echo '<div class="center">';
			if ($current_page != 1 && isset($_GET["photo"]) == '') {
				echo '<a color: #e3e3e3;" href="?page=' . "1" . '&d=' . $sub_photo_dir . '"><img style="margin-right:1em;" src="svg/arrow-top-left-o.svg"/></a> ';
			}
			if ($current_page > 1 && isset($_GET["photo"]) == '') {
				echo '<a color: #e3e3e3;" href="?page=' . ($current_page - 1) . '&d=' . $sub_photo_dir . '"><img style="margin-right:1em;" src="svg/arrow-left-o.svg"/></a> ';
			}
			if ($current_page < $last_page && isset($_GET["photo"]) == '') {
				echo '<a color: #e3e3e3;" href="?page=' . ($current_page + 1) . '&d=' . $sub_photo_dir . '"><img style="margin-right:1em;" src="svg/arrow-right-o.svg"/></a>';
			}
			if ($current_page != $last_page && isset($_GET["photo"]) == '') {
				echo ' <a style="color: #e3e3e3;" href="?page=' . ($last_page) . '&d=' . $sub_photo_dir . '"><img src="svg/arrow-top-right-o.svg"/></a>';
			}
			echo '</div>';
		}
		//Pagination. Create the navigation links * END

		// The $photo parameter is used to show an individual photo
		$file = (isset($_GET['photo']) ? $_GET['photo'] : null);
		if (isset($file)) {
			$key = array_search($file, $files); // Determine the array key of the current item (we need this for generating the Next and Previous links)
			$tim = $photo_dir . 'tims/' . basename($file);
			$exif = exif_read_data($file, 0, true);
			$file_path = pathinfo($file);

			//Check if the related RAW file exists and link to it
			if ($show_raw) {
				$raw_file = glob($photo_dir . $file_path['filename'] . $raw_formats, GLOB_BRACE);
				if (!empty($raw_file)) {
					echo "<h1>" . $file_path['filename'] . " <a class='superscript' href=" . $raw_file[0] . ">RAW</a></h1>";
				}
			} else {
				echo "<h1>" . $file_path['filename'] . "</h1>";
			}

			// NAVIGATION LINKS
			// Set first and last photo navigation links according to specified	 sort order
			$last_photo = $files[count($files) - 1];
			$first_photo = $files[0];

			// If there is only one photo in the album, show the home navigation link
			if ($file_count == 1) {
				echo "<div class='center'><a href='" . basename($_SERVER['PHP_SELF']) . '?d=' . $sub_photo_dir . "' accesskey='g'><img src='svg/home.svg'/></a></div>";
			}
			// Disable the Previous link if this is the FIRST photo
			elseif (empty($files[$key - 1])) {
				echo "<div class='center'><a href='" . basename($_SERVER['PHP_SELF']) . '?d=' . $sub_photo_dir .  "' accesskey='g'><img style='margin-right:1em;' src='svg/home.svg'/></a><a href='" . basename($_SERVER['PHP_SELF']) . "?photo=" . $files[$key + 1] . '&d=' . $sub_photo_dir . "' accesskey='n'><img style='margin-right:1em;' src='svg/arrow-right-o.svg'/></a><a href='" . basename($_SERVER['PHP_SELF']) . "?photo=" . $last_photo . '&d=' . $sub_photo_dir .  "' accesskey='l'><img src='svg/arrow-top-right-o.svg'/></a></div>";
			}
			// Disable the Next link if this is the LAST photo
			elseif (empty($files[$key + 1])) {
				echo "<div class='center'><a href='" . basename($_SERVER['PHP_SELF']) . '?d=' . $sub_photo_dir . "' accesskey='g'><img style='margin-right:1em;' src='svg/home.svg'/></a><a href='" . basename($_SERVER['PHP_SELF']) . "?photo=" . $first_photo . '&d=' . $sub_photo_dir . "' accesskey='f'><img style='margin-right:1em;' src='svg/arrow-top-left-o.svg'/></a><a href='" . basename($_SERVER['PHP_SELF']) . '?d=' . $sub_photo_dir .  "&photo=" . $files[$key - 1] . "' accesskey='p'><img style='margin-right:1em;' src='svg/arrow-left-o.svg'/></a></div>";
			}
			// Show all navigation links
			else {

				echo "<div class='center'>
			<a href='" . basename($_SERVER['PHP_SELF']) . '?d=' . $sub_photo_dir . "' accesskey='g'><img style='margin-right:1em;' src='svg/home.svg'/></a><a href='" . basename($_SERVER['PHP_SELF']) . "?photo=" . $first_photo . '&d=' . $sub_photo_dir . "' accesskey='f'><img style='margin-right:1em;' src='svg/arrow-top-left-o.svg'/></a><a href='" . basename($_SERVER['PHP_SELF']) . "?photo=" . $files[$key - 1] . '&d=' . $sub_photo_dir . "' accesskey='p'><img style='margin-right:1em;' src='svg/arrow-left-o.svg'/></a><a href='" . basename($_SERVER['PHP_SELF']) . "?photo=" . $files[$key + 1] . '&d=' . $sub_photo_dir . "' accesskey='n'><img style='margin-right:1em;' src='svg/arrow-right-o.svg'/></a><a href='" . basename($_SERVER['PHP_SELF']) . "?photo=" . $last_photo . '&d=' . $sub_photo_dir . "' accesskey='l'><img src='svg/arrow-top-right-o.svg'/></a></div>";
			}

			// Check whether the localized description file matching the browser language exists
			if (file_exists($photo_dir . $language . '-' . $file_path['filename'] . '.md')) {
				$description = @file_get_contents($photo_dir . $language . '-' . $file_path['filename'] . '.md');
				// If the localized description file doesn't exist, use the default one
			} else {
				$description = @file_get_contents($photo_dir . $file_path['filename'] . '.md');
			}
			$gps = read_gps_location($file);

			$aperture = $exif['COMPUTED']['ApertureFNumber'];
			if (empty($aperture)) {
				$aperture = "";
			} else {
				$aperture = $aperture . " // ";
			}
			$exposure = $exif['EXIF']['ExposureTime'];
			if (empty($exposure)) {
				$exposure = "";
			} else {
				$exposure = $exposure . " // ";
			}
			$iso = $exif['EXIF']['ISOSpeedRatings'];
			if (empty($iso)) {
				$iso = "";
			} else {
				$iso = $iso . " // ";
			}
			$datetime = $exif['EXIF']['DateTimeOriginal'];
			if (empty($datetime)) {
				$datetime = "";
			}
			if (!isset($exif['COMMENT']['0'])) {
				$comment = "";
			} else {
				$comment = $exif['COMMENT']['0'];
			}

			//Generate map URL. Choose between Google Maps and OpenStreetmap
			if ($google_maps) {
				$map_url = " <a href='http://maps.google.com/maps?q=" . $gps['lat'] . "," . $gps['lon'] . "' target='_blank'><img style='vertical-align: middle;' src='svg/track.svg'/></a>";
			} else {
				$map_url = " <a href='http://www.openstreetmap.org/index.html?mlat=" . $gps['lat'] . "&mlon=" . $gps['lon'] . "&zoom=18' target='_blank'><img style='vertical-align: text-bottom;' src='svg/track.svg'/></a>";
			}

			$photo_info = $aperture . $exposure . $iso . $datetime;
			// Enable the Map anchor if the photo contains geographical coordinate
			if (!empty($gps['lat'])) {
				$photo_info = $photo_info . $map_url;
			}

			$info = "<span style='word-spacing:1em'>" . $photo_info . "</span>";
			$Parsedown = new Parsedown();
			// Show photo, EXIF data, description, and info
			echo '<div class="center"><ul class="rig column-1"><li><a href="' . $file . '" download><img src="' . $tim . '" alt=""></a><p class="caption">' . $comment . ' ' . $Parsedown->text($description) . '</p><p class="box">' . $info . '</p></li></ul></div>';
		}

		// Show links
		if ($links) {
			$array_length = count($urls);
			echo '<div class="footer">';
			for ($i = 0; $i < $array_length; $i++) {
				echo '<span style="word-spacing:0.5em;"><a style="color: white" href="' . $urls[$i][0] . '">' . $urls[$i][1] . '</a> // </span>';
			}
			echo  $footer . '</div>';
		} else {
			echo '<div class="footer">' . $footer . '</div>';
		}
		?>
	</div>
</body>

</html>
