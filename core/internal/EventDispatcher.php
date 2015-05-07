<?php

/**
 * The Event dispatcher, properly. Send messages of events for who ask it for.
 * @author Orlando Leite
 * @version 0.8
 * @package core
 * @subpackage internal
 * @access public
 * @see Event
 * @name EventDispatcher
 */
class EventDispatcher
{
	private $funcs;
	private $objs;
	
	/**
	* Add a Listener
    * @author Orlando Leite
    * @access public
    * @param string $type of the event.
    * @param object $obj the object who are listening the event.
    * @param string $func The method of the object who should be called when the event type occur.
    * @static
    * @return void
    */
	public function addEventListener( $type, $obj, $func )
	{
		if( @$this->funcs[$type] == null )
		{
			$this->funcs[$type] = array();
			$this->objs[$type] = array();
		}
		else
		{
			if( $this->find( $this->objs[$type], $this->funcs[$type], $obj, $func ) ) return;
		}
		
		$this->objs[$type][array_push( $this->funcs[$type], $func ) - 1] = $obj;
	}
	
	/**
	* Remove a listener
    * @author Orlando Leite
    * @access public
    * @param string $type of the event.
    * @param object $obj the object what are listening the event.
    * @param string $func The method of the object what was listening the event type.
    * @static
    * @return void
    */
	public function removeEventListener( $type, $obj, $func )
	{
		if( $this->funcs[$type] )
		{
			$c = count( $this->funcs[$type] );
			
			for( $i = 0; $i <  $c; $i++ )
			{
				if( $this->funcs[$type][$i] == $func and $this->objs[$type][$i] == $obj )
				{
					array_splice( $this->funcs[$type][$i], $i, 1 );
					array_splice( $this->objs[$type][$i], $i, 1 );
					return;
				}
			}
		}
		else return;
	}
	
	/**
	* Dispatch a Event
    * @author Orlando Leite
    * @access public
    * @param string $event the event to be dispatched.
    * @static
    * @return void
    */
	public function dispatchEvent( $event )
	{
		$t = $event->getType();
		
		if( @$this->funcs[$t] )
		{
			$c = count( $this->funcs[$t] );
			for( $i = 0; $i < $c; $i++ )
			{
				$f = $this->funcs[$t][$i];
				$this->objs[$t][$i]->$f( $event );
			}
		}
	}
	
	/**
	* Find for object who wants be warned from the occurring event.
    * @author Orlando Leite
    * @access private
    * @param string $event the event to be dispatched.
    * @static
    * @return void
    */
	private function find( $a1, $a2, $obj, $func )
	{
		$c = count( $a1 );
		for( $i = 0; $i < $c; $i++ )
		{
			if( $a1[$i] == $obj and $a2[$i] == $func ) return true;
		}
		return false;
	}
}

?>