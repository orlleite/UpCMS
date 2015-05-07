<?php

/**
 * Print View of ApplicationList
 * @author Orlando Leite
 * @version 0.8
 * @package front
 * @access public
 * @see ApplicationList
 * @name ViewList
 */
class ViewList
{
	public static function authors()
	{
		global $result;
		
		echo json_encode( $result );
	}
	
	public static function get()
	{
		global $result;
		
		echo json_encode( $result );
	}
}

?>