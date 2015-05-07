<?php

/**
 * Print View of ApplicationLogin
 * @author Orlando Leite
 * @version 0.8
 * @package front
 * @access public
 * @see ApplicationLogin
 * @name ViewLogin
 */
class ViewLogin
{
	public static function login()
	{
		global $result;
		
		if( $result->status )
			echo "{ \"status\":\"".( $result->status ? "true" : "false" )."\", \"name\":\"".$result->name."\", \"group\":\"".$result->group."\", \"id\":\"".$result->id."\" }";
		else
			echo "{ \"status\":\"".( $result->status ? "true" : "false" )."\" }";
	}
	
	public static function logout()
	{
		echo "{ \"status\":\"true\" }";
	}
	
	public static function getInitScript()
	{
		global $UpCMS, $Language;
		
		$searchList = array();
		echo "function startMenu() {\n";
		
		foreach( $UpCMS->menu as $b )
		{
			echo "	addLinkMenu( \"".$b->rel."\", \"".$b->name."\", \"".( strpos( $b->url, "ext://" ) === 0 ? substr( $b->url, 6 ) : "#/".$b->url )."\", \"".$b->icon."\" );\n";
			if( isset( $b->options ) ) foreach( $b->options as $s ) echo "		addSubMenu( \"".$b->rel."\", \"".$s->name."\", \"".( strpos( $s->url, "ext://" ) === 0 ? substr( $s->url, 6 ) : "#/".$s->url )."\" );\n";
		}
		
		echo "}";
	}
	
	public static function logged()
	{
		global $result;
		
		if( $result->status )
		{
			echo "{ \"status\":\"true\", \"name\":\"".$result->name."\", \"group\":\"".$result->group."\", \"id\":\"".$result->id."\" }";
		}
		else
		{
			echo "{ \"status\":\"false\" }";
		}
	}
}

?>