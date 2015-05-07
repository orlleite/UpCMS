// JavaScript Document
function Validator()
{
	var target = null;
	
	var between = function( from, to )
	{
		var number = Number( target );
		return number < from ? false : ( number > to ? false : true );
	}
	
	var length = function( from, to )
	{
		var number = String( target ).length;
		return number < from ? false : ( number > to ? false : true );
	}
	
	this.parseValidator = function( validate, value )
	{
		var result = false;
		target = value;
		eval( 'result = ' + validate );
		
		return result;
	}
}

Validator = new Validator();
