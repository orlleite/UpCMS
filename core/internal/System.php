<?php

/**
 * Adds group and user tables for $UpCMS->config.
 * included when users are managing this kind of content.
 * @author Orlando Leite
 * @version 0.8
 * @package core
 * @subpackage internal
 * @access public
 * @name System
 */

// GROUPS TABLE //
$groups = $UpCMS->config->addChild( "table" );
$groups->addAttribute( "name", $Language->groups );
$groups->addAttribute( "rel", "system_groups" );
$groups->addAttribute( "permission", "strict" );
$groups->addAttribute( "reltable", "groups" );
$groups->addAttribute( "icon", "users" );

// groupname field
$field = $groups->addChild( "field" );
$field->addAttribute( "name", $Language->groupname );
$field->addAttribute( "rel", "name" );
$field->addAttribute( "type", "simpletext" );
$field->addAttribute( "quickedit", "enabled" );

// anyread switcher field
$field = $groups->addChild( "field" );
$field->addAttribute( "name", $Language->anyread );
$field->addAttribute( "rel", "switch_anyread" );
$field->addAttribute( "type", "switch" );

$option = $field->addChild( "option" );
$option->addAttribute( "name", strtolower( $Language->ptotal ) );
$option->addAttribute( "value", "all" );

$option = $field->addChild( "option" );
$option->addAttribute( "name", strtolower( $Language->pcustom ) );
$option->addAttribute( "value", "custom" );

// anyread group custom
$group = $groups->addChild( "group" );
$group->addAttribute( "name", "anyread_custom" );
$group->addAttribute( "rel", "switch_anyread" );
$group->addAttribute( "value", "custom" );

$anyread = $group->addChild( "field" );
$anyread->addAttribute( "name", $Language->selectTable );
$anyread->addAttribute( "rel", "anyread" );
$anyread->addAttribute( "type", "options" );
$anyread->addAttribute( "params", "value='string'" );

// anyread group all
$group = $groups->addChild( "group" );
$group->addAttribute( "name", "anyread_all" );
$group->addAttribute( "rel", "switch_anyread" );
$group->addAttribute( "value", "all" );

$field = $group->addChild( "field" );
$field->addAttribute( "display", "false" );
$field->addAttribute( "name", "linkage" );
$field->addAttribute( "rel", "anyread" );
$field->addAttribute( "value", "#all#" );


// ownwrite switcher field
$field = $groups->addChild( "field" );
$field->addAttribute( "name", $Language->ownwrite );
$field->addAttribute( "rel", "switch_ownwrite" );
$field->addAttribute( "type", "switch" );

$option = $field->addChild( "option" );
$option->addAttribute( "name", strtolower( $Language->ptotal ) );
$option->addAttribute( "value", "all" );

$option = $field->addChild( "option" );
$option->addAttribute( "name", strtolower( $Language->pcustom ) );
$option->addAttribute( "value", "custom" );

// ownwrite group custom
$group = $groups->addChild( "group" );
$group->addAttribute( "name", "ownwrite_custom" );
$group->addAttribute( "rel", "switch_ownwrite" );
$group->addAttribute( "value", "custom" );

$ownwrite = $group->addChild( "field" );
$ownwrite->addAttribute( "name", $Language->selectTable );
$ownwrite->addAttribute( "rel", "ownwrite" );
$ownwrite->addAttribute( "type", "options" );
$ownwrite->addAttribute( "params", "value='string'" );

// ownwrite group all
$group = $groups->addChild( "group" );
$group->addAttribute( "name", "ownwrite_all" );
$group->addAttribute( "rel", "switch_ownwrite" );
$group->addAttribute( "value", "all" );

$field = $group->addChild( "field" );
$field->addAttribute( "display", "false" );
$field->addAttribute( "name", "linkage" );
$field->addAttribute( "rel", "ownwrite" );
$field->addAttribute( "value", "#all#" );


// anywrite switcher field
$field = $groups->addChild( "field" );
$field->addAttribute( "name", $Language->anywrite );
$field->addAttribute( "rel", "switch_anywrite" );
$field->addAttribute( "type", "switch" );

$option = $field->addChild( "option" );
$option->addAttribute( "name", strtolower( $Language->ptotal ) );
$option->addAttribute( "value", "all" );

$option = $field->addChild( "option" );
$option->addAttribute( "name", strtolower( $Language->pcustom ) );
$option->addAttribute( "value", "custom" );

