<?php

/**
 * Print the index of Up!CMS Front
 * @author Orlando Leite
 * @version 0.8
 * @package front
 * @access public
 * @name Front Index
 */

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="chrome=1" />
<title><?php echo UP_APP_NAME ?></title>

<link rel="stylesheet" href="<?php echo UP_FRONT_FOLDER ?>styles/jquery.Jcrop.css" type="text/css" media="all" />
<link rel="stylesheet" href="<?php echo UP_FRONT_FOLDER ?>styles/init.css" type="text/css" media="all" />
<link rel="stylesheet" href="<?php echo UP_FRONT_FOLDER ?>styles/style.css" type="text/css" media="all" />
<link rel="stylesheet" href="<?php echo UP_FRONT_FOLDER ?>styles/popbox.css" type="text/css" media="all" />
<link rel="stylesheet" href="<?php echo UP_FRONT_FOLDER ?>styles/tipbox.css" type="text/css" media="all" />
<link rel="stylesheet" href="<?php echo UP_FRONT_FOLDER ?>styles/colorpicker/colorpicker.css" type="text/css" media="all" />

<?php
$styles = $UpCMS->html->listCSS();
foreach( $styles as $css )
	echo '<link rel="stylesheet" href="'.$css.'" type="text/css" media="all" />';
?>

<script type="text/javascript">
<?php

global $upload_folder, $plugin_folder;

$frontSettings = new stdClass();
$frontSettings->animationLevel = $UpCMS->options->get( "upfront", "animation_level" );
$frontSettings->minimizeBox = $UpCMS->options->get( "upfront", "minimize_box" );
$frontSettings->quickEdit = $UpCMS->options->get( "upfront", "quickedit" );
$frontSettings->listThumbSize = explode( ";", $UpCMS->options->get( "upfront", "list_thumb_size" ) );
$frontSettings->autoShowTableContent = $UpCMS->options->get( "upfront", "auto_show_table_content" );
$frontSettings->multipleAdding = $UpCMS->options->get( "upfront", "multiple_adding" );
$frontSettings->showUpVersion = $UpCMS->options->get( "upfront", "show_up_version" );

echo 'var THEME_FOLDER = "'.UP_FRONT_FOLDER.'";
var TimeStamp = "'.$Language->timeStamp.'";
var UPLOAD_FOLDER = "'.$upload_folder.'";
var PLUGIN_FOLDER = "'.$plugin_folder.'";
var Language = '.json_encode( $Language ).';
var Settings = '.json_encode( $frontSettings ).';
';

?>
</script>

<script type="text/javascript" src="<?php echo UP_FRONT_FOLDER ?>js/jquery-1.4.1.min.js"></script>
<script type="text/javascript" src="<?php echo UP_FRONT_FOLDER ?>js/functions.js"></script>
<script type="text/javascript" src="<?php echo UP_FRONT_FOLDER ?>js/browser.js"></script>
<script type="text/javascript" src="<?php echo UP_FRONT_FOLDER ?>js/animation.js"></script>
<script type="text/javascript" src="<?php echo UP_FRONT_FOLDER ?>js/swfaddress.js"></script>

<script type="text/javascript" src="<?php echo UP_FRONT_FOLDER ?>js/colorpicker.js"></script>
<script type="text/javascript" src="<?php echo UP_FRONT_FOLDER ?>js/tinymce/tiny_mce.js"></script>
<script type="text/javascript" src="<?php echo UP_FRONT_FOLDER ?>js/colorpicker/colorpicker.js"></script>

<?php
$javascripts = $UpCMS->html->listJavascripts();
foreach( $javascripts as $js )
	echo '<script type="text/javascript" src="'.$js.'"></script>';
?>
<!--script type="text/javascript" src="http://getfirebug.com/releases/lite/1.2/firebug-lite-compressed.js"></script-->
<style>

</style>
</head>

<body>
<div id="prompt"></div>
<!--div id="debug" style="width:100px; height:50px; position:fixed; background:#FFF; color:#000; top:0; left:0;"></div-->

<div id="starting">
	<div id="footer">
			<div class="left"></div>
			<div class="center">
				<div class="content"></div>
				<div class="right"></div>
				<div class="version"><?php if( $frontSettings->showUpVersion == "true" ) echo $Language->version." ".UP_APP_VERSION; ?></div>
			</div>
	</div>

	<div id="loginbox">
		<h1><?php echo UP_APP_NAME ?></h1>
		<form id="loginform" method="post" action="">
			<label><span class="label"><?php echo $Language->user ?>:</span><span class="field"><input name="username" type="text" /></span></label>
			<label><span class="label"><?php echo $Language->password ?>:</span><span class="field"><input name="password" type="password" /></span></label>
			<label id="checking"><input type="checkbox" /> <?php echo $Language->rememberMe ?></label><a id="login_button" href="javascript:;" onclick="$('#loginform').submit()"><span></span><?php echo $Language->login ?></a>
			<input type="submit" style="display:none" />
		</form>
		<a id="lost_password" href="#"><?php echo $Language->lostYourPassword ?></a>

		<div id="loading">
			<h1><?php echo $Language->loading ?></h1>
			<div id="progress"><div id="loaded"></div></div>
		</div>
	</div>
