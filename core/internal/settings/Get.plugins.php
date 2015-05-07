<?php

/**
 * Get plugins settings
 * @author Orlando Leite
 * @version 0.8
 * @package core
 * @subpackage internal
 * @access protected
 * @see UpCMS->settings
 * @name Get plugins
 */

global $Language, $UpCMS, $debugging;

$list->name = $Language->pluginsSettings;

// LIST //
$n = new stdClass();
$n->columns->name->width = "40%";
$n->columns->name->name = $Language->pluginName;

// Plugin ON-OFF //
$f = new stdClass();
$f->name = "";
$f->value = "on";
$f->type = "lonoff";
$f->options->on = $Language->pluginOn;
$f->options->off = $Language->pluginOff;
$n->columns->name->fields->activate = $f;

// Plugin Delete //
$f = new stdClass();
$f->type = "llink";
$f->name = $Language->pluginUninstall;
$n->columns->name->fields->delete = $f;

// Description Row //
$n->columns->description->name = $Language->pluginDescription;
$n->columns->description->width = "60%";

// Author //
$f = new stdClass();
$f->type = "llabel";
$n->columns->description->fields->author = $f;

// Version //
$f = new stdClass();
$f->type = "llabel";
$n->columns->description->fields->version = $f;

$plugins = PluginManager::alist();

foreach( $plugins as $k => $p )
{
	// Items //
	$i = new stdClass();
	$i->name->activate = $p->working ? "on" : "off";
	$i->name->delete = "delete";
	$i->name->content = $p->name;
	$i->description->content = $p->description;
	$i->description->version = $Language->pluginVersion." ".$p->version;
	$i->description->author = $Language->pluginBy." <a href=\"".$p->url."\">".$p->author."</a>";
	
	$rel = $p->rel;
	$n->rows->$rel = $i;
}
 
$list->groups->plugins->name = "";
$list->groups->plugins->list = $n;

?>