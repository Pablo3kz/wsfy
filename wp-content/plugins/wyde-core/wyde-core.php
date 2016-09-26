<?php
/*
Plugin Name: Wyde Core
Plugin URI: http://www.wydethemes.com
Description: Core Plugin for Wyde Themes
Version: 1.7.0
Author: WydeThemes
Author URI: http://www.wydethemes.com
*/

if( ! class_exists( 'WydeCore_Plugin' ) ) {
	class WydeCore_Plugin {

	     /**
	     * Plugin version, used for cache-busting of style and script file references.
	     *
	     * @since   1.0.0
	     *
	     * @var     string
	     */
	    protected $version = '1.7.0';

	    /**
	     * Unique identifier for your plugin.
	     *
	     * Use this value (not the variable name) as the text domain when internationalizing strings of text. It should
	     * match the Text Domain file header in the main plugin file.
	     *
	     * @since    1.0.0
	     *
	     * @var      string
	     */
	    protected $plugin_slug = 'wyde-core';

	    /**
	     * Instance of this class.
	     *
	     * @since    1.0.0
	     *
	     * @var      object
	     */
	    protected static $instance = null;
		
		/**
		 * Initialize the plugin by setting localization and loading public scripts
		 * and styles.
		 *
		 * @since     1.0.0
		 */
		private function __construct() {
			$this->init();
		}

		/**
		 * Registers TinyMCE editor buttons and Shortcodes
		 *
		 * @return	void
		 */
		function init() {

		}

        /**
         * Register custom post types
         */

	    /**
	     * Return an instance of this class.
	     *
	     * @since     1.0.0
	     *
	     * @return    object    A single instance of this class.
	     */
	    public static function get_instance() {

		    // If the single instance hasn't been set, set it now.
		    if ( null == self::$instance ) {
			    self::$instance = new self;
		    }

		    return self::$instance;
	    }

	}
}
// Load the instance of the plugin
add_action( 'plugins_loaded', array( 'WydeCore_Plugin', 'get_instance' ) );


