<?php

/**
 * Load the correct language, based in option selected.
 * @author Orlando Leite
 * @version 0.8
 * @package core
 * @subpackage languages
 * @access public
 * @name Language Starter
 */
include_once( $UpCMS->options->get( "upcms", "language" ).".php" );
include_once( UP_FRONT_FOLDER."/languages/index.php" );

?>