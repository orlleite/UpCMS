function Editer()
{
	this.id;
	this.target;
	
	this.data;
	this.type;
	this.groups;
	this.button;
	this.tableData;
	
	this.createFirstField = function( rel, obj )
	{
		//if( obj.type == "simpletext" )
		//	return obj.strict ? UI.firstSimpleText( rel, obj.value, true ) : UI.firstSimpleText( rel, obj.value );
		//else
			return this.createSimpleField( rel, obj );
	}
	
	this.createSimpleField = function( rel, obj )
	{
		if( obj.type == "simpletext" )
			return UI.simpleText( rel, obj.name, obj.value, obj.strict ? true : false );
		if( obj.type == "password" )
			return UI.password( rel, obj.name, obj.value );
		else if( obj.type == "text" )
			return UI.text( rel, obj.name, obj.value );
		else if( obj.type == "simplehtml" )
			return UI.simpleHtml( rel, obj.name, obj.value, obj.params );
		else if( obj.type == "html" )
			return UI.html( rel, obj.name, obj.value, obj.params );
		else if( obj.type == "image" )
			return UI.image( rel, obj.name, obj.value, obj.params );
		else if( obj.type == "options" )
			return UI.options( rel, obj.name, obj.value, obj.options, obj.params );
		else if( obj.type == "select" )
			return UI.select( rel, obj.name, obj.value, obj.options, obj.params, obj.strict ? true : false );
		else if ( obj.type == "file" )
			return UI.file( rel, obj.name, obj.value, obj.params );
		else if ( obj.type == "datetime" )
			return UI.datetime( rel, obj.name, obj.value, obj.params, obj.strict ? true : false );
		else if( obj.type == "switch" )
			return UI.switcher( rel, obj.name, obj.value, obj.options, "editer.switchChange(this)" );
		else if( obj.type == "simpletable" )
			return UI.simpleTable( rel, obj.name, obj.params, editer.target + "." + rel + "=" + editer.id, editer.id == 0 );
		else if( obj.type == "table" )
			return UI.table( rel, obj.name, obj.params, editer.target + "." + rel + "=" + editer.id, editer.id == 0 );
		else if ( obj.type == "color" )
			return obj.strict ? UI.color( rel, obj.name, obj.value, obj.params, true ) : UI.color( rel, obj.name, obj.value, obj.params );
		else if ( obj.type == "categories" )
			return UI.categories( rel, obj.name, obj.value, obj.options );
		else if ( obj.type == "tags" )
			return UI.tags( rel, obj.name, obj.value, obj.options );
		else if( obj.type == "group" )
		{
			editer.groups[rel] = { relation:obj.rel, value:obj.value }; 
			return UI.group( rel, obj.rel, obj.value, obj.params, editer.getGroupContent( obj.fields ) );
		}
		else if( obj.type == "number" )
			return obj.strict ? UI.number( rel, obj.name, obj.value, true ) : UI.number( rel, obj.name, obj.value );
		else return "";
	}
	
	this.getGroupContent = function( fields )
	{
		var content = "";
		for( var i in fields ) content += editer.createSimpleField( i, fields[i] );
		return content;
	}
	
	this.switchChange = function( t )
	{
		var a = $( t );
		
		var n = a.attr( "name" );
		var g = editer.groups;
		var v = a.val();
		
		for( var i in g )
		{
			if( g[i].relation == n )
			{
				if( g[i].value == v )
					Animation.showEditingGroup( $( "#editing_group_" + i ) );
				else
					Animation.hideEditingGroup( $( "#editing_group_" + i ) );
			}
		}
	}
	
	this.showSimpleTable = function( target, rel )
	{
		if( !editer.tableData ) editer.tableData = [];
		
		var func2 = function( data )
		{
			var field;
			var content = "";
			editer.tableData[target] = data;
			
			var n = 0;
			for( var i in data.fields )
			{
				if( n == 1 )
				{
					field = data.fields[i];
					field.rel = i;
					break;
				}
				n++;
			}
			
			if( field.type == "image" || field.type == "file" )
			{
				for( var i in data.list )
				{
					var t = target + "-" + data.list[i].id;
					content += "\
						<div id=\"" + t + "\" onMouseOver=\"quicker.createEditBallon( this, \'" + target + "\', \'" + data.rel + "=" + editer.id + "\', " +  i + " )\" onMouseOut=\"quicker.hideEditBallon()\" class=\"editing_image\">\
							<img src=\"" + "./utils/thumb.php?w=150&h=100&src=" + UPLOAD_FOLDER + data.list[i][field.rel] + "\" />\
						</div>";
				}
			}
			else
			{
				for( var i in data.list )
				{
					var t = target + "-" + data.list[i].id;
					content += "<div id=\"" + t + "\" onMouseOver=\"quicker.createEditBallon( this, \'" + target + "\', \'" + data.rel + "=" + editer.id + "\', " +  i + ", true )\" onMouseOut=\"quicker.hideEditBallon()\" class=\"editing_text\">" + data.list[i][field.rel] + "</div>";
				}
			}
			
			var title = $( "#editing_" + target + " .title" );
			
			var temp = $( "#editing_content_" + target ).css( "overflow-y", "auto" ).css( "position", "relative" ).css( "height", "300px" ).html( content );
			
			var temp2 = $( "#editing_" + target );
			var btns = temp2.find( " .editing_image" );
			btns.mouseover( lister.tableOverHandler );
			btns.mouseout( lister.tableOutHandler );
			
			Animation.showSimpleTableStep2( temp, temp2 );
			temp2.find( "a" ).attr( "href", "javascript:quicker.createAddBallon( \'" + target + "\', \'" + data.rel + "=" + editer.id + "\' )" );
		}
		
		var func = function()
		{
			$.post( "?List::get", { rel:rel, page:0, order:"", direction:"" }, func2, "json" );
		}
		
		Animation.showSimpleTableStep1( target, func );
	}
	
	this.showTable = function( target, rel )
	{
		if( !editer.tableData ) editer.tableData = [];
		var func2 = function( data )
		{
			var field;
			var content = "";
			editer.tableData[target] = data;
			
			var n = 0;
			for( var i in data.fields )
			{
				if( n == 1 )
				{
					field = data.fields[i];
					field.name = i;
					break;
				}
				n++;
			}
			
			if( field.type == "image" || field.type == "file" )
			{
				for( var i in data.list )
				{
					var t = target + "-" + data.list[i].id;
					content += "<div id=\"" + t + "\" onMouseOver=\"quicker.createEditBallon( this, \'" + target + "\', \'" + data.rel + "=" + editer.id + "\', " +  i + " )\" onMouseOut=\"quicker.hideEditBallon()\" class=\"editing_image\"><img src=\"" + "./utils/thumb.php?w=150&h=100&src=" + UPLOAD_FOLDER + data.list[i][field.name] + "\" /></div>";
				}
			}
			else
			{
				for( var i in data.list )
				{
					var t = target + "-" + data.list[i].id;
					content += "<div id=\"" + t + "\" onMouseOver=\"quicker.createEditBallon( this, \'" + target + "\', \'" + data.rel + "=" + editer.id + "\', " +  i + ", true )\" onMouseOut=\"quicker.hideEditBallon()\" class=\"editing_text\">" + data.list[i][field.name] + "</div>";
				}
			}
			
			var title = $( "#editing_" + target + " .title" );
			
			var temp = $( "#editing_content_" + target ).css( "overflow-y", "auto" ).css( "position", "relative" ).css( "height", "300px" ).html( content );
			
			var temp2 = $( "#editing_" + target );
			var btns = temp2.find( " .editing_image" );
			btns.mouseover( lister.tableOverHandler );
			btns.mouseout( lister.tableOutHandler );
			
			Animation.showSimpleTableStep2( temp, temp2 );
			temp2.find( "a" ).attr( "href", "javascript:editer.boxEditer( \'" + target + "\', \'" + data.rel + "=" + editer.id + "\', 0 )" );
		}
		
		var func = function()
		{
			$.post( "?List::get", { rel:rel, page:0, order:"", direction:"" }, func2, "json" );
		}
		
		Animation.showSimpleTableStep1( target, func );
	}
	
	this.removeTableRow = function( id, rel, target )
	{
		var func = function()
		{
			$.post( "?Edit::remove", { rel:rel, id:id }, function() { sucessBox( Language.done, Language.deletedSucessfully ); Animation.removeTableRow( "#" + target ); } );
		}
		
		showLoadingScreen();
		confirmBox( Language.attention, Language.sureDeleteItem, func );
	}
	
	this.tableOverHandler = function()
	{
		$( "#" + this.id + " .quick_edit" ).css( "visibility", "visible" );
	}

	this.tableOutHandler = function()
	{
		$( "#" + this.id + " .quick_edit" ).css( "visibility", "hidden" );
	}
	
	this.selectFile = function( target, quick )
	{
		var func = function( data )
		{
			var value = data[0].split( "/" );
			value = value[value.length - 1];
			
			var temp = $( "#editing_" + target );
			if( temp.attr( "type" ) == "image" )
			{
				temp.html( "<img src=\"" + ( quick ? "thumb.php?w=56&h=42&src=" + UPLOAD_FOLDER + data[0] : UPLOAD_FOLDER + data[0]) + "\" alt=\"\" />" );
				temp.parent().parent().find(".bottom .center span label").text( value );
			}
			else temp.html( value );
			
			temp.attr( "href", UPLOAD_FOLDER + data[0] );
		}
		
		var params = parseParams( $( "#editing_" + target ).attr( "params" ) );
		
		params.types = params.types ? params.types : [Language.images, "*.jpg;*.gif;*.png"];
		
		uploader.init( func, true, params );
	}
	
	this.deleteFile = function( target )
	{
		$( "#editing_" + target ).html( Language.undefined );
		$( "#editing_" + target ).attr( "href", "" );
		$( "#editing_" + target ).parent().parent().find(".bottom .center span label").text( Language.undefined );
	}
	
	this.getFieldContent = function( f, i )
	{
		if( f[i].type == "simpletext" )
			return $( "#editing_" + i ).val();
		else if( f[i].type == "simplehtml" )
			return $( "#editing_" + i ).val();
		else if( f[i].type == "password" )
			return $( "#editing_" + i ).val();
		else if( f[i].type == "html" )
			return $( "#editing_" + i ).val();
		else if( f[i].type == "text" )
			return $( "#editing_" + i ).val();
		else if( f[i].type == "image" )
		{
			var t = $( "#editing_" + i + " img" ).attr( "src" );
			if( t )
				return t.substr( UPLOAD_FOLDER.length );
			else
				return undefined;
		}
		else if( f[i].type == "options" )
		{
			if( f[i].params == "value='string'" )
			{
				var n = "";
				$( "#editing_"+ i + " .options_" + i + ":checked" ).each( function( i, v ) { n += v.name + ";" } );
			}
			else
			{
				var n = 0;
				$( "#editing_"+ i + " .options_" + i + ":checked" ).each( function( i, v ) { n += 1 << Number( v.name ) } );
			}
			
			return n;
		}
		else if ( f[i].type == "color" )
			return $( "#editing_" + i ).val();
		else if( f[i].type == "select" )
			return $( "#editing_" + i ).val();
		else if( f[i].type == "switch" )
			return $( "#editing_" + i ).val();
		else if ( f[i].type == "file" )
			return $( "#editing_" + i ).attr( "href" ).substr( UPLOAD_FOLDER.length );
		else if ( f[i].type == "datetime" )
		{
			var t = $( "#editing_" + i );
			return Date.fromLocaleFormat( TimeStamp, editer.formatText( t.find( ".date" ).val(), "**-**-****" ) + " " + t.find( ".hour" ).val() ).toDatetime();
		}
		else if ( f[i].type == "categories" )
		{
			var t = new Array();
			$( "#editing_" + i + " table input:checked" ).parent().find( ".value" ).each( function( i, v ) { t.push( v.innerHTML ) } );
			return t.join( ";" ) + ";";
		}
		else if ( f[i].type == "tags" )
		{
			var values = new Array();
			var selecteds = $( "#editing_" + i + " .selected_tags .value" );
			$( "#editing_" + i + " table .tag" ).each( function( i, v ) { values.push( v.innerHTML ) } );
			selecteds.each( function( i, v ) { values.push( v.innerHTML ) } );
			
			$.post( "?Edit::resetArray", { rel:this.target, target:i, value:Array.removeRepeated( values ).join( ";" ) }, function(data) { }, "text" );
			
			var t = new Array();
			selecteds.each( function( i, v ) { t.push( v.innerHTML ) } );
			
			return t.join( ";" ) + ";";
		}
		else if( f[i].type == "number" )
			return $( "#editing_" + i ).val();
		else return "";
	}
	
	this.formatText = function( target, format )
	{
		for ( var i = 0; i < target.length; i++ )
		{
			if ( format.charAt( i ) != "*" && format.charAt( i ) != target.charAt( i ) )
			{
				if( target.charAt( i ) == "/" )
					target = target.substr( 0, i ) + format.charAt( i ) + target.substr( i + 1 );
				else
					target = target.substr( 0, i ) + format.charAt( i ) + target.substr( i );
			}
		}
		return target;
	}
	
	this.submitHandler = function()
	{
		var f = this.data.fields;
		var request = new Object();
		
		for( var i in f )
		{
			if( f[i].type == "group" )
			{
				if( $( "#editing_" + f[i].rel ).val() == f[i].value )
				{
					var b = f[i].fields;
					for( var a in b )
					{
						var value = this.getFieldContent( b, a );
						
						if( b[a].validate )
							if( !Validator.parseValidator( b[a].validate, value ) )
							{
								alertBox( Language.ops, Language.invalidFieldPt1 + b[a].name + Language.invalidFieldPt2 );
								return;
							}
						
						request[a] = value;
					}
				}
			}
			else
			{
				var value = this.getFieldContent( f, i );
				
				if( f[i].validate )
					if( !Validator.parseValidator( f[i].validate, value ) )
					{
						alertBox( Language.ops, Language.invalidFieldPt1 + f[i].name + Language.invalidFieldPt2 );
						return;
					}
				
				request[i] = value;
			}
		}
		
		deactivateRedButton( editer.button );
		changeRedButtonValue( editer.button, Language.saving );
		
		request.rel = this.target;
		
		if( f["id"].value )
		{
			request.id = f["id"].value;
			$.post( "?Edit::update", request, this.submitLoadedHandler, "json" );
		}
		else
			$.post( "?Edit::save", request , this.submitLoadedHandler, "json" );
	}
	
	this.submitLoadedHandler = function( data )
	{
		// activateRedButton( editer.button );
		// changeRedButtonValue( editer.button, Language.save );
		// {"error":false,"edit":true,"id":10,"rel":"caravanas"}
		
		if( data.status == 'success' )
		{
			if( editer.id == 0 )
				SWFAddress.setValue( "/" + editer.target + "/" + data.id + "/edit" );
			else
				SWFAddress.setValue( "/" + editer.target + "/list" );
		}
		else if( data.status == 'error' )
		{
			SWFAddress.setValue( "/" + editer.target + "/list" );
			alertBox( Language.ops, data.error );
		}
	}
	
	this.loaderHandler = function( data )
	{
		editer.groups = new Object();
		
		var func = function()
		{
			var content = "<span class=\"icon icon_" + data.icon + "\"></span> <h1>" + data.name + "</h1> <h2>- " + ( editer.data.operation == "" ? Language.adding : Language.editing ) + "</h2><form class=\"editor\"><div class=\"sidebar\">";
			
			editer.button = editer.target + "_editer_btn";
			
			// ACTION BOX
			content += "<div class=\"box actionbox\"><div class=\"top\"><div class=\"left\"></div><div class=\"center\"><div class=\"right\"></div><span>" + Language.actions + "</span></div></div><div class=\"bottom\"><div class=\"left\"><div class=\"right\"><div class=\"center\"><div class=\"options\">" + UI.redButton( Language.save, "save", "javascript:editer.submitHandler();", editer.button ) + UI.redButton( Language.cancel, "cancel", "#/" + editer.target + "/list" ) + "</div></div></div></div></div></div><br />";
			
			for( var i in data.fields )
			{
				if( data.fields[i].position == "sidebar" ) content += editer.createSimpleField( i, data.fields[i] );
			}
			
			var boxs = "";
			for( var i in data.guis )
			{
				if( data.guis[i].position == "sidebar" && boxs.indexOf( i ) == -1 )
				{
					boxs += i;
					content += "<div id=\"sidebar_box_" + i + "\" class=\"box\"><div class=\"top\"><div class=\"left\"></div><div class=\"center\"><div class=\"right\"></div><span>" + data.guis[i].name + ( Settings.minimizeBox == "true" ? " <a onclick=\"Animation.boxMinMaxChange(this)\" href=\"javascript:;\" class=\"min_max_button\"></a>" : "" ) + "</span></div></div><div class=\"bottom\"><div class=\"left\"><div class=\"right\"><div class=\"center\"></div></div></div></div></div><br />";
				}
			}
			
			content += "</div><div class=\"edit_content\">";
			
			var first = null;
			
			for( var i in data.fields )
			{
				if( i != "id" )
				{
					if( !first )
					{
						content += editer.createFirstField( i, data.fields[i] );
						first = i;
					}
					else if( data.fields[i].box == "" && !data.fields[i].position )
					{
						content += editer.createSimpleField( i, data.fields[i] );
					}
				}
			}
			
			for( var i in data.gui )
			{
				if( data.gui[i].position != "sidebar" && boxs.indexOf( i ) == -1 )
				{
					boxs += i;
					content += "<div id=\"main_box_" + i + "\" class=\"box\"><div class=\"top\"><div class=\"left\"></div><div class=\"center\"><div class=\"right\"></div>" + ( Settings.minimizeBox == "true" ? "<a onclick=\"Animation.boxMinMaxChange(this)\" href=\"javascript:;\" class=\"min_max_button\"></a>" : "" ) + "<span>" + data.gui[i].name + "</span></div></div><div class=\"bottom\"><div class=\"left\"></div><div class=\"center\"><div class=\"right\"></div></div></div></div>";
				}
			}
			
			content += "<br /><br /></form><br style=\"clear:both\" /><br /><br />";
			$( "#main_content" ).html( content );
			
			for( var i in data.fields )
			{
				if( Settings.autoShowTableContent == "true" )
				{
					if( data.fields[i].type == "table" ) editer.showTable( i, editer.target + "." + i + "=" + editer.id );
					else if( data.fields[i].type == "simpletable" ) editer.showSimpleTable( i, editer.target + "." + i + "=" + editer.id );
				}
				
				if( data.fields[i].box != "" ) $( "#sidebar_box_" + data.fields[i].box + " .bottom .center" ).append( editer.createSimpleField( i, data.fields[i] ) );
			}
			
			Animation.showMainContent();
			UI.startSwitchers();
			UI.startTinymce();
		}
		
		editer.data = data;
		Animation.hideMainContent( func );
		hideLoadingScreen();
	}
	
	this.removeCategory = function( rel, value )
	{
		var values = new Array();
		$( "#editing_" + rel + " label .value" ).each( function( i, v )
		{
			var a = v.innerHTML;
			if( a != value ) values.push( a );
			else Animation.removeCategory( v );
		} );
		
		$.post( "?Edit::resetArray", { rel:this.target, target:rel, value:values.join( ";" ) }, function(data){ }, "text" );
	}
	
	this.addCategories = function( rel )
	{
		var func = function( data )
		{
			var a = $( "#editing_" + rel + " table tr" );
			td1 = a.find( "td:first-child" );
			td2 = a.find( "td:last-child" );
			
			for( var i = total; i < add.length; i++ )
			{
				if( i % 2 ) td2.append( "<label class=\"recent\"><span class=\"remove_category\"><a href=\"javascript:editer.removeCategory( '" + rel + "', '" + add[i] + "' )\"></a></span><input type=\"checkbox\" class=\"category_" + rel + "\"> <span class=\"value\">" + add[i] + "</span></label>" );
				else td1.append( "<label class=\"recent\"><span class=\"remove_category\"><a href=\"javascript:editer.removeCategory( '" + rel + "', '" + add[i] + "' )\"></a></span><input type=\"checkbox\" class=\"category_" + rel + "\"> <span class=\"value\">" + add[i] + "</span></label>" );
			}
			
			a.find( ".recent" ).each( function( i, v ) { Animation.addCategory( v, i ) } );
		}
		
		var values = new Array();
		var total = $( "#editing_" + rel + " label .value" ).each( function( i, v ) { values.push( v.innerHTML ) } ).length;
		var field = $( "#editing_" + rel + " .add_category .field input" );
		var t = field.val().replaceAll( "\"", "" ).replaceAll( "'", "" ).replaceAll( " ", ";" ).replaceAll( ",", ";" ).replaceAll( ";;", ";" );
		field.val( "" );
		
		var add = Array.removeRepeated( Array.removeEmpty( Array.merge( values, t.split( ";" ) ) ) );
		$.post( "?Edit::resetArray", { rel:this.target, target:rel, value:add.join( ";" ) }, func, "text" );
	}
	
	this.addTag = function( rel, value )
	{
		var t = $( "#editing_" + rel + " .selected_tags" );
		var l = t.find( "label .value" );
		var a = new Array();
		
		if( l.length )
		{
			var f = false;
			l.each( function( i, v ) { if( $( v ).html() == value ) f = true } );
			
			if( !f )
			{
				t.append( "<label class=\"recent\"><span class=\"remove_category\"><a href=\"javascript:editer.removeTag( '" + rel + "', '" + value + "' );\"></a></span><span class=\"value\">" + value + "</span>&nbsp;&nbsp;</label>" );
				Animation.addTag( t.find( ".recent" ).css( "display", "none" ) );
			}
			
		}
		else
		{
			t.html( "<label class=\"recent\"><span class=\"remove_category\"><a href=\"javascript:editer.removeTag( '" + rel + "', '" + value + "' );\"></a></span><span class=\"value\">" + value + "</span>&nbsp;&nbsp;</label>" );
			Animation.addTag( t.find( ".recent" ).css( "display", "none" ) );
		}
	}
	
	this.addNewTags = function( rel )
	{
		$( "#editing_" + rel + " .add_tags" );
		
		var values = new Array();
		var total = $( "#editing_" + rel + " label .value" ).each( function( i, v ) { values.push( v.innerHTML ) } ).length;
		var field = $( "#editing_" + rel + " .add_tags .field input" );
		var t = field.val().replaceAll( "\"", "" ).replaceAll( "'", "" ).replaceAll( " ", ";" ).replaceAll( ",", ";" ).replaceAll( ";;", ";" );
		field.val( "" );
		
		var add = Array.removeRepeated( Array.removeEmpty( Array.merge( values, t.split( ";" ) ) ) );
		var t = $( "#editing_" + rel + " .selected_tags" );
		
		if( total == 0 && total != add.length ) t.html( "" );
		
		for( var i = total; i < add.length; i++ )
		{
			t.append( "<label class=\"recent\"><span class=\"remove_category\"><a href=\"javascript:editer.removeTag( '" + rel + "', '" + add[i] + "' );\"></a></span><span class=\"value\">" + add[i] + "</span>&nbsp;&nbsp;</label>" );
			Animation.addTag( t.find( ".recent" ).css( "display", "none" ) );
		}
	}
	
	this.removeTag = function( rel, value )
	{
		var t = $( "#editing_" + rel + " .selected_tags label .value" );
		t.each( function( i, v ) { v = $( v ); if( v.html() == value )
		{
			Animation.removeTag( v.parent(), function()
			{
				if( t.length == 1 ) $( "#editing_" + rel + " .selected_tags" ).html( Language.nonea );
				else v.remove();
			} );
		} } );
		
	}
	
	this.showTags = function( rel )
	{
		$( "#editing_" + rel + " a.show_tags" ).html( Language.hideUsedTags ).attr( "href", "javascript:editer.hideTags( '" + rel + "' )" );
		Animation.showTags( $( "#editing_" + rel + " table" ) );
	}
	
	this.hideTags = function( rel )
	{
		$( "#editing_" + rel + " a.show_tags" ).html( Language.showUsedTags ).attr( "href", "javascript:editer.showTags( '" + rel + "' )" );
		Animation.hideTags( $( "#editing_" + rel + " table" ) );
	}
	
	this.boxEditer = function( target, rel, id )
	{
		boxEditer = new BoxEditer();
		boxEditer.show( rel, id, function() {
			editer.showTable( target, rel );
			boxEditer = null;
		} );
	}
	
	this.show = function( target, id )
	{
		this.id = id;
		this.target = target;
		
		showLoadingScreen();
		
		if( id == 0 )
			$.post( "?Edit::getNew", { rel:target }, this.loaderHandler, "json" );
		else
			$.post( "?Edit::getEdit", { rel:target, id:id }, this.loaderHandler, "json" );
	}
}