</div>

<div id="white_screen"></div>
<div id="popbox_screen"></div>
<div id="dark_screen"></div>
<div id="loading_screen"><div><?php echo $Language->loading ?></div></div>
<div id="editer_screen"></div>

<script type="text/javascript">
var displayname;
var usergroup;
var userid;

var home;
var setter;
var imager;
var lister;
var editer;
var inserter;
var uploader;
var callendar;
var boxEditer;
var colorPicker;

function loadCMS()
{
	var complete = function()
	{
		var func = function()
		{
			Animation.hideStarting( startCMS );
		}

		Animation.preloaderProgress( 1, func );
	}

	$( "#loginform" ).remove();
	$( "#lost_password" ).remove();

	Animation.showPreloader();

	var scripts = [
		"<?php echo UP_FRONT_FOLDER ?>js/tinymce/jquery.tinymce.js",
		"<?php echo UP_FRONT_FOLDER ?>js/swfobject.js",
		"<?php echo UP_FRONT_FOLDER ?>js/callendar.js",
		"<?php echo UP_FRONT_FOLDER ?>js/ui.js",
		"<?php echo UP_FRONT_FOLDER ?>js/home.js",
		"<?php echo UP_FRONT_FOLDER ?>js/uploader.js",
		"<?php echo UP_FRONT_FOLDER ?>js/inserter.js",
		"<?php echo UP_FRONT_FOLDER ?>js/quicker.js",
		"<?php echo UP_FRONT_FOLDER ?>js/imager.js",
		"<?php echo UP_FRONT_FOLDER ?>js/editer.js",
		"<?php echo UP_FRONT_FOLDER ?>js/setter.js",
		"<?php echo UP_FRONT_FOLDER ?>js/lister.js",
		"<?php echo UP_FRONT_FOLDER ?>js/menu.js",
		"<?php echo UP_FRONT_FOLDER ?>js/ballon.js",
		"<?php echo UP_FRONT_FOLDER ?>js/boxediter.js",
		"<?php echo UP_FRONT_FOLDER ?>js/jquery.Jcrop.js",
		"<?php echo UP_FRONT_FOLDER ?>js/extDate.js",
		"<?php echo UP_FRONT_FOLDER ?>js/validator.js",
		"?Login::getInitScript"
	];

	var images = [
		"<?php echo UP_FRONT_FOLDER ?>/imgs/loginbox.png"
		,"<?php echo UP_FRONT_FOLDER ?>/imgs/loading_progress.png"

		,"<?php echo UP_FRONT_FOLDER ?>/imgs/add_icon.png"

		,"<?php echo UP_FRONT_FOLDER ?>/imgs/alert_button_bg.png"
		,"<?php echo UP_FRONT_FOLDER ?>/imgs/alert_button_bg2.png"

		,"<?php echo UP_FRONT_FOLDER ?>/imgs/ballon_arrow.png"
		,"<?php echo UP_FRONT_FOLDER ?>/imgs/ballon_bottom_bg.png"
		,"<?php echo UP_FRONT_FOLDER ?>/imgs/ballon_bottom_bg2.png"
		,"<?php echo UP_FRONT_FOLDER ?>/imgs/ballon_content_bg.png"
		,"<?php echo UP_FRONT_FOLDER ?>/imgs/ballon_content_bg2.png"
		,"<?php echo UP_FRONT_FOLDER ?>/imgs/ballon_top_bg.png"
		,"<?php echo UP_FRONT_FOLDER ?>/imgs/ballon_top_bg2.png"

		,"<?php echo UP_FRONT_FOLDER ?>/imgs/box_top_bg.png"
		,"<?php echo UP_FRONT_FOLDER ?>/imgs/box_top_bg2.png"
		,"<?php echo UP_FRONT_FOLDER ?>/imgs/box_buttons.png"
		,"<?php echo UP_FRONT_FOLDER ?>/imgs/box_bottom_bg.png"
		,"<?php echo UP_FRONT_FOLDER ?>/imgs/box_bottom_bg2.png"
		,"<?php echo UP_FRONT_FOLDER ?>/imgs/box_bottom_action_bg.png"
		,"<?php echo UP_FRONT_FOLDER ?>/imgs/box_bottom_action_bg2.png"
		,"<?php echo UP_FRONT_FOLDER ?>/imgs/box_bottom_option_bg.png"
		,"<?php echo UP_FRONT_FOLDER ?>/imgs/box_bottom_option_bg2.png"

		,"<?php echo UP_FRONT_FOLDER ?>/imgs/footer_bg.png"
		,"<?php echo UP_FRONT_FOLDER ?>/imgs/footer_bg2.png"

		,"<?php echo UP_FRONT_FOLDER ?>/imgs/icons_box.png"
		,"<?php echo UP_FRONT_FOLDER ?>/imgs/icons_menu.png"
		,"<?php echo UP_FRONT_FOLDER ?>/imgs/icons_title.png"
		,"<?php echo UP_FRONT_FOLDER ?>/imgs/icons_button.png"
		,"<?php echo UP_FRONT_FOLDER ?>/imgs/icons_general.png"

		,"<?php echo UP_FRONT_FOLDER ?>/imgs/menu_bg.png"
		,"<?php echo UP_FRONT_FOLDER ?>/imgs/menu_bg2.png"
		,"<?php echo UP_FRONT_FOLDER ?>/imgs/order_arrow.png"
		,"<?php echo UP_FRONT_FOLDER ?>/imgs/submenu_arrow.png"
		,"<?php echo UP_FRONT_FOLDER ?>/imgs/menu_separate.png"
		,"<?php echo UP_FRONT_FOLDER ?>/imgs/paginate_arrow.png"
		,"<?php echo UP_FRONT_FOLDER ?>/imgs/min_max_button.png"

		,"<?php echo UP_FRONT_FOLDER ?>/imgs/popbox_top_bg.png"
		,"<?php echo UP_FRONT_FOLDER ?>/imgs/popbox_top_bg2.png"
		,"<?php echo UP_FRONT_FOLDER ?>/imgs/popbox_bottom_bg.png"
		,"<?php echo UP_FRONT_FOLDER ?>/imgs/popbox_bottom_bg2.png"

		,"<?php echo UP_FRONT_FOLDER ?>/imgs/remove_icon.png"
		,"<?php echo UP_FRONT_FOLDER ?>/imgs/red_button_bg.png"

		,"<?php echo UP_FRONT_FOLDER ?>/imgs/slider_bottom_bg.png"
		,"<?php echo UP_FRONT_FOLDER ?>/imgs/slider_bottom_bg2.png"

		,"<?php echo UP_FRONT_FOLDER ?>/imgs/top_bg.png"
		,"<?php echo UP_FRONT_FOLDER ?>/imgs/top_bg2.png"
		,"<?php echo UP_FRONT_FOLDER ?>/imgs/table_bg.png"
		,"<?php echo UP_FRONT_FOLDER ?>/imgs/undefined.png"
		,"<?php echo UP_FRONT_FOLDER ?>/imgs/submenu_arrow.png"
	];

	loadScriptsAndImages( scripts, images, Animation.preloaderProgress, complete );
}

