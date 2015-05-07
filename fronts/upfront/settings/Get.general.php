<?php

/**
 * Get Up!CMS Front settings
 * @author Orlando Leite
 * @version 0.8
 * @package front
 * @subpackage settings
 * @access public
 */
global $Language, $UpCMS;

$list->name = $Language->frontSettings;
$list->groups->front->name = "";

// Animations //
$n = NULL;
$n->type = "select";
$n->name = $Language->animationLevel;
$n->about = $Language->aboutAnimationLevel;
$n->value = $UpCMS->options->get( "upfront", "animation_level" );
$n->options[0] = $Language->animation0;
$n->options[1] = $Language->animation1;
$n->options[2] = $Language->animation2;
$list->groups->front->fields->animations = $n;

// Minimize box //
$n = NULL;
$n->type = "onoff";
$n->name = $Language->minimizeBox;
$n->options->on = $Language->minimizeBoxOn;
$n->options->off = $Language->minimizeBoxOff;
$n->value = $UpCMS->options->get( "upfront", "minimize_box" ) == "true" ? "on" : "off";
$list->groups->front->fields->minimize = $n;

// Quickedit //
$n = NULL;
$n->type = "onoff";
$n->name = $Language->quickEdit;
$n->options->on = $Language->quickEditOn;
$n->options->off = $Language->quickEditOff;
$n->value = $UpCMS->options->get( "upfront", "quickedit" ) == "true" ? "on" : "off";
$list->groups->front->fields->quickedit = $n;

// List thumb size //
$n = NULL;
$n->type = "number2d";
$n->name = $Language->listThumbSize;
$n->about = $Language->aboutListThumbSize;
$n->value = $UpCMS->options->get( "upfront", "list_thumb_size" );
$list->groups->front->fields->thumbsize = $n;

// Auto show table content //
$n = NULL;
$n->type = "onoff";
$n->name = $Language->autoShowTableContent;
$n->options->on = $Language->autoShowTableContentOn;
$n->options->off = $Language->autoShowTableContentOff;
$n->value = $UpCMS->options->get( "upfront", "auto_show_table_content" ) == "true" ? "on" : "off";
$list->groups->front->fields->tablecontent = $n;

// Multiple Adding //
$n = NULL;
$n->type = "onoff";
$n->name = $Language->multipleAdding;
$n->options->on = $Language->multipleAddingOn;
$n->options->off = $Language->multipleAddingOff;
$n->value = $UpCMS->options->get( "upfront", "multiple_adding" ) == "true" ? "on" : "off";
$list->groups->front->fields->multipleadding = $n;

// Show UP!CMS Version //
$n = NULL;
$n->type = "onoff";
$n->name = $Language->showUpVersion;
$n->options->on = $Language->showUpVersionOn;
$n->options->off = $Language->showUpVersionOff;
$n->value = $UpCMS->options->get( "upfront", "show_up_version" ) == "true" ? "on" : "off";
$list->groups->front->fields->showversion = $n;

?>