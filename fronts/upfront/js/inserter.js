function Inserter()
{
	this.callback;
	
	this.hide = function()
	{
		hidePopboxScreen();
		Animation.hideInsertBox();
	}
	
	this.cancel = function()
	{
		inserter.hide();
	}
	
	this.save = function()
	{
		if( inserter.callback ) inserter.callback( $( "#insert_box textarea" ).val() );
		inserter.hide();
	}
	
	this.init = function( title, src, callback )
	{
		inserter.callback = callback;
		$( "body" ).append( "<div class=\"popbox\" id=\"insert_box\"><div class=\"top\"><div class=\"left\"></div><div class=\"center\"><div class=\"right\"></div><span>" + title +" <a class=\"close_btn\" href=\"javascript:inserter.hide();\"> </a> </span></div></div><div class=\"container\"><div class=\"left\"></div><div class=\"center\"><div class=\"right\"></div><div class=\"content\"><div class=\"box\"><div class=\"content\"><textarea>" + src + "</textarea></div><div class=\"bottom\"><div class=\"left\"><div class=\"right\"><div class=\"center\"></div></div></div></div></div><div align=\"right\">" + UI.redButton( Language.cancel, "cancel", "javascript:inserter.cancel();" ) + " " + UI.redButton( Language.save, "save", "javascript:inserter.save();" ) + "</div></div></div></div></div>" );
		
		showPopboxScreen();
		Animation.showInsertBox();
	}
}
