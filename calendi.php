<?php
	/*
		Plugin Name:  Calendi
		Plugin URI:   https://calendiwp.com/
		Description:  An easy to use editorial calendar for WordPress. Ability to manage multiple post types.
		Version:      1.1.1
		Author:       Seb Kay
		Author URI:   https://sebkay.com/
		License:      GPL2
		License URI:  https://www.gnu.org/licenses/gpl-2.0.html
		Text Domain:  calendi
	*/



	defined('ABSPATH') or die('No script kiddies please!');



	/*------------------------------------------------------------
	-------- Setup
	------------------------------------------------------------*/
	//---- Constants
	define('CALENDI_VERSION', '1.0');

	//---- Files
	require 'functions.php';

	//---- Translation Ready
	function cwp_setup() {
		load_plugin_textdomain('calendi', false, dirname(plugin_basename( __FILE__ )) . '/languages');
	}

	add_action('plugins_loaded', 'cwp_setup');



	/*------------------------------------------------------------
	-------- Enqueue Styles & Scripts
	------------------------------------------------------------*/
	function calendi_enqueue_assets() {
		//---- CSS
		wp_enqueue_style('calendi', plugin_dir_url(__FILE__ ) . 'assets/css/style.css', false, CALENDI_VERSION);

		//---- JS
		wp_enqueue_script('calendi', plugin_dir_url(__FILE__ ) . 'assets/js//min/app.min.js', array('jquery'), CALENDI_VERSION, true);
	}

	add_action('admin_enqueue_scripts', 'calendi_enqueue_assets');



	/*------------------------------------------------------------
	-------- Create Options Page
	------------------------------------------------------------*/
	//----- Add menu item
	function cwp_menu_item() {
		if(current_user_can('manage_options')) {
			$menu_page = add_menu_page(
				'Calendi',
				'Calendi',
				'manage_options',
				'calendi',
				'cwp_options_page',
				'dashicons-calendar-alt',
				6
			);
		}
	}

	add_action('admin_menu', 'cwp_menu_item');

	//----- Create and display options page
	function cwp_options_page() {
		if(!current_user_can('manage_options')) {
			wp_die(__("You don't have sufficient access to this page.", 'calendi'));
		}

		require 'includes/options-page.php';
	}



	/*------------------------------------------------------------
	-------- Register Plugin Settings
	------------------------------------------------------------*/
	function cwp_register_settings() {
		register_setting('cwp_settings_group', 'cwp_settings');
	}

	add_action('admin_init', 'cwp_register_settings');



	/*------------------------------------------------------------
	-------- Get Plugin Settings
	------------------------------------------------------------*/
	function cwp_get_setting($setting = false) {
		if($setting) {
			$options = get_option('cwp_settings');

			if(isset($options[$setting])) {
				return $options[$setting];
			}
		}
	}



	/*------------------------------------------------------------
	-------- Add Default Settings
	------------------------------------------------------------*/
	function cwp_plugin_activation() {
		//---- Make 'post' post type enabled by default
		$current_settings = get_option('cwp_settings');

		if(!$current_settings) {
			update_option('cwp_settings', array('enabled_post_types' => array('post')));
		}
	}

	register_activation_hook(__FILE__, 'cwp_plugin_activation');
