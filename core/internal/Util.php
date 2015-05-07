<?php

/**
 * A collection of usefull functions.
 * @author Orlando Leite
 * @version 0.8
 * @package core
 * @subpackage internal
 * @access public
 * @name Util
 */
class Util
{
	/**
	* Make a new path based in parsed path, using as reference files with the same name gave.
	* If there is a file './folder/my_file.jpg' and I parse this same path, the return will be './folder/my_file_1.jpg', and it again, the return will be './folder/my_file_2.jpg'.
	* But, if after that example you parse the path './folder/my_file_1.jpg', the return will be a little different './folder/my_file_1_1.jpg'
	* @access public
	* @param string $path should tested.
	* @static
	* @return string new path.
	*/
	public static function nonOverwritePath( $path )
	{
		$i = 1;
		
		$t = substr( $path, count( $path ) - 2 );
		
		if( $t == "/" or $t == "\\" ) $path = substr( $path, 0, count( $path ) - 2 );
		
		$t = explode( "/", $path );
		array_pop( $t );
		$p = join( "/", $t );
		
		$filename = "/".basename( $path );
		$curname = $filename;
		
		$t = explode( ".", $filename );
		$ext = count( $t ) > 1 ? array_pop( $t ) : "";
		$filename = join( ".", $t );
		
		while( is_file( $p.$curname ) or is_dir( $p.$curname ) )
		{
			$curname = $filename."_".$i;
			$curname .= ".".$ext;
			$i++;
		}
		
		return $p.$curname;
	}
	
	/**
	* Prepares the string to be online. In other words, remove special chars like accents, cedil and spaces.
	* @access public
	* @param string $string string to be tested.
	* @static
	* @return string new string.
	*/
	public static function presetOnlineURL( $string )
	{
		$a = array(
		"/[ÂÀÁÄÃ]/"=>"A",
		"/[âãàáä]/"=>"a",
		"/[ÊÈÉË]/"=>"E",
		"/[êèéë]/"=>"e",
		"/[ÎÍÌÏ]/"=>"I",
		"/[îíìï]/"=>"i",
		"/[ÔÕÒÓÖ]/"=>"O",
		"/[ôõòóö]/"=>"o",
		"/[ÛÙÚÜ]/"=>"U",
		"/[ûúùü]/"=>"u",
		"/ç/"=>"c",
		"/Ç/"=> "C",
		"/ /"=> "_");
		// Remove accents //
		return preg_replace( array_keys( $a ), array_values( $a ), $string );
	}
	
	/**
	* List files with gave extensions types.
	* e.g. ('/my_path/', 'jpg;jpeg;png;gif').
	* @access public
	* @param string $path folder path.
	* @param string $exts permitted extensions.
	* @static
	* @return array list of files (name, path, type). The type is means dir for folder and file.
	*/
	public static function listfiles( $path, $exts )
	{
		global $upload_folder;
		
		$result = array();
		$substr = strpos( $path, $upload_folder ) === 0 ? true : false;
		
		if( !is_dir( $path ) ) return NULL;
		
		if( $handle = opendir( $path ) )
		{
			if( $exts[0] == "!" )
			{
				while( false !== ( $file = readdir( $handle ) ) )
				{
					$ext = end( explode( ".", $file ) );
					
					if( $file != "." and $file != ".." and ( $exts == ";" or strpos( $exts, $ext ) === false or strpos( $exts, $file ) === false ) )
					{
						$t = NULL;
						$t->name = $file;
						$t->path = ( $substr ? substr( $path, strlen( $upload_folder ) ) : $path )."/".$file;
						$t->type = is_dir( $path."/".$file ) ? "dir" : "file";
						array_push( $result, $t );
					}
				}
			}
			else
			{
				while( false !== ( $file = readdir( $handle ) ) )
				{
					$ext = end( explode( ".", $file ) );
					
					if( $file != "." and $file != ".." and ( $exts == ";" or strpos( $exts, $ext ) !== false or strpos( $exts, $file ) !== false ) )
					{
						$t = NULL;
						$t->name = $file;
						$t->path = ( $substr ? substr( $path, strlen( $upload_folder ) ) : $path )."/".$file;
						$t->type = is_dir( $path."/".$file ) ? "dir" : "file";
						array_push( $result, $t );
					}
				}
			}
			
			closedir( $handle );
		}
		
		return $result;
	}
	
