Animation = 
{
	mode:Settings.animationLevel == 2 && ( ( Browser.name == "Chrome" && Browser.version >= 4 ) || ( Browser.name == "Safari" && Browser.version >= 4 ) ) ? 1 : 0,
	
	longTime:Settings.animationLevel == 2 ? 400 : ( Settings.animationLevel == 1 ? 200 : 0 ),
	
	shortTime:Settings.animationLevel == 2 ? 200 : ( Settings.animationLevel == 1 ? 0 : 0 ),
	
	timer:function( time, step, func )
	{
		var div = "anim_" + Math.floor( Math.random() * 99999 );
		$( document.body ).append( "<div id=\""+ div + "\"></div>" );
		div = $( "#" + div ).css( "left", 0 ).animate( { left:time }, { step:step, duration:time, complete:function() { div.remove(); if( func ) func() } } );
	},
	
	loginOut:function()
	{
		$( "#loginbox" ).stop().animate( { opacity:"hide" }, Animation.longTime );
	},
	
	showWhiteScreen:function( func )
	{
		$( "#white_screen" ).stop().fadeIn( 0, func );
	},
	
	hideWhiteScreen:function( func )
	{
		$( "#white_screen" ).stop().fadeOut( Animation.longTime, func );
	},
	
	hideLoginForm:function( func )
	{
		if( this.mode == 1 )
		{
			var func2 = function()
			{
				t.css( "opacity", "hide" ).css( "-webkit-transform", "rotateY(0deg)" );
				t.parent().css( "-webkit-perspective", "none" );
				func();
			}
			
			$( "#lost_password" ).animate( { opacity:"hide" }, Animation.longTime );
			
			var t = $( "#loginform" );
			t.parent().css( "-webkit-perspective", 1000 );
			
			Animation.timer( Animation.longTime, function( value ) {
				t.stop().css( "-webkit-transform", "rotateY(" + -90 * value / Animation.longTime + "deg)" );
			}, func2 );
		}
		else
		{
			$( "#lost_password" ).animate( { opacity:"hide" }, Animation.longTime );
			$( "#loginform" ).animate( { opacity:"hide", marginTop:10 }, Animation.longTime, func );
		}
	},
	
	preloaderProgress:function( value, func )
	{
		$( "#loaded" ).stop().animate( { width:value * 100 + "%" }, Animation.shortTime, func );
	},
	
	hideStarting:function( func )
	{
		$( "#starting" ).animate( { opacity:"hide" }, Animation.longTime, func );
	},
	
	showPreloader:function()
	{
		if( this.mode == 1 )
		{
			var func2 = function()
			{
				l.parent().css( "-webkit-perspective", "none" );
			}
			
			var l = $( "#loading" ).css( { display:"block", opacity:1 } );
			l.parent().css( "-webkit-perspective", 1000 );
			
			Animation.timer( Animation.longTime, function( value ) {
				l.stop().css( "-webkit-transform", "rotateY(" + ( 90 - 90 * value / Animation.longTime ) + "deg)" );
			} );
		}
		else $( "#loading" ).animate( { opacity:"1" }, Animation.longTime );
	},
	
	showMainContent:function( func )
	{
		$( "#main_content" ).animate( { opacity:"show" }, Animation.longTime, func );
	},
	
	hideMainContent:function( func )
	{
		$( "#main_content" ).animate( { opacity:"hide" }, Animation.longTime, func );
	},
	
	sliderButtonClick:function( target )
	{
		var t = $( target );
		var o = $( t ).parent().parent();
		o.find( ".selecter" ).animate( { width:t.width() + 3, marginLeft:t.position().left -( 5 + o.position().left ) }, Animation.longTime );
	},
	
	showBallon:function( id, func )
	{
		$( "#ballon_" + id ).stop().animate( { opacity:"show" }, Animation.longTime, func );
	},
	
	hideBallon:function( id, func )
	{
		$( "#ballon_" + id ).stop().animate( { opacity:"hide" }, Animation.longTime, func );
	},
	
	showContentBallon:function( target, func )
	{
		target.stop().animate( { opacity:1 }, Animation.longTime, func );
	},
	
	hideContentBallon:function( target, func )
	{
		target.stop().animate( { opacity:1 }, Animation.shortTime, func );
	},
	
	changeBallonSize:function( target, values, func )
	{
		target.stop().animate( values, Animation.shortTime, func() );
	},
	
	showEditingGroup:function( target )
	{
		target.stop().animate( { opacity:"show" }, Animation.longTime );
	},
	
	hideEditingGroup:function( target )
	{
		target.stop().animate( { opacity:"hide" }, Animation.longTime );
	},
	
	setProgress:function( target, value, func )
	{
		target.stop().animate( { width:value * 100 + "%" }, Animation.longTime, func );
	},
	
	selectCategories:function( id )
	{
		var func = function()
		{
			a.css( "display", "none" );
		}
		
		var t = $( "#" + id );
		var a = t.find( ".add_category" );
		a.stop().animate( { height:0 }, Animation.shortTime, func );
		var r = t.find( "label .remove_category a" ).stop().animate( { width:0, marginRight:5 }, Animation.shortTime );
	},
	
	manageCategories:function( id )
	{
		var t = $( "#" + id );
		var a = t.find( ".add_category" ).stop().css( "display", "block" ).height( "auto" );
		var h1 = a.height();
		
		a.height( 1 ).animate( { height:h1 }, Animation.shortTime );
		t.find( ".remove_category a" ).stop().css( { width:1, marginRight:1 } ).animate( { width:7, marginRight:5 }, Animation.shortTime );
	},
	
	addCategory:function( v, i )
	{
		var func = function() { v.find( ".remove_category a" ).animate( { width:7, marginRight:5 }, Animation.shortTime + i * 5 ) };
		
		v = $( v );
		h = v.height();
		v.height( 1 ).animate( { height:h }, Animation.shortTime + i * 30, func ).removeClass( "recent" );
	},
	
	removeCategory:function( v )
	{
		v = $( v );
		v.parent().stop().animate( { height:0 }, Animation.shortTime, function() { v.parent().remove() } );
	},
	
	addTag:function( t )
	{
		t.animate( { opacity:"show" }, Animation.shortTime ).removeClass( "recent" );
	},
	
	removeTag:function( t, func )
	{
		t.stop().animate( { opacity:"hide" }, Animation.shortTime, func );
	},
	
	showTags:function( t )
	{
		t.css( "display", "block" ).height( "auto" );
		var h = t.height();
		
		t.stop().height( 1 ).animate( { height:h }, Animation.shortTime );
	},
	
	hideTags:function( t )
	{
		t.stop().animate( { height:0 }, Animation.shortTime, function() { t.css( "display", "none" ) } );
	},
	
	showQuickEdit:function( t1, t2 )
	{
		var called = false;
		
		var func = function()
		{
			if( called ) return;
			t1.css( "display", "none" );
			t2.css( "display", "table-row" ).children().css( "display", "table-cell" ).css( "opacity", 0 ).stop().animate( { opacity:1 }, Animation.longTime, function() { t2.children().css( "opacity", "show" ) } );
			
			called = true;
		}
		
		var value = t2.height() - t1.height();
		t1.children().stop().animate( { opacity:"hide" }, Animation.longTime );
		t2.stop().css( "overflow", "hidden" ).css( "display", "block" ).height( 1 );
		t2.children().css( "display", "none" );
		t2.animate( { height:value }, Animation.longTime, func );
	},
	
	hideQuickEdit:function( t1, t2 )
	{
		var h1 = t1.height();
		var h2 = t2.height();
		
		var func = function()
		{
			t2.remove();
			t1.css( "height", "auto" );
			t1.stop().css( "display", "table-row" ).children().stop().animate( { opacity:"show" }, Animation.longTime );
			// t1.children().children().stop().css( "opacity", 0 ).animate( { opacity:1 }, Animation.longTime, function() { t1.children().children().css( "opacity", "show" ) } );
		}
		
		t2.stop().animate( { opacity:"hide" }, Animation.longTime, func );
	},
	
	showSimpleTableStep1:function( target, func )
	{
		$( "#editing_content_" + target ).animate( { height:300 }, Animation.longTime, func ).find( "a" ).animate( { opacity:0 }, Animation.longTime );
	},
	
	showSimpleTableStep2:function( temp, temp2 )
	{
		temp.find( "> *" ).css( "display", "none" ).animate( { opacity:"show" }, Animation.longTime );
		temp2.find( ".options" ).fadeIn();
	},
	
	removeTableRow:function( target )
	{
		var t = $( target );
		t.animate( { opacity:"hide" }, Animation.longTime, function() { t.remove() } );
	},
	
	showEditerBox:function()
	{
		$( "#editer_screen" ).fadeTo( Animation.longTime, 0.5 );
		$( "#editer_box" ).fadeIn( Animation.longTime );
	},
	
	hideEditerBox:function( func )
	{
		$( "#editer_screen" ).fadeOut();
		$( "#editer_box" ).fadeOut( func );
	},
	
	backMonth:function( m1, m2 )
	{
		m1 = $( m1 );
		m2 = $( m2 );
		m1.css( "left", "-225px" ).animate( { left:0 }, Animation.longTime );
		m2.css( "left", "-225px" ).animate( { left:0 }, Animation.longTime, function() { m2.remove(); m1.css( "left", 0 ) } );
	},
	
	nextMonth:function( m1, m2 )
	{
		m1 = $( m1 );
		m2 = $( m2 );
		m1.animate( { left:-225 }, Animation.longTime );
		m2.animate( { left:-225 }, Animation.longTime, function() { m1.css( "left", 0 ); m2.remove() } );
	},
	
	showBox:function( target, func )
	{
		if( this.mode == 1 )
		{
			var func2 = function()
			{
				target.parent().css( "-webkit-perspective", "none" );
				if( func ) func();
			}
			
			var p = target.position();
			target.stop().css( "-webkit-transform", "rotateX(50deg) rotateY(50deg) scale3d(0.2, 0.2, 1)" ).css( { display:"none", top:p.top + 50, left:p.left - 50 } ).animate( { opacity:"show" }, Animation.longTime );
			target.parent().css( "-webkit-perspective", 1000 );
			Animation.timer( Animation.longTime, function( value ) {
				var value = value / Animation.longTime;
				target.css( "-webkit-transform", "rotateX(" + ( 50 - 50 * value ) + "deg) rotateY(" + ( 50 - 50 * value ) + "deg) scale3d(" + ( 0.2 + 0.8 * value ) + ", " + ( 0.2 + 0.8 * value ) + ", 1)" ).css( { top:p.top + 50 - 50 * value, left:p.left - 50 + 50 * value } );
			}, ( func ) ? func : null );
		}
		else target.stop().css( "display", "none" ).animate( { opacity:"show" }, Animation.longTime );
	},
	
	removeBox:function( target, func )
	{
		if( this.mode == 1 )
		{
			var func2 = function()
			{
				target.parent().css( "-webkit-perspective", "none" );
				target.remove();
				if( func ) func();
			}
			
			target = $( target );
			var p = target.position();
			target.stop().animate( { opacity:"hide" }, Animation.longTime );
			target.parent().css( "-webkit-perspective", 1000 );
			Animation.timer( Animation.longTime, function( value ) {
				var value = value / Animation.longTime;
				target.css( "-webkit-transform", "rotateX(" + ( 50 * value ) + "deg) rotateY(" + ( 50 * value ) + "deg) scale3d(" + ( 1 - 0.8 * value ) + ", " + ( 1 - 0.8 * value ) + ", 1)" ).css( { top:p.top + 50 * value, left:p.left - 50 * value } );
			}, func2 );
		}
		else $( target ).stop().fadeOut( Animation.shortTime, function() { $( target ).remove(); if( func ) func() } );
	},
	
	boxMinMaxChange:function( target )
	{
		var a = $( target );
		var t = a.parent().parent().parent().parent();
		var b = t.find( "> .bottom" );
		
		if( t.css( "min-height" ) == "32px" )
		{
			var h = b.css( "display", "block" ).height( "auto" ).height();
			t.animate( { minHeight:32 + h + 5 }, Animation.longTime );
			a.animate( { backgroundPosition:0 }, Animation.longTime );
			
			b.height( 1 ).animate( { height:h }, Animation.longTime ).find( "> .left" ).animate( { marginTop:0 }, Animation.longTime, function()
			{
				t.css( "min-height", null );
				b.height( "auto" );
			} );
		}
		else
		{
			var h = b.height();
			t.animate( { minHeight:32 }, Animation.longTime );
			a.animate( { backgroundPosition:-22 }, Animation.longTime );
			b.animate( { height:0 }, Animation.longTime, function() { b.css( "display", "none" ) } ).find( "> .left" ).animate( { marginTop:-h }, Animation.longTime );
		}
	},
	
	showScreen:function( target )
	{
		target.stop().animate( { opacity:"show" }, Animation.shortTime );
		target.stop().animate( { opacity:0.5 }, Animation.shortTime );
	},
	
	showScreen2:function( target )
	{
		target.stop().animate( { opacity:"show" }, Animation.shortTime );
		target.stop().animate( { opacity:1 }, Animation.shortTime );
	},
	
	hideScreen:function( target )
	{
		target.stop().animate( { opacity:"hide" }, Animation.shortTime );
	},
	
	showImageBox:function()
	{
		$( "#image_box" ).fadeIn( Animation.longTime );
	},
	
	hideImageBox:function( func )
	{
		$( "#image_box" ).fadeOut( Animation.longTime, func );
	},
	
	showInsertBox:function()
	{
		$( "#insert_box" ).fadeIn( Animation.longTime );
	},
	
	hideInsertBox:function()
	{
		var func = function()
		{
			$( "#insert_box" ).remove();
		}
		
		$( "#insert_box" ).fadeOut( Animation.longTime, func );
	},
	
	tableOverHandler:function( target )
	{
		var func = function() { $( this ).find( ".quick_edit" ).css( { overflow:"visible", display:"block" } ); };
		$( target ).find( ".quick_edit" ).stop().css( "overflow", "hidden" ).animate( { marginTop:5, height:28 }, Animation.longTime, func );
	},
	
	tableOutHandler:function( target )
	{
		var func = function() { $( this ).find( ".quick_edit" ).css( { overflow:"visible", display:"none" } ); };
		$( target ).find( ".quick_edit" ).stop().css( "overflow", "hidden" ).animate( { marginTop:0, height:0 }, Animation.longTime, func );
	},
	
	slideUp:function( target )
	{
		target.slideUp( Animation.longTime );
	},
	
	slideDown:function( target )
	{
		target.slideDown( Animation.longTime );
	},
	
	changeAbout:function( target, text )
	{
		target.animate( { opacity:0 }, Animation.shortTime, function() { target.html( text ).animate( { opacity:1 }, Animation.shortTime ) } )
	},
	
	showUploadBox:function()
	{
		$( "#upload_box" ).fadeIn( Animation.longTime );
	},
	
	hideUploadBox:function( func )
	{
		$( "#upload_box" ).fadeOut( Animation.longTime, func );
	},
	
	moveContentUploadBox:function( value )
	{
		$( "#upload_box .content" ).animate( { left:value }, Animation.longTime );
	},
	
	changeUploadButton:function( target, value )
	{
		var func = function()
		{
			t.html( value ).css( "display", "block" ).animate( { opacity:"show" }, Animation.shortTime );
			swfobject.embedSWF( THEME_FOLDER + "swfs/flashup.swf", "flashup", "160", "25", "9.0.0", "expressInstall.swf", { text:Language.select, width:160 }, { menu:"false", wmode:"transparent",	bgcolor:"#FFFFFF" }, { id:"flashup" } );
		}
		
		var t = $( target );
		t.animate( { opacity:"hide" }, Animation.shortTime, func );
	}
}