<?php

/**
 * Front management.
 * Use to add contents in header, body and modify things of interface.
 * @author Orlando Leite
 * @version 0.8
 * @package core
 * @subpackage internal
 * @access public
 * @name Html
 */
class Html
{
	/**
	* List of JS to add in header.
	* @access protected
	* @var array
	*/
	protected $js = array();
	
	/**
	* List of CSS to add in header.
	* @access protected
	* @var array
	*/
	protected $css = array();
	
	/**
	* Add a Javascript to header.
	* @access public
	* @param string $path javascript path.
	* @static
	* @return void.
	*/
	public function addJavascript( $path )
	{
		array_push( $this->js, $path );
	}
	
	/**
	* Current list of javascripts to be added in header.
	* @access public
	* @static
	* @return array.
	*/
	public function listJavascripts()
	{
		return $this->js;
	}
	
	/**
	* Add a CSS to header.
	* @access public
	* @param string $path javascript path.
	* @static
	* @return void.
	*/
	public function addCSS( $path )
	{
		array_push( $this->css, $path );
	}
	
	/**
	* Current list of CSS to be added in header.
	* @access public
	* @static
	* @return array.
	*/
	public function listCSS()
	{
		return $this->css;
	}
}

?>