// JavaScript Document

Date.months = Language.monthName;
Date.weekdays = Language.daysName;

Date.prototype.getDOY = function() { var onejan = new Date(this.getFullYear(),0,1); return Math.ceil((this - onejan) / 86400000); };
function y2k(number) {  }

Date.prototype.getWeek = function() {
	var y2k = function( v ) { return ( v < 1000 ) ? v + 1900 : v };
	
	year = y2k( this.getFullYear() );
	month = this.getMonth();
	day = this.getDay();
	
	var when = new Date(year,month,day);
    var newYear = new Date(year,0,1);
    var offset = 7 + 1 - newYear.getDay();
    if (offset == 8) offset = 1;
    var daynum = ((Date.UTC(y2k(year),when.getMonth(),when.getDate(  ),0,0,0) - Date.UTC(y2k(year),0,1,0,0,0)) /1000/60/60/24) + 1;
    var weeknum = Math.floor((daynum-offset+7)/7);
	
    if (weeknum == 0) {
        year--;
        var prevNewYear = new Date(year,0,1);
        var prevOffset = 7 + 1 - prevNewYear.getDay();
        if (prevOffset == 2 || prevOffset == 8) weeknum = 53; else weeknum = 52;
    }
    return weeknum;
}

Date.prototype.toLocaleFormat = function( format )
{
	var a = this;
	var t = "";
	
	var g = function( v ) {
		v = String( v ).length > 2 ? Number( String( v ).substr( 1 ) ) : v;
		return v < 10 ? "0" + v : v;
	};
	
	var h = function( v ) { return v < 10 ? "00" + v : ( v < 100 ? "0" + v : v ) };
	
	var f = function( c ) {
		switch( c ) {
			case "a":
				return Date.weekdays[a.getDay()].substr( 0, 3 );
				break;
			
			case "A":
				return Date.weekdays[a.getDay()];
				break;
			
			case "b":
				return Date.months[a.getMonth()].substr( 0, 3 );
				break;
			
			case "h":
			case "B":
				return Date.months[a.getMonth()];
				break;
			
			case "c":
				return a.toLocaleString();
				break;
			
			case "C":
				return a.toString();
				break;
			
			case "d":
				return g( a.getDate() );
				break;
			
			case "D":
				return g( a.getMonth() + 1 ) + "/" + g( a.getDate() ) + "/" + g( a.getYear() );
				break;
			
			case "e":
				return a.getDate();
				break;
			
			case "H":
				return g( a.getHours() );
				break;
			
			case "I":
				return g( ( t = a.getHours() % 12 ) == 0 ? 12 : t );
				break;
			
			case "j":
				return h( a.getDOY() );
				break;
			
			case "m":
				return g( a.getMonth() + 1 );
				break;
			
			case "M":
				return g( a.getMinutes() );
				break;
			
			case "n":
				return "\n";
				break;
			
			case "p":
				return a.getHours() < 11 ? "a.m." : "p.m.";
				break;
			
			case "r":
				return g( ( t = a.getHours() % 12 ) == 0 ? 12 : t ) + ":" + g( a.getMinutes() ) + ":" + g( a.getSeconds() ) + " " + ( a.getHours() < 11 ? "a.m." : "p.m." );
				break;
			
			case "R":
				return g( a.getHours() ) + ":" + g( a.getMinutes() );
				break;
			
			case "t":
				return "	";
				break;
			
			case "X":
			case "T":
				return g( a.getHours() ) + ":" + g( a.getMinutes() ) + ":" + g( a.getSeconds() );
				break;
			
			case "S":
				return g( a.getSeconds() );
				break;
			
			case "w":
			case "u":
				return a.getDay();
				break;
			
			case "x":
			case "W":
			case "V":
			case "U":
				return a.getWeek();
				break;
			
			case "y":
				return g( a.getYear() );
				break;
			
			case "Y":
				return a.getFullYear();
				break;
			
			case "%":
				return "%";
		}
	}
	
	for( var i = 0; i < format.length; i++ ) {
		var c = format.charAt( i );
		if(  c == "%" ) { i++; t += f( format.charAt( i ) ); }
		else t += c;
	}
	
	return t;
}

