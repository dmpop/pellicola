<?php
include('config.php');
// Include i18n class and initialize it
require_once 'i18n.class.php';
$i18n = new i18n();
$i18n->setCachePath('cache');
$i18n->setFilePath('lang/{LANGUAGE}.ini');
$i18n->setFallbackLang('en');
$i18n->init();
// Check whether the php-exif and php-gd libraries are installed
if (!extension_loaded('gd')) {
	exit("<center><code style='color: red;'>" . L::warning_php_gd . "</code></center>");
}
if (!extension_loaded('exif')) {
	exit("<center><code style='color: red;'>" . L::warning_php_exif . "</code></center>");
}
// Time allowed the script to run. Generating tims can take time,
// and increasing the time limit prevents the script from ending prematurely
set_time_limit(600);
// Start a session to keep track of albums
if (session_status() == PHP_SESSION_NONE) {
	session_start();
}
?>

<!DOCTYPE html>
<html lang="<?php echo $i18n->getAppliedLang(); ?>">

<!--
	 Author: Dmitri Popov
	 License: GPLv3 https://www.gnu.org/licenses/gpl-3.0.txt
	 Source code: https://github.com/dmpop/pellicola
	-->

<head>
	<meta charset="utf-8">
	<link rel="shortcut icon" href="favicon.png" />
	<meta name="viewport" content="width=device-width">
	<link rel="stylesheet" href="styles.css" />
	<link rel="alternate" type="application/rss+xml" href="rss.php" title="<?php echo $title; ?>">

	<title><?php echo $title; ?></title>
</head>

