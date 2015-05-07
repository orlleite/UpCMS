<?php

/**
 * List items from a table.
 * @author Orlando Leite
 * @version 0.8
 * @package core
 * @subpackage cms
 * @access public
 * @name ApplicationList
 */
class ApplicationList
{
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
		global $Language;
		
		// EDITED BY //
		$field = $target->addChild( "field" );
		$field->addAttribute( "name", $Language->edited_by );
		$field->addAttribute( "quickedit", "enabled" );
		$field->addAttribute( "type", "simpletext" );
		$field->addAttribute( "rel", "edited_by" );
		$field->addAttribute( "strict", "true" );
		
		// EDITED AT //
		$field = $target->addChild( "field" );
		$field->addAttribute( "name", $Language->edited_at );
		$field->addAttribute( "quickedit", "enabled" );
		$field->addAttribute( "type", "datetime" );
		$field->addAttribute( "rel", "edited_at" );
		$field->addAttribute( "strict", "true" );
		
		// CREATED BY //
		$field = $target->addChild( "field" );
		$field->addAttribute( "name", $Language->created_by );
		$field->addAttribute( "quickedit", "enabled" );
		$field->addAttribute( "type", "simpletext" );
		$field->addAttribute( "rel", "created_by" );
		$field->addAttribute( "strict", "true" );
		
