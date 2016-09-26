<?php
/* Donut Chart
---------------------------------------------------------- */
class WPBakeryShortCode_Donut_Chart extends WPBakeryShortCode {
}

vc_map( array(
            'name' => __('Vela : Donut Chart', 'Vela'),
            'description' => __('Animated donut chart.', 'Vela'),
            'base' => 'donut_chart',
            'class' => '',
            'controls' => 'full',
            'icon' =>  'vela-icon donut-chart-icon', 
            'category' => 'VELA',
            'params' => array(
                    array(
                        'type' => 'textfield',
                        'class' => '',
                        'heading' => __('Title', 'Vela'),
                        'param_name' => 'title',
                        'admin_label' => true,
                        'value' => '',
                        'description' => __('Set chart title.', 'Vela')
                    ),
                    array(
                        'type' => 'textfield',
                        'class' => '',
                        'heading' => __('Chart Value', 'Vela'),
                        'param_name' => 'value',
                        'admin_label' => true,
                        'value' => '',
                        'description' => __('Input chart value here. can be 1 to 100.', 'Vela')
                    ),
                    array(
                        'type' => 'dropdown',
                        'class' => '',
                        'heading' => __('Label Mode', 'Vela'),
                        'param_name' => 'label_mode',
                        'value' => array(
                            'Text' => '', 
                            'Icon' => 'icon', 
                            ),
                        'description' => __('Select a label mode.', 'Vela')
                    ),
                    array(
                        'type' => 'textfield',
                        'class' => '',
                        'heading' => __('Label', 'Vela'),
                        'param_name' => 'label',
                        'value' => '',
                        'description' => __('Set chart label.', 'Vela'),
                        'dependency' => array(
		                    'element' => 'label_mode',
		                    'is_empty' => true,
		                )
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
                        'dependency' => array(
		                    'element' => 'label_mode',
		                    'value' => array('icon')
		                )
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
                        'heading' => __('Style', 'Vela'),
                        'param_name' => 'style',
                        'value' => array(
                            'Default' => '', 
                            'Outline' => 'outline', 
                            'Inline' => 'inline', 
                            ),
                        'description' => __('Select style.', 'Vela')
                    ),
                    array(
                        'type' => 'colorpicker',
                        'class' => '',
                        'heading' => __('Bar Color', 'Vela'),
                        'param_name' => 'bar_color',
                        'value' => '',
                        'description' => __('Select bar color.', 'Vela')
                    ),
                    array(
                        'type' => 'colorpicker',
                        'class' => '',
                        'heading' => __('Border Color', 'Vela'),
                        'param_name' => 'bar_border_color',
                        'value' => '',
                        'description' => __('Select border color.', 'Vela')
                    ),
                    array(
                        'type' => 'colorpicker',
                        'class' => '',
                        'heading' => __('Fill Color', 'Vela'),
                        'param_name' => 'fill_color',
                        'value' => '',
                        'description' => __('Select background color of the whole circle.', 'Vela')
                    ),
                    array(
                        'type' => 'dropdown',
                        'class' => '',
                        'heading' => __('Start', 'Vela'),
                        'param_name' => 'start',
                        'value' => array(
                            'Default' => '', 
                            '90 degree' => '90', 
                            '180 degree' => '180', 
                            '270 degree' => '270', 
                            ),
                        'description' => __('Select the degree to start animate.', 'Vela')
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
	                    'heading' => __( 'Css', 'Vela' ),
	                    'param_name' => 'css',
	                    'group' => __( 'Design options', 'Vela' )
                    ) 
            )
));