<?php

/**
 * Set General settings
 * @author Orlando Leite
 * @version 0.8
 * @package core
 * @subpackage internal
 * @access protected
 * @see UpCMS->settings
 * @name Set general
 */

global $Language, $UpCMS, $debugging;

// APP //
$UpCMS->options->set( "upcms", "app_name", @$_POST["general_app_title"] );
$n->value = $UpCMS->options->set( "upcms", "app_url", @$_POST["general_app_url"] );

// INTERFACE //
$newlanguage = str_replace( "_", "-", @$_POST["general_interface_language"] );
if( $UpCMS->options->get( "upcms", "language" ) != $newlanguage )
{
	$UpCMS->options->set( "upcms", "language", $newlanguage );
	$result->refresh = true;
}

$UpCMS->options->set( "upcms", "list_limit", @$_POST["general_interface_listlimit"] );

if( $UpCMS->options->get( "upcms", "front" ) != @$_POST["general_interface_front"] )
{
	$UpCMS->options->set( "upcms", "front", @$_POST["general_interface_front"] );
	$result->refresh = true;
}

// SYSTEM //
$UpCMS->options->set( "upcms", "default_cache_time", @$_POST["general_system_cache"] );
$newdebug = @$_POST["general_system_debugger"] == "true" ? true : false;

if( $newdebug != $debugging )
{
	$debugging = $newdebug;
	
	if( !Util::createConfigPhp() )
	{
		$result->setter = false;
		$result->error = $Language->createConfigPhpError;
	}
	else $result->setter = true;
}
else $result->setter = true;

?>