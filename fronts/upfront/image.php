<?php

/**
 * Print View of ApplicationImage
 * @author Orlando Leite
 * @version 0.8
 * @package front
 * @access public
 * @see ApplicationImage
 * @name ViewImage
 */
class ViewImage
{
	public static function resizecrop()
	{
		global $result;
		
		echo "{ \"status\":\"".( $result->status ? "true" : "false" )."\", \"path\":\"".$result->path."\" }";
	}
}

?>