<?php
/*
Plugin Name: Flattr
Plugin URI: http://flattr.com/
Description: Give your readers the opportunity to Flattr your effort
Version: 0.9.13
Author: Flattr.com
Author URI: http://flattr.com/
License: This code is (un)licensed under the kopimi (copyme) non-license; http://www.kopimi.com. In other words you are free to copy it, taunt it, share it, fork it or whatever. :)
*/

class Flattr
{
	const VERSION = '0.9.13';
	const WP_MIN_VER = '2.9';
	const PHP_MIN_VER = '5.0.0';
	const API_SCRIPT  = 'https://api.flattr.com/js/0.5.0/load.js?mode=auto';

	/** @var array */
	protected static $categories = array('text', 'images', 'audio', 'video', 'software', 'rest');
	/** @var array */
	protected static $languages;
	/** @var Flattr */
	protected static $instance;

	/** @var Flattr_Settings */
	protected $settings;

	/** @var String */
	protected $basePath;

	public function __construct()
	{	
		if (is_admin())
		{
			if (!$this->compatibilityCheck())
			{
				return;
			}
			
			$this->init();
		}
		if ( get_option('flattr_aut_page', 'off') == 'on' || get_option('flattr_aut', 'off') == 'on' )
		{
			remove_filter('get_the_excerpt', 'wp_trim_excerpt');
			add_filter('the_content', array($this, 'injectIntoTheContent'),11);
			add_filter('get_the_excerpt', array($this, 'filterGetExcerpt'), 1);
		}

		wp_enqueue_script('flattrscript', self::API_SCRIPT);		
	}
	
	protected function addAdminNoticeMessage($msg)
	{
		if (!isset($this->adminNoticeMessages))
		{
			$this->adminNoticeMessages = array();
			add_action( 'admin_notices', array(&$this, 'adminNotice') );
		}
		
		$this->adminNoticeMessages[] = $msg;
	}
	
	public function adminNotice()
	{
		echo '<div id="message" class="error">';
		
		foreach($this->adminNoticeMessages as $msg)
		{
			echo "<p>{$msg}</p>";
		}
		
		echo '</div>';
	}

	protected function compatibilityCheck()
	{
		global $wp_version;
		
		if (version_compare(PHP_VERSION, self::PHP_MIN_VER, '<'))
		{
			$this->addAdminNoticeMessage('<strong>Warning:</strong> The Flattr plugin requires PHP5. You are currently using '. PHP_VERSION);
  			add_action( 'admin_notices', array(&$this, 'adminNotice') );
			return false;
		}
		
		if (version_compare($wp_version, self::WP_MIN_VER, '<'))
		{
			$this->addAdminNoticeMessage('<strong>Warning:</strong> The Flattr plugin requires WordPress '. self::WP_MIN_VER .' or later. You are currently using '. $wp_version);
			return false;
		}
		
		return true;
	}

	public function getBasePath()
	{
		if (!isset($this->basePath))
		{
			$this->basePath = WP_PLUGIN_DIR . '/' . plugin_basename( dirname(__FILE__) ) . '/';
		}
		
		return $this->basePath;
	}

	public function getButton($skipOptionCheck = false)
	{
		global $post;

		if ( ! $skipOptionCheck && ( ($post->post_type == 'page' && get_option('flattr_aut_page', 'off') != 'on') || ($post->post_type != 'page' && get_option('flattr_aut', 'off') != 'on') || is_feed() ) )
		{
			return '';
		}

		if (get_post_meta($post->ID, '_flattr_btn_disabled', true))
		{
			return '';
		}

		$selectedLanguage = get_post_meta($post->ID, '_flattr_post_language', true);
		if (empty($selectedLanguage))
		{
			$selectedLanguage = get_option('flattr_lng');
		}

		$selectedCategory = get_post_meta($post->ID, '_flattr_post_category', true);
		if (empty($selectedCategory))
		{
			$selectedCategory = get_option('flattr_cat');
		}

		$hidden = get_post_meta($post->ID, '_flattr_post_hidden', true);
		if ($hidden == '')
		{
			$hidden = get_option('flattr_hide', false);
		}

		$buttonData = array(

			'user_id'	=> get_option('flattr_uid'),
			'url'		=> get_permalink(),
			'compact'	=> ( get_option('flattr_compact', false) ? true : false ),
			'hide'		=> $hidden,
			'language'	=> $selectedLanguage,
			'category'	=> $selectedCategory,
			'title'		=> strip_tags(get_the_title()),
			'body'		=> strip_tags(preg_replace('/\<br\s*\/?\>/i', "\n", $this->getExcerpt())),
			'tag'		=> strip_tags(get_the_tag_list('', ',', ''))

		);

		if (isset($buttonData['user_id'], $buttonData['url'], $buttonData['language'], $buttonData['category']))
		{
			return $this->getButtonCode($buttonData);
		}
	}

