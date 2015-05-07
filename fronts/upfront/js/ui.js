UI =
{
	startTinymce:function()
	{
		$( document ).ready( function()
		{
			// HTML VERSION //
			$( "textarea.tinymce_html" ).tinymce({
				// General options
				mode : "textareas",
				theme : "default",
				plugins : "safari,pagebreak,style,paste,layer,table,save,advhr,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,searchreplace,print,contextmenu,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",

				// Theme options
				theme_advanced_buttons1 : "[bold,italic,underline,strikethrough],|,[justifyleft,justifycenter,justifyright,justifyfull],|,formatselect,|,fontsizeselect",
				theme_advanced_buttons2 : "[forecolor,backcolor],|,[sub,sup],|,[link,unlink,image,charmap,anchor,blockquote,cite,code],|,[cut,copy,paste,pastetext],|,[undo,redo]",
				theme_advanced_buttons3 : "[bullist,numlist]",
				theme_advanced_toolbar_location : "top",
				theme_advanced_toolbar_align : "left",
				theme_advanced_statusbar_location : "none",
				theme_advanced_resizing : false,

				// Change skin
				skin : "up",
				relative_urls: false,
				paste_text_sticky: true,
				paste_text_sticky_default: true,
				paste_as_text: true,
				

				entity_encoding : "raw",

				// Drop lists for link/image/media/template dialogs
				template_external_list_url : "js/template_list.js",
				external_link_list_url : "js/link_list.js",
				external_image_list_url : "js/image_list.js",
				media_external_list_url : "js/media_list.js",

				width:"100%",
				height:"400px",

				setup:function(ed) {

					ed.onInit.add(function(ed) {
				      ed.pasteAsPlainText = true;
				    });
					// Add a custom button
					ed.addButton('image', {
						title : Language.addImage,
						//image : 'img/example.gif',
						onclick : function() {
							// Add you own code to execute something on click

							var func = function(data)
							{
								ed.focus();
								for( var i = 0; i < data.length; i++ )
									ed.selection.setContent("<img src=\"" + UPLOAD_FOLDER + data[i] + "\" />");
							}

							uploader.init( func, false, parseParams( $( "#" + ed.id ).attr( "params" ) ) );
						}
        			});

					ed.addButton('media', {
						title : Language.embedMedia,
						onclick : function() {
							var func = function(data) { ed.selection.setContent( data ) }
							inserter.init( Language.embedMedia, "", func );
						}
					});

					ed.addButton('code', {
						title : Language.editHTML,
						onclick : function() {
							var func = function(data) { ed.setContent( data ) }
							inserter.init( Language.editHTML, ed.getContent( {format : 'raw'} ), func );
						}
					});

					ed.addButton('pasteword', {
						title : Language.pasteWord,
						onclick : function() {
							var func = function(data) { ed.execCommand( 'mceInsertClipboardContent', false, { content:data, wordContent:true } ); }
							inserter.init( Language.pasteWord, "", func );
						}
					});
				}
			});


			// SIMPLEHTML VERSION //
			$( "textarea.tinymce_simplehtml" ).tinymce({
				// General options
				mode : "textareas",
				theme : "default",
				plugins : "safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",

				// Theme options
				theme_advanced_buttons1 : "[bold,italic,underline,strikethrough],|,[justifyleft,justifycenter,justifyright,justifyfull],|,formatselect,|,fontselect,|,fontsizeselect",
				theme_advanced_buttons2 : "[forecolor,backcolor],|,[sub,sup],|,[link,unlink,charmap,anchor,blockquote,cite,code],|,[cut,copy,paste,pastetext,pasteword],|,[undo,redo]",
				theme_advanced_buttons3 : "[bullist,numlist,pagebreak,outdent,indent,hr,removeformat]",
				theme_advanced_toolbar_location : "top",
				theme_advanced_toolbar_align : "left",
				theme_advanced_statusbar_location : "none",
				theme_advanced_resizing : false,

				// Change skin
				skin : "up",
				relative_urls: false,

				entity_encoding : "raw",

				// Example content CSS (should be your site CSS)
				content_css : "css/example.css",

				// Drop lists for link/image/media/template dialogs
				template_external_list_url : "js/template_list.js",
				external_link_list_url : "js/link_list.js",
				external_image_list_url : "js/image_list.js",
				media_external_list_url : "js/media_list.js",

				width:"100%",
				height:"300px",

				setup:function(ed) {
					ed.addButton('code', {
						title : Language.editHTML,
						onclick : function() {
							var func = function(data) { ed.setContent( data ) }
							inserter.init( Language.editHTML, ed.getContent( {format : 'raw'} ), func );
						}
					});

					ed.addButton('pasteword', {
						title : Language.pasteWord,
						onclick : function() {
							var func = function(data) { ed.execCommand( 'mceInsertClipboardContent', false, { content:data, wordContent:true } ); }
							inserter.init( Language.pasteWord, "", func );
						}
					});
				}
			});
		});
	},

	firstSimpleText:function( rel, value, readonly )
	{
		return "<span class=\"field text_first\"><input " + ( readonly ? "readonly=\"true\" " : "" ) + "id=\"editing_" + rel + "\" name=\"" + rel + "\" type=\"text\" value=\"" + value + "\" /></span><br />";
	},

	simpleText:function( rel, name, value, readonly )
	{
		return "<label class=\"edit_field\">" + name + "<br /><span class=\"field\"><input " + ( readonly ? "readonly=\"true\" " : "" ) + "id=\"editing_" + rel + "\" name=\"" + rel + "\" type=\"text\" value=\"" + value + "\" /></span></label><br />";
	},

	number:function( rel, name, value, readonly )
	{
		return "<label class=\"edit_field\">" + name + "<br /><span class=\"field\"><input " + ( readonly ? "readonly=\"true\" " : "" ) + "id=\"editing_" + rel + "\" name=\"" + rel + "\" type=\"text\" value=\"" + value + "\" /></span></label><br />";
	},

	password:function( rel, name, value, readonly )
	{
		return "<label class=\"edit_field\">" + name + "<br /><span class=\"field\"><input " + ( readonly ? "readonly=\"true\" " : "" ) + "id=\"editing_" + rel + "\" name=\"" + rel + "\" type=\"password\" value=\"" + value + "\" /></span></label><br />";
	},

	text:function( rel, name, value )
	{
		return "<div class=\"box\"><div class=\"top\"><div class=\"left\"></div><div class=\"center\"><div class=\"right\"></div><span>" + name + "</span></div></div><div class=\"content\"><textarea id=\"editing_" + rel + "\" name=\"" + rel + "\">" + value + "</textarea></div><div class=\"bottom\"><div class=\"left\"><div class=\"right\"><div class=\"center\"></div></div></div></div></div><br />";
	},

	simpleHtml:function( rel, name, value, params )
	{
		return "<textarea id=\"editing_" + rel + "\" name=\"" + rel + "\" class=\"tinymce_simplehtml\" params=\"" + params + "\">" + value + "</textarea><br />";
	},

	html:function( rel, name, value, params )
	{
		return "<textarea id=\"editing_" + rel + "\" name=\"" + rel + "\" class=\"tinymce_html\" params=\"" + params + "\">" + value + "</textarea><br />";
	},

	image:function( rel, name, value, params )
	{
		var empty = false;
		if( value == "" ) empty = true;
		
		var base = '';
		
		if( params )
		{
			var temp = params.split(';');
			for( var a in temp )
			{
				var p = temp[a].split('=');
				if( p[0] == 'base' ) base = p[1] + '/';
			}
		}

		var v = value.split( "/" );
		v = v[v.length - 1];
		value = UPLOAD_FOLDER + base + value;
		return "\
			<div class=\"box imagebox\">\
				<div class=\"top\">\
					<div class=\"left\"></div>\
					<div class=\"center\"><div class=\"right\"></div><span>" + name + "</span></div>\
				</div>\
				<div class=\"content\">\
					<a id=\"editing_" + rel + "\" params=\"" + params + "\" type=\"image\" href=\"" + value + "\" target=\"_blank\">" +
						( empty ? Language.undefined : "<img src=\"" + value + "\" alt=\"\" />" ) +
					"</a>\
				</div>\
				<div class=\"bottom\">\
					<div class=\"left\">\
					<div class=\"right\"><div class=\"center\"><span><label>" + v + "</label><div class=\"options\"><span class=\"general_icon icon_delete\"></span>\
						<a href=\"javascript:editer.deleteFile('" + rel + "')\">" + Language.deletes + "</a> <span class=\"general_icon icon_select\"></span><a href=\"javascript:editer.selectFile('" + rel + "')\">" + Language.selectImage + "</a></div></div></div></span>\
					</div>\
				</div>\
			</div><br />";
	},

	options:function( rel, name, value, opts, params )
	{
		var c = "<div id=\"editing_" + rel + "\" class=\"edit_label\"><b>" + name + "</b><br />";

		var i = 0;
		var v = Number( value ) || 0;

		if( params == "value='string'" )
			for( var o in opts )
			{
				c += "<label><input class=\"options_" + rel + "\" type=\"checkbox\" name=\"" + o + "\"" + ( value.indexOf( o + ";" ) != -1 ? "checked=\"checked\"" : "" ) + " value=\"" + opts[o] + "\" /> " + opts[o] + "</label>&nbsp;&nbsp;";
				i++;
			}
		else
			for( var o in opts )
			{
				c += "<label><input class=\"options_" + rel + "\" type=\"checkbox\" name=\"" + o + "\"" + ( v >> o & 0x01 ? "checked=\"checked\"" : "" ) + " value=\"" + opts[o] + "\" /> " + opts[o] + "</label>&nbsp;&nbsp;";
				i++;
			}


		return c + "</div><br />";
	},

	select:function( rel, name, value, opts, params, readonly )
	{
		var c = "<label class=\"edit_field\">" + name + "<br /><select id=\"editing_" + rel + "\" name=\"" + rel + "\" class=\"edit_select\" " + ( readonly ? "disabled" : "" ) + ">";
		for( var o in opts )
		{
			c += "<option value=\"" + o + "\"" + ( value == o ? "selected=\"selected\"" : "" ) + ">" + opts[o] + "</option>";
		}
		return c + "</select></label><br />";
	},

	file:function( rel, name, value, params )
	{
		var empty = false;
		if( value == "" ) empty = true;

		var v = value.split( "/" );
		v = v[v.length - 1];
		value = UPLOAD_FOLDER + value;
		return "<div class=\"box imagebox\"><div class=\"top\"><div class=\"left\"></div><div class=\"center\"><div class=\"right\"></div><span>" + name + "</span></div></div><div class=\"content\"><a id=\"editing_" + rel + "\" params=\"" + params + "\" type=\"text\" href=\"" + value + "\" target=\"_blank\">" +
				( empty ? Language.undefined : "<img src=\"./utils/thumb.php?w=150&h=150&src=" + value + "\" alt=\"\" />" ) +
				"</a></div><div class=\"bottom\"><div class=\"left\"><div class=\"right\"><div class=\"center\"><span><label>" + v + "</label><div class=\"options\"><span class=\"general_icon icon_delete\"></span><a href=\"javascript:editer.deleteFile('" + rel + "')\">" + Language.deletes + "</a> <span class=\"general_icon icon_select\"></span><a href=\"javascript:editer.selectFile('" + rel + "')\">" + Language.selectFile + "</a></div></div></div></span></div></div></div><br />";
	},

	datetime:function( rel, name, value, params, readonly )
	{
		var d = dateSqlToStamp( value );
		d = d.split( " " );
		return "<div class=\"edit_field\" params=\"" + params + "\" id=\"editing_" + rel + "\" name=\"" + rel + "\" value=\"" + value + "\">" + name + "<br /><div class=\"datefield\">" + Language.date.toLowerCase() + " <span class=\"field\"><input class=\"date\" " + ( readonly ? "readonly=\"true\" " : "" ) + " value=\"" + d[0] + "\" type=\"text\" " + ( readonly ? "" : "onblur=\"callendar.show(this)\" onclick=\"callendar.show(this)\" " ) + "/></span></div> <div class=\"datefield\"> " + Language.time.toLowerCase() + " <span class=\"field\"><input class=\"hour\" " + ( readonly ? "readonly=\"true\" " : "" ) + " value=\"" + ( d[1] ? d[1] : "" ) + "\" type=\"text\" /></span></div></div><br />";
	},

	simpleTable:function( rel, name, params, link, first )
	{
		return "<div id=\"editing_" + rel + "\" params=\"" + params + "\" class=\"box imagebox\">\
					<div class=\"top\"><div class=\"left\"></div><div class=\"center\"><div class=\"right\"></div><span>" + name + "</span></div></div>\
					<div class=\"content\" id=\"editing_content_" + rel + "\">" + ( first ? Language.needSaveFirst : "<a href=\"javascript:editer.showSimpleTable('" + rel + "','" + link + "')\">" + Language.showContent + "</a>" ) + "</div><div class=\"bottom\"><div class=\"left\"><div class=\"right\"><div class=\"center\"><span><div class=\"options\" style=\"display:none\"><div id=\"add_btn_"+ rel + "\"><span class=\"general_icon icon_add\"></span><a href=\"#\">" + Language.add + "</a></div></div></span></div></div></div></div>\
				</div><br />";
	},

	table:function( rel, name, params, link, first )
	{
		return "<div id=\"editing_" + rel + "\" params=\"" + params + "\" class=\"box imagebox\"><div class=\"top\"><div class=\"left\"></div><div class=\"center\"><div class=\"right\"></div><span>" + name + "</span></div></div><div class=\"content\" id=\"editing_content_" + rel + "\">" + ( first ? Language.needSaveFirst : "<a href=\"javascript:editer.showTable('" + rel + "','" + link + "')\">" + Language.showContent + "</a>" ) + "</div><div class=\"bottom\"><div class=\"left\"><div class=\"right\"><div class=\"center\"><span><div class=\"options\" style=\"display:none\"><div id=\"add_btn_"+ rel + "\"><span class=\"general_icon icon_add\"></span><a href=\"#\">" + Language.add + "</a></div></div></span></div></div></div></div></div><br />";
	},

	color:function( rel, name, value, params, readonly )
	{
		return "<div class=\"edit_field\">" + name + "<br /><span class=\"field\"><input params=\"" + params + "\" id=\"editing_" + rel + "\" name=\"" + rel + "\" class=\"color\" " + ( readonly ? "readonly=\"true\" " : "" ) + " value=\"" + value + "\" type=\"text\" " + ( readonly ? "" : "onblur=\"colorPicker.show(this)\" onclick=\"colorPicker.show(this)\" " ) + "/></span></div><br />";
	},

	startSwitchers:function()
	{
		$( ".edit_switch" ).each( function( i, o ) { o.onchange() } );
	},

	switcher:function( rel, name, value, opts, change )
	{
		var c = "<label class=\"edit_field\">" + name + "<br /><select onchange=\""+ change + "\" id=\"editing_" + rel + "\" name=\"" + rel + "\" class=\"edit_switch\">";
		for( var o in opts )
		{
			c += "<option value=\"" + o + "\"" + ( value == o ? "selected=\"selected\"" : "" ) + ">" + opts[o] + "</option>";
		}
		return c + "</select></label><br />";
	},

	group:function( rel, relation, value, params, content, display )
	{
		display = display || false;
		return "<div style=\"display:" + ( display ? "block" : "none" ) + "\" class=\"group\" id=\"editing_group_" + rel + "\" rel=\"" + relation + "\" value=\"" + value + "\" params=\"" + params + "\">" + content + "</div>";
	},

	redButton:function( name, icon, href, id )
	{
		return "<a " + ( id ? "id=\"" + id + "\" " : "" ) + "href=\"" + href + "\" class=\"red_button\"><span class=\""+ icon + "\"></span> <i>" + name + "</i></a>";
	},

	redButtonField:function( id, name, icon, href, src, params )
	{
		return "<a " + ( id ? "id=\"" + id + "\" " : "" ) + ( params ? "params=\"" + params + "\" " : "" ) + ( src ? "src=\"" + src + "\" " : "" ) + "href=\"" + href + "\" class=\"red_button\"><span class=\""+ icon + "\"></span> <i>" + name + "</i></a>";
	},

	categories:function( rel, name, value, opts )
	{
		var c = "<div class=\"box array_box categories_box\" id=\"editing_"+ rel + "\"><div class=\"top\"><div class=\"left\"></div><div class=\"center\"><div class=\"right\"></div><span>" + name + ( Settings.minimizeBox == "true" ? " <a onclick=\"Animation.boxMinMaxChange(this)\" href=\"javascript:;\" class=\"min_max_button\"></a>" : "" ) + createSlider( [{ icon:"select", href:"javascript:Animation.selectCategories('editing_" + rel + "');" }, { icon:"manage", href:"javascript:Animation.manageCategories('editing_" + rel + "');" }] ) + "</span></div></div><div class=\"bottom\"><div class=\"left\"><div class=\"right\"><div class=\"center\"><table><tr><td>";

		var td1 = "";
		var td2 = "";

		if( opts != "" )
		{
			opts = opts.split( ";" );
			for( var o in opts )
			{
				if( o % 2 ) td2 += "<label><span class=\"remove_category\"><a href=\"javascript:editer.removeCategory( '" + rel + "', '" + opts[o] + "' )\"></a></span><input type=\"checkbox\" class=\"category_" + rel + "\" " + ( value.indexOf( opts[o] + ";" ) != -1 ? "checked=\"checked\"" : "" ) + "> <span class=\"value\">" + opts[o] + "</span></label>";
				else td1 += "<label><span class=\"remove_category\"><a href=\"javascript:editer.removeCategory( '" + rel + "', '" + opts[o] + "' )\"></a></span><input type=\"checkbox\" class=\"category_" + rel + "\" " + ( value.indexOf( opts[o] + ";" ) != -1 ? "checked=\"checked\"" : "" ) + "> <span class=\"value\">" + opts[o] + "</span></label>";
			}
		}

		return c + td1 + "</td><td>" + td2 + "</td></tr></table><div class=\"add_array add_category\"><b>" + Language.addCategory + ":</b><br /><i><span class=\"field\"><input onkeypress=\"submitByEnter(event, editer.addCategories, '" + rel + "' )\" type=\"text\" value=\"\"></span></i> <a href=\"javascript:editer.addCategories( '" + rel + "' )\" class=\"button add_button\"><span class=\"left\"></span><span class=\"center\"><span></span></span><span class=\"right\"></span></a></div></div></div></div></div></div><br />";
	},

	tags:function( rel, name, value, opts )
	{
		var c = "<div class=\"box array_box tags_box\" id=\"editing_"+ rel + "\"><div class=\"top\"><div class=\"left\"></div><div class=\"center\"><div class=\"right\"></div><span>" + name + ( Settings.minimizeBox == "true" ? " <a onclick=\"Animation.boxMinMaxChange(this)\" href=\"javascript:;\" class=\"min_max_button\"></a>" : "" ) + "</span></div></div><div class=\"bottom\"><div class=\"left\"><div class=\"right\"><div class=\"center\"><div class=\"add_array add_tags\"><b>" + Language.addTags + ":</b><br /><i><span class=\"field\"><input type=\"text\" value=\"\" onkeypress=\"submitByEnter(event, editer.addNewTags, '" + rel + "' )\" /></span></i> <a href=\"javascript:editer.addNewTags( '"+  rel + "' )\" class=\"button add_button\"><span class=\"left\"></span><span class=\"center\"><span></span></span><span class=\"right\"></span></a><br /><i class=\"info\">" + Language.separateTagsCommas + "</i></div><br /><div><b>" + Language.selectedTags + ":</b><br /><div class=\"selected_tags\">";

		value = value.split( ";" );
		value.pop();

		if( value[0] == undefined || value[0] == "" ) c += Language.nonea;
		else
		{
			for( var v in value ) c += "<label><span class=\"remove_category\"><a href=\"javascript:editer.removeTag( '" + rel + "', '" + value[v] + "' )\"></a></span><span class=\"value\">" + value[v] + "</span>&nbsp;&nbsp;</label>";
		}

		c += "</div></div><br /><a class=\"show_tags\" href=\"javascript:editer.showTags( '" + rel + "' )\">" + Language.showUsedTags + "</a><br /><table><tr><td>";
		opts = opts.split( ";" );

		for( var o in opts ) { c += "<a href=\"javascript:editer.addTag('" + rel + "', '" + opts[o] + "')\" class=\"tag\">" + opts[o] + "</a> "; }

		return c + "</td></tr></table><br /></div></div></div></div></div><br />";
	},

	// QUICKEDIT //
	quickSimpleText:function( rel, name, value, readonly )
	{
		return "<label class=\"quickedit_field\"><span class=\"left\"><i>" + name + "</i></span><span class=\"center\"><i><span class=\"field\"><input " + ( readonly ? "readonly=\"true\" " : "" ) + "id=\"quick_editing_" + rel + "\" name=\"" + rel + "\" type=\"text\" value=\"" + value + "\" /></span></i></span><br style=\"clear:both\" /></label>";
	},

	quickImage:function( rel, name, value, params, multiples )
	{
		if( value == "" ) value = Language.undefined;

		var v = value.split( "/" );
		v = v[v.length - 1];
		value = UPLOAD_FOLDER + value;

		return "<label class=\"quickedit_field\"><span class=\"left\"><i>" + name + "</i></span><span id=\"quick_editing_" + rel + "\" params=\"" + params + "\" type=\"image\" class=\"center\"><a class=\"img_link\" href=\"" + value + "\" target=\"_blank\"><img src=\"./utils/thumb.php?src=" + value + "&w=56&h=42\" alt=\"\" /></a><div class=\"image_info\"><i>" + v + "</i><br /><span class=\"general_icon icon_delete\"></span><a href=\"javascript:quicker.deleteFile('" + rel + "')\">" + Language.deletes + "</a> <span class=\"general_icon icon_select\"></span><a href=\"javascript:" + ( multiples ? "quicker.selectFileBallon('" + rel + "');" : "quicker.selectFile('" + rel + "');" ) + "\">" + Language.selectImage + "</a></div></span><br style=\"clear:both\" /></label>";
	},

	quickOptions:function( rel, name, value, opts, params )
	{
		var c = "<div id=\"quick_editing_" + rel + "\" class=\"quickedit_field\"><span class=\"left\"><i>" + name + "</i></span><span class=\"center\"><i>";

		var i = 0;
		var v = Number( value ) || 0;

		if( params == "value='string'" )
			for( var o in opts )
			{
				c += "<label><input class=\"options_" + rel + "\" type=\"checkbox\" name=\"" + o + "\"" + ( value.indexOf( o + ";" ) != -1 ? "checked=\"checked\"" : "" ) + " value=\"" + opts[o] + "\" /> " + opts[o] + "</label>&nbsp;&nbsp;";
				i++;
			}
		else
			for( var o in opts )
			{
				c += "<label><input class=\"options_" + rel + "\" type=\"checkbox\" name=\"" + o + "\"" + ( v >> o & 0x01 ? "checked=\"checked\"" : "" ) + " value=\"" + opts[o] + "\" /> " + opts[o] + "</label>&nbsp;&nbsp;";
				i++;
			}


		return c + "</i></span><br style=\"clear:both\" /></div>";
	},

	quickSelect:function( rel, name, value, opts )
	{
		var c = "<label class=\"quickedit_field\"><span class=\"left\"><i>" + name + "</i></span><span class=\"center\"><i><select id=\"quick_editing_" + rel + "\" name=\"" + rel + "\" class=\"edit_select\">";
		for( var o in opts )
		{
			c += "<option value=\"" + o + "\"" + ( value == o ? "selected=\"selected\"" : "" ) + ">" + opts[o] + "</option>";
		}
		return c + "</select></i></span><br style=\"clear:both\" /></label>";
	},

	quickFile:function( rel, name, value, params, multiples )
	{
		if( value == "" ) value = Language.undefined;

		var v = value.split( "/" );
		v = v[v.length - 1];
		value = UPLOAD_FOLDER + value;
		return "<label class=\"quickedit_field\"><span class=\"left\"><i>" + name + "</i></span><span id=\"quick_editing_" + rel + "\" params=\"" + params + "\" type=\"file\" class=\"center\"><a class=\"img_link\" href=\"" + value + "\" target=\"_blank\"><img src=\"./utils/thumb.php?src=" + value + "&w=56&h=42\" alt=\"\" /></a><div class=\"image_info\"><i>" + v + "</i><br /><span class=\"general_icon icon_delete\"></span><a href=\"javascript:quicker.deleteFile('" + rel + "')\">" + Language.deletes + "</a> <span class=\"general_icon icon_select\"></span><a href=\"javascript:" + ( multiples ? "quicker.selectFileBallon('" + rel + "');" : "quicker.selectFile('" + rel + "');" ) + "\">" + Language.selectFile + "</a></div></span><br style=\"clear:both\" /></label>";
	},

	quickDatetime:function( rel, name, value, params, readonly )
	{
		var d = dateSqlToStamp( value );
		d = d.split( " " );

		return "<div class=\"quickedit_field\" params=\"" + params + "\" id=\"quick_editing_" + rel + "\" name=\"" + rel + "\" value=\"" + value + "\"><span class=\"left\"><i>" + name + "</i></span><span class=\"center\"><i><div class=\"datefield\">" + Language.date.toLowerCase() + " <span class=\"field\"><input class=\"date\" " + ( readonly ? "readonly=\"true\" " : "" ) + " value=\"" + d[0] + "\" type=\"text\" " + ( readonly ? "" : "onblur=\"callendar.show(this)\" onclick=\"callendar.show(this)\" " ) + "/></span></div> <div class=\"datefield\"> " + Language.time.toLowerCase() + " <span class=\"field\"><input class=\"hour\" " + ( readonly ? "readonly=\"true\" " : "" ) + " value=\"" + ( d[1] ? d[1] : "" ) + "\" type=\"text\" /></span></div></i></span><br style=\"clear:both\" /></div>";
	},

	uSimpleText:function( id, name, value, readonly, about )
	{
		value = value || "";
		return "<label class=\"quickedit_field" + ( about ? " set_field" : "" ) + "\"><span class=\"left\"><i>" + name + "</i></span><span class=\"center\"><i><span class=\"field\"><input " + ( readonly ? "readonly=\"true\" " : "" ) + "id=\"" + id + "\" name=\"" + id + "\" type=\"text\" value=\"" + value + "\" /></span></i></span>" + ( about ? "<span class=\"right\"><i>" + about + "</i></span>" : "" ) + "<br style=\"clear:both\" /></label>";
	},

	uPassword:function( id, name, value, readonly, about )
	{
		value = value || "";
		return "<label class=\"quickedit_field" + ( about ? " set_field" : "" ) + "\"><span class=\"left\"><i>" + name + "</i></span><span class=\"center\"><i><span class=\"field\"><input " + ( readonly ? "readonly=\"true\" " : "" ) + "id=\"" + id + "\" name=\"" + id + "\" type=\"password\" value=\"" + value + "\" /></span></i></span>" + ( about ? "<span class=\"right\"><i>" + about + "</i></span>" : "" ) + "<br style=\"clear:both\" /></label>";
	},

	uText:function( id, name, value, readonly, about )
	{
		value = value || "";
		return "<label class=\"quickedit_field" + ( about ? " set_field" : "" ) + "\"><span class=\"left\"><i>" + name + "</i></span><span class=\"center\"><i><span class=\"field\"><textarea " + ( readonly ? "readonly=\"true\" " : "" ) + "id=\"" + id + "\" name=\"" + id + "\">" + value + "</textarea></span></i></span>" + ( about ? "<span class=\"right\"><i>" + about + "</i></span>" : "" ) + "<br style=\"clear:both\" /></label>";
	},

	uImage:function( id, name, value, params, about, deletes, selects )
	{
		if( value == "" ) value = Language.undefined;

		var v = value.split( "/" );
		v = v[v.length - 1];
		value = UPLOAD_FOLDER + value;

		return "<label class=\"quickedit_field" + ( about ? " set_field" : "" ) + "\"><span class=\"left\"><i>" + name + "</i></span><span id=\"" + id + "\" params=\"" + params + "\" type=\"image\" class=\"center\"><a class=\"img_link\" href=\"" + value + "\" target=\"_blank\"><img src=\"./utils/thumb.php?src=" + value + "&w=56&h=42\" alt=\"\" /></a><div class=\"image_info\"><i>" + v + "</i><br /><span class=\"general_icon icon_delete\"></span><a href=\"javascript:" + deletes + "('" + id + "')\">" + Language.deletes + "</a> <span class=\"general_icon icon_select\"></span><a href=\"javascript:javascript:" + selects + "('" + id + "')\">" + Language.selectImage + "</a></div></span>" + ( about ? "<span class=\"right\"><i>" + about + "</i></span>" : "" ) + "<br style=\"clear:both\" /></label>";
	},

	uOptions:function( id, name, value, opts, params, about )
	{
		var c = "<div id=\"" + id + "\" class=\"quickedit_field" + ( about ? " set_field" : "" ) + "\"><span class=\"left\"><i>" + name + "</i></span><span class=\"center\"><i>";

		var i = 0;
		var v = Number( value ) || 0;

		if( params == "value='string'" )
			for( var o in opts )
			{
				c += "<label><input class=\"options_" + id + "\" type=\"checkbox\" name=\"" + o + "\"" + ( value.indexOf( o + ";" ) != -1 ? "checked=\"checked\"" : "" ) + " value=\"" + opts[o] + "\" /> " + opts[o] + "</label>&nbsp;&nbsp;";
				i++;
			}
		else
			for( var o in opts )
			{
				c += "<label><input class=\"options_" + id + "\" type=\"checkbox\" name=\"" + o + "\"" + ( v >> o & 0x01 ? "checked=\"checked\"" : "" ) + " value=\"" + opts[o] + "\" /> " + opts[o] + "</label>&nbsp;&nbsp;";
				i++;
			}

		return c + "</i></span>" + ( about ? "<span class=\"right\"><i>" + about + "</i></span>" : "" ) + "<br style=\"clear:both\" /></div>";
	},

	uSelect:function( id, name, value, opts, about, readonly )
	{
		var c = "<label class=\"quickedit_field" + ( about ? " set_field2" : "" ) + "\"><span class=\"left\"><i>" + name + "</i></span><span class=\"center\"><i><select id=\"" + id + "\" name=\"" + id + "\" " + ( readonly ? "readonly=\"true\" " : "" ) + ">";

		for( var o in opts )
		{
			c += "<option value=\"" + o + "\"" + ( value == o ? "selected=\"selected\"" : "" ) + ">" + opts[o] + "</option>";
		}

		return c + "</select></i></span>" + ( about ? "<span class=\"right\"><i>" + about + "</i></span>" : "" ) + "<br style=\"clear:both\" /></label>";
	},

	uFile:function( id, name, value, params, about, deletes, selects )
	{
		if( value == "" ) value = Language.undefined;

		var v = value.split( "/" );
		v = v[v.length - 1];
		value = UPLOAD_FOLDER + value;
		return "<label class=\"quickedit_field" + ( about ? " set_field" : "" ) + "\"><span class=\"left\"><i>" + name + "</i></span><span id=\"" + id + "\" params=\"" + params + "\" type=\"file\" class=\"center\"><a class=\"img_link\" href=\"" + value + "\" target=\"_blank\"><img src=\"./utils/thumb.php?src=" + value + "&w=56&h=42\" alt=\"\" /></a><div class=\"image_info\"><i>" + v + "</i><br /><span class=\"general_icon icon_delete\"></span><a href=\"javascript:" + deletes + "('" + id + "')\">" + Language.deletes + "</a> <span class=\"general_icon icon_select\"></span><a href=\"javascript:" + selects + "('" + id + "')\">" + Language.selectFile + "</a></div></span>" + ( about ? "<span class=\"right\"><i>" + about + "</i></span>" : "" ) + "<br style=\"clear:both\" /></label>";
	},

	uDatetime:function( id, name, value, params, readonly, about )
	{
		var d = dateSqlToStamp( value );
		d = d.split( " " );

		return "<div class=\"quickedit_field" + ( about ? " set_field" : "" ) + "\" params=\"" + params + "\" id=\"" + id + "\" name=\"" + id + "\" value=\"" + value + "\"><span class=\"left\"><i>" + name + "</i></span><span class=\"center\"><i><div class=\"datefield\">" + Language.date.toLowerCase() + " <span class=\"field\"><input class=\"date\" " + ( readonly ? "readonly=\"true\" " : "" ) + " value=\"" + d[0] + "\" type=\"text\" " + ( readonly ? "" : "onblur=\"callendar.show(this)\" onclick=\"callendar.show(this)\" " ) + "/></span></div> <div class=\"datefield\"> " + Language.time.toLowerCase() + " <span class=\"field\"><input class=\"hour\" " + ( readonly ? "readonly=\"true\" " : "" ) + " value=\"" + ( d[1] ? d[1] : "" ) + "\" type=\"text\" /></span></div></i></span>" + ( about ? "<span class=\"right\"><i>" + about + "</i></span>" : "" ) + "<br style=\"clear:both\" /></div>";
	},

	uColor:function( id, name, value, params, readonly, about )
	{
		value = value || "";
		return "<label class=\"quickedit_field" + ( about ? " set_field" : "" ) + "\"><span class=\"left\"><i>" + name + "</i></span><span class=\"center\"><i><span class=\"field\"><input params=\"" + params + "\" " + ( readonly ? "readonly=\"true\" " : "" ) + "id=\"" + id + "\" name=\"" + id + "\" type=\"text\" value=\"" + value + "\" " + ( readonly ? "" : "onblur=\"colorPicker.show(this)\" onclick=\"colorPicker.show(this)\" " ) + "/></span></i></span>" + ( about ? "<span class=\"right\"><i>" + about + "</i></span>" : "" ) + "<br style=\"clear:both\" /></label>";
	},

	uSelectInfo:function( id, name, value, opts )
	{
		var c = "<label class=\"quickedit_field set_field2\"><span class=\"left\"><i>" + name + "</i></span><span class=\"center\"><i><select onchange=\"UI.changeSelectInfo( this )\" id=\"" + id + "\" name=\"" + id + "\">";

		for( var o in opts )
		{
			c += "<option value=\"" + o + "\"" + ( value == o ? "selected=\"selected\"" : "" ) + " about=\"" + opts[o].about + "\">" + opts[o].name + "</option>";
		}

		return c + "</select></i></span><span class=\"right\"><i>" + ( opts[value].about || "" ) + "</i></span><br style=\"clear:both\" /></label>";
	},

	uNumber:function( id, name, value, readonly, about )
	{
		value = value || "";
		return "<div class=\"quickedit_field set_field2\"><span class=\"left\"><i>" + name + "</i></span><span class=\"center\"><i><span class=\"field\"><input " + ( readonly ? "readonly=\"true\" " : "" ) + "id=\"" + id + "\" name=\"" + id + "\" type=\"text\" value=\"" + value + "\" /></span></i></span>" + ( about ? "<span class=\"right\"><i>" + about + "</i></span>" : "" ) + "<br style=\"clear:both\" /></div>";
	},

	uNumber2D:function( id, name, value, readonly, about )
	{
		value = String( value || ";" ).split( ";" );
		value[0] = Number( value[0] ) || 0;
		value[1] = Number( value[1] ) || 0;

		return "<label class=\"quickedit_field set_field2\"><span class=\"left\"><i>" + name + "</i></span><span class=\"center\"><i id=\"" + id + "\"><span class=\"field\"><input class=\"value0\"" + ( readonly ? "readonly=\"true\" " : "" ) + " type=\"text\" value=\"" + value[0] + "\" /></span> x <span class=\"field\"><input class=\"value1\"" + ( readonly ? "readonly=\"true\" " : "" ) + " type=\"text\" value=\"" + value[1] + "\" /></span></i></span>" + ( about ? "<span class=\"right\"><i>" + about + "</i></span>" : "" ) + "<br style=\"clear:both\" /></label>";
	},

	uOnoff:function( id, name, value, opts, about )
	{
		var t = "<div class=\"slider onoff\"><div class=\"options\"><a class=\"button button_on\" onclick=\"Animation.sliderButtonClick(this);\" href=\"javascript:UI.changeOnoff('" + id + "', true )\">&nbsp;&nbsp;" + Language.on + "</a><a class=\"button button_off\" style=\"color:#747474\" onclick=\"Animation.sliderButtonClick(this);\" href=\"javascript:UI.changeOnoff('" + id + "', false )\">&nbsp;&nbsp;" + Language.off + "</a></div><div class=\"base\"><div class=\"left\"></div><div class=\"center\"><div class=\"right\"></div><div class=\"selecter\"></div></div></div></div>";

		$( "body" ).append( "<span id=\"temporary_slider\"></div>" );

		var o = $( "#temporary_slider" );
		o.append( t );
		var t = o.find( ".slider .button_" + value );
		o.find( ".slider .selecter" ).css( "width", t.width() + 3 ).css( "margin-left", t.position().left -( 5 + t.parent().parent().position().left ) );
		var rtn = o.html();
		o.remove();

		var on = opts.on || "";
		var off = opts.off || "";

		return "<label class=\"quickedit_field set_field2\"><span class=\"left\"><i>" + name + "</i></span><span class=\"center\"><i><input id=\"" + id + "\" type=\"checkbox\" style=\"display:none\" " + ( value == "on"? "checked=\"checked\"" : "" ) + " on=\"" + on + "\" off=\"" + off + "\"/>" + rtn + "</i></span>" + ( about ? "<span class=\"right\"><i>" + ( value == "on" ? on : off ) + "</i></span>" : "" ) + "<br style=\"clear:both\" /></label>";
	},

	uLabel:function( id, name, value )
	{
		return "<label class=\"quickedit_field set_field2\"><span class=\"left\"><i>" + name + "</i></span><span class=\"center\"><i id=\"" + id + "\">" + value + "</i></span><br style=\"clear:both\" /></label>";
	},

	uLink:function( id, name, value )
	{
		return "<label class=\"quickedit_field set_field2\"><span class=\"left\"><i> </i></span><span class=\"center\"><i><a id=\"" + id + "\" href=\"" + value + "\">" + name + "</a></i></span><br style=\"clear:both\" /></label>";
	},

	luOnoff:function( id, value, opts, onchange )
	{
		var t = "<div class=\"slider onoff\"><div class=\"options\"><a class=\"button button_on\" onclick=\"Animation.sliderButtonClick(this);\" href=\"javascript:UI.changeOnoff('" + id + "', true )\">&nbsp;&nbsp;" + Language.on + "</a><a class=\"button button_off\" style=\"color:#747474\" onclick=\"Animation.sliderButtonClick(this);\" href=\"javascript:UI.changeOnoff('" + id + "', false )\">&nbsp;&nbsp;" + Language.off + "</a></div><div class=\"base\"><div class=\"left\"></div><div class=\"center\"><div class=\"right\"></div><div class=\"selecter\"></div></div></div></div>";

		$( "body" ).append( "<span id=\"temporary_slider\"></div>" );

		var o = $( "#temporary_slider" );
		o.append( t );
		var t = o.find( ".slider .button_" + value );
		o.find( ".slider .selecter" ).css( "width", t.width() + 3 ).css( "margin-left", t.position().left -( 5 + t.parent().parent().position().left ) );
		var rtn = o.html();
		o.remove();

		var on = opts.on || "";
		var off = opts.off || "";

		return "<span class=\"onoff_container\"><input id=\"" + id + "\" " + ( onchange ? "onchange=\"" + onchange + "('" + id + "')\" " : "" ) + "type=\"checkbox\" style=\"display:none\" " + ( value == "on" ? "checked=\"checked\"" : "" ) + " on=\"" + on + "\" off=\"" + off + "\"/>" + rtn + "</span>";
	},

	luLabel:function( id, value )
	{
		return "<span class=\"lu\" id=\"" + id + "\">" + value + "</span>";
	},

	luLink:function( id, name, value )
	{
		return "<a class=\"lu\" id=\"" + id + "\" href=\"javascript:setter.set('" + id + "')\">" + name + "</a>";
	},

	changeOnoff:function( id, value )
	{
		var text = "";
		var t = $( "#" + id );

		if( value )
		{
			text = t.attr( "checked", "checked" ).attr( "on" );
		}
		else
		{
			text = t.removeAttr( "checked" ).attr( "off" );
		}

		t.change();

		var a = t.parent().parent().parent().find( ".right i" );
		Animation.changeAbout( a, text );
	},

	changeSelectInfo:function( target )
	{
		var t = $( target );
		var a = t.parent().parent().parent().find( ".right i" );
		Animation.changeSelectInfo( a, t.find( ":selected" ).attr( "about" ) );
	}
}
