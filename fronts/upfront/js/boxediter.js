function BoxEditer()
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
		if( obj.type == "simpletext" )
			return obj.strict ? UI.firstSimpleText( rel, obj.value, true ) : UI.firstSimpleText( rel, obj.value );
		else
			return this.createSimpleField( rel, obj );
	}

	this.createSimpleField = function( rel, obj )
	{
		if( obj.type == "simpletext" )
			return obj.strict ? UI.simpleText( rel, obj.name, obj.value, true ) : UI.simpleText( rel, obj.name, obj.value );
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
			return UI.select( rel, obj.name, obj.value, obj.options );
		else if ( obj.type == "file" )
			return UI.file( rel, obj.name, obj.value, obj.params );
		else if ( obj.type == "datetime" )
			return obj.strict ? UI.datetime( rel, obj.name, obj.value, obj.params, true ) : UI.datetime( rel, obj.name, obj.value, obj.params );
		else if( obj.type == "switch" )
			return UI.switcher( rel, obj.name, obj.value, obj.options, "boxEditer.switchChange(this)" );
		else if( obj.type == "simpletable" )
			return UI.simpleTable( rel, obj.name, obj.params, boxEditer.target + "." + rel + "=" + boxEditer.id, boxEditer.id == 0 );
		else if( obj.type == "table" )
			return UI.table( rel, obj.name, obj.params, boxEditer.target + "." + rel + "=" + boxEditer.id, boxEditer.id == 0 );
		else if ( obj.type == "color" )
			return obj.strict ? UI.color( rel, obj.name, obj.value, obj.params, true ) : UI.color( rel, obj.name, obj.value, obj.params );
		else if( obj.type == "group" )
		{
			boxEditer.groups[rel] = { relation:obj.rel, value:obj.value };
			return UI.group( rel, obj.rel, obj.value, obj.params, boxEditer.getGroupContent( obj.fields ) );
		}
		else return "";
	}

	this.getGroupContent = function( fields )
	{
		var content = "";
		for( var i in fields ) content += boxEditer.createSimpleField( i, fields[i] );
		return content;
	}

	this.switchChange = function( t )
	{
		var a = $( t );

		var n = a.attr( "name" );
		var g = boxEditer.groups;
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
		if( !boxEditer.tableData ) boxEditer.tableData = [];

		var func2 = function( data )
		{
			var content = "";
			var field = data.first[1];
			boxEditer.tableData[target] = data;

			if( data.first[0] == "image" || data.first[0] == "file" )
			{
				for( var i = 1; i < data.list.length; i++ )
				{
					var t = target + "-" + data.list[i].id;
					content += "<div id=\"" + t + "\" onMouseOver=\"quicker.createEditBallon( this, \'" + target + "\', \'" + data.rel + "=" + boxEditer.id + "\', " +  i + " )\" onMouseOut=\"quicker.hideEditBallon()\" class=\"editing_image\"><img src=\"" + "./utils/thumb.php?w=150&h=100&src=" + data.list[i][field] + "\" /></div>";
				}
			}

			var title = $( "#editing_" + target + " .title" );

			var temp = $( "#editing_content_" + target ).css( "overflow-y", "scroll" ).css( "position", "relative" ).css( "height", "300px" ).html( content ).css( "opacity", "hide" );
			Animation.showSimpleTableStep2( temp );

			var btns = temp.find( ".editing_image" );
			btns.mouseover( lister.tableOverHandler );
			btns.mouseout( lister.tableOutHandler );

			temp.find( "a" ).attr( "href", "javascript:quicker.createAddBallon( \'" + target + "\', \'" + data.rel + "=" + boxEditer.id + "\' )" );
		}

		var func = function()
		{
			$.post( "?List::get", { rel:rel, page:0, order:"", direction:"" }, func2, "json" );
		}

		Animation.showSimpleTable( target, func );
	}

	this.showTable = function( target, rel )
	{
		if( !boxEditer.tableData ) boxEditer.tableData = [];
		var func2 = function( data )
		{
			var content = "";
			var field = data.first[1];
			boxEditer.tableData[target] = data;

			if( data.first[0] == "image" || data.first[0] == "file" )
			{
				for( var i = 1; i < data.list.length; i++ )
				{
					var t = target + "-" + data.list[i].id;
					content += "<div id=\"" + t + "\" onMouseOver=\"quicker.createEditBallon( this, \'" + target + "\', \'" + data.rel + "=" + boxEditer.id + "\', " +  i + ", true )\" onMouseOut=\"quicker.hideEditBallon()\" class=\"editing_image\"><img src=\"" + "./utils/thumb.php?w=150&h=100&src=" + data.list[i][field] + "\" /></div>";
				}
			}

			var title = $( "#editing_" + target + " .title" );

			var temp = $( "#editing_content_" + target ).css( "overflow-y", "scroll" ).css( "position", "relative" ).css( "height", "300px" ).html( content )
			Animation.showSimpleTableStep2( temp );

			var btns = temp.find( ".editing_image" );
			btns.mouseover( lister.tableOverHandler );
			btns.mouseout( lister.tableOutHandler );

			temp.find( "a" ).attr( "href", "javascript:boxEditer.boxEditer( \'" + data.rel + "=" + boxEditer.id + "\', 0 )" );
		}

		var func = function()
		{
			$.post( "?List::get", { rel:rel, page:0, order:"", direction:"" }, func2, "json" );
		}

		Animation.showSimpleTableStep1( "#editing_" + target, func );
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
			return $( "#editing_" + i + " img" ).attr( "src" ).substr( UPLOAD_FOLDER.length );
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
		else return "";
	}

	this.submitHandler = function( target )
	{
		this.button = target;
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

		$( target ).attr( "disabled", "true" );

		request.rel = this.target;

		if( f["id"].value )
		{
			request.id = f["id"].value;
			$.post( "?Edit::update", request, this.submitLoadedHandler, "json" );
		}
		else
			$.post( "?Edit::save", request, this.submitLoadedHandler, "json" );
	}

	this.submitLoadedHandler = function( data )
	{

		// activateRedButton( editer.button );
		// changeRedButtonValue( editer.button, Language.save );
		// {"error":false,"edit":true,"id":10,"rel":"caravanas"}

		if( data.status == 'success' )
		{
			boxEditer.hide();
		}
		else if( data.status == 'error' )
		{
			alertBox( Language.ops, data.error );
		}

	}

	this.loaderHandler = function( data )
	{
		boxEditer.groups = new Object();

		var func = function()
		{
			var content = "<div id=\"editer_box\" class=\"popbox\" style=\"display: block;\"><div class=\"top\"><div class=\"left\"></div><div class=\"center\"><div class=\"right\"></div><span>" + data.name + " - " + ( boxEditer.data.operation == "" ? Language.add : Language.edit ) + "<a href=\"javascript:boxEditer.hide();\" class=\"close_btn\"> </a></span></div></div><div class=\"container\"><div class=\"left\"></div><div class=\"center\"><div class=\"right\"></div><div class=\"content_wrap\"><div class=\"content\">";

			// ACTION BOX
			var btns = "<div align=\"right\">" + UI.redButton( Language.save, "save", "javascript:boxEditer.submitHandler(this);" ) + UI.redButton( Language.cancel, "cancel", "javascript:boxEditer.hide();" ) + "</div>";

			for( var i in data.fields )
			{
				if( i != "id" )
				{
					if( data.fields[i].box == "" )
					{
						content += boxEditer.createSimpleField( i, data.fields[i] );
					}
				}
			}

			for( var i in data.gui )
			{
				if( boxs.indexOf( i ) == -1 )
				{
					boxs += i;
					content += "<div id=\"main_box_" + i + "\" class=\"box\"><div class=\"top\"><div class=\"left\"></div><div class=\"center\"><div class=\"right\"></div><span>" + data.gui[i].name + "</span></div></div><div class=\"bottom\"><div class=\"left\"></div><div class=\"center\"><div class=\"right\"></div></div></div></div>";
				}
			}

			content += "<br /><br /></form><br style=\"clear:both\" /></div></div>" + btns + "</div></div></div>";
			$( "#editer_container" ).css( "display", "block" ).append( content );

			Animation.showEditerBox();

			UI.startSwitchers();
			UI.startTinymce();
		}

		boxEditer.data = data;
		hideLoadingScreen();
		func();
	}

	this.hide = function()
	{
		var func = function()
		{
			$( "#editer_box" ).remove();
			$( "#editer_container" ).css( "display", "none" );
			if( typeof( boxEditer.callback ) == "function" ) boxEditer.callback();
		}

		Animation.hideEditerBox( func );
	}

	this.show = function( target, id, callback )
	{
		this.id = id;
		this.target = target;
		this.callback = callback;

		showLoadingScreen();

		if( id == 0 )
			$.post( "?Edit::getNew", { rel:target }, this.loaderHandler, "json" );
		else
			$.post( "?Edit::getEdit", { rel:target, id:id }, this.loaderHandler, "json" );
	}
}
