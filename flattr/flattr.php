<?php
/*
Plugin Name: Flattr
Plugin URI: http://api.flattr.com/plugins/
Description: Give your readers the opportunity to Flattr your effort
Version: 0.2
Author: Flattr
Author URI: http://flattr.com/
*/


// Defines

define(FLATTR_WP_VERSION, '0.2');
define(FLATTR_WP_SCRIPT,  'http://flattr.com/api/flattr.js');


// Init

if (is_admin())
{
	add_action('admin_menu', 'flattr_admin_menu');
	add_action('admin_init', 'flattr_admin_init' );
}


// Admin methods

function flattr_admin_menu()
{
	add_options_page('Flattr', 'Flattr', 8, basename(__FILE__), 'flattr_settings_page');
}

function flattr_admin_init()
{
	register_setting('flattr-settings-group', 'flattr_uid');
	register_setting('flattr-settings-group', 'flattr_cat');
}

function flattr_settings_page()
{
	?>
	<div class="wrap">
		<h2>Flattr Settings</h2>
		<form method="post" action="options.php">
			<?php settings_fields( 'flattr-settings-group' ); ?>
			<table class="form-table">
				<tr valign="top">
					<th scope="row">Your Flattr user ID</th>
					<td><input name="flattr-uid" type="text" value="<?php echo(get_option('flattr_uid')); ?>" /></td>
				</tr>
				<tr valign="top">
					<th scope="row">The category for your posts</th>
					<td><input type="text" name="flattr-cat" value="<?php echo get_option('flattr_cat'); ?>" /> (choose between text, images, audio, video, software, rest)</td>
				</tr>
			</table>
			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			</p>
		</form>
	</div>
	<?php
}

function flattr_safe_output($expression)
{
	return trim(str_replace("\n", ' ', htmlspecialchars(addslashes($expression))));
}


// User methods

function the_flattr_permalink()
{
	echo(get_the_flattr_permalink());
}

function get_the_flattr_permalink()
{
	$output = "<script type=\"text/javascript\">\n";
	$output .= "var flattr_wp_ver = '" . FLATTR_WP_VERSION . "';\n";
	$output .= "var flattr_uid = '" . flattr_safe_output(get_option('flattr-uid'))                  . "';\n";
	$output .= "var flattr_cat = '" . flattr_safe_output(get_option('flattr-cat'))                  . "';\n";
	$output .= "var flattr_tle = '" . flattr_safe_output(get_the_title())                           . "';\n";
	$output .= "var flattr_dsc = '" . flattr_safe_output(get_the_excerpt())                         . "';\n";
	$output .= "var flattr_tag = '" . flattr_safe_output(strip_tags(get_the_tag_list('', ',', ''))) . "';\n";
	$output .= "var flattr_url = '" . flattr_safe_output(get_permalink())                           . "';\n";
	$output .= "</script>";

	return $output . '<script src="' . FLATTR_WP_SCRIPT . '" type="text/javascript"></script>';
}


// Deprecated methods

function FlattrPerma()
{
	$message = 'Deprecated function FlattrPerma() called, use the_flattr_permalink() instead.';
	trigger_error($message, E_USER_NOTICE);
	echo('<!-- ' . $message . ' -->');
}

function FlattrDyn()
{
	$message = 'Deprecated function FlattrDyn() called.';
	trigger_error($message, E_USER_NOTICE);
	echo('<!-- ' . $message . ' -->');
}