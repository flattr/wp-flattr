<?php
/*
Plugin Name: Flattr
Plugin URI: http://wordpress.org/extend/plugins/flattr/
Description: Give your readers the opportunity to Flattr your effort
Version: 0.9.17
Author: Flattr.com
Author URI: http://flattr.com/
License: This code is (un)licensed under the kopimi (copyme) non-license; http://www.kopimi.com. In other words you are free to copy it, taunt it, share it, fork it or whatever. :)
*/

if (version_compare(PHP_VERSION, '5.0.0', '<'))
{
	require_once( WP_PLUGIN_DIR . '/' . plugin_basename( dirname(__FILE__) ) . '/flattr4.php');
}
else
{
	require_once( WP_PLUGIN_DIR . '/' . plugin_basename( dirname(__FILE__) ) . '/flattr5.php');
}
