// JavaScript Document
function showDarkScreen()
{
 	Animation.showScreen( $( "#dark_screen" ) );
}

function hideDarkScreen()
{
	Animation.hideScreen( $( "#dark_screen" ) );
}

function showLoadingScreen()
{
	Animation.showScreen2( $( "#loading_screen" ) );
}

function hideLoadingScreen()
{
	Animation.hideScreen( $( "#loading_screen" ) );
}

function showPopboxScreen()
{
	Animation.showScreen( $( "#popbox_screen" ) );
}

function hidePopboxScreen()
{
	Animation.hideScreen( $( "#popbox_screen" ) );
}

function progressBox( title, value, func )
{
	showDarkScreen();
	$( "body" ).append( "<div class=\"alert_box progress_box\"><span class=\"icon\"></span><div><h1>" + title + "</h1><span class=\"value\">" + value + "</span><br /> <div class=\"progress\"><div class=\"loaded\"></div></div><br /><div class=\"box_btn_container\"></div></div></div>" );
	var t = $( ".progress_box" );
	
	t.css( { top:( Browser.height - t.height() ) / 2, left:( Browser.width - t.width() ) / 2 } );
	Animation.showBox( t );
	
	var progress = function( text, value )
	{
		Animation.setProgress( t.find( ".loaded" ), value );
		t.find( ".value" ).html( text );
	}
	
	var complete = function()
	{
		Animation.setProgress( t.find( ".loaded" ), 1, function() { func(); removeBox( ".progress_box" ); } );
	}
	
	return { progress:progress, complete:complete };
}

function alertBox( title, value, func )
{
	showDarkScreen();
	$( "body" ).append( "<div class=\"alert_box\"><span class=\"icon\"></span><div><h1>" + title + "</h1>" + value + "<br /><div class=\"box_btn_container\"><a id=\"alert_box_button\" class=\"button\" href=\"javascript:;\"><span class=\"left\"></span><span class=\"center\">" + Language.ok + "</span><span class=\"right\"></span></a></div></div></div>" );
	var t = $( ".alert_box" );
	
	t.css( { top:( Browser.height - t.height() ) / 2, left:( Browser.width - t.width() ) / 2 } );
	Animation.showBox( t );
	
	$( "#alert_box_button" ).click( function() { removeBox( ".alert_box", func ) } );
}

function sucessBox( title, value, func )
{
	showDarkScreen();
	$( "body" ).append( "<div class=\"sucess_box\"><span class=\"icon\"></span><div><h1>" + title + "</h1>" + value + "<br /><div class=\"box_btn_container\"><a id=\"sucess_box_button\" class=\"button\" href=\"javascript:;\"><span class=\"left\"></span><span class=\"center\">" + Language.ok + "</span><span class=\"right\"></span></a></div></div></div>" );
	var t = $( ".sucess_box" );
	
	t.css( { top:( Browser.height - t.height() ) / 2, left:( Browser.width - t.width() ) / 2 } );
	Animation.showBox( t );
	
	$( "#sucess_box_button" ).click( function() { removeBox( ".sucess_box", func ) } );
}

function confirmBox( title, value, func1, func2 )
{
	showDarkScreen();
	$( "body" ).append( "<div class=\"confirm_box\"><span class=\"icon\"></span><div><h1>" + title + "</h1>" + value + "<br /><div class=\"box_btn_container\"><a id=\"cancel_box_button\" class=\"button\" href=\"javascript:;\"><span class=\"left\"></span><span class=\"center\">" + Language.cancel + "</span><span class=\"right\"></span></a> <a id=\"ok_box_button\" class=\"button\" href=\"javascript:;\"><span class=\"left\"></span><span class=\"center\">" + Language.ok + "</span><span class=\"right\"></span></a></div></div></div>" );
	var t = $( ".confirm_box" )
	
	t.css( { top:( Browser.height - t.height() ) / 2, left:( Browser.width - t.width() ) / 2 } );
	Animation.showBox( t );
	
	$( "#ok_box_button" ).click( function() { removeBox( ".confirm_box", func1 ); func1 = null } );
	$( "#cancel_box_button" ).click( function() { if( func2 ) removeBox( ".confirm_box", func2 ); else removeBox( ".confirm_box" ); } );
}

