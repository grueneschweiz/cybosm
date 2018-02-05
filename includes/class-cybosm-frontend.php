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
		public function display( $id, $sticky, $sharing_only ) {
			$options = get_option( CYBOSM_PLUGIN_PREFIX . '_options' );
			               
			$types         = array( 'fb', 'tw', 'gp', 'yt', 'em', 'pt' );
			$shareable     = array( 'fb', 'tw', 'gp', 'em', 'pt' );
			$icons         = array();
			
               if ( $sharing_only ) {
                    $selected_types = $shareable;
               } else {
                    $selected_types = $types;
               }
                              
			foreach( $selected_types as $type ) {
				if ( isset($options[ CYBOSM_PLUGIN_PREFIX . "_{$type}_option" ]) && ! ( 'off' == $options[ CYBOSM_PLUGIN_PREFIX . "_{$type}_option" ] ) ) {
					$icons[ $type ]['type'] = $type;
					$icons[ $type ]['link'] = $this->get_link( $options, $type, $sharing_only );
					$icons[ $type ]['screenreader'] = $this->get_screenreader_text( $options, $type );
				}
			}
			               
               $element_id = empty( $sticky ) ? $id : $id . '-' . $sticky;
               
			include CYBOSM_PLUGIN_PATH . '/frontend/cybosm-icons.php';
		}
		
		/**
		 * Get link of the social icon
		 */
		public function get_link( $options, $type, $share_only ) {
               
			if ( isset($options[ CYBOSM_PLUGIN_PREFIX . "_{$type}_option" ]) && 'visit' == $options[ CYBOSM_PLUGIN_PREFIX . "_{$type}_option" ]
                    && false == $share_only ) {
				return $options[ CYBOSM_PLUGIN_PREFIX . "_{$type}_url" ]; // BREAKPOINT
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
                    
                    case 'em':
                         return 'mailto:?subject=' . get_the_title() . '&amp;body=' .
                                sprintf(
                                   __( "I'd like to recomend you the following page: %s", 'cybosm' ),
                                   urlencode( $current_url )
                                ); // BREAKPOINT
                         break;
                    
                    case 'pt':
                         return 'javascript:window.print()'; // BREAKPOINT
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
                    
                    case 'em':
					return __( 'Email this page to a friend.', 'cybosm' ); // BREAKPOINT
					break;
                    
                    case 'pt':
                         return __( 'Print this page.', 'cybosm' );
                         break;
			}
		}
	}
}