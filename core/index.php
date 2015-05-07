<?php

/**
 * Start the Up!CMS environment.
 * - define INCLUDE_PATH;
 * - include config.php and config.xml;
 * - Create the central object $UpCMS;
 * 
 * @author Orlando Leite
 * @version 0.8
 * @package core
 * @access public
 * @name Up!CMS Environment
 */

/**
 * Define Up!CMS Version
 */
define( 'UP_APP_VERSION', '0.8.6.3' );

// Define o timezone local
date_default_timezone_set('America/Halifax');

if ( get_magic_quotes_gpc() )
{
	function stripslashes_recursive( $var ) { return ( is_array( $var ) ? array_map( 'stripslashes_recursive', $var ) : stripslashes( $var ) ); }
	
	$_GET = stripslashes_recursive( $_GET );
	$_POST = stripslashes_recursive( $_POST );
	$_COOKIE = stripslashes_recursive( $_COOKIE );
}

set_include_path( substr( __FILE__, 0, strlen( __FILE__ ) - strlen( 'core/index.php' ) ) );

/**
 * Define correct INCLUDE_PATH
 */
define( 'INCLUDE_PATH', get_include_path() );

/**
 * Should not use trans_sid
 * And use only cookies
 */
ini_set( 'session.use_trans_sid', '0' );
ini_set( 'session.use_only_cookies', '1' );

/**
 * Include config.php.
 * It's will define if are in debug mode or not.
 */
include_once( INCLUDE_PATH.'config.php' );

if( $debugging )
{
	error_reporting( E_ALL & ~E_NOTICE );
	ini_set( 'display_errors', '1' );
/**
 * config.php define this definition
 */
	define( "DEBUGGING", true );
}
else
{
	ini_set( 'display_errors', '0' );
/**
 * config.php define this definition
 */
	define( 'DEBUGGING', false );
	error_reporting( 0 );
}

/**
 * Include UpCMS and fill the properties such as options, user, etc...
 * UpCMS includes Event and EventDispatcher
 */
include_once( 'internal/UpCMS.php' );
$UpCMS = UpCMS::instance();

/**
 * Include Options
 */
include_once( 'internal/Options.php' );

/**
 * Include User
 */
include_once( 'internal/User.php' );

/**
 * Include DB
 */
include_once( 'db/index.php' );

$UpCMS->options = new Options();
$UpCMS->config = simplexml_load_string( file_get_contents( 'config.xml', true ) );

/**
 * define UP_FRONT_NAME based in the option upcms->front
 */
define( 'UP_FRONT_NAME', $UpCMS->options->get( 'upcms', 'front' ) );

/**
 * define UP_APP_NAME based in the option upcms->app_name
 */
define( 'UP_APP_NAME', $UpCMS->options->get( 'upcms', 'app_name' ) );

/**
 * define UP_APP_URL based in the option upcms->app_url
 */
define( 'UP_APP_URL', $UpCMS->options->get( 'upcms', 'app_url' ) );

/**
 * define UP_FRONT_FOLDER based in UP_FRONT_NAME
 */
define( 'UP_FRONT_FOLDER', 'fronts/'.UP_FRONT_NAME.'/' );

/**
 * include languages
 */
include_once( 'languages/index.php' );

/**
 * include Util
 */
include_once( 'internal/Util.php' );

/**
 * include Html
 */
include_once( 'internal/Html.php' );
$UpCMS->html = new Html();

/**
 * include PluginManager and starts
 */
include_once( 'internal/PluginManager.php' );

// PLUGINS //
PluginManager::start();

$UpCMS->user = User::instance();
if( $UpCMS->user )
{
	if( $UpCMS->user->application( 'settings' ) )
	{
		$UpCMS->settings['general'] = new stdClass();
		$UpCMS->settings['general']->get = 'core/internal/settings/Get.general.php';
		$UpCMS->settings['general']->set = 'core/internal/settings/Set.general.php';
		
		$UpCMS->settings['plugins'] = new stdClass();
		$UpCMS->settings['plugins']->get = 'core/internal/settings/Get.plugins.php';
		$UpCMS->settings['plugins']->set = 'core/internal/settings/Set.plugins.php';
	}
	
	/**
	 * If have a logged user, then include Menu
	 */
	include_once( 'internal/Menu.php' );
	$UpCMS->menu = Menu::get();
}

/**
 * This is a deprecated function used mainly by Up!Front.
 * It's change \" for \\\"
 */
function addslashes2( $string )
{
	return str_replace( "\"", "\\\"", $string );
}

/**
 * Include config of the Front
 */
include_once( UP_FRONT_FOLDER."config.php" );

/**
 * YOU CAN START UP!CMS CALLING THIS FUNCTION.
 * If you only want the environment only include this file.
 */
function __upcms()
{
	global $UpCMS, $Language;
	
	$t = urldecode( $_SERVER['REQUEST_URI'] );
	
	// GET Class::Method //
	$n = explode( "?", $t, 2 );
	$CURRENT_CALL = strpos( $t, "?" ) === false ? "" : end( $n );
	
	$format = "index";
	
	$t = explode( "::", $CURRENT_CALL, 2 );
	$CLASS = $t[0];
	$n = isset( $t[1] ) ? $t[1] : "";
	$n = explode( "&", $n );
	$METHOD = reset( $n );
	
	if( $CLASS )
	{
		include_once( "cms/".$CLASS.".php" );
		
		// Call ApplicationClass //
		eval( "Application".$CLASS."::".$METHOD."();" );
		$format = strtolower( $CLASS );
	}
	
	if( is_file( "./fronts/".UP_FRONT_NAME."/".$format.".php" ) )
	{
		// Call ViewClass //
		if( $format == "index" )
		{
			$UpCMS->dispatchEvent( new Event( UpCMS::BEFORE_FIRST_PAGE, NULL ) );
			include_once( "./fronts/".UP_FRONT_NAME."/".$format.".php" );
			$UpCMS->dispatchEvent( new Event( UpCMS::AFTER_FIRST_PAGE, NULL ) );
		}
		else
		{
			include_once( "./fronts/".UP_FRONT_NAME."/".$format.".php" );
		}
	}
	
	if( $CLASS )
	{
		eval( "View".$CLASS."::".$METHOD."();" );
	}
	
	$UpCMS->db->close();
}

?>