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
	exit('<center><code style="color: #f7768e;">' . L::warning_php_gd . '</code></center>');
}
if (!extension_loaded('exif')) {
	exit('<center><code style="color: #f7768e;">' . L::warning_php_exif . '</code></center>');
}
// Time allowed the script to run. Generating tims can take time,
// and increasing the time limit prevents the script from ending prematurely
set_time_limit(600);

// Start a session to keep track of albums
if (session_status() == PHP_SESSION_NONE) {
	session_start();
}
// If $_GET['nocount'] is set, set a cookie to disable counting views and downloads
if (isset($_GET['nocount'])) {
	setcookie("nocount", "0", 2147483647);
}

$protect = false;
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
	<link rel="alternate" type="application/rss+xml" href="rss.php" title="<?php echo $TITLE; ?>">
	<link rel="stylesheet" href="leaflet/leaflet.css" />
	<script src="leaflet/leaflet.js"></script>
	<title><?php echo $TITLE; ?></title>
	<script type="text/javascript">
		if (window.history.replaceState) {
			window.history.replaceState(null, null, window.location.href);
		}
	</script>
</head>

<body>
	<?php
	// Create $ROOT_PHOTO_DIR if it doesn't exist
	if (!file_exists($ROOT_PHOTO_DIR)) {
		mkdir($ROOT_PHOTO_DIR, 0755, true);
	}
	/*  If $_GET['album'] is set, its value is saved in the $_SESSION['album'] session and assigned to the $album variable.
		If $_GET['album'] is not set, the value of $album is the current (unchanged) value of $_SESSION['album'].
		*/
	if (isset($_GET['album'])) {
		$_SESSION['album'] = htmlentities($_GET['album']);
		$album = $_SESSION['album'];
	} elseif (!empty($_SESSION['album'])) {
		$album = $_SESSION['album'];
	} else {
		$album = NULL;
	}
	?>
	<?php if ($protect && !isset($_SESSION['protect'])) : ?>
		<div class="c">
			<div class="card" style="text-align: center;">
				<form style="margin-top: .7em; display: inline;" action=" " method="POST">
					<label for="password"><?php echo L::password; ?></label>
					<input style="vertical-align: middle;" class="card" type="password" name="key" value="">
					<button style="display: inline; vertical-align: middle; margin-left: 0.2em;" class="btn green" type="submit" name="submit"><?php echo L::btn_confirm; ?></button>
				</form>
				<a class="btn primary" style="text-decoration: none; vertical-align: middle; margin-left: 0.2em;" href="index.php"><?php echo L::btn_back; ?></a>
			</div>
		</div>
	<?php endif; ?>
	<?php
	// Show the grid only if 1) album is not protected or 2) album is protected and password check was successful
	if (!$protect || ($protect && isset($_SESSION['protect']))) {
		// Create $ROOT_PHOTO_DIR if it doesn't exist
		if (!file_exists($ROOT_PHOTO_DIR)) {
			mkdir($ROOT_PHOTO_DIR, 0755, true);
		}

		if (!file_exists($STATS_DIR)) {
			mkdir($STATS_DIR, 0755, true);
		}

		// htmlentities() and str_replace() are used to sanitize the path and prevent the path traversal attacks. Not very elegant, but it should do the trick.
		$photo_dir = htmlentities(str_replace(DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $ROOT_PHOTO_DIR . DIRECTORY_SEPARATOR . $album . DIRECTORY_SEPARATOR));

		/* ======= FUNCTIONS ======= */

		/* EXTRACT LATITUDE AND LONGITUDE ---START---
	   * https://stackoverflow.com/a/16437888
	   */
		function gps($coordinate, $hemisphere)
		{
			if (!empty($coordinate) && !empty($hemisphere)) {
				if (is_string($coordinate)) {
					$coordinate = array_map('trim', explode(',', $coordinate));
				}
				for ($i = 0; $i < 3; $i++) {
					$part = explode('/', $coordinate[$i]);
					if (count($part) == 1) {
						$coordinate[$i] = $part[0];
					} else if (count($part) == 2) {
						$coordinate[$i] = floatval($part[0]) / floatval($part[1]);
					} else {
						$coordinate[$i] = 0;
					}
				}
				list($degrees, $minutes, $seconds) = $coordinate;
				$sign = ($hemisphere == 'W' || $hemisphere == 'S') ? -1 : 1;
				return $sign * ($degrees + $minutes / 60 + $seconds / 3600);
			}
		}
		/* EXTRACT LATITUDE AND LONGITUDE ---END--- */

		/* CREATE TIMS ---START--- */
		function create_tim($original, $tim, $tim_size)
		{
			global $TIM_QUALITY;
			// Load image
			$img = @imagecreatefromjpeg($original);
			if (!$img) return false;
			// Rotate $original based on orientation EXIF data
			$exif = exif_read_data($original);
			if (!empty($exif['Orientation'])) {
				switch ($exif['Orientation']) {
					case 3:
						$img = imagerotate($img, -180, 0);
						break;
					case 6:
						$img = imagerotate($img, -90, 0);
						break;
					case 8:
						$img = imagerotate($img, 90, 0);
						break;
				}
			}

			// Get image size
			$width = imagesx($img);
			$height = imagesy($img);

			// Calculate tim size
			$new_width	= $tim_size;
			$new_height = floor($height * ($tim_size / $width));

			// Create a new temporary image
			$tmp_img = imagecreatetruecolor($new_width, $new_height);

			// Copy and resize old image into new image
			imagecopyresampled($tmp_img, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

			// Save tim into a file
			$ok = @imagejpeg($tmp_img, $tim, $TIM_QUALITY);

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
			if ($current_page != 1 && !isset($_GET['file'])) {
				echo '<a href="?page=1"><img class="navigation" src="svg/arrow-up.svg" alt="' . L::nav_first . '" title="' . L::nav_first . '"/></a> ';
			}
			if ($current_page > 1 && !isset($_GET['file'])) {
				echo '<a href="?page=' . ($current_page - 1) . '"><img class="navigation" src="svg/arrow-left.svg" alt="' . L::nav_prev . '" title="' . L::nav_prev . '"/></a> ';
			}
			if ($current_page < $last_page && !isset($_GET['file'])) {
				echo '<a href="?page=' . ($current_page + 1) . '"><img class="navigation" src="svg/arrow-right.svg" alt="' . L::nav_next . '" title="' . L::nav_next . '"/></a>';
			}
			if ($current_page != $last_page && !isset($_GET['file'])) {
				echo ' <a href="?page=' . ($last_page) . '"><img class="navigation" src="svg/arrow-down.svg" alt="' . L::nav_last . '" title="' . L::nav_last . '"/></a>';
			}
			echo '</div>';
		}
		/* PAGINATION LINKS ---END --- */

		/* ======= FUNCTIONS ======= */

		// Create missing tims
		if (file_exists($photo_dir) && !file_exists($TIMS_DIR)) {
			mkdir($TIMS_DIR);
		}

		$files = array();
		// Find all files or a specific file if $_GET['query'] is set
		if (isset($_GET['query'])) {
			if ($_GET['search'] == 'search_name') {
				$files = glob($photo_dir . "*" . $_GET['query'] . "*.{" . $IMG_FORMATS . "}", GLOB_BRACE);
			} else {
				$all_files = glob($photo_dir . "*.{" . $IMG_FORMATS . "}", GLOB_BRACE);
				foreach ($all_files as $file) {
					$exif = exif_read_data($file);
					if (isset($exif['ImageDescription']) && stripos($exif['ImageDescription'], $_GET['query']) !== FALSE) {
						array_push($files, $file);
					}
				}
			}
			// Find all files if $_GET['query'] is not set
		} else {
			$files = glob($photo_dir . "*.{" . $IMG_FORMATS . "}", GLOB_BRACE);
		}

		// Check whether the reversed order option is enabled and sort the array accordingly
		if ($REVERSED_SORT) {
			rsort($files);
		}

		// Randomize the $files array
		if (isset($_GET['shuffle'])) {
			shuffle($files);
		}

		// Update count (we might have removed some files)
		$file_count = count($files);

		// Generate missing tims
		foreach ($files as $file) {
			$tim = $TIMS_DIR . basename($file);
			if (!file_exists($tim)) {
				create_tim($file, $tim, $TIM_SIZE);
			}
		}

		/* Prepare pagination. Calculate total items per page ---START--- */
		$total = count($files);
		$last_page = ceil($total / $PER_PAGE);

		if (!isset($_GET['file'])) {

			if (isset($_GET['page']) && ($_GET['page'] <= $last_page) && ($_GET['page'] > 0) && (!isset($_GET['all']))) {
				$page = $_GET['page'];
				$offset = ($PER_PAGE) * ($page - 1);
			} else {
				$page = 1;
				$offset = 0;
			}

			$max = $offset + $PER_PAGE;
		}
		if (!isset($max)) {
			$max = NULL;
		}
		if ($max > $total) {
			$max = $total;
		}
		/* Pagination. Calculate total items per page ---END --- */

		// The $grid parameter is used to show the main grid
		$grid = (isset($_GET['file']) ? $_GET['file'] : NULL);
		if (!isset($grid)) {
			echo '<div style="text-align: center; margin-bottom: 1.5em; margin-top: 1.5em;">';
			echo '<a style="text-decoration: none;" href="' . $BASE_URL . '/index.php?album"><img style="height: 5em; margin-bottom: 1.5em;" src="favicon.png" alt="' . $TITLE . '" /></a>';
			echo '<a style="text-decoration: none;" href="' . $BASE_URL . '/index.php?album"><h1 class="hide" style="font-size: 2.3em; margin: auto;">' . $TITLE . '</h1></a>';
			echo '</div>';
			echo '<div class="center" style="margin-bottom: 1em;">' . $SUBTITLE . '</div>';
			echo '<div class="center" style="margin-bottom: 1em;">';
	?>
			<!-- Search form -->
			<div style="margin-bottom: 0.5em;">
				<form autocomplete="off" method="GET" action=" ">
					<select style="vertical-align: middle;" name="search">
						<option value="search_usercomment"><?php echo L::find_by_usercomment; ?></option>
						<option value="search_name"><?php echo L::find_by_name; ?></option>
					</select>
					<input style="vertical-align: middle;" type="text" name="query">
					<!-- The hidden input field is used to pass the $album value (album) to the search -->
					<input type="hidden" name="album" value="<?php echo $album; ?>">
					<!-- The hidden input field to set $_GET['all'] to show all results without pagination -->
					<input type="hidden" name="all" value="show">
					<input class="navigation" type="image" src="svg/search.svg" alt="<?php echo L::search_btn; ?>" title="<?php echo L::search_btn; ?>">
				</form>
			</div>
			<?php
			// Show stats icon
			echo '<a href="stats.php"><img class="navigation" src="svg/stats.svg" alt="' . L::stats . '" title="' . L::stats . '"/></a>';
			// Show map icon
			if ($SHOW_MAP) {
				echo '<a href="map.php" target="_blank"><img class="navigation" src="svg/map.svg" alt="' . L::map . '" title="' . L::map . '"/></a>';
			}
			// Show the grid icon if there are several pages
			if (!isset($_GET['all']) && $file_count > $PER_PAGE) {
				echo '<a href="?all=show"><img class="navigation" src="svg/display-grid.svg" alt="' . L::img_show_all . '" title="' . L::img_show_all . '"/></a>';
			}
			// Show randomize icon
			echo '<a href="?shuffle"><img class="navigation" src="svg/dice-three.svg" alt="' . L::shuffle . '" title="' . L::shuffle . '"/></a>';

			echo '<hr style="margin-bottom: 1em;">';

			// Create an array with all subdirectories
			$all_sub_dirs = array_filter(glob($photo_dir . '*'), 'is_dir');
			$sub_dirs = array_diff($all_sub_dirs, array($TIMS_DIR));
			$count = count(glob($photo_dir . "*.{" . $IMG_FORMATS . "}", GLOB_BRACE));
			echo L::album_items_count . ': ' . $count;
			echo '</div>';

			// Populate a drop-down list with subdirectories
			if ((count($sub_dirs)) > 0 or (!empty($album))) {
				echo '<noscript>';
				echo '<h3><img style="vertical-align: middle; margin-right: .5em;" src="svg/denied.svg"/> ' . L::warning_enable_js . '</h3>';
				echo '</noscript>';
				echo '<div class="center" style="margin-bottom: 1em;">';
				echo '<a href="'  . $BASE_URL . '?album"><img class="navigation" alt="' . L::img_root_album . '" title="' . L::img_root_album . '" src="svg/home.svg"/></a> &rarr;&nbsp;';
				if (isset($_GET['album'])) {
					$higher_dirs = explode(DIRECTORY_SEPARATOR, $_GET['album']);
				} else {
					$higher_dirs = [];
				}
				$higher_dir_cascade = '';
				foreach ($higher_dirs as $higher_dir) {
					if (!empty($higher_dir)) {
						if (!empty($higher_dir_cascade)) {
							$higher_dir_cascade = $higher_dir_cascade . DIRECTORY_SEPARATOR;
						}
						$higher_dir_cascade = $higher_dir_cascade . $higher_dir;
						echo '<a href="'  . $BASE_URL . '?album=' . $higher_dir_cascade . '">' . $higher_dir . '</a> &rarr;&nbsp;';
					}
				}

				echo '<select class="select" name="" onchange="javascript:location.href = this.value;">';
				echo '<option value="Default">' . L::album . '</option>';
				foreach ($sub_dirs as $dir) {
					$dir_name = basename($dir);
					$dir_option = str_replace('\'', '&apos;', $album . DIRECTORY_SEPARATOR . $dir_name);
					echo '<option value="?album=' . ltrim($dir_option, '/') . '">' . $dir_name . '</option>';
				}
				echo '</select></div>';
			}
			?>

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
				echo '<div style="margin: auto; margin: 1.5em 1.5em;">';
				echo file_get_contents($photo_dir . "preamble.html");
				echo '</div>';
			}
			echo '</div>';
			/* SHOW THE GRID WITH TIMS ---START --- */
			echo '<div class="gallery-grid">';
			if (isset($_GET['all'])) {
				foreach ($files as $file) {
					$tim = $TIMS_DIR . basename($file);
					$file_path = pathinfo($file);
					echo '<figure class="gallery-frame">';
					echo '<a href="' . $BASE_URL . '/index.php?file=' . $file  . '"><img class="gallery-img" src="' . $tim . '" alt="' . $file_path['filename'] . '" title="' . $file_path['filename'] . '"></a>';
					echo '<figcaption>' . $file_path['filename'] . '</figcaption></figure>';
				}
			} else {
				for ($i = $offset; $i < $max; $i++) {
					$file = $files[$i];
					$tim = $TIMS_DIR . basename($file);
					$file_path = pathinfo($file);
					echo '<figure class="gallery-frame">';
					echo '<a href="' . $BASE_URL . '/index.php?file=' . $file . '"><img class="gallery-img" src="' . $tim . '" alt="' . $file_path['filename'] . '" title="' . $file_path['filename'] . '"></a>';
					echo '<figcaption>' . $file_path['filename'] . '</figcaption></figure>';
				}
			}
			echo '</div>';

			/* SHOW PAGINATION */
			if (!isset($_GET['all'])) {
				// Set $page to NULL if $file is set to avoid undefined variable warning
				(isset($_GET['file'])) ? $page = NULL : NULL;
				show_pagination($page, $last_page, $album); // Pagination. Show navigation on bottom of page
			}
		}
	}
	/* SHOW THE GRID WITH TIMS ---END --- */

	/* SHOW SINGLE PHOTO */
	// The $file parameter is used to show an individual photo
	$file = isset($_GET['file']) ? $_GET['file'] : '';
	// Get the current views and downloads count
	$filename = pathinfo($file, PATHINFO_FILENAME);
	$views_file = $STATS_DIR . DIRECTORY_SEPARATOR . $filename . ".views";
	$downloads_file = $STATS_DIR . DIRECTORY_SEPARATOR . $filename . ".downloads";
	if (file_exists($downloads_file)) {
		$downloads_count = fgets(fopen($downloads_file, 'r'));
	} else {
		$downloads_count = 0;
	}
	if (!empty($file)) {
		// If $views_file exists, increment views count by 1
		if (file_exists($views_file)) {
			$views_count = fgets(fopen($views_file, 'r'));
			// Increment count only if $_COOKIE['nocount'] is not set
			if (!isset($_COOKIE['nocount'])) {
				$views_count++;
				@file_put_contents($views_file, $views_count);
			}
		} else {
			// Otherwise, create $views_file and set $views_count to 1
			@file_put_contents($views_file, '1');
			$views_count = 1;
		}
		$key = array_search($file, $files); // Determine the array key of the current item (we need this for generating the Next and Previous links)
		$tim = $TIMS_DIR . basename($file);
		// Get the content of the ImageDescription EXIF tag
		$exif = @exif_read_data($file);
		$comment = isset($exif['ImageDescription']) ? htmlentities($exif['ImageDescription']) : NULL;
		// Get latitude and longitude values
		$exif = exif_read_data($file, 0, true);
		if ($exif && array_key_exists('GPS', $exif)) {
			$lat = gps($exif['GPS']['GPSLatitude'], $exif['GPS']['GPSLatitudeRef']);
			$lon = gps($exif['GPS']['GPSLongitude'], $exif['GPS']['GPSLongitudeRef']);
		} else {
			$lat = NULL;
			$lon = NULL;
		}
		// Get width and height, calculate resolution in megapixels
		$image_size = getimagesize($file);
		$width = $image_size[0];
		$height = $image_size[1];
		if (!$width || !$height) {
			$resolution = NULL;
		} else {
			$resolution = ' • ' . sprintf("%.2f", ($width * $height) / 1000000) . 'MP';
		}

		$file_path = pathinfo($file);

		// Easter egg: if $_GET['t'] is set, rebuild the tim of the currently viewed photo
		if (isset($_GET['t'])) {
			unlink($tim);
			create_tim($file, $tim, $TIM_SIZE);
		}

		// Get URL of the current page for use with the Back button
		$url = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		$_SESSION['page'] = $url;

		echo '<h1 style="margin-top: 1em;">' . $file_path['filename'] . '</h1><hr>';

		/* NAVIGATION LINKS ---START--- */
		// Set first and last photo navigation links according to specified	 sort order
		$last_photo = $files[count($files) - 1];
		$first_photo = $files[0];

		// If there is only one photo in the album, show the home navigation link
		if ($file_count == 1) {
			echo '<div class="navigation"><a href="' . $BASE_URL . '/index.php?album=' . $album . '" accesskey="g"><img src="svg/home.svg" alt="' . L::nav_home . '" title="' . L::nav_home . '"/></a></div>';
		}
		// Disable the Previous link if this is the FIRST photo
		elseif (empty($files[$key - 1])) {
			echo '<div class="navigation"><a href="' . $BASE_URL . '/index.php?album=' . $album . '" accesskey="g"><img class="navigation" src="svg/home.svg" alt="' . L::nav_home . '" title="' . L::nav_home . '"/></a><a href="' . $BASE_URL . '/index.php?file=' . $files[$key + 1] . '" accesskey="n"><img class="navigation" src="svg/arrow-right.svg"  alt="' . L::nav_next . '" title="' . L::nav_next . '"/></a><a href="' . $BASE_URL . '/index.php?file=' . $last_photo . '" accesskey="l"><img class="navigation" src="svg/arrow-down.svg" alt="' . L::nav_last . '" title="' . L::nav_last . '"/></a></div>';
		}
		// Disable the Next link if this is the LAST photo
		elseif (empty($files[$key + 1])) {
			echo '<div class="navigation"><a href="' . $BASE_URL . '/index.php?album=' . $album . '" accesskey="g"><img class="navigation" src="svg/home.svg" alt="' . L::nav_home . '" title="' . L::nav_home . '"/></a><a href="' . $BASE_URL . '/index.php?file=' . $first_photo . '" accesskey="f"><img class="navigation" src="svg/arrow-up.svg" alt="' . L::nav_first . '" title="' . L::nav_first . '"/></a><a href="' . $BASE_URL . '/index.php?file=' . $files[$key - 1] . '" accesskey="p"><img class="navigation" src="svg/arrow-left.svg" alt="' . L::nav_prev . '" title="' . L::nav_prev . '"/></a></div>';
		}
		// Show all navigation links
		else {

			echo '<div class="navigation"><a href="' . $BASE_URL . '/index.php?album=' . $album . '" accesskey="g"><img class="navigation" src="svg/home.svg" alt="' . L::nav_home . '" title="' . L::nav_home . '"/></a><a href="' . $BASE_URL . '/index.php?file=' . $first_photo . '" accesskey="f"><img class="navigation" src="svg/arrow-up.svg" alt="' . L::nav_first . '" title="' . L::nav_first . '"/></a><a href="' . $BASE_URL . '/index.php?file=' . $files[$key - 1] . '" accesskey="p"><img class="navigation" src="svg/arrow-left.svg" alt="' . L::nav_prev . '" title="' . L::nav_prev . '"/></a><a href="' . $BASE_URL . '/index.php?file=' . $files[$key + 1] . '" accesskey="n"><img class="navigation" src="svg/arrow-right.svg" alt="' . L::nav_next . '" title="' . L::nav_next . '"/></a><a href="' . $BASE_URL . '/index.php?file=' . $last_photo . '" accesskey="l"><img class="navigation" src="svg/arrow-down.svg" alt="' . L::nav_last . '" title="' . L::nav_last . '"/></a></div>';
		}
		/* NAVIGATION LINKS ---END--- */

		// Check whether the localized description file matching the browser language exists
		if (file_exists($photo_dir . $i18n->getAppliedLang() . '-' . $file_path['filename'] . '.txt')) {
			$description = @file_get_contents($photo_dir . $i18n->getAppliedLang() . '-' . $file_path['filename'] . '.txt');
			// If the localized description file doesn't exist, use the default one
		} else {
			$description = @file_get_contents($photo_dir . $file_path['filename'] . '.txt');
		}

		// Get aperture, exposure, iso, and datetime from EXIF
		$aperture = (!isset($exif['COMPUTED']['ApertureFNumber']) ? NULL : htmlentities($exif['COMPUTED']['ApertureFNumber']));
		$exposure = (!isset($exif['EXIF']['ExposureTime']) ? NULL : htmlentities($exif['EXIF']['ExposureTime']));
		$f_length = (!isset($exif['EXIF']['FocalLength']) ? NULL : ' • ' . eval('return ' . htmlentities($exif['EXIF']['FocalLength']) . ';') . 'mm');
		// Normalize exposure
		// https://stackoverflow.com/questions/3049998/parsing-exifs-exposuretime-using-php
		if (!is_null($exposure)) {
			$parts = explode('/', $exposure);
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
		$iso = !isset($exif['EXIF']['ISOSpeedRatings']) ? NULL : ' • ' . htmlentities($exif['EXIF']['ISOSpeedRatings']);
		$datetime = !isset($exif['EXIF']['DateTimeOriginal']) ? NULL : htmlentities((date('Y-m-d H:i', strtotime($exif['EXIF']['DateTimeOriginal']))));

		// Concatenate $exif_info
		$exif_info = '<img class="navigation" src="svg/camera.svg" alt="' . L::img_exif . '" title="' . L::img_exif . '"/>' . $aperture . $f_length . $exposure . $iso . $resolution . '<img class="navigation" src="svg/calendar.svg" alt="' . L::img_date . '" title="' . L::img_date . '"/>' .  $datetime;

		// Add the pin icon if the photo contains geographical coordinates
		if (!empty($lat) && !empty($lon)) {
			//Generate Geo URI
			if ($OPENSTREETMAP) {
				$map_url = '<a href="http://www.openstreetmap.org/index.html?mlat=' . $lat . '&mlon=' . $lon . '&zoom=18" target="_blank"><img class="navigation" src="svg/pin.svg" alt="' . L::img_map . '" title="' . L::img_map . '"/></a>';
			} else {
				$map_url = '<a href="geo:' . $lat . ',' . $lon . '"><img class="navigation" src="svg/pin.svg" alt="' . L::img_map . '" title="' . L::img_map . '"/></a>';
			}
			$exif_info = $exif_info . $map_url;
		}
		// Find all RAW files
		$raw_file = glob($photo_dir . $file_path['filename'] . "*.{" . $RAW_FORMATS . "}", GLOB_BRACE) ?? NULL;
		$raw = !empty($raw_file[0]) ? htmlentities($raw_file[0]) : NULL;
		$image_download = '<a href="' . $BASE_URL . '/download.php?file=' . htmlentities($file) . '"><img class="navigation" src="svg/download.svg" alt="' . L::img_download . '" title="' . L::img_download . '" /></a>';
		if ($raw) {
			$image_delete = '<a href="' . $BASE_URL . '/delete.php?file=' . htmlentities($file) . "&raw=" . $raw . '"><img class="navigation" src="svg/remove-image.svg" alt="' . L::img_delete . '" title="' . L::img_delete . '" /></a>';
		} else {
			$image_delete = '<a href="' . $BASE_URL . '/delete.php?file=' . htmlentities($file) . '"><img class="navigation" src="svg/remove-image.svg" alt="' . L::img_delete . '" title="' . L::img_delete . '" /></a>';
		}

		//Check if the related RAW file exists and link to it
		if (!empty($raw_file)) {
			$raw_download = '<a href="' . $BASE_URL . '/download.php?file=' . $raw . '"><img class="navigation" alt="' . L::raw_download . '" title="' . L::raw_download . '" src="svg/raw.svg"/></a>';
		} else {
			$raw_download = NULL;
		}
		if ($DOWNLOAD) {
			echo '<div class="center"><img src="' . htmlentities($tim) . '" onclick="this.requestFullscreen()" alt="' . $file_path['filename'] . '" title="' . $file_path['filename'] . '" /><div class="caption">' . $comment . ' ' . $description . '</div>';
			echo '<div class="caption">' . $exif_info . '</div>';
			echo '<div class="caption">' . $image_download . $raw_download . $image_delete . '</div>';
			echo '<div class="caption">' . L::views . ': ' . $views_count . ' ' . L::downloads . ": " . $downloads_count . '</div>';
		} else {
			echo '<div class="center"><img src="' . htmlentities($tim) . '" onclick="this.requestFullscreen()" alt="' . $file_path['filename'] . '" title="' . $file_path['filename'] . '" /><div class="caption">' . $comment . ' ' . $description . '</div>';
			echo '<div class="caption">' . $exif_info . "<span style='margin-left: 1em;'>" . $image_delete . '</span></div>';
		}
		// Show embedded map if photo is geotagged and $SHOW_MAP is true
		if ($SHOW_MAP && !empty($lat) && !empty($lon)) {
			echo '<div id="map"></div>';
		}
	}
	?>
	<!-- JavaScript to display embedded map for geotagged photos -->
	<?php if ($SHOW_MAP && !empty($lat) && !empty($lon)) : ?>
		<script type="text/javascript">
			// Creating map options
			var mapOptions = {
				center: [<?php echo $lat; ?>, <?php echo $lon; ?>],
				zoom: 10
			}
			// Creating a map object
			var map = new L.map('map', mapOptions);
			// Creating a Layer object
			var layer = new L.TileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png');
			// Adding layer to the map
			map.addLayer(layer);
			// Creating a marker
			var marker = L.marker([<?php echo $lat; ?>, <?php echo $lon; ?>]);
			// Adding marker to the map
			marker.addTo(map);
		</script>
	<?php endif; ?>
	<?php // Show footer
	if (isset($_COOKIE['nocount'])) {
		$FOOTER = $FOOTER . " <span style='color: #ff9e64;'>&there4;</span>";
	}
	if ($LINKS) {
		$array_length = count($URLS);
		echo '<div class="footer" style="z-index: 2">';
		foreach ($URLS as $url) {
			echo '<span style="word-spacing:0.1em;"><a href="' . $url[0] . '">' . $url[1] . '</a> • </span>';
		}
		echo  $FOOTER . '</div>';
	} else {
		echo '<div class="footer">' . $FOOTER . '</div>';
	}
	?>
</body>

</html>