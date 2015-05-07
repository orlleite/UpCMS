// JavaScript Document
function Lister()
{
	this.rel;
	this.data;
	this.page;
	this.order;
	this.target;
	this.columns;
	this.direction;
	
	this.current = "";
	this.address = "";
	
	this.quickEditing;
	
	this.loaderHandler = function( data )
	{
		lister.data = data;
		lister.rel = data.rel;
		lister.current = data.rel.split( "." ).pop();
		
		var func = function()
		{
			var paginate = "<div class=\"paginate\">";
			
			if( Number( data.length ) > Number( data.rowsPerPage ) )
			{
				paginate += Language.pages + ": ";
				
				var total = Math.ceil( data.length / data.rowsPerPage );
				var navoptions = ( lister.order && lister.order != "" ? "/" + lister.order : "" ) + ( lister.direction && lister.direction != "" ? "/" + lister.direction : "" );
				if( lister.page != 0 ) paginate += "<a class=\"back\" href=\"#/" + lister.rel + "/list/" + ( lister.page - 1 ) + navoptions + "\"></a> ";
				
				var pages = lister.page + 2;
				for( var i = lister.page - 2; i <= pages; i++ )
				{
					if( i == lister.page )
						paginate += "<a class=\"page_selected\">" + ( i + 1 ) + "</a>" + ( i + 1 < total ? ", " : "" );
					else if( i >= 0 && i < total )
						paginate += " <a href=\"#/" + lister.rel + "/list/" + i + navoptions + "\">" + ( i + 1 ) + "</a>" + ( i + 1 < total ? ", " : "" );
				}
				
				if( lister.page != total - 1 ) paginate += " <a class=\"next\" href=\"#/" + lister.rel + "/list/" + ( lister.page + 1 ) + navoptions + "\"></a>";
			}
			paginate += "</div>";
			
			var searching = "<div id=\"searcher\"><a class=\"button\" href=\"javascript:lister.searchSubmit('#searchField');\"><span class=\"left\"></span><span class=\"center\">" + Language.ok + "</span><span class=\"right\"></span></a><span class=\"field search\"><input onkeypress=\"submitByEnter(event, lister.searchSubmit, this );\" onblur=\"lister.searchingBlur(this);\" onfocus=\"lister.searchingFocus(this);\" value=\"" + ( lister.search ? lister.search : Language.search ) + "\" name=\"search\" type=\"text\"  id=\"searchField\"/></span></div>";
			
			var operations = "<div class=\"options\">" + UI.redButton( Language.add, "add", "javascript:SWFAddress.setValue( '/" + lister.rel + "/add' );" ) + " " + UI.redButton( Language.remove, "remove", "javascript:lister.removeSelecteds()" ) + "<div class=\"selection\">" + Language.select + ": <a href=\"javascript:lister.selectAll()\">" + Language.all + "</a> - <a href=\"javascript:lister.selectNone()\">" + Language.none + "</a>";
			
			if( data.permission == "any" )
			{
				
				operations += "<div class=\"filter\"></div>";
				$.post( "?List::authors", { rel:lister.rel }, function(data)
				{
					var c = Language.showAddedBy + ": <select onchange=\"lister.changeAuthor(this);\"><option value=\"\"" + ( lister.author == "" ? "selected=\"selected\"" : "" ) + ">" + Language.all + "</option>";
					for( var a in data.authors ) c += "<option  value=\"" + data.authors[a].username + "\"" + ( lister.author == data.authors[a].username ? "selected=\"selected\"" : "" ) + ">" + data.authors[a].displayname + "</option>";
					$( ".filter" ).html( c + "</select>" );
				}, "json" ); 
			}
			
			operations += "</div></div>";
			
			var content = "<span class=\"icon icon_" + data.icon + "\"></span> <h1>" + data.name + "</h1> <h2>- " + Language.listing + "</h2>" + searching + operations + "<table class=\"table_list lister\"><thead><tr>";
			var fields = "";
			
			var n = 0;
			for( var t in data.fields )
			{
				if ( n <= 4 )
				{
					if( t != "id" ) fields += "<td onclick=\"lister.changeOrderBy('" + t + "')\"" + ( lister.order == t ? "class=\"" + ( !lister.direction || lister.direction == "" ? "asc" : lister.direction ) + "_order\"" : "" ) + ">" + data.fields[t].name + " <span></span></td>";
				}
				
				n++;
			}
			
			lister.columns = n;
			
			content += fields + "</tr></thead><tfoot><tr>" + fields + "</tr></tfoot><tbody>";

			function getValue( listItem, type )
			{
				var field = data.fields[type];
				switch( data.fields[type].type )
				{
					case "image":
					case "file":
						var base = '';
						var temp = field.params.split(';');
						for( var a in temp )
						{
							var p = temp[a].split('=');
							if( p[0] == 'base' ) base = p[1] + '/';
						}
						return "<img border=\"1\" src=\"./utils/thumb.php?w=" + 
							Settings.listThumbSize[0] + "&h=" + Settings.listThumbSize[1] + 
							"&src=" + ( listItem[type] != "" ? UPLOAD_FOLDER + base + listItem[type] : THEME_FOLDER + "/imgs/undefined.png" ) + 
							"\" />";

					case "select":
						return data.fields[type].options[listItem[type]];

					case "datetime":
						return dateSqlToStamp( listItem[type] );

					case "options":
					{
						var opts = data.fields[type].options;
						var v = Number( listItem[type] ) || listItem[type];
						
						var value = new Array();
						if( data.fields[type].params == "value='string'" )
							for( var o in opts ) 
							{
								if( v.indexOf( o + ";" ) != -1 ) value.push( opts[o] );
							}
						else
							for( var o in opts )
							{
								if( v >> o & 0x01 ) value.push( opts[o] );
							}
						return value.join( ", " );
					}

					default:
						return listItem[type];
				}
			}
			
			for( var i = 0; i < data.list.length; i++ )
			{
				n = 0;
				var id = "";
				for( var t in data.list[i] )
				{
					if ( n > 4 ) break;
					
					var l = lister.current + "_" + id;
					
					if( n == 0 )
					{
						id = data.list[i][t];
					}
					else if( n == 1 )
					{
						content += "<tr id=\"" + lister.current + "_" + id + "\"><td><input type=\"checkbox\" value=\"" + id + "\" /><i>" + getValue( data.list[i], t ) + "</i><div class=\"quick_edit\"><span class=\"general_icon icon_edit\"></span><a href=\"#/" + data.rel + "/" + id + "/edit\">" + Language.edit + "</a>"+ ( Settings.quickEdit == "true" ? " <span class=\"general_icon icon_quick_edit\"></span><a href=\"javascript:lister.quickEdit( "+ i + ", '" + data.rel + "', '" + id + "');\">" + Language.quickEdit + "</a>" : "" ) + " <span class=\"general_icon icon_delete\"></span><a href=\"javascript:lister.removeRow('" + id + "', '" + data.rel + "_" + id + "');\">" + Language.deletes + "</a></div></td>";
					}
					else
					{
						content += "<td onclick=\"SWFAddress.setValue( '/" + data.rel + "/" + id + "/edit' );\">";
						if( data.fields[t].type == "image" || data.fields[t].type == "file" )
							content += getValue( data.list[i], t );
						else
						{
							content += "<i>" + getValue( data.list[i], t ) +  "</i>";
						}
					}
					
					n++;
					
					content += "</td>";
				}
				content += "</tr>";
			}
			
			content += "</<tbody></table>" + paginate + "<br /><br />";
			$( "#main_content" ).html( content );
			Animation.showMainContent();
			
			var btns = $( ".table_list tbody tr" ).hover( lister.tableOverHandler ).mouseleave( lister.tableOutHandler ).find( ".quick_edit" ).css( { marginTop:0, height:0 } );
		}
		
		Animation.hideMainContent( func );
		hideLoadingScreen();
	}
	
	this.searchingBlur = function( target )
	{
		if( $( target ).val() == "" ) $( target ).val( Language.search );
	}
	
	this.searchingFocus = function( target )
	{
		if( $( target ).val() == Language.search ) $( target ).val( "" );
	}
	
	this.searchSubmit = function( target )
	{
		lister.search = $( target ).val();
		lister.direction = "";
		lister.author = "";
		lister.order = "";
		lister.updateURL();
	}
	
	this.changeOrderBy = function( target )
	{
		if( lister.order == target ) lister.direction = !lister.direction || lister.direction == "asc" ? "desc" : "asc";
		lister.order = target;
		lister.updateURL();
	}
	
	this.changeAuthor = function( target )
	{
		lister.author = $( target ).val();
		lister.direction = "";
		lister.order = "";
		lister.updateURL();
	}
	
	this.updateURL = function()
	{
		var url = "/" + lister.rel + "/list";
		
		if( lister.author && lister.author != "" ) url += "/author/" + lister.author;
		if( lister.search && lister.search != "" ) url += "/search/" + lister.search;
		if( lister.order && lister.order != "" ) url += "/order/" + lister.order;
		if( lister.direction && lister.direction != "" ) url += "/" + lister.direction;
		if( lister.page != 0 ) url += "/" + lister.page;
		
		SWFAddress.setValue( url );
	}
	
	this.selectAll = function()
	{
		for( var i = 0; i < lister.data.list.length; i++ )
		{
			n = 0;
			var id = "";
			$( "#" + lister.current + "_" + lister.data.list[i].id + " input[type=checkbox]" ).attr( "checked", "checked" );
		}
	}
	
	this.selectNone = function()
	{
		for( var i = 0; i < lister.data.list.length; i++ )
		{
			n = 0;
			var id = "";
			$( "#" + lister.current + "_" + lister.data.list[i].id + " input[type=checkbox]" ).removeAttr( "checked" );
		}
	}
	
	this.removeSelecteds = function()
	{
		var list = $( ".table_list input[type=checkbox]:checked" ).get();
		for( var i in list ) list[i] = $( list[i] ).attr( "value" );
		
		var func = function()
		{
			$.post( "?Edit::remove", { rel:lister.rel, id:list.join( "," ) }, function() { 
				sucessBox( Language.done, Language.deletedSucessfully );
				lister.show( lister.rel, lister.page, lister.order, lister.direction );
			} );
		}
		
		if( list.length == 1 )
			confirmBox( Language.attention, Language.sureDeleteSelectedItem, func );
		else
			confirmBox( Language.attention, Language.sureDeleteSelectedItems, func );
	}
	
	this.removeRow = function( id, target )
	{
		if( quicker.saving ) return;
		
		var func = function()
		{
			$.post( "?Edit::remove", { rel:lister.rel, id:id }, function() { alertBox( Language.done, Language.deletedSucessfully ); Animation.removeTableRow( "#" + target ) } );
		}
		
		confirmBox( Language.attention, Language.sureDeleteItem, func );
	}
	
	this.quickEdit = function( index, rel, id )
	{
		if( quicker.saving ) return;
		
		if( lister.quickEditing ) lister.hideQuickEdit();
		
		var t1 = $( "#" + rel + "_" + id ).after( quicker.createEditRow( rel + "_" + id, lister.data.fields, lister.data.list[index], lister.columns - 1, lister.rel ) );
		var t2 = $( "#" + rel + "_" + id + "_quick" );
		t1.height( t1.height() );
		
		lister.quickEditing = { index:index, rel:rel, id:id };
		Animation.showQuickEdit( t1, t2 );
	}
	
	this.hideQuickEdit = function()
	{
		if( quicker.saving ) return;
		
		var t = lister.quickEditing.rel + "_" + lister.quickEditing.id;
		lister.quickEditing = null;
		
		Animation.hideQuickEdit( $( "#" + t ), $( "#" + t + "_quick" ) );
	}
	
	this.show = function( target, params )
	{
		showLoadingScreen();
		
		var request = { };
		request.rel = target;
		params.page = params.page || 0;
		
		for( var i in params )
		{
			this[i] = request[i] = params[i];
		}
		
		this.request = request;
		$.post( "?List::get", request, this.loaderHandler, "json" );
	}
	
	this.tableOverHandler = function()
	{
		Animation.tableOverHandler( this );
	}

	this.tableOutHandler = function()
	{
		Animation.tableOutHandler( this );
	}
}
