function Uploader()
{
	this.id;
	this.target;
	this.params;
	this.callback;
	this.uploading = 0;
	this.uploadType = "flash";
	
	this.result;
	
	this.singlefile = false;
	
	this.list = new Array();
	this.tlist = new Array();
	
	this.nupls = 0;
	this.nurls = 0;
	this.ngals = 0;
	
	this.MAX_LENGTH_NAME = 25;
	
	this.filter = function()
	{
		if( uploader.params.decompress )
			var t = uploader.params.types[1].charAt( uploader.params.types[1].length - 1 ) == ";" ? uploader.params.types[1] + uploader.params.decompress : uploader.params.types[1] + ";" + uploader.params.decompress;
		else
			var t = uploader.params.types[1];
		
		return [uploader.params.types[0], t, uploader.params.types[2]];
	}
	
	this.url = function()
	{
		return document.location.href.split( "#" )[0] + "?FileGallery::upload&rel=" + uploader.target + "&id=" + uploader.id;
	}
	
	this.showUploadFile = function()
	{
		Animation.moveContentUploadBox( 0 );
	}
	
	this.showUploadURL = function()
	{
		Animation.moveContentUploadBox( -562 );
	}
	
	this.showGallery = function()
	{
		Animation.moveContentUploadBox( -1124 );
	}
	
	this.cancelUploadingFile = function( id )
	{
		document.flashup.cancelUploadingFile( id );
	}
	
	this.uploadFileAdd = function( id, name, noProgress )
	{
		deactivateRedButton( "upload_submit" );
		changeRedButtonValue( "upload_submit", Language.waitSending );
		
		uploader.uploading++;
		uploader.tlist[id] = { name:name, rurl:name.length > uploader.MAX_LENGTH_NAME ? name.substr( 0, uploader.MAX_LENGTH_NAME - 3 ) + "..." : name, rname:name.length > uploader.MAX_LENGTH_NAME ? name.substr( 0, uploader.MAX_LENGTH_NAME - 3 ) + "...": name };
		$( "#upload .uplist" ).append( "<div id=\"file_uploading_" + id + "\" class=\"file_item\"><b>" + Language.adding + " " + uploader.tlist[id].rname + " - <span class=\"percentage\">" + ( noProgress ? Language.waitSending : "0.0%" ) + "</span></b> <span class=\"options\"><span class=\"general_icon icon_cancel\"></span><a class=\"upload_canceler_button\" href=\"javascript:uploader.cancelUploadingFile( " + id + " )\" target=\"" + id + "\">" + Language.cancel + "</a></span><span class=\"progress_container\"><div class=\"progress\"><div class=\"loaded\"></div></div></span></div>" );
	}
	
	this.uploadFileProgress = function( id, value )
	{
		value = String( value * 100 );
		var t = value.indexOf( "." );
		
		if( t == -1 ) value = value + ".0"; else value = value.substr( 0, t + 2 );
		
		$( "#file_uploading_" + id + " .progress .loaded" ).css( "width", value + "%" );
		$( "#file_uploading_" + id + " .percentage" ).html( value + "%" );
	}
	
	this.uploadFileComplete = function( id, value )
	{
		value = $.parseJSON( value );
		
		if( value.status == "false" ) alertBox( Language.done, Language.problemUploadFile );
		
		var tid = id;
		var t = value.path.split( "/" );
		var obj = new Object();
		
		obj.url = value.path;
		uploader.uploading--;
		obj.name = t[t.length - 1];
		obj.ext = obj.name.split( "." );
		obj.ext = obj.ext[obj.ext.length - 1].toLowerCase();
		
		var func1 = function()
		{
			var func = function( data )
			{
				var add = "";
				var a = $( "#upload .uplist" );
				
				for( var i in data.files )
				{
					var obj = new Object();
					obj.url = data.files[i].path;
					obj.name = data.files[i].name;
					obj.rurl = obj.url.length > uploader.MAX_LENGTH_NAME ? obj.url.substr( 0, uploader.MAX_LENGTH_NAME - 3 ) + "..." : obj.url;
					obj.rname = obj.name.length > uploader.MAX_LENGTH_NAME ? obj.name.substr( 0, uploader.MAX_LENGTH_NAME - 3 ) + "..." : obj.name;
					
					id = uploader.list.push( obj ) - 1;
					
					a.append( "<div id=\"file_upload_" + id + "\" class=\"file_item\"><div class=\"file_container\"><span class=\"marker\"><input onclick=\"uploader.selectFile( " + id + ", 'ngals' )\" type=\"checkbox\" /></span><img src=\"utils/thumb.php?w=75&h=75&src=" + UPLOAD_FOLDER + obj.url + "\" alt=\"\" /><p><span class=\"title\"><b>" + Language.title + ":</b> " + uploader.list[id].rname + "</span><span><br /></span><span class=\"address\"><b>" + Language.address + ":</b> <a class=\"file_link\" href=\"" + UPLOAD_FOLDER + uploader.list[id].url + "\" target=\"_blank\">" + uploader.list[id].rurl + "</a></span></p><div class=\"options\"><span class=\"general_icon icon_delete\"></span><a href=\"javascript:uploader.deleteFile('" + id + "','" + uploader.list[id].name + "', 'temp')\">" + Language.deletes + "</a><br /><span class=\"general_icon icon_edit\"></span><a href=\"javascript:uploader.renameFile('" + id + "','" + uploader.list[id].name + "', 'temp')\">" + Language.rename + "</a></div></div></div>" );
					
					$( "#file_upload_" + id + " input" ).attr( "checked", true );
					uploader.selectFile( id, "nupls" );
				}
			}
			
			$( "#file_uploading_" + tid ).remove();
			$.post( "?FileGallery::decompress", { rel:uploader.target, id:uploader.id, file:value.name }, func, "json" );
		}
		
		var func2 = function()
		{
			obj.rurl = obj.url.length > uploader.MAX_LENGTH_NAME ? obj.url.substr( 0, uploader.MAX_LENGTH_NAME - 3 ) + "..." : obj.url;
			obj.rname = obj.name.length > uploader.MAX_LENGTH_NAME ? obj.name.substr( 0, uploader.MAX_LENGTH_NAME - 3 ) + "..." : obj.name;
			
			id = uploader.list.push( obj ) - 1;
			
			if( !uploader.params.types[1] || uploader.params.types[1].indexOf( obj.ext ) != -1 )
			{
				$( "#file_uploading_" + tid ).replaceWith( "<div id=\"file_upload_" + id + "\" class=\"file_item\"><div class=\"file_container\"><span class=\"marker\"><input onclick=\"uploader.selectFile( " + id + ", 'ngals' )\" type=\"checkbox\" /></span><img src=\"utils/thumb.php?w=75&h=75&src=" + UPLOAD_FOLDER + value.path + "\" alt=\"\" /><p><span class=\"title\"><b>" + Language.title + ":</b> " + uploader.list[id].rname + "</span><span><br /></span><span class=\"address\"><b>" + Language.address + ":</b> <a class=\"file_link\" href=\"" + UPLOAD_FOLDER + uploader.list[id].url + "\" target=\"_blank\">" + uploader.list[id].rurl + "</a></span></p><div class=\"options\"><span class=\"general_icon icon_delete\"></span><a href=\"javascript:uploader.deleteFile('" + id + "','" + uploader.list[id].name + "', 'temp')\">" + Language.deletes + "</a><br /><span class=\"general_icon icon_edit\"></span><a href=\"javascript:uploader.renameFile('" + id + "','" + uploader.list[id].name + "', 'temp')\">" + Language.rename + "</a></div></div></div>" );
				
				$( "#file_upload_" + id + " input" ).attr( "checked", true );
				uploader.selectFile( id, "nupls" );
			}
			else $( "#file_uploading_" + tid ).remove();
		}
		
		if( uploader.uploading == 0 )
		{
			activateRedButton( "upload_submit" );
			changeRedButtonValue( "upload_submit", Language.addSelected );
		}
		
		if( uploader.params.decompress && uploader.params.decompress.indexOf( obj.ext ) != -1 )
		{
			confirmBox2( Language.decompress, Language.uploadedCompressedFile, func1, func2 );
		}
		else func2();
	}
	
	this.selectFile = function( id, type )
	{
		checkbox = $( "#file_upload_" + id + " input" ).get()[0];
		
		if( uploader.singlefile )
		{
			for( var i = 0; i < uploader.list.length; i++ )
			{
				$( "#file_upload_" + i ).css( "background", "#FBFBFB" );
				$( "#file_upload_" + i + " input" ).attr( "checked", false );
			}
			
			$( "#file_upload_" + id ).css( "background", "#DDD" );
			$( "#file_upload_" + id + " input" ).attr( "checked", true );
		}
		else if( Boolean( $( checkbox ).attr( "checked" ) ) )
		{
			uploader[type]++;
			$( "#file_upload_" + id ).css( "background", "#DDD" );
		}
		else
		{
			uploader[type]--;
			$( "#file_upload_" + id ).css( "background", "#FBFBFB" );
		}
	}
	
	this.deleteFile = function( id, name, type )
	{
		var callback = function( data )
		{
			Animation.removeTableRow( "#file_upload_" + id );
		}
		
		var func = function()
		{
			$.post( "?FileGallery::delete", { rel:uploader.target, id:uploader.id, name:name }, callback, "text" );
		}
		
		confirmBox( Language.attention, Language.sureDeleteFile, func );
	}
	
	this.renameFile = function( id, name, type )
	{
		var callback = function( data )
		{
			var t = data.split( "/" );
			uploader.list[id].url = data;
			uploader.list[id].name = t[t.length - 1];
			uploader.list[id].rurl = uploader.list[id].url.length > uploader.MAX_LENGTH_NAME ? uploader.list[id].url.substr( 0, uploader.MAX_LENGTH_NAME - 3 ) + "..." : uploader.list[id].url;
			uploader.list[id].rname = uploader.list[id].name.length > uploader.MAX_LENGTH_NAME ? uploader.list[id].name.substr( 0, uploader.MAX_LENGTH_NAME - 3 ) + "..." : uploader.list[id].name;
			
			$( "#file_upload_" + id + " span.title" ).html( "<b>" + Language.title + ":</b> " + uploader.list[id].rname );
			$( "#file_upload_" + id + " .file_link" ).html( uploader.list[id].rurl ).attr( "href", UPLOAD_FOLDER + uploader.list[id].url );
			
			var a = $( "#file_upload_" + id + " .options a" ).get();
			$( a[1] ).attr( "href", "javascript:uploader.renameFile('" + id + "','" + uploader.list[id].name + "', 'temp')" );
			$( a[2] ).attr( "href", "javascript:uploader.deleteFile('" + id + "','" + uploader.list[id].name + "', 'temp')" );
		}
		
		var func = function( data )
		{
			if( data.indexOf( "." ) == -1 )
			{
				var t = name.split( "." );
				data += "." + t[t.length - 1];
			}
			
			$.post( "?FileGallery::rename", { rel:uploader.target, id:uploader.id, name:name, newname:data }, callback, "text" );
		}
		
		inputBox( Language.rename, Language.typeNewName, func, null, name );
	}
	
	this.upload = function()
	{
		deactivateRedButton( "upload_submit" );
		changeRedButtonValue( "upload_submit", Language.verifyingFiles );
		
		uploader.result = new Array();
		
		for( var i = 0; i < uploader.list.length; i++ )
		{
			if( Boolean( $( "#file_upload_" + i + " input" ).attr( "checked" ) ) )
			{
				var t = $( "#file_upload_" + i + " a" ).attr( "href" );
				uploader.result.push( t );
			}
		}
		
		$.post( "?FileGallery::verify", { files:"\"" + uploader.result.join( "\",\"" ) + "\"", min:uploader.params.min.join(","), max:uploader.params.max.join(","), size:uploader.params.size.join(","), ratio:uploader.params.ratio.join(","), types:uploader.params.types[1] || "", convert:uploader.params.types[2] || "" }, uploader.verify, "json" );
	}
	
	this.verify = function( data )
	{
		var i = 0;
		
		var func = function( value )
		{
			if( value == false ) return;
			
			if( value != "." ) uploader.result[i - 1] = value.substr( UPLOAD_FOLDER.length );
			
			if( data.files[i] )
			{
				i++;
				
				if( data.files[i - 1] == "false" ) imager.init( uploader.result[i - 1], uploader.params, func );
				else
				{
					uploader.result[i - 1] = uploader.result[i - 1].substr( UPLOAD_FOLDER.length );
					func( "." );
				}
			}
			else
			{
				uploader.callback( uploader.result );
			}
		}
		
		uploader.hide( function() { func( "." ) } );
	}
	
	this.hide = function( callback )
	{
		var func = function()
		{
			$( "#upload_box" ).remove();
			$( "#popbox_container" ).css( "display", "none" );
			if( typeof( callback ) == "function" ) callback();
		}
		
		var list = $( "upload_canceler_button" ).get();
		for( var i in list ) uploader.cancelUploadingFile( $( list[i] ).attr( "target" ) );
		
		hidePopboxScreen();
		Animation.hideUploadBox( func );
	}
	
	this.jsupCompleteHandler = function( id )
	{
		var i = document.getElementById( id );
		
		if ( i.contentDocument )
			var d = i.contentDocument;
		else if( i.contentWindow )
			var d = i.contentWindow.document;
		else
			var d = window.frames[id].document;
		
		if( d.location.href == "about:blank" ) return;
		
		uploader.uploadFileComplete( id, d.body.innerHTML );
	}
	
	this.jsup = function()
	{
		// Thanks to webtoolkit.aim //
		var n = 'f' + Math.floor( Math.random() * 99999 );
		var d = document.createElement( 'div' );
		d.innerHTML = "<iframe style=\"display:none\" src=\"about:blank\" id=\"" + n + "\" name=\"" + n + "\" onload=\"uploader.jsupCompleteHandler('" + n + "')\"></iframe>";
		document.body.appendChild( d );
		document.jsup.setAttribute( 'target', n );
		
		uploader.uploadFileAdd( n, Language.waitSending, true );
		document.jsup.submit();
	}
	
	this.changeUploadButton = function()
	{
		if( uploader.uploadType == "flash" ) uploader.uploadType = "javascript";
		else uploader.uploadType = "flash";
		
		Animation.changeUploadButton( ".popbox .content_box .upload_button", uploader.getUploadButton() );
	}
	
	this.getUploadButton = function()
	{
		if( uploader.uploadType == "flash" ) 
			return "<div id=\"flashup\"><span class=\"general_icon icon_select\"></span><a href=\"javascript:;\">" + Language.select + "</a></div> <a href=\"javascript:uploader.changeUploadButton()\">" + Language.toJSUP + "</a>";
		else if( this.uploadType == "javascript" )
			return "<div id=\"jsup\"><form name=\"jsup\" method=\"post\" action=\"" + this.url() + "\" enctype=\"multipart/form-data\"><input name=\"file\" type=\"file\" onchange=\"uploader.jsup( this )\" /></form></div> <a href=\"javascript:uploader.changeUploadButton()\">" + Language.toFlashUP + "</a>";
	}
	
	this.init = function( callback, singlefile, params )
	{
		this.nupls = 0;
		this.nurls = 0;
		this.ngals = 0;
		
		this.params = params;
		this.callback = callback;
		this.singlefile = Settings.multipleAdding == "true" ? ( singlefile || false ) : true;
		
		this.showUploadFile();
		
		this.target = params.target || editer.target;
		this.id = params.id || ( editer.id == 0 ? "__acd372841289b14dade72301f2b57ba64c8506ed__" : editer.id );
		
		$( "#upload_submit" ).val( Language.add );
		
		var callback = function( data )
		{
			$( "#upload .uplist" ).html( "" );
			$( "#upgallery .uplist" ).html( "" );
			$( "#urlloader .uplist" ).html( "" );
			
			for( var i = 0; i < data.files.length; i++ )
			{
				var obj = new Object();
				obj.url = data.files[i].path;
				obj.name = data.files[i].name;
				obj.rurl = obj.url.length > uploader.MAX_LENGTH_NAME ? obj.url.substr( 0, uploader.MAX_LENGTH_NAME - 3 ) + "..." : obj.url;
				obj.rname = obj.name.length > uploader.MAX_LENGTH_NAME ? obj.name.substr( 0, uploader.MAX_LENGTH_NAME - 3 ) + "..." : obj.name;
				var id = uploader.list.push( obj ) - 1;
				
				$( "#upgallery .uplist" ).append( "<div id=\"file_upload_" + id + "\" class=\"file_item\"><div class=\"file_container\"><span class=\"marker\"><input onclick=\"uploader.selectFile( " + id + ", 'ngals' )\" type=\"checkbox\" /></span><img src=\"utils/thumb.php?w=75&h=75&src=" + UPLOAD_FOLDER + uploader.list[id].url + "\" alt=\"\" /><p><span class=\"title\"><b>" + Language.title + ":</b> " + uploader.list[id].rname + "</span><span><br /></span><span class=\"address\"><b>" + Language.address + ":</b> <a class=\"file_link\" href=\"" + UPLOAD_FOLDER + uploader.list[id].url + "\" target=\"_blank\">" + uploader.list[id].rurl + "</a></span></p><div class=\"options\"><span class=\"general_icon icon_delete\"></span><a href=\"javascript:uploader.deleteFile('" + id + "','" + uploader.list[id].name + "', 'temp')\">" + Language.deletes + "</a><br /><span class=\"general_icon icon_edit\"></span><a href=\"javascript:uploader.renameFile('" + id + "','" + uploader.list[id].name + "', 'temp')\">" + Language.rename + "</a></div></div></div>" );
			}
		}
		
		$.post( "?FileGallery::alist", { rel:uploader.target, id:uploader.id, types:params.types[1] || "", convert:params.types[2] || "" }, callback, "json" );
		
		$( "#popbox_screen" ).css( "display", "block" );
		
		$( "#popbox_container" ).css( "display", "block" ).append( "<div class=\"popbox\" id=\"upload_box\"><div class=\"top\"><div class=\"left\"></div><div class=\"center\"><div class=\"right\"></div><span>" + Language.addAFile + "<a class=\"close_btn\" href=\"javascript:uploader.hide();\"> </a></span></div></div><div class=\"container\"><div class=\"left\"></div><div class=\"center\"><div class=\"right\"></div><div class=\"content_wrap\"><div class=\"content\"><div class=\"content_box\" id=\"upload\"><div class=\"inside\"><h1>" + Language.addFilesComputer + "</h1><div class=\"upload_button\">" + uploader.getUploadButton() + "</div><div class=\"uplist\"></div></div></div><div class=\"content_box\" id=\"urlloader\"><div class=\"inside\"><h1>" + Language.addFilesURL + "</h1><div class=\"url_buttons\">" + Language.address + ": <span class=\"urlfield field\"><input name=\"url\" type=\"text\" /></span> <a href=\"#\">" + Language.useURL + "</a><a class=\"button\" href=\"javascript:;\"><span class=\"general_icon icon_library\"></span><span class=\"text\">" + Language.copyToLibrary + "</span></a></div><div class=\"uplist\"></div></div></div><div class=\"content_box\" id=\"upgallery\"><div class=\"inside\"><h1>" + Language.library + "</h1><div class=\"uplist uplist_gallery\"></div></div></div></div><div align=\"right\">" + UI.redButton( Language.cancel, "cancel", "javascript:uploader.hide();" ) + UI.redButton( Language.addSelected, "add", "javascript:uploader.upload();", "upload_submit" ) + "</div></div></div></div></div>" );
		
		createSlider( [{ name:Language.fromComputer, icon:"computer", href:"javascript:uploader.showUploadFile();" }, { name:Language.fromURL, icon:"url", href:"javascript:uploader.showUploadURL();" }, { name:Language.fromLibrary, icon:"library", href:"javascript:uploader.showGallery();" }], $( "#upload_box .top .center span:first" ) );
		
		showPopboxScreen();
		Animation.showUploadBox();
		
		if( this.uploadType == "flash" ) swfobject.embedSWF( THEME_FOLDER + "swfs/flashup.swf", "flashup", "160", "25", "9.0.0", "expressInstall.swf", { text:Language.select, width:160 }, { menu:"false", wmode:"transparent",	bgcolor:"#FFFFFF" }, { id:"flashup" } );
	}
	
}