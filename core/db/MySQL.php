<?php

include_once( "IConnection.php" );

/**
 * MySQL class for DB connections. See IConnection to know how it's works.
 * @author Orlando Leite
 * @version 0.8
 * @package core
 * @subpackage db
 * @access public
 * @name MySQL Connector
 */
class MySQL implements IConnection
{
	private $hostname, $username, $password, $database, $result, $conn;
	
	private static $_instance;
	
    private function __construct() { }
    private function __clone() { }
	
	public static function instance()
	{
		if( self::$_instance === NULL ) self::$_instance = new self();
		return self::$_instance;
	}
	
	public function connect( $hostname, $username, $password, $database )
	{
		$this->hostname = $hostname;
		$this->username = $username;
		$this->password = $password;
		$this->database = $database;
		
		$this->conn = mysql_connect( $this->hostname, $this->username, $this->password );
		if( $database != NULL ) mysql_select_db( $this->database, $this->conn );
		
		mysql_query( "SET CHARACTER SET utf8" );
		
		return $this->conn != NULL ? true : false;
	}
	
	public function databases()
	{
		$this->execute( "SHOW DATABASES" );
		
		if( DEBUGGING )
		{
			$t = fopen( "debug/db_execute.txt", "ab", true );
			fwrite( $t, "SHOW DATABASES\r\n" );
			fclose( $t );
		}
		
		$temp = array();
		while( $row = $this->row( DB_NUM ) )
		{
			array_push( $temp, $row[0] );
		}
		
		return $temp;
	}
	
	public function tables()
	{
		$this->execute( "SHOW TABLES" );
		
		if( DEBUGGING )
		{
			$t = fopen( "debug/db_execute.txt", "ab", true );
			fwrite( $t, "SHOW TABLES\r\n" );
			fclose( $t );
		}
		
		$temp = array();
		while( $row = $this->row( DB_NUM ) )
		{
			array_push( $temp, $row[0] );
		}
		
		return $temp;
	}
	
	public function columns( $table )
	{
		$this->execute( "SHOW COLUMNS FROM ".$table );
		
		if( DEBUGGING )
		{
			$t = fopen( "debug/db_execute.txt", "ab", true );
			fwrite( $t, "SHOW COLUMNS FROM ".$table."\r\n" );
			fclose( $t );
		}
		
		$temp = array();
		while( $row = $this->row( DB_NUM ) )
		{
			array_push( $temp, $row[0] );
		}
		
		return $temp;
	}
	
	public function createTable( $name, $fields )
	{
		$list = array();
		foreach( $fields as $k => $field )
		{
			$target = "`".$k."`";
			switch( $field["type"] )
			{
				case "chars":
					$target .= " varchar(255) NOT NULL";
					break;
				
				case "byte":
					$target .= " SMALLINT NOT NULL";
					break;
				
				case "int":
					$target .= " INT NOT NULL";
					break;
				
				case "bigint":
					$target .= " BIGINT NOT NULL";
					break;
				
				case "double":
					$target .= " DOUBLE NOT NULL";
					break;
				
				case "text":
					$target .= " text";
					break;
				
				case "timestamp":
					$target .= " datetime NOT NULL";
					break;
			}
			
			if( $field["unsigned"] == true )
			{
				$target .= " unsigned";
			}
			
			if( $field["unique"] == true )
			{
				$target .= " UNIQUE";
			}
			
			array_push( $list, $target );
		}
		
		return $this->execute( "CREATE TABLE ".$name." ( id INT unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY, ".implode( ", ", $list )." )" );
	}
	
	public function editTable( $name, $fields )
	{
		$list = array();
		foreach( $fields as $k => $field )
		{
			switch( $field["do"] )
			{
				case "change":
					$target = "CHANGE ".$k." ".$k;
					break;
				
				case "add":
					$target = "ADD ".$k;
					break;
				
				case "drop":
					$target = "DROP ".$k;
					break;
			}
			
			if( $field["do"] != "drop" )
			{
				switch( $field["type"] )
				{
					case "chars":
						$target .= " varchar(255) NOT NULL";
						break;
					
					case "byte":
						$target .= " SMALLINT NOT NULL";
						break;
					
					case "int":
						$target .= " INT NOT NULL";
						break;
					
					case "bigint":
						$target .= " BIGINT NOT NULL";
						break;
					
					case "double":
						$target .= " DOUBLE NOT NULL";
						break;
					
					case "text":
						$target .= " text";
						break;
					
					case "timestamp":
						$target .= " timestamp NOT NULL";
						break;
				}
				
				if( $field["unsigned"] == true )
				{
					$target .= " unsigned";
				}
				
				if( $field["unique"] == true )
				{
					$target .= " UNIQUE";
				}
			}
			
			array_push( $list, $target );
		}
		
		return $this->execute( "ALTER TABLE ".$name." ".implode( ", ", $list ) );
	}
	
	public function insertEmptyRow( $table )
	{
		return @$this->execute( "INSERT INTO ".$table." (id) VALUES (NULL)" );
	}
	
	public function select( $query, $type = DB_ASSOC )
	{
		$result = mysql_query( $query );
		
		if( DEBUGGING )
		{
			$t = fopen( "debug/db_execute.txt", "ab", true );
			fwrite( $t, $query."\r\n" );
			fclose( $t );
		}
		
		$temp = array();
		
		if( !is_bool( $result ) )
		{
			while( $row = mysql_fetch_array( $result, $type ) )
			{
				foreach( $row as $k => $obj ) $row[$k] = $obj;
				array_push( $temp, $row );
			}
		}
		
		return $temp;
	}
	
	public function execute( $query )
	{
		if( DEBUGGING )
		{
			$t = fopen( "debug/db_execute.txt", "ab", true );
			fwrite( $t, $query."\r\n" );
			fclose( $t );
		}
		
		return $this->result = mysql_query( $query );
	}
	
	public function row( $type = DB_ASSOC, $resource = NULL )
	{
		return  @mysql_fetch_array( $resource == NULL ? $this->result : $resource, $type );
	}
	
	public function lastID()
	{
		return mysql_insert_id( $this->conn );
	}
	
	public function close()
	{
		return mysql_close( $this->conn );
	}
	
	public function error()
	{
		return mysql_error( $this->conn );
	}
	
	public function __toString()
	{
		return "[Object MySQL]";
	}
	
}

?>