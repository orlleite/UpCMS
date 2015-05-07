// JavaScript Document
var searchList = { };

function menuOverHandler( obj )
{
	var t = $( obj );
	if( t.attr( "submenu" ) ) t.find( ".submenu_more" ).css( "display", "inline-block" );
}

function menuOutHandler( obj )
{
	var t = $( obj );
	if( t.attr( "submenu" ) ) t.find( ".submenu_more" ).css( "display", "none" );
}

function moreClickHandler( obj )
{
	if( obj.opened )
	{
		Animation.slideUp( $( obj ).parent().find( ".content" ) );
	}
	else
	{
		Animation.slideDown( $( obj ).parent().find( ".content" ) );
	}
	
	obj.opened = !obj.opened;
}

function moreParentClickHandler( obj )
{
	var t = $( obj ).parent();
	var s = t.find( "submenu_more" );
	if( !s.opened )
	{
		Animation.slideDown( t.find( ".content" ) );
		s.opened = true;
	}
}

function addLinkMenu( rel, name, url, icon )
{
	var id = rel + "_btn";
	searchList[rel] = name;
	
	$( "#menu .content:first" ).append( "<div class=\"submenu\" id=\"" + id + "\" onmouseover=\"menuOverHandler( this )\" onmouseout=\"menuOutHandler( this )\"><span class=\"icon " + ( icon ? icon : "generic_list" ) + "\"></span><a onclick=\"moreParentClickHandler( this )\" onmouseover=\"menuOverHandler( this )\" href=\"" + url + "\">" + name + "</a><span class=\"submenu_more\" onclick=\"moreClickHandler( this )\"></span></div>" );
}

function addSubMenu( rel, name, url )
{
	var id = rel + "_btn";
	searchList[rel] = name;
	
	var t = $( "#" + id );
	
	if( !t.attr( "submenu" ) )
	{
		t.attr( "submenu", id + "_sub" );
		t.append( "<div id=\"" + id + "_sub\" class=\"content\"><a class=\"btn\" href=\"" + url + "\">" + name + "</a></div>" );
	}
	else
	{
		$( "#" + id + "_sub" ).append( "<a class=\"btn\" href=\"" + url + "\">" + name + "</a>" );
	}
}