function init()
{
	var loginHandler = function( data )
	{
		if( data.status == "true" )
		{
			userid		= data.id;
			usergroup	= data.group;
			displayname = data.name;
			Animation.hideLoginForm( loadCMS );
		}
		else
		{
			alertBox( Language.ops, Language.loginFail );
			$( "#starting input[name=\"loginsubmit\"" ).removeAttr( "disabled" ).val( Language.log );
		}
	}

	var validator = function()
	{
		var user = $( "#starting input[name=\"username\"]" );
		var pass = $( "#starting input[name=\"password\"]" );

		if( user.val() == "" )
		{
			alertBox( Language.ops, Language.typeUser );
		}
		else if( pass.val() == "" )
		{
			alertBox( Language.ops, Language.typePassword );
		}
		else
		{
			$.post( "?Login::login", { username:user.val(), password:pass.val() }, loginHandler, "json" );
			$( "#starting input[name=\"loginsubmit\"" ).attr( "disabled", true ).val( Language.logging );
			user.val( "" );
			pass.val( "" );
		}

		return false;
	}

	var func = function( data )
	{
		if( data.status == "true" )
		{
			Animation.hideWhiteScreen();
			displayname = data.name;
			usergroup = data.group;
			userid = data.id;
			loadCMS();
		}
		else
		{
			$( "#loginform" ).submit( validator );
			Animation.hideWhiteScreen();
		}
	}

	$.get( "?Login::logged", {}, func, "json" );
}

