
function Ballon( id, content )
{
	this.id = id + "_" + String( Math.floor( Math.random() * 10000 ) );
	this.tposition;
	
	$( "body" ).append( "<div id=\"ballon_" + this.id + "\" class=\"ballon\"><div class=\"top\"><div class=\"left\"></div><div class=\"center\"><div class=\"right\"></div></div></div><div class=\"content\"><div class=\"left\"><div class=\"right\"><div class=\"center\"><div class=\"value\">" + content + "</div></div></div></div></div><div class=\"bottom\"><div class=\"left\"></div><div class=\"center\"><span></span><div class=\"right\"></div></div></div></div>" );
	
	this.target = $( "#ballon_" + this.id + " .content .center .value" );
	$( "#ballon_" + this.id ).css( "display", "none" );
	// Animation.showBallon( this.id );
	
	this.position = function()
	{
		var p = $( this.tposition );
		var t = $( "#ballon_" + this.id );
		var top = p.offset().top - t.height() + p.height() * 0.3;
		var left=p.offset().left - ( t.width() - p.width() ) * 0.5;
		top = Math.between( top, 0, Infinity );
		left= Math.between( left, 0, Browser.width - t.width() - 20 );
		t.css( { "top":top, "left":left } );
	}
	
	this.html = function( content )
	{
		var t = this;
		var d = $( "#ballon_" + this.id );
		var x1, y1, w1, h1, x2, y2, w2, h2;
		x1 = d.position().left;
		y1 = d.position().top;
		h1 = d.height();
		w1 = d.width();
		
		var func = function()
		{
			t.target.html( content );
			t.position();
			
			x2 = d.position().left;
			y2 = d.position().top;
			h2 = d.height();
			w2 = d.width();
			
			Animation.changeBallonSize( d.css( { "top":y1, "left":x1, "width":w1, "height":h1 } ), { top:y2, left:x2, width:w2, height:h2 }, function() { Animation.showContentBallon( t.target ) } );
		}
		
		Animation.hideContentBallon( t.target, func );
	}
	
	this.append = function( content )
	{
		this.target.append( content );
	}
	
	this.show = function( func )
	{
		Animation.showBallon( this.id, func );
	}
	
	this.hide = function( func )
	{
		Animation.hideBallon( this.id, func );
	}
	
	this.destroy = function()
	{
		var id = this.id;
		Animation.hideBallon( id, function() { $( "#ballon_" + id ).remove() } );
	}
	
	this.jquery = function()
	{
		return $( "#ballon_" + this.id );
	}
	
	this.overring = function()
	{
		return this.jquery().attr( "over" );
	}
}
