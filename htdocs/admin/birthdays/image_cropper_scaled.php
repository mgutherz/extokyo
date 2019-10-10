<?php

# Written by Alan Mizrahi on 2009-02-20

	# Crops and resizes image, returns FALSE on sucess, or error message on failure
	function cropImage($dst, $src, $x, $y, $w, $h, $dstw, $dsth, $quality = 95) {
		/* This is commented as a workaround of exif_imagetype not working or not being installed
		   remove the pathinfo based type detection when exif_imagetype works, replace the switch by:
		$imgtype = exif_imagetype($image);
		*/
		switch (strtolower(pathinfo($src, PATHINFO_EXTENSION))) {
			case 'gif' : $imgtype = IMAGETYPE_GIF; break;
			case 'jpg' : case 'jpeg': $imgtype = IMAGETYPE_JPEG; break;
			case 'png' : $imgtype = IMAGETYPE_PNG; break;
		}

		switch ($imgtype) {
			case IMAGETYPE_GIF:
				$srcImage = imagecreatefromgif($src);
			break;
			case IMAGETYPE_JPEG:
				$srcImage = imagecreatefromjpeg($src);
			break;
			case IMAGETYPE_PNG:
				$srcImage = imagecreatefrompng($src);
			break;
			default:
				return "Unsupported image type: $imgtype";
			break;
		}

		if (!$srcImage)
			return "Error opening image";
		
		if ($w <= 0) # use all if no width specified
			$w = imagesx($srcImage);
		if ($h <= 0) # use all if no height specified
			$h = imagesy($srcImage);

		if ($w / $h > $dstw / $dsth) { # use dstw, scale down dsth
			$dsth = intval($h * $dstw / $w);
		} else { # use dsth, scale down dstw
			$dstw = intval($w * $dsth / $h);
		}

		if (!($dstImage = imagecreatetruecolor($dstw, $dsth)))
			return "Error creating new image";

		imagecopyresized($dstImage, $srcImage, 0, 0, $x, $y, $dstw, $dsth, $w, $h);
		imagedestroy($srcImage);
		
		switch ($imgtype) {
			case IMAGETYPE_GIF:
				$tmp = imagegif($dstImage, $dst);
			break;
			case IMAGETYPE_JPEG:
				$tmp = imagejpeg($dstImage, $dst, $quality);
			break;
			case IMAGETYPE_PNG:
				$tmp = imagepng($dstImage, $dst);
			break;
		}
		imagedestroy($dstImage);
		if (!$tmp)
			return "Error saving image";
		return FALSE;
	}

?>
