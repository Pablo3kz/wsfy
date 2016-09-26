<?php
/* Link Button
---------------------------------------------------------- */
class WPBakeryShortCode_Link_Button extends WPBakeryShortCode {
}

vc_map( array(
    'name' => __('Vela : Link Button', 'Vela'),
    'description' => __('Add link button with icon.', 'Vela'),
    'base' => 'link_button',
    'class' => '',
    'controls' => 'full',
    'icon' =>  'vela-icon link-button-icon', 
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
                'heading' => __('Style', 'Vela'),
                'param_name' => 'style',
                'value' => array(
                    'Round' =>'', 
                    'Square' => 'square', 
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
			    'type' => 'colorpicker',
			    'heading' => __( 'Hover Color', 'Vela' ),
			    'param_name' => 'hover_color',
			    'description' => __( 'Select hover text color.', 'Vela' ),
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