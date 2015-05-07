<?php

/**
 * Interface for DB connections. All classes made for DB connections should be use this.
 * @author Orlando Leite
 * @version 0.8
 * @package core
 * @access public
 * @name IConnection
 */

define( "DB_ASSOC", 1 );
define( "DB_NUM", 2 );
define( "DB_BOTH", 3 );

interface IConnection
{
	/**
    * Singleton instance getter.
    * @author Orlando Leite
    * @access public
    * @return IConnection
    */
	public static function instance();
	
	/**
    * Connect to DB.
    * @author Orlando Leite
    * @access public
    * @param string $hostname the host address. e.g. 'localhost'.
    * @param string $username login for host. e.g. 'root'.
    * @param string $password password for login used. e.g. '' or 'root'.
    * @param string $database the database name. If you don't want to choose one leave NULL. e.g. 'upcms'.
    * @return boolean true for success.
    */
    public function connect( $hostname, $username, $password, $database );
	
	/**
    * Get all databases in the server.
    * @author Orlando Leite
    * @access public
    * @return array filled with all databases in the connected server.
    */
    public function databases();
	
	/**
    * Get all tables in the connected database.
    * @author Orlando Leite
    * @access public
    * @return array filled with all databases in the connected server.
    */
    public function tables();
	
	/**
    * Get all columns from a table.
    * @author Orlando Leite
    * @access public
    * @param string $table the table to get columns.
    * @return array filled with all databases in the connected server.
    */
    public function columns( $table );
	
	/**
    * Create a table in the current connected database.
    * @author Orlando Leite
    * @access public
    * @param string $name the new table name.
    * @param array $fields Columns of the new table. Use as key the name of the new column. The attributes should be 'type', 'unique' and 'unsigned'.
    * Type can be 'chars', 'byte, 'int, 'bigint, 'double', 'text' and 'timestamp'.
    * The attributes unique and unsigned is a boolean;
    * @return true for success.
    */
	public function createTable( $name, $fields );
	
	/**
    * Alter a table in the current connected database.
    * @author Orlando Leite
    * @access public
    * @param string $name the new table name.
    * @param array $fields Columns of the new table. Use as key the name of the new column. The attributes should be 'do', 'type', 'unique' and 'unsigned'.
    * Set the 'do' for the action to do. It can be 'change', 'add', 'drop'.
    * Type can be 'chars', 'byte, 'int, 'bigint, 'double', 'text' and 'timestamp'.
    * The attributes unique and unsigned is a boolean;
    * @return true for success.
    */
	public function editTable( $name, $fields );
	
	/**
    * Execute a select query in DB.
    * @author Orlando Leite
    * @access public
    * @param string $query what should be executed. e.g. 'SELECT * FROM MyDB'
    * @param integer $type what the type used in return mode. You can choose DB_ASSOC, DB_NUM or DB_BOTH
    * @return array an array with rows from query
    */
	public function select( $query, $type = DB_ASSOC );
	
	/**
    * INSERT INTO a table a empty row.
    * @author Orlando Leite
    * @access public
    * @param string $table The table name.
    * @return boolean true for success.
    */
	public function insertEmptyRow( $table );
	
	/**
    * Execute your query in DB.
    * @author Orlando Leite
    * @access public
    * @param string $query what should be executed. e.g. 'SELECT * FROM MyDB'
    * @return resource If you make a select, you should use row() for get the items.
    */
	public function execute( $query );
	
	/**
    * After made a insert, use this for get the ID inserted.
    * @author Orlando Leite
    * @access public
    * @return integer id number.
    */
	public function lastID();
	
	/**
    * Close connection. some services host made this after php execution finish. But is highly recomended call after everything is done.
    * @author Orlando Leite
    * @access public
    * @return boolean true for success.
    */
	public function close();
	
	/**
    * Get a row. If you made a select using execute method, use this for get the rows.
    * @author Orlando Leite
    * @access public
    * @param integer $type what the type used in return mode. You can choose DB_ASSOC, DB_NUM or DB_BOTH
    * @param resource $resource Where the row came from. The default value is the last execute() resource.
    * @return object an object with values selected.
    */
	public function row( $type = DB_ASSOC, $resource = NULL );
	
	/**
    * In case of error, you can get what happens calling this.
    * @author Orlando Leite
    * @access public
    * @return string error info.
    */
	public function error();
	
	/**
    * Define this function in your class to return what is the type of.
    * @author Orlando Leite
    * @access public
    * @return string like a typeof, in MySQL class it's return '[Object MySQL]'.
    */
	public function __toString();
}

?>