function confirmBox2( title, value, func1, func2 )
{
	showDarkScreen();
	$( "body" ).append( "<div class=\"confirm_box\"><span class=\"icon\"></span><div><h1>" + title + "</h1>" + value + "<br /><div class=\"box_btn_container\"><a id=\"cancel_box_button\" class=\"button\" href=\"javascript:;\"><span class=\"left\"></span><span class=\"center\">" + Language.no + "</span><span class=\"right\"></span></a> <a id=\"ok_box_button\" class=\"button\" href=\"javascript:;\"><span class=\"left\"></span><span class=\"center\">" + Language.yes + "</span><span class=\"right\"></span></a></div></div></div>" );
	var t = $( ".confirm_box" );
	
	t.css( { top:( Browser.height - t.height() ) / 2, left:( Browser.width - t.width() ) / 2 } );
	Animation.showBox( t );
	
	$( "#ok_box_button" ).click( function() { removeBox( ".confirm_box", func1 ); func1 = null } );
	$( "#cancel_box_button" ).click( function() { if( func2 ) removeBox( ".confirm_box", func2 ); else removeBox( ".confirm_box" ); } );
}

function inputBox( title, value, func1, func2, fill )
{
	showDarkScreen();
	$( "body" ).append( "<div class=\"input_box\"><span class=\"icon\"></span><div><h1>" + title + "</h1>" + value + "<br /><br /><span class=\"field\"><input id=\"input_field\" type=\"text\" " + ( fill ? "value=\"" + fill + "\"" : "" ) + " /></span><br /><div class=\"box_btn_container\"><a id=\"cancel_box_button\" class=\"button\" href=\"javascript:;\"><span class=\"left\"></span><span class=\"center\">" + Language.cancel + "</span><span class=\"right\"></span></a> <a id=\"ok_box_button\" class=\"button\" href=\"javascript:;\"><span class=\"left\"></span><span class=\"center\">" + Language.ok + "</span><span class=\"right\"></span></a></div></div></div>" );
	var t = $( ".input_box" );
	
	t.css( { top:( Browser.height - t.height() ) / 2, left:( Browser.width - t.width() ) / 2 } );
	Animation.showBox( t );
	
	var value = "";
	
	$( "#ok_box_button" ).click( function() { func1( $( "#input_field" ).val() ); removeBox( ".input_box" ) } );
	$( "#cancel_box_button" ).click( function() { if( func2 ) removeBox( ".input_box", func2 ); else removeBox( ".input_box" ); } );
}

function loadScriptsAndImages( scripts, images, progress, complete )
{
	var loaded = 0;
	var total = scripts.length + images.length;
	
	var handler = function()
	{
		loaded++;
		if( loaded == total ) complete(); else if( progress ) progress( loaded / total );
	}
	
	for( var i = 0; i < scripts.length; i++ )
	{
		$.getScript( scripts[i], handler );
	}
	
	for( i = 0; i < images.length; i++ )
	{
		var temp = new Image();
		temp.onload = handler;
		temp.src = images[i];
	}
}
/*
function loadScripts( list, progress, complete )
{
	var loaded = 0;
	var total = list.length;
	
	var handler = function()
	{
		loaded++;
		if( loaded == total ) complete(); else if( progress ) progress( loaded / total );
	}
	
	for( var i = 0; i < total; i++ )
	{
		$.getScript( list[i], handler );
	}
}

function cacheImage( list, progress, complete )
{
	var loaded = 0;
	var total = list.length;
	
	var handler = function()
	{
		loaded++;
		if( loaded == total ) complete(); else if( progress ) progress( loaded / total );
	}
	
	for( var i = 0; i < total; i++ )
	{
		var temp = new Image();
		temp.onload = handler;
		temp.src = list[i];
	}
}
*/
function loadCSS( address )
{
	$("head").append( "<link>" );
	css = $( "head" ).children( ":last" );
	css.attr({
		rel: "stylesheet",
		type:"text/css",
		href:address
	});
}

