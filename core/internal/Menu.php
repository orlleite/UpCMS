<?php

/**
 * Inform what the Menu should have.
 * @author Orlando Leite
 * @version 0.8
 * @package core
 * @subpackage internal
 * @access public
 * @name Menu
 */
class Menu
{
	protected static $menu;
	
	/**
	* Get the menu.
	* If your application need add some menu or linkmenu, you should add a listener for MENU_BEFORE_GET if you want first of all or MENU_AFTER_GET if you want after all.
    * @author Orlando Leite
    * @access public
    * @static
    * @return array with all items to be showed on menu.
    */
	public static function get()
	{
		// GET EXTERNAL VARIABLES //
		global $UpCMS, $Language;
		
		self::$menu = array();
		
		// DISPATCH START EVENT //
		$UpCMS->dispatchEvent( new Event( UpCMS::MENU_BEFORE_GET, $this ) );
		
		// Group Menu //
		$menu = $UpCMS->config->gui;
		$addedMenu = array();
		
		$total = count( $UpCMS->config->table );
		for( $i = 0; $i < $total; $i++ )
		{
			if( $UpCMS->user->ownwrite( (string) $UpCMS->config->table[$i]["rel"] ) or $UpCMS->user->anyread( (string) $UpCMS->config->table[$i]["rel"] ) )
			{
				$tab = $UpCMS->config->table[$i];
				$relMenu = (string)$tab["menu"];
				$tarMenu = NULL;
				
				if( $relMenu != "" && count( $tarMenu = $menu->xpath('//menu[@rel=\''.$relMenu.'\']') ) )
				{
					if( !$addedMenu[$relMenu] )
					{
						$temp = NULL;
						$temp->name	= $tarMenu[0]["name"];
						$temp->rel	= $tarMenu[0]["rel"];
						$temp->icon	= $tarMenu[0]["icon"];
						$temp->url	= "";
						$temp->options = array();
						
						$addedMenu[$relMenu] = $temp;
						array_push( self::$menu, $temp );
					}
					
					$temp = NULL;
					$temp->name = $UpCMS->config->table[$i]["name"];
					
					$id = @$UpCMS->config->table[$i]["id"];
					
					if( $id && $id != "" )
						$temp->url	= $UpCMS->config->table[$i]["rel"]."/".$id."/edit";
					else
						$temp->url	= $UpCMS->config->table[$i]["rel"]."/list";
					
					array_push( $addedMenu[$relMenu]->options, $temp );
				}
				else
				{
					$temp = NULL;
					$temp->name	= $UpCMS->config->table[$i]["name"];
					$temp->rel	= $UpCMS->config->table[$i]["rel"];
					$temp->icon	= $UpCMS->config->table[$i]["icon"];
					
					$id = @$UpCMS->config->table[$i]["id"];
					
					if( $id && $id != "" )
						$temp->url	= $UpCMS->config->table[$i]["rel"]."/".$id."/edit";
					else
						$temp->url	= $UpCMS->config->table[$i]["rel"]."/list";
					
					array_push( self::$menu, $temp );
				}
			}
		}
		
		// DISPATCH BEFORE USERS EVENT //
		$UpCMS->dispatchEvent( new Event( UpCMS::MENU_BEFORE_GET_USERS, $this ) );
		
		if( ( $UpCMS->user->ownwrite( "users" ) or $UpCMS->user->anyread( "users" ) )
			&& $UpCMS->options->get( "upcms", "users_system" ) == "true" )
		{
			$temp = NULL;
			$temp->name	= $Language->users;
			$temp->rel	= "system_users";
			$temp->url	= "system_users/list";
			$temp->icon	= "users";
			
			$temp->options[0]->name = $Language->users_edit;
			$temp->options[0]->url = "system_users/list";
			
			$temp->options[1]->name = $Language->groups_edit;
			$temp->options[1]->url = "system_groups/list";
			
			array_push( self::$menu, $temp );
		}
		
		if( $UpCMS->user->application( "settings" ) &&
		    $UpCMS->options->get( "upcms", "settings" ) == "true" )
		{
			$temp = NULL;
			$temp->name = $Language->settings;
			$temp->rel = "system_settings";
			$temp->url = "general/set";
			$temp->icon	= "settings";
			
			$temp->options[0]->name = $Language->general;
			$temp->options[0]->url = "general/set";
			
			$temp->options[1]->name = $Language->front;
			$temp->options[1]->url = "front/set";
			
			$temp->options[2]->name = $Language->plugins;
			$temp->options[2]->url = "plugins/set";
			
			array_push( self::$menu, $temp );
		}
		
		$UpCMS->dispatchEvent( new Event( UpCMS::MENU_AFTER_GET, $this ) );
		
		return self::$menu;
	}
	
	/**
	* Add a menu.
	* If your application need add some menu or linkmenu, and you added a listener for it.
	* Now, call this function and a menu will be added.
    * @author Orlando Leite
    * @access public
    * @param string $name Menu or link menu name.
    * @param string $rel relative for.
    * @param string $url link should be called when clicked.
    * @param array $options if it's a menu, and not a link menu. Put here the sublinks. Use (object:name,url)
    * @static
    * @return void
    */
	public static function addMenu( $name, $rel, $url, $options )
	{
		$temp->url = $url;
		$temp->rel = $rel;
		$temp->name = $name;
		$temp->options = $options;
		array_push( self::$menu, $temp );
	}
}

?>