<body>
	<div id="content">
		<?php
		// Create $base_photo_dir if it doesn't exist
		if (!file_exists($base_photo_dir)) {
			mkdir($base_photo_dir, 0755, true);
		}
		/*  If $_GET["album"] is set, its value is saved in the $_SESSION["album"] session and assigned to the $album variable.
		If $_GET["album"] is not set, the value of $album is the current (unchanged) value of $_SESSION["album"].
		*/
		if (isset($_GET["album"])) {
			$_SESSION["album"] = htmlentities($_GET["album"]);
			$album = $_SESSION["album"];
		} elseif (isset($_GET["album"]) || !empty($_SESSION["album"])) {
			$album = $_SESSION["album"];
		} else {
			$album = NULL;
		}
		// Create $base_photo_dir if it doesn't exist
		if (!file_exists($base_photo_dir)) {
			mkdir($base_photo_dir, 0755, true);
		}

		// htmlentities() and str_replace() are used to sanitize the path and prevent the path traversal attacks. Not very elegant, but it should do the trick.
		$photo_dir = htmlentities(str_replace(DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $base_photo_dir . DIRECTORY_SEPARATOR . $album . DIRECTORY_SEPARATOR));

		/* ======= FUNCTIONS ======= */

		// Mask and unmask URL parameter using bin2hex() and hex2bin()
		function mask_param($param)
		{
			if (file_exists($param)) {
				return bin2hex($param);
			} else {
				exit("¯\_(ツ)_/¯");
			}
		}
		function unmask_param($param)
		{
			// Check if $param is a hex string
			if (ctype_xdigit($param)) {
				return hex2bin($param);
			} else {
				exit("¯\_(ツ)_/¯");
			}
		}

		/* EXTRACT LATITUDE AND LONGITUDE ---START---
	   * Returns an array of latitude and longitude from the image file.
	   * @param image $file
	   * @return multitype:number |boolean
	   * http://stackoverflow.com/questions/5449282/reading-geotag-data-from-image-in-php
	   */
		function read_gps_location($file)
		{
			if (is_file($file)) {
				$exif = @exif_read_data($file);
				if (
					isset($exif['GPSLatitude']) && isset($exif['GPSLongitude']) &&
					isset($exif['GPSLatitudeRef']) && isset($exif['GPSLongitudeRef']) &&
					in_array($exif['GPSLatitudeRef'], array('E', 'W', 'N', 'S')) && in_array($exif['GPSLongitudeRef'], array('E', 'W', 'N', 'S'))
				) {

					$GPSLatitudeRef	 = strtolower(trim($exif['GPSLatitudeRef']));
					$GPSLongitudeRef = strtolower(trim($exif['GPSLongitudeRef']));

					$lat_degrees_a = explode('/', $exif['GPSLatitude'][0]);
					$lat_minutes_a = explode('/', $exif['GPSLatitude'][1]);
					$lat_seconds_a = explode('/', $exif['GPSLatitude'][2]);
					$lon_degrees_a = explode('/', $exif['GPSLongitude'][0]);
					$lon_minutes_a = explode('/', $exif['GPSLongitude'][1]);
					$lon_seconds_a = explode('/', $exif['GPSLongitude'][2]);

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
						'lat' => htmlentities($lat),
						'lon' => htmlentities($lon)
					);
				}
			}
			return false;
		}
		/* EXTRACT LATITUDE AND LONGITUDE ---END--- */

		/* CREATE TIMS ---START--- */
		function createTim($original, $tim, $timWidth)
		{
			// Load image
			$img = @imagecreatefromjpeg($original);
			if (!$img) return false;

			// Get image size
			$width = imagesx($img);
			$height = imagesy($img);

			// Calculate tim size
			$new_width	= $timWidth;
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
		/* CREATE TIMS ---END--- */

		/* PAGINATION LINKS ---START --- */
		function show_pagination($current_page, $last_page)
		{
			echo '<div class="center">';
			if ($current_page != 1 && !isset($_GET["file"])) {
				echo '<a color: #e3e3e3;" href="?page=1"><img style="margin-right:1em;" src="svg/arrow-up.svg" alt="' . L::nav_first . '" title="' . L::nav_first . '"/></a> ';
			}
			if ($current_page > 1 && !isset($_GET["file"])) {
				echo '<a color: #e3e3e3;" href="?page=' . ($current_page - 1) . '"><img style="margin-right:1em;" src="svg/arrow-left.svg" alt="' . L::nav_prev . '" title="' . L::nav_prev . '"/></a> ';
			}
			if ($current_page < $last_page && !isset($_GET["file"])) {
				echo '<a color: #e3e3e3;" href="?page=' . ($current_page + 1) . '"><img style="margin-right:1em;" src="svg/arrow-right.svg" alt="' . L::nav_next . '" title="' . L::nav_next . '"/></a>';
			}
			if ($current_page != $last_page && !isset($_GET["file"])) {
				echo ' <a style="color: #e3e3e3;" href="?page=' . ($last_page) . '"><img src="svg/arrow-down.svg" alt="' . L::nav_last . '" title="' . L::nav_last . '"/></a>';
			}
			echo '</div>';
		}
		/* PAGINATION LINKS ---END --- */

		/* ======= FUNCTIONS ======= */

		// Create missing tims
		if (file_exists($photo_dir) && !file_exists($tims_dir)) {
			mkdir($tims_dir);
		}

		// Find all files or a specific file if $_GET["query"] is set
		if (isset($_GET["query"])) {
			$files = glob($photo_dir . "*" . $_GET["query"] . "*.{" . $img_formats . "}", GLOB_BRACE);
		} else {
			$files = glob($photo_dir . "*.{" . $img_formats . "}", GLOB_BRACE);
		}

		// Check whether the reversed order option is enabled and sort the array accordingly
		if ($r_sort) {
			rsort($files);
		}

		// Update count (we might have removed some files)
		$file_count = count($files);

		// Generate missing tims
		for ($i = 0; $i < $file_count; $i++) {
			$file  = $files[$i];
			$tim = $tims_dir . basename($file);

			if (!file_exists($tim)) {
				createTim($file, $tim, $tim_size);
			}
		}

		/* Prepare pagination. Calculate total items per page ---START--- */
		$total = count($files);
		$last_page = ceil($total / $per_page);

		if (!isset($_GET["file"])) {

			if (isset($_GET["page"]) && ($_GET["page"] <= $last_page) && ($_GET["page"] > 0) && (!isset($_GET["all"]))) {
				$page = $_GET["page"];
				$offset = ($per_page) * ($page - 1);
			} else {
				$page = 1;
				$offset = 0;
			}

			$max = $offset + $per_page;
		}
		if (!isset($max)) {
			$max = NULL;
		}
		if ($max > $total) {
			$max = $total;
		}
		/* Pagination. Calculate total items per page ---END --- */

		// The $grid parameter is used to show the main grid
		$grid = (isset($_GET["file"]) ? $_GET["file"] : NULL);
		if (!isset($grid)) {
			echo '<div style="text-align:center; margin-bottom: 1.5em; margin-top: 5em;">';
			echo '<a style="text-decoration:none;" href="index.php?album="><img style="display: inline; height: 3.5em; vertical-align: middle;" src="favicon.png" alt="' . $title . '" /></a>';
			echo '<a style="text-decoration:none;" href="index.php?album="><h1 style="display: inline; font-size: 2.3em; margin-left: 0.19em; vertical-align: middle;">' . $title . '</h1></a>';
			echo '</div>';
			echo "<div class ='center' style='color: gray; margin-bottom: 1em;'>" . $subtitle . "</div>";
			echo "<div class ='center' style='margin-bottom: 1em;'>";
			// Show stats icon
			echo '<a href="stats.php"><img src="svg/stats.svg" alt="' . L::stats . '" title="' . L::stats . '"/></a>';
			// Show the grid icon if there are several pages
			if (!isset($_GET["all"]) && $file_count > $per_page) {
				echo '<a href="?all=show"><img  style="margin-left: .5em;" src="svg/display-grid.svg" alt="' . L::img_show_all . '" title="' . L::img_show_all . '"/></a>';
			}
			echo '<hr style="margin-bottom: 1em;">';

			// Create an array with all subdirectories
			$all_sub_dirs = array_filter(glob($photo_dir . '*'), 'is_dir');
			$sub_dirs = array_diff($all_sub_dirs, array($tims_dir));
			$count = count(glob($photo_dir . "*.{" . $img_formats . "}", GLOB_BRACE));
			echo "<span style='color: gray'>" . L::album_items_count . ": </span>" . $count;
			echo "</div>";

			// Populate a drop-down list with subdirectories
			if ((count($sub_dirs)) > 0 or (!empty($album))) {
				echo "<noscript>";
				echo "<h3><img style='vertical-align: middle; margin-right: .5em;' src='svg/denied.svg'/> " . L::warning_enable_js . "</h3>";
				echo "</noscript>";
				echo '<div class="center" style="margin-bottom: 1em;">';
				echo "<a href='"  . basename($_SERVER['PHP_SELF']) . "?album='><img style='vertical-align: middle;' alt='" . L::img_root_album . "' title='" . L::img_root_album . "' src='svg/home.svg'/></a> &rarr;&nbsp;";
				$higher_dirs = explode("/", $album);
				$higher_dir_cascade = "";
				foreach ($higher_dirs as $higher_dir) {
					if (!empty($higher_dir)) {
						if (!empty($higher_dir_cascade)) {
							$higher_dir_cascade = $higher_dir_cascade . DIRECTORY_SEPARATOR;
						}
						$higher_dir_cascade = $higher_dir_cascade . $higher_dir;
						echo "<a href='"  . basename($_SERVER['PHP_SELF']) . "?album=" . $higher_dir_cascade . "'>" . $higher_dir . "</a> &rarr;&nbsp;";
					}
				}

				echo '<select class="select" name="" onchange="javascript:location.href = this.value;">';
				echo '<option value="Default">' . L::album . '</option>';
				foreach ($sub_dirs as $dir) {
					$dir_name = basename($dir);
					$dir_option = str_replace('\'', '&apos;', $album . DIRECTORY_SEPARATOR . $dir_name);
					echo "<option value='?album=" . ltrim($dir_option, '/') . "'>" . $dir_name . "</option>";
				}
				echo "</select></div>";
			}
		?>

			<!-- Search filed in the upper-right corner -->
			<div class="topcorner">
				<form autocomplete="off" style="margin-top: 0.5em; margin-right: 1em;" method="GET" action=" ">
					<label for='weight'><?php echo L::find_by_name; ?>:</label>
					<input style="vertical-align: middle;" type='text' name='query'>
					<!-- The hidden input field is used to pass the $album value (album) to the search -->
					<input type='hidden' name='album' value='<?php echo $album; ?>'>
					<input style="vertical-align: middle;" type="image" src="svg/search.svg" alt="<?php echo L::search_btn; ?>" title="<?php echo L::search_btn; ?>">
				</form>
			</div>

		<?php
			// Check whether $photo_dir directory exists
			if (!file_exists($photo_dir)) {
				echo ("<h3 style='margin-top: 2em;'><img style='vertical-align: middle; margin-right: .5em;' src='svg/denied.svg'/>" . L::warning_no_album . "</h3>");
				exit;
			}
			if ($file_count < 1) {
				echo ("<h3 style='margin-top: 2em;'><img style='vertical-align: middle; margin-right: .5em;' src='svg/denied.svg'/> " . L::warning_empty . "</h3>");
				exit;
			}
			// Show the content of the preamble.html file if it exists in the album
			if (file_exists($photo_dir . "preamble.html")) {
				echo '<div style="margin: auto; margin-top: 1.5em; margin-bottom: 1em; width: 50%;">';
				echo file_get_contents($photo_dir . "preamble.html");
				echo "</div>";
			}
			echo "</div>";
			echo '<div class="gallery-grid">';
			if (isset($_GET["all"])) {
				for ($i = 0; $i < $file_count; $i++) {
					$file = $files[$i];
					$tim = $tims_dir . basename($file);
					$file_path = pathinfo($file);
					echo '<figure class="gallery-frame">';
					echo '<a href="index.php?file=' . mask_param($file)  . '"><img class="gallery-img" src="' . $tim . '" alt="' . $file_path['filename'] . '" title="' . $file_path['filename'] . '"></a>';
					echo '<figcaption>' . $file_path['filename'] . '</figcaption></figure>';
				}
			} else {
				for ($i = $offset; $i < $max; $i++) {
					$file = $files[$i];
					$tim = $tims_dir . basename($file);
					$file_path = pathinfo($file);
					echo '<figure class="gallery-frame">';
					echo '<a href="index.php?file=' . mask_param($file) . '"><img class="gallery-img" src="' . $tim . '" alt="' . $file_path['filename'] . '" title="' . $file_path['filename'] . '"></a>';
					echo '<figcaption>' . $file_path['filename'] . '</figcaption></figure>';
				}
			}
			echo "</div>";
		}
		if (!isset($_GET["all"])) {
			// Set $page to NULL if $file is set to avoid undefined variable warning
			(isset($_GET["file"])) ? $page = NULL : NULL;
			show_pagination($page, $last_page, $album); // Pagination. Show navigation on bottom of page
		}

		// The $file parameter is used to show an individual photo
		$file = isset($_GET["file"]) ? unmask_param($_GET["file"]) : NULL;
		if (isset($file)) {
			$key = array_search($file, $files); // Determine the array key of the current item (we need this for generating the Next and Previous links)
			$tim = $tims_dir . basename($file);
			$exif = @exif_read_data($file, 0, true);
			$file_path = pathinfo($file);

			// Obtain URL of the current page for use with the Back button
			$url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
			$_SESSION["page"] = $url;

			echo "<h1 style='margin-top: 1em;'>" . $file_path['filename'] . "</h1><hr>";

			/* NAVIGATION LINKS ---START--- */
			// Set first and last photo navigation links according to specified	 sort order
			$last_photo = $files[count($files) - 1];
			$first_photo = $files[0];

			// If there is only one photo in the album, show the home navigation link
			if ($file_count == 1) {
				echo "<div class='center'><a href='index.php?album=" . $album . "' accesskey='g'><img src='svg/home.svg' alt='" . L::nav_home . "' title='" . L::nav_home . "'/></a></div>";
			}
			// Disable the Previous link if this is the FIRST photo
			elseif (empty($files[$key - 1])) {
				echo "<div class='center' style='margin-bottom: 1em;'><a href='index.php?album=" . $album . "' accesskey='g'><img style='margin-right:1em;' src='svg/home.svg' alt='" . L::nav_home . "' title='" . L::nav_home . "'/></a><a href='index.php?file=" . mask_param($files[$key + 1]) . "' accesskey='n'><img style='margin-right:1em;' src='svg/arrow-right.svg'  alt='" . L::nav_next . "' title='" . L::nav_next . "'/></a><a href='index.php?file=" . mask_param($last_photo) . "' accesskey='l'><img src='svg/arrow-down.svg' alt='" . L::nav_last . "' title='" . L::nav_last . "'/></a></div>";
			}
			// Disable the Next link if this is the LAST photo
			elseif (empty($files[$key + 1])) {
				echo "<div class='center' style='margin-bottom: 1em;'><a href='index.php?album=" . $album . "' accesskey='g'><img style='margin-right:1em;' src='svg/home.svg' alt='" . L::nav_home . "' title='" . L::nav_home . "'/></a><a href='index.php?file=" . mask_param($first_photo) . "' accesskey='f'><img style='margin-right:1em;' src='svg/arrow-up.svg' alt='" . L::nav_first . "' title='" . L::nav_first . "'/></a><a href='index.php?file=" . mask_param($files[$key - 1]) . "' accesskey='p'><img style='margin-right:1em;' src='svg/arrow-left.svg' alt='" . L::nav_prev . "' title='" . L::nav_prev . "'/></a></div>";
			}
			// Show all navigation links
			else {

				echo "<div class='center' style='margin-bottom: 1em;'><a href='index.php?album=" . $album . "' accesskey='g'><img style='margin-right:1em;' src='svg/home.svg' alt='" . L::nav_home . "' title='" . L::nav_home . "'/></a><a href='index.php?file=" . mask_param($first_photo) . "' accesskey='f'><img style='margin-right:1em;' src='svg/arrow-up.svg' alt='" . L::nav_first . "' title='" . L::nav_first . "'/></a><a href='index.php?file=" . mask_param($files[$key - 1]) . "' accesskey='p'><img style='margin-right:1em;' src='svg/arrow-left.svg' alt='" . L::nav_prev . "' title='" . L::nav_prev . "'/></a><a href='index.php?file=" . mask_param($files[$key + 1]) . "' accesskey='n'><img style='margin-right:1em;' src='svg/arrow-right.svg' alt='" . L::nav_next . "' title='" . L::nav_next . "'/></a><a href='index.php?file=" . mask_param($last_photo) . "' accesskey='l'><img src='svg/arrow-down.svg' alt='" . L::nav_last . "' title='" . L::nav_last . "'/></a></div>";
			}
			/* NAVIGATION LINKS ---END--- */

			// Check whether the localized description file matching the browser language exists
			if (file_exists($photo_dir . $i18n->getAppliedLang() . '-' . $file_path['filename'] . '.txt')) {
				$description = @file_get_contents($photo_dir . $i18n->getAppliedLang() . '-' . $file_path['filename'] . '.txt');
				// If the localized description file doesn't exist, use the default one
			} else {
				$description = @file_get_contents($photo_dir . $file_path['filename'] . '.txt');
			}
			$gps = read_gps_location($file);

			// Get aperture, exposure, iso, and datetime from EXIF
			$aperture = htmlentities((is_null($exif['COMPUTED']['ApertureFNumber']) ? NULL : $exif['COMPUTED']['ApertureFNumber']));
			$exposure = htmlentities((is_null($exif['EXIF']['ExposureTime']) ? NULL : $exif['EXIF']['ExposureTime']));
			// Normalize exposure
			// https://stackoverflow.com/questions/3049998/parsing-exifs-exposuretime-using-php
			if (!is_null($exposure)) {
				$parts = explode("/", $exposure);
				if (($parts[1] % $parts[0]) == 0 || $parts[1] == 1000000) {
					$exposure = htmlentities(' • 1/' . round($parts[1] / $parts[0], 0));
				} else {
					if ($parts[1] == 1) {
						$exposure = htmlentities(' • ' . $parts[0]);
					} else {
						$exposure = htmlentities(' • ' . $parts[0] . '/' . $parts[1]);
					}
				}
			}
			$iso = htmlentities((is_null($exif['EXIF']['ISOSpeedRatings']) ? NULL : " • " . $exif['EXIF']['ISOSpeedRatings']));
			$datetime = htmlentities($exif['EXIF']['DateTimeOriginal']) ?? NULL;
			$comment = htmlentities($exif['COMMENT']['0']) ?? NULL;

			// Concatenate $exif_info
			if (!is_null($aperture) || !is_null($exposure) || !is_null($iso) || !is_null($datetime)) {
				$exif_info = '<img style="margin-right: .5rem;" src="svg/camera.svg" alt="' . L::img_exif . '" title="' . L::img_exif . '"/>' . $aperture . $exposure . $iso . '<img style="margin-left: .5rem; margin-right: .5rem;" src="svg/calendar.svg" alt="' . L::img_date . '" title="' . L::img_date . '"/>' .  $datetime;
			}

			// Add the pin icon if the photo contains geographical coordinates
			if (!empty($gps['lat']) && !empty($gps['lon'])) {
				//Generate Geo URI
				if ($openstreetmap) {
					$map_url = "<a href='http://www.openstreetmap.org/index.html?mlat=" . $gps['lat'] . "&mlon=" . $gps['lon'] . "&zoom=18' target='_blank'><img style='margin-left: .5rem;' src='svg/pin.svg' alt='" . L::img_map . "' title='" . L::img_map . "'/></a>";
				} else {
					$map_url = "<a href='geo:" . $gps['lat'] . "," . $gps['lon'] . "'><img style='margin-left: .5rem;' src='svg/pin.svg' alt='" . L::img_map . "' title='" . L::img_map . "'/></a>";
				}
				$exif_info = $exif_info . $map_url;
			}
			// Find all RAW files
			$raw_file = glob($photo_dir . $file_path['filename'] . "*.{" . $raw_formats . "}", GLOB_BRACE);
			$raw = (!empty($raw_file[0]) ? mask_param(htmlentities($raw_file[0])) : $raw = NULL);
			$image_download = '<a href="download.php?file=' . mask_param(htmlentities($file)) . '"><img style="margin-right: 1em;" src="svg/download.svg" alt="' . L::img_download . '" title="' . L::img_download . '" /></a>';
			$image_delete = '<a href="delete.php?file=' . mask_param(htmlentities($file)) . "&raw=" . $raw . '"><img src="svg/remove-image.svg" alt="' . L::img_delete . '" title="' . L::img_delete . '" /></a>';
			//Check if the related RAW file exists and link to it
			if (!empty($raw_file)) {
				$raw_download = "<a href='download.php?file=" . $raw . "'><img style='margin-right: 1em;' alt='" . L::raw_download . "' title='" . L::raw_download . "' src='svg/raw.svg'/></a>";
			}
			if ($download) {
				echo '<div class="center"><img style="max-width: 100%; border-radius: 7px;" src="' . htmlentities($tim) . '" alt="' . $file_path['filename'] . '" title="' . $file_path['filename'] . '"><div class="caption">' . $comment . ' ' . $description . '</div>';
				echo '<div class="caption">' . $exif_info . '</div>';
				echo '<div class="caption">' . $image_download . $raw_download . $image_delete . '</div></div>';
			} else {
				echo '<div class="center"><img style="max-width: 100%; border-radius: 7px;" src="' . htmlentities($tim) . '" alt="' . $file_path['filename'] . '" title="' . $file_path['filename'] . '"><div class="caption">' . $comment . ' ' . $description . '</div>';
				echo '<div class="caption">' . $exif_info . "<span style='margin-left: 1em;'>" . $image_delete . '</span></div></div>';
			}
		}

		// Show links
		if ($links) {
			$array_length = count($urls);
			echo '<div class="footer">';
			for ($i = 0; $i < $array_length; $i++) {
				echo '<span style="word-spacing:0.1em;"><a href="' . $urls[$i][0] . '">' . $urls[$i][1] . '</a> • </span>';
			}
			echo  $footer . '</div>';
		} else {
			echo '<div class="footer">' . $footer . '</div>';
		}
		?>
	</div>
</body>

</html>