	/**
	* Move files. Files uploaded exactly.
	* @access public
	* @param string $target file path.
	* @param string $rel table relation.
	* @param string $id value id.
	* $param string $type can bem 'html' or 'text'.
	* @static
	* @return string new name.
	*/
	public static function movefiles( $target, $rel, $id, $type )
	{
		global $upload_folder;
		
		$origin = $temp = $target;
		$folder = $basepath = $upload_folder.$rel."/";
		
		if( !is_dir( $folder ) )
		{
			mkdir( $folder, 0777 );
			chmod( $folder, 0777 );
		}
		
		$folder .= $id;
		
		if( !is_dir( $folder ) )
		{
			mkdir( $folder, 0777 );
			chmod( $folder, 0777 );
		}
		
		if( empty( $type ) or $type == "text" )
		{
			if( strpos( $origin, "__acd372841289b14dade72301f2b57ba64c8506ed__" ) )
			{
				$name = end( explode( "/", $temp ) );
				$old = $basepath."__acd372841289b14dade72301f2b57ba64c8506ed__"."/".$name;
				$new = $basepath.$id."/".$name;
				
				if( rename( "./".$old, "./".$new ) ) $origin = substr( $new, strlen( $upload_folder ) );
			}
		}
		else if( $type == "html" )
		{
			while( strpos( $origin, "__acd372841289b14dade72301f2b57ba64c8506ed__" ) )
			{
				if( $i = strpos( $temp, "src=" ) )
				{
					if( strpos( $t = substr( $temp, $i + 5, strpos( $temp, substr( $temp, $i + 4, 1 ), $i + 5 ) - ( $i + 5 ) ), "__acd372841289b14dade72301f2b57ba64c8506ed__" ) )
					{
						$name = end( explode( "/", $t ) );
						$old = $basepath."__acd372841289b14dade72301f2b57ba64c8506ed__"."/".$name;
						$new = $basepath.$id."/".$name;
						
						if( rename( "./".$old, "./".$new ) ) $origin = str_replace( $old, $new, $origin );
					}
					
					$count = 1;
					$temp = substr_replace( $temp, "#$%&", $i, 4 );
				}
				else if( $i = strpos( $temp, "href=" ) )
				{
					if( strpos( $t = substr( $temp, $i + 6, strpos( $temp, substr( $temp, $i + 5, 1 ), $i + 6 ) - ( $i + 6 ) ), "__acd372841289b14dade72301f2b57ba64c8506ed__" ) )
					{
						$name = end( explode( "/", $t ) );
						$old = $basepath."__acd372841289b14dade72301f2b57ba64c8506ed__"."/".$name;
						$new = $basepath.$id."/".$name;
						
						if( rename( "./".$old, "./".$new ) ) $origin = str_replace( $old, $new, $origin );
					}
					
					$count = 1;
					$temp = substr_replace( $temp, "#$%&*", $i, 4 );
				}
				else break;
			}
		}
		
		return $origin;
	}
	
	/**
	* Loads an image based in the extension given.
	* @access public
	* @param string $source file path.
	* @param string $ext file extension.
	* @static
	* @return resource image instance.
	*/
	public static function loadImage( $source, $ext )
	{
		$ext = strtolower( $ext );
		
		ini_set( 'memory_limit', '128M' );
		
		
		switch( $ext )
		{
			case "jpg":
			case "jpeg":
				return imagecreatefromjpeg( $source );
				break;
			
			case "png":
				return imagecreatefrompng( $source );
				break;
			
			case "gif":
				return imagecreatefromgif( $source );
				break;
			
			case "bmp":
			case "wbmp":
				return imagecreatefromwbmp( $source );
				break;
			
			default:
				return false;
		}
	}
	
	/**
	* Save an image.
	* @access public
	* @param string $image image instance.
	* @param string $dest file destination.
	* @param string $format Can be 'jpg', 'png', 'aif' and 'bmp'.
	* @static
	* @return resource image instance.
	*/
	public static function saveImage( $image, $dest, $format )
	{
		$format = strtolower( $format );
		
		switch( $format )
		{
			case "jpg":
			case "jpeg":
				return imagejpeg( $image, $dest, 100 );
				break;
			
			case "png":
				return imagepng( $image, $dest );
				break;
			
			case "gif":
				return imagegif( $image, $dest );
				break;
			
			case "bmp":
			case "wbmp":
				return imagewbmp( $image, $dest );
				break;
			
			default:
				return false;
		}
	}
	
	/**
	* Crop an image.
	* @access public
	* @param string $image image instance.
	* @param string $x position x in instance image for crop.
	* @param string $y position y in instance image for crop.
	* @param string $w width of the crop.
	* @param string $h height of the crop.
	* @static
	* @return resource new image instance.
	*/
	public static function crop( $image, $x, $y, $w, $h )
	{
		$cmemory = ini_get( 'memory_limit' );
		$nmemory = ceil( ( $w * $h * 9.4 + 8388608 ) / 4194304 ) * 4;
		ini_set( 'memory_limit', $nmemory.'M' );
		
		$temp = imagecreatetruecolor( $w, $h );
		imagecopyresampled( $temp, $image, 0, 0, $x, $y, $w, $h, $w, $h );
		
		return $temp;
	}
	
	/**
	* Resize an image.
	* @access public
	* @param string $image image instance.
	* @param string $w width of the crop.
	* @param string $h height of the crop.
	* @static
	* @return string new image instance.
	*/
	public static function resize( $image, $w, $h )
	{
		$cmemory = ini_get( 'memory_limit' );
		$nmemory = ceil( ( $w * $h * 9.4 + 8388608 ) / 4194304 ) * 4;
		ini_set( 'memory_limit', $nmemory.'M' );
		
		$temp = imagecreatetruecolor( $w, $h );
		imagecopyresampled( $temp, $image, 0, 0, 0, 0, $w, $h, imagesx( $image ), imagesy( $image ) );
		
		return $temp;
	}
	
