<?php 
/* Counter Box
---------------------------------------------------------- */
class WPBakeryShortCode_Counter_Box extends WPBakeryShortCode {
}

vc_map( array(
            'name' => __('Vela : Counter Box', 'Vela'),
            'description' => __('Animated numbers.', 'Vela'),
            'base' => 'counter_box',
            'class' => '',
            'controls' => 'full',
            'icon' =>  'vela-icon counter-box-icon', 
            'category' => 'VELA',
            'params' => array(
                    array(
                        'type' => 'textfield',
                        'class' => '',
                        'heading' => __('Title', 'Vela'),
                        'param_name' => 'title',
                        'admin_label' => true,
                        'value' => '',
                        'description' => __('Set counter title.', 'Vela')
                    ),
                    array(
			                'type' => 'dropdown',
			                'heading' => __( 'Icon Set', 'Vela' ),
			                'value' => array(
				                'Font Awesome' => '',
				                'Open Iconic' => 'openiconic',
				                'Typicons' => 'typicons',
				                'Entypo' => 'entypo',
				                'Linecons' => 'linecons',
			                ),
			                'admin_label' => true,
			                'param_name' => 'icon_set',
			                'description' => __('Select an icon set.', 'Vela'),
		                ),
		            array(
			            'type' => 'iconpicker',
			            'heading' => __( 'Icon', 'Vela' ),
			            'param_name' => 'icon',
			            'value' => '', 
			            'settings' => array(
				            'emptyIcon' => true, 
				            'iconsPerPage' => 4000, 
			            ),
                        'description' => __('Select an icon.', 'Vela'),
			            'dependency' => array(
				            'element' => 'icon_set',
				            'is_empty' => true,
			            ),
		            ),
		            array(
			            'type' => 'iconpicker',
			            'heading' => __( 'Icon', 'Vela' ),
			            'param_name' => 'icon_openiconic',
			            'value' => '', 
			            'settings' => array(
				            'emptyIcon' => true, 
				            'type' => 'openiconic',
				            'iconsPerPage' => 4000, 
			            ),
                        'description' => __('Select an icon.', 'Vela'),
			            'dependency' => array(
				            'element' => 'icon_set',
				            'value' => 'openiconic',
			            ),
		            ),
		            array(
			            'type' => 'iconpicker',
			            'heading' => __( 'Icon', 'Vela' ),
			            'param_name' => 'icon_typicons',
			            'value' => '', 
			            'settings' => array(
				            'emptyIcon' => true, 
				            'type' => 'typicons',
				            'iconsPerPage' => 4000, 
			            ),
			            'description' => __('Select an icon.', 'Vela'),
			            'dependency' => array(
				            'element' => 'icon_set',
				            'value' => 'typicons',
			            ),
		            ),
		            array(
			            'type' => 'iconpicker',
			            'heading' => __( 'Icon', 'Vela' ),
			            'param_name' => 'icon_entypo',
			            'value' => '', 
			            'settings' => array(
				            'emptyIcon' => true, 
				            'type' => 'entypo',
				            'iconsPerPage' => 4000,
			            ),
                        'description' => __('Select an icon.', 'Vela'),
			            'dependency' => array(
				            'element' => 'icon_set',
				            'value' => 'entypo',
			            ),
		            ),
		            array(
			            'type' => 'iconpicker',
			            'heading' => __( 'Icon', 'Vela' ),
			            'param_name' => 'icon_linecons',
			            'value' => '',
			            'settings' => array(
				            'emptyIcon' => true, 
				            'type' => 'linecons',
				            'iconsPerPage' => 4000,
			            ),
			            'description' => __('Select an icon.', 'Vela'),
			            'dependency' => array(
				            'element' => 'icon_set',
				            'value' => 'linecons',
			            ),
		            ),
                    array(
                        'type' => 'textfield',
                        'class' => '',
                        'heading' => __('Start From', 'Vela'),
                        'param_name' => 'start',
                        'value' => '0',
                        'description' => __('Set start value.', 'Vela')
                    ),
                    array(
                        'type' => 'textfield',
                        'class' => '',
                        'heading' => __('Value', 'Vela'),
                        'param_name' => 'value',
                        'value' => '100',
                        'description' => __('Set counter value.', 'Vela')
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