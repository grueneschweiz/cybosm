/**
 * jQuery wrapper
 */
( function( $ ) {
	var Position = new Position();
	
	/**
	 * handels the settings stuff
	 */
	function Position() {
		
		var self = this;
		
		/*
		 * Set initial position
		 */
		this.setup = function setup( crutialWith ) {
			if ( crutialWith <= $( window ).width() ) {
				self.fullscreenify();
			} else {
				self.widgetize();
			}
		};
		
		/*
		 * display the sharebuttons as widget
		 */
		this.widgetize = function widgetize() {
			$( '#widget_area_cybosm' ).parent().show();
			$( '#cybosm-side' ).hide();
		};
		
		/*
		 * display sharebuttons on the side
		 */
		this.fullscreenify = function fullscreenify() {
			if ( 0 < $( '#cybosm-side' ).length ) {
				$( '#widget_area_cybosm' ).parent().hide();
				$( '#cybosm-side' ).show();
			} else {
				$( 'body' ).append( '<div id="cybosm-side">' + $( '#widget_area_cybosm' ).html() + '</div>' );
				$( '#widget_area_cybosm' ).parent().hide();
			}
		};
	}
	
	
	/**
	 * fires after DOM is loaded
	 */
	$( document ).ready( function() {
		Position.setup( 1080 );
	});
	
	$( window ).resize( function() {
		Position.setup( 1080 );
	} );
	
} )( jQuery );