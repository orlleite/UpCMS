<?php

/**
 * Make changes in db content based in what is required and how much permission the user logged have.
 * @author Orlando Leite
 * @version 0.8
 * @package core
 * @subpackage cms
 * @access public
 * @name ApplicationEdit
 */
class ApplicationEdit
{
	/**
    * Some fields type can have dynamic options. This function get that values.
    * @author Orlando Leite
    * @access protected
    * @static
    * @param string $prefix db prefix used, ordinary can be $up_prefix or $db_prefix
    * @param object $target a target field
    * @param string related node section
    * @param int related id of current node
    * @param array $array currently array of values. the dynamic values will be added to this array.
    * @return array
    */
	protected static function getDynamicOptions( $prefix, $target, $rel, $id, $array )
	{
		global $UpCMS;
		
		$table = $prefix.$target['reltable'];
		$value = isset( $target['value'] );
		$where = ( isset( $target['where'] ) ? ' WHERE '.$target['where'] : '' );
		
		if( $target['linked'] == 'true' )
		{
			$linked = $target['reltable'].'.rel_'.$rel.' = '.$id;
			if( $where == '' )
				$where = ' WHERE '.$linked;
			else
				$where = ' AND '.$linked;
		}
		
		$query = 'SELECT '.$table.'.'.$target['name'].
				( $value ? ', '.$table.'.'.$target['value'] : '' ).
				' FROM '.$table.$where;
		
		$UpCMS->db->execute( $query );
		
		while( $row = $UpCMS->db->row( DB_NUM ) )
		{
			$array[(string) $value ? $row[1] : $row[0]] = $row[0];
		}
		
		return $array;
	}
	
	/**
    * When a table have strict permission this function will be called, which adds created_by, created_at, edited_by and edited_at fields.
    * @author Orlando Leite
    * @access protected
    * @static
    * @param object $target a target table
    * @return void
    */
	protected static function addStrictPermission( $target )
	{
		global $Language, $UpCMS;
		
		$temp = $UpCMS->config->xpath( "//gui" );
		$box = $temp[0]->addChild( "box" );
		$box->addAttribute( "position", "sidebar" );
		$box->addAttribute( "rel", "edition_details" );
		$box->addAttribute( "name", $Language->details );
		
		// CREATED BY //
		$field = $target->addChild( "field" );
		$field->addAttribute( "name", $Language->created_by );
		$field->addAttribute( "box", "edition_details" );
		$field->addAttribute( "type", "simpletext" );
		$field->addAttribute( "rel", "created_by" );
		$field->addAttribute( "strict", "true" );
		
		// CREATED AT //
		$field = $target->addChild( "field" );
		$field->addAttribute( "name", $Language->created_at );
		$field->addAttribute( "box", "edition_details" );
		$field->addAttribute( "rel", "created_at" );
		$field->addAttribute( "type", "datetime" );
		$field->addAttribute( "strict", "true" );
		
		// EDITED BY //
		$field = $target->addChild( "field" );
		$field->addAttribute( "name", $Language->edited_by );
		$field->addAttribute( "box", "edition_details" );
		$field->addAttribute( "quickedit", "enabled" );
		$field->addAttribute( "type", "simpletext" );
		$field->addAttribute( "rel", "edited_by" );
		$field->addAttribute( "strict", "true" );
		
		// EDITED AT //
		$field = $target->addChild( "field" );
		$field->addAttribute( "name", $Language->edited_at );
		$field->addAttribute( "box", "edition_details" );
		$field->addAttribute( "quickedit", "enabled" );
		$field->addAttribute( "type", "datetime" );
		$field->addAttribute( "rel", "edited_at" );
		$field->addAttribute( "strict", "true" );
	}
	
	/**
    * Get the 'created_by' of a $id in a $table
    * @author Orlando Leite
    * @access public
    * @static
    * @param string $table a table name with prefix. e.g. 'sys_group'
    * @param integer $id the id, properly. Can be integer or string
    * @return integer id of the owner
    */
	public static function getItemOwner( $table, $id )
	{
		global $UpCMS;
		
		$UpCMS->db->execute( "SELECT ".$table.".created_by FROM ".$table." WHERE ".$table.".id = ".$id );
		$temp = $UpCMS->db->row();
		return $temp["created_by"];
	}
	