	/**
	* Decompress a zip file.
	* Becarefull It's overwrite anyfile.
	* @access public
	* @param string $target file path of a zip.
	* @param string $destiny folder the zip should be decompressed.
	* @static
	* @return boolean|array false for error and array of file names for success.
	*/
	public static function decompressZip( $target, $destiny )
	{
		global $upload_folder;
		
		$files = array();
		$file = new ZipArchive();
		if( $file->open( $target ) === true )
		{
			for ( $i = 0; $i < $file->numFiles; $i++ )
			{
				$t = $file->statIndex( $i );
				$t["path"] = substr( $destiny."/".$t["name"], strlen( $upload_folder ) );
				
				array_push( $files, $t );
			}
			
			$extract = $file->extractTo( $destiny );
			$file->close();
			
			return $extract ? $files : false;
		}
		else return false;
	}
	
	/**
	* Create a config.php file. Based on currently state of vars.
	* If you want change the $debugging var, for example, set the var and then call this function.
	* @access public
	* @static
	* @return boolean for success or not.
	*/
	public static function createConfigPhp()
	{
		function func()
		{
			global $debugging, $db_type, $db_prefix, $up_prefix, $db_hostname, $db_username, $db_password, $db_database, $upload_folder, $plugin_folder;
			
			$t = @fopen( "config.php", "wb", true );
			if( !$t ) return false;
			$done = fwrite( $t, "<?php

\$debugging		= ".( $debugging ? "true" : "false" ).";
\$db_type		= \"".$db_type."\";
\$db_prefix		= \"".$db_prefix."\";
\$up_prefix		= \"".$up_prefix."\";
\$db_hostname	= \"".$db_hostname."\";
\$db_username	= \"".$db_username."\";
\$db_password	= \"".$db_password."\";
\$db_database	= \"".$db_database."\";
\$upload_folder	= \"".$upload_folder."\";
\$plugin_folder	= \"".$plugin_folder."\";

?>" );
			fclose( $t );
			
			return $done;
		}
		
		if( !func() )
		{
			if( @chmod( "config.php", 0777 ) )
			{
				return func();
			}
			else return false;
		}
		else return true;
	}
	
	/**
	* Delete, recursively, a directory.
	* @param string $dir path of the directory you want to delete.
	* @access public
	* @static
	* @return void.
	*/
	public static function deleteDir( $dir )
	{
		if ( is_dir( $dir ) )
		{
			$objects = scandir( $dir );
			foreach ( $objects as $object )
			{
				if ( $object != "." && $object != ".." )
				{
					if( filetype( $dir."/".$object ) == "dir" ) self::deleteDir( $dir."/".$object ); else unlink( $dir."/".$object );
				}
			}
			
			reset( $objects );
			rmdir( $dir );
		}
	}
	
	/**
	* chmod, recursively, a directory.
	* @param string $dir path of the directory you want to change mode.
	* @access public
	* @static
	* @return void.
	*/
	public static function chmodDir( $dir, $value )
	{
		if ( is_dir( $dir ) )
		{
			$objects = scandir( $dir );
			foreach ( $objects as $object )
			{
				if ( $object != "." && $object != ".." )
				{
					if( filetype( $dir."/".$object ) == "dir" ) self::chmodDir( $dir."/".$object ); else chmod( $dir."/".$object, $value );
				}
			}
			
			reset( $objects );
			chmod( $dir, $value );
		}
	}
	
	/**
	* Set tables based in the currently config.xml.
	* Table contents will not be erased. But if you let remove, the content of columns eliminated will be gone.
	* @param boolean $edit It change the type if there is a column in the table with the same name but the type of values is different.
	* @param boolean $remove It remove a if there is columns in the table that is not listed in config.xml.
	* @access public
	* @static
	* @return void.
	*/
	public static function setTables( $edit = false, $remove = false )
	{
		include_once( "AdminTool.setTables.php" );
	}
	
	/**
	* Make a MySQL backup
	* @access public
	* @static
	* @return string sql dump db.
	*/
	public static function mysqlBackup()
	{
		global $UpCMS, $db_database;
		
		$backup = "";
		
		$tables = $UpCMS->db->select( "SHOW TABLES FROM ".$db_database, DB_NUM );
		foreach( $tables as $row )
		{
			$table = $row[0];
			$create = $UpCMS->db->select( "SHOW CREATE TABLE ".$table, DB_NUM );
			
			foreach( $create as $lin )
			{
				$backup .= "-- Criando tabela : ".$table."\n";
				$backup .= $lin[1]."\n--Dump de Dados\n";
				
				$items = $UpCMS->db->select( "SELECT * FROM ".$table, DB_NUM );
				foreach( $items as $r )
				{
					$sql="INSERT INTO ".$table." VALUES ('";
					$sql .= implode( "','", $r );
					$sql .= "')\n";
					$backup .= $sql;
				}
			} 
		}
		
		return $backup;
	}
}

?>