<?php

/*
    Plugin Name: kk Plugin Options Demonstration
    Plugin URI: http://wakeusup.com
    Description: Understanding WordPress Plugin Options/Settings.
    Version: 0.1
    Author: Kamal Khan
    Author URI: http://wakeusup.com
*/

if(!class_exists('kkPluginOptions')) :

// DEFINE PLUGIN ID
define('KKPLUGINOPTIONS_ID', 'kk-plugin-options');
// DEFINE PLUGIN NICK
define('KKPLUGINOPTIONS_NICK', 'kk Plugin Options');

    class kkPluginOptions
    {
		/** function/method
		* Usage: return absolute file path
		* Arg(1): string
		* Return: string
		*/
		public static function file_path($file)
		{
			return ABSPATH.'wp-content/plugins/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__)).$file;
		}
		/** function/method
		* Usage: hooking the plugin options/settings
		* Arg(0): null
		* Return: void
		*/
		public static function register()
		{
			register_setting(KKPLUGINOPTIONS_ID.'_options', 'kkpo_quote');
		}
		/** function/method
		* Usage: hooking (registering) the plugin menu
		* Arg(0): null
		* Return: void
		*/
		public static function menu()
		{
			// Create menu tab
			add_options_page(KKPLUGINOPTIONS_NICK.' Plugin Options', KKPLUGINOPTIONS_NICK, 'manage_options', KKPLUGINOPTIONS_ID.'_options', array('kkPluginOptions', 'options_page'));
		}
		/** function/method
		* Usage: show options/settings form page
		* Arg(0): null
		* Return: void
		*/
		public static function options_page()
		{ 
			if (!current_user_can('manage_options')) 
			{
				wp_die( __('You do not have sufficient permissions to access this page.') );
			}
			
			$plugin_id = KKPLUGINOPTIONS_ID;
			// display options page
			include(self::file_path('options.php'));
		}
		/** function/method
		* Usage: filtering the content
		* Arg(1): string
		* Return: string
		*/
		public static function content_with_quote($content)
		{
			$quote = '<p><blockquote>' . get_option('kkpo_quote') . '</blockquote></p>';
			return $content . $quote;
		}
    }
	
	if ( is_admin() )
	{
		add_action('admin_init', array('kkPluginOptions', 'register'));
		add_action('admin_menu', array('kkPluginOptions', 'menu'));
	}
	add_filter('the_content', array('kkPluginOptions', 'content_with_quote'));

endif;

?>