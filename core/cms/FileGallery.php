<?php

/**
 * Solve problems with files, specialy from uploaded.
 * @author Orlando Leite
 * @version 0.8
 * @package core
 * @subpackage cms
 * @access public
 * @name ApplicationFileGallery
 */
class ApplicationFileGallery
{
	/**
    * Verify if the path exists and have write permissions. The path comes from POST (REQUEST:rel).
    * @author Orlando Leite
    * @access private
    * @static
    * @return string a correct string path
    */
	private static function init()
	{
		$rel = $_REQUEST["rel"];
		
		if( strpos( $rel, "." ) !== false )
		{
			$temp = explode( "=", $rel );
			$rel = array_pop( explode( ".", reset( $temp ) ) );
		}
		
		global $upload_folder;
		
		$filePath = $upload_folder.$rel;
		
		if( !is_dir( $filePath ) ) 
		{
			mkdir( $filePath, 0777 );
			chmod( $filePath, 0777 );
		}
		
		$filePath .= "/".$_REQUEST["id"];
		if( !is_dir( $filePath ) ) 
		{
			mkdir( $filePath, 0777 );
			chmod( $filePath, 0777 );
		}
		
		return $filePath;
	}
	
	/**
    * Delete a file. And set global $result for success or error. The path comes from POST (REQUEST:rel).
    * @author Orlando Leite
    * @access public
    * @static
    * @return void
    */
	public static function delete()
	{
		global $UpCMS;
		
		$UpCMS->dispatchEvent( new Event( UpCMS::FILEGALLERY_BEFORE_DELETE, $this ) );
		
		$filePath = ApplicationFileGallery::init();
		
		$filePath = $filePath ."/".$_POST["name"];
		$fileResult = unlink( $filePath );
		
		global $upload_folder, $result;
		
		$result->path = substr( $filePath, strlen( $upload_folder ) );
		$result->status = $fileResult;
		
		$UpCMS->dispatchEvent( new Event( UpCMS::FILEGALLERY_AFTER_DELETE, $this ) );
	}
	
	/**
    * Rename a file. And set global $result for success or error. The path comes from POST (REQUEST:rel).
    * @author Orlando Leite
    * @access public
    * @static
    * @return void
    */
	public static function rename()
	{
		global $UpCMS, $upload_folder;
		
		$UpCMS->dispatchEvent( new Event( UpCMS::FILEGALLERY_BEFORE_RENAME, $this ) );
		
		$filePath = ApplicationFileGallery::init();
		
		$fileResult = rename( $filePath."/".$_POST["name"], $filePath."/".$_POST["newname"] );
		$filePath = substr( $filePath, strlen( $upload_folder ) )."/".$_POST["newname"];
		
		global $upload_folder, $result;
		
		$result->path = $filePath;
		$result->status = $fileResult;
		
		$UpCMS->dispatchEvent( new Event( UpCMS::FILEGALLERY_AFTER_RENAME, $this ) );
	}
	
	/**
    * List files from a folder and set this values to global $result. The path comes from POST (REQUEST:rel).
    * @author Orlando Leite
    * @access public
    * @static
    * @return void
    */
	public static function alist()
	{
		global $UpCMS;
		
		$UpCMS->dispatchEvent( new Event( UpCMS::FILEGALLERY_BEFORE_ALIST, $this ) );
		
		$filePath = ApplicationFileGallery::init();
		
		$types = $_POST["types"];
		$convert = $_POST["convert"];
		
		$extensions = $convert.";".$types;
		
		// GET FILES IN FOLDER //
		$fileResult = Util::listfiles( $filePath, $extensions );
		
		$compressions = "";
		if( extension_loaded( "zip" ) ) $compressions .= "zip;";
		if( extension_loaded( "rar" ) ) $compressions .= "zip;";
		
		global $result;
		
		$result->path = substr( $filePath, strlen( $upload_folder ) );
		$result->list = $fileResult;
		
		$UpCMS->dispatchEvent( new Event( UpCMS::FILEGALLERY_AFTER_ALIST, $this ) );
	}
	
