<?php

/**
 * Load the correct language, based in option selected.
 * @author Orlando Leite
 * @version 0.8
 * @package front
 * @subpackage languages
 * @access public
 */
include_once( $UpCMS->options->get( "upcms", "language" ).".php" );

?>