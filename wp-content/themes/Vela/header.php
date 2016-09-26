<!DOCTYPE html>
<html <?php language_attributes(); ?>>
    <head>
        <meta charset="<?php bloginfo( 'charset' ); ?>">
        <?php global $wyde_options; ?>
        <?php 
        $disable_zoom = ', maximum-scale=1.0, user-scalable=no';
        if( isset($wyde_options['mobile_zoom']) && $wyde_options['mobile_zoom'] ){
            $disable_zoom = '';
        }
        ?>             
        <!--<meta name="viewport" content="width=device-width, initial-scale=1.0<?php echo esc_attr($disable_zoom); ?>" />-->
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <title><?php wp_title(''); ?></title>
        <?php if ( ! ( function_exists( 'has_site_icon' ) && has_site_icon() ) ) { ?>
        <?php if( !empty($wyde_options['favicon_image']['url']) ): ?>
        <link rel="icon" href="<?php echo esc_url( $wyde_options['favicon_image']['url'] ); ?>" type="image/png" />
        <?php endif; ?>
        <?php if( !empty($wyde_options['favicon']['url']) ): ?>
        <link rel="shortcut icon" href="<?php echo esc_url( $wyde_options['favicon']['url'] ); ?>" type="image/x-icon" />
        <?php endif; ?>
        <?php if( !empty($wyde_options['favicon_iphone']['url']) ): ?>
        <link rel="apple-touch-icon-precomposed" href="<?php echo esc_url( $wyde_options['favicon_iphone']['url'] ); ?>">
        <?php endif; ?>
        <?php if( !empty($wyde_options['favicon_iphone_retina']['url']) ): ?>
        <link rel="apple-touch-icon-precomposed" sizes="114x114" href="<?php echo esc_url( $wyde_options['favicon_iphone_retina']['url'] ); ?>">
        <?php endif; ?>
        <?php if( !empty($wyde_options['favicon_ipad']['url']) ): ?>
        <link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?php echo esc_url( $wyde_options['favicon_ipad']['url'] ); ?>">
        <?php endif; ?>
        <?php if( !empty($wyde_options['favicon_ipad_retina']['url']) ): ?>
        <link rel="apple-touch-icon-precomposed" sizes="144x144" href="<?php echo esc_url( $wyde_options['favicon_ipad_retina']['url'] ); ?>">
        <?php endif; ?>
        <?php } ?>        
        <?php
        global $wyde_page_id, $wyde_header_overlay, $wyde_title_area;

        $body_classes = array($wyde_options['layout']=='boxed'? 'boxed':'wide');

        if($wyde_options['onepage']) $body_classes[] = 'onepage';
        
        if($wyde_options['boxed_shadow']) $body_classes[] = 'boxed-shadow';
        
        if($wyde_options['background_mode'] == 'pattern' && isset( $wyde_options['background_pattern'] ) ) $body_classes[] = 'pattern-'. $wyde_options['background_pattern'];

        if($wyde_options['background_mode'] == 'pattern' && $wyde_options['background_pattern_fixed']) $body_classes[] = 'background-fixed';

        if($wyde_options['background_mode'] != 'pattern' && $wyde_options['background_pattern_overlay']) $body_classes[] = 'background-overlay';

        
        $wyde_page_id = get_the_ID();        

        if( is_home() || is_search() || ( ( is_archive() || is_author() ) && get_post_type(get_the_ID()) == 'post' ) ){
            $blog_page_id = get_option('page_for_posts');
            if($blog_page_id) $wyde_page_id = $blog_page_id;
        }

        if( class_exists('WooCommerce') && ( is_shop() || is_product_category() ) ){
            $wyde_page_id = get_option('woocommerce_shop_page_id');
        }

        $wyde_page_header = get_post_meta( $wyde_page_id, '_meta_page_header', true );


        if( $wyde_options["header_transparent"] ){            
            $wyde_header_overlay = 1;
        }else{
            $wyde_header_overlay = 0;
        }       

        $slider = get_post_meta( $wyde_page_id, '_meta_slider_show', true ) == 'on';

        if( $slider == true && $wyde_page_header != 'hide' && $wyde_options['header_position'] == '2' ){
            $body_classes[] = 'top-slider';
            $wyde_header_overlay = 0;
        }

        if( $wyde_page_header != 'hide' && !$wyde_header_overlay ) $body_classes[] = 'header-space-v'.$wyde_options['header_layout'];

        $wyde_title_area = get_post_meta( $wyde_page_id, '_meta_title', true );

        if( is_single() ){
            if( get_post_meta( $wyde_page_id, '_meta_post_custom_title', true ) != 'on' ){
                $wyde_title_area = 'show';
            }
        }

        if( $wyde_title_area == 'hide' && (!$slider || $wyde_options['header_position'] == '2') ){
            $body_classes[] = 'no-title';
        }

        ?>
        <?php wp_head(); ?>
    </head>
    <body <?php body_class( esc_attr( implode(' ', $body_classes) ) ); ?>>        
        <div id="container" class="container">            
            <div id="preloader">
                <?php
                    $loader_version = isset($wyde_options['page_loader'])? $wyde_options['page_loader']:'1';
                ?>
                <div id="loading-animation" class="loader-<?php echo $loader_version; ?>">
                <?php wyde_page_loader( $loader_version ); ?>
                </div>
            </div>
            <div id="page">

                <?php get_template_part('page', 'background');?>
                <?php
                if( $wyde_page_header != 'hide' && $wyde_options["header_position"] == '1' ){   
                    get_template_part( 'inc/header' );
                }
                ?>
                <?php                
                if($slider == true) {
                ?>
                <div id="slider">
                <?php
                    wyde_get_slider(get_post_meta( $wyde_page_id, '_meta_slider_item', true ));
                ?>
                </div>
                <?php
                }
                if($wyde_page_header != 'hide' && $wyde_options['header_position'] == '2' ){    
                    get_template_part( 'inc/header' );
                }                
                ?>
                <div id="content">
                <?php
                get_template_part('page', 'title');