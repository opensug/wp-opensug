<?php
/*
	Plugin Name:	openSug
	Version:		1.0.0
	Plugin URL:		https://github.com/opensug/wp-opensug/
	Description:	It provides suggestions for visitors to search, making users' search convenient and fast.
	Author:			openSug
	Author URI:		https://www.opensug.eu.org/
	Text Domain:	opensug
	Domain Path:	/languages
*/
header( "content-type:text/html;charset=utf-8" );
define( "openSug_SYMBOL",	"opensug_cfg" );
wp_enqueue_script( "jquery" );

if( function_exists("is_admin") ) {
	define( "openSug_DIRNAME",	plugin_basename( plugin_dir_path( __FILE__ ) ) );
	define( "openSug_SET_PAGE",	admin_url( "options-general.php?page=". openSug_DIRNAME ."%2findex.php" ) );
	define( "openSug_AJAX",		preg_replace( "/^(http|https):\/\//i", "//", admin_url("admin-ajax.php") ) );

	if( !is_admin() ) wp_enqueue_script( openSug_DIRNAME, "https://opensug.github.io/js/opensug.js", array(), false, true );

	// Languages
	function openSug_i18n() {
		load_plugin_textdomain( openSug_DIRNAME, false , dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}

	// Config page
	function openSug_config() {
		require plugin_dir_path( __FILE__ ) ."setting.php";
	}

	function setup_link( $links ) {
  		$settings_link = sprintf('<a href="%s">%s</a>', openSug_SET_PAGE, __("Settings", openSug_DIRNAME));
  		array_unshift( $links, $settings_link );
  		return $links;
	}

	// Menu
	function openSug_menu() {
		add_options_page( __("openSug.js config", openSug_DIRNAME), __("openSug.js", openSug_DIRNAME), "administrator",  __FILE__, "openSug_config", false, 9e9 );
		add_filter( "plugin_action_links_". plugin_basename( plugin_dir_path( __FILE__ ) . "index.php"), "setup_link" );
	}

	// config
	function openSug_cfg() {
		$cfg = array(
			"id"			=> "wp-block-search__input-1",
			"source"		=> openSug_AJAX . "?action=openSug&kw=",
			"sugSubmit"		=> "1",
			"padding"		=> "",
			"XOffset"		=> "",
			"YOffset"		=> "",
			"radius"		=> "4px",
			"shadow"		=> "0 16px 10px #00000080",
			"fontColor"		=> "#ff0000",
			"bgcolor"		=> "#ffffff",
			"bgcolorHI"		=> "#4d90fe",
			"width"			=> "",
			"fontSize"		=> "16",
			"callback"		=> "",
			"borderColor"	=> "#999999",
			"fontColorHI"	=> "#ffffff",
			"fontFamily"	=> ""
		);

		if( get_option( openSug_SYMBOL ) ) {
			$cfg = unserialize( get_option( openSug_SYMBOL ) );
		}

		if( count( $cfg ) === 0 ) {
			delete_option( openSug_SYMBOL );
		}

		if( isset($cfg["fontSize"]) && strlen($cfg["fontSize"]) > 0 ) {
			$cfg["fontSize"] .= "px";
		}

		$cfg["sugSubmit"] = $cfg["sugSubmit"] === "0" ? "false" : "true";

		if( isset($cfg["id"]) && strlen($cfg["id"]) > 0 ) {
			echo "<script type='text/javascript' language='javascript' id='config_sug'>'use strict';(function(){\r\n    var ipt = document['getElementById']('{$cfg["id"]}');\r\n	if( ipt != null && (\r\n		(ipt['getAttribute']('type') || '')['toLocaleLowerCase']() === 'search' || \r\n		(ipt['getAttribute']('type') || '')['toLocaleLowerCase']() === 'text') && \r\n	   	'function' === typeof( window['openSug'] )\r\n	) window['openSug']( '{$cfg["id"]}', {\r\n		source		: '{$cfg["source"]}',\r\n		sugSubmit	: {$cfg["sugSubmit"]},\r\n		padding		: '{$cfg["padding"]}',\r\n		XOffset		: '{$cfg["XOffset"]}',\r\n		YOffset		: '{$cfg["YOffset"]}',\r\n		radius		: '{$cfg["radius"]}',\r\n		shadow		: '{$cfg["shadow"]}',\r\n		fontColor	: '{$cfg["fontColor"]}',\r\n		fontColorHI	: '{$cfg["fontColorHI"]}',\r\n		bgcolor		: '{$cfg["bgcolor"]}',\r\n		bgcolorHI	: '{$cfg["bgcolorHI"]}',\r\n		borderColor	: '{$cfg["borderColor"]}',\r\n		width		: '{$cfg["width"]}',\r\n		fontSize	: '{$cfg["fontSize"]}',\r\n		fontFamily	: '{$cfg["fontFamily"]}'\r\n	},function(cb){\r\n			{$cfg["callback"]}\r\n	});\r\n}(this));</script>";
		}
	}

	function openSug_suggestion( $var = "" ) {
		header("Expires:-1");
		header("Pramga: no-cache");
		header("Content-type: text/javascript");
		header("Cache-Control: no-cache, must-revalidate, no-store");
		header("Last-Modified: ". gmdate("D, d M Y 01:01:01", (time()-86400)) ." GMT");
		
		global $wpdb;
		$out		= "";
		$keys		= array();
		$keyword	= sanitize_text_field(isset ($_GET["kw"]) && strlen ($_GET["kw"]) > 0 ? addslashes($_GET["kw"]) : "");
		$callback	= sanitize_text_field(isset( $_GET["cb"] ) && strlen( $_GET["cb"] ) > 0 ? $_GET["cb"] : "");

		if (strlen( $keyword ) > 0) {
			$res = $wpdb->get_results( "SELECT DISTINCT post_title, post_content FROM {$wpdb->posts} WHERE post_title LIKE '%{$keyword}%' OR post_content LIKE '%{$keyword}%'" );
			$res = json_decode( json_encode( $res ), true );
			$len = count( $res );

			if ( $len > 0 ) {
				foreach($res as $v){
					$contents = "{$v["title"]}{$v["post_content"]}";
					$contents = strip_tags( $contents );
					$contents = preg_replace( "/\s/", "", $contents );

					if ( strlen( $contents ) > 0 ) {
						preg_match_all( "/(\w{0,5}\W{0,5}){$keyword}(\w{0,5}\W{0,5})/uis", $contents, $matches );
						$keys = array_merge( $keys, $matches[0] );
						$keys = array_unique( $keys );
					}
				}

				for ( $i = 0, $len = count( $keys ); $i < ( $len > 10 ? 10 : $len ); $i++ ) {
					if ( strlen( $keys[$i] ) > 0 ){
						$key	= addslashes($keys[$i]);
						$out	.= "\"{$key}\",";
					}
				}

				$out = rtrim( $out, "," );
			}
		}
		if ( preg_match( "/^BaiduSuggestion\.res\.__\d+$/i", $callback ) ) echo wp_kses_post("{$callback}({s:[{$out}]});");
		exit;
	}

	// Unionstall
	function openSug_uninstall(){
		delete_option( openSug_SYMBOL );
	}

	// Activate
	function openSug_activate() {
		add_option( "openSug_redirect", true );
	}

	// Jump to the settings page when the plugin is enabled.
	function openSug_redirect() {
		if ( get_option( "openSug_redirect", false ) ) {
			delete_option( "openSug_redirect" );
			wp_safe_redirect( esc_url_raw( openSug_SET_PAGE ), 301 );
			die;
		}
	}
	
	
	
	register_activation_hook( __FILE__, "openSug_activate" );
	register_deactivation_hook( __FILE__, "openSug_uninstall" );
	add_action( "admin_init", "openSug_redirect" );
	add_action( "wp_ajax_nopriv_openSug", "openSug_suggestion" );
	add_action( "wp_ajax_openSug", "openSug_suggestion" );
	add_action( "plugins_loaded", "openSug_i18n" );
	add_action( "wp_footer", "openSug_cfg", 9e9 );
	add_action( "admin_menu","openSug_menu" );
}