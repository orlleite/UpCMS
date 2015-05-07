<?php

/**
 * Set Up!CMS Front settings
 * @author Orlando Leite
 * @version 0.8
 * @package front
 * @subpackage settings
 * @access public
 */
global $UpCMS;

// Animations //
$UpCMS->options->set( "upfront", "animation_level", @$_POST["front_front_animations"] );

// Minimize box //
$UpCMS->options->set( "upfront", "minimize_box", @$_POST["front_front_minimize"] == "true" ? "true" : "false" );

// Quickedit //
$UpCMS->options->set( "upfront", "quickedit", @$_POST["front_front_quickedit"] == "true" ? "true" : "false" );

// List thumb size //
$UpCMS->options->set( "upfront", "list_thumb_size", @$_POST["front_front_thumbsize"] );

// Auto show table content //
$UpCMS->options->set( "upfront", "auto_show_table_content", @$_POST["front_front_tablecontent"] == "true" ? "true" : "false" );

// Multiple Adding //
$UpCMS->options->set( "upfront", "multiple_adding", @$_POST["front_front_multipleadding"] == "true" ? "true" : "false" );

// Show UP!CMS Version //
$UpCMS->options->set( "upfront", "show_up_version", @$_POST["front_front_showversion"] == "true" ? "true" : "false" );

$result->setter = true;
$result->refresh = true;

?>