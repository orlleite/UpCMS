<?php

include_once( "EventDispatcher.php" );
include_once( "Event.php" );

/**
 * UpCMS main class.
 * Basically, when we call UpCMS environment, indeed, is a instance of this class.
 * All UpCMS Events type is defined here.
 * @author Orlando Leite
 * @version 0.8
 * @package core
 * @subpackage internal
 * @access public
 * @see EventDispatcher, Menu, ApplicationLogin, ApplicationList, ApplicationEdit, ApplicationFileGallery, ApplicationImage, ApplicationSet
 * @name UpCMS
 */
class UpCMS extends EventDispatcher
{
	/**
	* DB connection instance. Use this for make a default connection.
	* @access public
	* @var IConnection
	*/
	public $db;
	
	/**
	* Options instance. Use for get options without instanciate another Options class.
	* @access public
	* @var Options
	*/
	public $options;
	
	/**
	* Currently logged user.
	* @access public
	* @var User
	*/
	public $user;
	
	/**
	* Currently upcms configuration (XML).
	* @access public
	* @var SimpleXML
	*/
	public $config;
	
	/**
	* Currently upcms menu.
	* @access public
	* @var Menu
	*/
	public $menu;
	
	/**
	* List of Settings. If you want to create a set page. Add here.
	* @access public
	* @var Menu
	*/
	public $settings;
	
	// Menu //
	const MENU_BEFORE_GET_USERS = "menu_before_get_users";
	const MENU_BEFORE_GET = "menu_before_get";
	const MENU_AFTER_GET = "menu_after_get";
	
	// Index //
	const BEFORE_FIRST_PAGE = "before_first_page";
	const AFTER_FIRST_PAGE = "after_first_page";
	
	// Login //
	const GET_INIT_SCRIPT = "get_init_script";
	const BEFORE_LOGIN = "before_login";
	const AFTER_LOGIN = "after_login";
	const BEFORE_LOGOUT = "before_logout";
	const AFTER_LOGOUT = "after_logout";
	const BEFORE_LOGGED = "before_logged";
	const AFTER_LOGGED = "after_logged";
	
	// List //
	const LIST_BEFORE_AUTHORS = "list_before_authors";
	const LIST_AFTER_AUTHORS = "list_after_authors";
	const LIST_BEFORE_GET = "list_before_get";
	const LIST_AFTER_GET = "list_after_get";
	
	// Edit //
	const EDIT_BEFORE_RESET_ARRAY = "edit_before_reset_array";
	const EDIT_AFTER_RESET_ARRAY = "edit_after_reset_array";
	const EDIT_BEFORE_UPDATE = "edit_before_update";
	const EDIT_AFTER_UPDATE = "edit_after_update";
	const EDIT_BEFORE_GETEDIT = "edit_before_getedit";
	const EDIT_AFTER_GETEDIT = "edit_after_getedit";
	const EDIT_BEFORE_SAVE = "edit_before_save";
	const EDIT_AFTER_SAVE = "edit_after_save";
	const EDIT_BEFORE_GETNEW = "edit_before_getnew";
	const EDIT_AFTER_GETNEW = "edit_after_getnew";
	const EDIT_BEFORE_REMOVE = "edit_before_remove";
	const EDIT_AFTER_REMOVE = "edit_after_remove";
	
	// FileGallery //
	const FILEGALLERY_BEFORE_DELETE = "filegallery_before_delete";
	const FILEGALLERY_AFTER_DELETE = "filegallery_after_delete";
	const FILEGALLERY_BEFORE_RENAME = "filegallery_before_rename";
	const FILEGALLERY_AFTER_RENAME = "filegallery_after_rename";
	const FILEGALLERY_BEFORE_ALIST = "filegallery_before_alist";
	const FILEGALLERY_AFTER_ALIST = "filegallery_after_alist";
	const FILEGALLERY_BEFORE_UPLOAD = "filegallery_before_upload";
	const FILEGALLERY_AFTER_UPLOAD = "filegallery_after_upload";
	const FILEGALLERY_BEFORE_VERIFY = "filegallery_before_verify";
	const FILEGALLERY_AFTER_VERIFY = "filegallery_after_verify";
	const FILEGALLERY_BEFORE_DECOMPRESS = "filegallery_before_decompress";
	const FILEGALLERY_AFTER_DECOMPRESS = "filegallery_after_decompress";
	
	// Image //
	const IMAGE_BEFORE_RESIZECROP = "image_before_resizecrop";
	const IMAGE_AFTER_RESIZECROP = "image_after_resizecrop";
	
	// Set //
	const SET_BEFORE_GET = "set_before_get";
	const SET_AFTER_GET = "set_after_get";
	const SET_BEFORE_SET = "set_before_set";
	const SET_AFTER_SET = "set_after_set";
	
	private static $_instance;
	
	protected function __clone() { }
	
	public static function instance()
	{
		if( self::$_instance === NULL ) self::$_instance = new self();
		return self::$_instance;
	}
	
	protected function __construct()
	{
		$this->eventDispatcher = new EventDispatcher();
	}
}

?>