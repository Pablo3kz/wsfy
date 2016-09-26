<?php
/* Posts Grid
---------------------------------------------------------- */
class WPBakeryShortCode_Posts_Grid extends WPBakeryShortCode {

    public function get_masonry_layout(){
        return array('width2 height2', '', 'height2', 'height2', '', 'width2', 'item-h', '', 'width2 height2', 'height2', 'width2', 'height2', '', 'item-h', '', 'height2', 'width2 height2', 'height2', 'width2', '', 'item-h');
    }

}

vc_map( array(
    'name' => __('Vela : Posts Grid', 'Vela'),
    'description' => __('Posts in grid view.', 'Vela'),
    'base' => 'posts_grid',
    'class' => '',
    'controls' => 'full',
    'icon' =>  'icon-wpb-application-icon-large', 
    'category' => 'VELA',
    'params' => array(
            array(
                'type' => 'textfield',
                'class' => '',
                'heading' => __('Title', 'Vela'),
                'param_name' => 'title',
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
				    'size' => array( 'hidden' => true),
				    'order_by' => array( 'value' => 'date' ),
				    'order' => array( 'value' => 'DESC' ),
			    ),
			    'description' => __( 'Create WordPress loop, to populate content from your site.', 'js_composer' )
		    ),
            array(
                'type' => 'dropdown',
                'class' => '',
                'heading' => __('Columns', 'Vela'),
                'param_name' => 'columns',
                'admin_label' => true,
                'value' => array(
                    '1', 
                    '2', 
                    '3', 
                    '4'
                ),
                'std' => '3',
                'description' => __('Select the number of grid columns.', 'Vela')
            ),
            array(
                'type' => 'textfield',
                'class' => '',
                'heading' => __('Post Count', 'Vela'),
                'param_name' => 'count',
                'value' => '9',
                'description' => __('Number of posts to show.', 'Vela')
            ),
            array(
                'type' => 'checkbox',
                'class' => '',
                'param_name' => 'hide_filter',
                'value' => array(
                        'Hide Filter' => 'true'
                ),
                'description' => __('Display animated category filter to your grid.', 'Vela')
            ),
            array(
                'type' => 'checkbox',
                'class' => '',
                'param_name' => 'show_more',
                'value' => array(
                        'Load More Button' => 'true'
                ),
                'description' => __('Display load more button.', 'Vela')
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