// anywrite group custom
$group = $groups->addChild( "group" );
$group->addAttribute( "name", "anywrite_custom" );
$group->addAttribute( "rel", "switch_anywrite" );
$group->addAttribute( "value", "custom" );

$anywrite = $group->addChild( "field" );
$anywrite->addAttribute( "name", $Language->selectTable );
$anywrite->addAttribute( "rel", "anywrite" );
$anywrite->addAttribute( "type", "options" );
$anywrite->addAttribute( "params", "value='string'" );

// anywrite group all
$group = $groups->addChild( "group" );
$group->addAttribute( "name", "anywrite_all" );
$group->addAttribute( "rel", "switch_anywrite" );
$group->addAttribute( "value", "all" );

$field = $group->addChild( "field" );
$field->addAttribute( "display", "false" );
$field->addAttribute( "name", "linkage" );
$field->addAttribute( "rel", "anywrite" );
$field->addAttribute( "value", "#all#" );

$total = count( $UpCMS->config->table );
for( $i = 0; $i < $total; $i++ )
{
	if( $UpCMS->config->table[$i] != $groups )
	{
		$v = (string) $UpCMS->config->table[$i]["rel"];
		$n = (string) $UpCMS->config->table[$i]["name"];
		
		$option = $anyread->addChild( "option" );
		$option->addAttribute( "value", $v );
		$option->addAttribute( "name", $n );
		
		$option = $ownwrite->addChild( "option" );
		$option->addAttribute( "value", $v );
		$option->addAttribute( "name", $n );
		
		$option = $anywrite->addChild( "option" );
		$option->addAttribute( "value", $v );
		$option->addAttribute( "name", $n );
	}
}


// USERS TABLE //
$users = $UpCMS->config->addChild( "table" );
$users->addAttribute( "name", $Language->users );
$users->addAttribute( "permission", "strict" );
$users->addAttribute( "rel", "system_users" );
$users->addAttribute( "reltable", "users" );
$users->addAttribute( "icon", "users" );

// username field
$field = $users->addChild( "field" );
$field->addAttribute( "name", $Language->username );
$field->addAttribute( "rel", "username" );
$field->addAttribute( "type", "simpletext" );
$field->addAttribute( "quickedit", "enabled" );

// password field
$field = $users->addChild( "field" );
$field->addAttribute( "name", $Language->password );
$field->addAttribute( "rel", "password" );
$field->addAttribute( "type", "password" );

// fullname field
$field = $users->addChild( "field" );
$field->addAttribute( "name", $Language->fullname );
$field->addAttribute( "rel", "fullname" );
$field->addAttribute( "type", "simpletext" );
$field->addAttribute( "quickedit", "enabled" );

// displayname field
$field = $users->addChild( "field" );
$field->addAttribute( "name", $Language->displayname );
$field->addAttribute( "rel", "displayname" );
$field->addAttribute( "type", "simpletext" );

// e-mail field
$field = $users->addChild( "field" );
$field->addAttribute( "name", $Language->email );
$field->addAttribute( "rel", "email" );
$field->addAttribute( "type", "simpletext" );
$field->addAttribute( "quickedit", "enabled" );

// image field
$field = $users->addChild( "field" );
$field->addAttribute( "name", $Language->image );
$field->addAttribute( "rel", "image" );
$field->addAttribute( "type", "image" );
$field->addAttribute( "params", "types(Language.images,'*.jpg;*.gif;*.png');" );

// url field
$field = $users->addChild( "field" );
$field->addAttribute( "name", $Language->url );
$field->addAttribute( "rel", "url" );
$field->addAttribute( "type", "simpletext" );

// group field
$field = $users->addChild( "field" );
$field->addAttribute( "name", $Language->group );
$field->addAttribute( "rel", "ugroup" );
$field->addAttribute( "type", "select" );

$dynamic = $field->addChild( "dynamic" );
$dynamic->addAttribute( "name", "name" );
$dynamic->addAttribute( "value", "id" );
$dynamic->addAttribute( "reltable", "groups" );

// access field
$field = $users->addChild( "field" );
$field->addAttribute( "name", $Language->access );
$field->addAttribute( "rel", "access" );
$field->addAttribute( "type", "select" );

$option = $field->addChild( "option" );
$option->addAttribute( "name", strtolower( $Language->allowed ) );
$option->addAttribute( "value", "#allowed#" );

$option = $field->addChild( "option" );
$option->addAttribute( "name", strtolower( $Language->denied ) );
$option->addAttribute( "value", "#denied#" );

?>