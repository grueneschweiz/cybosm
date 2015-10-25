<?php

/**
 * lock out script kiddies: die an direct call 
 */
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );


if( ! class_exists( 'Cybosm_Frontend' ) ) {

	class Cybosm_Frontend {
		
		/**
		 * Display the social media icons
		 */
		public function display( $id ) {
			$options = get_option( CYBOSM_PLUGIN_PREFIX . '_options' );
			
			$types = array( 'fb', 'tw', 'gp', 'yt' );
			$icons = array();
			
			foreach( $types as $type ) {
				if ( ! ( 'off' == $options["cybosm_{$type}_option"] ) ) {
					$icons[$type]['type'] = $type;
					$icons[$type]['link'] = $this->get_link( $options, $type );
					$icons[$type]['screenreader'] = $this->get_screenreader_text( $options, $type );
				}
			}
			
			include CYBOSM_PLUGIN_PATH . '/frontend/cybosm-icons.php';
		}
		
		/**
		 * Get link of the social icon
		 */
		public function get_link( $options, $type ) {
			
			if ( 'visit' == $options["cybosm_{$type}_option"] ) {
				return $options["cybosm_{$type}_url"]; // BREAKPOINT
			}
			
			global $wp;
			$current_url = home_url( add_query_arg( array(), $wp->request ) );
			
			switch ( $type ) {
				case 'fb':
					return 'https://www.facebook.com/sharer/sharer.php?u=' . urlencode( $current_url ); // BREAKPOINT
					break;
				
				case 'tw':
					if ( ! empty( $options['cybosm_tw_via'] ) ) {
						$user = str_replace( '@', '', $options['cybosm_tw_via'] );
						$user = str_replace( ' ', '', $user );
						$via = '&via=' . $user;
					} else {
						$via = '';
					}
					return 'https://twitter.com/share?url=' . urlencode( $current_url ) . $via ; // BREAKPOINT
					break;
				
				case 'gp':
					return 'https://plus.google.com/share?url=' . urlencode( $current_url ); // BREAKPOINT
					break;
			}
		}
		
		/**
		 * get screenreader text
		 */
		public function get_screenreader_text( $options, $type ) {
			
			$o = $options["cybosm_{$type}_option"];
			
			switch ( $type ) {
				case 'fb':
					return 'share' == $o ? __( 'Share this page on facebook', 'cybosm' ) : __( 'Visit us on facebook', 'cybosm' ); // BREAKPOINT
					break;
				
				case 'tw':
					return 'share' == $o ? __( 'Share this page on twitter', 'cybosm' ) : __( 'Visit us on twitter', 'cybosm' ); // BREAKPOINT
					break;
				
				case 'gp':
					return 'share' == $o ? __( 'Share this page on google+', 'cybosm' ) : __( 'Visit us on google+', 'cybosm' ); // BREAKPOINT
					break;
				
				case 'yt':
					return __( 'Check out our youtube chanel', 'cybosm' ); // BREAKPOINT
					break;
			}
		}
	}
}