<?php
class Vela_Widget_Flickr extends WP_Widget {

	function __construct() {
		parent::__construct(
            'vela-flickr', 
            __('Vela: Flickr', 'Vela'), 
            array(
                'classname' => 'vela_widget_flickr', 
                'description' => "Displays a Flickr photo stream."
            )
        );

	}

	function widget($args, $instance) {

        extract( $args );

		$title = apply_filters('widget_title', $instance['title']);
		$flickr_id = strip_tags( $instance['flickr_id'] );
		$count = intval( $instance['count'] );
	
        echo $before_widget;

		if ( $title ) echo $before_title .esc_html( $title ) . $after_title; 
	
		echo '<div class="vela-flickr" data-id="'.esc_attr( $flickr_id ).'" data-count="'.esc_attr( $count ).'">';
		echo '</div>';
		
		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		
        $instance['title']			= sanitize_text_field( $new_instance['title'] );
		$instance['flickr_id'] 		= strip_tags( $new_instance['flickr_id'] );
		$instance['count'] 			= intval( $new_instance['count'] );

		return $instance;
	}

	function form( $instance ) {
		
        // Set up the default form values.
		$defaults = array(
			'title'			=> 'Flickr Stream',
			'flickr_id'		=> '',
			'count'			=> 9
		);

		/* Merge the user-selected arguments with the defaults. */
		$instance = wp_parse_args( (array) $instance, $defaults );
        
        ?>

		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>">Title:</label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
        </p>


        <div><label for="<?php echo $this->get_field_id( 'flickr_id' ); ?>">Flickr ID:</label>
		<input id="<?php echo $this->get_field_id( 'flickr_id' ); ?>" name="<?php echo $this->get_field_name( 'flickr_id' ); ?>" type="text" value="<?php echo esc_attr( $instance['flickr_id'] ); ?>" />
        <p>If you don\'t know your ID, go to <a href="http://idgettr.com/" target="_blank">Flickr NSID Lookup</a>.</p>
        </div>

		<p><label for="<?php echo $this->get_field_id( 'count' ); ?>">Number of images to show:</label>
		<input id="<?php echo $this->get_field_id( 'count' ); ?>" name="<?php echo $this->get_field_name( 'count' ); ?>" type="text" value="<?php echo esc_attr( $instance['count'] ); ?>" size="3" />
        </p>
<?php
	}
}
add_action('widgets_init', 'vela_widget_flickr_load');

function vela_widget_flickr_load()
{
	register_widget('Vela_Widget_Flickr');
}