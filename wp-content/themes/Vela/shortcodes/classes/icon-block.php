<?php 
/* Icon Block
---------------------------------------------------------- */
class WPBakeryShortCode_Icon_Block extends WPBakeryShortCode {
}

vc_map( array(
            'name' => __('Vela : Icon Block', 'Vela'),
            'description' => __('Add icon block.', 'Vela'),
            'base' => 'icon_block',
            'class' => '',
            'controls' => 'full',
            'icon' =>  'vela-icon icon-block-icon', 
            'category' => 'VELA',
            'params' => array(
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
                    'type' => 'dropdown',
                    'class' => '',
                    'heading' => __('Icon Size', 'Vela'),
                    'param_name' => 'size',
                    'value' => array(
                        'Small' => 'small', 
                        'Medium' => 'medium', 
                        'Large' => 'large',
                        'Extra Large' => 'xlarge'
                    ),
                    'description' => __('Select icon size.', 'Vela')
                  ),
                  array(
                    'type' => 'dropdown',
                    'class' => '',
                    'heading' => __('Icon Style', 'Vela'),
                    'param_name' => 'style',
                    'value' => array(
                        'Circle' => 'circle', 
                        'Square' => 'square',
                    ),
                    'description' => __('Select icon style.', 'Vela')
                  ),
                  array(
                    'type' => 'colorpicker',
                    'class' => '',
                    'heading' => __('Background Color', 'Vela'),
                    'param_name' => 'color',
                    'description' => __('Select icon background color. If empty "Theme Color Scheme" will be used.', 'Vela')
                  ),
                  array(
                    'type' => 'dropdown',
                    'class' => '',
                    'heading' => __('Hover Effect', 'Vela'),
                    'param_name' => 'hover',
                    'value' => array(
                        'Zoom In' => 1,
                        'Zoom Out'  => 2,
                        'Pulse'  => 3,
                        'Left to Right'  => 4,
                        'Right to Left' =>5,
                        'Bottom to Top' =>6,
                        'Top to Bottom' =>7
                    ),
                    'description' => __('Select icon hover effect.', 'Vela')
                  ),
                  array(
                    'type' => 'vc_link',
                    'class' => '',
                    'heading' => __('URL', 'Vela'),
                    'param_name' => 'link',
                    'description' => __('Icon link.', 'Vela')
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
                  ),
		          array(
			        'type' => 'css_editor',
			        'heading' => __( 'Css', 'js_composer' ),
			        'param_name' => 'css',
			        'group' => __( 'Design options', 'js_composer' )
		          )
            )
) );