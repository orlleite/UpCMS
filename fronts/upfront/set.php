<?php

/**
 * Print View of ApplicationSet
 * @author Orlando Leite
 * @version 0.8
 * @package front
 * @access public
 * @see ApplicationSet
 * @name ViewSet
 */
class ViewSet
{
	public static function get()
	{
		global $result;
		
		echo json_encode( $result->list );
	}
	
	public static function set()
	{
		global $result;
		
		echo json_encode( $result );
	}
}

?>