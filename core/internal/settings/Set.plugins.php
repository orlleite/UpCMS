<?php

/**
 * Set plugins settings
 * @author Orlando Leite
 * @version 0.8
 * @package core
 * @subpackage internal
 * @access protected
 * @see UpCMS->settings
 * @name Set plugins
 */

include_once( "Get.plugins.php" );

$list->groups->plugins->name = "";
$list->groups->plugins->list = $n;

$rows = $list->groups->plugins->list->rows;
$columns = $list->groups->plugins->list->columns;

global $result;

foreach( $rows as $r => $row )
{
	$activate = $_REQUEST["plugins_plugins_".$r."_activate"];
	if( $activate != NULL )
	{
		PluginManager::setState( $r, $activate );
		$result->setter = true;
	}
	
	$delete = $_REQUEST["plugins_plugins_".$r."_delete"];
	if( $delete == "true" )
	{
		PluginManager::uninstall( $r );
		$result->status = true;
		$result->setter = true;
	}
}

?>
