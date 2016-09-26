<?php

defined( 'ABSPATH' ) or die( 'You cannot access this script directly' );

if( !class_exists('Wyde_Ajax_Importer') ) {

    class Wyde_Ajax_Importer {

        function __construct(){
            add_action('wp_ajax_wyde_importer', array($this, 'data_importer'));
            add_action('wp_ajax_nopriv_wyde_importer', array($this, 'data_importer'));
        }

        function data_importer() {


            if ( class_exists('Wyde_Importer') && current_user_can( 'manage_options' ) && isset( $_GET['type'] ) && !empty($_GET['type']) ) {
               
                    $demo = isset( $_GET['demo'] )? $_GET['demo'] : '1';
                    $type = isset( $_GET['type'] )? $_GET['type'] : 'settings';

                    $code = '';

                    $importer = new Wyde_Importer();
                    
                try{

                    switch( strtolower($type) ){
                        case 'posts':
                        $importer->import_demo_content(get_template_directory() . '/admin/importer/data/post.xml'); 
                        break;
                        case 'portfolios':
                        $importer->import_demo_content(get_template_directory() . '/admin/importer/data/portfolio.xml'); 
                        break;
                        case 'testimonials':
                        $importer->import_demo_content(get_template_directory() . '/admin/importer/data/testimonial.xml'); 
                        break;
                        case 'team members':
                        $importer->import_demo_content(get_template_directory() . '/admin/importer/data/team-member.xml'); 
                        break;
                        case 'pages':
                        $importer->import_demo_content(get_template_directory() . '/admin/importer/data/'.$demo.'/page.xml'); 
                        break;
                        case 'widgets':
                        $importer->import_widgets(get_template_directory_uri() . '/admin/importer/data/'.$demo.'/widget_data.txt');
                        break;
                        case 'sliders':
                        $importer->import_revsliders(get_template_directory() . '/admin/importer/data/'.$demo.'/revsliders/');
                        break;
                        case 'settings':
                        $importer->import_demo_content(get_template_directory() . '/admin/importer/data/'.$demo.'/menu.xml'); 
                        $this->default_settings();
                        break;
                    }   

                    echo json_encode( array('code' => '1', 'message' => __('All done', 'Flora') ) );        

                } catch (Exception $e) {
                    echo json_encode( array('code' => '2', 'message' => __('There has been an error.', 'Flora').' '. $e ) );           
                }

            }else{
                echo json_encode( array('code' => '-1', 'message' => __('Cannot access to Administration Panel options.', 'Flora') ) );           
            }

            exit;
               
        }


        function default_settings(){
            global $wp_rewrite;

            try{

                $this->set_menu_locations();

                $this->set_woocommerce_pages();

                // Settings -> Reading 
	            $homepage = get_page_by_title( 'Home' );
	            if(isset( $homepage ) && $homepage->ID) {
		            update_option('show_on_front', 'page');
		            update_option('page_on_front', $homepage->ID); // Front Page
	            }
                $posts_page = get_page_by_title( 'Blog' );
	            if(isset( $posts_page ) && $posts_page->ID) {
		            update_option('page_for_posts', $posts_page->ID); // Blog Page
	            }

                // Settings -> Permalinks
                //$wp_rewrite->set_permalink_structure('/%postname%/');
                //$wp_rewrite->flush_rules();

                return true;

             } catch (Exception $e) {
                return false;           
            }
        }

        function set_menu_locations(){
            // Set imported menus to registered theme locations
            $locations = get_theme_mod( 'nav_menu_locations' ); // get registered menu locations in theme
            $menus = wp_get_nav_menus(); // get registered menus

            if( $menus ) {
                foreach($menus as $menu) { // assign menus to theme locations
                    if( $menu->name == 'Primary' ) {
                        $locations['primary'] = $menu->term_id;
                    } else if( $menu->name == 'Footer' ) {
                        $locations['footer'] = $menu->term_id;
                    } else if( $menu->name == 'Header Top Bar' ) {
                        $locations['top'] = $menu->term_id;
                    }
                }
            }

            set_theme_mod( 'nav_menu_locations', $locations ); // set menus to locations
        }

        function set_woocommerce_pages(){
            if( class_exists('WooCommerce') ) {
            
                // Set woocommerce pages
			    $woopages = array(
				    'woocommerce_shop_page_id' => 'Shop',
				    'woocommerce_cart_page_id' => 'Cart',
				    'woocommerce_checkout_page_id' => 'Checkout',
				    'woocommerce_pay_page_id' => 'Checkout &#8594; Pay',
				    'woocommerce_thanks_page_id' => 'Order Received',
				    'woocommerce_myaccount_page_id' => 'My Account',
				    'woocommerce_edit_address_page_id' => 'Edit My Address',
				    'woocommerce_view_order_page_id' => 'View Order',
				    'woocommerce_change_password_page_id' => 'Change Password',
				    'woocommerce_logout_page_id' => 'Logout',
				    'woocommerce_lost_password_page_id' => 'Lost Password'
			    );

			    foreach($woopages as $woo_page_name => $woo_page_title) {
				    $woopage = get_page_by_title( $woo_page_title );
				    if(isset( $woopage ) && $woopage->ID) {
					    update_option($woo_page_name, $woopage->ID);
				    }
			    }

                // No longer need to install woocommerce pages
                delete_option( '_wc_needs_pages' );
                delete_transient( '_wc_activation_redirect' );
            }
        }

    }

}

new Wyde_Ajax_Importer();