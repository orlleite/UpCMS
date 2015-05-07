<?php

/**
 * Config example
 * @author Orlando Leite
 * @version 0.8
 * @package default
 * @access public
 */

/**
 * Set if should run in debug mode or not.
 * @var boolean
 */
$debugging              = true;

/**
 * Set db software. e.g. 'mysql'.
 * @var string
 */
$db_type                = "mysql";

/**
 * Set db prefix. e.g. 'up_'.
 * @var string
 */
$db_prefix              = "";

/**
 * Set system prefix. e.g. 'sys_'.
 * @var string
 */
$up_prefix              = "sys_";

/**
 * Set host address. When PostGre you must set address:port. e.g. 'localhost:5432'
 * @var string
 */
$db_hostname    = "localhost";

/**
 * Set username to login. e.g. 'root'.
 * @var string
 */
$db_username    = "root";

/**
 * Set password to login. e.g. 'root' or ''.
 * @var string
 */
$db_password    = "";

/**
 * Set database name. e.g. 'upcms'.
 * @var string
 */
$db_database    = "qualquer";

/**
 * Set upload folder path. e.g. 'files/'.
 * Don't forget to put '/' after all.
 * @var string
 */
$upload_folder  = "files/";

/**
 * Set upload folder path. e.g. 'plugins/'.
 * Don't forget to put '/' after all.
 * @var string
 */
$plugin_folder  = "plugins/";

?>
