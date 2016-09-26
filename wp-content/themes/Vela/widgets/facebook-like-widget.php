<?php
class Vela_Widget_Facebook_Like extends WP_Widget {

	function __construct() {
		parent::__construct(
            'vela-facebook-like', 
            __('Vela: Facebook Like', 'Vela'), 
            array(
                'classname' => 'vela_widget_facebook_like', 
                'description' => "Facebook Like box." 
            )
        );

	}

	function widget($args, $instance)
	{
		extract($args);

		$title = apply_filters('widget_title', $instance['title']);
		$page_url = $instance['page_url'];
		$width = $instance['width'];
		$color_scheme = $instance['color_scheme'];
		$show_faces = isset($instance['show_faces']) ? 'true' : 'false';
		$show_stream = isset($instance['show_stream']) ? 'true' : 'false';
		$show_header = isset($instance['show_header']) ? 'true' : 'false';
		$height = '35';

		if($show_faces == 'true') {
			$height = '210';
		}

		if($show_stream == 'true') {
			$height = '485';
		}

		if($show_stream == 'true' && $show_faces == 'true' && $show_header == 'true') {
			$height = '510';
		}

		if($show_stream == 'true' && $show_faces == 'true' && $show_header == 'false') {
			$height = '510';
		}

		if($show_header == 'true') {
			$height = $height + 30;
		}

		echo $before_widget;

		if($title) {
			echo $before_title. esc_html( $title ) .$after_title;
		}

		if( !empty( $page_url )): ?>
        <div class="vela-facebook-box">
		    <iframe src="http<?php echo (is_ssl())? 's' : ''; ?>://www.facebook.com/plugins/likebox.php?href=<?php echo urlencode( esc_url( $page_url ) ); ?>&amp;width=<?php echo absint( $width ); ?>&amp;colorscheme=<?php echo esc_attr( $color_scheme ); ?>&amp;show_faces=<?php echo esc_attr( $show_faces ); ?>&amp;stream=<?php echo esc_attr( $show_stream ); ?>&amp;header=<?php echo esc_attr( $show_header ); ?>&amp;height=<?php echo absint( $height ); ?>&amp;force_wall=true<?php if($show_faces == 'true'): ?>&amp;connections=8<?php endif; ?>" style="width:<?php echo absint( $width ); ?>px; height: <?php echo absint( $height ); ?>px;"></iframe>
		</div>
        <?php endif;

		echo $after_widget;
	}

	function update($new_instance, $old_instance)
	{
		$instance = $old_instance;

		$instance['title'] = sanitize_text_field( $new_instance['title'] );
		$instance['page_url'] = esc_url( $new_instance['page_url'] );
		$instance['width'] = absint( $new_instance['width'] );
		$instance['color_scheme'] = $new_instance['color_scheme'];
		$instance['show_faces'] = $new_instance['show_faces'];
		$instance['show_stream'] = $new_instance['show_stream'];
		$instance['show_header'] = $new_instance['show_header'];

		return $instance;
	}

	function form($instance)
	{
		$defaults = array('title' => 'Find us on Facebook', 'page_url' => '', 'width' => '268', 'color_scheme' => 'light', 'show_faces' => 'on', 'show_stream' => false, 'show_header' => false);
		$instance = wp_parse_args((array) $instance, $defaults); ?>

		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>">Title:</label>
			<input class="widefat" type="text" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('page_url'); ?>">Facebook Page URL:</label>
			<input class="widefat" type="text" id="<?php echo $this->get_field_id('page_url'); ?>" name="<?php echo $this->get_field_name('page_url'); ?>" value="<?php echo esc_url( $instance['page_url'] ); ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('width'); ?>">Width:</label>
			<input class="widefat" type="text" style="width: 50px;" id="<?php echo $this->get_field_id('width'); ?>" name="<?php echo $this->get_field_name('width'); ?>" value="<?php echo esc_attr( $instance['width'] ); ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('color_scheme'); ?>">Color Scheme:</label>
			<select id="<?php echo $this->get_field_id('color_scheme'); ?>" name="<?php echo $this->get_field_name('color_scheme'); ?>" class="widefat" style="width:100%;">
				<option <?php if ('light' == $instance['color_scheme']) echo 'selected="selected"'; ?>>light</option>
				<option <?php if ('dark' == $instance['color_scheme']) echo 'selected="selected"'; ?>>dark</option>
			</select>
		</p>

		<p>
			<input class="checkbox" type="checkbox" <?php checked($instance['show_faces'], 'on'); ?> id="<?php echo $this->get_field_id('show_faces'); ?>" name="<?php echo $this->get_field_name('show_faces'); ?>" />
			<label for="<?php echo $this->get_field_id('show_faces'); ?>">Show faces</label>
		</p>

		<p>
			<input class="checkbox" type="checkbox" <?php checked($instance['show_stream'], 'on'); ?> id="<?php echo $this->get_field_id('show_stream'); ?>" name="<?php echo $this->get_field_name('show_stream'); ?>" />
			<label for="<?php echo $this->get_field_id('show_stream'); ?>">Show stream</label>
		</p>

		<p>
			<input class="checkbox" type="checkbox" <?php checked($instance['show_header'], 'on'); ?> id="<?php echo $this->get_field_id('show_header'); ?>" name="<?php echo $this->get_field_name('show_header'); ?>" />
			<label for="<?php echo $this->get_field_id('show_header'); ?>">Show facebook header</label>
		</p>
	<?php
	}
}
add_action('widgets_init', 'vela_widget_facebook_like_load');

function vela_widget_facebook_like_load()
{
	register_widget('Vela_Widget_Facebook_Like');
}