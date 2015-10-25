/**
 * jQuery wrapper
 */
( function( $ ) {
	var Settings = new Settings();
	
	
	/**
	 * handels the settings stuff
	 */
	function Settings() {
		
		var self = this;
		
		/*
		 * adds the click events
		 */
		this.initiateVisibilitySwitcher = function initiateVisibilitySwitcher() {
			var obj = [ 'fb', 'tw', 'gp', 'yt' ];
			
			$.each( obj, function( index, value ) {
				$( '.cybosm_' + value + '_option' ).click( function() {
					self.setState();
				});
			} );
		};
		
		/*
		 * setState
		 */
		this.setState = function setState() {
			var obj = [ 'fb', 'tw', 'gp', 'yt' ];
			
			$.each( obj, function( index, value ) {
				if ( $( '#cybosm_' + value + '_option_visit' ).is( ':checked' ) ) {
					$( '#cybosm_' + value + '_url' ).parent().parent().show();
				} else {
					$( '#cybosm_' + value + '_url' ).parent().parent().hide();
				}
			} );
			
			if ( $( '#cybosm_tw_option_share' ).is( ':checked' ) ) {
				$( '#cybosm_tw_via' ).parent().parent().show();
			} else {
				$( '#cybosm_tw_via' ).parent().parent().hide();
			}
		};
		
		/*
		 * adds the click events that show the input field
		 */
		this.addShowClickEvent = function addShowClickEvent( obj ) {
			$( '#cybosm_'+obj+'_option_visit' ).click( function() { 
				$( '#cybosm_'+obj+'_url' ).parent().parent().show();
			} );
		};
		
		/*
		 * adds the click events that hide the input field
		 */
		this.addHideClickEvent = function addHideClickEvent( obj ) {
			$( '#cybosm_'+obj+'_option_share, #cybosm_'+obj+'_option_off' ).click( function() { 
				$( '#cybosm_'+obj+'_url' ).parent().parent().hide();
			} );
		};
	}
	
	
	/**
	 * fires after DOM is loaded
	 */
	$( document ).ready( function() {
		Settings.initiateVisibilitySwitcher();
		Settings.setState();
	});
	
} )( jQuery );