		// CREATED AT //
		$field = $target->addChild( "field" );
		$field->addAttribute( "name", $Language->created_at );
		$field->addAttribute( "quickedit", "enabled" );
		$field->addAttribute( "type", "datetime" );
		$field->addAttribute( "rel", "created_at" );
		$field->addAttribute( "strict", "true" );
	}
	
	/**
    * Fill global $result with authors from a table. The selected front should uses this $result to show them. Variables used here come from $_POST.
    * @author Orlando Leite
    * @access public
    * @static
    * @return void
    */
	public static function authors()
	{
		// GET EXTERNAL VARIABLES //
		global $UpCMS, $db_prefix, $up_prefix;
		
		// DISPATCH START EVENT //
		$UpCMS->dispatchEvent( new Event( UpCMS::LIST_BEFORE_AUTHORS, NULL ) );
		
		// RELATION TABLE //
		$relation = $_POST['rel'];
		$permission = "";
		$limit = true;
		
		// IF RELATION IS A SYSTEM  TABLE (like users and groups) //
		if( strpos( $relation, "system_" ) === 0 )
		{
			global $Language;
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
			$limit = false;
			$values = explode( "=", $relation );
			$relation = reset( $values );
			
			$tempRelation = explode( ".", $relation );
			
			$temp = "//table[@rel='".str_replace( ".", "']//field[@rel='", $relation )."']";
			$temp = $UpCMS->config->xpath( $temp );
			
			$relPermission	= reset( explode( ".", $relation ) );
			$table = $prefix.$temp[0]["reltable"];
			$permission = $temp[0]["permission"];
			$target = $temp[0];
		}
		else
		{
			$temp = "//table[@rel='".str_replace( ".", "']//field[@rel='", $relation )."']";
			$temp = $UpCMS->config->xpath( $temp );
			
			$relPermission	= reset( explode( ".", $relation ) );
			$table = $prefix.$temp[0]["reltable"];
			$permission = $temp[0]["permission"];
			$target = $temp[0];
		}
		
		// GET PERMISSION //
		if( $permission == "strict" )
		{
			self::addStrictPermission( $target );
			
			if( $UpCMS->user->anyread( $relPermission ) )
				$permission = "any";
			else
			{
				global $result;
			
				$result->status = "error";
				$result->error = "access denied";
				return;
			}
		}
		else
		{
			global $result;
			
			$result->status = "error";
			$result->error = "access denied";
			return;
		}
		
		$t = new stdClass();
		$t->name = "id";
		$fields["id"] = $t;
		$query = "SELECT ".$up_prefix."users.username, ".$up_prefix."users.displayname, ( SELECT count( * ) FROM ".$table." WHERE ".$table.".created_by = ".$up_prefix."users.id ) AS created, ( SELECT count( * ) FROM ".$table." WHERE edited_by = ".$up_prefix."users.id ) AS edited, ( ( SELECT count( * ) FROM ".$table." WHERE ".$table.".created_by = ".$up_prefix."users.id ) + ( SELECT count( * ) FROM ".$table." WHERE edited_by = ".$up_prefix."users.id ) ) AS total FROM ".$up_prefix."users";
		
		$dbReturn = $UpCMS->db->select( $query );
		
		global $result;
		
		$result->authors	= $dbReturn;
		
		$UpCMS->dispatchEvent( new Event( UpCMS::LIST_AFTER_AUTHORS, NULL ) );
	}
	
	/**
    * Fill global $result with items from a table. The selected front should uses this $result to show them. Variables used here come from $_POST.
    * 
    * The steps are:
    * - Dispatch a event LIST_BEFORE_GET;
    * - Get the relation (POST:rel) value to get the table in $UpCMS->config. The relation can be a path 'mytable.mygallery=10' what means table('mytable')-> field('mygallery') who have relation by id '10';
    * - Get permission, if strict call addStrictPermission, if is already possible determine if permission is denied, the execution stop here setting $result for error;
    * - Create the fields and groups using a hard foreach;
    * - Save in $result and dispatch a event LIST_AFTER_GET;
    *
    * (POST:direction) is used for ASC or DESC the list
    * (POST:search) is used for LIKE %value%
    * (POST:order) is used to set the reference of direction. It should be a field name
    * Number of items per page is defined by a option.
    * 
    * @author Orlando Leite
    * @access public
    * @static
    * @return void
    */
	public static function get()
	{
		// GET EXTERNAL VARIABLES //
		global $UpCMS, $db_prefix, $up_prefix, $db_type;
		
		// DISPATCH START EVENT //
		$UpCMS->dispatchEvent( new Event( UpCMS::LIST_BEFORE_GET, NULL ) );
		
		// RELATION TABLE //
		$direction = strtolower( @$_POST['direction'] );
		$searchFor = @$_POST['search'];
		$orderBy = @$_POST['order'];
		$relation = $_POST['rel'];
		$permission = "";
		$limit = true;
		$search = "( NULL";
		$searchParams = " )";
		$where = "";
		$order = "";
		$join = "";
		
		// IF RELATION IS A SYSTEM  TABLE (like users and groups) //
		if( strpos( $relation, "system_" ) === 0 )
		{
			global $Language;
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
			$limit = false;
			$values = explode( "=", $relation );
			$relation = reset( $values );
			
			$tempRelation = explode( ".", $relation );
			
			$temp = "//table[@rel='".str_replace( ".", "']//field[@rel='", $relation )."']";
			$temp = $UpCMS->config->xpath( $temp );
			
			$relPermission	= reset( explode( ".", $relation ) );
			
			$id = end( $values );
			$rel = array_shift( $tempRelation );
			
			$target = $temp[0];
			$table = $prefix.$target["reltable"];
			$permission = $target["permission"];
			
			if( count( $values ) > 1 ) $where .= " WHERE u.rel_".$rel." = ".end( $values );
		}
		else
		{
			$temp = "//table[@rel='".str_replace( ".", "']//field[@rel='", $relation )."']";
			$temp = $UpCMS->config->xpath( $temp );
			
			$a = explode( ".", $relation );
			$relPermission	= reset( $a );
			
			$target = $temp[0];
			$table = $prefix.$target["reltable"];
			$permission = $target["permission"];
		}
		
		// GET PERMISSION //
		if( $permission == "strict" )
		{
			self::addStrictPermission( $target );
			
			if( $UpCMS->user->anyread( $relPermission ) )
				$permission = "any";
			else if( $UpCMS->user->ownwrite( $relPermission ) )
				$permission = "own";
			else
			{
				global $result;
			
				$result->status = "error";
				$result->error = "access denied";
				return;
			}
		}
		else if( $UpCMS->user->anyread( $relPermission ) )
		{
			$permission = "all";
		}
		else
		{
			global $result;
			
			$result->status = "error";
			$result->error = "access denied";
			return;
		}
		
		$name = $target["name"];
		$mode = isset( $target["mode"] ) ? $target["mode"] : "normal";
		
		$t = new stdClass();
		$t->name = "id";
		$fields["id"] = $t;
		$query = "SELECT u.id";
		
		if( $db_type == "pgsql" )
		{
			$search = "( u.id";
			$searchParams = " ) ILIKE '%".$searchFor."%'";
		}
		
		foreach( $target as $k => $n )
		{
			$p = (string) $n["params"];
			$v = (string) $n["value"];
			$t = (string) $n["type"];
			$r = (string) $n["rel"];
			
			if( $v == "" )
			{
				if( !empty( $searchFor ) and $t != "switch" and $t != "group" and $t != "simpletable" and $t != "table" )
				{
					if( $db_type == "mysql" )
						$search .= " OR u.".$r." LIKE '%".$searchFor."%'";
					else
						$search .= " || u.".$r;
				}
				
				if( (string) $n["quickedit"] == "enabled" and $t != "text" and $t != "html" and $t != "simplehtml" and $t != "switch" and (string) $k != "group" )
				{
					$f = new stdClass();
					$f->type = $t;
					$f->params = $p;
					$f->name = (string) $n["name"];
					if( (string) $n["strict"] == "true" ) $f->strict = true;
					
					if( $t == "select" or $t == "options" )
					{
						if( isset( $n->dynamic ) ) $f->options = ApplicationList::getDynamicOptions( $prefix, $n->dynamic, $rel, $id, $f->options );
						
						foreach( $n->option as $opt ) $f->options[(string) $opt["value"]] = (string) $opt["name"];
					}
					
					$fields[$r] = $f;
					
					if( (string) $n["strict"] == "true" and $r == "created_by" )
						$query .= ", c.displayname AS created_by";
					else if( (string) $n["strict"] == "true" and $r == "edited_by" )
						$query .= ", e.displayname AS edited_by";
					else
						$query .= ", u.".$r;
				}
			}
			else if( $v != "" and $t != "switch" and (string) $k != "group" )
			{
				$where = ( strlen( $where ) == 0 ? " WHERE " : " AND " )."u.".$r." = '".$v."'";
			}
		}
		
		if( !empty( $searchFor ) ) $where .= ( strlen( $where ) == 0 ? " WHERE " : " AND " ).$search.$searchParams;
		$query .= " FROM ".$table." AS u";
		
		if( $permission == "own" )
		{
			$join = " LEFT JOIN ".$up_prefix."users AS c ON u.created_by = c.id LEFT JOIN ".$up_prefix."users AS e ON u.edited_by = e.id ";
			$where .= ( strlen( $where ) == 0 ? " WHERE " : " AND " )."u.created_by = '".$UpCMS->user->info( id )."'";
		}
		if( $permission == "any" )
		{
			$join .= " LEFT JOIN ".$up_prefix."users AS c ON u.created_by = c.id LEFT JOIN ".$up_prefix."users AS e ON u.edited_by = e.id ";
			if( !empty( $_POST["author"] ) && $_POST["author"] != "" ) $where .= ( strlen( $where ) == 0 ? " WHERE " : " AND " )."( c.username =  '".$_POST["author"]."' OR e.username = '".$_POST["author"]."' )";
		}
		
		$rows = $UpCMS->options->get( "upcms", "list_limit" );
		
		// ORDER BY 
		if( !empty( $orderBy ) )
		{
			$find = $target->xpath( "//field[@rel='".$orderBy."']" );
			
			if( count( $find ) != 0 )
			{
				if( $permission == "any" or $permission == "own" )
				{
					if( $orderBy == "created_by" ) $orderBy = "c.displayname";
					if( $orderBy == "edited_by" ) $orderBy = "e.displayname";
				}
				
				$order = " ORDER BY ".$orderBy;
			}
			else $order = " ORDER BY id";
		}
		else
		{
			$order = " ORDER BY id";
		}
		
		// DESC or ASC
		$order .= $direction == "asc" ? " ASC" : " DESC";
		$query .= $join.$where.$order.( $limit ? " LIMIT ".$rows." OFFSET ".( $rows * $_POST["page"] ) : "" );
		
		$List = $UpCMS->db->select( $query );
		$length = current( $UpCMS->db->select( "SELECT COUNT(*) FROM ".$table." AS u".$join.$where, DB_NUM ) );
		$length = is_array( $length ) ? current( $length ) : 0;
		
		global $result;
		
		$result = new stdClass();
		$result->list		= $List;
		$result->fields		= $fields;
		$result->permission	= $permission;
		$result->rel		= (string) $relation;
		$result->mode		= (string) $mode;
		$result->name		= (string) $name;
		$result->length		= (string) $length;
		$result->rowsPerPage= (string) $rows;
		$result->icon		= (string) $target["icon"];
		
		$UpCMS->dispatchEvent( new Event( UpCMS::LIST_AFTER_GET, NULL ) );
	}
	
	/**
    * Some fields type can have dynamic options. This function get that values.
    * @author Orlando Leite
    * @access protected
    * @static
    * @param string $prefix db prefix used, ordinary can be $up_prefix or $db_prefix
    * @param object $target a target field
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
}

?>