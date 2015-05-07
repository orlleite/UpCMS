<?php

/**
 * Print View of ApplicationEdit
 * @author Orlando Leite
 * @version 0.8
 * @package front
 * @access public
 * @see ApplicationEdit
 * @name ViewEdit
 */
class ViewEdit
{
	public static function update()
	{
		global $result;
		
		// echo $result->edit;
		echo json_encode( $result );
	}
	
	public static function getEdit()
	{
		self::getPrint();
	}
	
	public static function save()
	{
		global $result;
		
		echo json_encode( $result );
	}
	
	public static function getNew()
	{
		self::getPrint();
	}
	
	protected static function getPrint()
	{
		global $result;
		
		foreach( $result->fields as $k => $value )
		{
			if( !@$value->display ) unset( $value );
			else if( $value->type == "group" )
				foreach( $value->fields as $a => $f )
				{
					if( !@$f->display ) unset( $value->fields[$a] );
				}
		}
		// print_r( $result );
		echo json_encode( $result );
	}
	
	public static function resetArray()
	{
		global $result;
		
		echo $result->edit;
	}
	
	public static function remove()
	{
		global $result;
		
		echo $result->remove;
	}
}

?>