	/**
    * Set global $result for new-form. The selected front should uses this $result to show the a new-form. Variables used here come from $_POST.
    *
    * The steps are:
    * - Dispatch a event EDIT_BEFORE_GETNEW;
    * - Get the relation (POST:rel) value to get the table in $UpCMS->config. The relation can be a path 'mytable.mygallery=10' what means table('mytable')-> field('mygallery') who have relation by id '10';
    * - Get permission, if strict call addStrictPermission, if is already possible determine if permission is denied, the execution stop here setting $result for error;
    * - Create the fields and groups using a hard foreach;
    * - Save in $result and dispatch a event EDIT_AFTER_GETNEW;
    * 
    * @author Orlando Leite
    * @access public
    * @static
    * @return void
    */
	public static function getNew()
	{
		// GET EXTERNAL VARIABLES //
		global $UpCMS, $db_prefix, $up_prefix, $Language;
		
		// DISPATCH START EVENT //
		$UpCMS->dispatchEvent( new Event( UpCMS::EDIT_BEFORE_GETNEW, NULL ) );
		
		// RELATION TABLE //
		$relation = $_POST['rel'];
		$id = 0;
		
		// IF RELATION IS A PATH (ex:gallery.image=10) //
		if( strpos( $relation, '.' ) !== false )
		{
			$temp = explode( '=', $relation );
			$relation = reset( $temp );
		}
		
		$relPermission = reset( explode( '.', $relation ) );
		
		// IF RELATION IS A SYSTEM TABLE (like users and groups) //
		if( strpos( $relation, 'system_' ) === 0 )
		{
			$prefix = $up_prefix;
			include_once( 'core/internal/System.php' );
		}
		else
		{
			$prefix = $db_prefix;
		}
		
		// GET PERMISSION //
		if( !$UpCMS->user->ownwrite( $relPermission ) )
		{
			global $result;
			
			$result->status = 'error';
			$result->error = 'access denied';
			return;
		}
		
		// GET A BIT OF CONFIG XML //
		$temp = '//table[@rel=\''.str_replace( '.', '\']//field[@rel=\'', $relation ).'\']';
		$temp = $UpCMS->config->xpath( $temp );
		
		$target = $temp[0];
		$table = $prefix.$target['reltable'];
		$name = $target['name'];
		$rel = $target['rel'];
		$guis = array();
		
		$t->name = 'id';
		$fields['id'] = $t;
		
		// GET FIELDS AND GROUPS FROM TABLE //
		$tboxs = ' ';
		$total = count( $target );
		
		foreach( $target as $k => $n )
		{
			if( $n['box'] != '' )
			{
				$b = new stdClass();
				if( strpos( $tboxs, (string) $n["box"] ) == false )
				{
					$tboxs .= $target[$i]["box"];
					$box = $UpCMS->config->xpath( "//gui/box[@rel='".$n["box"]."']" );
					$b->position	= (string) $box[0]["position"];
					$b->name		= (string) $box[0]["name"];
					$guis[(string)$box[0]["rel"]] = $b;
				}
			}
			
			if( $k == "group" )
			{
				$t = new stdClass();
				$t->type	= "group";
				$t->rel		= (string) $n["rel"];
				$t->box		= (string) $n["box"];
				$t->value	= (string) $n["value"];
				$t->params	= (string) $n["params"];
				$t->display	= ( (string) $n["display"] ) == 'false' ? false : true;
				if( $n["validate"] ) $t->validate = (string) $n["validate"];
				
				foreach( $n as $f )
				{
					$a = new stdClass();
					$a->box		= (string) $f["box"];
					$a->name	= (string) $f["name"];
					$a->type	= (string) $f["type"];
					$a->value	= (string) $f["value"];
					$a->params	= (string) $f["params"];
					$a->display	= ( (string) $f["display"] ) == "false" ? false : true;
					if( $f["validate"] ) $a->validate = (string) $f["validate"];
					
					if( $f["position"] ) $a->position = (string) $f["position"];
					
					if( $a->type == "select" or $a->type == "options" )
					{
						$a->options = array();
						
						if( isset( $f->dynamic ) ) $a->options = ApplicationEdit::getDynamicOptions( $prefix, $f->dynamic, $rel, $id, $a->options );
						
						foreach( $f->option as $opt ) $a->options[(string) $opt["value"]] = (string) $opt["name"];
					}
					elseif( $a->type == "categories" or $a->type == "tags" )
					{
						$c["value"] = "name";
						$c["name"] = "value";
						$c["reltable"] = "array";
						$c["where"] = "name='".$f["from"]."'";
						$result = ApplicationEdit::getDynamicOptions( $up_prefix, $c, $rel, $id, array() );
						
						$a->options = reset( $result );
					}
					
					$t->fields[(string) $f["rel"]] = $a;
				}
				
				$fields[(string) $n["name"]] = $t;
			}
			else
			{
				$t = new stdClass();
				$t->box		= (string) $n["box"];
				$t->name	= (string) $n["name"];
				$t->type	= (string) $n["type"];
				$t->value	= (string) $n["value"];
				$t->params	= (string) $n["params"];
				$t->display	= ( (string) $n["display"] ) == "false" ? false : true;
				if( $n["validate"] ) $t->validate = (string) $n["validate"];
				
				if( $n["position"] ) $t->position = (string) $n["position"];
				
				if( $t->type == "select" or $t->type == "options" or $t->type == "switch" )
				{
					$t->options = array();
					
					if( isset( $n->dynamic ) ) $t->options = ApplicationEdit::getDynamicOptions( $prefix, $n->dynamic, $rel, $id, $t->options );
					
					foreach( $n->option as $opt ) $t->options[(string) $opt["value"]] = (string) $opt["name"];
				}
				elseif( $t->type == "categories" or $t->type == "tags" )
				{
					$c["value"] = "name";
					$c["name"] = "value";
					$c["reltable"] = "array";
					$c["where"] = "name='".$n["from"]."'";
					$result = ApplicationEdit::getDynamicOptions( $up_prefix, $c, $rel, $id, array() );
					
					$t->options = reset( $result );
				}
				
				$fields[(string) $n["rel"]] = $t;
			}
		}
		
		// SET RESULT //
		global $result;
		
		$result->fields = $fields;
		$result->guis 	= $guis;
		$result->id		= (string) $id;
		$result->rel	= (string) $relation;
		$result->name	= (string) $name;
		$result->icon	= (string) $target["icon"];
		
		// DISPATCH FINISH EVENT //
		$UpCMS->dispatchEvent( new Event( UpCMS::EDIT_AFTER_GETNEW, NULL ) );
	}
	
