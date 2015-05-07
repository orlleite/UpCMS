<?php

/**
 * EventDispatcher use this classes an event.
 * @author Orlando Leite
 * @version 0.8
 * @package core
 * @subpackage internal
 * @access public
 * @see EventDispatcher
 * @name Event
 */
class Event
{
	protected $type, $from;
	
	private function __clone() { }
	
	/**
    * @author Orlando Leite
    * @access public
    * @param string $type of the event.
    * @param integer $from who are calling this event.
    * @static
    * @return Event
    */
	public function __construct( $type, $from )
	{
		$this->type = $type;
		$this->from = $from;
	}
	
	/**
    * @author Orlando Leite
    * @access public
    * @static
    * @return string get the type.
    */
	public function getType()
	{
		return $this->type;
	}
	
	/**
    * @author Orlando Leite
    * @access public
    * @static
    * @return string get who are callling this event.
    */
	public function getFrom()
	{
		return $this->from;
	}
}

?>