<?php
function_exists( "is_admin" ) || header( "Status: 404 Not Found" ) & exit;
function openSug_value( $data = array(), $var = "", $val = "" ){
	return isset( $data[$var] ) && strlen( $data[$var] ) > 0 ? $data[$var] : $val;
}

if( $_SERVER["REQUEST_METHOD"] === "POST" ) {
	update_option( openSug_SYMBOL , serialize( array(
		"id"			=> sanitize_text_field(openSug_value( $_POST, "ipt"		)),
		"source"		=> sanitize_text_field(openSug_value( $_POST, "source"		)),
		"sugSubmit"		=> sanitize_text_field(openSug_value( $_POST, "action"		)) == 0 ? "0" : "1",
		"padding"		=> sanitize_text_field(openSug_value( $_POST, "padding"	)),
		"XOffset"		=> sanitize_text_field(openSug_value( $_POST, "XOffset"	)),
		"YOffset"		=> sanitize_text_field(openSug_value( $_POST, "YOffset"	)),
		"radius"		=> sanitize_text_field(openSug_value( $_POST, "radius"		)),
		"shadow"		=> sanitize_text_field(openSug_value( $_POST, "shadow"		)),
		"width"			=> sanitize_text_field(openSug_value( $_POST, "width"		)),
		"callback"		=> sanitize_text_field(openSug_value( $_POST, "cb"			)),
		"fontColor"		=> sanitize_text_field(openSug_value( $_POST, "fontColor"	)),
		"bgcolor"		=> sanitize_text_field(openSug_value( $_POST, "bgcolor"	)),
		"bgcolorHI"		=> sanitize_text_field(openSug_value( $_POST, "bgcolorHI"	)),
		"fontSize"		=> sanitize_text_field(openSug_value( $_POST, "fontSize"	)),
		"borderColor"	=> sanitize_text_field(openSug_value( $_POST, "borderColor")),
		"fontColorHI"	=> sanitize_text_field(openSug_value( $_POST, "fontColorHI")),
		"fontFamily"	=> sanitize_text_field(openSug_value( $_POST, "fontFamily"	))
	) ) );
	echo "<div class=\"updated settings-error notice is-dismissible\"><p><strong>". __( "Saved.", "opensug" ) ."</strong></p></div>";
}

