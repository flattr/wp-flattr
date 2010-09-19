<?php

class Flattr_Settings
{
    public function __construct()
    {
        add_action('admin_init',    array( $this, 'register_settings') );
        add_action('admin_menu',    array( $this, 'init_ui') );
    }

    public function init_ui()
    {
        add_options_page('Flattr Setup', 'Flattr', 'manage_options', __FILE__, array($this, 'render'));
    }

    public function register_settings()
    {
        register_setting('flattr-settings-uid-group', 'flattr_uid',     array($this, 'sanitize_userid'));
        register_setting('flattr-settings-group', 'flattr_aut',         array($this, 'sanitize_auto'));
        register_setting('flattr-settings-group', 'flattr_aut_page',    array($this, 'sanitize_auto_page'));
        register_setting('flattr-settings-group', 'flattr_cat',         array($this, 'sanitize_category'));
        register_setting('flattr-settings-group', 'flattr_lng',         array($this, 'sanitize_language'));
        register_setting('flattr-settings-group', 'flattr_compact',     array($this, 'sanitize_checkbox'));
        register_setting('flattr-settings-group', 'flattr_hide',        array($this, 'sanitize_checkbox'));
        register_setting('flattr-settings-group', 'flattr_top',         array($this, 'sanitize_checkbox'));
        register_setting('flattr-settings-group', 'flattr_override_sharethis', array($this, 'sanitize_checkbox'));
    }

    public function render()
    {
        if (array_key_exists('FlattrId', $_GET)) {
            include('settings-confirm-template.php');
        }
        else {
            include('settings-template.php');
        }
    }

    public function sanitize_category($category)
    {
        return $category;
    }

    public function sanitize_language($language)
    {
        return $language;
    }

    public function sanitize_checkbox($input)
    {
        return ($input == 'true' ? 'true' : '');
    }

    public function sanitize_auto($input)
    {
        return ($input == 'on' ? 'on' : '');
    }

    public function sanitize_auto_page($input)
    {
        return ($input == 'on' ? 'on' : '');
    }

    public function sanitize_userid($userId)
    {
        if (preg_match('/[^A-Za-z0-9-_.]/', $userId)) {
            $userId = false;
        }
        else if (is_numeric($userId)) {
            $userId = intval($userId);
        }
        return $userId;
    }
}
