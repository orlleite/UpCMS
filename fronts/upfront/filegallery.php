<?php

/**
 * Print View of ApplicationFileGallery
 * @author Orlando Leite
 * @version 0.8
 * @package front
 * @access public
 * @see ApplicationGallery
 * @name ViewFileGallery
 */
class ViewFileGallery
{
	public static function delete()
	{
		global $result;
		
		echo $result->status;
	}
	
	public static function rename()
	{
		global $result;
		
		echo $result->path;
	}
	
	public static function alist()
	{
		global $result;
		
		echo "{ \"files\":[";
		
		$total = count( $result->list );
		
		if( $total != 0 )
		{
			echo "{ \"name\":\"".$result->list[0]->name."\", \"type\":\"".$result->list[0]->type."\", \"path\":\"".$result->list[0]->path."\" }";
			
			for( $i = 1; $i < $total; $i++ )
			{
				echo ", { \"name\":\"".$result->list[$i]->name."\", \"type\":\"".$result->list[$i]->type."\", \"path\":\"".$result->list[$i]->path."\" }";
			}
		}
		
		echo "] }";
	}
	
	public static function upload()
	{
		global $result;
		
		echo json_encode( $result );
	}
	
	public static function decompress()
	{
		global $result;
		
		echo json_encode( $result );
	}
	
	public static function verify()
	{
		global $result;
		
		echo "{ \"files\":[";
		
		$total = count( $result->list );
		
		if( $total != 0 )
		{
			echo $result->list[0] ? "\"true\"" : "\"false\"";
			
			for( $i = 1; $i < $total; $i++ )
			{
				echo ", ".(  $result->list[$i] ? "\"true\"" : "\"false\"" );
			}
		}
		
		echo "] }";
	}
}

?>