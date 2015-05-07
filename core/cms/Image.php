<?php

/**
 * Edit and modify images, specialy uploaded.
 * @author Orlando Leite
 * @version 0.8
 * @package core
 * @subpackage cms
 * @access public
 * @name ApplicationImage
 */
class ApplicationImage
{
	/**
    * Resize and crop a image. The path comes from POST (REQUEST:rel).
    * @author Orlando Leite
    * @access public
    * @static
    * @return void
    */
	public static function resizecrop()
	{
		global $UpCMS;
		
		$UpCMS->dispatchEvent( new Event( UpCMS::IMAGE_BEFORE_RESIZECROP, $this ) );
		
		$source = $_POST["source"];
		$crop = explode( ",", $_POST["crop"] );
		
		$ext = end( explode( "/", $source ) );
		$ext = end( explode( ".", $ext ) );
		
		$image = Util::loadImage( $source, $ext );
		$image = Util::crop( $image, $crop[0], $crop[1], $crop[2], $crop[3] );
		
		if( !empty( $_POST["size"] ) )
		{
			$temp = preg_split( "/[x:,\/]/", $_POST["size"] );
			$image = Util::resize( $image, $temp[0], $temp[1] );
		}
		else
		{
			$w = imagesx( $image );
			$h = imagesy( $image );
			
			if( !empty( $_POST["min"] ) )
			{
				$temp = preg_split( "/[x:,\/]/", $_POST["min"] );
				
				if( $w < $temp[0] or $h < $temp[1] )
				{
					$prop1 = $w / $h;
					
					if( $prop1 > $temp[0] / $temp[1] ) $image = Util::resize( $image, $temp[1] * $prop1, $temp[1] );
					else $image = Util::resize( $image, $temp[0], $temp[0] / $prop1 );
				}
			}
			if( !empty( $_POST["max"] ) )
			{
				$temp = preg_split( "/[x:,\/]/", $_POST["max"] );
				
				if( $w > $temp[0] or $h > $temp[1] )
				{
					$prop1 = $w / $h;
					
					if( $prop1 < $temp[0] / $temp[1] ) $image = Util::resize( $image, $temp[1] * $prop1, $temp[1] );
					else $image = Util::resize( $image, $temp[0], $temp[0] / $prop1 );
				}
			}
		}
		
		if( $_POST["format"] != "" )
		{
			$format = $_POST["format"];
			$source = substr( $source, 0, strlen( $source ) - strlen( $ext ) ).$format;
			$ext = $format;
		}
		
		$source = Util::nonOverwritePath( $source );
		
		global $result;
		
		$result->status = Util::saveImage( $image, $source, $ext );
		$result->path = $source;
		
		$UpCMS->dispatchEvent( new Event( UpCMS::IMAGE_AFTER_RESIZECROP, $this ) );
	}
}

?>