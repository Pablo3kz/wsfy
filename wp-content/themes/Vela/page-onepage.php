<div class="main-content full-width">
    <?php
	if (($locations = get_nav_menu_locations()) && $locations['primary'] ) {
        $menu = wp_get_nav_menu_object( $locations['primary'] );
        $menu_items = wp_get_nav_menu_items($menu->term_id);
        $wyde_page_ids = array();

        foreach($menu_items as $item) {
            if($item->object == 'page' && $item->menu_item_parent == 0 && !(strpos($item->url, '#') === 0))
                $page_ids[] = $item->object_id;
        }

        $page_posts = new WP_Query( array( 'post_type' => 'page', 'post__in' => $page_ids, 'posts_per_page' => count($page_ids), 'orderby' => 'post__in' ) );
    

	while ( $page_posts->have_posts() ) : $page_posts->the_post();

    $page_bg_css = array();
    $page_bg_style = array();
    $page_bg_video = '';
    $page_overlay = '';

    $page_bg = get_post_meta( $post->ID, '_meta_background', true );

    if($page_bg != ''){//customized background
        if($page_bg != 'none'){
            array_push($page_bg_css, 'background');

            if($page_bg == 'image'){
                $page_bg_image =   get_post_meta( $post->ID, '_meta_background_image', true ); 
                if($page_bg_image != ''){
                    array_push($page_bg_style, 'background-image:url(\''. esc_url( $page_bg_image ) .'\');');
                }
            
                array_push($page_bg_css, 'background-'.get_post_meta( $post->ID, '_meta_background_size', true ));

                if(get_post_meta( $post->ID, '_meta_background_parallax', true ) == 'on') array_push($page_bg_css, 'parallax');

            }else if($page_bg == 'video'){
                $page_bg_video =   get_post_meta( $post->ID, '_meta_background_video', true ); 
            }

            $page_bg_color = get_post_meta( $post->ID, '_meta_background_color', true );
            if($page_bg_color!='') array_push($page_bg_style, 'background-color:'.$page_bg_color.';');
    
            $page_overlay = get_post_meta( $post->ID, '_meta_background_overlay', true ); 
            if($page_overlay != '') array_push($page_bg_css, 'with-overlay');
            if($page_overlay == 'color') $page_overlay_color = get_post_meta( $post->ID, '_meta_background_overlay_color', true ); 

        }
    }else{//use default theme options

        global $wyde_options;

        $page_bg = $wyde_options['page_background_mode'];
    
        if($page_bg != 'none'){
    
            array_push($page_bg_css, 'background');

            if($page_bg == 'image'){
        
                $page_bg_image = $wyde_options['page_background_image']['background-image']; 
                if($page_bg_image != ''){
                    array_push($page_bg_style, 'background-image:url(\''. esc_url( $page_bg_image ) .'\');');
                }

                array_push($page_bg_css, 'background-'.$wyde_options['page_background_image']['background-size']);

                if($wyde_options['page_background_parallax'] == true) array_push($page_bg_css, 'parallax');

            }else if($page_bg == 'video'){
                $page_bg_video =   $wyde_options['page_background_video']['url']; 
            }

            $page_bg_color = $wyde_options['page_background_color'];
            if($page_bg_color!='') array_push($page_bg_style, 'background-color:'.$page_bg_color.';');
        
            $page_overlay = $wyde_options['page_overlay']; 
            if($page_overlay != '') array_push($page_bg_css, 'with-overlay');
            if($page_overlay == 'color') $page_bg_overlay_color = $wyde_options['page_overlay_color']; 

        }

    }
    ?>	
	<section id="<?php echo esc_attr( $post->post_name );?>" class="content-wrapper <?php echo esc_attr( implode(' ', $page_bg_css) ); ?>" style="<?php echo esc_attr( implode(' ', $page_bg_style) ); ?>">
        <?php 
        $post_custom_css = get_post_meta( $post->ID, '_wpb_post_custom_css', true );
        if ( ! empty( $post_custom_css ) ) {
            echo '<style type="text/css" data-type="vc_custom-css">';
            echo esc_textarea( $post_custom_css );
            echo '</style>';
        }

        $shortcodes_custom_css = get_post_meta( $post->ID, '_wpb_shortcodes_custom_css', true );
        if ( ! empty( $shortcodes_custom_css ) ) {
            echo '<style type="text/css" data-type="vc_shortcodes-custom-css">';
            echo esc_textarea( $shortcodes_custom_css );
            echo '</style>';
        }
        ?>
        <?php if( !empty( $page_bg_video ) ){ ?>
        <div class="vdobg-wrapper">
            <video class="vdobg" autoplay loop muted>
                <source src="<?php echo esc_url( $page_bg_video ); ?>" type="video/mp4" />
            </video>
        </div>
        <?php }?>
        <?php if($page_overlay): ?>
        <div class="section-overlay<?php echo $page_overlay == 'pattern'?' pattern-overlay':'';?>"<?php echo ($page_overlay=='color' && !empty($page_overlay_color))?' style="background-color:'.esc_attr( $page_overlay_color ).';"':'';?>>
        </div>
        <?php endif; ?>
        <?php
        if(get_post_meta( $post->ID, '_meta_title', true ) != 'hide'){
        ?>
        <div class="section-title"><h2><?php echo esc_html(  get_the_title() );?></h2></div>
        <?php } ?>
		<?php the_content(); ?>    
    </section>
    <?php
	endwhile;
    wp_reset_postdata();	
    }
    ?>		
</div>