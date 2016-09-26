<?php
/* Heading
---------------------------------------------------------- */
class WPBakeryShortCode_Heading extends WPBakeryShortCode {
}

vc_map( array(
    'name' => __('Vela : Heading', 'Vela'),
    'description' => __('Heading text.', 'Vela'),
    'base' => 'heading',
    'class' => '',
    'controls' => 'full',
    'icon' =>  'vela-icon heading-icon', 
    'category' => 'VELA',
    'params' => array(
        array(
            'type' => 'textfield',
            'class' => '',
            'heading' => __('Title', 'Vela'),
            'param_name' => 'title',
            'admin_label' => true,
            'value' => '',
            'description' => __('Enter title text.', 'Vela')
        ),
        array(
            'type' => 'textfield',
            'class' => '',
            'heading' => __('Sub Title', 'Vela'),
            'param_name' => 'sub_title',
            'admin_label' => true,
            'value' => '',
            'description' => __('Enter sub title text.', 'Vela')
        ),
        array(
            'type' => 'dropdown',
            'class' => '',
            'heading' => __('Separator Style', 'Vela'),
            'param_name' => 'style',
            'value' => array(
                '1', 
                '2', 
                '3', 
                '4', 
                '5',
                '6',
                '7',
                '8',
                '9',
                '10',
            ),
            'description' => __('Select a heading separator style.', 'Vela')
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