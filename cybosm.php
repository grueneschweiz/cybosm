<?php

/**
 * Plugin Name: Cybosm
 * Plugin URI: https://github.com/cyrillbolliger/cybosm
 * Version: 1.0.3
 * Description: Yet another social sharing plugin. Extremly lightwight.
 * Author: Cyrill Bolliger
 * Author URI: http://www.cyrillbolliger.ch
 * Text Domain: cybosm
 * Domain Path: languages
 * GitHub Plugin URI: cyrillbolliger/cybosm
 * License: GPL 2.
 */
 
/**
 * Copyright 2015  Cyrill Bolliger  (email: bolliger@gmx.ch)
 * 
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as 
 * published by the Free Software Foundation.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

/**
 * lock out script kiddies: die an direct call 
 */
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/**
 * abspath to plugins directory
 */
define( 'CYBOSM_PLUGIN_PATH', dirname( __FILE__ ) );

/**
 * version number (dont forget to change it also in the header)
 */
define( 'CYBOSM_VERSION', '1.0.3' );

/**
 * plugin prefix
 */
define( 'CYBOSM_PLUGIN_PREFIX', 'cybosm' );

/**
 * load settings class
 */
require_once( CYBOSM_PLUGIN_PATH . '/includes/class-cybosm-settings.php' );


