<?php   
/* Blog_Posts
---------------------------------------------------------- */
class WPBakeryShortCode_Blog_Posts extends WPBakeryShortCode {

    public function get_masonry_layout(){
        return array('width2 height2', '', 'height2', 'height2', '', 'width2', 'item-h', '', 'width2 height2', 'height2', 'width2', 'height2', '', 'item-h', '', 'height2', 'width2 height2', 'height2', 'width2', '', 'item-h');
    }

}

vc_map( array(
        'name' => __('Vela : Blog Posts', 'Vela'),
        'description' => __('Displays Blog Posts list.', 'Vela'),
        'base' => 'blog_posts',
        'class' => '',
        'controls' => 'full',
        'icon' =>  'vela-icon blog-posts-icon', 
        'category' => 'VELA',
        'params' => array(
                array(
                    'type' => 'textfield',
                    'class' => '',
                    'heading' => __('Title', 'Vela'),
                    'param_name' => 'title',
                    'value' => '',
                    'admin_label' => true,
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
                    'type' => 'dropdown',
                    'class' => '',
                    'heading' => __('Layout', 'Vela'),
                    'param_name' => 'view',
                    'admin_label' => true,
                    'value' => array(
                        'Large Image' => 'large', 
                        'Medium Image' => 'medium', 
                        'Masonry' => 'masonry'
                    ),
                    'description' => __('Select blog posts view.', 'Vela')
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
                        '4'),
                    'std' => '3',
                    'description' => __('Select the number of columns.', 'Vela'),
                    'dependency' => array(
				    'element' => 'view',
				    'value' => array( 'masonry')
			        )
                ),
                array(
                    'type' => 'dropdown',
                    'class' => '',
                    'heading' => __('Pagination Type', 'Vela'),
                    'param_name' => 'pagination',
                    'value' => array(
                        'Hide' => '0',
                        'Numeric Pagination' => '1', 
                        'Infinite Scroll' => '2',
                        'Next and Previous' => '3'
                        ),
                    'description' => __('Select the pagination type for blog page.', 'Vela')
                ),
                array(
			        'type' => 'loop',
			        'heading' => __( 'Custom Posts', 'Vela' ),
			        'param_name' => 'posts_query',
			        'settings' => array(
                        'post_type'  => array('hidden' => true),
                        'tax_query'  => array('hidden' => true),
				        'size' => array( 'hidden' => true),
				        'order_by' => array( 'value' => 'date' ),
				        'order' => array( 'value' => 'DESC' ),
			        ),
			        'description' => __( 'Create WordPress loop, to populate content from your site.', 'js_composer' )
		        ),
                array(
                    'type'      => 'textfield',
                    'class' => '',
                    'heading'     => __('Number of Posts per Page', 'Vela'),
                    'param_name'    => 'count',
                    'description'  => __('Enter the number of posts per page.', 'Vela'),
                    'value'   => '10'
                        
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