function addressChangeHandler( event )
{
	event = event || { path:SWFAddress.getValue() };

	$( "#footer" ).css( { position:"static" } );

	if( event.path == "/" )
	{
		home.show();
	}
	else
	{
		var t = event.path.split( "/" );

		if( t[2] == "list" && t.length >= 3 )
		{
			var params = { };
			for( var i = 3; i < t.length; i++ )
			{
				if( !Number( t[i] ) )
				{
					t[i] = t[i].toLowerCase();
					if( t[i] == "asc" || t[i] == "desc" ) params.direction = t[i];
					else
					{
						params[t[i]] = t[i + 1];
						i++;
					}
				}
				else params.page = t[i];
			}

			lister.show( t[1], params );
		}
		else if( t[2] == "add" && t.length == 3 )
		{
			editer.show( t[1], 0 );
		}
		else if( t[3] == "edit" && t.length == 4 )
		{
			editer.show( t[1], t[2] );
		}
		else if( t[2] == "set" && t.length == 3 )
		{
			setter.show( t[1] );
		}
	}

	quicker.addressHandler();
}

function logout()
{
	var func2 = function()
	{
		$( "body" ).html( "<div id=\"white_screen\"></div><div id=\"popbox_screen\"></div><div id=\"dark_screen\"></div><div id=\"editer_screen\"></div><div id=\"starting\"><div id=\"footer\"><div class=\"left\"></div><div class=\"center\"><div class=\"content\"></div><div class=\"right\"></div><div class=\"version\"><?php if( $frontSettings->showUpVersion == "true" ) echo $Language->version." ".UP_APP_VERSION; ?></div></div></div><div id=\"loginbox\"><h1><?php echo UP_APP_NAME ?></h1><form id=\"loginform\" method=\"post\" action=\"\"><label><span class=\"label\"><?php echo $Language->user ?>:</span><span class=\"field\"><input name=\"username\" type=\"text\" /></span></label><label><span class=\"label\"><?php echo $Language->password ?>:</span><span class=\"field\"><input name=\"password\" type=\"password\" /></span></label><label id=\"checking\"><input type=\"checkbox\" /> <?php echo $Language->rememberMe ?></label><a id=\"login_button\" href=\"javascript:;\" onclick=\"$('#loginform').submit()\"><span></span><?php echo $Language->login ?></a><input type=\"submit\" style=\"display:none\" /></form><a id=\"lost_password\" href=\"#\"><?php echo $Language->lostYourPassword ?></a><div id=\"loading\"><h1><?php echo $Language->loading ?></h1><div id=\"progress\"><div id=\"loaded\"></div></div></div></div></div>" );
		init();
	}

	var func = function( data )
	{
		Animation.hideWhiteScreen( func2 );
	}

	$.get( "?Login::logout", {}, func, "json" );
}

function startCMS()
{
	<?php
	$time = date("Hi");

	if( $time >= 1800 )
		$time = $Language->evening;
	else if( $time >= 1200 )
		$time = $Language->afternoon;
	else if( $time >= 600 )
		$time = $Language->morning;
	else
		$time = $Language->aftermoon;
	?>;

	$( "body" ).append( "<div id=\"editer_container\"></div><div id=\"popbox_container\"></div><div id=\"wrap\"><div id=\"top\"><div class=\"left\"></div><div class=\"center\"><div class=\"content\"><a href=\"<?php echo UP_APP_URL ?>\" target=\"_blank\"><h1><?php echo UP_APP_NAME ?></h1></a><span><?php echo $time ?>, <a href=\"#/system_users/" + userid + "/edit\">" + displayname + "</a>  <span class=\"general_icon icon_logout\"></span><a href=\"javascript:logout();\"><?php echo $Language->logout ?></a></span></div><div class=\"right\"></div></div></div><div id=\"container\"><div id=\"menu\"><div class=\"background\"><div class=\"top\"></div><div class=\"middle\"></div><div class=\"bottom\"></div></div><div class=\"content\"><span class=\"home_btn\" ><span class=\"icon\"></span><a href=\"#\"><?php echo $Language->home ?></a></span></div></div><div id=\"main_content\"></div></div><br style=\"clear:both\" /></div>" );
	$( "body" ).append( $( "#footer" ) );
	Animation.showWhiteScreen();
	Animation.hideWhiteScreen();
	$( "#starting" ).remove();

	startMenu();

	home = new Home();
	setter = new Setter();
	lister = new Lister();
	editer = new Editer();
	quicker = new Quicker();
	callendar = new Callendar();
	colorPicker = new ColorPicker();
	uploader = new Uploader();
	inserter = new Inserter();
	imager = new Imager();

	Animation.hideWhiteScreen();

	$( ".table_list tbody tr" ).mouseover( lister.tableOverHandler );
	$( ".table_list tbody tr" ).mouseout( lister.tableOutHandler );

	$( "#popbox_screen" ).css( "opacity", 0 );
	hideLoadingScreen();

	$( "#main_content" ).css( "min-height", ( $( "#menu" ).height() + 100 ) + "px");
	SWFAddress.addEventListener( SWFAddressEvent.CHANGE, addressChangeHandler );
	addressChangeHandler();
}

$( document ).ready( init );
</script>
</body>
</html>
