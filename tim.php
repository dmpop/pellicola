<?php
include('config.php');

function tim($source, $newWidth)
{
    $image = imagecreatefromjpeg($source);
    // Get image dimensions
    $dimensions = getimagesize($source);
    $width = $dimensions[0];
    $height = $dimensions[1];

    // Calculate ration
    $ratio = $width / $height;
    $newHeight = $newWidth / $ratio;

    // Create an empty image
    $resizedImage = imagecreatetruecolor($newWidth, $newHeight);
    // Fill it with resized version of original image
    imagecopyresampled($resizedImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

    // Notify the browser that incoming response is an image
    header("Content-Type: image/jpeg");
    echo imagejpeg($resizedImage);

    // Free the memory
    imagedestroy($image);
    imagedestroy($resizedImage);
}

tim(hex2bin($_GET["image"]), $TIM_SIZE);