	/**
    * Set global $result for edit-form. The selected front should uses this $result to show the a edit-form. Variables used here come from $_POST.
    *
    * The steps are:
    * - Dispatch a event EDIT_BEFORE_GETEDIT;
    * - Get the relation (POST:rel) value to get the table in $UpCMS->config. The relation can be a path 'mytable.mygallery=10' what means table('mytable')-> field('mygallery') who have relation by id '10';
    * - Get permission, if strict call addStrictPermission, if is already possible determine if permission is denied, the execution stop here setting $result for error;
    * - Create the fields and groups using a hard foreach;
    * - Populate the $result field and groups by the values doing a SELECT by id (POST:id) using another hard foreach
    * - Save in $result and dispatch a event EDIT_AFTER_GETEDIT;
    * 
    * @author Orlando Leite
    * @access public
    * @static
    * @return void
    */
	public static function getEdit()
	{
		// GET EXTERNAL VARIABLES //
		global $UpCMS, $db_prefix, $up_prefix, $Language;
		
		// DISPATCH START EVENT //
		$UpCMS->dispatchEvent( new Event( UpCMS::EDIT_BEFORE_GETEDIT, NULL ) );
		
		// RELATION TABLE AND ID //
		$id = $rid = $_POST['id'];
		$relation = $_POST['rel'];
		
		// IF RELATION IS A PATH (ex:gallery.image=10) //
		if( strpos( $relation, "." ) !== false )
		{
			$temp = explode( "=", $relation );
			$relation = reset( $temp );
			$rid = end( $temp );
		}
		
		$a = explode( ".", $relation );
		$relPermission = reset( $a );
		
		// IF RELATION IS A SYSTEM  TABLE (like users and groups) //
		if( strpos( $relation, "system_" ) === 0 )
		{
			$prefix = $up_prefix;
			include_once( "core/internal/System.php" );
		}
		else
		{
			$prefix = $db_prefix;
		}
		
		// GET A BIT OF CONFIG XML //
		$temp = "//table[@rel='".str_replace( ".", "']//field[@rel='", $relation )."']";
		$temp = $UpCMS->config->xpath( $temp );
		
		$target = $temp[0];
		$table = $prefix.$target['reltable'];
		$permission = $target['permission'];
		$name = $target['name'];
		$rel = $relPermission;
		$guis = array();
		
		// GET PERMISSION //
		if( $permission == 'strict' )
		{
			self::addStrictPermission( $target );
			
			if( $UpCMS->user->anywrite( $relPermission ) )
				$permission = 'any';
			else if( $UpCMS->user->ownwrite( $relPermission ) or self::getItemOwner( $table, $id ) == $UpCMS->user->info( 'id' ) )
				$permission = 'own';
			else if( $relation == 'system_users' and $id == $UpCMS->user->info( 'id' ) )
			{
				$permission = 'own';
				list($t) = $target->xpath( '//field[@rel=\'group\']' );
				$a = dom_import_simplexml( $t );
				$a->parentNode->removeChild( $a );
				
				list($t) = $target->xpath( '//field[@rel=\'username\']' );
				$t->addAttribute( 'strict', 'true' );
			}
			else
			{
				global $result;
			
				$result->status = 'error';
				$result->error = 'access denied';
				return;
			}
		}
		else if( $UpCMS->user->ownwrite( $relPermission ) )
		{
			$permission = 'any';
		}
		else
		{
			global $result;
			
			$result->status = 'error';
			$result->error = 'access denied';
			return;
		}
		
		// GET FIELDS AND GROUPS FROM TABLE //
		$t = new stdClass();
		$t->name = 'id';
		$fields['id'] = $t;
		$query = 'SELECT a.id';
		
		$tboxs = ' ';
		foreach( $target as $k => $n )
		{
			if( $n["box"] != "" )
			{
				$b = new stdClass();
				if( strpos( $tboxs, (string) $n["box"] ) == false )
				{
					$tboxs .= $n["box"];
					$box = $UpCMS->config->xpath( "//gui/box[@rel='".$n["box"]."']" );
					$b->position	= (string) $box[0]->position;
					$b->name		= (string) $box[0]->name;
					$guis[(string)$box[0]->rel] = $b;
				}
			}
			
			if( $k == "group" )
			{
				$t = new stdClass();
				$t->type	= "group";
				$t->rel		= (string) $n["rel"];
				$t->box		= (string) $n["box"];
				$t->value	= (string) $n["value"];
				$t->params	= (string) $n["params"];
				$t->display	= ( (string) $n["display"] ) == "false" ? false : true;
				
				if( $n["validate"] ) $t->validate = (string) $n["validate"];
				
				foreach( $n as $f )
				{
					$a = new stdClass();
					$a->box		= (string) $f["box"];
					$a->name	= (string) $f["name"];
					$a->type	= (string) $f["type"];
					$a->value	= (string) $f["value"];
					$a->params	= (string) $f["params"];
					$a->display	= ( (string) $f["display"] ) == "false" ? false : true;
					
					if( $f["validate"] ) $a->validate = (string) $f["validate"];
					if( $f["position"] ) $a->position = (string) $f["position"];
					
					if( $a->type == "select" or $a->type == "options" )
					{
						$a->options = array();
						
						if( isset( $f->dynamic ) ) $a->options = ApplicationEdit::getDynamicOptions( $prefix, $f->dynamic, $rel, $rid, $a->options );
						
						foreach( $f->option as $opt ) $a->options[(string) $opt["value"]] = (string) $opt["name"];
					}
					elseif( $a->type == "categories" or $a->type == "tags" )
					{
						$c["value"] = "name";
						$c["name"] = "value";
						$c["reltable"] = "array";
						$c["where"] = "name='".$f["from"]."'";
						$result = ApplicationEdit::getDynamicOptions( $up_prefix, $c, $rel, $rid, array() );
						
						$a->options = reset( $result );
					}
					
					$t->fields[(string) $f["rel"]] = $a;
					
					if( $a->type != "table" and $a->type != "simpletable" ) $query .= ", a.".$f["rel"];
				}
				
				$fields[(string) $n["name"]] = $t;
			}
			else
			{
				$t = new stdClass();
				$t->box		= (string) $n["box"];
				$t->name	= (string) $n["name"];
				$t->type	= (string) $n["type"];
				$t->value	= (string) $n["value"];
				$t->params	= (string) $n["params"];
				$t->display	= ( (string) $n["display"] ) == "false" ? false : true;
				// echo $n["validate"]."\n";
				if( $n["validate"] ) $t->validate = (string) $n["validate"];
				if( $n["position"] ) $t->position = (string) $n["position"];
				
				if( $t->type == "select" or $t->type == "options" or $t->type == "switch" )
				{
					$t->options = array();
					
					if( isset( $n->dynamic ) ) $t->options = ApplicationEdit::getDynamicOptions( $prefix, $n->dynamic, $rel, $rid, $t->options );
					
					foreach( $n->option as $opt ) 
						$t->options[(string) $opt["value"]] = (string) $opt["name"];
				}
				elseif( $t->type == "categories" or $t->type == "tags" )
				{
					$c["value"] = "name";
					$c["name"] = "value";
					$c["reltable"] = "array";
					$c["where"] = "name='".$n["from"]."'";
					$result = ApplicationEdit::getDynamicOptions( $up_prefix, $c, $rel, $rid, array() );
					
					$t->options = reset( $result );
				}
				
				$fields[(string) $n["rel"]] = $t;
				
				if( (string) $n["strict"] == "true" ) $t->strict = true;
				
				if( ( (string) $n["rel"] == "created_by" || (string) $n["rel"] == "edited_by" ) and (string) $n["strict"] == "true" )
					$query .= ", ( SELECT ".$up_prefix."users.username FROM ".$up_prefix."users WHERE ".$up_prefix."users.id = a.".$n["rel"]." ) AS ".$n["rel"];
				else if( $t->type != "table" and $t->type != "simpletable" )
					$query .= ", a.".$n["rel"];
			}
		}
		
		// SET QUERY ID AND EXECUTE //
		$query .= " FROM ".$table." AS a WHERE id = '".$id."'";
		$UpCMS->db->execute( $query );
		$dbReturn = $UpCMS->db->row();
		
		// FILL FIELDS AND SET RESULT //
		global $result;
		
		if( empty( $dbReturn ) )
		{
			$result->status = 'error';
			$result->error = $UpCMS->db->error();
		}
		else
		{
			$result = new stdClass();
			$result->save = true;
			if( $permission == "strict" and $dbReturn["created_by"] != $UpCMS->user->info( "id" ) and !$UpCMS->user->anywrite( $relation ) )
			{
				if( $UpCMS->user->anyread( $relation ) )
					$result->save = false;
				else
				{
					global $result;
				
					$result->status = "error";
					$result->error = "access denied";
					return;
				}
			}
			
			foreach( $fields as $k => $t )
			{
				if( @$t->type == "group" )
				{
					foreach( $t->fields as $a => $v )
					{
						if( $v->type != "password" ) $v->value = $dbReturn[$a];
					}
				}
				else if( @$t->type != "password" ) 
					$t->value = $dbReturn[$k];
			}
			
			
			$result->fields	= $fields;
			$result->guis	= $guis;
			$result->id		= (string) $id;
			$result->rel	= (string) $relation;
			$result->name	= (string) $name;
			$result->icon	= (string) $target["icon"];
		}
		
		$UpCMS->dispatchEvent( new Event( UpCMS::EDIT_AFTER_GETEDIT, NULL ) );
	}
	
