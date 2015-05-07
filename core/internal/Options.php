<?php

/**
 * Manage the options. All options is saved in sys options table from db.
 * @author Orlando Leite
 * @version 0.8
 * @package core
 * @subpackage internal
 * @access public
 * @name Options
 */
class Options
{
	private $opt;
	
	/**
	* Constructor
	* Select all options and put in an array.
    * @author Orlando Leite
    * @access public
    * @return Options
    */
	public function Options()
	{
		global $UpCMS, $up_prefix;
		// UPDATE THIS PART. THERE ARE TWO 'FOR', SHOULD BE DONE IN ROW LEVEL //
		$this->parse( $UpCMS->db->select( "SELECT ".$up_prefix."options.owner, ".$up_prefix."options.name, ".$up_prefix."options.value, ".$up_prefix."options.status FROM ".$up_prefix."options", DB_NUM ) );
	}
	
	/**
	* Parse all content get from an array to $opt.
	* This method are will be merged to constructor soon. The parse should be using rows.
    * @author Orlando Leite
    * @access private
    * @param array $opt An array of options got from db.
    * @return void
    */
	private function parse( $opt )
	{
		$total = count( $opt );
		for( $i = 0; $i < $total; $i++ )
		{
			if( $opt[$i][3] == "" || $opt[$i][3] == "true" )
				$this->$opt[$i][0]->$opt[$i][1] = $opt[$i][2];
		}
	}
	
	/**
	* Get a option value.
	* @author Orlando Leite
    * @access private
    * @param string $owner the owner of this option, use lowercase. Options of Up!CMS uses upcms.
    * @param string $name the option name.
    * @return string the option value.
    */
	public function get( $owner, $name )
	{
		return @$this->$owner->$name;
	}
	
	/**
	* Set a option value.
	* @author Orlando Leite
    * @access private
    * @param string $owner the owner of this option, use lowercase. Options of Up!CMS uses upcms.
    * @param string $name the option name.
    * @param string $value the value to be set.
    * @param string $status currently status of the option. In case of false, the value will not be returned from 'get'.
    * @return string the option value.
    */
	public function set( $owner, $name, $value, $status = "true" )
	{
		global $UpCMS, $up_prefix;
		
		if( $status == "" || $status == "true" )
			$this->$owner->$name = $value;
		else
			$this->$owner->$name = NULL;
		
		return $UpCMS->db->execute( "UPDATE ".$up_prefix."options SET value = '".$value."', status = '".$status."' WHERE owner = '".$owner."' AND name = '".$name."'" );
	}
	
	/**
	* Add a option and the value of.
	* @author Orlando Leite
    * @access private
    * @param string $owner the owner of this option, use lowercase. Options of Up!CMS uses upcms.
    * @param string $name the option name.
    * @param string $value the value to be set.
    * @param string $status currently status of the option. In case of false, the value will not be returned from 'get'.
    * @return string the option value.
    */
	public function add( $owner, $name, $value, $status = "true" )
	{
		global $UpCMS, $up_prefix;
		
		if( $status == "" || $status == "true" )
			$this->$owner->$name = $value;
		else
			$this->$owner->$name = NULL;
		
		return $UpCMS->db->execute( "INSERT INTO ".$up_prefix."options ( owner, name, value, status ) VALUES ( '".$owner."', '".$name."', '".$value."', '".$status."' );" );
	}
	
	/**
	* Remove a option.
	* @author Orlando Leite
    * @access private
    * @param string $owner the owner of this option, use lowercase. Options of Up!CMS uses upcms.
    * @param string $name the option name.
    * @return boolean the result.
    */
	public function remove( $owner, $name )
	{
		global $UpCMS, $up_prefix;
		
		$this->$owner->$name = NULL;
		
		return $UpCMS->db->execute( "DELETE FROM ".$up_prefix."options WHERE ".$up_prefix."options.owner = '".$owner."' AND ".$up_prefix."options.name = '".$name."';" );
	}
	
	/**
    * Return what is the type of this class.
    * @author Orlando Leite
    * @access public
    * @return string '[Object Options]'.
    */
	public function __toString()
	{
		return "[Object Options]";
	}
}

?>