<?php
/* Google Maps
---------------------------------------------------------- */
class WPBakeryShortCode_GMaps extends WPBakeryShortCode {
}

vc_map( array(
    'name' => __('Vela : Google Maps', 'Vela'),
    'description' => __('Google Maps block.', 'Vela'),
    'base' => 'gmaps',
    'class' => '',
    'controls' => 'full',
    'icon' =>  'icon-wpb-map-pin', 
    'category' => 'VELA',
	'params' => array(
        array(
			'type' => 'wyde_gmaps',
			'heading' => 'Address',
			'param_name' => 'gmaps',
		),
		array(
			'type' => 'textfield',
			'heading' => __( 'Map Height', 'Vela' ),
			'param_name' => 'height',
			'admin_label' => true,
            'value' => '300',
			'description' => __( 'Enter map height in pixels. Example: 300.', 'Vela' )
		),
        array(
                'type' => 'colorpicker',
                'class' => '',
                'heading' => __('Map Color', 'Vela'),
                'param_name' => 'color',
                'value' => '',
                'description' => __('Select map background color. If empty "Theme Color Scheme" will be used.', 'Vela')
        ),
		array(
			'type' => 'attach_image',
			'heading' => __( 'Icon', 'Vela' ),
			'param_name' => 'icon',
			'description' => __( 'To custom your own marker icon, upload or select images from media library.', 'Vela' )
		),
		array(
			'type' => 'textfield',
			'heading' =>  __('Extra CSS Class', 'Vela'),
			'param_name' => 'el_class',
			'description' => __('If you wish to style particular content element differently, then use this field to add a class name.', 'Vela')
		)
	)
));