	/**
    * Save values from a submited form and set global $result for success or error. Variables used here come from $_POST.
    *
    * The steps are:
    * - Dispatch a event EDIT_BEFORE_SAVE;
    * - Get the relation (POST:rel) value to get the table in $UpCMS->config. The relation can be a path 'mytable.mygallery=10' what means table('mytable')-> field('mygallery') who have relation by id '10';
    * - Get permission, if strict call addStrictPermission, if is already possible determine if permission is denied, the execution stop here setting $result for error;
    * - INSERT INTO db the submited form parsed by POST;
    * - Set $result to success and the new id or error;
    * - Dispatch a event EDIT_AFTER_SAVE;
    * 
    * @author Orlando Leite
    * @access public
    * @static
    * @return void
    */
	public static function save()
	{
		// GET EXTERNAL VARIABLES //
		global $UpCMS, $db_prefix, $up_prefix, $Language;
		
		// DISPATCH START EVENT //
		$UpCMS->dispatchEvent( new Event( UpCMS::EDIT_BEFORE_SAVE, NULL ) );
		
		// RELATION TABLE //
		$relation = $_POST['rel'];
		$rel_query_part = "";
		$folder = $relation;
		$where = "";
		
		// IF RELATION IS A SYSTEM  TABLE (like users and groups) //
		if( strpos( $relation, "system_" ) === 0 )
		{
			$prefix = $up_prefix;
			include_once( "core/internal/System.php" );
		}
		else
		{
			$prefix = $db_prefix;
		}
		
		// IF RELATION IS A PATH (ex:gallery.image=10) //
		if( strpos( $relation, "." ) !== false )
		{
			$values = explode( "=", $relation );
			$relation = reset( $values );
			
			$tempRelation = explode( ".", $relation );
			$folder = end( $tempRelation );
			$relPermission = reset( $tempRelation );
			
			$temp = "//table[@rel='".str_replace( ".", "']//field[@rel='", $relation )."']";
			$temp = $UpCMS->config->xpath( $temp );
			
			$table = $prefix.$temp[0]["reltable"];
			$permission = $temp[0]["permission"];
			$target = $temp[0];
			
			if( count( $values ) > 1 )
			{
				$rel_query_part .= "rel_".array_shift( $tempRelation )." = '".end( $values )."'";
				$where .= " WHERE u.rel_".$rel." = ".end( $values );
			}
		}
		else
		{
			$relPermission = reset( explode( ".", $relation ) );
			$temp = "//table[@rel='".str_replace( ".", "']//field[@rel='", $relation )."']";
			$temp = $UpCMS->config->xpath( $temp );
			
			$table = $prefix.$temp[0]["reltable"];
			$permission = $temp[0]["permission"];
			$target = $temp[0];
		}
		
		// GET PERMISSION //
		if( $permission == "strict" )
		{
			self::addStrictPermission( $target );
			
			if( $UpCMS->user->ownwrite( $relPermission ) )
				$permission = "own";
			else
			{
				global $result;
			
				$result->status = "error";
				$result->error = "access denied";
				return;
			}
		}
		else if( $UpCMS->user->ownwrite( $relPermission ) )
		{
			$permission = "any";
		}
		else
		{
			global $result;
			
			$result->status = "error";
			$result->error = "access denied";
			return;
		}
		
		// FIRST, CREATE ITEM //
		$UpCMS->db->insertEmptyRow( $table );
		// $add = $UpCMS->db->execute( $query );
		// if( empty( $dbReturn ) ) echo $UpCMS->db->error();
		
		// SECOND, CHANGE FILES FROM TEMP FOLDER TO CORRECT FOLDER AND PUT THE CORRECTS VALUES //
		$query = "UPDATE ".$table." SET ";
		$id = $UpCMS->db->lastID();
		$first = true;
		
		if( $rel_query_part != "" ) $query .= $rel_query_part.", ";
		
		foreach( $target as $k => $n )
		{
			if( $k == "group" )
			{
				if( $_POST[(string)$n["rel"]] == (string) $n["value"] )
				{
					foreach( $n as $f )
					{
						$v = (string) $f["value"];
						$t = (string) $f["type"];
						$r = (string) $f["rel"];
						
						if( $t != "table" and $t != "simpletable" )
						{
							if( $v == "" )
							{
								$query .= $first ? "" : ", ";
								
								if( $t == "datetime" )
									$query .= $r."".( $_POST[$r] == "" ? " = NOW()" : " = '".date( "Y-m-d H:i:00", strtotime( $_POST[$r] ) )."'" );
								else if( $t == "image" or $t == "file" )
									$query .= $r." = '".Util::movefiles( addslashes( $_POST[$r] ), $folder, $id, "text" )."'";
								else if( $t == "html" or $t == "simplehtml" or $t == "text" )
									$query .= $r." = '".Util::movefiles( addslashes( $_POST[$r] ), $folder, $id, "html" )."'";
								else if( $t == "password" )
									$query .= $r." = SHA1('".addslashes( $_POST[$r] )."')";
								else
									$query .= $r." = '".addslashes( $_POST[$r] )."'";
								
								$first = false;
							}
							else if( $v != "" and $t != "switch" and (string) $k != "group" )
							{
								$query .= $first ? "" : ", ";
								
								$where = ( strlen( $where ) == 0 ? " WHERE " : " AND " )."u.".$r." = '".$v."'";
								$query .= $r." = '".$v."'";
								$first = false;
							}
						}
					}
				}
			}
			else
			{
				$v = (string) $n["value"];
				$t = (string) $n["type"];
				$r = (string) $n["rel"];
				
				if( $t != "table" and $t != "simpletable" and (string) $n["strict"] != "true" )
				{
					$query .= $first ? "" : ", ";
					
					if( $v == "" )
					{
						if( $t == "datetime" )
							$query .= $r."".( $_POST[$r] == "" ? " = NOW()" : " = '".date( "Y-m-d H:i:00", strtotime( $_POST[$r] ) )."'" );
						else if( $t == "image" or $t == "file" )
							$query .= $r." = '".Util::movefiles( addslashes( $_POST[$r] ), $folder, $id, "text" )."'";
						else if( $t == "html" or $t == "simplehtml" or $t == "text" )
							$query .= $r." = '".Util::movefiles( addslashes( $_POST[$r] ), $folder, $id, "html" )."'";
						else if( $t == "password" )
							$query .= $r." = SHA1('".addslashes( $_POST[$r] )."')";
						else
							$query .= $r." = '".addslashes( $_POST[$r] )."'";
						
						$first = false;
					}
					else if( $v != "" and $t != "switch" and (string) $k != "group" )
					{
						$where = ( strlen( $where ) == 0 ? " WHERE " : " AND " )."u.".$r." = '".$v."'";
						$query .= $r." = '".$v."'";
						$first = false;
					}
				}
				else if( (string) $n["strict"] == "true" )
				{
					$query .= $first ? "" : ", ";
					
					if( $r == "created_by" or $r == "edited_by" )
					{
						$query .= $r." = '".$UpCMS->user->info( "id" )."'";
						$first = false;
					}
					else if( $r == "created_at" or $r == "edited_at" )
					{
						$query .= $r." = NOW()";
						$first = false;
					}
					else
					{
						$first = true;
					}
				}
			}
		}
		
		$execute = true;
		$error = '';
		
		if( $target['limit'] )
		{
			$length = current( $UpCMS->db->select( "SELECT COUNT(*) FROM ".$table." AS u".$where, DB_NUM ) );
			$length = is_array( $length ) ? current( $length ) : 0;
			
			if( $length > ( (int) $target['limit'] ) )
			{
				$UpCMS->db->execute( 'DELETE FROM '.$table.' WHERE id='.$id );
				$execute = false;
				$error = $Language->limitExceeded;
			}
		}
		
		// SET RESULT //
		global $result;
		
		if( $execute )
		{
			// SET QUERY ID AND EXECUTE //
			$query .= " WHERE id = ".$id;
			$dbReturn = $UpCMS->db->execute( $query );
			
			if( empty( $dbReturn ) )
			{
				$result->status = 'error';
				$result->error = $UpCMS->db->error();
			}
			else
			{
				$result->status	= 'success';
				$result->edit	= $dbReturn;
				$result->id		= $id;
				$result->rel	= $relation;
			}
		}
		else
		{
			$result->status = 'error';
			$result->error = $error;
		}
		
		$UpCMS->dispatchEvent( new Event( UpCMS::EDIT_AFTER_SAVE, NULL ) );
	}
	
