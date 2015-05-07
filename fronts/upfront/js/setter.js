function Setter()
{
	var rel;
	var data;
	var button;
	
	this.createField = function( id, obj )
	{
		if( obj.type == "simpletext" )
			return obj.strict ? UI.uSimpleText( id, obj.name, obj.value, true, obj.about || " " ) : UI.uSimpleText( id, obj.name, obj.value, false, obj.about || " " );
		if( obj.type == "password" )
			return obj.strict ? UI.uPassword( id, obj.name, obj.value, true, obj.about || " " ) : UI.uPassword( id, obj.name, obj.value, false, obj.about || " " );
		else if( obj.type == "text" )
			return obj.strict ? UI.uText( id, obj.name, obj.value, true, obj.about || " " ) : UI.uText( id, obj.name, obj.value, false, obj.about || " " );
		else if( obj.type == "image" )
			return UI.uImage( id, obj.name, obj.value, obj.params || "", obj.about || " " );
		else if( obj.type == "options" )
			return UI.uOptions( id, obj.name, obj.value, obj.options, obj.params, obj.about || " " );
		else if( obj.type == "select" )
			return UI.uSelect( id, obj.name, obj.value, obj.options, obj.about || " " );
		else if( obj.type == "select-info" )
			return UI.uSelectInfo( id, obj.name, obj.value, obj.options, obj.about || " " );
		else if ( obj.type == "file" )
			return UI.uFile( id, obj.name, obj.value, obj.params, obj.about || " " );
		else if ( obj.type == "datetime" )
			return obj.strict ? UI.uDatetime( id, obj.name, obj.value, obj.params, true, obj.about || " " ) : UI.uDatetime( id, obj.name, obj.value, obj.params, false, obj.about || " " );
		else if ( obj.type == "color" )
			return obj.strict ? UI.uColor( id, obj.name, obj.value, obj.params, true, obj.about || " " ) : UI.uColor( id, obj.name, obj.value, obj.params, false, obj.about || " " );
		else if( obj.type == "number" )
			return obj.strict ? UI.uNumber( id, obj.name, obj.value, true, obj.about || " " ) : UI.uNumber( id, obj.name, obj.value, false, obj.about || " " );
		else if( obj.type == "number2d" )
			return obj.strict ? UI.uNumber2D( id, obj.name, obj.value, true, obj.about || " " ) : UI.uNumber2D( id, obj.name, obj.value, false, obj.about || " " );
		else if( obj.type == "onoff" )
			return UI.uOnoff( id, obj.name, obj.value, obj.options, obj.about || " " );
		else if( obj.type == "label" )
			return UI.uLabel( id, obj.name, obj.value );
		else if( obj.type == "link" )
			return UI.uLabel( id, obj.name, obj.value );
		else if( obj.type == "lonoff" )
			return UI.luOnoff( id, obj.value, obj.options, "setter.changeOnoff" );
		else if( obj.type == "llabel" )
			return UI.luLabel( id, obj.value );
		else if( obj.type == "llink" )
			return UI.luLink( id, obj.name, obj.value );
		else return "";
	}
	
	this.getFieldContent = function( f, i )
	{
		if( f.type == "simpletext" )
			return $( "#" + i ).val();
		else if( f.type == "password" )
			return $( "#" + i ).val();
		else if( f.type == "text" )
			return $( "#" + i ).val();
		else if( f.type == "image" )
			return $( "#" + i + " .img_link" ).attr( "href" ).substr( UPLOAD_FOLDER.length );
		else if( f.type == "options" )
		{
			if( f.params == "value='string'" )
			{
				var n = "";
				$( "#"+ i + " .options_" + i + ":checked" ).each( function( i, v ) { n += v.name + ";" } );
			}
			else
			{
				var n = 0;
				$( "#"+ i + " .options_" + i + ":checked" ).each( function( i, v ) { n += 1 << Number( v.name ) } );
			}
			
			return n;
		}
		else if( f.type == "select" )
			return $( "#" + i ).val();
		else if( f.type == "select-info" )
			return $( "#" + i ).val();
		else if( f.type == "file" )
			return $( "#" + i + " .img_link" ).attr( "href" ).substr( UPLOAD_FOLDER.length );
		else if( f.type == "datetime" )
		{
			var t = $( "#" + i );
			return Date.fromLocaleFormat( TimeStamp, editer.formatText( t.find( ".date" ).val(), "**-**-****" ) + " " + t.find( ".hour" ).val() ).toDatetime();
		}
		else if ( f.type == "color" )
			return $( "#" + i ).val();
		else if( f.type == "number" )
			return $( "#" + i ).val();
		else if( f.type == "number2d" )
		{
			var t = $( "#" + i );
			return t.find( ".value0" ).val() + ";" + t.find( ".value1" ).val();
		}
		else if( f.type == "onoff" )
			return $( "#" + i ).attr( "checked" );
		else return "";
	}
	
	this.submitHandler = function()
	{
		var data = setter.data;
		var request = { };
		for( var g in data.groups )
		{
			var group = data.groups[g];
			for( var f in group.fields )
			{
				var target = setter.rel + "_" + g + "_" + f;
				request[target] = setter.getFieldContent( group.fields[f], target );
			}
		}
		
		deactivateRedButton( setter.button );
		changeRedButtonValue( setter.button, Language.savingChanges );
		
		request.rel = setter.rel;
		$.post( "?Set::set", request, this.submitLoadedHandler, "json" );
	}
	
	this.submitLoadedHandler = function( data )
	{
		var func = function()
		{
			if( data.refresh ) window.location.lang( true );
		}
		
		if( data.setter )
			sucessBox( Language.done, Language.changedSucessfully, func );
		else
			alertBox( Language.attention, data.error );
		
		activateRedButton( setter.button );
		changeRedButtonValue( setter.button, Language.saveChanges );
	}
	
	this.changeOnoff = function( t )
	{
		request[t] = $( "#" + t ).attr( "checked" );
		request.rel = setter.rel;
		
		$.post( "?Set::set", request, this.simpleSetLoadedHandler, "json" );
	}
	
	this.set = function( target )
	{
		var request = { };
		request[target] = "true";
		request.rel = setter.rel;
		
		$.post( "?Set::set", request, this.simpleSetLoadedHandler, "json" );
	}
	
	this.simpleSetLoadedHandler = function( data )
	{
		if( data )
		{
			if( data.refresh ) window.location.reload( true );
			if( data.updater ) setter.show( setter.rel );
		}
	}
	
	this.createList = function( id, obj )
	{
		var content = "<table class=\"table_list\"><thead><tr>";
		var fields = "";
		var options = { };
		
		for( var t in obj.columns )
		{
			fields += "<td width=\"" + obj.columns[t].width +  "\">" + obj.columns[t].name + "</td>";
		}
		
		content += fields + "</tr></thead><tfoot><tr>" + fields + "</tr></tfoot><tbody>";
		
		for( var t in obj.rows )
		{
			var r = obj.rows[t];
			
			content += "<tr>";
			
			for( var c in obj.columns )
			{
				var options = new Array();
				var column = obj.columns[c];
				
				for( var a in column.fields )
				{
					column.fields[a].value = r[c][a];
					options.push( setter.createField( id + "_" + t + "_" + a, column.fields[a] ) );
				}
				
				content += "<td>" + r[c].content + "<div class=\"options\">" + options.join( "<span class=\"vertical_bar\"></span>" ) + "</div></td>";
			}
			
			content += "</tr>";
		}
		
		content += "</tbody></table>";
		
		return content;
	}
	
	this.loaderHandler = function( data )
	{
		var func = function()
		{
			var content = "<span class=\"icon icon_settings\"></span> <h1>" + data.name + "</h1><form class=\"editor\"><div class=\"set_content\">";
			
			var first = null;
			
			for( var g in data.groups )
			{
				var group = data.groups[g];
				content += "<h3>" + group.name +  "</h3>";
				
				if( group.list )
				{
					content += setter.createList( setter.rel + "_" + g, group.list );
				}
				
				for( var f in group.fields )
				{
					content += setter.createField( setter.rel + "_" + g + "_" + f, group.fields[f] );
				}
				content += "<br /><br />";
			}
			
			setter.button = setter.rel + "_setter_btn";
			
			var saveButton = data.saveButton == false ? '' : UI.redButton( Language.saveChanges, "save", "javascript:setter.submitHandler();", setter.button );
			content += saveButton + "<br /><br /></div></form><br /><br />";
			
			$( "#main_content" ).html( content );
			Animation.showMainContent();
			UI.startSwitchers();
			UI.startTinymce();
		}
		
		setter.data = data;
		Animation.hideMainContent( func );
		hideLoadingScreen();
	}
	
	this.show = function( rel )
	{
		showLoadingScreen();
		this.rel = rel;
		
		$.post( "?Set::get", { rel:rel }, this.loaderHandler, "json" );
	}
}
