<?php
class Vela_Shortcode{

    function __construct() {
		add_action( 'init', array($this, 'init'));
        add_action( 'wp_enqueue_scripts', array($this, 'load_scripts' ) );
        
        $this->revslider_set_as_theme();
        $this->integrate_with_vc();

	}

    function init() {

        if( get_user_option('rich_editing') == 'true' ){
            add_filter( 'mce_buttons', array( $this, 'register_buttons' ), 1000 );
            add_filter( 'mce_external_plugins', array( $this, 'add_buttons' ) );
		}

	}

    /*
    * Register editor buttonsvc
    */
    public function register_buttons( $buttons ) {

        //insert dropcap button
        array_splice($buttons, 3, 0, 'dropcap');
        
        //insert hilight button
        array_splice($buttons, 4, 0, 'highlight');

        //Remove the revslider button
        $remove = 'revslider';
        //Find the array key and then unset
        if ( ( $key = array_search($remove, $buttons) ) !== false )	unset($buttons[$key]);

        return $buttons;

    }

    /*
    * Add Wyde button plugins
    */
    public function add_buttons( $plugin_array ) {
        $plugin_array['wydeEditor'] = get_template_directory_uri() . '/shortcodes/js/editor-plugin.js';
        return $plugin_array;
    }

    function revslider_set_as_theme(){
        global $revSliderAsTheme, $pagenow;

        if( function_exists('set_revslider_as_theme') ){
            $revSliderAsTheme = true;
            update_option('revslider-valid-notice', 'off');            
            add_filter('revslider_set_notifications', array($this, 'revslider_set_notifications') );
        }

        if( class_exists('RevSliderAdmin') && $pagenow == 'plugins.php' ){
            remove_action('admin_notices', array('RevSliderAdmin', 'add_plugins_page_notices'));
        }
    }

    function revslider_set_notifications(){
		return 'off';
	}

    /*
    * Integrate with Visual Composer
    */
    function integrate_with_vc() {
        // Check if Visual Composer is installed
        if ( ! defined( 'WPB_VC_VERSION' ) ) {
            // Display notice that Visual Compser is required
            //add_action('admin_notices', array($this, 'show_vc_notice'));
            return;
        }

        add_action( 'vc_before_init', array($this, 'vc_before_init') );

		add_action( 'vc_build_admin_page', array(&$this, 'update_plugins_shortcodes'), 11 );
        add_action( 'vc_load_shortcode', array(&$this, 'update_plugins_shortcodes'), 11 );

		add_action( 'init', array($this, 'deregister_grid_element'), 100);
        remove_action( 'init', 'vc_page_welcome_redirect' );

        add_action( 'vc_after_init', array($this, 'vc_after_init') );
        add_action( 'vc_after_init_base', array($this, 'vc_after_init_base') );

        add_action( 'vc_mapper_init_after', array($this, 'init_shortcodes') );

        add_action( 'vc_backend_editor_enqueue_js_css', array($this, 'load_admin_scripts'));

        add_filter( 'vc_iconpicker-type-fontawesome', array($this, 'get_font_awesome_icons') );

        
    }

    public function init_shortcodes(){
        $min = defined('WP_DEBUG') && WP_DEBUG? '' : '.min';

        WpbakeryShortcodeParams::addField('wyde_animation', array( $this, 'animation_field'), get_template_directory_uri() .'/shortcodes/js/wyde-animation'.$min.'.js');
        WpbakeryShortcodeParams::addField('wyde_gmaps', array( $this, 'gmaps_field'), get_template_directory_uri() .'/shortcodes/js/wyde-gmaps'.$min.'.js');

        $this->add_elements();
        $this->update_elements();

    }
    
    /*
    * Add action before vc init
    */
    public function vc_before_init() {

        global $vc_manager; 
        //Disable automatic updates notifications
        vc_set_as_theme(true);
        $vc_manager->disableUpdater(true);
        // Set default shortcodes templates
        vc_set_shortcodes_templates_dir( get_template_directory() .'/shortcodes/templates' );

    }

    public function vc_after_init() {
        //remove vc edit button from admin bar
        remove_action( 'admin_bar_menu', array( vc_frontend_editor(), 'adminBarEditLink' ), 1000 );
        //remove vc edit button from wp edit links
        remove_filter( 'edit_post_link', array( vc_frontend_editor(), 'renderEditButton' ) );
        //vc_disable_frontend(); // this will disable frontend editor

    }

    public function vc_after_init_base() {

    }

    /** Deregister Grid Element post type **/
    public function deregister_grid_element(){
        $this->unregister_post_type('vc_grid_item');
        remove_action('vc_menu_page_build', 'vc_gitem_add_submenu_page');
    }

    public function unregister_post_type( $post_type ){
        global $wp_post_types;
	    if ( isset( $wp_post_types[ $post_type ] ) ) {
            unset( $wp_post_types[ $post_type ] );
	    }
    }

    /*
	* Find and include all shortcode classes within classes folder
	*/
	public function add_elements() {

		foreach( glob( get_template_directory() . '/shortcodes/classes/*.php' ) as $filename ) {
			require_once $filename;
		}

	}