	/**
    * UPDATE values from a submited form and set global $result for success or error. Variables used here come from $_POST.
    *
    * The steps are:
    * - Dispatch a event EDIT_BEFORE_UPDATE;
    * - Get the relation (POST:rel) value to get the table in $UpCMS->config. The relation can be a path 'mytable.mygallery=10' what means table('mytable')-> field('mygallery') who have relation by id '10';
    * - Get permission, if strict call addStrictPermission, if is already possible determine if permission is denied, the execution stop here setting $result for error;
    * - UPDATE db the submited form parsed by POST;
    * - Set $result to success or error and dispatch a event EDIT_AFTER_UPDATE;
    * 
    * @author Orlando Leite
    * @access public
    * @static
    * @return void
    */
	public static function update()
	{
		// GET EXTERNAL VARIABLES //
		global $UpCMS, $db_prefix, $up_prefix, $Language;
		
		// DISPATCH START EVENT //
		$UpCMS->dispatchEvent( new Event( UpCMS::EDIT_BEFORE_UPDATE, NULL ) );
		
		// RELATION TABLE //
		$relation = $_POST['rel'];
		$id = $_POST['id'];
		
		// IF RELATION IS A PATH (ex:gallery.image=10) //
		if( strpos( $relation, "." ) !== false )
		{
			$temp = explode( "=", $relation );
			$relation = reset( $temp );
		}
		
		// IF RELATION IS A SYSTEM  TABLE (like users and groups) //
		if( strpos( $relation, "system_" ) === 0 )
		{
			$prefix = $up_prefix;
			include_once( "core/internal/System.php" );
		}
		else
		{
			$prefix = $db_prefix;
		}
		
		// GET A BIT OF CONFIG XML //
		$temp = "//table[@rel='".str_replace( ".", "']//field[@rel='", $relation )."']";
		$temp = $UpCMS->config->xpath( $temp );
		
		$table = $prefix.$temp[0]["reltable"];
		$permission = $temp[0]["permission"];
		$target = $temp[0];
		
		$relPermission = reset( explode( ".", $relation ) );
		
		// GET PERMISSION //
		if( $permission == "strict" )
		{
			self::addStrictPermission( $target );
			
			if( $UpCMS->user->anywrite( $relPermission ) )
				$permission = "any";
			else if( $UpCMS->user->ownwrite( $relPermission ) or self::getItemOwner( $table, $id ) == $UpCMS->user->info( "id" ) )
				$permission = "own";
			else if( $relation == "system_users" and $id == $UpCMS->user->info( "id" ) )
			{
				$permission = "own";
				list($t) = $target->xpath( "//field[@rel='group']" );
				$a = dom_import_simplexml( $t );
				$a->parentNode->removeChild( $a );
			}
			else
			{
				global $result;
			
				$result->status = "error";
				$result->error = "access denied";
				return;
			}
		}
		else if( $UpCMS->user->ownwrite( $relPermission ) )
		{
			$permission = "any";
		}
		else
		{
			global $result;
			
			$result->status = "error";
			$result->error = "access denied";
			return;
		}
		
		// CREATE QUERY //
		$first = true;
		$query = "UPDATE ".$table." SET ";
		foreach( $target as $k => $n )
		{
			if( $k == "group" )
			{
				if( $_POST[(string)$n["rel"]] == (string) $n["value"] )
				{
					foreach( $n as $f )
					{
						$v = (string) $f["value"];
						$t = (string) $f["type"];
						$r = (string) $f["rel"];
						
						if( isset( $_POST[$r] ) )
						{
							if( $v == "" and $t != "table" and $t != "simpletable" and $t != "password" )
							{
								if( $t == "datetime" )
								{
									$query .= $first ? "" : ", ";
									$query .= $r." = '".date( "Y-m-d H:i:00", strtotime( $_POST[$r] ) )."'";
								}
								else
								{
									$query .= $first ? "" : ", ";
									
									if( $t == "text" or $t == "html" or $t == "simplehtml" )
										$query .= $r." = '".addslashes( $_POST[$r] )."'";
									else
										$query .= $r." = '".addslashes( $_POST[$r] )."'";
								}
								
								$first = false;
							}
							else if( $t == "password" and $_POST[$r] != "" )
							{
								$query .= $first ? "" : ", ";
								$query .= $r." = SHA1('".$_POST[$r]."')";
								$first = false;
							}
							else if( $v != "" )
							{
								$query .= $first ? "" : ", ";
								$query .= $r." = '".$v."'";
								$first = false;
							}
						}
					}
				}
			}
			else
			{
				$v = (string) $n["value"];
				$t = (string) $n["type"];
				$r = (string) $n["rel"];
				
				if( isset( $_POST[$r] ) )
				{
					if( $v == "" and $t != "table" and $t != "simpletable" and $t != "password" and (string) $n["strict"] != "true" )
					{
						if( $t == "datetime" )
						{
							$query .= $first ? "" : ", ";
							$query .= $r." = '".date( "Y-m-d H:i:00", strtotime( $_POST[$r] ) )."'";
						}
						else
						{
							$query .= $first ? "" : ", ";
							
							if( $t == "text" or $t == "html" or $t == "simplehtml" )
								$query .= $r." = '".addslashes( $_POST[$r] )."'";
							else
								$query .= $r." = '".addslashes( $_POST[$r] )."'";
						}
						
						$first = false;
					}
					else if( $t == "password" and $_POST[$r] != "" and (string) $n["strict"] != "true" )
					{
						$query .= $first ? "" : ", ";
						$query .= $r." = SHA1('".$_POST[$r]."')";
						$first = false;
					}
					else if( $v != "" )
					{
						$query .= $first ? "" : ", ";
						$query .= $r." = '".$v."'";
						$first = false;
					}
					else if( (string) $n["strict"] == "true" and $r != "created_by" and $r != "created_at" )
					{
						$query .= $first ? "" : ", ";
						
						if( $r == "edited_by" )
						{
							$query .= $r." = '".$UpCMS->user->info( "id" )."'";
							$first = false;
						}
						else if( $r == "edited_at" )
						{
							$query .= $r." = NOW()";
							$first = false;
						}
						else
							$first = true;
					}
				}
			}
		}
		
		// SET QUERY ID AND EXECUTE //
		$query .= " WHERE ".$table.".id = ".$id;
		$dbReturn = $UpCMS->db->execute( $query );
		
		// SET RESULT //
		global $result;
		
		if( empty( $dbReturn ) )
		{
			$result->status = 'error';
			$result->error = $UpCMS->db->error();
		}
		else
		{
			$result->status = 'success';
			$result->edit	= true;
			$result->id		= $id;
			$result->unique = $target["id"] ? true : false;
			$result->rel	= $relation;
		}
		
		$UpCMS->dispatchEvent( new Event( UpCMS::EDIT_AFTER_UPDATE, NULL ) );
	}
	
