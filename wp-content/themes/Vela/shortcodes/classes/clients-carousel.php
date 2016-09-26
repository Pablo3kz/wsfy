<?php
/* Clients Carousel
---------------------------------------------------------- */
class WPBakeryShortCode_Clients_Carousel extends WPBakeryShortCode {
}

vc_map( array(
    'name' => __('Vela : Clients Carousel', 'Vela'),
    'description' => __('Create beautiful responsive carousel slider.', 'Vela'),
    'base' => 'clients_carousel',
    'class' => '',
    'controls' => 'full',
    'icon' =>  'vela-icon image-carousel-icon', 
    'category' => 'VELA',
    'params' => array(
        array(
            'type' => 'textfield',
            'class' => '',
            'heading' => __('Title', 'Vela'),
            'param_name' => 'title',
            'value' => '',
            'description' => __('Enter text which will be used as widget title. Leave blank if no title is needed.', 'Vela')
        ),
        array(
            'type' => 'attach_images',
            'class' => '',
            'heading' => __('Images', 'Vela'),
            'param_name' => 'images',
            'value' => '',
            'description' => __('Upload or select images from media library.', 'Vela')
        ),
		array(
			'type' => 'dropdown',
			'heading' => __( 'Image Size', 'Vela' ),
			'param_name' => 'image_size',
			'value' => array(
				'Thumbnail (150x150)' => 'thumbnail',
				'Medium (300x300)' => 'medium',
				'Large (640x640)'=> 'large',
                'Full (Original)'=> 'full',
				'Blog Medium (600x340)'=> 'blog-medium',
				'Blog Large (800x450)'=> 'blog-large',
				'Blog Full (1066x600)'=> 'blog-full',
			),
			'description' => __( 'Select image size.', 'Vela' )
		),
        array(
            'type' => 'dropdown',
            'class' => '',
            'heading' => __('Visible Items', 'Vela'),
            'param_name' => 'visible_items',
            'value' => array('auto', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10'),
            'std' => '3',
            'description' => __('The maximum amount of items displayed at a time.', 'Vela')
        ),
        array(
            'type' => 'checkbox',
            'class' => '',
            'param_name' => 'show_navigation',
            'value' => array(
                    'Show Navigation' => 'true'
            ),
            'description' => __('Display "next" and "prev" buttons.', 'Vela')
        ),
        array(
            'type' => 'checkbox',
            'class' => '',
            'param_name' => 'show_pagination',
            'value' => array(
                    'Show Pagination' => 'true'
            ),
            'description' => __('Show pagination.', 'Vela')
        ),
        array(
            'type' => 'checkbox',
            'class' => '',
            'heading' => __('Auto Play', 'Vela'),
            'param_name' => 'auto_play',
            'value' => array(
                    'Auto Play' => 'true'
            ),
            'description' => __('Auto play slide.', 'Vela')
        ),
        array(
            'type' => 'checkbox',
            'class' => '',
            'param_name' => 'loop',
            'value' => array(
                    'Loop' => 'true'
            ),
            'description' => __('Inifnity loop. Duplicate last and first items to get loop illusion.', 'Vela')
        ),
        array(
            'type' => 'wyde_animation',
            'class' => '',
            'heading' => __('Animation', 'Vela'),
            'param_name' => 'animation',
            'description' => __('Select a CSS3 Animation that applies to this element.', 'Vela')
        ),
        array(
            'type' => 'textfield',
            'class' => '',
            'heading' => __('Animation Delay', 'Vela'),
            'param_name' => 'animation_delay',
            'value' => '',
            'description' => __('Defines when the animation will start (in seconds). Example: 0.5, 1, 2, ...', 'Vela'),
            'dependency' => array(
	            'element' => 'animation',
	            'not_empty' => true
            )
        ),
        array(
            'type' => 'textfield',
            'class' => '',
            'heading' => __('Extra CSS Class', 'Vela'),
            'param_name' => 'el_class',
            'value' => '',
            'description' => __('If you wish to style particular content element differently, then use this field to add a class name.', 'Vela')
        )
    )
) );