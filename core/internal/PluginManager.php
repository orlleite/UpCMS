<?php

include_once( "IPlugin.php" );

/**
 * Manage the plugins.
 * Start what should be started, install and uninstall.
 * @author Orlando Leite
 * @version 0.8
 * @package core
 * @subpackage internal
 * @access public
 * @name PluginManager
 */
class PluginManager
{
	protected static $working = array();
	protected static $unworking = array();
	
	/**
	* Install a plugin
	* @author Orlando Leite
	* @param string $name Plugin name. don't put '.php', only the name. e.g 'MyPlugin'.
    * @access public
    * @return void
    */
	public static function install( $name )
	{
		global $UpCMS, $plugin_folder;
		
		$UpCMS->options->add( strtolower( $name ), "working", $value );
		
		$path = INCLUDE_PATH."/".$plugin_folder."/".$name;
		$path .= is_dir( $path ) ? "/main.php" : ".php";
		
		include_once( $path );
		$temp = new $name();
		$temp->install();
	}
	
	/**
	* Uninstall a plugin
	* @author Orlando Leite
	* @param string $name Plugin name. don't put '.php', only the name. e.g 'MyPlugin'.
    * @access public
    * @return void
    */
	public static function uninstall( $name )
	{
		global $UpCMS, $plugin_folder;
		
		$path = INCLUDE_PATH."/".$plugin_folder.$name;
		
		if( $UpCMS->options->get( strtolower( $name ), "working" ) != NULL )
		{
			$UpCMS->options->remove( strtolower( $name ), "working" );
			
			include_once( $path.( is_dir( $path ) ? "/main.php" : ".php" ) );
			$temp = new $name();
			$temp->uninstall();
			
			if( is_dir( $path ) ) Util::deleteDir( $path );
			else unlink( $path.".php" );
		}
		else
		{
			if( is_dir( $path ) ) Util::deleteDir( $path );
			else unlink( $path.".php" );
		}
	}
	
	/**
	* Set a state of a plugin.
	* If it is not installed and you are command to be working, the plugin will be installed.
	* @author Orlando Leite
	* @param string $name Plugin name. don't put '.php', only the name. e.g 'MyPlugin'.
	* @param string $value the new plugin status. Use "true" or "false".
    * @access public
    * @return void
    */
	public static function setState( $name, $value )
	{
		global $UpCMS;
		
		if( $UpCMS->options->get( strtolower( $name ), "working" ) == NULL and $value == "true" )
		{
			self::install( $name );
		}
		else
		{
			$UpCMS->options->set( strtolower( $name ), "working", $value );
		}
	}
	
	/**
	* Find plugins and create a lista of them.
	* It get all descriptions, versions, authors, names and urls.
	* @author Orlando Leite
	* @access public
    * @return array the list of plugins from the folder plugin.
    */
	public static function alist()
	{
		global $UpCMS, $plugin_folder;
		
		$result = array();
		
		if( $handle = opendir( INCLUDE_PATH."/".$plugin_folder ) )
		{
			while( false !== ( $file = readdir( $handle ) ) )
			{
				$ext = end( explode( ".", $file ) );
				
				if( $file != "." and $file != ".." and is_readable( $plugin_folder."/".$file ) )
				{
					$name = current( explode( ".", $file ) );
					
					if( $name != "" )
					{
						$o = NULL;
						$o->rel = $name;
						$o->file = $file;
						
						$o->working = $UpCMS->options->get( strtolower( $name ), "working" ) == "true" ? true : false;
						
						if( is_dir( $plugin_folder."/".$file ) ) $file .= "/main.php";
						
						$value = file_get_contents( $plugin_folder."/".$file, true );
						$description = strpos( $value, "@description" );
						$version = strpos( $value, "@version" );
						$author = strpos( $value, "@author" );
						$name = strpos( $value, "@name" );
						$url = strpos( $value, "@url" );
						
						if( $name !== false )
						{
							$name += 6;
							$o->name = substr( $value, $name, strpos( $value, "\n", $name ) - $name );
						}
						
						if( $author !== false )
						{
							$author += 8;
							$o->author = substr( $value, $author, strpos( $value, "\n", $author ) - $author );
						}
						
						if( $version !== false )
						{
							$version += 9;
							$o->version = substr( $value, $version, strpos( $value, "\n", $version ) - $version );
						}
						
						if( $url !== false )
						{
							$url += 5;
							$o->url = substr( $value, $url, strpos( $value, "\n", $url ) - $url );
						}
						
						if( $description !== false )
						{
							$description += 13;
							$description = substr( $value, $description, strpos( $value, "\n", $description ) - $description );
							$o->description = $description;
							
							if( strpos( $description, "\$Language->" ) === 0 )
							{
								$description = substr( $description, 11 );
								$o->description = $Language->$description;
							}
						}
						
						array_push( $result, $o );
					}
				}
			}
			
			closedir( $handle );
		}
		
		return $result;
	}
	
	/**
	* Start plugins who should started.
	* @author Orlando Leite
	* @access public
    * @return void
    */
	public static function start()
	{
		global $UpCMS, $plugin_folder;
		
		// FIND NEW PLUGINS AND START THE OLDS //
		if( $handle = opendir( INCLUDE_PATH."/".$plugin_folder ) )
		{
			while( false !== ( $file = readdir( $handle ) ) )
			{
				$ext = end( explode( ".", $file ) );
				
				if( $file != "." and $file != ".." and is_readable( $plugin_folder."/".$file ) )
				{
					$name = current( explode( ".", $file ) );
					
					if( $name != "" )
					{
						if( $UpCMS->options->get( strtolower( $name ), "working" ) == "true" )
						{
							if( is_dir( $plugin_folder."/".$file ) ) $file .= "/main.php";
							
							include_once( $plugin_folder."/".$file );
							$temp = new $name();
							$temp->start();
							
							array_push( self::$working, $temp );
						}
						else
						{
							array_push( self::$unworking, $name );
						}
					}
				}
			}
			
			closedir( $handle );
		}
	}
}

?>