function createSlider( values, target )
{
	var t = "<div class=\"slider\"><div class=\"options\">";
	
	for( var i = 0; i < values.length; i++ )
		t += "<a class=\"button\" onclick=\"Animation.sliderButtonClick(this);\" href=\"" + values[i].href + "\"><span class=\"general_icon icon_" + values[i].icon + "\"></span> " + ( values[i].name ? "<span class=\"text\" id=\"aupload\">" + values[i].name + "</span>" : "" ) + "</a>";
	
	t += "</div><div class=\"base\"><div class=\"left\"></div><div class=\"center\"><div class=\"right\"></div><div class=\"selecter\"></div></div></div></div>";
	
	$( "body" ).append( "<span id=\"temporary_slider\"></div>" ); 
	
	var o = $( "#temporary_slider" );
	o.append( t );
	o.find( ".slider .selecter" ).css( "width", o.find( ".slider .button:first" ).width() + 3 );
	//$( ".slider .options .button" ).click( Animation.sliderButtonClick );
	
	if( target ) $( target ).append( $( "#temporary_slider .slider" ) );
	else
	{
		var rtn = o.html();
		o.remove();
		return rtn;
	}
	
	o.remove();
}

function changeRedButtonValue( id, value )
{
	if( !value ) return;
	$( "#" + id + " i" ).html( value );
}

function activateRedButton( id )
{
	$t = $( "#" + id );
	if( !$t.hasClass( "red_button_deactivated" ) ) return;
	$t.removeClass( "red_button_deactivated" );
	$t.unbind( "click" );
}

function deactivateRedButton( id )
{
	$t = $( "#" + id );
	if( $t.hasClass( "red_button_deactivated" ) ) return;
	$t.addClass( "red_button_deactivated" );
	$t.click( function() { return false } );
}

function submitByEnter( event, func, params )
{
	var key = event.keyCode || event.which;
	if ( key == 13 ) func( params );
}

// INTERNAL ------------------------------------------------------------------------- //
function openBox( obj )
{
	var t = $( obj.parentNode.parentNode.getElementsByTagName("div")[1] );
	if( t.css( "display" ) == "none" ) 
	{
		t.slideDown( 400 );
		$( obj ).html( "-" );
	}
	else 
	{
		t.slideUp( 400 );
		$( obj ).html( "+" );
	}
}

function removeBox( target, func )
{
	hideDarkScreen();
	Animation.removeBox( target, func );
}

// --------------------------------------------------------------------------------- //
function masterEdit( target )
{
	target = target.split( "." );
}

function dateSqlToStamp( string )
{
	if( string == "" ) return "";
	var s = string.split( " " );
	var d = s[0].split( "-" );
	var h = s[1].split( ":" );
	var r = new Date( d[0], d[1] - 1, d[2], h[0], h[1], h[2] );
	return r.toLocaleFormat( TimeStamp );
}

function parseParams( value )
{
	var obj = { types:[], min:[], max:[], size:[], ratio:[] };
	
	var types = function( a1, a2, a3 )
	{
		obj.types = [a1, a2, a3];
	}
	
	var min = function( a1, a2 )
	{
		obj.min = [a1, a2];
	}
	
	var max = function( a1, a2 )
	{
		obj.max = [a1, a2];
	}
	
	var size = function( a1, a2 )
	{
		obj.size = [a1, a2];
	}
	
	var ratio = function( a1, a2 )
	{
		obj.ratio = [a1, a2];
	}
	
	var decompress = function( a )
	{
		obj.decompress = a;
	}
	
	eval( value );
	
	return obj;
}

function setDelay( func, time )
{
	var id = setInterval( function() { clearInterval( id ); func(); }, time );
}

Math.between = function( value, min, max )
{
	return value > max ? max : ( value < min ? min : value );
}

String.prototype.replaceAll = function( s, r )
{
	var t = this;
	while( ( f = t.indexOf( s ) ) != -1 )
	{
		t = t.replace( s, r );
	}
	
	return t;
}

Array.merge = function( a, b )
{
	for( var c in b ) a.push( b[c] );
	return a;
}

Array.removeEmpty = function( target )
{
	var d = new Array(), t = target;
	for( var i in t )
	{
		if( t[i] && t[i] != "" ) d.push( t[i] );
	}
	
	return d;
}

Array.removeRepeated = function( target )
{
	var d = new Array(), t = target;
	for( var i in t )
	{
		var f = false;
		for( var a in d ) { if( t[i] == d[a] ) { f = true; break } };
		if( !f ) d.push( t[i] );
	}
	
	return d;
}
