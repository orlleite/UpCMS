<?php

/**
 * This file is 'the-inside-of' Util::setTables.
 * @author Orlando Leite
 * @version 0.8
 * @package core
 * @subpackage internal
 * @access public
 * @see Util::setTables
 * @name AdminTool setTables
 */
global $UpCMS, $up_prefix, $db_prefix;

function parseTable( $table, $edit, $remove )
{
	global $UpCMS, $up_prefix, $db_prefix;
	
	$tables = implode( ";", $UpCMS->db->tables() );
	
	if( strpos( $tables, $db_prefix.$table["reltable"] ) === false )
	{
		$exiting = "";
		$fields = array();
		$search = $table->xpath( "field|group/field" );
		
		foreach( $search as $field )
		{
			if( strpos( $exiting, $field["rel"] ) === false )
			{
				switch( $field["type"] )
				{
					case "id":
						$fields[(string)$field["rel"]] = array( "type" => "int", "unsigned" => true );
						break;
					
					case "file":
					case "image":
					case "select":
					case "switch":
					case "options":
					case "simpletext":
						$fields[(string)$field["rel"]] = array( "type" => "chars" );
						break;
					
					case "color":
						$fields[(string)$field["rel"]] = array( "type" => "chars" );
						break;
					
					case "password":
						$fields[(string)$field["rel"]] = array( "type" => "chars" );
						break;
					
					case "text":
					case "html":
					case "simplehtml":
						$fields[(string)$field["rel"]] = array( "type" => "text" );
						break;
					
					case "tags":
					case "categories":
						$UpCMS->db->execute( "INSERT INTO ".$up_prefix."array ( name, value ) VALUES ( '".$field["from"]."', '' )" );
						$fields[(string)$field["rel"]] = array( "type" => "text" );
						break;
					
					case "datetime":
						$fields[(string)$field["rel"]] = array( "type" => "timestamp" );
						break;
					
					case "table":
					case "simpletable":
						$relField = $field->addChild( "field" );
						$relField->addAttribute( "type", "id" );
						$relField->addAttribute( "rel", "rel_".$table["reltable"] );
						parseTable( $field, $edit, false );
						break;
					
					default:
						$fields[(string)$field["rel"]] = array( "type" => "text" );
						break;
				}
				
				$exiting .= $field["rel"].";";
			}
		}
		
		if( $table["permission"] == "strict" )
		{
			$fields["created_by"] = array( "type" => "int" );
			$fields["created_at"] = array( "type" => "timestamp" );
			$fields["edited_by"] = array( "type" => "int" );
			$fields["edited_at"] = array( "type" => "timestamp" );
		}
		
		$UpCMS->db->createTable( $db_prefix.$table["reltable"], $fields );
	}
	else
	{
		$oldFields = $newFields = "";
		$list = $UpCMS->db->columns( $db_prefix.$table["reltable"] );
		foreach( $list as $item ) $oldFields .= (string)$item[0].";";
		
		$exiting = "";
		$fields = array();
		$search = $table->xpath( "field|group/field" );
		
		foreach( $search as $field )
		{
			$newFields .= (string)$field["rel"].";";
			
			if( strpos( $exiting, (string)$field["rel"] ) === false and ( strpos( $oldFields, (string)$field["rel"] ) === false or $edit == true ) )
			{
				$opp = strpos( $oldFields, (string)$field["rel"] ) !== false ? "change" : "add";
				
				switch( $field["type"] )
				{
					case "id":
						$fields[(string)$field["rel"]] = array( "do" => $opp, "type" => "int", "unsigned" => true );
						break;
					
					case "file":
					case "image":
					case "select":
					case "switch":
					case "simpletext":
						$fields[(string)$field["rel"]] = array( "do" => $opp, "type" => "chars" );
						break;
					
					case "color":
						$fields[(string)$field["rel"]] = array( "do" => $opp, "type" => "chars" );
						break;
					
					case "password":
						$fields[(string)$field["rel"]] = array( "do" => $opp, "type" => "chars" );
						break;
					
					case "text":
					case "html":
					case "simplehtml":
						$fields[(string)$field["rel"]] = array( "do" => $opp, "type" => "text" );
						break;
					
					case "tags":
					case "categories":
						$UpCMS->db->execute( "INSERT INTO ".$up_prefix."array ( name ) VALUES ( '".$field["from"]."' )" );
						$fields[(string)$field["rel"]] = array( "do" => $opp, "type" => "text" );
						break;
					
					case "datetime":
						$fields[(string)$field["rel"]] = array( "do" => $opp, "type" => "timestamp" );
						break;
					
					case "table":
					case "simpletable":
						$relField = $field->addChild( "field" );
						$relField->addAttribute( "type", "id" );
						$relField->addAttribute( "rel", "rel_".$table["reltable"] );
						parseTable( $field, $edit, false );
						break;
					
					default:
						break;
				}
				
				$exiting .= $field["rel"].";";
			}
		}
		
		if( $table["permission"] == "strict" )
		{
			if( strpos( $oldFields, "created_by" ) === false )
				$fields["created_by"] = array( "do" => "add", "type" => "int" );
			else if( $edit )
				$fields["created_by"] = array( "do" => "change", "type" => "int" );
			
			if( strpos( $oldFields, "created_at" ) === false )
				$fields["created_at"] = array( "do" => "add", "type" => "timestamp" );
			else if( $edit )
				$fields["created_at"] = array( "do" => "change", "type" => "timestamp" );
			
			if( strpos( $oldFields, "edited_by" ) === false )
				$fields["edited_by"] = array( "do" => "add", "type" => "int" );
			else if( $edit )
				$fields["edited_by"] = array( "do" => "add", "type" => "int" );
			
			if( strpos( $oldFields, "edited_at" ) === false )
				$fields["edited_at"] = array( "do" => "add", "type" => "timestamp" );
			else if( $edit )
				$fields["edited_at"] = array( "do" => "change", "type" => "timestamp" );
			
			$newFields .= "created_by;created_at;edited_by;edited_at;";
		}
		
		if( $remove == true )
		{
			foreach( $list as $item )
			{
				$name = (string)$item[0];
				if( strpos( $newFields, $name ) === false and $name != "id" ) $fields[(string)$field["rel"]] = array( "do" => "drop" );
			}
		}
		
		if( count( $fields ) != 0 )
		{
			$UpCMS->db->editTable( $db_prefix.$table["reltable"], $fields );
		}
	}
}

foreach( $UpCMS->config->table as $table )
{
	parseTable( $table, $edit, $remove );
}

?>