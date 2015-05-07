// JavaScript Document
function Quicker()
{
	this.MAX_LENGTH_NAME = 25;
	
	this.saving = false;
	
	this.relation;
	this.target;
	this.fields;
	this.index;
	this.data;
	this.row;
	this.id;
	
	this.editing;
	this.ballon;
	
	this.createTitleValue = function( rel, name, type, value, params, options )
	{
		return "<b>" + quicker.createSimpleValue( rel, name, type, value, params, options ) + "</b>";
	}
	
	this.createSimpleValue = function( rel, name, type, value, params, options )
	{
		if( type == "simpletext" )
			return value + "<br />";
		else if( type == "image" || type == "file" )
		{
			var tname;
			if( value != "" )
			{
				tname = value.split( "/" );
				tname = tname[tname.length - 1];
			}
			else
			{
				tname = Language.undefined;
			}
			
			return ( tname.length > quicker.MAX_LENGTH_NAME ? tname.substr( 0, quicker.MAX_LENGTH_NAME - 3 ) + "..." : tname ) + "<br />";
		}
		else if( type == "options" )
		{
			var c = "";
			
			var i = 0;
			var value = Number( value ) || 0;
			
			for( var o in options )
			{
				if( value >> o & 0x01 )
				{
					if( i != 0 ) c += ", ";
					c += options[o];
					i++;
				}
			}
			
			return c + "<br />";
		}
		else if( type == "select" )
		{
			for( var o in options ) 
			{
				if( value == o ) return options[o] + "<br />";
			}
		}
		else if ( type == "datetime" )
			return value + "<br />";
		else return "";
	}
	
	this.createTitleField = function( rel, obj, value )
	{
		return quicker.createSimpleField( rel, obj, value );
	}
	
	this.createSimpleField = function( rel, obj, value )
	{
		if( obj.type == "datetime" )
			return obj.strict ? UI.quickDatetime( rel, obj.name, value, obj.params, true ) : UI.quickDatetime( rel, obj.name, value, obj.params );
		else if( obj.type == "simpletext" )
			return obj.strict ? UI.quickSimpleText( rel, obj.name, value, true ) : UI.quickSimpleText( rel, obj.name, value );
		else if( obj.type == "options" )
			return UI.quickOptions( rel, obj.name, value, obj.options, obj.params );
		else if( obj.type == "select" )
			return UI.quickSelect( rel, obj.name, value, obj.options );
		else if( obj.type == "image" )
			return UI.quickImage( rel, obj.name, value, obj.params, obj.multiples );
		else if( obj.type == "file" )
			return UI.quickFile( rel, obj.name, value, obj.params, obj.multiples );
		else return "";
	}
	
	this.submitMultipleFiles = function( target, data )
	{
		var index = 0;
		this.saving = true;
		this.button = target;
		var request = new Object();
		quicker.data = new Object();
		
		var finished = function()
		{
			quicker.editing = quicker.saving = false;
			quicker.deleteEditingBallon();
			
			editer.showSimpleTable( quicker.row, quicker.relation );
		}
		
		var listener = progressBox( Language.addingItems, data[0], finished );
		
		var next = function( nothing )
		{
			index++;
			if( index == data.length ) listener.complete();
			else func();
		}
		
		var func = function()
		{
			listener.progress( data[index], index / data.length );
			
			if( !editer.tableData[quicker.row].list[quicker.index] ) editer.tableData[quicker.row].list[quicker.index] = new Object();
			
			editer.tableData[quicker.row].list[quicker.index][target] = request[target] = data[index];
			if( quicker.fields[target][1] == "image" || quicker.fields[target][1] == "file" ) $( quicker.target ).find( "img" ).attr( "src", "./utils/thumb.php?w=150&h=100&src=" + ( request[target] != "" ? request[target] : THEME_FOLDER + "/imgs/undefined.png" ) );
			
			request.id = quicker.id;
			request.rel = quicker.relation;
			if( quicker.id == 0 ) $.post( "?Edit::save", request, next, "text" );
			else $.post( "?Edit::update", request, next, "text" );
		}
		
		func();
	}
	
	this.selectFileBallon = function( target )
	{
		var func2 = function( data )
		{
			var value = data[0].split( "/" );
			value = value[value.length - 1];
			
			$( "#quick_editing_" + target + " a" ).attr( "href", UPLOAD_FOLDER + data[0] ).find( "img" ).attr( "src", "./utils/thumb.php?w=56&h=42&src=" + UPLOAD_FOLDER + data[0] );
			$( "#quick_editing_" + target + " .image_info i" ).html( value );
		}
		
		var func = function( data )
		{
			if( data.length > 1 )
				confirmBox( Language.attention, Language.sureSendAllOfThem, function() { quicker.submitMultipleFiles( target, data ) }, function() { func2( data ) } );
			else
				func2( data );
		}
		
		var params = parseParams( $( "#quick_editing_" + target ).attr( "params" ) );
		
		params.types = params.types ? params.types : [lang.images, "*.jpg;*.gif;*.png"];
		
		params.target = quicker.relation;
		params.id = quicker.id;
		
		uploader.init( func, false, params );
	}
	
	this.selectFile = function( target )
	{
		var func = function( data )
		{
			var value = data[0].split( "/" );
			value = value[value.length - 1];
			
			$( "#quick_editing_" + target + " a" ).attr( "href", UPLOAD_FOLDER + data[0] ).find( "img" ).attr( "src", "./utils/thumb.php?w=56&h=42&src=" + UPLOAD_FOLDER + data[0] );
			$( "#quick_editing_" + target + " .image_info i" ).html( value );
		}
		
		var params = parseParams( $( "#quick_editing_" + target ).attr( "params" ) );
		
		params.types = params.types ? params.types : [lang.images, "*.jpg;*.gif;*.png"];
		
		params.target = quicker.relation;
		params.id = quicker.id;
		
		uploader.init( func, true, params );
	}
	
	this.deleteFile = function( target )
	{
		$( "#quick_editing_" + target + " a" ).attr( "href", UPLOAD_FOLDER + data[0] ).find( "img" ).attr( "src", "./utils/thumb.php?w=56&h=42&src=" + lang.undefined );
		$( "#quick_editing_" + target + " .image_info i" ).html( lang.undefined );
	}
	
	this.removeItem = function( target, rel, id )
	{
		if( quicker.saving ) return;
		
		var func = function()
		{
			$.post( "?Edit::remove", { rel:rel, id:id }, function() { alertBox( Language.done, Language.deletedSucessfully ); Animation.removeTableRow( "#" + target ); } );
		}
		
		confirmBox( Language.attention, Language.sureDeleteItem, func );
	}
	
	this.submitHandlerBallon = function( target )
	{
		this.saving = true;
		this.button = target;
		var request = new Object();
		quicker.data = new Object();
		
		var n = 0;
		
		if( !editer.tableData[quicker.row].list[quicker.index] ) editer.tableData[quicker.row].list[quicker.index] = { };
		
		for( var t in quicker.fields )
		{
			if( t != "id" )
			{
				var value = quicker.getFieldContent( quicker.fields, t );
				
				if( quicker.fields[t].validate )
					if( !Validator.parseValidator( quicker.fields[t].validate, value ) )
					{
						alertBox( Language.ops, Language.invalidFieldPt1 + quicker.fields[t].name + Language.invalidFieldPt2 );
						return;
					}
				
				editer.tableData[quicker.row].list[quicker.index][t] = request[t] = value;
			}
		}
		
		var func = function( data )
		{
			quicker.editing = quicker.saving = false;
			quicker.deleteEditingBallon();
			
			editer.showSimpleTable( quicker.row, quicker.relation );
		}
		
		request.id = quicker.id;
		request.rel = quicker.relation;
		$( target ).attr( "disabled", "true" );
		if( quicker.id == 0 ) $.post( "?Edit::save", request, func, "text" );
		else $.post( "?Edit::update", request, func, "text" );
	}
	
	this.getFieldContent = function( f, i )
	{
		if( f[i].type == "simpletext" )
			return $( "#quick_editing_" + i ).val();
		else if( f[i].type == "image" )
			return $( "#quick_editing_" + i + " .img_link" ).attr( "href" ).substr( UPLOAD_FOLDER.length );
		else if( f[i].type == "options" )
		{
			if( f[i].params == "value='string'" )
			{
				var n = "";
				$( "#quick_editing_"+ i + " .options_" + i + ":checked" ).each( function( i, v ) { n += v.name + ";" } );
			}
			else
			{
				var n = 0;
				$( "#quick_editing_"+ i + " .options_" + i + ":checked" ).each( function( i, v ) { n += 1 << Number( v.name ) } );
			}
			
			return n;
		}
		else if( f[i].type == "select" )
			return $( "#quick_editing_" + i ).val();
		else if( f[i].type == "file" )
			return $( "#quick_editing_" + i + " .img_link" ).attr( "href" ).substr( UPLOAD_FOLDER.length );
		else if( f[i].type == "datetime" )
		{
			var t = $( "#quick_editing_" + i );
			return Date.fromLocaleFormat( TimeStamp, editer.formatText( t.find( ".date" ).val(), "**-**-****" ) + " " + t.find( ".hour" ).val() ).toDatetime();
		}
		else return "";
	}
	
	this.submitHandler = function( target )
	{
		this.saving = true;
		this.button = target;
		var request = new Object();
		
		var n = 0;
		
		for( var t in quicker.fields )
		{
			if( t != "id" )
			{
				lister.data.list[lister.quickEditing.index][t] = request[t] = quicker.getFieldContent( quicker.fields, t );
				
				if( quicker.fields[t].type == "image" || quicker.fields[t].type == "file" )
					$( "#" + lister.quickEditing.rel + "_" + lister.quickEditing.id + " td:eq(" + n + ") img" ).attr( "src", "./utils/thumb.php?w=100&h=70&src=" + ( request[t] != "" ? UPLOAD_FOLDER + request[t] : THEME_FOLDER + "/imgs/undefined.png" ) );
				else if( lister.data.fields[t].type == "select" )
					$( "#" + lister.quickEditing.rel + "_" + lister.quickEditing.id + " td:eq(" + n + ") i" ).html( request[t] );
				else if( lister.data.fields[t].type == "options" )
				{
					var i = 0;
					var opts = lister.data.fields[t].options;
					var v = Number( request[t] ) || request[t];
					
					var value = new Array();
					if( lister.data.fields[t].params == "value='string'" )
						for( var o in opts ) 
						{
							if( v.indexOf( o + ";" ) != -1 ) value.push( opts[o] );
							i++;
						}
					else
						for( var o in opts )
						{
							if( v >> o & 0x01 ) value.push( opts[o] );
							i++;
						}
					
					$( "#" + lister.quickEditing.rel + "_" + lister.quickEditing.id + " td:eq(" + n + ") i" ).html( "<i>" + value.join( ", " ) + "</i>" );
				}
				else if( lister.data.fields[t].type == "datetime" )
					content += $( "#" + lister.quickEditing.rel + "_" + lister.quickEditing.id + " td:eq(" + n + ") i" ).html( dateSqlToStamp( request[t] ) );
				else
					$( "#" + lister.quickEditing.rel + "_" + lister.quickEditing.id + " td:eq(" + n + ") i" ).html( request[t] );
				
				n++;
			}
		}
		
		var func = function( data )
		{
			quicker.saving = false;
			if( data == 1 ) lister.hideQuickEdit();
		}
		
		request.id = lister.quickEditing.id;
		request.rel = lister.quickEditing.rel;
		$( target ).attr( "disabled", "true" );
		$.post( "?Edit::update",request, func, "text" );
	}
	
	this.createEditRow = function( target, fields, data, columns, relation )
	{
		quicker.relation = relation;
		quicker.target = target;
		quicker.fields = fields;
		quicker.id = data.id;
		quicker.data = data;
		
		var id, n = 0;
		var content = "<tr id=\"" + target + "_quick\"><td colspan=\"" + ( columns > 4 ? 4 : columns ) + "\" class=\"quick_editor\"><div class=\"quickedit\" id=\"quick_edit_container\">";
		
		var collength = Math.ceil( columns / 2 );
		
		var col1 = "<div class=\"block\">", col2 = "</div><div class=\"block\">";
		
		for( var t in data )
		{
			if( n == 0 )
			{
				id = data[t];
			}
			else
			{
				if( n <= collength ) col1 += quicker.createSimpleField( t, fields[t], data[t] );
				else col2 += quicker.createSimpleField( t, fields[t], data[t] );
			}
			
			n++;
		}
		
		content += col1 + col2 + "</div><br style=\"clear:both\" /><div class=\"actionbox\">" + UI.redButton( Language.cancel, "cancel", "javascript:lister.hideQuickEdit()" ) + " " + UI.redButton( Language.save, "save", "javascript:quicker.submitHandler(this)" ) + "</div></div></td></tr>";
		
		return content;
	}
	
	this.createEditBallon = function( target, row, relation, index, edit )
	{
		if( quicker.editing ) return;
		
		var o = editer.tableData[row];
		this.relation = relation;
		this.target = target;
		this.index = index;
		this.row = row;
		this.data = data = o.list[index];
		this.fields = fields = o.fields;
		
		var n = 0;
		var content = "";
		
		for( var t in data )
		{
			if( n == 0 )
				id = data[t];
			else if ( n == 1 )
				content += quicker.createTitleValue( t, fields[t].name, fields[t].type, data[t], fields[t].params, fields[t].options ) + "<br />";
			else
				content += quicker.createSimpleValue( t, fields[t].name, fields[t].type, data[t], fields[t].params, fields[t].options ) + "<br />";
			
			n++;
		}
		
		edit = edit ? "editer.boxEditer(\'" + row + "\', '" + relation + "', '" + id + "' );" : "quicker.startEditingBallon();";
		
		content += "<br /><div class=\"right_menu\"><span class=\"general_icon icon_quick_edit\"></span><a href=\"javascript:" + edit + "\">" + Language.edit + "</a> <span class=\"general_icon icon_delete\"></span><a href=\"javascript:quicker.removeItem( \'" + row + "-" + id + "\', \'" + relation + "\', " + id + " );\">" + Language.deletes + "</a></div>";
		
		if( quicker.ballon ) quicker.ballon.destroy();
		
		quicker.ballon = new Ballon( "quicker_" + quicker.id, content );
		quicker.ballon.tposition = target;
		quicker.ballon.position();
		quicker.ballon.show();
		
		quicker.ballon.jquery().mouseenter( function(){ $(this).attr( "over", "true" ) } ).mouseleave( function(){ $(this).attr( "over", "false" ) } ).attr( "over", "false" );
	}
	
	this.createAddBallon = function( row, relation )
	{
		if( quicker.editing ) return;
		
		var o = editer.tableData[row];
		this.target = target = $( "#add_btn_" + row + " a" );
		this.relation = relation;
		this.editing = true;
		this.index = 0;
		this.row = row;
		
		this.fields = fields = o.fields;
		
		var n = 0;
		var field;
		var content = "<div style=\"width:345px\">";
		
		for( var t in fields )
		{
			if( n == 0 )
				quicker.id = 0;
			else
			{
				if( fields[t].type == "image" ) fields[t].multiples = true;
				
				if ( n == 1 )
					content += quicker.createTitleField( t, fields[t], "" );
				else
					content += quicker.createSimpleField( t, fields[t], "" );
			}
			n++;
		}
		
		if( quicker.ballon ) quicker.ballon.destroy();
		
		quicker.ballon = new Ballon( "quicker_" + quicker.id, content + "</div><div class=\"right_menu\">" + UI.redButton( Language.cancel, "cancel", "javascript:quicker.deleteEditingBallon();" ) + " " + UI.redButton( Language.save, "save", "javascript:quicker.submitHandlerBallon(this);" ) + "</div>" );//"<span class=\"red_button\"><span class=\"save\"></span> <a href=\"javascript:quicker.submitHandlerBallon(this);\">Salvar</a></span></div>" );
		quicker.ballon.tposition = target;
		quicker.ballon.position();
		quicker.ballon.show();
	}
	
	this.stopEditingBallon = function()
	{
		quicker.ballon.jquery().mouseleave( function(){ b.destroy(); b = null; } );
		
		quicker.editing = false;
		
		var n = 0;
		var content = "";
		
		for( var t in data )
		{
			if( n == 0 )
			{
				id = data[t];
			}
			else if ( n == 1 )
			{
				content += quicker.createTitleValue( t, fields[t].name, fields[t].type, data[t], fields[t].params, fields[t].options ) + "<br />";
			}
			else
			{
				content += quicker.createSimpleValue( t, fields[t].name, fields[t].type, data[t], fields[t].params, fields[t].options ) + "<br />";
			}
			
			n++;
		}
		
		content += "<br /><div class=\"right_menu\"><span class=\"general_icon icon_quick_edit\"></span><a href=\"javascript:quicker.startEditingBallon();\">" + Language.edit + "</a> <span class=\"general_icon icon_delete\"></span><a href=\"javascript:quicker.removeItem( \'" + quicker.row + "-" + quicker.id + "\', \'" + quicker.relation + "\', " + id + " );\">" + Language.deletes + "</a></div>";
		
		quicker.ballon.html( content );
	}
	
	this.startEditingBallon = function()
	{
		quicker.editing = true;
		
		var n = 0;
		var field;
		var content = "<div style=\"width:345px\">";
		var buttons = UI.redButton( Language.cancel, "cancel", "javascript:quicker.stopEditingBallon();" ) + " ";//"<span class=\"red_button\"><span class=\"cancel\"></span> <a href=\"javascript:quicker.stopEditingBallon();\">Cancelar</a></span> ";
		
		for( var t in data )
		{
			if( n == 0 )
				quicker.id = data[t];
			else
			{
				if ( n == 1 )
					content += quicker.createTitleField( t, fields[t], data[t] );
				else
					content += quicker.createSimpleField( t, fields[t], data[t] );
			}
			n++;
		}
		
		quicker.ballon.html( content + "</div><div class=\"right_menu\">" + buttons + UI.redButton( Language.save, "save", "javascript:quicker.submitHandlerBallon(this);" ) + "</div>" );
		quicker.ballon.position();
	}
	
	this.deleteEditingBallon = function()
	{
		if( quicker.ballon )
		{
			quicker.ballon.destroy();
			quicker.editing = false;
			quicker.ballon = null;
		}
	}
	
	this.addressHandler = function()
	{
		// if( quicker.editing ) quicker.stopEditingBallon();
		if( quicker.ballon ) quicker.deleteEditingBallon();
	}
	
	this.hideEditBallon = function()
	{
		if( quicker.editing ) return;
		
		if( quicker.ballon )
		{
			var b = quicker.ballon;
			
			var func = function()
			{
				if( quicker.editing ) return;
				
				var func2 = function()
				{
					if( quicker.editing ) return;
					
					if( b.overring() == "false" )
					{
						b.destroy();
						b = null;
						
						return false;
					}
					
					return true;
				};
				
				if( func2() ) b.jquery().mouseleave( func2 );
			}
			
			setDelay( func, 1000 );
		}
	}
}