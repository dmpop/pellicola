<?php
include('config.php');
include('protect.php');
// Upload directory
$upload_dir = "photos";
?>

<html lang="en">
<!-- Author: Dmitri Popov, dmpop@linux.com
         License: GPLv3 https://www.gnu.org/licenses/gpl-3.0.txt -->

<head>
	<title>Upload</title>
	<meta charset="utf-8">
	<link rel="shortcut icon" href="favicon.png" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<style>
		body {
			font-family: 'Barlow', sans-serif;
			font-size: 1em;
			text-align: justify;
			background-color: #303030;
		}

		h1 {
			color: #e3e3e3;
			font-family: 'Barlow', sans-serif;
			font-size: 2.5em;
			font-weight: 400;
			text-align: center;
			margin-top: 0.3em;
			margin-bottom: 0.5em;
			line-height: 100%;
			letter-spacing: 1px;
		}

		p {
			font-size: 1em;
			text-align: center;
		}

		form {
			margin-left: auto;
			margin-right: auto;
			margin-bottom: 0px;
			margin-top: 0.5em;
			border-radius: 5px;
			width: 25em;
			border-width: 1px;
			font-size: 1em;
			letter-spacing: 3px;
			padding: 5px;
			color: #ffffff;
			background: #3399ff;
			text-align: center;
		}

		#content {
			color: #e3e3e3;
		}

		.text {
			text-align: center;
			padding: 0px;
			color: inherit;
			float: left;
		}

		.center {
			font-size: 1em;
			padding: 1px;
			height: auto;
			text-align: center;
			padding: 0px;
			margin-top: 20%;
			margin-bottom: 2em;
		}
	</style>
</head>

<body>
	<div id='content'>
		<div class='center'>
			<?php
			if (isset($_POST['submit'])) {
				// count total files
				$countfiles = count($_FILES['file']['name']);
				// looping all files
				for ($i = 0; $i < $countfiles; $i++) {
					$filename = $_FILES['file']['name'][$i];
					// upload file
					move_uploaded_file($_FILES['file']['tmp_name'][$i], $upload_dir . DIRECTORY_SEPARATOR . $filename);
				}
			}
			?>
			<h1>Upload</h1>
			<form method='post' action='' enctype='multipart/form-data'>
				<input class="uk-input" type="file" name="file[]" id="file" multiple>
				<button type='submit' role='button' name='submit'>Upload</button>
				<input type="button" onclick="window.location.href='index.php';" value="Back" />
			</form>
		</div>
	</div>
</body>

</html>