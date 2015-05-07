// JavaScript Document
function Callendar()
{
	this.target;
	this.ballon;
	
	this.date = new Date();
	
	this.days = function()
	{
		var c = "";
		for( var i = 0; i < Language.daysName.length; i++ ) c += "<th>" + Language.daysName[i].substr( 0, 3 ) + "</th>";
		return c;
	}
	
	this.generateMonth = function()
	{
		var month = "<table><thead><tr>" + this.days() + "</tr></thead><tbody><tr>";
		var temp = new Date( this.date.getFullYear(), this.date.getMonth(), 1 );
		var start = temp.getDay();
		temp.setMonth( this.date.getMonth() + 1 );
		temp.setDate( 0 );
		var end = temp.getDate();
		
		var t = 1 - start;
		for( var i = 0; i < 42; i++ )
		{
			var a = t < 1 ? "" : ( t > end ? "" : t );
			//alert( a );
			if( i % 7 == 0 ) month += '<th style="font-weight:bold;"><span onclick="callendar.selectDate(this)">' + a + '</span></th>';
			else month += '<th><span onclick="callendar.selectDate(this)">' + a + '</span></th>';
			
			if( i % 7 == 6 ) month += '</tr><tr>';
			
			t++;
		}
		
		return month + '</tbody></table>';
	};
	
	this.backMonth = function()
	{
		callendar.date.setMonth( callendar.date.getMonth() - 1 );
		callendar.update();
		
		var months = $( "#callendar .callendar_content table" ).get();
		Animation.backMonth( months[0], months[1] );
	}
	
	this.nextMonth = function()
	{
		callendar.date.setMonth( callendar.date.getMonth() + 1 );
		callendar.update();
		
		var months = $( "#callendar .callendar_content table" ).get();
		Animation.nextMonth( months[0], months[1] );
	}
	
	this.backYear = function()
	{
		callendar.date.setFullYear( callendar.date.getFullYear() - 1 );
		callendar.update();
		
		var months = $( "#callendar .callendar_content table" ).get();
		Animation.backMonth( months[0], months[1] );
	}
	
	this.nextYear = function()
	{
		callendar.date.setFullYear( callendar.date.getFullYear() + 1 );
		callendar.update();
		
		var months = $( "#callendar .callendar_content table" ).get();
		Animation.nextMonth( months[0], months[1] );
	}
	
	this.update = function()
	{
		$( "#callendar .callendar_content" ).prepend( this.generateMonth() );
		$t = $( "#callendar .callendar_month div span" ).get();
		
		$( $t[0] ).html( " " + Language.monthName[this.date.getMonth()] + " " );
		$( $t[1] ).html( " " + this.date.getFullYear() + " " );
	}
	
	this.show = function( target )
	{
		this.target = this.ballon.tposition = target;
		this.ballon.position();
		
		this.ballon.show( function() { document.getElementById( "container" ).onclick = callendar.hide } );
	}
	
	this.hide = function()
	{
		callendar.ballon.hide();
		document.getElementById( "container" ).onclick = null;
	}
	
	this.selectDate = function( target )
	{
		var month = this.date.getMonth() + 1;
		month = month < 10 ? "0" + month : month;
		var date = $( target ).html() < 10 ? "0" + $( target ).html() : $( target ).html();
		
		var temp = dateSqlToStamp( this.date.getFullYear() + "-" + month + "-" + date + " 00:00:00" ).split( " " )[0];
		$( this.target ).val( temp );
		callendar.hide();
	}
	
	this.init = function()
	{
		this.ballon = new Ballon( 'callendar', '<div id="callendar"><div class="callendar_month"><div><a href="javascript:callendar.backMonth();">&lt;</a> <span>' + Language.monthName[this.date.getMonth()] + '</span> <a href="javascript:callendar.nextMonth();">&gt;</a></div><div><a href="javascript:callendar.backYear();">&lt;</a> <span>' + this.date.getFullYear() + '</span> <a href="javascript:callendar.nextYear();">&gt;</a></div></div><div class="callendar_content"></div><br style="clear:both" /></div>' );
		
		$( "#callendar .callendar_content" ).append( this.generateMonth() );
	}
	
	this.init();
}