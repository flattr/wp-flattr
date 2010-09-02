<?php
/*
Plugin Name: Flattr
Plugin URI: http://api.flattr.com/plugins/
Description: Give your readers the opportunity to Flattr your effort
Version: 0.3
Author: Flattr
Author URI: http://flattr.com/
*/

// Defines

define(FLATTR_WP_VERSION, '0.3');
define(FLATTR_WP_SCRIPT,  'http://flattr.com/api/flattr.js');


// Init

if (is_admin())
{
	add_action('admin_menu', 'flattr_admin_menu');
	add_action('admin_init', 'flattr_admin_init' );
}

if (get_option('flattr_aut', 'on') == 'on')
{
	add_filter('get_the_excerpt', create_function('$content', 'remove_filter("the_content", "flattr_the_content"); return $content;'), 9);
	add_filter('get_the_excerpt', create_function('$content', 'add_filter("the_content", "flattr_the_content"); return $content;'), 11);
	add_filter('the_content', 'flattr_the_content'); 
}


// Admin methods

function flattr_admin_init()
{
	register_setting('flattr-settings-group', 'flattr_uid');
	register_setting('flattr-settings-group', 'flattr_cat');
	register_setting('flattr-settings-group', 'flattr_aut');
}

function flattr_admin_menu()
{
	add_options_page('Flattr', 'Flattr', 8, basename(__FILE__), 'flattr_settings_page');
}

function flattr_permalink($userID, $category, $title, $description, $tags, $url)
{
	$output = "<script type=\"text/javascript\">\n";
	$output .= "var flattr_wp_ver = '" . FLATTR_WP_VERSION . "';\n";
	$output .= "var flattr_uid = '" . flattr_safe_output($userID)      . "';\n";
	$output .= "var flattr_cat = '" . flattr_safe_output($category)    . "';\n";
	$output .= "var flattr_tle = '" . flattr_safe_output($title)       . "';\n";
	$output .= "var flattr_dsc = '" . flattr_safe_output($description) . "';\n";
	$output .= "var flattr_tag = '" . flattr_safe_output($tags)        . "';\n";
	$output .= "var flattr_url = '" . flattr_safe_output($url)         . "';\n";
	$output .= "</script>";

	return $output . '<script src="' . FLATTR_WP_SCRIPT . '" type="text/javascript"></script>';
}

function flattr_safe_output($expression)
{
	return trim(preg_replace('~\r\n|\r|\n~', ' ', addslashes($expression)));
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
					<td><input name="flattr_uid" type="text" value="<?php echo(get_option('flattr_uid')); ?>" /></td>
				</tr>
				<tr valign="top">
					<th scope="row">Default category for your posts</th>
					<td><input type="text" name="flattr_cat" value="<?php echo(get_option('flattr_cat')); ?>" /><br />(choose between text, images, audio, video, software, rest)</td>
				</tr>
				<tr valign="top">
					<th scope="row">Insert Flattr automagically</th>
					<td><input <?php if (get_option('flattr_aut', 'on') == 'on') { echo(' checked="checked"'); } ?> type="checkbox" name="flattr_aut" value="on" /><br />(uncheck this if you rather use <code>&lt;?php the_flattr_permalink() ?&gt;</code>)</td>
				</tr>
			</table>
			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			</p>
		</form>
	</div>
	<?php
}

function flattr_the_content($content)
{
	$content .= get_the_flattr_permalink();
	return $content;
}


// User methods

function get_the_flattr_permalink()
{
	$uid = get_option('flattr_uid');
	$cat = get_option('flattr_cat');

	if (strlen($uid) && strlen($cat))
	{
		return flattr_permalink($uid, $cat, get_the_title(), get_the_excerpt(), strip_tags(get_the_tag_list('', ',', '')), get_permalink());
	}
}

function the_flattr_permalink()
{
	echo(get_the_flattr_permalink());
}


// Deprecated methods

function FlattrDyn()
{
	$message = 'Deprecated function FlattrDyn() called.';
	trigger_error($message, E_USER_NOTICE);
	echo('<!-- ' . $message . ' -->');
}

function FlattrPerma()
{
	$message = 'Deprecated function FlattrPerma() called, use the_flattr_permalink() instead.';
	trigger_error($message, E_USER_NOTICE);
	echo('<!-- ' . $message . ' -->');
}