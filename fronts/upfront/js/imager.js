
function Imager()
{
	this.ratio;
	this.width;
	this.height;
	this.source;
	this.params;
	this.current;
	this.fixedSize;
	
	this.realwidth;
	this.realheight;
	
	this.hide = function( callback )
	{
		var func = function()
		{
			$( "#image_box" ).remove();
			if( typeof( callback ) == "function" ) callback();
		}
		
		hidePopboxScreen();
		Animation.hideImageBox( func );
	}
	
	this.cancel = function()
	{
		imager.hide( function() { imager.callback( false ); } );
	}
	
	this.add = function()
	{
		$( "#imager_cancel" ).attr( "disabled", "true" );
		$( "#imager_submit" ).attr( "disabled", "true" );
		// callback
		
		if( imager.current )
		{
			var callback = function( data )
			{
				imager.hide( function() { imager.callback( data.path ) } );
			}
			
			var post = { source:imager.source, format:imager.format };
			if( imager.params.size.length == 2 ) post.size = imager.params.size[0] + "x" + imager.params.size[1];
			if( imager.params.min.length == 2 ) post.min = imager.params.min[0] + "x" + imager.params.min[1];
			if( imager.params.max.length == 2 ) post.max = imager.params.max[0] + "x" + imager.params.max[1];
			
			var prop = imager.realwidth / $( "#cropbox" ).attr( "width" );
			
			post.crop = imager.current.x * prop + "," + imager.current.y * prop + "," + imager.current.w * prop + "," + imager.current.h * prop;
			
			$.post( "?Image::resizecrop", post, callback, "json" );
		}
	}
	
	this.init = function( src, params, callback )
	{
		imager.source = src;
		this.callback = callback;
		if( !params ) params = {};
		
		this.params = params;
		
		var temp = src.split( "/" );
		temp = temp[temp.length - 1].split( "." );
		var cformat = temp[temp.length - 1];
		
		imager.format = uploader.params.types[2] ? uploader.params.types[2] : "";
		
		var convert = imager.format != "" && imager.format != cformat ? cformat.toUpperCase() + " -> " + imager.format.toUpperCase() : "";
		
		$( "body" ).append( "<div class=\"popbox\" id=\"image_box\"><div class=\"top\"><div class=\"left\"></div><div class=\"center\"><div class=\"right\"></div><span>" + Language.editImage + " <a class=\"close_btn\" href=\"javascript:imager.cancel();\"> </a> </span></div></div><div class=\"container\"><div class=\"left\"></div><div class=\"center\"><div class=\"right\"></div><div class=\"content\"><h2>" + Language.youNeedAdjustImageSize + "</h2><div class=\"image\"></div><div class=\"image_sidebar\">" + convert + "<div class=\"image_info\"><span>" + Language.posX + ":</span><b id=\"image_infox\">214</b><br /><span>" + Language.posY + ":</span><b id=\"image_infoy\"></b><br /><br /><span>" + Language.width + ":</span><b id=\"image_infow\"></b><br /><span>" + Language.height + ":</span><b id=\"image_infoh\"></b></div></div><div align=\"right\">" + UI.redButton( Language.cancel, "cancel", "javascript:imager.cancel();" ) + " " + UI.redButton( Language.save, "save", "javascript:imager.add();" ) + "</div></div></div></div>" );
		
		showPopboxScreen();
		Animation.showImageBox();
		
		$( '#image_infow' ).html( "" ).attr( "enabled", "true" );
		$( '#image_infoh' ).html( "" ).attr( "enabled", "true" );
		
		this.fixedSize = false;
		var crop = {};
		var a, t;
		
		if( params.size.length == 2 )
		{
			crop.setSelect = [0, 0, params.size[0], params.size[1]];
			crop.aspectRatio = params.size[0] / params.size[1];
			this.fixedSize = true;
			
			$( '#image_infow' ).html( params.size[0] ).attr( "disabled", "true" );
			$( '#image_infoh' ).html( params.size[1] ).attr( "disabled", "true" );
		}
		else 
		{
			if( params.ratio.length == 2 ) this.ratio = crop.aspectRatio = params.ratio[0] / params.ratio[1];
			
			crop.setSelect = [0, 0, 1000, 1000];
		}
		
		if( a )
		{
			this.height = a[1];
			this.width = a[0];
		}
		
		var func = function( c )
		{
			imager.current = c;
			
			$( '#image_infox' ).html( c.x );
			$( '#image_infoy' ).html( c.y );
			
			if( !imager.fixedSize )
			{
				$( '#image_infoh' ).html( imager.height != 0 && c.h > imager.height ? imager.height : c.h );
				$( '#image_infow' ).html( imager.width != 0 && c.w > imager.width ? imager.width : c.w );
			}
		}
		
		var img = new Image();
		
		var cropper = function()
		{
			img.style.display = "block";
			imager.realwidth = img.width;
			imager.realheight = img.height;
			var prop = imager.realwidth / imager.realheight;
			
			if( prop > 380 / 265 )
			{
				img.width = 380;
				img.height = Number( 380 / prop );
			}
			else
			{
				img.width = Number( prop * 265 );
				img.height = 265;
			}
			
			crop.onChange = crop.onSelect = func;
			$( '#cropbox' ).Jcrop( crop );
			$( "#imager_cancel" ).removeAttr( "disabled" ).click( imager.cancel );
			$( "#imager_save" ).removeAttr( "disabled" ).click( imager.add );
		}
		
		var loading = function()
		{
			var verify = function()
			{
				if( $( "#cropbox" ).get() )
				{
					clearInterval( interval );
					cropper();
				}
			}
			
			var interval = window.setInterval( verify, 100 );
		}
		
		img.setAttribute( "id", "cropbox" );
		img.style.display = "none";
		img.onload = loading;
		img.src = src;
		
		$( "#imager_cancel" ).attr( "disabled", "disabled" );
		$( "#imager_submit" ).attr( "disabled", "disabled" );
		
		$( ".container .image" ).append( img );
	}
}
