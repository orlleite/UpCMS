// JavaScript Document
function ColorPicker()
{
	this.target;
	this.ballon;
	this.picker;
	
	this.show = function( target )
	{
		this.target = this.ballon.tposition = target;
		this.ballon.position();
		
		$( "#colorpickercontainer" ).ColorPickerSetColor($(this.target).val());
		this.ballon.show( function() { document.getElementById( "container" ).onclick = colorPicker.hide } );
	}
	
	this.hide = function()
	{
		colorPicker.ballon.hide();
		document.getElementById( "container" ).onclick = null;
	}
	
	this.init = function()
	{
		this.ballon = new Ballon( 'colorpicker', '<div id=\"colorpickercontainer\"></div>' );
		
		this.picker = $( "#colorpickercontainer" ).ColorPicker({
			onSubmit: function(hsb, hex, rgb, el) {
				$(colorPicker.target).val(hex);
				colorPicker.hide();
			},
			onChange: function(hsb, hex, rgb, el) {
				$(colorPicker.target).val(hex);
			},
			onBeforeShow: function () {
				$(this).ColorPickerSetColor($(colorPicker.target).val());
			},
			flat:true
		})
		.bind('keyup', function(){
			$(this).ColorPickerSetColor($(colorPicker.target).val());
		});
	}
	
	this.init();
}