	/**
    * UPDATE values of a system array. Ordinary comes from categories or tags. And set global $result for success or error. The selected front should uses this $result to show the a new-form. Variables used here come from $_POST.
    *
    * The steps are:
    * - Dispatch a event EDIT_BEFORE_RESET_ARRAY;
    * - Get the relation (POST:rel) value to get the table in $UpCMS->config. The relation can be a path 'mytable.mygallery=10' what means table('mytable')-> field('mygallery') who have relation by id '10';
    * - Get permission, if strict call addStrictPermission, if is already possible determine if permission is denied, the execution stop here setting $result for error;
    * - UPDATE system array submited form parsed by POST;
    * - Set $result to success or error and dispatch a event EDIT_AFTER_RESET_ARRAY;
    * 
    * @author Orlando Leite
    * @access public
    * @static
    * @return void
    */
	public static function resetArray()
	{
		// GET EXTERNAL VARIABLES //
		global $UpCMS, $db_prefix, $up_prefix;
		
		// DISPATCH START EVENT //
		$UpCMS->dispatchEvent( new Event( UpCMS::EDIT_BEFORE_RESET_ARRAY, NULL ) );
		
		// RELATION TABLE //
		$relation = $_POST['rel'];
		$id = $_POST['id'];
		
		// IF RELATION IS A PATH (ex:gallery.image=10) //
		if( strpos( $relation, "." ) !== false )
		{
			$temp = explode( "=", $relation );
			$relation = reset( $temp );
		}
		
		// GET A BIT OF CONFIG XML //
		$temp = "//table[@rel='".str_replace( ".", "']//field[@rel='", $relation )."']";
		$target = $UpCMS->config->xpath( $temp."//field[@rel='".$_POST["target"]."']" );
		$temp = $UpCMS->config->xpath( $temp );
		
		$table = $prefix.$temp[0]["reltable"];
		$permission = $temp[0]["permission"];
		
		$relPermission = reset( explode( ".", $relation ) );
		
		// GET PERMISSION //
		if( $permission == "strict" )
		{
			if( $UpCMS->user->anywrite( $relPermission ) )
				$permission = "any";
			else if( $UpCMS->user->ownwrite( $relPermission ) or self::getItemOwner( $table, $id ) == $UpCMS->user->info( "id" ) )
				$permission = "own";
			else if( $relation == "system_users" and $id == $UpCMS->user->info( "id" ) )
			{
				$permission = "own";
			}
			else
			{
				global $result;
			
				$result->status = "error";
				$result->error = "access denied";
				return;
			}
		}
		else if( $UpCMS->user->ownwrite( $relPermission ) )
		{
			$permission = "any";
		}
		else
		{
			global $result;
			
			$result->status = "error";
			$result->error = "access denied";
			return;
		}
		
		// CREATE QUERY //
		$first = true;
		$query = "UPDATE ".$up_prefix."array SET value = '".addslashes( $_POST["value"] )."' WHERE name = '".$target[0]["from"]."'";
		$dbReturn = $UpCMS->db->execute( $query );
		
		// SET RESULT //
		global $result;
		
		if( empty( $dbReturn ) )
		{
			$result->status = 'error';
			$result->value = $UpCMS->db->error();
		}
		else
		{
			$result->status = 'success';
			$result->edit	= $dbReturn;
			$result->id		= $id;
			$result->rel	= $relation;
		}
		
		$UpCMS->dispatchEvent( new Event( UpCMS::EDIT_AFTER_RESET_ARRAY, NULL ) );
	}
	
