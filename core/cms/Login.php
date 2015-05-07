<?php

/**
 * Make login-out, and get initScript.
 * @author Orlando Leite
 * @version 0.8
 * @package core
 * @subpackage cms
 * @access public
 * @see User
 * @name ApplicationLogin
 */
class ApplicationLogin
{
	/**
    * Login the Up!CMS and set global $result to success or error. Uses (POST:username) and (POST:password).
    * @author Orlando Leite
    * @access public
    * @static
    * @return void
    */
	public static function login()
	{
		global $UpCMS, $result;
		
		$UpCMS->dispatchEvent( new Event( UpCMS::BEFORE_LOGIN, $this ) );
		
		$result->status = User::login( addslashes( $_POST["username"] ), addslashes( $_POST["password"] ) );
		
		if( $result->status )
		{
			$user = User::instance();
			$result->name	= $user->info( "displayname" );
			$result->group	= $user->info( "group" );
			$result->id		= $user->info( "id" );
		}
		
		$UpCMS->dispatchEvent( new Event( UpCMS::AFTER_LOGIN, $this ) );
	}
	
	/**
    * Logout the Up!CMS and set global $result to success or error.
    * @author Orlando Leite
    * @access public
    * @static
    * @return void
    */
	public static function logout()
	{
		global $UpCMS;
		
		$UpCMS->dispatchEvent( new Event( UpCMS::BEFORE_LOGOUT, $this ) );
		User::logout();
		$UpCMS->dispatchEvent( new Event( UpCMS::AFTER_LOGOUT, $this ) );
	}
	
	/**
    * Get nothing, the initScript should be defined by selected front and improved for objects listening the GET_INIT_SCRIPT event.
    * @author Orlando Leite
    * @access public
    * @static
    * @return void
    */
	public static function getInitScript()
	{
		global $UpCMS;
		
		$UpCMS->dispatchEvent( new Event( UpCMS::GET_INIT_SCRIPT, $this ) );
	}
	
	/**
    * Set global $result for true or false in case of session exists and is logged user.
    * @author Orlando Leite
    * @access public
    * @static
    * @return void
    */
	public static function logged()
	{
		global $UpCMS, $result;
		
		$UpCMS->dispatchEvent( new Event( UpCMS::BEFORE_LOGGED, $this ) );
		
		if( $UpCMS->user )
		{
			$result->status = true;
			$result->name	= $UpCMS->user->info( "displayname" );
			$result->group	= $UpCMS->user->info( "group" );
			$result->id		= $UpCMS->user->info( "id" );
		}
		else
		{
			$result->status = false;
		}
		
		$UpCMS->dispatchEvent( new Event( UpCMS::AFTER_LOGGED, $this ) );
	}
}

?>