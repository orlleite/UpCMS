<?php

include_once( "IConnection.php" );

/**
 * PgSQL class for DB connections. See IConnection to know how it's works.
 * @author Orlando Leite
 * @version 0.8
 * @package core
 * @subpackage db
 * @access public
 * @name PgSQL Connector
 */
class PgSQL implements IConnection
{
	private $hostname, $port, $username, $password, $database, $conn, $result, $lastid;
	
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
		$t = explode( ":", $hostname );
		
		$this->hostname = $t[0];
		$this->username = $username;
		$this->password = $password;
		$this->database = $database;
		$this->port = $t[1];
		
		$this->conn = pg_connect( "host=".$this->hostname." port=".$this->port." ".( $database != NULL ? "dbname=".$this->database : "" )." user=".$this->username." password=".$this->password );
	}
	
	public function databases()
	{
		$this->execute( "SELECT datname FROM pg_database" );
		
		if( DEBUGGING )
		{
			$t = fopen( "debug/db_execute.txt", "ab", true );
			fwrite( $t, "SELECT datname FROM pg_database\r\n" );
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
		$this->execute( "SELECT table_name FROM information_schema.tables WHERE table_schema='public' AND table_type='BASE TABLE'" );
		
		if( DEBUGGING )
		{
			$t = fopen( "debug/db_execute.txt", "ab", true );
			fwrite( $t, "SELECT table_name FROM information_schema.tables WHERE table_schema='public' AND table_type='BASE TABLE'\r\n" );
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
		$this->execute( "SELECT column_name FROM information_schema.columns WHERE table_name ='".$table."'" );
		
		if( DEBUGGING )
		{
			$t = fopen( "debug/db_execute.txt", "ab", true );
			fwrite( $t, "SELECT column_name FROM information_schema.columns WHERE table_name ='".$table."'\r\n" );
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
			$target = "\"".$k."\"";
			switch( $field["type"] )
			{
				case "chars":
					$target .= " varchar(255) NOT NULL DEFAULT ''";
					break;
				
				case "byte":
					$target .= " TINYINT NOT NULL DEFAULT 0";
					break;
				
				case "int":
					$target .= " INT NOT NULL DEFAULT 0";
					break;
				
				case "bigint":
					$target .= " BIGINT NOT NULL DEFAULT 0";
					break;
				
				
				case "double":
					$target .= " DOUBLE NOT NULL DEFAULT 0";
					break;
				
				case "text":
					$target .= " text NOT NULL DEFAULT ''";
					break;
				
				case "timestamp":
					$target .= " timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP";
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
		
		return $this->execute( "CREATE TABLE ".$name." ( id SERIAL PRIMARY KEY, ".implode( ", ", $list )." )" );
	}
	
	public function editTable( $name, $fields )
	{
		$list = array();
		foreach( $fields as $k => $field )
		{
			switch( $field["do"] )
			{
				case "change":
					$target = "CHANGE \"".$k."\" \"".$k."\"";
					break;
				
				case "add":
					$target = "ADD \"".$k."\"";
					break;
				
				case "drop":
					$target = "DROP \"".$k."\"";
					break;
			}
			
			if( $field["do"] != "drop" )
			{
				switch( $field["type"] )
				{
					case "chars":
						$target .= " varchar(255) NOT NULL DEFAULT ''";
						break;
					
					case "byte":
						$target .= " TINYINT NOT NULL DEFAULT 0";
						break;
					
					case "int":
						$target .= " INT NOT NULL DEFAULT 0";
						break;
					
					case "bigint":
						$target .= " BIGINT NOT NULL DEFAULT 0";
						break;
					
					
					case "double":
						$target .= " DOUBLE NOT NULL DEFAULT 0";
						break;
					
					case "text":
						$target .= " text NOT NULL DEFAULT ''";
						break;
					
					case "timestamp":
						$target .= " timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP";
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
		$res = $this->execute( "SELECT NEXTVAL( '".$table."_id_seq' )" );
		$row = $this->row( DB_NUM );
		$this->lastid = $row[0];
		
		return $this->execute( "INSERT INTO ".$table." (id) VALUES ( ".$this->lastid." )" );
	}
	
	public function select( $query, $type = DB_ASSOC )
	{
		$result = @pg_query( $this->conn, $query );
		
		if( DEBUGGING )
		{
			$t = fopen( "debug/db_execute.txt", "ab", true );
			fwrite( $t, $query."\r\n" );
			fclose( $t );
		}
		
		$temp = array();
		
		if( !is_bool( $result ) )
		{
			while( $row = @pg_fetch_array( $result, NULL, $type ) )
			{
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
		
		$vale = $this->result = @pg_query( $this->conn, $query );
		return $vale;
	}
	
	public function row( $type = DB_ASSOC, $resource = NULL )
	{
		return pg_fetch_array( $resource == NULL ? $this->result : $resource, NULL, $type );
	}
	
	public function lastID()
	{
		return $this->lastid;
	}
	
	public function close()
	{
		pg_close( $this->conn );
	}
	
	public function error()
	{
		return pg_last_error( $this->conn );
	}
	
	public function __toString()
	{
		return "[Object PgSQL]";
	}
	
}

?>