	/**
    * Remove a item from db. And set global $result for success or error. The selected front should uses this $result to show the a new-form. Variables used here come from $_POST.
    *
    * The steps are:
    * - Dispatch a event EDIT_BEFORE_REMOVE;
    * - Get the relation (POST:rel) value to get the table in $UpCMS->config. The relation can be a path 'mytable.mygallery=10' what means table('mytable')-> field('mygallery') who have relation by id '10';
    * - Get permission, if strict call addStrictPermission, if is already possible determine if permission is denied, the execution stop here setting $result for error;
    * - DELETE item based on your id by POST;
    * - Set $result to success or error and dispatch a event EDIT_AFTER_REMOVE;
    * 
    * @author Orlando Leite
    * @access public
    * @static
    * @return void
    */
	public static function remove()
	{
		// GET EXTERNAL VARIABLES //
		global $UpCMS, $db_prefix, $up_prefix, $Language;
		
		// DISPATCH START EVENT //
		$UpCMS->dispatchEvent( new Event( UpCMS::EDIT_BEFORE_REMOVE, NULL ) );
		
		// RELATION TABLE //
		$id = explode( ",", $_POST['id'] );
		$relation = $_POST['rel'];
		
		if( strpos( $relation, "." ) !== false )
		{
			$values = explode( "=", $relation );
			$relation = reset( $values );
		}
		
		// IF RELATION IS A SYSTEM  TABLE (like users and groups) //
		if( strpos( $relation, "system_" ) === 0 )
		{
			$prefix = $up_prefix;
			include_once( "core/internal/System.php" );
		}
		else
		{
			$prefix = $db_prefix;
		}
		
		// IF RELATION IS A PATH (ex:gallery.image=10) //
		$temp = "//table[@rel='".str_replace( ".", "']//field[@rel='", $relation )."']";
		$temp = $UpCMS->config->xpath( $temp );
		
		$table = $prefix.$temp[0]["reltable"];
		$permission = $temp[0]["permission"];
		
		$relPermission = reset( explode( ".", $relation ) );
		
		// GET PERMISSION //
		$list = array();
		$denied = array();
		foreach( $id as $k => $r )
		{
			if( $permission == "strict" )
			{
				if( $UpCMS->user->anywrite( $relation ) )
					array_push( $list, $r );
				else if( $UpCMS->user->ownwrite( $relation ) and self::getItemOwner( $table, $r ) == $UpCMS->user->info( "id" ) )
					array_push( $list, $r );
				else
				{
					array_push( $denied, $r );
				}
			}
			else if( $UpCMS->user->ownwrite( $relation ) )
				array_push( $list, $r );
			else
				array_push( $denied, $r );
		}
		
		$query = "DELETE FROM ".$table." WHERE id = ".implode( " OR id = ", $list );
		
		// EXECUTE QUERY //
		$dbReturn = $UpCMS->db->execute( $query );
		
		// SET RESULT //
		global $result;
		
		if( empty( $dbReturn ) )
		{
			$result->status = 'error';
			$result->value = $UpCMS->db->error();
		}
		else
		{
			$result->status = 'success';
			$result->remove	= $dbReturn;
			$result->id		= $id;
			$result->rel	= $relation;
		}
		
		$UpCMS->dispatchEvent( new Event( UpCMS::EDIT_AFTER_REMOVE, NULL ) );
	}
}

?>