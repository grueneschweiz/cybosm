<div class="wrap">
	<h2><?php _e( 'Cybo Social Sharing Options', 'cybosm' ); ?></h2>
	
	<form action="options.php" method="post">
			
			<?php
				settings_fields( CYBOSM_PLUGIN_PREFIX . '_options' );
				do_settings_sections( CYBOSM_PLUGIN_PREFIX . '_options' );
				submit_button();
			?>
			
	</form>
</div>