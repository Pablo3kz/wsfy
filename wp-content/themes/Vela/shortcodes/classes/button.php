<?php
/* Button
---------------------------------------------------------- */  
class WPBakeryShortCode_Button extends WPBakeryShortCode {
}

vc_map( array(
            'name' => __('Vela : Button', 'Vela'),
            'description' => __('Add button.', 'Vela'),
            'base' => 'button',
            'class' => '',
            'controls' => 'full',
            'icon' =>  'vela-icon button-icon', 
            'category' => 'VELA',
            'params' => array(
                    array(
                        'type' => 'textfield',
                        'class' => '',
                        'heading' => __('Title', 'Vela'),
                        'param_name' => 'title',
                        'admin_label' => true,
                        'value' => '',
                        'description' => __('Text on the button.', 'Vela')
                    ),
                    array(
			            'type' => 'vc_link',
			            'heading' => __( 'URL (Link)', 'Vela' ),
			            'param_name' => 'link',
			            'description' => __( 'Set a button link.', 'Vela' )
		            ),
                    array(
                        'type' => 'dropdown',
                        'class' => '',
                        'heading' => __('Style', 'Vela'),
                        'param_name' => 'style',
                        'value' => array(
                            'Square' => '', 
                            'Round' => 'round', 
                            'Square Outline' => 'outline', 
                            'Round Outline' => 'round-outline', 
                        ),
                        'description' => __('Select button style.', 'Vela')
                    ),
                    array(
                        'type' => 'dropdown',
                        'class' => '',
                        'heading' => __('Size', 'Vela'),
                        'param_name' => 'size',
                        'value' => array(
                            'Small' => '', 
                            'Large' =>'large', 
                        ),
                        'description' => __('Select button size.', 'Vela')
                    ),
                    array(
			            'type' => 'colorpicker',
			            'heading' => __( 'Button Color', 'Vela' ),
			            'param_name' => 'color',
			            'description' => __( 'Select button color.', 'Vela' ),
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
));