$cfg = array();
if( get_option( openSug_SYMBOL ) ) $cfg = unserialize( get_option( openSug_SYMBOL ) );
?>
<div class="wrap">
	<h1>openSug.js <?php esc_html_e( "Settings", "opensug" );?></h1>
	<p>
		<?php esc_html_e( "Simply reference a section of JS to get a search box with “search box prompts” to make your search easier!", "opensug" );?><br />
		<?php esc_html_e( "Default use libs sources", "opensug" );?>: <a target="_blank" href="https://opensug.github.io/js/opensug.js">https://github.com/</a>, 
		<?php esc_html_e( "Porject home", "opensug" );?>: <a href="https://www.opensug.eu.org/" target="_blank">https://www.opensug.eu.org/</a>
	</p>
	<form action="" method="post" id="ConfigFormSimilar">
	<table class="form-table">
	<tbody>
		<tr>
			<th scope="row">
				<label for="ipt"><?php esc_html_e( "Bind id with input", "opensug" );?></label>
			</th><td>
				<input type="text" class="regular-text" autocomplete="off" spellcheck="false" x-webkit-speech="false" id="ipt" name="ipt" value="<?php esc_html_e(openSug_value($cfg, "id"));?>" required="required" placeholder="wp-block-search__input-1" />
			</td>
		</tr>
		<tr>
			<th scope="row">
				<label for="source"><?php esc_html_e( "Result source", "opensug" );?></label>
			</th><td>
				<?php 
				$source = openSug_value($cfg, "source", "");
				?>
				<select id="source" name="source" style="width:120px;color:blue;">
					<option <?php echo $source === "" || preg_match("/^(https|http):\/\//", $source) ? 'selected="selected" ' : "";?>value="<?php echo admin_url("admin-ajax.php?action=openSug&kw=");?>"><?php esc_html_e( "* On self site", "opensug" );?></option>
					<option <?php echo $source === "baidu"	? 'selected="selected" ' : "";?>value="baidu"><?php		esc_html_e( "Baidu.com",	"opensug" );?></option>
					<option <?php echo $source === "google"	? 'selected="selected" ' : "";?>value="google"><?php 	esc_html_e( "Google.com",	"opensug" );?></option>
					<option <?php echo $source === "haoso"	? 'selected="selected" ' : "";?>value="haoso"><?php		esc_html_e( "So.com",		"opensug" );?></option>
					<option <?php echo $source === "kugou"	? 'selected="selected" ' : "";?>value="kugou"><?php		esc_html_e( "Kugou.com",	"opensug" );?></option>
					<option <?php echo $source === "yahoo"	? 'selected="selected" ' : "";?>value="yahoo"><?php		esc_html_e( "Yahoo.com",	"opensug" );?></option>
					<option <?php echo $source === "yandex"	? 'selected="selected" ' : "";?>value="yandex"><?php	esc_html_e( "Yandex.ru",	"opensug" );?></option>
					<option <?php echo $source === "youku"	? 'selected="selected" ' : "";?>value="youku"><?php		esc_html_e( "Youku.com",	"opensug" );?></option>
					<option <?php echo $source === "taobao"	? 'selected="selected" ' : "";?>value="taobao"><?php	esc_html_e( "Taobao.com",	"opensug" );?></option>
					<option <?php echo $source === "attayo"	? 'selected="selected" ' : "";?>value="attayo"><?php	esc_html_e( "Attayo.jp",	"opensug" );?></option>
					<option <?php echo $source === "mgtv"	? 'selected="selected" ' : "";?>value="mgtv"><?php		esc_html_e( "Mgtv.com",		"opensug" );?></option>
					<option <?php echo $source === "sm"		? 'selected="selected" ' : "";?>value="sm"><?php		esc_html_e( "Sm.cn",		"opensug" );?></option>
					<option <?php echo $source === "weibo"	? 'selected="selected" ' : "";?>value="weibo"><?php		esc_html_e( "Weibo.com",	"opensug" );?></option>
					<option <?php echo $source === "rambler"? 'selected="selected" ' : "";?>value="rambler"><?php	esc_html_e( "Rambler.ru",	"opensug" );?></option>
					<!--option <?php echo $source === "book"	? 'selected="selected" ' : "";?>value="book"><?php		esc_html_e( "Zongheng.com",	"opensug" );?></option-->
					<option <?php echo $source === "soft"	? 'selected="selected" ' : "";?>value="soft"><?php		esc_html_e( "Software",		"opensug" );?></option>
					<option <?php echo $source === "naver"	? 'selected="selected" ' : "";?>value="naver"><?php 	esc_html_e( "Naver.com",	"opensug" );?></option>
					<option <?php echo $source === "car"	? 'selected="selected" ' : "";?>value="car"><?php		esc_html_e( "Car[sina]",	"opensug" );?></option>
					<option <?php echo $source === "car2"	? 'selected="selected" ' : "";?>value="car2"><?php		esc_html_e( "car[netease]",	"opensug" );?></option>
					<option <?php echo $source === "qunar"	? 'selected="selected" ' : "";?>value="qunar"><?php		esc_html_e( "Qunar.com",	"opensug" );?></option>
					<option <?php echo $source === "lagou"	? 'selected="selected" ' : "";?>value="lagou"><?php		esc_html_e( "Lagou.com",	"opensug" );?></option>
				</select>
			</td>
		</tr>
		<tr>
			<th scope="row">
				<label for="action"><?php esc_html_e( "sugSubmit", openSug_DIRNAME );?></label>
			</th><td>
				<select name="action" id="action">
					<option <?php if( openSug_value( $cfg, "sugSubmit", "1" ) != "0") echo 'selected="selected"';?> value="1"><?php esc_html_e( "Selected submission(default)",	"opensug" );?></option>
					<option <?php if( openSug_value( $cfg, "sugSubmit", "1" ) == "0") echo 'selected="selected"';?> value="0"><?php esc_html_e( "Manual submission",			"opensug" );?></option>
				</select>
			</td>
		</tr>
		<tr>
			<th scope="row">
				<label for="XOffset"><?php esc_html_e( "X-Offset", "opensug" );?></label>
			</th><td>
				<input type="number" class="regular-text" autocomplete="off" spellcheck="false" x-webkit-speech="false" id="XOffset" name="XOffset" value="<?php esc_html_e(openSug_value($cfg, "XOffset"));?>" placeholder="-10" />
			</td>
		</tr>
		<tr>
			<th scope="row">
				<label for="YOffset"><?php esc_html_e( "Y-Offset", "opensug" );?></label>
			</th><td>
				<input type="number" class="regular-text" autocomplete="off" spellcheck="false" x-webkit-speech="false" id="YOffset" name="YOffset" value="<?php esc_html_e(openSug_value($cfg, "YOffset"));?>" placeholder="-15" />
			</td>
		</tr>
		<tr>
			<th scope="row">
				<label for="width"><?php esc_html_e( "Width", "opensug" );?></label>
			</th><td>
				<input type="number" class="regular-text" autocomplete="off" spellcheck="false" x-webkit-speech="false" id="width" name="width" value="<?php esc_html_e(openSug_value($cfg, "width"));?>" placeholder="300" />
			</td>
		</tr>
		<tr>
			<th scope="row">
				<label for="fontColor"><?php esc_html_e( "font Color", "opensug" );?></label>
			</th><td>
				<input type="color" class="regular-text" autocomplete="off" spellcheck="false" x-webkit-speech="false" id="fontColor" name="fontColor" value="" />
			</td>
		</tr>
		<tr>
			<th scope="row">
				<label for="fontColorHI"><?php esc_html_e( "font Color HI", "opensug" );?></label>
			</th><td>
				<input type="color" class="regular-text" autocomplete="off" spellcheck="false" x-webkit-speech="false" id="fontColorHI" name="fontColorHI" value="<?php esc_html_e(openSug_value($cfg, "fontColorHI", "#ffffff"));?>" />
			</td>
		</tr>
		<tr>
			<th scope="row">
				<label for="bgcolor"><?php esc_html_e( "background Color", "opensug" );?></label>
			</th><td>
				<input type="color" class="regular-text" autocomplete="off" spellcheck="false" x-webkit-speech="false" id="bgcolor" name="bgcolor" value="<?php esc_html_e(openSug_value($cfg, "bgcolor", "#ffffff"));?>" />
			</td>
		</tr>
		<tr>
			<th scope="row">
				<label for="bgcolorHI"><?php esc_html_e( "background Color HI", "opensug" );?></label>
			</th><td>
				<input type="color" class="regular-text" autocomplete="off" spellcheck="false" x-webkit-speech="false" id="bgcolorHI" name="bgcolorHI" value="<?php esc_html_e(openSug_value($cfg, "bgcolorHI", "#4d90fe"));?>" />
			</td>
		</tr>
		<tr>
			<th scope="row">
				<label for="fontSize"><?php esc_html_e( "font Size", "opensug" );?></label>
			</th><td>
				<input type="number" class="regular-text" autocomplete="off" spellcheck="false" x-webkit-speech="false" id="fontSize" name="fontSize" value="<?php esc_html_e(openSug_value($cfg, "fontSize", "16"));?>" placeholder="16" />
			</td>
		</tr>
		<tr>
			<th scope="row">
				<label for="fontFamily"><?php esc_html_e( "font Family", "opensug" );?></label>
			</th><td>
				<input type="text" class="regular-text" autocomplete="off" spellcheck="false" x-webkit-speech="false" id="fontFamily" name="fontFamily" value="<?php esc_html_e(openSug_value($cfg, "fontFamily", "cursive"));?>" placeholder="verdana" />
			</td>
		</tr>
		<tr>
			<th scope="row">
				<label for="borderColor"><?php esc_html_e( "border Color", "opensug" );?></label>
			</th><td>
				<input type="color" class="regular-text" autocomplete="off" spellcheck="false" x-webkit-speech="false" id="borderColor" name="borderColor" value="<?php esc_html_e(openSug_value($cfg, "borderColor", "#999999"));?>" />
			</td>
		</tr>
		<tr>
			<th scope="row">
				<label for="padding"><?php esc_html_e( "Padding", "opensug" );?></label>
			</th><td>
				<input type="text" class="regular-text" autocomplete="off" spellcheck="false" x-webkit-speech="false" id="padding" name="padding" value="<?php esc_html_e(openSug_value($cfg, "padding"));?>" placeholder="0px" />
			</td>
		</tr>
		<tr>
			<th scope="row">
				<label for="radius"><?php esc_html_e( "Radius", "opensug" );?></label>
			</th><td>
				<input type="text" class="regular-text" autocomplete="off" spellcheck="false" x-webkit-speech="false" id="radius" name="radius" value="<?php esc_html_e(openSug_value($cfg, "radius", "4px"));?>" placeholder="4px" />
			</td>
		</tr>
		<tr>
			<th scope="row">
				<label for="shadow"><?php esc_html_e( "Shadow", "opensug" );?></label>
			</th><td>
				<input type="text" class="regular-text" autocomplete="off" spellcheck="false" x-webkit-speech="false" id="shadow" name="shadow" value="<?php esc_html_e(openSug_value($cfg, "shadow", "0 16px 10px #00000080"));?>" placeholder="0 16px 10px #00000080" />
			</td>
		</tr>
		<tr>
			<th scope="row">
				<label for="cb"><?php esc_html_e( "Callback", "opensug" );?></label>
			</th><td>
				<textarea class="regular-text" spellcheck="false" x-webkit-speech="false" id="cb" name="cb" placeholder="alert(cb);" /><?php esc_html_e(openSug_value( $cfg, "callback", "/*console.log(cb);*/"));?></textarea>
			</td>
		</tr>
	</tbody>
	</table>
	<p class="submit"><input type="submit" class="button button-primary" name="submit" value="<?php esc_html_e( "Save", "opensug" );?>" /></p>
	</form>
	<script type="text/javascript">
	!jQuery||jQuery("input:text")["click"](function(){
		jQuery(this).select();
	});
	</script>
</div>