	/**
    * Saves a file uploaded to the correct folder and set the new name and new path to global $result. The destination path comes from POST (REQUEST:rel).
    * @author Orlando Leite
    * @access public
    * @static
    * @return void
    */
	public static function upload()
	{
		global $UpCMS;
		
		$UpCMS->dispatchEvent( new Event( UpCMS::FILEGALLERY_BEFORE_UPLOAD, $this ) );
		
		$filePath = ApplicationFileGallery::init();
		
		$i = 1;
		$curname = basename( Util::nonOverwritePath( Util::presetOnlineURL( $filePath."/".basename( $_FILES['file']['name'] ) ) ) );
		
		global $upload_folder, $result;
		
		$filePath .= "/".$curname;
		$result->name	= $curname;
		$result->path	= substr( $filePath, strlen( $upload_folder ) );
		$result->status	= @move_uploaded_file( $_FILES['file']['tmp_name'], $filePath );
		$r = @chmod( $filePath, 0777 );
		
		$UpCMS->dispatchEvent( new Event( UpCMS::FILEGALLERY_AFTER_UPLOAD, $this ) );
	}
	
	/**
    * Decompress a zip file and set the names and paths files to global $result. The path of the zip comes from POST (REQUEST:rel).
    * @author Orlando Leite
    * @access public
    * @static
    * @return void
    */
	public static function decompress()
	{
		global $UpCMS;
		
		$UpCMS->dispatchEvent( new Event( UpCMS::FILEGALLERY_BEFORE_DECOMPRESS, $this ) );
		
		$filePath = ApplicationFileGallery::init();
		$file = $_POST["file"];
		$ext = end( explode( ".", $file ) );
		
		if( strtolower( $ext ) == "zip" )
			$files = Util::decompressZip( $filePath."/".$file, $filePath );
		
		global $result;
		if( $files )
		{
			$result->files = $files;
			$result->status = true;
		}
		else $result->status = false;
		
		$UpCMS->dispatchEvent( new Event( UpCMS::FILEGALLERY_AFTER_DECOMPRESS, $this ) );
	}
	
	/**
    * Check if the files have the neeeded parameters and set global $result for success or error. This is mainly used for images. The file paths comes from POST (REQUEST:rel).
    * @author Orlando Leite
    * @access public
    * @static
    * @return void
    */
	public static function verify()
	{
		global $UpCMS;
		
		$UpCMS->dispatchEvent( new Event( UpCMS::FILEGALLERY_BEFORE_VERIFY, $this ) );
		
		$files = $_POST["files"];
		$files = explode( "\",\"", substr( $files, 1, count( $files ) - 2 ) );
		
		$types = $_POST["types"];
		$convert = $_POST["convert"];
		$min = explode( ",", $_POST["min"] );
		$max = explode( ",", $_POST["max"] );
		$size = explode( ",", $_POST["size"] );
		$ratio = explode( ",", $_POST["ratio"] );
		
		$value = array();
		
		foreach( $files as $temp )
		{
			$ext = strtolower( end( explode( ".", $temp ) ) );
			
			if( strpos( "jpg;png;gif;bmp;jpeg", $ext ) !== false )
			{
				$img = Util::loadImage( $temp, $ext );
				$height = imagesy( $img );
				$width = imagesx( $img );
				
				if( ( count( $size ) == 2 and ( $width != $size[0] or $height != $size[1] ) ) or
					( count( $min ) == 2 and ( $width < $min[0] or $height < $min[1] ) ) or
					( count( $max ) == 2 and ( $width > $max[0] or $height > $max[1] ) ) or
					( count( $ratio ) == 2 and ( int( $width / $height * 100 ) == int( $ratio[0] / $ratio[1] * 100 ) ) ) or
					( $convert == "" and $types != "" and strpos( $types, $ext ) === false or $convert != "" and $types != "" and $ext != $convert ) )
				{
					array_push( $value, false );
				}
				else array_push( $value, true );
			}
			else if ( $convert == "" and $types != "" and strpos( $types, $ext ) === false or $convert != "" and $types != "" and $ext == $convert ) array_push( $value, $false );
			else array_push( $value, true );
		}
		
		global $upload_folder, $result;
		
		$result->path = substr( $filePath, strlen( $upload_folder ) );
		$result->list = $value;
		
		$UpCMS->dispatchEvent( new Event( UpCMS::FILEGALLERY_AFTER_VERIFY, $this ) );
	}
}

?>