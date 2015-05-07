<?php
/**
 * DEFINE DB_ASSOC, DB_NUM and DB_BOTH for use in connections classes;
 * Start db connection and set it up to $UpCMS->db.
 * @author Orlando Leite
 * @version 0.8
 * @package core
 * @subpackage db
 * @access public
 * @name DB Starter
 */

if( $db_type == "mysql" )
{
	include_once( "MySQL.php" );
	$UpCMS->db = MySQL::instance();
}
else
{
	include_once( "PgSQL.php" );
	$UpCMS->db = PgSQL::instance();
}

$UpCMS->db->connect( $db_hostname, $db_username, $db_password, $db_database );

?>