<?php

/**
 * User class
 * used for get properties and searchPermissions from a logged user.
 * @author Orlando Leite
 * @version 0.8
 * @package core
 * @subpackage cms
 * @access public
 * @see Login
 * @name User
 */
class User
{
	/**
	* User ID
	* @access protected
	* @var string|integer
	*/
	protected $id;
	
	/**
	* User name
	* @access protected
	* @var string
	*/
	protected $username;
	
	/**
	* User group
	* @access protected
	* @var object
	*/
	protected $group;
	
	/**
	* User infos such as full name and display name.
	* @access protected
	* @var object
	*/
	protected $info;
	
	private static $_instance;
	
	protected function __clone() { }
	
	public static function instance()
	{
		session_start();
		
		if( isset( $_SESSION["username"] ) )
		{
			if( self::$_instance === NULL ) self::$_instance = new self();
			return self::$_instance;
		}
		else
		{
			return false;
		}
	}
	
	protected function __construct()
	{
		$this->id = $_SESSION["id"];
		$this->username = $_SESSION["username"];
	}
	
	/**
	* Set the protected vars, info and group with values parsed from $target.
	* @param array $target values.
	* @access protected
	* @see User->getUserValues
	* @return void
	*/
	protected function setValues( $target )
	{
		$info["id"] = $this->id;
		$info["username"] = $this->username;
		
		$info["fullname"] = $target[2];
		$info["displayname"] = $target[3];
		$info["email"] = $target[4];
		$info["image"] = $target[5];
		$info["url"] = $target[6];
		$info["group"] = $target[7];
		
		$group["ownread"] = $target[8];
		$group["ownwrite"] = $target[9];
		$group["anyread"] = $target[10];
		$group["anywrite"] = $target[11];
		$group["applications"] = $target[12];
		
		$this->info = $info;
		$this->group = $group;
	}
	
	/**
	* Get User values from DB.
	* @access protected
	* @see User->searchPermission
	* @retun void
	*/
	protected function getUserValues()
	{
		global $UpCMS, $up_prefix;
		
		$UpCMS->db->execute( "SELECT ".$up_prefix."users.id, ".$up_prefix."users.username, ".$up_prefix."users.fullname, ".$up_prefix."users.displayname, ".$up_prefix."users.email, ".$up_prefix."users.image, ".$up_prefix."users.url, ".$up_prefix."groups.name, ".$up_prefix."groups.ownread, ".$up_prefix."groups.ownwrite, ".$up_prefix."groups.anyread, ".$up_prefix."groups.anywrite, ".$up_prefix."groups.applications FROM ".$up_prefix."users JOIN ".$up_prefix."groups ON ".$up_prefix."users.ugroup = ".$up_prefix."groups.id WHERE ".$up_prefix."users.username = '".$this->username."'" );
		
		if( $target = $UpCMS->db->row( DB_NUM ) ) $this->setValues( $target );
	}
	
	/**
	* Search for permission parsing target rel table and group type.
	* @param string $target relation table.
	* @param string $in type of permission needed. e.g. 'anyread'.
	* @access protected
	* @see User->anyread, User->ownread, User->anywrite, User->ownwrite
	* @return boolean permission allowed or not.
	*/
	protected function searchPermission( $target, $in )
	{
		if( !$this->group ) $this->getUserValues();
		
		$in = $this->group[$in];
		
		if( $in == "#all#" or strpos( $in, $target ) !== false ) return true;
		else return false;
	}
	
	/**
	* Get a info user property.
	* @param string $target property name. e.g. 'fullname'.
	* @access public
	* return string property value.
	*/
	public function info( $target )
	{
		if( !$this->group ) $this->getUserValues();
		
		return $this->info[$target];
	}
	
	/**
	* Get anyread for the table parsed.
	* @param string $target table name name.
	* @access public
	* @return boolean permission allowed or not.
	*/
	public function anyread( $target )
	{
		return $this->searchPermission( $target, "anyread" );
	}
	
	/**
	* Get ownread for the table parsed.
	* @param string $target table name name.
	* @access public
	* @return boolean permission allowed or not.
	*/
	public function ownread( $target )
	{
		return $this->searchPermission( $target, "ownread" );
	}
	
	/**
	* Get anywrite for the table parsed.
	* @param string $target table name name.
	* @access public
	* @return boolean permission allowed or not.
	*/
	public function anywrite( $target )
	{
		return $this->searchPermission( $target, "anywrite" );
	}
	
	/**
	* Get ownwrite for the table parsed.
	* @param string $target table name name.
	* @access public
	* @return boolean permission allowed or not.
	*/
	public function ownwrite( $target )
	{
		return $this->searchPermission( $target, "ownwrite" );
	}
	
	/**
	* Get application permission for the name parsed.
	* @param string $target table name name.
	* @access public
	* @return boolean permission allowed or not.
	*/
	public function application( $target )
	{
		return $this->searchPermission( $target, "applications" );
	}
	
	/**
	* Do a login.
	* @param string $username username for log in.
	* @param string $password password for log in.
	* @access public
	* @static
	* @return boolean success or not.
	*/
	public static function login( $username, $password )
	{
		global $UpCMS, $up_prefix;
		
		/*if( $username == "admin" )
		{
			include_once( "Admin.php");
			return Admin::login( $password );
		}
		else
		{*/
			$UpCMS->db->execute( "SELECT ".$up_prefix."users.id, ".$up_prefix."users.username, ".$up_prefix."users.fullname, ".$up_prefix."users.displayname, ".$up_prefix."users.email, ".$up_prefix."users.image, ".$up_prefix."users.url, ".$up_prefix."groups.name, ".$up_prefix."groups.ownread, ".$up_prefix."groups.ownwrite, ".$up_prefix."groups.anyread, ".$up_prefix."groups.anywrite, ".$up_prefix."groups.applications, ".$up_prefix."users.access FROM ".$up_prefix."users JOIN ".$up_prefix."groups ON ".$up_prefix."users.ugroup = ".$up_prefix."groups.id WHERE ".$up_prefix."users.username = '".$username."' AND ".$up_prefix."users.password = SHA1( '".$password."' )" );
			
			if( $target = $UpCMS->db->row( DB_NUM ) and strpos( $target[13], "#allowed#" ) !== false )
			{
				session_start();
				$_SESSION['id'] = $target[0];
				$_SESSION["username"] = $target[1];
				
				$user = User::instance();
				$user->setValues( $target );
				
				return true;
			}
			else
			{
				return false;
			}
		// }
	}
	
	/**
	* Do a logout.
	* @access public
	* @static
	* @return void.
	*/
	public static function logout()
	{
		session_start();
		session_destroy();
		session_unset();
		
		self::$_instance->username = self::$_instance->group = null;
	}
	
}

?>