Date.fromLocaleFormat = function( format, string )
{
	var a = new Date( 0, 0, 0, 0, 0, 0 );
	
	var g = function( v ) {
		v = String( v ).length > 2 ? Number( String( v ).substr( 1 ) ) : v;
		return v < 10 ? "0" + v : v;
	};
	
	var h = function( v ) { return v < 10 ? "00" + v : ( v < 100 ? "0" + v : v ) };
	
	var f = function( c ) {
		switch( c ) {
			case "a":
				string = string.substring( 3 );//Date.weekdays[a.getDay()].substr( 0, 3 );
				break;
			
			case "A":
				for( var i in Date.weekdays ) if( string.indexOf( Date.weekdays[i] ) == 0 ) { string = string.substr( Date.weekdays[i].length ); break };
				break;
			
			case "b":
				for( var i in Date.months ) if( string.indexOf( Date.months[i].substr( 0, 3 ) ) == 0 ) { string = string.substr( 3 ); a.setMonth( i ); break };
				break;
			
			case "h":
			case "B":
				for( var i in Date.months ) if( string.indexOf( Date.months[i] ) == 0 ) { string = string.substr( Date.months[i].length ); a.setMonth( i ); break };
				break;
			
			case "c":
			case "C":
				break;
			
			case "d":
				a.setDate( string.substr( 0, 2 ) );
				string = string.substr( 2 );
				break;
			
			case "D":
				a.setMonth( Number( string.substr( 0, 2 ) ) - 1 );
				a.setDate( string.substr( 3, 2 ) );
				a.setYear( string.substr( 6, 2 ) );
				string = string.substr( 8 );
				break;
			
			case "e":
				var n1 = Number( string.substr( 0, 1 ) );
				if( n1 < 4 && Number( string.substr( 0, 2 ) ) ) { a.setDate( string.substr( 0, 2 ) ); string = string.substr( 2 ) }
				else { a.setDate( n1 ); string = string.substr( 1 ) };
				break;
				
			case "H":
				a.setHours( string.substr( 0, 2 ), a.getMinutes(), a.getSeconds() );
				string = string.substr( 2 );
				break;
			
			case "I":
				a.setHours( string.substr( 0, 2 ) );
				string = string.substr( 2 );
				break;
			
			case "j":
				a.setMonth( 0, string.substr( 0, 3 ) );
				string = string.substr( 3 );
				break;
			
			case "m":
				a.setMonth( Number( string.substr( 0, 2 ) ) - 1 );
				string = string.substr( 2 );
				break;
			
			case "M":
				a.setMinutes( string.substr( 0, 2 ) );
				string = string.substr( 2 );
				break;
			
			case "n":
				string = string.substr( 1 );
				break;
			
			case "p":
 				a.setHours( ( a.getHours() < 11 ? a.getHours() : 0 ) + ( string.substr( 0, 4 ).toLowerCase() == "p.m." ? 12 : 0 ) );
				string = string.substr( 4 );
				break;
			
			case "r":
				var hour = Number( string.substr( 0, 2 ) );
				a.setHours( ( hour < 11 ? hour : 0 ) + ( string.substr( 9, 4 ).toLowerCase() == "p.m." ? 12 : 0 ) );
				a.setMinutes( string.substr( 3, 2 ) );
				a.setSeconds( string.substr( 6, 2 ) );
				string = string.substr( 13 );
				break;
			
			case "R":
				a.setMinutes( string.substr( 3, 2 ) );
				a.setHours( string.substr( 0, 2 ) );
				string = string.substr( 5 );
				break;
			
			case "t":
				string = string.substr( 1 );
				break;
			
			case "X":
			case "T":
				a.setSeconds( string.substr( 6, 2 ) );
				a.setMinutes( string.substr( 3, 2 ) );
				a.setHours( string.substr( 0, 2 ) );
				string = string.substr( 8 );
				break;
			
			case "S":
				a.setSeconds( string.substr( 0, 2 ) );
				string = string.substr( 2 );
				break;
			
			case "%":
			case "w":
			case "u":
				string = string.substr( 1 );
				break;
			
			case "x":
			case "W":
			case "V":
			case "U":
				return a.getWeek();
				break;
			
			case "y":
				a.setYear( string.substr( 0, 2 ) );
				string = string.substr( 2 );
				break;
			
			case "Y":
				a.setFullYear( string.substr( 0, 4 ) );
				string = string.substr( 4 );
				break;
		}
	}
	
	for( var i = 0; i < format.length; i++ ) {
		var c = format.charAt( i );
		if(  c == "%" ) { i++; f( format.charAt( i ) ); }
		else string = string.substr( 1 );
	}
	
	return a;
}

Date.prototype.toDatetime = function()
{
	return this.toLocaleFormat( "%Y-%m-%d %H:%M:%S" );
}
