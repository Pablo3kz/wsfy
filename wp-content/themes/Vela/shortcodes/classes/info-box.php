<?php
/* Info Box
---------------------------------------------------------- */
class WPBakeryShortCode_Info_Box extends WPBakeryShortCode {

}

vc_map( array(
    'name' => __('Vela : Info Box', 'Vela'),
    'description' => __('Content box with icon.', 'Vela'),
    'base' => 'info_box',
    'class' => '',
    'controls' => 'full',
    'icon' =>  'vela-icon info-box-icon', 
    'category' => 'VELA',
    'params' => array(
            array(
                'type' => 'textfield',
                'class' => '',
                'heading' => __('Title', 'Vela'),
                'param_name' => 'title',
                'admin_label' => true,
                'value' => '',
                'description' => __('Set info box title.', 'Vela')
            ),
            array(
                'type' => 'textarea_html',
                'class' => '',
                'heading' => __('Content', 'Vela'),
                'param_name' => 'content',
                'value' => '',
                'description' => __('Enter your content.', 'Vela')
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
                'type' => 'dropdown',
                'class' => '',
                'heading' => __('Icon Size', 'Vela'),
                'param_name' => 'icon_size',
                'value' => array(
                    'Small' => 'small', 
                    'Medium' => 'medium', 
                    'Large' => 'large'
                ),
                'std' => 'small',
                'description' => __('Select icon size.', 'Vela')
            ),
            array(
                'type' => 'dropdown',
                'class' => '',
                'heading' => __('Border Style', 'Vela'),
                'param_name' => 'style',
                'value' => array(
                    __('Square', 'Vela') => 'square', 
                    __('Circle', 'Vela') => 'circle',
                    __('None', 'Vela') => 'none',
                ),
                'std' => 'square',
                'description' => __('Select icon border style.', 'Vela')
            ),
            array(
                'type' => 'dropdown',
                'class' => '',
                'heading' => __('Icon Position', 'Vela'),
                'param_name' => 'icon_position',
                'value' => array(
                        __('Align Left', 'Vela') => 'left', 
                        __('Align Top', 'Vela') => 'top',
                        __('Align Right', 'Vela') => 'right',
                        ),
                'std' => 'top',
                'description' => __('Select icon alignment.', 'Vela')
            ),
            array(
                    'type' => 'colorpicker',
                    'class' => '',
                    'heading' => __('Color', 'Vela'),
                    'param_name' => 'color',
                    'value' => '',
                    'description' => __('Select an icon color.', 'Vela')
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
) );