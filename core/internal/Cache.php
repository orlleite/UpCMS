<?php

/**
 * It's a usefull class to make cache of anything. You call the better method of your application and the return wil be cached value. The update is made automatically.
 * @author Orlando Leite
 * @version 0.8
 * @package core
 * @subpackage internal
 * @access public
 * @name Cache
 */
class Cache
{
	/**
    * Make a url cache. If you call much times some URL, use this function to make a cache of.
    * @author Orlando Leite
    * @access public
    * @param string $url the url to be returned.
    * @param integer $cache_time the maximum cache time. If you leave it NULL, the default_cache_time will be used.
    * @static
    * @return string
    */
	public static function getURL( $url, $cache_time )
	{
		global $UpCMS, $upload_folder;
		
		$path = INCLUDE_PATH."/".$upload_folder."/_cache_url";
		
		if( !is_dir( $path ) )
		{
			mkdir( $path, 0777 );
			chmod( $path, 0777 );
		}
		
		$path .= "/".Cache::parseRequest( $url );
		
		if( !is_file( $path ) || time() - filemtime( $path ) > ( $cache_time != NULL ? $cache_time : (int) $UpCMS->options->get( "upcms", "default_cache_time" ) ) )
		{
			return Cache::updateURL( $url );
		}
		
		return file_get_contents( $path );
	}
	
	/**
    * Update a URL and make cache of it. If you want to force recache, call it.
    * @author Orlando Leite
    * @access public
    * @param string $url the url to be returned.
    * @static
    * @return string
    */
	public static function updateURL( $url )
	{
		global $UpCMS, $upload_folder;
		
		$path = INCLUDE_PATH."/".$upload_folder."/_cache_url";
		
		if( !is_dir( $path ) )
		{
			mkdir( $path, 0777 );
			chmod( $path, 0777 );
		}
		
		$path .= "/".Cache::parseRequest( $url );
		
		$content = file_get_contents( $url );
		
		if( is_file( $path ) ) unlink( $path );
		
		$fp = fopen( $path, "w" );
		fputs( $fp, $content );
		fclose( $fp );
		
		return $content;
	}
	
	/**
    * Make a function cache. If a function is called much time or harded code, use this function to make a cache of. e.g. 'MyClass::myStaticFunction'
    * @author Orlando Leite
    * @access public
    * @param string $eval function to be called.
    * @param integer $cache_time the maximum cache time. If you leave it NULL, the default_cache_time will be used.
    * @static
    * @return string returning value of the call.
    */
	public static function getEvalReturn( $eval, $cache_time )
	{
		global $UpCMS, $upload_folder;
		
		$path = INCLUDE_PATH."/".$upload_folder."/_cache_eval";
		
		if( !is_dir( $path ) )
		{
			mkdir( $path, 0777 );
			chmod( $path, 0777 );
		}
		
		$path .= "/".Cache::parseRequest( $eval );
		
		if( !is_file( $path ) || time() - filemtime( $path ) > ( $cache_time != NULL ? $cache_time : (int) $UpCMS->options->get( "upcms", "default_cache_time" ) ) )
		{
			return Cache::updateEvalReturn( $eval );
		}
		
		return file_get_contents( $path );
	}
	
	/**
    * Update the function return cache. If you want to force recache, call it.
    * @author Orlando Leite
    * @access public
    * @param string $eval function to be called.
    * @static
    * @return string returning value of the call.
    */
	public static function updateEvalReturn( $eval )
	{
		global $UpCMS, $upload_folder;
		
		$path = INCLUDE_PATH."/".$upload_folder."/_cache_eval";
		
		if( !is_dir( $path ) )
		{
			mkdir( $path, 0777 );
			chmod( $path, 0777 );
		}
		
		$path .= "/".Cache::parseRequest( $eval );
		
		$content = $eval();
		
		if( is_file( $path ) ) unlink( $path );
		
		$fp = fopen( $path, "w" );
		fputs( $fp, $content );
		fclose( $fp );
		
		return $content;
	}
	
	/**
    * Create a path for the request. e.g. parsing 'http://upcms.net' the file name will bew 'http...upcms.net'
    * @author Orlando Leite
    * @access protected
    * @param string $r value to create a correctly path
    * @static
    * @return string a correctly path.
    */
	protected static function parseRequest( $r )
	{
		return str_replace( array("/", "\\", ":", "?", "*", "\"", "<", ">", "|"), ".", (string) $r );
	}
}

?>