    /*
    * Update VC elements
    */
    public function update_elements(){

       
        /* Remove VC Elements 
        ---------------------------------------------------------- */
        vc_remove_element('vc_button');
        vc_remove_element('vc_button2');
        vc_remove_element('vc_carousel');
        vc_remove_element('vc_posts_grid');
        vc_remove_element('vc_posts_slider');
        vc_remove_element('vc_pie');
        vc_remove_element('vc_gmaps');

        // remove unused elements
        vc_remove_element('vc_cta');
        vc_remove_element('vc_icon');
        vc_remove_element('vc_basic_grid');
        vc_remove_element('vc_media_grid');
        vc_remove_element('vc_masonry_grid');
        vc_remove_element('vc_masonry_media_grid');
        
        
        /*
        vc_map_update( 'vc_tta_tabs', array('deprecated' => '4.6') );
        vc_map_update( 'vc_tta_tour', array('deprecated' => '4.6') );
        vc_map_update( 'vc_tta_accordion', array('deprecated' => '4.6') );
        vc_map_update( 'vc_tta_section', array('deprecated' => '4.6') );
        */

        vc_remove_element('vc_tta_tabs');
        vc_remove_element('vc_tta_tour');
        vc_remove_element('vc_tta_accordion');
        vc_remove_element('vc_tta_section');
        vc_remove_element('vc_tta_pageable');

        
        

        /* Update VC Elements 
        /* Row
        ---------------------------------------------------------- */
        vc_map( array(
	        'name' => __( 'Row', 'Vela' ),
	        'base' => 'vc_row',
	        'is_container' => true,
	        'icon' => 'icon-wpb-row',
	        'show_settings_on_create' => false,
	        'category' => __( 'Content', 'Vela' ),
	        'description' => __( 'Place content elements inside the row', 'Vela' ),
	        'params' => array(
                array(
                    'type' => 'dropdown',
                    'class' => '',
                    'heading' => __('Content Width', 'Vela'),
                    'param_name' => 'full_width',
                    'value' => array(
                            'Default' => '',
                            'Full Width' => 'true'
                    ),
                    'std' => '',
                    'description' => __('Select Full Width to stretch content to full the browser width.', 'Vela')
                ),
                array(
                    'type' => 'dropdown',
                    'class' => '',
                    'heading' => __('Content Height', 'Vela'),
                    'param_name' => 'full_screen',
                    'value' => array(
                            'Default' => '',
                            'Full Screen' => 'true'
                    ),
                    'std' => '',
                    'description' => __('Select Full Screen to create a Full screen section (Full Height).', 'Vela')
                ),
                array(
                    'type' => 'dropdown',
                    'class' => '',
                    'heading' => __('Text Color', 'Vela'),
                    'param_name' => 'alt_color',
                    'value' => array(
                            'Dark' => '',
                            'Light' => 'true'
                    ),
                    'description' => __('Select dark or light text color.', 'Vela')
                ),
                array(
                    'type' => 'dropdown',
                    'class' => '',
                    'heading' => __('Background Color', 'Vela'),
                    'param_name' => 'alt_bg_color',
                    'value' => array(                            
                            'Light' => '',
                            'Dark' => 'true',
                    ),
                    'std' => '',
                    'description' => __('Select dark or light background color.', 'Vela')
                ),
                array(
                    'type' => 'dropdown',
                    'class' => '',
                    'heading' => __('Background Parallax', 'Vela'),
                    'param_name' => 'parallax',
                    'value' => array(
                            'None' => '',
                            'Parallax' => 'true'
                    ),
                    'std' => '',
                    'description' => __('Select parallax to enable parallax background scrolling.', 'Vela')
                ),
                array(
                    'type' => 'dropdown',
                    'class' => '',
                    'heading' => __('Background Overlay', 'Vela'),
                    'param_name' => 'background_overlay',
                    'value' => array(
                            'None' => '',
                            'Color Overlay' => 'color',
                            'Pattern Overlay' => 'pattern'
                    ),
                    'description' => __('Apply an overlay to the background.', 'Vela')
                ),
                array(
			        'type' => 'colorpicker',
			        'heading' => __( 'Overlay Color', 'Vela' ),
			        'param_name' => 'overlay_color',
			        'description' => __( 'Select background color overlay.', 'Vela' ),
                    'dependency' => array(
				        'element' => 'background_overlay',
				        'not_empty' => true
			        )
		        ),
                array(
                    'type' => 'dropdown',
                    'class' => '',
                    'heading' => __('Content Mask', 'Vela'),
                    'param_name' => 'mask',
                    'value' => array(
                            'None' => '',
                            'Top' => 'top',
                            'Bottom' => 'bottom'
                    ),
                    'description' => __('Select content mask position.', 'Vela')
                ),
                array(
			        'type' => 'colorpicker',
			        'heading' => __( 'Mask Color', 'Vela' ),
			        'param_name' => 'mask_color',
			        'description' => __( 'Select content mask color.', 'Vela' ),
                    'dependency' => array(
				        'element' => 'mask',
				        'not_empty' => true
			        )
		        ), 
                array(
                    'type' => 'dropdown',
                    'class' => '',
                    'heading' => __('Mask Style', 'Vela'),
                    'param_name' => 'mask_style',
                    'value' => array(
                            '0/100' => '0', 
                            '10/90' => '10',
                            '20/80' => '20',
                            '30/70' => '30',
                            '40/60' => '40',
                            '50/50' => '50',
                            '60/40' => '60',
                            '70/30' => '70',
                            '80/20' => '80',
                            '90/10' => '90',
                            '100/0' => '100',
                        ),
                    'description' => __('Select content mask style.', 'Vela'),
                    'dependency' => array(
				        'element' => 'mask',
				        'not_empty' => true
			        ),
                    'std' => '50'
                ),
                array(
                    'type' => 'dropdown',
                    'class' => '',
                    'heading' => __('Content Vertical Alignment', 'Vela'),
                    'param_name' => 'vertical_align',
                    'value' => array(
                        'Top' => '', 
                        'Middle' =>'middle', 
                        'Bottom' => 'bottom', 
                    ),
                    'description' => __('Select content vertical alignment.', 'Vela')
                ),
                array(
                    'type' => 'dropdown',
                    'class' => '',
                    'heading' => __('Padding Size', 'Vela'),
                    'param_name' => 'padding_size',
                    'value' => array(
                        'Default' => '', 
                        'No Padding' =>'no-padding', 
                        'Small' => 's-padding', 
                        'Medium' => 'm-padding', 
                        'Large' => 'l-padding', 
                        'Extra Large' => 'xl-padding'
                    ),
                    'description' => __('Select padding size.', 'Vela')
                ),
                array(
                    'type' => 'textfield',
                    'heading' => __( 'Row ID', 'flora' ),
                    'param_name' => 'el_id',
                    'description' => sprintf( __( 'Enter row ID (Note: make sure it is unique and valid according to <a href="%s" target="_blank">W3C specification</a>).', 'flora' ), 'http://www.w3schools.com/tags/att_global_id.asp' )
                )   ,
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
            ),
	        'js_view' => 'VcRowView'
        ));

        /* Row Inner
        ---------------------------------------------------------- */
        vc_map( array(
	        'name' => __( 'Row', 'Vela' ), //Inner Row
	        'base' => 'vc_row_inner',
	        'content_element' => false,
	        'is_container' => true,
	        'icon' => 'icon-wpb-row',
	        'weight' => 1000,
	        'show_settings_on_create' => false,
	        'description' => __( 'Place content elements inside the row', 'Vela' ),
	        'params' => array(
                array(
                    'type' => 'dropdown',
                    'class' => '',
                    'heading' => __('Text Color', 'Vela'),
                    'param_name' => 'alt_color',
                    'value' => array(
                            'Dark' => '',
                            'Light' => 'true'
                    ),
                    'description' => __('Select dark or light text color.', 'Vela')
                ),
                array(
                    'type' => 'dropdown',
                    'class' => '',
                    'heading' => __('Background Color', 'Vela'),
                    'param_name' => 'alt_bg_color',
                    'value' => array(                            
                            'Light' => '',
                            'Dark' => 'true',
                    ),
                    'std' => '',
                    'description' => __('Select dark or light background color.', 'Vela')
                ),
                array(
                    'type' => 'dropdown',
                    'class' => '',
                    'heading' => __('Background Overlay', 'Vela'),
                    'param_name' => 'background_overlay',
                    'value' => array(
                            'None' => '',
                            'Color Overlay' => 'color',
                            'Pattern Overlay' => 'pattern'
                    ),
                    'description' => __('Apply an overlay to the background.', 'Vela')
                ),
                array(
			        'type' => 'colorpicker',
			        'heading' => __( 'Overlay Color', 'Vela' ),
			        'param_name' => 'overlay_color',
			        'description' => __( 'Select background color overlay.', 'Vela' ),
                    'dependency' => array(
				        'element' => 'background_overlay',
				        'not_empty' => true
			        )
		        ),
                array(
                    'type' => 'dropdown',
                    'class' => '',
                    'heading' => __('Content Mask', 'Vela'),
                    'param_name' => 'mask',
                    'value' => array(
                            'None' => '',
                            'Top' => 'top',
                            'Bottom' => 'bottom'
                    ),
                    'description' => __('Select content mask position.', 'Vela')
                ),
                array(
			        'type' => 'colorpicker',
			        'heading' => __( 'Mask Color', 'Vela' ),
			        'param_name' => 'mask_color',
			        'description' => __( 'Select content mask color.', 'Vela' ),
                    'dependency' => array(
				        'element' => 'mask',
				        'not_empty' => true
			        )
		        ), 
                array(
                    'type' => 'dropdown',
                    'class' => '',
                    'heading' => __('Mask Style', 'Vela'),
                    'param_name' => 'mask_style',
                    'value' => array(
                            '0/100' =>  '0', 
                            '10/90' => '10',
                            '20/80' => '20',
                            '30/70' => '30',
                            '40/60' => '40',
                            '50/50' => '50',
                            '60/40' => '60',
                            '70/30' => '70',
                            '80/20' => '80',
                            '90/10' => '90',
                            '100/0' => '100',
                        ),
                    'description' => __('Select content mask style.', 'Vela'),
                    'dependency' => array(
				        'element' => 'mask',
				        'not_empty' => true
			        ),
                    'std' => '50'
                ),
                array(
                    'type' => 'dropdown',
                    'class' => '',
                    'heading' => __('Content Vertical Alignment', 'Vela'),
                    'param_name' => 'vertical_align',
                    'value' => array(
                        'Top' => '', 
                        'Middle' =>'middle', 
                        'Bottom' => 'bottom', 
                    ),
                    'description' => __('Select content vertical alignment.', 'Vela')
                ),
                array(
                    'type' => 'dropdown',
                    'class' => '',
                    'heading' => __('Padding Size', 'Vela'),
                    'param_name' => 'padding_size',
                    'value' => array(
                        'Default' => '', 
                        'No Padding' =>'no-padding', 
                        'Small' => 's-padding', 
                        'Medium' => 'm-padding', 
                        'Large' => 'l-padding', 
                        'Extra Large' => 'xl-padding'
                    ),
                    'description' => __('Select padding size.', 'Vela')
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
            ),
	        'js_view' => 'VcRowView'
        ));

       
        

        /* Column
        ---------------------------------------------------------- */
        vc_map( array(
	        'name' => __( 'Column', 'Vela' ),
	        'base' => 'vc_column',
	        'is_container' => true,
	        'content_element' => false,
	        'params' => array(
                array(
                    'type' => 'dropdown',
                    'class' => '',
                    'heading' => __('Text Color', 'Vela'),
                    'param_name' => 'alt_color',
                    'value' => array(
                            'Dark' => '',
                            'Light' => 'true'
                    ),
                    'description' => __('Select dark or light text color.', 'Vela')
                ),
                array(
                    'type' => 'dropdown',
                    'class' => '',
                    'heading' => __('Background Color', 'Vela'),
                    'param_name' => 'alt_bg_color',
                    'value' => array(                            
                            'Light' => '',
                            'Dark' => 'true',
                    ),
                    'std' => '',
                    'description' => __('Select dark or light background color.', 'Vela')
                ),
                array(
                    'type' => 'dropdown',
                    'class' => '',
                    'heading' => __('Text Alignment', 'Vela'),
                    'param_name' => 'text_align',
                    'value' => array(
                        'Left' => '', 
                        'Center' =>'center', 
                        'Right' => 'right', 
                    ),
                    'description' => __('Select text alignment.', 'Vela')
                ),
                array(
                    'type' => 'dropdown',
                    'class' => '',
                    'heading' => __('Padding Size', 'Vela'),
                    'param_name' => 'padding_size',
                    'value' => array(
                        'Default' => '', 
                        'No Padding' =>'no-padding', 
                        'Small' => 's-padding', 
                        'Medium' => 'm-padding', 
                        'Large' => 'l-padding', 
                        'Extra Large' => 'xl-padding'
                    ),
                    'description' => __('Select padding size.', 'Vela')
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
			        'type' => 'dropdown',
			        'heading' => __( 'Width', 'Vela' ),
			        'param_name' => 'width',
			        'value' => array(
                            __( '1 column - 1/12', 'flora' ) => '1/12',
                            __( '2 columns - 1/6', 'flora' ) => '1/6',
                            __( '3 columns - 1/4', 'flora' ) => '1/4',
                            __( '4 columns - 1/3', 'flora' ) => '1/3',
                            __( '5 columns - 5/12', 'flora' ) => '5/12',
                            __( '6 columns - 1/2', 'flora' ) => '1/2',
                            __( '7 columns - 7/12', 'flora' ) => '7/12',
                            __( '8 columns - 2/3', 'flora' ) => '2/3',
                            __( '9 columns - 3/4', 'flora' ) => '3/4',
                            __( '10 columns - 5/6', 'flora' ) => '5/6',
                            __( '11 columns - 11/12', 'flora' ) => '11/12',
                            __( '12 columns - 1/1', 'flora' ) => '1/1',
                    ),
			        'group' => __( 'Responsive Options', 'Vela' ),
			        'description' => __( 'Select column width.', 'Vela' ),
			        'std' => '1/1'
		        ),
		        array(
			        'type' => 'column_offset',
			        'heading' => __( 'Responsiveness', 'Vela' ),
			        'param_name' => 'offset',
			        'group' => __( 'Responsive Options', 'Vela' ),
			        'description' => __( 'Adjust column for different screen sizes. Control width, offset and visibility settings.', 'Vela' )
		        ),
		        array(
			        'type' => 'css_editor',
			        'heading' => __( 'Css', 'Vela' ),
			        'param_name' => 'css',
			        'group' => __( 'Design options', 'Vela' )
                )
	        ),
	        "js_view" => 'VcColumnView'
        ) );


         /* Column Inner
        ---------------------------------------------------------- */
        vc_map( array(
            "name" => __( "Column", "Vela" ),
	        "base" => "vc_column_inner",
	        "class" => "",
	        "icon" => "",
	        "wrapper_class" => "",
	        "controls" => "full",
	        "allowed_container_element" => false,
	        "content_element" => false,
	        "is_container" => true,
	        "params" => array(		        
                array(
                    'type' => 'dropdown',
                    'class' => '',
                    'heading' => __('Text Color', 'Vela'),
                    'param_name' => 'alt_color',
                    'value' => array(
                            'Dark' => '',
                            'Light' => 'true'
                    ),
                    'description' => __('Select dark or light text color.', 'Vela')
                ),
                array(
                    'type' => 'dropdown',
                    'class' => '',
                    'heading' => __('Background Color', 'Vela'),
                    'param_name' => 'alt_bg_color',
                    'value' => array(                            
                            'Light' => '',
                            'Dark' => 'true',
                    ),
                    'std' => '',
                    'description' => __('Select dark or light background color.', 'Vela')
                ),
                array(
                    'type' => 'dropdown',
                    'class' => '',
                    'heading' => __('Text Alignment', 'Vela'),
                    'param_name' => 'text_align',
                    'value' => array(
                        'Left' => '', 
                        'Center' =>'center', 
                        'Right' => 'right', 
                    ),
                    'description' => __('Select text alignment.', 'Vela')
                ),
                array(
                    'type' => 'dropdown',
                    'class' => '',
                    'heading' => __('Padding Size', 'Vela'),
                    'param_name' => 'padding_size',
                    'value' => array(
                        'Default' => '', 
                        'No Padding' =>'no-padding', 
                        'Small' => 's-padding', 
                        'Medium' => 'm-padding', 
                        'Large' => 'l-padding', 
                        'Extra Large' => 'xl-padding'
                    ),
                    'description' => __('Select padding size.', 'Vela')
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
			        'type' => 'dropdown',
			        'heading' => __( 'Width', 'Vela' ),
			        'param_name' => 'width',
			        'value' => array(
                            __( '1 column - 1/12', 'flora' ) => '1/12',
                            __( '2 columns - 1/6', 'flora' ) => '1/6',
                            __( '3 columns - 1/4', 'flora' ) => '1/4',
                            __( '4 columns - 1/3', 'flora' ) => '1/3',
                            __( '5 columns - 5/12', 'flora' ) => '5/12',
                            __( '6 columns - 1/2', 'flora' ) => '1/2',
                            __( '7 columns - 7/12', 'flora' ) => '7/12',
                            __( '8 columns - 2/3', 'flora' ) => '2/3',
                            __( '9 columns - 3/4', 'flora' ) => '3/4',
                            __( '10 columns - 5/6', 'flora' ) => '5/6',
                            __( '11 columns - 11/12', 'flora' ) => '11/12',
                            __( '12 columns - 1/1', 'flora' ) => '1/1',
                    ),
			        'group' => __( 'Responsive Options', 'Vela' ),
			        'description' => __( 'Select column width.', 'Vela' ),
			        'std' => '1/1'
		        ),
		        array(
			        'type' => 'column_offset',
			        'heading' => __( 'Responsiveness', 'Vela' ),
			        'param_name' => 'offset',
			        'group' => __( 'Responsive Options', 'Vela' ),
			        'description' => __( 'Adjust column for different screen sizes. Control width, offset and visibility settings.', 'Vela' )
		        ),
		        array(
			        'type' => 'css_editor',
			        'heading' => __( 'Css', 'Vela' ),
			        'param_name' => 'css',
			        'group' => __( 'Design options', 'Vela' )
                ),
	        ),
	        "js_view" => 'VcColumnView'
        ) );
      

        /* Text Block
        ---------------------------------------------------------- */
        vc_map( array(
	        'name' => __( 'Text Block', 'Vela' ),
	        'base' => 'vc_column_text',
	        'icon' => 'icon-wpb-layer-shape-text',
	        'wrapper_class' => 'clearfix',
	        'category' => __( 'Content', 'Vela' ),
	        'description' => __( 'A block of text with WYSIWYG editor', 'Vela' ),
	        'params' => array(
		        array(
			        'type' => 'textarea_html',
			        'holder' => 'div',
			        'heading' => __( 'Text', 'Vela' ),
			        'param_name' => 'content',
			        'value' => __( '<p>I am text block. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.</p>', 'Vela' )
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
			        'heading' => __( 'CSS box', 'Vela' ),
			        'param_name' => 'css',
			        // 'description' => __( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'Vela' ),
			        'group' => __( 'Design Options', 'Vela' )
		        )
	        )
        ) );



        /* Message box
        ---------------------------------------------------------- */
        vc_remove_param('vc_message', 'css_animation');
        vc_remove_param('vc_message', 'el_class');

        vc_add_param('vc_message', array(
                      'type' => 'wyde_animation',
                      'class' => '',
                      'heading' => __('Animation', 'Vela'),
                      'param_name' => 'animation',
                      'description' => __('Select a CSS3 Animation that applies to this element.', 'Vela')
        ));

        vc_add_param('vc_message', array(
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
        ));

        vc_add_param('vc_message', array(
                      'type' => 'textfield',
                      'class' => '',
                      'heading' => __('Extra CSS Class', 'Vela'),
                      'param_name' => 'el_class',
                      'value' => '',
                      'description' => __('If you wish to style particular content element differently, then use this field to add a class name.', 'Vela')
        ));             

        /* Accordion block
        ---------------------------------------------------------- */
        vc_map( array(
	        'name' => __( 'Accordion', 'js_composer' ),
	        'base' => 'vc_accordion',
	        'show_settings_on_create' => false,
	        'is_container' => true,
	        'icon' => 'icon-wpb-ui-accordion',
	        'category' => __( 'Content', 'js_composer' ),
	        'description' => __( 'Collapsible content panels', 'js_composer' ),
	        'params' => array(
		        array(
			        'type' => 'textfield',
			        'heading' => __( 'Widget title', 'js_composer' ),
			        'param_name' => 'title',
			        'description' => __( 'Enter text used as widget title (Note: located above content element).', 'js_composer' )
		        ),
		        array(
			        'type' => 'textfield',
			        'heading' => __( 'Active section', 'js_composer' ),
			        'param_name' => 'active_tab',
			        'value' => 1,
			        'description' => __( 'Enter section number to be active on load or enter "false" to collapse all sections.', 'js_composer' )
		        ),
		        array(
			        'type' => 'checkbox',
			        'heading' => __( 'Allow collapse all sections?', 'js_composer' ),
			        'param_name' => 'collapsible',
			        'description' => __( 'If checked, it is allowed to collapse all sections.', 'js_composer' ),
			        'value' => array( __( 'Yes', 'js_composer' ) => 'yes' )
		        ),
		        array(
			        'type' => 'checkbox',
			        'heading' => __( 'Disable keyboard interactions?', 'js_composer' ),
			        'param_name' => 'disable_keyboard',
			        'description' => __( 'If checked, disables keyboard arrow interactions (Keys: Left, Up, Right, Down, Space).', 'js_composer' ),
			        'value' => array( __( 'Yes', 'js_composer' ) => 'yes' )
		        ),
		        array(
			        'type' => 'textfield',
			        'heading' => __( 'Extra class name', 'js_composer' ),
			        'param_name' => 'el_class',
			        'description' => __( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'js_composer' )
		        )
	        ),
	        'custom_markup' => '
            <div class="wpb_accordion_holder wpb_holder clearfix vc_container_for_children">
            %content%
            </div>
            <div class="tab_controls">
                <a class="add_tab" title="' . __( 'Add section', 'js_composer' ) . '"><span class="vc_icon"></span> <span class="tab-label">' . __( 'Add section', 'js_composer' ) . '</span></a>
            </div>
            ',
	        'default_content' => '
            [vc_accordion_tab title="' . __( 'Section 1', 'js_composer' ) . '"][/vc_accordion_tab]
            [vc_accordion_tab title="' . __( 'Section 2', 'js_composer' ) . '"][/vc_accordion_tab]
            ',
	        'js_view' => 'VcAccordionView'
        ) );


        /* Accordion Section
        ---------------------------------------------------------- */
        vc_map( array(
	        'name' => __( 'Section', 'Vela' ),
	        'base' => 'vc_accordion_tab',
	        'allowed_container_element' => 'vc_row',
	        'is_container' => true,
	        'content_element' => false,
	        'params' => array(
		        array(
			        'type' => 'textfield',
			        'heading' => __( 'Title', 'Vela' ),
			        'param_name' => 'title',
			        'description' => __( 'Enter accordion section title.', 'Vela' )
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
			        'description' => __('Select an icon set.', 'Vela')                        
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
			        'type' => 'el_id',
			        'heading' => __( 'Section ID', 'Vela' ),
			        'param_name' => 'el_id',
			        'description' => sprintf( __( 'Enter optionally section ID. Make sure it is unique, and it is valid as w3c specification: %s (Must not have spaces)', 'Vela' ), '<a target="_blank" href="http://www.w3schools.com/tags/att_global_id.asp">' . __( 'link', 'Vela' ) . '</a>' ),
		        ),
                array(
                      'type' => 'textfield',
                      'class' => '',
                      'heading' => __('Extra CSS Class', 'Vela'),
                      'param_name' => 'el_class',
                      'value' => '',
                      'description' => __('If you wish to style particular content element differently, then use this field to add a class name.', 'Vela')
                )
	        ),
	        'js_view' => 'VcAccordionTabView'
        ) );

        /* Call to Action
        ---------------------------------------------------------- */
        vc_map( array(
	        'name' => __( 'Call to Action', 'js_composer' ),
	        'base' => 'vc_cta_button',
	        'icon' => 'icon-wpb-call-to-action',
	        'category' => __( 'Content', 'js_composer' ),
	        'description' => __( 'Catch visitors attention with CTA block', 'js_composer' ),
	        'params' => array(
		        array(
			        'type' => 'textarea',
			        'admin_label' => true,
			        'heading' => __( 'Text', 'js_composer' ),
			        'param_name' => 'call_text',
			        'value' => __( 'Click edit button to change this text.', 'js_composer' ),
			        'description' => __( 'Enter text content.', 'js_composer' )
		        ),
		        array(
			        'type' => 'textfield',
			        'heading' => __( 'Text on the button', 'js_composer' ),
			        'param_name' => 'title',
			        'value' => __( 'Text on the button', 'js_composer' ),
			        'description' => __( 'Enter text on the button.', 'js_composer' )
		        ),
		        array(
			        'type' => 'href',
			        'heading' => __( 'URL (Link)', 'js_composer' ),
			        'param_name' => 'href',
			        'description' => __( 'Enter button link.', 'js_composer' )
		        ),
		        array(
			        'type' => 'dropdown',
			        'heading' => __( 'Target', 'js_composer' ),
			        'param_name' => 'target',
			        'value' => array(
	                    __( 'Same window', 'js_composer' ) => '_self',
	                    __( 'New window', 'js_composer' ) => '_blank'
                    ),
			        'dependency' => array(
				        'element' => 'href',
				        'not_empty' => true,
				        'callback' => 'vc_cta_button_param_target_callback'
			        )
		        ),
		        array(
			        'type' => 'dropdown',
			        'heading' => __( 'Button position', 'js_composer' ),
			        'param_name' => 'position',
			        'value' => array(
				        __( 'Right', 'js_composer' ) => 'cta_align_right',
				        __( 'Left', 'js_composer' ) => 'cta_align_left',
				        __( 'Bottom', 'js_composer' ) => 'cta_align_bottom'
			        ),
			        'description' => __( 'Select button alignment.', 'js_composer' )
		        ),
                array(
			        'type' => 'colorpicker',
			        'heading' => __( 'Background Color', 'Vela' ),
			        'param_name' => 'color',
			        'description' => __( 'Select button background color. If empty "Theme Color Scheme" will be used.', 'Vela' ),
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
	        ),
	        'js_view' => 'VcCallToActionView'
        ) );


        /* Call to Action 2
        ---------------------------------------------------------- */
        vc_map( array(
	        'name' => __( 'Call to Action Block', 'Vela' ),
	        'base' => 'vc_cta_button2',
	        'icon' => 'icon-wpb-call-to-action',
	        'category' => array( __( 'Content', 'Vela' ) ),
	        'description' => __( 'Catch visitors attention with call to action block', 'Vela' ),
	        'params' => array(
		        array(
			        'type' => 'textfield',
			        'heading' => __( 'Heading first line', 'Vela' ),
			        'admin_label' => true,
			        'param_name' => 'h2',
			        'value' => '',
			        'description' => __( 'Text for the first heading line.', 'Vela' )
		        ),
		        array(
			        'type' => 'textfield',
			        'heading' => __( 'Heading second line', 'Vela' ),
			        //'holder' => 'h4',
			        //'admin_label' => true,
			        'param_name' => 'h4',
			        'value' => '',
			        'description' => __( 'Optional text for the second heading line.', 'Vela' )
		        ),
		        array(
			        'type' => 'dropdown',
			        'heading' => __( 'Text align', 'Vela' ),
			        'param_name' => 'txt_align',
			        'value' => array(
		                'Left' => 'left',
		                'Right' => 'right',
		                'Center' => 'center',
		                'Justify' => 'justify'
	                ),
			        'description' => __( 'Text align in call to action block.', 'Vela' )
		        ),
		        array(
			        'type' => 'colorpicker',
			        'heading' => __( 'Custom Background Color', 'Vela' ),
			        'param_name' => 'accent_color',
			        'description' => __( 'Select background color for your element.', 'Vela' )
		        ),
		        array(
			        'type' => 'textarea_html',
			        //holder' => 'div',
			        //'admin_label' => true,
			        'heading' => __( 'Promotional text', 'Vela' ),
			        'param_name' => 'content',
			        'value' => __( 'I am promo text. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'Vela' )
		        ),
		        array(
			        'type' => 'vc_link',
			        'heading' => __( 'URL (Link)', 'Vela' ),
			        'param_name' => 'link',
			        'description' => __( 'Button link.', 'Vela' )
		        ),
		        array(
			        'type' => 'textfield',
			        'heading' => __( 'Text on the button', 'Vela' ),
			        'param_name' => 'title',
			        'value' => '',
			        'description' => __( 'Text on the button.', 'Vela' )
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
			        'type' => 'colorpicker',
			        'heading' => __( 'Button Background Color', 'Vela' ),
			        'param_name' => 'color',
			        'description' => __( 'Select button background color. If empty "Theme Color Scheme" will be used.', 'Vela' ),
                ),
		        array(
			        'type' => 'dropdown',
			        'heading' => __( 'Button position', 'Vela' ),
			        'param_name' => 'position',
			        'value' => array(
				        __( 'Align right', 'Vela' ) => 'right',
				        __( 'Align left', 'Vela' ) => 'left',
				        __( 'Align bottom', 'Vela' ) => 'bottom'
			        ),
			        'description' => __( 'Select button alignment.', 'Vela' )
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
        

        /* Separator
        ---------------------------------------------------------- */
        vc_map( array(
	        'name' => __( 'Separator', 'Vela' ),
	        'base' => 'vc_separator',
	        'icon' => 'icon-wpb-ui-separator',
	        'show_settings_on_create' => true,
	        'category' => __( 'Content', 'Vela' ),
	        //"controls"	=> 'popup_delete',
	        'description' => __( 'Horizontal separator line', 'Vela' ),
	        'params' => array(
		        array(
			        'type' => 'dropdown',
			        'heading' => __( 'Alignment', 'Vela' ),
			        'param_name' => 'align',
			        'value' => array(
				        __( 'Center', 'Vela' ) => 'align_center',
				        __( 'Left', 'Vela' ) => 'align_left',
				        __( 'Right', 'Vela' ) => "align_right"
			        ),
			        'description' => __( 'Select separator alignment.', 'Vela' )
		        ),
		        array(
			        'type' => 'dropdown',
			        'heading' => __( 'Style', 'Vela' ),
			        'param_name' => 'style',
			        'value' => array(
            	        'Border' => '',
		                'Dashed' => 'dashed',
		                'Dotted' => 'dotted',
		                'Double' => 'double',
                        'Wyde Theme' => 'theme',
	                ),
			        'description' => __( 'Separator Style', 'Vela' )
		        ),
		        array(
			        'type' => 'dropdown',
			        'heading' => __( 'Element width', 'Vela' ),
			        'param_name' => 'el_width',
			        'value' => array(
                        '10%',
                        '20%',
                        '30%',
                        '40%',
                        '50%',
                        '60%',
                        '70%',
                        '80%',
                        '90%',
                        '100%',
                    ),
			        'description' => __( 'Separator element width in percents.', 'Vela' ),
                    'dependency' => array(
		                'element' => 'style',
		                'value' => array('', 'dashed', 'dotted', 'double')
		            )
		        ),
                array(
			        'type' => 'colorpicker',
			        'heading' => __( 'Border Color', 'Vela' ),
			        'param_name' => 'color',
			        'description' => __( 'Select border color.', 'Vela' ),
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


        /* Text Separator
        ---------------------------------------------------------- */
        vc_map( array(
	        'name' => __( 'Separator with Text', 'Vela' ),
	        'base' => 'vc_text_separator',
	        'icon' => 'icon-wpb-ui-separator-label',
	        'category' => __( 'Content', 'Vela' ),
	        'description' => __( 'Horizontal separator line with heading', 'Vela' ),
	        'params' => array(
		        array(
			        'type' => 'textfield',
			        'heading' => __( 'Title', 'Vela' ),
			        'param_name' => 'title',
			        'holder' => 'div',
			        'value' => __( 'Title', 'Vela' ),
			        'description' => __( 'Add text to separator.', 'Vela' )
		        ),
		        array(
			        'type' => 'dropdown',
			        'heading' => __( 'Title position', 'Vela' ),
			        'param_name' => 'title_align',
			        'value' => array(
				        __( 'Center', 'Vela' ) => 'separator_align_center',
				        __( 'Left', 'Vela' ) => 'separator_align_left',
				        __( 'Right', 'Vela' ) => "separator_align_right"
			        ),
			        'description' => __( 'Select title location.', 'Vela' )
		        ),
		        array(
			        'type' => 'colorpicker',
			        'heading' => __( 'Border Color', 'Vela' ),
			        'param_name' => 'color',
			        'description' => __( 'Select border color. If empty "Theme Color Scheme" will be used.', 'Vela' ),
                ),
		        array(
			        'type' => 'dropdown',
			        'heading' => __( 'Style', 'Vela' ),
			        'param_name' => 'style',
			        'value' => VcSharedLibrary::getSeparatorStyles(),
			        'description' => __( 'Separator display style.', 'Vela' )
		        ),
		        array(
			        'type' => 'dropdown',
			        'heading' => __( 'Element width', 'Vela' ),
			        'param_name' => 'el_width',
			        'value' => VcSharedLibrary::getElementWidths(),
			        'description' => __( 'Separator element width in percents.', 'Vela' )
		        ),
		        array(
                      'type' => 'textfield',
                      'class' => '',
                      'heading' => __('Extra CSS Class', 'Vela'),
                      'param_name' => 'el_class',
                      'value' => '',
                      'description' => __('If you wish to style particular content element differently, then use this field to add a class name.', 'Vela')
                )
	        ),
	        'js_view' => 'VcTextSeparatorView'
        ) );


        /* Tabs
        ---------------------------------------------------------- */
        $tab_id_1 = ''; // 'def' . time() . '-1-' . rand( 0, 100 );
        $tab_id_2 = ''; // 'def' . time() . '-2-' . rand( 0, 100 );
        vc_map( array(
	        "name" => __( 'Tabs', 'js_composer' ),
	        'base' => 'vc_tabs',
	        'show_settings_on_create' => false,
	        'is_container' => true,
	        'icon' => 'icon-wpb-ui-tab-content',
	        'category' => __( 'Content', 'js_composer' ),
	        'description' => __( 'Tabbed content', 'js_composer' ),
	        'params' => array(
		        array(
			        'type' => 'textfield',
			        'heading' => __( 'Widget title', 'js_composer' ),
			        'param_name' => 'title',
			        'description' => __( 'Enter text used as widget title (Note: located above content element).', 'js_composer' )
		        ),
		        array(
			        'type' => 'dropdown',
			        'heading' => __( 'Auto rotate', 'js_composer' ),
			        'param_name' => 'interval',
			        'value' => array( __( 'Disable', 'js_composer' ) => 0, 3, 5, 10, 15 ),
			        'std' => 0,
			        'description' => __( 'Auto rotate tabs each X seconds.', 'js_composer' )
		        ),
		        array(
			        'type' => 'textfield',
			        'heading' => __( 'Extra class name', 'js_composer' ),
			        'param_name' => 'el_class',
			        'description' => __( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'js_composer' )
		        )
	        ),
	        'custom_markup' => '
            <div class="wpb_tabs_holder wpb_holder vc_container_for_children">
            <ul class="tabs_controls">
            </ul>
            %content%
            </div>'
            ,
	        'default_content' => '
            [vc_tab title="' . __( 'Tab 1', 'js_composer' ) . '" tab_id="' . $tab_id_1 . '"][/vc_tab]
            [vc_tab title="' . __( 'Tab 2', 'js_composer' ) . '" tab_id="' . $tab_id_2 . '"][/vc_tab]
            ',
	        'js_view' => 'VcTabsView'
        ) );

        /* Tab Section
        ---------------------------------------------------------- */
        vc_map( array(
	        'name' => __( 'Tab', 'Vela' ),
	        'base' => 'vc_tab',
	        'allowed_container_element' => 'vc_row',
	        'is_container' => true,
	        'content_element' => false,
	        'params' => array(
                    array(
			            'type' => 'dropdown',
			            'heading' => __( 'Navigation', 'Vela' ),
			            'param_name' => 'mode',
			            'value' => array(
				            'Text' => 'text',
				            'Icon' => 'icon',
			            ),
			            'description' => __( 'Select tab navigation mode.', 'Vela' )
                    ),
		            array(
			            'type' => 'textfield',
			            'heading' => __( 'Title', 'Vela' ),
			            'param_name' => 'title',
			            'description' => __( 'Tab title.', 'Vela' ),
                        'dependency' => array(
		                    'element' => 'mode',
		                    'value' => array('text')
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
		                    'element' => 'mode',
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
			            'type' => 'tab_id',
			            'heading' => __( 'Tab ID', 'Vela' ),
			            'param_name' => "tab_id"
		                )
	            ),
	        'js_view' => 'WydeTabView'
        ) );


        /* Toggle (FAQ)
        ---------------------------------------------------------- */
        vc_map( array(
	        'name' => __( 'FAQ', 'Vela' ),
	        'base' => 'vc_toggle',
	        'icon' => 'icon-wpb-toggle-small-expand',
	        'category' => __( 'Content', 'Vela' ),
	        'description' => __( 'Toggle element for Q&A block', 'Vela' ),
	        'params' => array(
		        array(
			        'type' => 'textfield',
			        'holder' => 'h4',
			        'class' => 'toggle_title',
			        'heading' => __( 'Toggle title', 'Vela' ),
			        'param_name' => 'title',
			        'value' => __( 'Toggle title', 'Vela' ),
			        'description' => __( 'Enter title of toggle block.', 'Vela' )
		        ),
		        array(
			        'type' => 'textarea_html',
			        'holder' => 'div',
			        'class' => 'toggle_content',
			        'heading' => __( 'Toggle content', 'Vela' ),
			        'param_name' => 'content',
			        'value' => __( '<p>Toggle content goes here, click edit button to change this text.</p>', 'Vela' ),
			        'description' => __( 'Toggle block content.', 'Vela' )
		        ),
		        array(
			        'type' => 'dropdown',
			        'heading' => __( 'Default state', 'Vela' ),
			        'param_name' => 'open',
			        'value' => array(
				        __( 'Closed', 'Vela' ) => 'false',
				        __( 'Open', 'Vela' ) => 'true'
			        ),
			        'description' => __( 'Select "Open" if you want toggle to be open by default.', 'Vela' )
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
	        ),
	        'js_view' => 'VcToggleView'
        ) );

            
        /* Tour Section
        ---------------------------------------------------------- */
        $tab_id_1 = ''; // time() . '-1-' . rand( 0, 100 );
        $tab_id_2 = ''; // time() . '-2-' . rand( 0, 100 );
        vc_map( array(
	        'name' => __( 'Tour', 'js_composer' ),
	        'base' => 'vc_tour',
	        'show_settings_on_create' => false,
	        'is_container' => true,
	        'container_not_allowed' => true,
	        'icon' => 'icon-wpb-ui-tab-content-vertical',
	        'category' => __( 'Content', 'js_composer' ),
	        'wrapper_class' => 'vc_clearfix',
	        'description' => __( 'Vertical tabbed content', 'js_composer' ),
	        'params' => array(
		        array(
			        'type' => 'dropdown',
			        'heading' => __( 'Auto rotate slides', 'js_composer' ),
			        'param_name' => 'interval',
			        'value' => array( __( 'Disable', 'js_composer' ) => 0, 3, 5, 10, 15 ),
			        'std' => 0,
			        'description' => __( 'Auto rotate slides each X seconds.', 'js_composer' )
		        ),
		        array(
			        'type' => 'textfield',
			        'heading' => __( 'Extra class name', 'js_composer' ),
			        'param_name' => 'el_class',
			        'description' => __( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'js_composer' )
		        )
	        ),
	        'custom_markup' => '
            <div class="wpb_tabs_holder wpb_holder vc_clearfix vc_container_for_children">
            <ul class="tabs_controls">
            </ul>
            %content%
            </div>'
            ,
	        'default_content' => '
            [vc_tab title="' . __( 'Tab 1', 'js_composer' ) . '" tab_id="' . $tab_id_1 . '"][/vc_tab]
            [vc_tab title="' . __( 'Tab 2', 'js_composer' ) . '" tab_id="' . $tab_id_2 . '"][/vc_tab]
            ',
	        'js_view' => 'VcTabsView'
        ) );


        /* Single Image
        ---------------------------------------------------------- */
        /*vc_map( array(
	        'name' => __( 'Single Image', 'Vela' ),
	        'base' => 'vc_single_image',
	        'icon' => 'icon-wpb-single-image',
	        'category' => __( 'Content', 'Vela' ),
	        'description' => __( 'Simple image with CSS animation', 'Vela' ),
	        'params' => array(
		        array(
			        'type' => 'textfield',
			        'heading' => __( 'Widget Title', 'Vela' ),
			        'param_name' => 'title',
			        'description' => __( 'Enter text which will be used as widget title. Leave blank if no title is needed.', 'Vela' )
		        ),
		        array(
			        'type' => 'attach_image',
			        'heading' => __( 'Image', 'Vela' ),
			        'param_name' => 'image',
			        'value' => '',
			        'description' => __( 'Select image from media library.', 'Vela' )
		        ),
		        array(
			        'type' => 'dropdown',
			        'heading' => __( 'Image Size', 'Vela' ),
			        'param_name' => 'img_size',
			        'value' => array(
				        'Thumbnail (150x150)' => 'thumbnail',
				        'Medium (300x300)' => 'medium',
				        'Large (640x640)'=> 'large',
                        'Full (Original)'=> 'full',
				        'Blog Medium (600x340)'=> 'blog-medium',
				        'Blog Large (800x450)'=> 'blog-large',
				        'Blog Full (1066x600)'=> 'blog-full',
			        ),
			        'description' => __( 'Select image size.', 'Vela' )
		        ),
		        array(
			        'type' => 'dropdown',
			        'heading' => __( 'Image Alignment', 'Vela' ),
			        'param_name' => 'alignment',
			        'value' => array(
				        __( 'Align Left', 'Vela' ) => '',
				        __( 'Align Right', 'Vela' ) => 'right',
				        __( 'Align Center', 'Vela' ) => 'center'
			        ),
			        'description' => __( 'Select image alignment.', 'Vela' )
		        ),
		        array(
			        'type' => 'dropdown',
			        'heading' => __( 'Image Style', 'Vela' ),
			        'param_name' => 'style',
			        'value' => VcSharedLibrary::getBoxStyles()
                ),
		        array(
			        'type' => 'colorpicker',
			        'heading' => __( 'Border Color', 'Vela' ),
			        'param_name' => 'border_color',
			        'dependency' => array(
				        'element' => 'style',
				        'value' => array( 'vc_box_border', 'vc_box_border_circle', 'vc_box_outline', 'vc_box_outline_circle' )
			        ),
			        'description' => __( 'Select border color.', 'Vela' ),
			        'param_holder_class' => 'vc_colored-dropdown'
		        ),
		        array(
			        'type' => 'checkbox',
			        'heading' => __( 'Link to large image?', 'Vela' ),
			        'param_name' => 'img_link_large',
			        'description' => __( 'If selected, image will be linked to the larger image.', 'Vela' ),
			        'value' => array( __( 'Yes, please', 'Vela' ) => 'yes' )
		        ),
		        array(
			        'type' => 'dropdown',
			        'heading' => __( 'Link Target', 'Vela' ),
			        'param_name' => 'img_link_target',
			        'value' => array(
                    	__( 'Pretty Photo', 'Vela' ) => "prettyphoto",
	                    __( 'Same window', 'Vela' ) => '_self',
	                    __( 'New window', 'Vela' ) => "_blank",
                    ),
                    'dependency' => array(
				        'element' => 'img_link_large',
				        'value' => array('yes')
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
			        'heading' => __('Extra CSS Class', 'Vela'),
			        'param_name' => 'el_class',
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
        */

        vc_map( array(
            'name' => __( 'Single Image', 'js_composer' ),
            'base' => 'vc_single_image',
            'icon' => 'icon-wpb-single-image',
            'category' => __( 'Content', 'js_composer' ),
            'description' => __( 'Simple image with CSS animation', 'js_composer' ),
            'params' => array(
                array(
                    'type' => 'textfield',
                    'heading' => __( 'Widget title', 'js_composer' ),
                    'param_name' => 'title',
                    'description' => __( 'Enter text used as widget title (Note: located above content element).', 'js_composer' ),
                ),
                array(
                    'type' => 'dropdown',
                    'heading' => __( 'Image source', 'js_composer' ),
                    'param_name' => 'source',
                    'value' => array(
                        __( 'Media library', 'js_composer' ) => 'media_library',
                        __( 'External link', 'js_composer' ) => 'external_link',
                        __( 'Featured Image', 'js_composer' ) => 'featured_image',
                    ),
                    'std' => 'media_library',
                    'description' => __( 'Select image source.', 'js_composer' ),
                ),
                array(
                    'type' => 'attach_image',
                    'heading' => __( 'Image', 'js_composer' ),
                    'param_name' => 'image',
                    'value' => '',
                    'description' => __( 'Select image from media library.', 'js_composer' ),
                    'dependency' => array(
                        'element' => 'source',
                        'value' => 'media_library',
                    ),
                ),
                array(
                    'type' => 'textfield',
                    'heading' => __( 'External link', 'js_composer' ),
                    'param_name' => 'custom_src',
                    'description' => __( 'Select external link.', 'js_composer' ),
                    'dependency' => array(
                        'element' => 'source',
                        'value' => 'external_link',
                    ),
                ),
                array(
                    'type' => 'dropdown',
                    'heading' => __( 'Image size', 'js_composer' ),
                    'param_name' => 'img_size',
                    'value' => array(
                        'Thumbnail (150x150)' => 'thumbnail',
                        'Medium (300x300)' => 'medium',
                        'Large (640x640)'=> 'large',
                        'Full (Original)'=> 'full',
                        'Blog Medium (600x340)'=> 'blog-medium',
                        'Blog Large (800x450)'=> 'blog-large',
                        'Blog Full (1066x600)'=> 'blog-full',
                    ),
                    'description' => __( 'Select image size.', 'Vela' ),
                    'dependency' => array(
                        'element' => 'source',
                        'value' => array( 'media_library', 'featured_image' ),
                    ),
                ),
                array(
                    'type' => 'textfield',
                    'heading' => __( 'Image size', 'js_composer' ),
                    'param_name' => 'external_img_size',
                    'value' => '',
                    'description' => __( 'Enter image size in pixels. Example: 200x100 (Width x Height).', 'js_composer' ),
                    'dependency' => array(
                        'element' => 'source',
                        'value' => 'external_link',
                    ),
                ),
                array(
                    'type' => 'textfield',
                    'heading' => __( 'Caption', 'js_composer' ),
                    'param_name' => 'caption',
                    'description' => __( 'Enter text for image caption.', 'js_composer' ),
                    'dependency' => array(
                        'element' => 'source',
                        'value' => 'external_link',
                    ),
                ),
                array(
                    'type' => 'checkbox',
                    'heading' => __( 'Add caption?', 'js_composer' ),
                    'param_name' => 'add_caption',
                    'description' => __( 'Add image caption.', 'js_composer' ),
                    'value' => array( __( 'Yes', 'js_composer' ) => 'yes' ),
                    'dependency' => array(
                        'element' => 'source',
                        'value' => array( 'media_library', 'featured_image' ),
                    ),
                ),
                array(
                    'type' => 'dropdown',
                    'heading' => __( 'Image alignment', 'js_composer' ),
                    'param_name' => 'alignment',
                    'value' => array(
                        __( 'Left', 'js_composer' ) => 'left',
                        __( 'Right', 'js_composer' ) => 'right',
                        __( 'Center', 'js_composer' ) => 'center',
                    ),
                    'description' => __( 'Select image alignment.', 'js_composer' ),
                ),
                array(
                    'type' => 'dropdown',
                    'heading' => __( 'Image style', 'js_composer' ),
                    'param_name' => 'style',
                    'value' => getVcShared( 'single image styles' ),
                    'description' => __( 'Select image display style.', 'js_comopser' ),
                    'dependency' => array(
                        'element' => 'source',
                        'value' => array( 'media_library', 'featured_image' ),
                    ),
                ),
                array(
                    'type' => 'dropdown',
                    'heading' => __( 'Image style', 'js_composer' ),
                    'param_name' => 'external_style',
                    'value' => getVcShared( 'single image external styles' ),
                    'description' => __( 'Select image display style.', 'js_comopser' ),
                    'dependency' => array(
                        'element' => 'source',
                        'value' => 'external_link',
                    ),
                ),
                array(
                    'type' => 'dropdown',
                    'heading' => __( 'Border color', 'js_composer' ),
                    'param_name' => 'border_color',
                    'value' => getVcShared( 'colors' ),
                    'std' => 'grey',
                    'dependency' => array(
                        'element' => 'style',
                        'value' => array(
                            'vc_box_border',
                            'vc_box_border_circle',
                            'vc_box_outline',
                            'vc_box_outline_circle',
                            'vc_box_border_circle_2',
                            'vc_box_outline_circle_2',
                        ),
                    ),
                    'description' => __( 'Border color.', 'js_composer' ),
                    'param_holder_class' => 'vc_colored-dropdown',
                ),
                array(
                    'type' => 'dropdown',
                    'heading' => __( 'Border color', 'js_composer' ),
                    'param_name' => 'external_border_color',
                    'value' => getVcShared( 'colors' ),
                    'std' => 'grey',
                    'dependency' => array(
                        'element' => 'external_style',
                        'value' => array(
                            'vc_box_border',
                            'vc_box_border_circle',
                            'vc_box_outline',
                            'vc_box_outline_circle',
                        ),
                    ),
                    'description' => __( 'Border color.', 'js_composer' ),
                    'param_holder_class' => 'vc_colored-dropdown',
                ),
                array(
                    'type' => 'dropdown',
                    'heading' => __( 'On click action', 'js_composer' ),
                    'param_name' => 'onclick',
                    'value' => array(
                        __( 'None', 'js_composer' ) => '',
                        __( 'Link to large image', 'js_composer' ) => 'img_link_large',
                        __( 'Open prettyPhoto', 'js_composer' ) => 'link_image',
                        __( 'Open custom link', 'js_composer' ) => 'custom_link',
                        __( 'Zoom', 'js_composer' ) => 'zoom',
                    ),
                    'description' => __( 'Select action for click action.', 'js_composer' ),
                    'std' => '',
                ),
                array(
                    'type' => 'href',
                    'heading' => __( 'Image link', 'js_composer' ),
                    'param_name' => 'link',
                    'description' => __( 'Enter URL if you want this image to have a link (Note: parameters like "mailto:" are also accepted).', 'js_composer' ),
                    'dependency' => array(
                        'element' => 'onclick',
                        'value' => 'custom_link',
                    ),
                ),
                array(
                    'type' => 'dropdown',
                    'heading' => __( 'Link Target', 'js_composer' ),
                    'param_name' => 'img_link_target',
                    'value' => array(
                        __( 'Same window', 'js_composer' ) => '_self',
                        __( 'New window', 'js_composer' ) => '_blank',
                    ),
                    'dependency' => array(
                        'element' => 'onclick',
                        'value' => array( 'custom_link', 'img_link_large' ),
                    ),
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
                    'heading' => __('Extra CSS Class', 'Vela'),
                    'param_name' => 'el_class',
                    'description' => __('If you wish to style particular content element differently, then use this field to add a class name.', 'Vela')
                ),
                array(
                    'type' => 'css_editor',
                    'heading' => __( 'Css', 'Vela' ),
                    'param_name' => 'css',
                    'group' => __( 'Design options', 'Vela' )
                ),
                // backward compatibility. since 4.6
                array(
                    'type' => 'hidden',
                    'param_name' => 'img_link_large',
                ),
            ),
        ));


        /* Progress Bar
        ---------------------------------------------------------- */
        vc_map( array(
	        'name' => __( 'Progress Bar', 'Vela' ),
	        'base' => 'vc_progress_bar',
	        'icon' => 'icon-wpb-graph',
	        'category' => __( 'Content', 'Vela' ),
	        'description' => __( 'Animated progress bar', 'Vela' ),
	        'params' => array(
		        array(
			        'type' => 'textfield',
			        'heading' => __( 'Title', 'Vela' ),
			        'param_name' => 'title',
			        'description' => __( 'Enter text used as widget title (Note: located above content element).', 'Vela' )
		        ),
		        array(
			        'type' => 'exploded_textarea',
			        'heading' => __( 'Values', 'Vela' ),
			        'param_name' => 'values',
			        'description' => __( 'Enter values for graph - value, title and color. Divide value sets with linebreak "Enter" (Example: 90|Development|#e75956).', 'Vela' ),
			        'value' => "90|Development,80|Design,70|Marketing"
		        ),
		        array(
			        'type' => 'textfield',
			        'heading' => __( 'Units', 'Vela' ),
			        'param_name' => 'units',
			        'description' => __( 'Enter measurement units (Example: %, px, points, etc. Note: graph value and units will be appended to graph title).', 'Vela' )
		        ),
                array(
			        'type' => 'colorpicker',
			        'heading' => __( 'Bar Color', 'Vela' ),
			        'param_name' => 'color',
			        'description' => __( 'Select progress bar color. If empty "Theme Color Scheme" will be used.', 'Vela' ),
			        'admin_label' => true,
                ),
		        array(
			        'type' => 'checkbox',
			        'heading' => __( 'Options', 'Vela' ),
			        'param_name' => 'options',
			        'value' => array(
				        __( 'Add stripes', 'Vela' ) => 'striped',
				        __( 'Add animation (Note: visible only with striped bar).', 'Vela' ) => 'animated'
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
     

        /* Image Gallery
        ---------------------------------------------------------- */
        vc_map( array(
            'name' => __( 'Image Gallery', 'js_composer' ),
            'base' => 'vc_gallery',
            'icon' => 'icon-wpb-images-stack',
            'category' => __( 'Content', 'js_composer' ),
            'description' => __( 'Responsive image gallery', 'js_composer' ),
            'params' => array(
                array(
                    'type' => 'textfield',
                    'heading' => __( 'Widget title', 'js_composer' ),
                    'param_name' => 'title',
                    'description' => __( 'Enter text used as widget title (Note: located above content element).', 'js_composer' )
                ),
                array(
                    'type' => 'dropdown',
                    'heading' => __( 'Gallery type', 'js_composer' ),
                    'param_name' => 'type',
                    'value' => array(
                        __( 'Flex slider fade', 'js_composer' ) => 'flexslider_fade',
                        __( 'Flex slider slide', 'js_composer' ) => 'flexslider_slide',
                        __( 'Nivo slider', 'js_composer' ) => 'nivo',
                        __( 'Image grid', 'js_composer' ) => 'image_grid'
                    ),
                    'description' => __( 'Select gallery type.', 'js_composer' )
                ),
                array(
                    'type' => 'dropdown',
                    'heading' => __( 'Auto rotate', 'js_composer' ),
                    'param_name' => 'interval',
                    'value' => array( 3, 5, 10, 15, __( 'Disable', 'js_composer' ) => 0 ),
                    'description' => __( 'Auto rotate slides each X seconds.', 'js_composer' ),
                    'dependency' => array(
                        'element' => 'type',
                        'value' => array( 'flexslider_fade', 'flexslider_slide', 'nivo' )
                    )
                ),
                array(
                    'type' => 'dropdown',
                    'heading' => __( 'Grid Columns', 'Vela' ),
                    'param_name' => 'columns',
                    'value' => array(
                        2,
                        3,
                        4,                        
                    ),
                    'std' => 3,
                    'description' => __( 'Select number of grid columns.', 'Vela' ),
                    'dependency' => array(
                        'element' => 'type',
                        'value' => array( 'image_grid', )
                    )
                ),
                array(
                    'type' => 'dropdown',
                    'heading' => __( 'Image source', 'js_composer' ),
                    'param_name' => 'source',
                    'value' => array(
                        __( 'Media library', 'js_composer' ) => 'media_library',
                        __( 'External links', 'js_composer' ) => 'external_link'
                    ),
                    'std' => 'media_library',
                    'description' => __( 'Select image source.', 'js_composer' )
                ),
                array(
                    'type' => 'attach_images',
                    'heading' => __( 'Images', 'js_composer' ),
                    'param_name' => 'images',
                    'value' => '',
                    'description' => __( 'Select images from media library.', 'js_composer' ),
                    'dependency' => array(
                        'element' => 'source',
                        'value' => 'media_library'
                    ),
                ),
                array(
                    'type' => 'exploded_textarea',
                    'heading' => __( 'External links', 'js_composer' ),
                    'param_name' => 'custom_srcs',
                    'description' => __( 'Enter external link for each gallery image (Note: divide links with linebreaks (Enter)).', 'js_composer' ),
                    'dependency' => array(
                        'element' => 'source',
                        'value' => 'external_link'
                    ),
                ),                
                array(
                    'type' => 'dropdown',
                    'heading' => __( 'Image Size', 'Vela' ),
                    'param_name' => 'img_size',
                    'value' => array(
                        'Thumbnail (150x150)' => 'thumbnail',
                        'Medium (300x300)' => 'medium',
                        'Large (640x640)'=> 'large',
                        'Full (Original)'=> 'full',
                        'Blog Medium (600x340)'=> 'blog-medium',
                        'Blog Large (800x450)'=> 'blog-large',
                        'Blog Full (1066x600)'=> 'blog-full',
                    ),
                    'description' => __( 'Select image size.', 'Vela' ),
                    'dependency' => array(
                        'element' => 'source',
                        'value' => 'media_library'
                    )
                ),
                array(
                    'type' => 'dropdown',
                    'heading' => __( 'On click action', 'js_composer' ),
                    'param_name' => 'onclick',
                    'value' => array(
                        __( 'None', 'js_composer' ) => '',
                        __( 'Link to large image', 'js_composer' ) => 'img_link_large',
                        __( 'Open prettyPhoto', 'js_composer' ) => 'link_image',
                        __( 'Open custom link', 'js_composer' ) => 'custom_link',
                    ),
                    'description' => __( 'Select action for click action.', 'js_composer' ),
                    'std' => 'link_image'
                ),
                array(
                    'type' => 'exploded_textarea',
                    'heading' => __( 'Custom links', 'js_composer' ),
                    'param_name' => 'custom_links',
                    'description' => __( 'Enter links for each slide (Note: divide links with linebreaks (Enter)).', 'js_composer' ),
                    'dependency' => array(
                        'element' => 'onclick',
                        'value' => array( 'custom_link' )
                    )
                ),
                array(
                    'type' => 'dropdown',
                    'heading' => __( 'Custom link target', 'js_composer' ),
                    'param_name' => 'custom_links_target',
                    'description' => __( 'Select where to open  custom links.', 'js_composer' ),
                    'dependency' => array(
                        'element' => 'onclick',
                        'value' => array( 'custom_link', 'img_link_large' ),
                    ),
                    'value' => array(
                        __( 'Same window', 'js_composer' ) => '_self',
                        __( 'New window', 'js_composer' ) => '_blank'
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
                    'heading' => __( 'CSS box', 'js_composer' ),
                    'param_name' => 'css',
                    'group' => __( 'Design Options', 'js_composer' )
                ),
            )
        ) );


    }

    public function update_plugins_shortcodes(){

        /* WooCommerce
        ---------------------------------------------------------- */
        if ( class_exists( 'WooCommerce' ) ) {

            /* Add default params for shortcodes */
            vc_map_update( 'woocommerce_cart', array( 'params' => array() ) );
            vc_map_update( 'woocommerce_checkout', array( 'params' => array() ) );
            vc_map_update( 'woocommerce_order_tracking', array( 'params' => array() ) );

            /* Recent products
            ---------------------------------------------------------- */
            vc_remove_param( 'recent_products', 'columns' );
            vc_add_param( 'recent_products', array(
                'type' => 'dropdown',
                'class' => '',
                'heading' => __('Columns', 'Flora'),
                'weight' => 1,
                'param_name' => 'columns',
                'value' => array(
                    '1', 
                    '2', 
                    '3', 
                    '4',
                    '5',
                    '6',
                ),
                'std' => '4',
                'description' => __('Select the number of columns.', 'Flora'),
            ) );

            /* Featured Products
            ---------------------------------------------------------- */
            vc_remove_param( 'featured_products', 'columns' );
            vc_add_param( 'featured_products', array(
                'type' => 'dropdown',
                'class' => '',
                'heading' => __('Columns', 'Flora'),
                'weight' => 1,
                'param_name' => 'columns',
                'value' => array(
                    '1', 
                    '2', 
                    '3', 
                    '4',
                    '5',
                    '6',
                ),
                'std' => '4',
                'description' => __('Select the number of columns.', 'Flora'),
            ) );

            /* Products
            ---------------------------------------------------------- */
            vc_remove_param( 'products', 'columns' );
            vc_add_param( 'products', array(
                'type' => 'dropdown',
                'class' => '',
                'heading' => __('Columns', 'Flora'),
                'weight' => 1,
                'param_name' => 'columns',
                'value' => array(
                    '1', 
                    '2', 
                    '3', 
                    '4',
                    '5',
                    '6',
                ),
                'std' => '4',
                'description' => __('Select the number of columns.', 'Flora'),
            ) );


            /* Product Category
            ---------------------------------------------------------- */
            vc_remove_param( 'product_category', 'columns' );
            vc_add_param( 'product_category', array(
                'type' => 'dropdown',
                'class' => '',
                'heading' => __('Columns', 'Flora'),
                'weight' => 1,
                'param_name' => 'columns',
                'value' => array(
                    '1', 
                    '2', 
                    '3', 
                    '4',
                    '5',
                    '6',
                ),
                'std' => '4',
                'description' => __('Select the number of columns.', 'Flora'),
            ) );


            /* Product Category
            ---------------------------------------------------------- */
            vc_remove_param( 'product_categories', 'columns' );
            vc_add_param( 'product_categories', array(
                'type' => 'dropdown',
                'class' => '',
                'heading' => __('Columns', 'Flora'),
                'weight' => 1,
                'param_name' => 'columns',
                'value' => array(
                    '1', 
                    '2', 
                    '3', 
                    '4',
                    '5',
                    '6',
                ),
                'std' => '4',
                'description' => __('Select the number of columns.', 'Flora'),
            ) );


            /* Sale products
            ---------------------------------------------------------- */
            vc_remove_param( 'sale_products', 'columns' );
            vc_add_param( 'sale_products', array(
                'type' => 'dropdown',
                'class' => '',
                'heading' => __('Columns', 'Flora'),
                'weight' => 1,
                'param_name' => 'columns',
                'value' => array(
                    '1', 
                    '2', 
                    '3', 
                    '4',
                    '5',
                    '6',
                ),
                'std' => '4',
                'description' => __('Select the number of columns.', 'Flora'),
            ) );

            /* Best Selling Products
            ---------------------------------------------------------- */
            vc_remove_param( 'best_selling_products', 'columns' );
            vc_add_param( 'best_selling_products', array(
                'type' => 'dropdown',
                'class' => '',
                'heading' => __('Columns', 'Flora'),
                'weight' => 1,
                'param_name' => 'columns',
                'value' => array(
                    '1', 
                    '2', 
                    '3', 
                    '4',
                    '5',
                    '6',
                ),
                'std' => '4',
                'description' => __('Select the number of columns.', 'Flora'),
            ) );

            /* Top Rated Products
            ---------------------------------------------------------- */
            vc_remove_param( 'top_rated_products', 'columns' );
            vc_add_param( 'top_rated_products', array(
                'type' => 'dropdown',
                'class' => '',
                'heading' => __('Columns', 'Flora'),
                'weight' => 1,
                'param_name' => 'columns',
                'value' => array(
                    '1', 
                    '2', 
                    '3', 
                    '4',
                    '5',
                    '6',
                ),
                'std' => '4',
                'description' => __('Select the number of columns.', 'Flora'),
            ) );

            /* Product Attribute
            ---------------------------------------------------------- */
            vc_remove_param( 'product_attribute', 'columns' );
            vc_add_param( 'product_attribute', array(
                'type' => 'dropdown',
                'class' => '',
                'heading' => __('Columns', 'Flora'),
                'weight' => 1,
                'param_name' => 'columns',
                'value' => array(
                    '1', 
                    '2', 
                    '3', 
                    '4',
                    '5',
                    '6',
                ),
                'std' => '4',
                'description' => __('Select the number of columns.', 'Flora'),
            ) );


            /* Related Products
            ---------------------------------------------------------- */
            vc_remove_param( 'related_products', 'columns' );
            vc_add_param( 'related_products', array(
                'type' => 'dropdown',
                'class' => '',
                'heading' => __('Columns', 'Flora'),
                'weight' => 1,
                'param_name' => 'columns',
                'value' => array(
                    '1', 
                    '2', 
                    '3', 
                    '4',
                    '5',
                    '6',
                ),
                'std' => '4',
                'description' => __('Select the number of columns.', 'Flora'),
            ) );

        }
    }

    public function get_font_awesome_icons($icons){

        $new_fontawesome_icons_4_3 = array(
		    "Web Application Icons" => array(
                array( "fa fa-bed" => "Bed" ),
                array( "fa fa-cart-arrow-down" => "Cart Arrow Down" ),
                array( "fa fa-cart-plus" => "Cart Plus" ),
                array( "fa fa-diamond" => "Diamond" ),
                array( "fa fa-heartbeat" => "Heartbeat" ),
                array( "fa fa-motorcycle" => "Motorcycle" ),
                array( "fa fa-server" => "Server" ),
                array( "fa fa-ship" => "Ship" ),
                array( "fa fa-street-view" => "Street View" ),
                array( "fa fa-user-plus" => "User Plus" ),
                array( "fa fa-user-secret" => "User Secret" ),
                array( "fa fa-user-times" => "User Times" ),
            ),
            "Transportation Icons" => array(
                array( "fa fa-subway" => "Subway" ),
                array( "fa fa-train" => "Train" ),
            ),
            "Brand Icons" => array(
                array( "fa fa-buysellads" => "Buysellads" ),
                array( "fa fa-connectdevelop" => "Connectdevelop" ),
                array( "fa fa-dashcube" => "Dashcube" ),
                array( "fa fa-facebook-official" => "Facebook Official" ),
                array( "fa fa-forumbee" => "Forumbee" ),
                array( "fa fa-leanpub" => "Leanpub" ),
                array( "fa fa-medium" => "Medium" ),
                array( "fa fa-pinterest-p" => "Pinterest P" ),
                array( "fa fa-sellsy" => "Sellsy" ),
                array( "fa fa-shirtsinbulk" => "Shirtsinbulk" ),
                array( "fa fa-simplybuilt" => "Simplybuilt" ),
                array( "fa fa-skyatlas" => "Skyatlas" ),

            ),
            "Gender Icons" => array(
                array( "fa fa-mars" => "Mars" ),
                array( "fa fa-mars-double" => "Mars Double" ),
                array( "fa fa-mars-stroke" => "Mars Stroke" ),
                array( "fa fa-mars-stroke-h" => "Mars Stroke Horizontal" ),
                array( "fa fa-mars-stroke-v" => "Mars Stroke Vertical" ),
                array( "fa fa-mercury" => "Mercury" ),
                array( "fa fa-neuter" => "Neuter" ),
                array( "fa fa-transgender" => "Transgender" ),
                array( "fa fa-transgender-alt" => "Transgender Alt" ),
                array( "fa fa-venus" => "Venus" ),
                array( "fa fa-venus-double" => "Venus Double" ),
                array( "fa fa-venus-mars" => "Venus Mars" ),
                array( "fa fa-viacoin" => "Viacoin" ),
            )

        );

        return array_merge_recursive( $icons, $new_fontawesome_icons_4_3 );
    }

    public function animation_field($settings, $value) {
    
        $dependency = vc_generate_dependencies_attributes($settings);

        $html ='<div class="wyde-animation">';
        $html .='<div class="animation-field">';
        $html .= sprintf('<select name="%1$s" class="wpb_vc_param_value %1$s %2$s_field" %3$s>', esc_attr( $settings['param_name'] ), esc_attr( $settings['type'] ), $dependency);

        $animations  = wyde_get_animations();

        foreach($animations as $key => $text){
            $html .= sprintf('<option value="%s" %s>%s</option>', esc_attr( $key ), ($value==$key? ' selected':''), esc_html( $text ) );
        }

        $html .= '</select></div>';
        $html .= '<div class="animation-preview"><span>Animation</span></div>';
        $html .= '</div>';

        return $html;

    }

    public function gmaps_field($settings, $value) {
    
        $dependency = vc_generate_dependencies_attributes($settings);

        $html ='<div class="wyde-gmaps">';
        $html .='<div class="gmaps-field">';
        $html .= sprintf('<input name="%1$s" class="wpb_vc_param_value %1$s %2$s_field" type="hidden" value="%3$s" %4$s/>', esc_attr( $settings['param_name'] ), esc_attr( $settings['type'] ), esc_attr( $value ), $dependency);
        $html .= sprintf('  <div class="edit_form_line"><input class="map-address" type="text" value="" /><span class="vc_description vc_clearfix">%s</span></div>', __('Enter text to display in the Info Window.', 'Vela'));
        
        $html .= '  <div class="vc_column vc_clearfix">';
        $html .= '      <div class="vc_col-sm-6">';
        $html .= sprintf('<div class="wpb_element_label">%s</div>', __('Map Type', 'Vela'));
        $html .= '          <div class="edit_form_line">';
        $html .= '              <select class="wpb-select dropdown map-type"><option value="1">Hybrid</option><option value="2">RoadMap</option><option value="3">Satellite</option><option value="4">Terrain</option></select>';
        $html .= '          </div>';
        $html .= '       </div>';
        $html .= '      <div class="vc_col-sm-6">';
        $html .= sprintf('<div class="wpb_element_label">%s</div>', __('Map Zoom', 'Vela'));
        $html .= '          <div class="edit_form_line">';
        $html .= '              <select class="wpb-select dropdown map-zoom">';
        for($i=1; $i<=20; $i++){
        $html .= sprintf('          <option value="%1$s">%1$s</option>', $i);
        }
        $html .= '              </select>';
        $html .= '          </div>';
        $html .= '      </div>';
        $html .= '  </div>';
        $html .= '</div>';
        $html .= '<div class="vc_column vc_clearfix">';
        $html .= sprintf('<span class="vc_description vc_clearfix">%s</span>', __('Drag & Drop marker to set your location.', 'Vela'));
        $html .= '  <div class="gmaps-canvas" style="height:300px;"></div>';
        $html .= '</div>';
        $html .= '</div>';

        return $html;

    }

    /*
    Load plugin css and javascript files which you may need on front end of your site
    */
    public function load_scripts() {
      
        //wp_enqueue_style( 'vc-extend-style', get_template_directory_uri() . '/shortcodes/css/vc-extend.css');
      
    }

    /*
    * Load admin scripts
    */
    public function load_admin_scripts() {
        
        wp_enqueue_script('vc-extend', get_template_directory_uri(). '/shortcodes/js/vc-extend.js', null, '1.4.3', true);

        //wp_enqueue_style( 'vc-extend-style', get_template_directory() . '/shortcodes/css/select2.min.css');
        wp_enqueue_style( 'vc-extend-style', get_template_directory_uri() . '/shortcodes/css/vc-extend.css', null, '1.4.3');
    
        wp_register_script('googlemaps', 'https://maps.googleapis.com/maps/api/js', null, null, false);
        wp_enqueue_script('googlemaps');

    }

    /*
    Show notice if this theme is activated but Visual Composer is not
    */
    public function show_vc_notice() {
        echo '<div class="updated"><p>'.__('<strong>This theme</strong> requires <strong><a href="http://bit.ly/vcomposer" target="_blank">Visual Composer</a></strong> plugin to be installed and activated on your site.', 'Vela').'</p></div>';
    }
    


}

new Vela_Shortcode();    
?>