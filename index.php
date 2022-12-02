<?php
/*
	Plugin Name:	openSug
	Plugin URL:		https://github.com/opensug/wp-opensug/
	Description:	It provides suggestions for visitors to search, making users' search convenient and fast.
	Author:			openSug
	Version:		1.0.0
	Author URL:		https://www.opensug.eu.org/
*/
header( 'content-type:text/html;charset=utf-8' );
define( 'openSug_SYMBOL',	'opensug_cfg' );
wp_enqueue_script('jquery');
if( function_exists('is_admin') ) {
	define( 'openSug_GH',		'https://opensug.github.io/js/opensug.js' );
	define( 'openSug_LANGUAGES', dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	define( 'openSug_SUGURL',	admin_url('admin-ajax.php') .'?action=openSug&kw=' );

	if( !is_admin() ) wp_enqueue_script( 'opensug', openSug_GH, array(), false, true );

	// Languages
	function openSug_i18n() {
		load_plugin_textdomain( 'opensug', false , openSug_LANGUAGES );
	}

	// Config page
	function openSug_config() {
		require plugin_dir_path( __FILE__ ) .'setting.php';
	}

	// Menu
	function openSug_menu() {
		add_options_page( __('openSug.js config', 'opensug'), __('openSug.js', 'opensug'), 'administrator',  __FILE__, 'openSug_config', false, 9e9 );
	}

	// config
	function openSug_cfg() {
		$cfg = array(
			'id'			=> 'wp-block-search__input-1',
			'source'		=> openSug_SUGURL,
			'sugSubmit'		=> '1',
			'padding'		=> '',
			'XOffset'		=> '',
			'YOffset'		=> '',
			'radius'		=> '4px',
			'shadow'		=> '0 16px 10px rgb(0 0 0 / 50%)',
			'fontColor'		=> '#f00',
			'bgcolor'		=> '#fff',
			'bgcolorHI'		=> '#4d90fe',
			'width'			=> '',
			'fontSize'		=> '16',
			'callback'		=> '',
			'borderColor'	=> '#999999',
			'fontColorHI'	=> '#ffffff',
			'fontFamily'	=> ''
		);

		if( get_option( openSug_SYMBOL ) ) {
			$cfg = unserialize( get_option( openSug_SYMBOL ) );
		}

		if( count($cfg) === 0 ) {
			delete_option( openSug_SYMBOL );
		}

		if( isset($cfg['fontSize']) && strlen($cfg['fontSize']) > 0 ) {
			$cfg['fontSize'] .= 'px';
		}

		$cfg['sugSubmit'] = $cfg['sugSubmit'] === '0' ? 'false' : 'true';

		if( isset($cfg['id']) && strlen($cfg['id']) > 0 ) {
			echo "<script type='text/javascript' language='javascript'>'use strict';/*<![CDATA[*/var \$osId=document.getElementById('{$cfg['id']}');if(\$osId!=null&&((\$osId.getAttribute('type')||\"\").toLocaleLowerCase()==='search'||(\$osId.getAttribute('type')||\"\").toLocaleLowerCase()==='text')&&\"function\" === typeof(window.openSug))window.openSug('{$cfg['id']}',{'source':'{$cfg['source']}','sugSubmit':{$cfg['sugSubmit']},'padding':'{$cfg['padding']}','XOffset':'{$cfg['XOffset']}','YOffset':'{$cfg['YOffset']}','radius':'{$cfg['radius']}','shadow':'{$cfg['shadow']}','fontColor':'{$cfg['fontColor']}','fontColorHI':'{$cfg['fontColorHI']}','bgcolor':'{$cfg['bgcolor']}','bgcolorHI':'{$cfg['bgcolorHI']}','borderColor':'{$cfg['borderColor']}','width':'{$cfg['width']}','fontSize':'{$cfg['fontSize']}','fontFamily':'{$cfg['fontFamily']}'},function(cb){{$cfg['callback']}});/*]]>*/</script>";
		}
	}
	
	function openSug_suggestion($var = ''){
		header('Expires:-1');
		header('Pramga: no-cache');
		header('Content-type: text/javascript');
		header('Cache-Control: no-cache, must-revalidate, no-store');
		header('Last-Modified: '. gmdate('D, d M Y 01:01:01', (time()-86400)) .' GMT');
		
		global $wpdb;
		$out = '';
		$tmp = array();
		$keyword	= sanitize_text_field(isset ($_GET['kw']) && strlen ($_GET['kw']) > 0 ? addslashes($_GET['kw']) : '');
		$callback	= sanitize_text_field(isset( $_GET['cb'] ) && strlen( $_GET['cb'] ) > 0 ? $_GET['cb'] : '');

		if (strlen( $keyword ) > 0) {
			$res = $wpdb->get_results( "SELECT DISTINCT post_title, post_content FROM {$wpdb->posts} WHERE post_title LIKE '%{$keyword}%' OR post_content LIKE '%{$keyword}%'" );
			$res = json_decode( json_encode( $res ), true );
			$len = count( $res );

			if ( $len > 0 ) {
				foreach($res as $v){
					$contents = "{$v['title']}{$v['post_content']}";
					$contents = strip_tags( $contents );
					$contents = preg_replace( '/\s/', '', $contents );

					if ( strlen( $contents ) > 0 ) {
						preg_match_all( "/(\w{0,3}\W{0,3}){$keyword}(\w{0,3}\W{0,3})/is", $contents, $matches );
						$tmp = array_merge( $tmp, $matches[0] );
						$tmp = array_unique( $tmp );
					}
				}

				for ( $i = 0, $len = count( $tmp ); $i < ( $len > 10 ? 10 : $len ); $i++ ) {
					if ( strlen( $tmp[$i] ) > 0 ) $out .= '"'. $tmp[$i] .'",';
				}

				$out = rtrim( $out, ',' );
			}
		}
		if ( preg_match( '/^BaiduSuggestion\.res\.__\d+$/i', $callback ) ) echo wp_kses_post("{$callback}({s:[{$out}]});");
		exit;
	}

	// Unionstall
	function openSug_uninstall(){
		delete_option( openSug_SYMBOL );
	}

	// Activate
	function openSug_activate() {
		add_option( 'openSug_redirect', true );
	}

	// Jump to the settings page when the plugin is enabled.
	function openSug_redirect() {
		if ( get_option( 'openSug_redirect', false ) ) {
			delete_option( 'openSug_redirect' );
			wp_redirect( admin_url( 'options-general.php?page=openSug%2findex.php' ) );
		}
	}

	register_activation_hook( __FILE__, 'openSug_activate' );
	register_deactivation_hook( __FILE__, 'openSug_uninstall' );
	add_action( 'admin_init', 'openSug_redirect' );
	add_action( 'wp_ajax_nopriv_openSug', 'openSug_suggestion' );
	add_action( 'wp_ajax_openSug', 'openSug_suggestion' );
	add_action( 'plugins_loaded', 'openSug_i18n' );
	add_action( 'wp_footer', "openSug_cfg", 9e9 );
	add_action( 'admin_menu','openSug_menu' );
}