<?php

/**
 * lock out script kiddies: die an direct call 
 */
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

if( ! class_exists( 'Cybosm_Widget' ) ) {
	
	/**
	 * the widget class creates the cybosm widget
	 * 
	 * @see WP_Widget
	 */
	class Cybosm_Widget extends WP_Widget {
	
		/**
		 * initiate the widget
		 */
		public function __construct() {
			parent::__construct(
			// Base ID of your widget
			'cybosm_widget', 
			
			// Widget name will appear in UI
			__('Cybo Social Sharing', 'cybosm' ), 
			
			// Widget description
			array( 'description' => __( 'Displays the social buttons as definded on the settings page.', 'cybosm' ), ) 
			);
		}
		
		/**
		 * the frontend
		 */
		public function widget( $args, $instance ) {
			
			// before and after widget arguments are defined by themes
			echo $args['before_widget'];
			
			// the widget title
			$title = apply_filters( 'widget_title', $instance['title'] );
			if ( ! empty( $title ) ) {
				echo $args['before_title'] . $title . $args['after_title'];
			}
			
               $sticky       = $instance[ 'sticky' ] ? 'sticky' : '';
               $sharing_only = $instance[ 'sharing_only' ] ? true : false;
                              
			// echo out the social buttons
			require_once CYBOSM_PLUGIN_PATH . '/includes/class-cybosm-frontend.php';
			$cybosm = new Cybosm_Frontend();
			$cybosm->display( 'widget_area_cybosm', $sticky, $sharing_only );
			
			// before and after widget arguments are defined by themes
			echo $args['after_widget'];
		}
			
		/**
		 * the backend
		 */
		public function form( $instance ) {
			if ( isset( $instance[ 'title' ] ) ) {
				$title = $instance[ 'title' ];
			} else {
				$title = __( 'The world is social.', 'cybosm' );
			}
			// Widget admin form
			include CYBOSM_PLUGIN_PATH . '/admin/widget-form.php'; 
		}
		
		/*
		 * Updating widget replacing old instances with new
		 */
		public function update( $new_instance, $old_instance ) {
			$instance = array();
			$instance['title']          = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
               $instance['sharing_only']   = $new_instance['sharing_only'];
               $instance['sticky']         = $new_instance['sticky'];
			return $instance;
		}
	} // Class Cybosm_Widget ends here
}


