<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">

<html>

	<head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	<link href="style.css" rel="stylesheet" type="text/css" media="all" />
	<link href='http://fonts.googleapis.com/css?family=Asap:400,700,400italic,700italic' rel='stylesheet' type='text/css'>
	<link rel="shortcut icon" href="favicon.ico" />
	
	<?php
	
	include 'config.php';
	
	echo "<title>$title</title>";
	echo "</head>";
	echo "<body>";
	
	echo "<div id='content'><h1>$title</h1>";
	echo "<div class='center'>$tagline</div>";
	
	$f = $_GET['f'];
	if (empty($f))
		{
		$dir=$basedir;
		}
	else
		{
		$dir=$basedir.$f."/";
		}
	
	$files = glob($dir.'*.jpg', GLOB_BRACE);
	$fileCount = count(glob($dir.'*.jpg'));
	
	for ($i=($fileCount-1); $i>=0; $i--)  {  
	$exif = exif_read_data($files[$i], 0, true);
	$filepath = pathinfo($files[$i]);
	echo "<h2>".$filepath['filename']."</h2>";
	echo "<p>".$exif['COMPUTED']['UserComment']."</p>";
    echo '<a href="'.$files[$i].'"><img class="dropshadow" src="'.$files[$i].'" alt="" width="500px"></a>';
    $Fnumber = explode("/", $exif['EXIF']['FNumber']);
    $Fnumber = $Fnumber[0] / $Fnumber[1];
    echo "<p class='box'>Aperture: f/</em>".$Fnumber." Shutter speed: " .$exif['EXIF']['ExposureTime']. " ISO: ".$exif['EXIF']['ISOSpeedRatings']. " Date: ".$exif['EXIF']['DateTimeOriginal']."</p>";
    }
    
    echo "<div class='footer'>$footer</div>";
	
	?>
	</div>
	</body>
</html>
