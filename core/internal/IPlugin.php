<?php

/**
 * Interface for Plugins. To make a good plugin, you shall use this interface.
 * @author Orlando Leite
 * @version 0.8
 * @package core
 * @access public
 * @name IPlugin
 */
interface IPlugin
{
	/**
	* Called when a plugin is going to be installed. Put what should be added before the plugin starts to work. options, tables and others.
	* @author Orlando Leite
	* @version 0.8
	* @package core
	* @access public
	*/
	public function install();
	
	/**
	* Called when a plugin is going to be uninstalled. Remove here everything you put in when install was called.
	* @author Orlando Leite
	* @version 0.8
	* @package core
	* @access public
	*/
	public function uninstall();
	
	/**
	* When upcms is called, this function is called too. Of course, if this plugin is installed.
	* @author Orlando Leite
	* @version 0.8
	* @package core
	* @access public
	*/
    public function start();
}

?>