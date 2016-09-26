<?php
/* Pricing Box
---------------------------------------------------------- */
class WPBakeryShortCode_Pricing_Box extends WPBakeryShortCode {
}

vc_map( array(
    'name' => __('Vela : Pricing Box', 'Vela'),
    'description' => __('Create pricing box.', 'Vela'),
    'base' => 'pricing_box',
    'class' => '',
    'controls' => 'full',
    'icon' =>  'vela-icon pricing-box-icon', 
    'category' => 'VELA',
    'params' => array(
            array(
                'type' => 'textfield',
                'class' => '',
                'heading' => __('Title', 'Vela'),
                'param_name' => 'heading',
                'admin_label' => true,
                'value' => '',
                'description' => __('Enter the heading or package name.', 'Vela')
            ),
            array(
                'type' => 'textfield',
                'class' => '',
                'heading' => __('Sub Heading', 'Vela'),
                'param_name' => 'sub_heading',
                'value' => '',
                'description' => __('Enter a short description.', 'Vela')
            ),
            array(
                'type' => 'textfield',
                'class' => '',
                'heading' => __('Price', 'Vela'),
                'param_name' => 'price',
                'admin_label' => true,
                'value' => '',
                'description' => __('Enter a price. Example: $100', 'Vela')
            ),
            array(
                'type' => 'textfield',
                'class' => '',
                'heading' => __('Price Unit', 'Vela'),
                'param_name' => 'price_unit',
                'value' => '',
                'description' => __('Enter a price unit. Example: per month', 'Vela')
            ),
            array(
                'type' => 'textarea_html',
                'class' => '',
                'heading' => __('Features', 'Vela'),
                'param_name' => 'content',
                'value' => '',
                'description' => __('Enter the features list or table description.', 'Vela')
            ),
            array(
                'type' => 'textfield',
                'class' => '',
                'heading' => __('Button Text', 'Vela'),
                'param_name' => 'button_text',
                'value' => '',
                'description' => __('Enter a button text.', 'Vela')
            ),
            array(
                'type' => 'vc_link',
                'class' => '',
                'heading' => __('Button Link', 'Vela'),
                'param_name' => 'link',
                'value' => '',
                'description' => __('Select or enter the link for button.', 'Vela')
            ),
            array(
                'type' => 'colorpicker',
                'class' => '',
                'heading' => __('Background Color', 'Vela'),
                'param_name' => 'bg_color',
                'value' => '',
                'description' => __('Select a background color.', 'Vela')
            ),
            array(
                'type' => 'colorpicker',
                'class' => '',
                'heading' => __('Text Color', 'Vela'),
                'param_name' => 'text_color',
                'value' => '',
                'description' => __('Select a text color.', 'Vela')
            ),
            array(
                'type' => 'checkbox',
                'class' => '',
                'heading' => __('Featured Box', 'Vela'),
                'param_name' => 'featured',
                'value' => array(
                        'Make this box as featured' => 'true'
                )
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