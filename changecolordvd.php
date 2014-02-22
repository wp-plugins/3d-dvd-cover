<?php
$color=($_GET['color'])?($_GET['color']):"#00FFFF";
$logo=($_GET['logo'])?($_GET['logo']):"logo3";
$hex = str_replace("#", "",$color);
   if(strlen($hex) == 3) {
      $r = hexdec(substr($hex,0,1).substr($hex,0,1));
      $g = hexdec(substr($hex,1,1).substr($hex,1,1));
      $b = hexdec(substr($hex,2,1).substr($hex,2,1));
   } else {
      $r = hexdec(substr($hex,0,2));
      $g = hexdec(substr($hex,2,2));
      $b = hexdec(substr($hex,4,2));
   }
   $stamp = imagecreatefromjpeg('imagenes_dvd/dvd_portada_delantera.jpg');
   $stamp_2 = imagecreatefromjpeg('imagenes_dvd/dvd_lomo.jpg');
   $stamp_3 = imagecreatefromjpeg('imagenes_dvd/dvd_portada_trasera.jpg');
   
	
	if($_GET['image_1']!=''){
	
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $_GET['image_1']); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // good edit, thanks!
		curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1); // also, this seems wise considering output is image.
		$data = curl_exec($ch);
		curl_close($ch);
		
		$stamp = imagecreatefromstring($data);
	}
	if($_GET['image_2']!=''){
	
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $_GET['image_2']); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // good edit, thanks!
		curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1); // also, this seems wise considering output is image.
		$data = curl_exec($ch);
		curl_close($ch);
		
		$stamp_2 = imagecreatefromstring($data);
	}
	if($_GET['image_3']!=''){
	
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $_GET['image_3']); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // good edit, thanks!
		curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1); // also, this seems wise considering output is image.
		$data = curl_exec($ch);
		curl_close($ch);
		
		$stamp_3 = imagecreatefromstring($data);
	}
	
header('Content-type: image/png');
$image = imagecreatefromjpeg('imagenes_dvd/portada_dvd.jpg');
/*
imagefilter($image, IMG_FILTER_NEGATE); 
imagefilter($image, IMG_FILTER_COLORIZE, $r,  $g, $b); // make it blue!
*/


// Set the margins for the stamp and get the height/width of the stamp image
$marge_right = 454;
$marge_bottom = 0;

$stamp=imagerotate($stamp,180, 0);


$sx = imagesx($stamp);
$sy = imagesy($stamp);
// Copy the stamp image onto our photo using the margin offsets and the photo 
// width to calculate positioning of the stamp. 
imagecopy($image, $stamp, imagesx($image) - $sx - $marge_right, imagesy($image) - $sy - $marge_bottom, 0, 0, imagesx($stamp), imagesy($stamp));

$stamp = imagecreatefrompng('imagenes_dvd/filedemo.png');;
imagecopy($image, $stamp, imagesx($image) - $sx - $marge_right, imagesy($image) - $sy - $marge_bottom, 0, 0, imagesx($stamp), imagesy($stamp));

$stamp =  $stamp_3 ;

// Set the margins for the stamp and get the height/width of the stamp image
$marge_right = 109;
$marge_bottom = 0;
$sx = imagesx($stamp);
$sy = imagesy($stamp);
// Copy the stamp image onto our photo using the margin offsets and the photo 
// width to calculate positioning of the stamp. 
imagecopy($image, $stamp, imagesx($image) - $sx - $marge_right, imagesy($image) - $sy - $marge_bottom, 0, 0, imagesx($stamp), imagesy($stamp));

$stamp = imagecreatefrompng('imagenes_dvd/filedemo.png');;
imagecopy($image, $stamp, imagesx($image) - $sx - $marge_right, imagesy($image) - $sy - $marge_bottom, 0, 0, imagesx($stamp), imagesy($stamp));


$stamp =  $stamp_2 ;

// Set the margins for the stamp and get the height/width of the stamp image
$marge_right = 73;
$marge_bottom = 0;
$sx = imagesx($stamp);
$sy = imagesy($stamp);
// Copy the stamp image onto our photo using the margin offsets and the photo 
// width to calculate positioning of the stamp. 
imagecopy($image, $stamp, imagesx($image) - $sx - $marge_right, imagesy($image) - $sy - $marge_bottom, 0, 0, imagesx($stamp), imagesy($stamp));



imagejpeg($image);
?>