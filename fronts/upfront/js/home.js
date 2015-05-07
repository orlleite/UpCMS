function Home()
{
	this.show = function()
	{
		var func = function()
		{
			$( "#main_content" ).html( "<span class=\"icon icon_home\"></span><h1>" + Language.home + "</h1>" );
			Animation.showMainContent();
		}
		
		Animation.hideMainContent( func );
	}
}