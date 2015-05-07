<?php

/**
 * Set the config of front.
 * Basically, adds settings of it.
 * @author Orlando Leite
 * @version 0.8
 * @package front
 * @access public
 * @name Front Config
 */
if( $UpCMS->user && $UpCMS->user->application( "settings" ) )
{
	$UpCMS->settings["front"]->get = UP_FRONT_FOLDER."settings/Get.general.php";
	$UpCMS->settings["front"]->set = UP_FRONT_FOLDER."settings/Set.general.php";
}

?>