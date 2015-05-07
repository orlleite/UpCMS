<?php

/**
 * Generates thumb.
 * (GET:src) for image source
 * (GET:w) for image width
 * (GET:h) for image height
 * e.g thumb.php?src=myimage.jpg&w=100&h=100
 * @author Orlando Leite
 * @version 0.8
 * @package utils
 * @access public
 */
$src = "../".$_GET["src"];
$width = $_GET["w"];
$height = $_GET["h"];

$handle = fopen( $src, "r" );
$ext = strtolower( end( explode( ".", $src ) ) );

if( $ext == "jpg" || $ext == "jpeg" )
	$img = imagecreatefromjpeg( $src );
else if( $ext == "png" )
	$img = imagecreatefrompng( $src );
else if( $ext == "gif" )
	$img = imagecreatefromgif( $src );
else if( $ext == "bmp" )
	$img = imagecreatefromwbmp( $src );
else
	$img = imagecreatetruecolor( $width, $height );

$fimg = imagecreatetruecolor( $width, $height );

$iwidth = imagesx( $img );
$iheight= imagesy( $img );

$prop = $iwidth / $iheight;
$nprop = $width / $height;

if( $nprop >= $prop )
	imagecopyresampled( $fimg, $img, 0, ( $height - ( $width / $prop ) ) * 0.5, 0, 0, $width, $width / $prop, $iwidth, $iheight );
else
	imagecopyresampled( $fimg,	$img, ( $width - ( $height * $prop ) ) * 0.5, 0, 0, 0, $height * $prop, $height, $iwidth, $iheight );

header('Content-Type: image/jpeg');
imagejpeg( $fimg, $img["name"], 100 );

?>