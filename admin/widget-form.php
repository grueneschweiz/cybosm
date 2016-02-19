<p>
     <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
     <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
</p>
<p>
     <input class="checkbox" type="checkbox" <?php checked( $instance[ 'sharing_only' ], 'on' ); ?> id="<?php echo $this->get_field_id( 'sharing_only' ); ?>" name="<?php echo $this->get_field_name( 'sharing_only' ); ?>" /> 
     <label for="<?php echo $this->get_field_id( 'sharing_only' ); ?>"><?php _e( 'Use sharebuttons only.', 'cybosm' ); ?></label>
</p>
<p>
     <input class="checkbox" type="checkbox" <?php checked( $instance[ 'sticky' ], 'on' ); ?> id="<?php echo $this->get_field_id( 'sticky' ); ?>" name="<?php echo $this->get_field_name( 'sticky' ); ?>" /> 
     <label for="<?php echo $this->get_field_id( 'sticky' ); ?>"><?php _e( "Don't jump to the right on big screens.", 'cybosm' ); ?></label>
</p>
