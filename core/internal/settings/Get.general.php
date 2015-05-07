<?php

/**
 * Get General settings
 * @author Orlando Leite
 * @version 0.8
 * @package core
 * @subpackage internal
 * @access protected
 * @see UpCMS->settings
 * @name Get general
 */

global $Language, $UpCMS, $debugging;

$list->name = $Language->generalSettings;

// APP //
$n = NULL;
$n->type = "simpletext";
$n->name = $Language->appTitle;
$n->value = $UpCMS->options->get( "upcms", "app_name" );
$list->groups->app->fields->title = $n;

$n = NULL;
$n->type = "simpletext";
$n->name = $Language->appURL;
$n->about = $Language->aboutAppURL;
$n->value = $UpCMS->options->get( "upcms", "app_url" );
$list->groups->app->fields->url = $n;

$list->groups->app->name = $Language->app;

// INTERFACE //

// Language //
$n = NULL;
$n->name = $Language->language;
$n->type = "select";
$n->about = $Language->aboutLanguage;
$n->value = str_replace( "-", "_", $UpCMS->options->get( "upcms", "language" ) );
$n->options = NULL;
$folders = Util::listfiles( "core/languages", "!index.php;.svn;" );

foreach( $folders as $f )
{
	$o = NULL;
	$value = file_get_contents( $f->path, true );
	$name = strpos( $value, "@name" );
	
	if( $name !== false )
	{
		$name += 6;
		$o = substr( $value, $name, strpos( $value, "\n", $name ) - $name );
	}
	
	$n->options[str_replace( "-", "_", reset( explode( ".", $f->name ) ) )] = $o;
}

$list->groups->interface->fields->language = $n;

// Items per page //
$n = NULL;
$n->type = "number";
$n->name = $Language->itemsPerPage;
$n->about = $Language->aboutItemsPerPage;
$n->value = $UpCMS->options->get( "upcms", "list_limit" );
$list->groups->interface->fields->listlimit = $n;

// Front //
$n = NULL;
$n->name = $Language->selectFront;
$n->type = "select-info";
$n->value = $UpCMS->options->get( "upcms", "front" );
$n->options = NULL;
$folders = Util::listfiles( "fronts", "!.svn;" );

foreach( $folders as $f )
{
	$o = NULL;
	$value = file_get_contents( $f->path."/author.about", true );
	$author = strpos( $value, "@author" );
	$about = strpos( $value, "@about" );
	$name = strpos( $value, "@name" );
	
	$o->about = "";
	
	if( $name !== false )
	{
		$name += 6;
		$o->name = substr( $value, $name, strpos( $value, "\n", $name ) - $name );
	}
	
	if( $author !== false )
	{
		$author += 8;
		$o->about = "<b>".substr( $value, $author, strpos( $value, "\n", $author ) - $author )."</b><br />";
	}
	
	if( $about !== false )
	{
		$about += 7;
		$about = substr( $value, $about, strpos( $value, "\n", $about ) - $about );
		$o->about = $about;
		
		if( strpos( $about, "\$Language->" ) === 0 )
		{
			$about = substr( $about, 11 );
			$o->about = $Language->$about;
		}
	}
	
	$n->options[$f->name] = $o;
}

$list->groups->interface->name = $Language->interface;
$list->groups->interface->fields->front = $n;


// SYSTEM //

// debugger //
$n = NULL;
$n->type = "onoff";
$n->name = $Language->debugger;
$n->value = $debugging ? "on" : "off";
$n->options->on = $Language->debuggerOn;
$n->options->off = $Language->debuggerOff;

$list->groups->system->name = $Language->system;
$list->groups->system->fields->debugger = $n;

// Cache //
$n = NULL;
$n->type = "number";
$n->name = $Language->defaultCacheTime;
$n->about = $Language->aboutDefaultCacheTime;
$n->value = $UpCMS->options->get( "upcms", "default_cache_time" );
$list->groups->system->fields->cache = $n;

?>