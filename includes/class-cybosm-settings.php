<?php

/**
 * lock out script kiddies: die an direct call 
 */
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

if( ! class_exists( 'Cybosm_Settings' ) ) {

	class Cybosm_Settings {
		
		/**
		 * Holds the default options
		 */
		public $network_options = array();
		public $single_blog_options = array();
		
		/**
		 * holds the full table names, WordPress prefix and plugin prefix included
		 */
		public $network_tables = array();
		public $single_blog_tables = array();
		
		/**
		 * holds an array with the role deinition
		 * Note: array must provide the arguments of the add_role() function
		 */
		public $roles = array();
		
		/**
		 * holds an array with the scripts / styles
		 * Note: have a look at __contruct to see how it's used 
		 */
		protected $scripts = array();
		protected $styles = array();
		
		public function __construct() {
			
			$this->network_options = array(); // put your default network options in here. 
			                                  // Ex.: $network_options = array( array( CYBOSM_PLUGIN_PREFIX . '_option1_name' => 'default value'),
			                                  //                                       CYBOSM_PLUGIN_PREFIX . '_option2_name' => 'default value') );
			                                  // Note: include the plugin prefix for the option name
			$this->single_blog_options = array(
				CYBOSM_PLUGIN_PREFIX . '_options' => array( 
					CYBOSM_PLUGIN_PREFIX . '_fb_option' => 'share',
					CYBOSM_PLUGIN_PREFIX . '_tw_option' => 'share',
					CYBOSM_PLUGIN_PREFIX . '_gp_option' => 'share',
					CYBOSM_PLUGIN_PREFIX . '_yt_option' => 'off',
				)
			); // put your blog options in here.
			
			$this->scripts[] = array(
				'handle'     => CYBOSM_PLUGIN_PREFIX . '-admin-js', // string
				'src'        => '/js/cybosm-admin-js.js', // string relative to plugin folder
				'deps'       => array( 'jquery' ), // array
				'in_footer'  => true, // bool
				'scope'      => 'admin', // admin | frontend | shared
			);
			
			$this->scripts[] = array(
				'handle'     => CYBOSM_PLUGIN_PREFIX . '-frontend-js', // string
				'src'        => '/js/cybosm-frontend-js.js', // string relative to plugin folder
				'deps'       => array( 'jquery' ), // array
				'in_footer'  => true, // bool
				'scope'      => 'frontend', // admin | frontend | shared
			);
			
			$this->styles[] = array(
				'handle'    => CYBOSM_PLUGIN_PREFIX . '-admin-css', // string
				'src'       => '/css/cybosm-admin-css.css', // string relative to plugin folder
				'deps'      => array(), // array
				'media'     => 'all', // css media tag
				'scope'     => 'admin', // admin | frontend | shared
			);
			
			$this->styles[] = array(
				'handle'    => CYBOSM_PLUGIN_PREFIX . '-frontend-css', // string
				'src'       => '/css/cybosm-frontend-css.css', // string relative to plugin folder
				'deps'      => array(), // array
				'media'     => 'all', // css media tag
				'scope'     => 'frontend', // admin | frontend | shared
			);
			
			$network_tables = array(); // put your table names in this array (whitout prefix and stuff)
			$single_blog_tables = array(); // put your table names in this array (whitout prefix and stuff)
			
			$this->set_network_tables( $network_tables );
			$this->set_single_blog_tables( $single_blog_tables );
			
			// array must provide the arguments of the add_role() function
			// $this->roles[] = array( CYBOSM_PLUGIN_PREFIX . '_user', __( 'Cybosm_user', 'cybosm'), array(  ) );
		}
		
		/**
		 * loads the $network_tables array. the key will be the table name, the content will be the fully prefixed table name
		 */
		private function set_network_tables( $table_names ) {
			global $wpdb;
			
			$tables = array();
			
			foreach( $table_names as $table_name ) {
				$tables[ $table_name ] = $wpdb->base_prefix . CYBOSM_PLUGIN_PREFIX . '_' . $table_name;
			}
			
			$this->network_tables = $tables;
		}
		
		/**
		 * loads the $single_blog_tables array. the key will be the table name, the content will be the table name with the plugin prefix.
		 * the blog prefix will NOT be set!
		 */
		private function set_single_blog_tables( $table_names ) {
			global $wpdb;
			
			$tables = array();
			
			foreach( $table_names as $table_name ) {
				$tables[ $table_name ] = CYBOSM_PLUGIN_PREFIX . '_' . $table_name;
			}
			
			$this->single_blog_tables = $tables;
		}
	}
}