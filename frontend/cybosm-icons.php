<div class="cybosm-wrapper" id="<?php echo esc_attr( $element_id ); ?>">
	<?php foreach ($icons as $icon): ?>
		<a href="<?php echo esc_url( $icon['link'] ); ?>" class="cybosm-icon cybosm-<?php echo $icon['type']; ?>-icon" target="_blank">
			<span class="screen-reader-text"><?php echo $icon['screenreader']; ?></span>
		</a>
	<?php endforeach; ?>
</div>
<div style="clear: both;"></div>
