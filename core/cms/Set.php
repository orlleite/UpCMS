<?php

/**
 * Set and get options pages. The settings menu uses for general, front and plugins, but can have others pages.
 * @author Orlando Leite
 * @version 0.8
 * @package core
 * @subpackage cms
 * @access public
 * @name ApplicationSet
 */
class ApplicationSet
{
	/**
    * Fill global $result with fields from (POST:rel).
    * @author Orlando Leite
    * @access public
    * @static
    * @return void
    */
	public static function get()
	{
		global $UpCMS, $result;
		
		$UpCMS->dispatchEvent( new Event( UpCMS::SET_BEFORE_GET, NULL ) );
		
		$rel = @$_POST["rel"];
		
		$list = NULL;
		include_once( $UpCMS->settings[$rel]->get );
		
		$result->list = $list;
		
		$UpCMS->dispatchEvent( new Event( UpCMS::SET_AFTER_GET, NULL ) );
	}
	
	/**
    * Set values and global $result for success or error.
    * @author Orlando Leite
    * @access public
    * @static
    * @return void
    */
	public static function set()
	{
		global $UpCMS, $result;
		
		$UpCMS->dispatchEvent( new Event( UpCMS::SET_BEFORE_SET, NULL ) );
		
		$rel = @$_POST["rel"];
		
		include_once( $UpCMS->settings[$rel]->set );
		
		$UpCMS->dispatchEvent( new Event( UpCMS::SET_AFTER_SET, NULL ) );
	}
}

?>