function wyde_register_post_types(){
    
    global $wyde_options, $wp_post_types;

    $portfolio_slug = isset( $wyde_options['portfolio_slug'] )?$wyde_options['portfolio_slug']:'portfolio-item';

    $wp_post_types['attachment']->exclude_from_search = true;

    //portfolio post type
    register_post_type('portfolio',
		array(
			'labels' => array(
				'name' 			=> __( 'Portfolios', 'Wyde' ),
				'singular_name' => __( 'Portfolio', 'Wyde' ),
                'add_new' => __('Add New', 'Wyde' ),
                'add_new_item' => __('Add New Portfolio', 'Wyde'),
                'edit_item' => __('Edit Portfolio', 'Wyde'),
                'new_item' => __('New Portfolio', 'Wyde'),
                'view_item' => __('View Portfolio', 'Wyde'),
                'menu_name' => __('Portfolios', 'Wyde')
			),
			'public' => true,
			'has_archive' => false,
			'rewrite' => array(
				'slug' => sanitize_title( $portfolio_slug )
			),
			'supports' => array( 'title', 'editor', 'author', 'thumbnail'),
			'can_export' => true,
            'menu_icon' => 'dashicons-portfolio'
		)
	);

    //portfolio category    
    register_taxonomy('portfolio_category', 'portfolio', array('hierarchical' => true, 'labels' => array(
            'name' =>  __('Portfolio Categories', 'Wyde'),
            'singular_name' => __('Portfolio Category', 'Wyde'),
            'all_items' => __('All Categories', 'Wyde' ),
            'edit_item' => __('Edit Category', 'Wyde' ),
            'update_item' => __('Update Category', 'Wyde' ),
            'add_new_item' => __('Add New Category', 'Wyde' ),
            'new_item_name' => __('New Category', 'Wyde' ),
        ), 'query_var' => true, 'rewrite' => array(
				'slug' => 'portfolio-category'
		        )
            )
    );
    //portfolio skill   
	register_taxonomy('portfolio_skill', 'portfolio', array('hierarchical' => true, 'labels' => array(
            'name' => __('Portfolio Skills', 'Wyde'),
            'singular_name' => __('Portfolio Skill', 'Wyde'),
            'all_items' => __('All Skills', 'Wyde'),
            'edit_item' => __('Edit Skill', 'Wyde'),
            'update_item' =>  __('Update Skill', 'Wyde'),
            'add_new_item' => __('Add New Skill', 'Wyde'),
            'new_item_name' => __('New Skill', 'Wyde'),
            ), 
            'query_var' => true, 
            'rewrite' => array(
				'slug' => 'portfolio-skill'
		        )
            )
    );

    /*
    //portfolio tag   
	register_taxonomy('portfolio_tag', 'portfolio', array('hierarchical' => false, 'labels' => array(
            'name' => 'Tags',
            'singular_name' => 'Tag',
            'all_items' => 'All Tags',
            'edit_item' => 'Edit Tag',
            'update_item' => 'Update Tag',
            'add_new_item' => 'Add New Tag',
            'new_item_name' =>  'New Tag',
            ), 'query_var' => true, 
            'rewrite' => true));

    */
	
    //testimonial post type  	
    register_post_type('testimonial',
		array(
			'labels' => array(
				'name' 			=> __( 'Testimonials', 'Wyde' ),
				'singular_name' => __( 'Testimonial', 'Wyde' ),
                'add_new' => __('Add New', 'Wyde' ),
                'add_new_item' => __('Add New Testimonial', 'Wyde'),
                'edit_item' => __('Edit Testimonial', 'Wyde'),
                'new_item' => __('New Testimonial', 'Wyde'),
                'view_item' => __('View Testimonial', 'Wyde'),
                'menu_name' => __('Testimonials', 'Wyde')
			),
			'public' => true,
			'has_archive' => false,
			'rewrite' => array(
				'slug' => 'testimonial-item'
			),
			'supports' => array( 'title', 'editor'),
			'can_export' => true,
            'menu_icon' => 'dashicons-testimonial'
		)
	);

            
    //testimonial category
    register_taxonomy('testimonial_category', 'testimonial', array('hierarchical' => true, 'labels' => array(
            'name' => __('Testimonial Categories', 'Wyde'),
            'singular_name' => __('Testimonial Category', 'Wyde'),
            'all_items' => __('All Categories', 'Wyde' ),
            'edit_item' => __('Edit Category', 'Wyde' ),
            'update_item' => __('Update Category', 'Wyde' ),
            'add_new_item' => __('Add New Category', 'Wyde' ),
            'new_item_name' => __('New Category', 'Wyde' ),
            ), 'query_var' => true, 
            'rewrite' => true));

    

    //team-member post type
    register_post_type('team-member', 
                array(
			'labels' => array(
			            'name' 					=> __('Team Members', 'Wyde' ),
			            'singular_name' 		=> __('Team Member', 'Wyde' ),
			            'add_new' 				=> __('Add New', 'Wyde' ),
			            'add_new_item' 			=> __('Add New Team Member', 'Wyde' ),
			            'edit_item' 			=> __('Edit Team Member', 'Wyde' ),
			            'new_item' 				=> __('New Team Member', 'Wyde' ),
			            'all_items' 			=> __('All Team Members', 'Wyde' ),
			            'view_item' 			=> __('View Team Members', 'Wyde' ),
			            'search_items' 			=> __('Search Team Members', 'Wyde' ),
			            'not_found' 			=> __('No %s Found', 'Wyde' ),
			            'not_found_in_trash' 	=> __('No %s Found In Trash', 'Wyde' ),
			            'parent_item_colon' 	=> '',
			            'menu_name' 			=> __( 'Team Members', 'Wyde' )

                ),
			'public' 				=> true,
			'rewrite' => array(
				'slug' => 'team-member'
			),
			'has_archive' 			=> false,
			'hierarchical' 			=> false,
			'supports' 				=> array('title', 'editor'),
			'menu_icon' 			=> 'dashicons-groups'
		));



    //team member category
    register_taxonomy('team-member_category', 'team-member', array('hierarchical' => true, 'labels' => array(
            'name' => __('Team Member Categories', 'Wyde'),
            'singular_name' => __('Team Member Category', 'Wyde'),
            'all_items' => __('All Categories', 'Wyde' ),
            'edit_item' => __('Edit Category', 'Wyde' ),
            'update_item' => __('Update Category', 'Wyde' ),
            'add_new_item' => __('Add New Category', 'Wyde' ),
            'new_item_name' => __('New Category', 'Wyde' ),
            ), 'query_var' => true, 
            'rewrite' => true));
        
}
add_action( 'init', 'wyde_register_post_types' );

/** Wyde Importer **/
include_once plugin_dir_path(__FILE__). '/importer/class-wyde-importer.php';