	protected function getButtonCode($params)
	{
		$rev = sprintf('flattr;uid:%s;language:%s;category:%s;',
			$params['user_id'],
			$params['language'],
			$params['category']
		);

		if (!empty($params['tag']))
		{
			$rev .= 'tags:'. addslashes($params['tag']) .';';
		}

		if ($params['hide'])
		{
			$rev .= 'hidden:1;';
		}

		if ($params['compact'])
		{
			$rev .= 'button:compact;';
		}

		if (empty($params['body']) && !in_array($params['category'], array('images', 'video', 'audio')))
		{
			$params['body'] = get_bloginfo('description');

			if (empty($params['body']) || strlen($params['body']) < 5)
			{
				$params['body'] = $params['title'];
			}
		}

		return sprintf('<a class="FlattrButton" style="display:none;" href="%s" title="%s" rev="%s">%s</a>',
			$params['url'],
			addslashes($params['title']),
			$rev,
			$params['body']
		);
	}

	public static function getCategories()
	{
		return self::$categories;
	}

	public static function filterGetExcerpt($content)
	{
        $excerpt_length = apply_filters('excerpt_length', 55);
        $excerpt_more = apply_filters('excerpt_more', ' ' . '[...]');

		return self::getExcerpt($excerpt_length) . $excerpt_more;
	}

	public static function getExcerpt($excerpt_max_length = 1024)
	{
		global $post;
		
		$excerpt = $post->post_excerpt;
		if (! $excerpt)
		{
			$excerpt = $post->post_content;
	    }

		$excerpt = strip_shortcodes($excerpt);
		$excerpt = strip_tags($excerpt);
		$excerpt = str_replace(']]>', ']]&gt;', $excerpt);
		
		// Hacks for various plugins
		$excerpt = preg_replace('/httpvh:\/\/[^ ]+/', '', $excerpt); // hack for smartyoutube plugin
		$excerpt = preg_replace('%httpv%', 'http', $excerpt); // hack for youtube lyte plugin
	
	    // Try to shorten without breaking words
	    if ( strlen($excerpt) > $excerpt_max_length )
	    {
			$pos = strpos($excerpt, ' ', $excerpt_max_length);
			if ($pos !== false)
			{
				$excerpt = substr($excerpt, 0, $pos);
			}
		}

		// If excerpt still too long
		if (strlen($excerpt) > $excerpt_max_length)
		{
			$excerpt = substr($excerpt, 0, $excerpt_max_length);
		}

		return $excerpt;
	}

	public static function getInstance()
	{
		if (!self::$instance)
		{
			self::$instance = new self();
		}
		
		return self::$instance;
	}
	
	public static function getLanguages()
	{
		if (!isset(self::$languages))
		{
			include(Flattr::getInstance()->getBasePath() . 'languages.php');
			self::$languages = $languages;
		}
		
		return self::$languages;
	}
	
	protected function init()
	{
		if (!$this->settings)
		{
			require_once($this->getBasePath() . 'settings.php');
			$this->settings = new Flattr_Settings();
		}

		if (!$this->postMetaHandler)
		{
			require_once($this->getBasePath() . 'postmeta.php');
			$this->postMetaHandler = new Flattr_PostMeta();
		}
	}

	public function setExcerpt($content)
	{
		global $post;
		return $post->post_content;
	}
	
	public function injectIntoTheContent($content)
	{
		return $content . $this->getButton();
	}	
}

Flattr::getInstance();

/**
 * returns the Flattr button
 * Use this from your template
 */
function get_the_flattr_permalink()
{
	return Flattr::getInstance()->getButton(true);
}

/**
 * prints the Flattr button
 * Use this from your template
 */
function the_flattr_permalink()
{
	echo(get_the_flattr_permalink());
}