if ( ! class_exists( 'Cybosm_Main' ) ) {
	
	class Cybosm_Main extends Cybosm_Settings {
		
		/*
		 * Construct the plugin object
		 */
		public function __construct() {
			parent::__construct();
			
			register_activation_hook( __FILE__, array( &$this, 'activate' ) );
			register_deactivation_hook( __FILE__, array( &$this, 'deactivate' ) );
			
			add_action( 'init', array( &$this, 'init' ) );
			add_action( 'init', array( &$this, 'fe_init' ) );
			add_action( 'admin_init', array( &$this, 'admin_init' ) );
			add_action( 'admin_menu', array( &$this, 'add_menu' ) );
			add_action( 'plugins_loaded', array( &$this, 'i18n' ) );
			add_action( 'plugins_loaded', array( &$this, 'upgrade' ) );
			add_action( 'wp_enqueue_scripts', array( &$this, 'load_resources' ) );
			add_action( 'admin_enqueue_scripts', array( &$this, 'load_resources' ) );
			add_action( 'widgets_init', array( &$this, 'cybosm_load_widget' ) );
		}
		
		/**
		 * Activate the plugin
		 */
		public function activate() {
			$this->set_version_number();
			$this->add_roles_on_plugin_activation();
			$this->add_capabilities_on_plugin_activation();
			$this->create_tables_on_plugin_activation();
			$this->create_default_options_on_first_plugin_activation();
		}
		
		/**
		 * Deactivate the plugin
		 */
		public function deactivate() {
			$this->remove_capabilities_on_plugin_deactivation();
			$this->remove_roles_on_plugin_deactivation();
		}
		
		/**
		 * Hook into WP's init action hook.
		 */
		public function init() {
			
		}
		
		/**
		 * Hook into WP's init action hook for frontend pages
		 */
		public function fe_init() {
			if ( ! is_admin() ) {
				// load the frontend
				require_once( CYBOSM_PLUGIN_PATH . '/includes/class-cybosm-frontend.php' );
			}
		}
		
		/**
		 * Hook into WP's admin_init action hook
		 */
		public function admin_init() {
			$this->init_options();
		}
		
		/**
		 * write version number to db
		 */
		public function set_version_number() {
			update_option( CYBOSM_PLUGIN_PREFIX . '_version_number', CYBOSM_VERSION );
		}
		
		/**
		 * upgrade routine
		 */
		public function upgrade() {
			$current_version = get_option( CYBOSM_PLUGIN_PREFIX . '_version_number' );
			
			// if everything is up to date stop here
			if ( CYBOSM_VERSION == $current_version ) {
				return; // BREAKPOINT
			}
			
			/*
			// run the upgrade routine for versions smaller 1.3.0
			if ( -1 == version_compare( $current_version, '1.3.0' ) ) {
				// YOUR UPGRADE ROUTINE
			}
			
			// set the current version number
			$this->set_version_number();
			*/
		}
		
		
		/**
		 * Initialize some custom settings
		 */
		public function init_options() {
			register_setting( CYBOSM_PLUGIN_PREFIX . '_options', CYBOSM_PLUGIN_PREFIX . '_options' );
			
			/**
			 * FACEBOOK
			 */
			add_settings_section(
				CYBOSM_PLUGIN_PREFIX . '_facebook_options', 
				__( 'Facebook', 'cybosm' ), 
				array( &$this, 'display_section_description' ), 
				CYBOSM_PLUGIN_PREFIX . '_options'
			);
			
			add_settings_field( 
				CYBOSM_PLUGIN_PREFIX . '_fb_option', 
				__( 'Choose the functionality of the button', 'cybosm' ), 
				array( &$this, 'render_options_radiobuttons' ),
				CYBOSM_PLUGIN_PREFIX . '_options', 
				CYBOSM_PLUGIN_PREFIX . '_facebook_options',
				array(
					'id' => CYBOSM_PLUGIN_PREFIX . '_fb_option',
					'options' => array( 
						'off'   => __( 'Off', 'cybosm' ),
						'share' => __( 'Share button', 'cybosm' ), 
						'visit' => __( 'Link to fanpage', 'cybosm' ), 
					),
				)
			);
			
			add_settings_field( 
				CYBOSM_PLUGIN_PREFIX . '_fb_url', 
				__( 'Link to your fanpage', 'cybosm' ), 
				array( &$this, 'render_options_url_input' ),
				CYBOSM_PLUGIN_PREFIX . '_options', 
				CYBOSM_PLUGIN_PREFIX . '_facebook_options',
				array(
					'id' => CYBOSM_PLUGIN_PREFIX . '_fb_url',
				)
			);
			
			/**
			 * TWITTER
			 */
			add_settings_section(
				CYBOSM_PLUGIN_PREFIX . '_twitter_options', 
				__( 'Twitter', 'cybosm' ), 
				array( &$this, 'display_section_description' ), 
				CYBOSM_PLUGIN_PREFIX . '_options'
			);
			
			add_settings_field( 
				CYBOSM_PLUGIN_PREFIX . '_tw_option', 
				__( 'Choose the functionality of the button', 'cybosm' ), 
				array( &$this, 'render_options_radiobuttons' ),
				CYBOSM_PLUGIN_PREFIX . '_options', 
				CYBOSM_PLUGIN_PREFIX . '_twitter_options',
				array(
					'id' => CYBOSM_PLUGIN_PREFIX . '_tw_option',
					'options' => array( 
						'off'   => __( 'Off', 'cybosm' ),
						'share' => __( 'Share button', 'cybosm' ), 
						'visit' => __( 'Link to profile', 'cybosm' ), 
					)
				)
			);
			
			add_settings_field( 
				CYBOSM_PLUGIN_PREFIX . '_tw_url', 
				__( 'Link to your twitter profile', 'cybosm' ), 
				array( &$this, 'render_options_url_input' ),
				CYBOSM_PLUGIN_PREFIX . '_options', 
				CYBOSM_PLUGIN_PREFIX . '_twitter_options',
				array(
					'id' => CYBOSM_PLUGIN_PREFIX . '_tw_url',
				)
			);
			
			add_settings_field( 
				CYBOSM_PLUGIN_PREFIX . '_tw_via', 
				__( 'Username (@example)', 'cybosm' ), 
				array( &$this, 'render_options_text_input' ),
				CYBOSM_PLUGIN_PREFIX . '_options', 
				CYBOSM_PLUGIN_PREFIX . '_twitter_options',
				array(
					'id' => CYBOSM_PLUGIN_PREFIX . '_tw_via',
				)
			);
			
			/**
			 * GOOGLE PLUS
			 */
			add_settings_section(
				CYBOSM_PLUGIN_PREFIX . '_gplus_options', 
				__( 'Google+', 'cybosm' ), 
				array( &$this, 'display_section_description' ), 
				CYBOSM_PLUGIN_PREFIX . '_options'
			);
			
			add_settings_field( 
				CYBOSM_PLUGIN_PREFIX . '_gp_option', 
				__( 'Choose the functionality of the button', 'cybosm' ), 
				array( &$this, 'render_options_radiobuttons' ),
				CYBOSM_PLUGIN_PREFIX . '_options', 
				CYBOSM_PLUGIN_PREFIX . '_gplus_options',
				array(
					'id' => CYBOSM_PLUGIN_PREFIX . '_gp_option',
					'options' => array( 
						'off'   => __( 'Off', 'cybosm' ),
						'share' => __( 'Share button', 'cybosm' ), 
						'visit' => __( 'Link to profile', 'cybosm' ), 
					)
				)
			);
			
			add_settings_field( 
				CYBOSM_PLUGIN_PREFIX . '_gp_url', 
				__( 'Link to your Google+ profile', 'cybosm' ), 
				array( &$this, 'render_options_url_input' ),
				CYBOSM_PLUGIN_PREFIX . '_options', 
				CYBOSM_PLUGIN_PREFIX . '_gplus_options',
				array(
					'id' => CYBOSM_PLUGIN_PREFIX . '_gp_url',
				)
			);
			
			/**
			 * YOUTUBE
			 */
			add_settings_section(
				CYBOSM_PLUGIN_PREFIX . '_youtube_options', 
				__( 'Youtube', 'cybosm' ), 
				array( &$this, 'display_section_description' ), 
				CYBOSM_PLUGIN_PREFIX . '_options'
			);
			
			add_settings_field( 
				CYBOSM_PLUGIN_PREFIX . '_yt_option', 
				__( 'Choose the functionality of the button', 'cybosm' ), 
				array( &$this, 'render_options_radiobuttons' ),
				CYBOSM_PLUGIN_PREFIX . '_options', 
				CYBOSM_PLUGIN_PREFIX . '_youtube_options',
				array(
					'id' => CYBOSM_PLUGIN_PREFIX . '_yt_option',
					'options' => array( 
						'off'   => __( 'Off', 'cybosm' ),
						'visit' => __( 'Link to chanel', 'cybosm' ), 
					)
				)
			);
			
			add_settings_field( 
				CYBOSM_PLUGIN_PREFIX . '_yt_url', 
				__( 'Link to your Youtube chanel', 'cybosm' ), 
				array( &$this, 'render_options_url_input' ),
				CYBOSM_PLUGIN_PREFIX . '_options', 
				CYBOSM_PLUGIN_PREFIX . '_youtube_options',
				array(
					'id' => CYBOSM_PLUGIN_PREFIX . '_yt_url',
				)
			);
		}
		
		/**
		 * Render the html for options radiobuttons
		 */
		public function render_options_radiobuttons( $args ) {
			$options = get_option( CYBOSM_PLUGIN_PREFIX . '_options' );
			
			foreach ( $args['options'] as $option_id => $option_title ) {
				if ( isset( $options[ $args['id'] ] ) ) {
					$checked = checked( $options[ $args['id'] ], $option_id, false );
				} else {
					$checked = '';
				}
				
				echo "<input type='radio' name='".CYBOSM_PLUGIN_PREFIX."_options[{$args['id']}]'".
				     " class='{$args['id']}' id='{$args['id']}_$option_id' value='$option_id' $checked>$option_title</br>\n";
			}
		}
		
		/**
		 * Render the html for options url input
		 */
		public function render_options_url_input( $args ) {
			$options = get_option( CYBOSM_PLUGIN_PREFIX . '_options' );
			
			echo "<input type='url' id='{$args['id']}' name='".CYBOSM_PLUGIN_PREFIX."_options[{$args['id']}]' value='{$options[$args['id']]}'>\n";
		}
		
		/**
		 * Render the html for options text input
		 */
		public function render_options_text_input( $args ) {
			$options = get_option( CYBOSM_PLUGIN_PREFIX . '_options' );
			
			echo "<input type='text' id='{$args['id']}' name='".CYBOSM_PLUGIN_PREFIX."_options[{$args['id']}]' value='{$options[$args['id']]}'>\n";
		}
		
		/**
		 * The description of the $section of the options page
		 * 
		 * @var   string   $section   name of the section
		 */
		public function display_section_description( $section ) {
			echo sprintf( __( 'Enter your options for the %s button here.', 'cybosm' ), $section['title'] );
		}
		
		/**
		 * Add a menu
		 */
		public function add_menu() {
			add_options_page( __('Cybo Social Sharing Options', 'cybosm'), __('Cybo Social Sharing', 'cybosm'), 'manage_options', CYBOSM_PLUGIN_PREFIX . '_options', array( &$this, 'plugin_options_page' ) );
		}

		/**
		 * Menu Callback
		 */
		public function plugin_options_page() {
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( __( 'You do not have sufficient permissions to access this page.', 'cybosm' ) );
			}
			
			// Render the settings template
			include CYBOSM_PLUGIN_PATH . '/admin/options.php';
		}
		
		/**
		 * Register and load the widget
		 */
		public function cybosm_load_widget() {
			// load the widget class
			require_once CYBOSM_PLUGIN_PATH . '/includes/class-cybosm-widget.php';
			// register the widget
			register_widget( 'cybosm_widget' );
		}
		
		/**
		 * I18n.
		 * 
		 * Note: Put the translation in the languages folder in the plugins directory,
		 * name the translation files like "nameofplugin-lanugage_COUUNTRY.po". Ex: "cybosm-fr_FR.po"
		 */
		public function i18n() {
			$path = dirname( plugin_basename(__FILE__) ) . '/languages';
			load_plugin_textdomain( 'cybosm', false, $path );
		}
		
		/**
		 * Add roles on plugin activation
		 */
		public function add_roles_on_plugin_activation() {
			if ( is_multisite() ) {
				global $wpdb;
				$blogs_list = $wpdb->get_results("SELECT blog_id FROM {$wpdb->blogs}", ARRAY_A);
				if ( ! empty( $blogs_list ) ) {
					foreach ($blogs_list as $blog) {
						switch_to_blog($blog['blog_id']);
						$this->add_roles_for_sigle_blog();
						restore_current_blog();
					}
				}
			} else {
				$this->add_roles_for_sigle_blog();
			}
		}
		
		/**
		 * actually adds the roles
		 */
		private function add_roles_for_sigle_blog() {
			foreach( $this->roles as $role ) {
				add_role( $role[0], $role[1], $role[2] );
			}
		}
		
		/**
		 * Remove roles on plugin deactivation
		 */
		public function remove_roles_on_plugin_deactivation() {
			if ( is_multisite() ) {
				global $wpdb;
				$blogs_list = $wpdb->get_results("SELECT blog_id FROM {$wpdb->blogs}", ARRAY_A);
				if ( ! empty( $blogs_list ) ) {
					foreach ($blogs_list as $blog) {
						switch_to_blog($blog['blog_id']);
						$this->remove_roles_for_sigle_blog();
						restore_current_blog();
					}
				}
			} else {
				$this->remove_roles_for_sigle_blog();
			}
		}
		
		/**
		 * actually removes the roles
		 */
		private function remove_roles_for_sigle_blog() {
			foreach( $this->roles as $role ) {
				remove_role( $role[0] );
			}
		}
		
		/**
		 * Add capabilities on plugin activation
		 */
		public function add_capabilities_on_plugin_activation() {
			if ( is_multisite() ) {
				global $wpdb;
				$blogs_list = $wpdb->get_results("SELECT blog_id FROM {$wpdb->blogs}", ARRAY_A);
				if ( ! empty( $blogs_list ) ) {
					foreach ($blogs_list as $blog) {
						switch_to_blog($blog['blog_id']);
						$this->add_capabilities_for_single_blog();
						restore_current_blog();
					}
				}
			} else {
				$this->add_capabilities_for_single_blog();
			}
		}
		
		
		/**
		 * Actually add capabilities
		 */
		private function add_capabilities_for_single_blog() {
			$capabilities = array();
			$this->add_plugin_capabilities_for( 'administrator' , $capabilities );
		}
		
		
		/**
		 * Remove capabilities on plugin deactivation
		 */
		public function remove_capabilities_on_plugin_deactivation() {
			if ( is_multisite() ) {
				global $wpdb;
				$blogs_list = $wpdb->get_results("SELECT blog_id FROM {$wpdb->blogs}", ARRAY_A);
				if ( ! empty( $blogs_list ) ) {
					foreach ($blogs_list as $blog) {
						switch_to_blog($blog['blog_id']);
						$this->remove_capabilities_for_single_blog();
						restore_current_blog();
					}
				}
			} else {
				$this->remove_capabilities_for_single_blog();
			}
		}
		
		
		/**
		 * Actually remove capabilities
		 */
		private function remove_capabilities_for_single_blog() {
			$capabilities = array(); 
			$this->remove_plugin_capabilities_for( 'administrator' , $capabilities );
			
		}
		
		/**
		 * Add capabilities
		 * 
		 * @var string			$role_name		subject
		 * @var string|array 	$capabilities	caps to add
		 */
		public function add_plugin_capabilities_for( $role_name, $capabilities ) {
			$role = get_role( $role_name );
			foreach ( (array) $capabilities as $capability ) {
				$role->add_cap( $capability );
			}
		}
		
		/**
		 * Remove capabilities
		 * 
		 * @var string			$role_name		subject
		 * @var string|array 	$capabilities	caps to remove
		 */
		public function remove_plugin_capabilities_for( $role_name, $capabilities ) {
			$role = get_role( $role_name );
			foreach ( (array) $capabilities as $capability ) {
				$role->remove_cap( $capability );
			}
		}
		
		/**
		 * Add tables on plugin activation if they dont exist yet
		 */
		public function create_tables_on_plugin_activation() {
			// dont forget to check if tables dont exist yet
			// dont forget to use $this->network_tables and $this->single_blog_tables (with $wpdb->prefix) as table names
		}
		
		/**
		 * Create options on plugin activation it they dont exist yet. Nothing will be overwritten.
		 */
		public function create_default_options_on_first_plugin_activation() {
			// single blog options
			if ( is_multisite() ) {
				global $wpdb;
				$blogs_list = $wpdb->get_results("SELECT blog_id FROM {$wpdb->blogs}", ARRAY_A);
				if ( ! empty( $blogs_list ) ) {
					foreach ($blogs_list as $blog) {
						switch_to_blog($blog['blog_id']);
						$this->add_options_for_sigle_blog();
						restore_current_blog();
					}
				}
			} else {
				$this->add_options_for_sigle_blog();
			}
			
			// options for all blogs (network options)
			$this->add_site_options();
		}
		
		/**
		 * Actually adds the options. If the option already exists it will simply be skiped.
		 * So nothing will be overwritten. This function will only add single blog options.
		 */
		private function add_options_for_sigle_blog() {
			foreach( $this->single_blog_options as $option_name => $option_data ) {
				add_option( $option_name, $option_data );
			}
		}
		
		/**
		 * Actually adds the options. If the option already exists it will simply be skiped.
		 * So nothing will be overwritten. This function will only add network options.
		 */
		private function add_site_options() {
			foreach( $this->network_options as $option_name => $option_data ) {
				add_site_option( $option_name, $option_data );
			}
		}
		
		/**
		 * handle short code
		 * 
		 * @var		array	$atts	provided from WP's add_shortcode() function
		 * @return	string
		 */
		public function short_code_handler( $atts ) {
			$frontend = new Cybosm_Frontend();
			return $frontend->process_short_code( $atts );
		}
		
		/**
		 * load ressources (js, css)
		 */
		public function load_resources() {
			
			foreach ( $this->styles as $style ) {
				if ( is_admin() && $style['scope'] == ( 'admin' || 'shared' ) ) {
					if ( ! wp_style_is( $style['handle'], 'enqueued' ) ) {
						$this->register_style( $style );
						wp_enqueue_style( $style['handle'] );
					}
				}
				if ( ! is_admin() && $style['scope'] == ( 'frontend' || 'shared' ) ) {
					if ( ! wp_style_is( $style['handle'], 'enqueued' ) ) {
						$this->register_style( $style );
						wp_enqueue_style( $style['handle'] );
					}
				}
			}
			
			foreach ( $this->scripts as $script ) {
				if ( is_admin() && $script['scope'] == ( 'admin' || 'shared' ) ) {
					if ( ! wp_script_is( $script['handle'], 'enqueued' ) ) {
						$this->register_script( $script );
						wp_enqueue_script( $script['handle'] );
					}
				}
				if ( ! is_admin() && $script['scope'] == ( 'frontend' || 'shared' ) ) {
					if ( ! wp_script_is( $script['handle'], 'enqueued' ) ) {
						$this->register_script( $script );
						wp_enqueue_script( $script['handle'] );
					}
				}
			}
		}
		
		/**
		 * register script
		 * 
		 * @var array 	$script		for params see __construct in Cybosm_Settings
		 */
		public function register_script( $script ) {
			wp_register_script( 
				$script['handle'],
				plugins_url( $script['src'], __FILE__ ),
				$script['deps'],
				CYBOSM_VERSION,
				$script['in_footer']
			);
		}
		
		/**
		 * register style
		 * 
		 * @var array 	$style		for params see __construct in Cybosm_Settings
		 */
		public function register_style( $style ) {
			wp_register_style(
				$style['handle'],
				plugins_url( $style['src'], __FILE__ ),
				$style['deps'],
				CYBOSM_VERSION,
				$style['media']
			);
		}
		
	} // END class Cybosm_Main
} // END if ( ! class_exists( 'Cybosm_Main' ) )

if ( class_exists( 'Cybosm_Main' ) ) {
	
	if ( ! is_admin() ) {
		require_once( CYBOSM_PLUGIN_PATH . '/includes/class-cybosm-frontend.php' );
	}
	
	$cybosm_main = new Cybosm_Main();
	
}