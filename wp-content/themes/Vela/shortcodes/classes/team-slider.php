<?php
/* Team Members Slider
---------------------------------------------------------- */
class WPBakeryShortCode_Team_Slider extends WPBakeryShortCode {
}

vc_map( array(
    'name' => __('Vela : Team Members Slider', 'Vela'),
    'description' => __('Team Members in slider view.', 'Vela'),
    'base' => 'team_slider',
    'class' => '',
    'controls' => 'full',
    'icon' =>  'vela-icon team-slider-icon', 
    'category' => 'VELA',
    'params' => array(
            array(
                'type' => 'textfield',
                'class' => '',
                'heading' => __('Title', 'Vela'),
                'param_name' => 'title',
                'admin_label' => true,
                'value' => '',
                'description' => __('Enter text which will be used as widget title. Leave blank if no title is needed.', 'Vela')
            ),
            array(
                'type' => 'textarea_html',
                'class' => '',
                'heading' => __('Description', 'Vela'),
                'param_name' => 'content',
                'value' => '',
                'description' => __('Enter text which will be used as widget description. Leave blank if no description is needed.', 'Vela')
            ),
            array(
			    'type' => 'loop',
			    'heading' => __( 'Custom Posts', 'Vela' ),
			    'param_name' => 'posts_query',
			    'settings' => array(
                    'post_type'  => array('hidden' => true),
                    'categories'  => array('hidden' => true),
                    'tags'  => array('hidden' => true),
				    'size' => array( 'hidden' => true),
				    'order_by' => array( 'value' => 'date' ),
				    'order' => array( 'value' => 'DESC' ),
			    ),
			    'description' => __( 'Create WordPress loop, to populate content from your site.', 'js_composer' )
		    ),
            array(
                'type' => 'textfield',
                'class' => '',
                'heading' => __('Post Count', 'Vela'),
                'param_name' => 'count',
                'value' => '10',
                'description' => __('Number of posts to show.', 'Vela')
            ),
            array(
                'type' => 'dropdown',
                'class' => '',
                'heading' => __('Visible Items', 'Vela'),
                'param_name' => 'visible_items',
                'value' => array('1', '2', '3', '4', '5'),
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