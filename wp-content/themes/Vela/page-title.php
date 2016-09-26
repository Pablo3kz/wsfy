<?php
global $wyde_options, $wyde_page_id, $wyde_title_area;


if( $wyde_title_area != 'hide' ){

    $title_css = array();
    $title_styles = array();

    $title_bg_video = '';
    $title_overlay = '';

    $title_align = get_post_meta( $wyde_page_id, '_meta_title_align', true );
    if($title_align == '') $title_align =  $wyde_options['title_align'];

    if($title_align != 'center') $title_css[] = 'text-'. $title_align;


    $title_mask = get_post_meta( $wyde_page_id, '_meta_title_mask', true );
        
    if($title_mask !== ''){
        $title_mask_color =get_post_meta( $wyde_page_id, '_meta_title_mask_color', true ); 
    }else{
        $title_mask = $wyde_options['title_mask']; 
        if($title_mask != 'none') $title_mask_color = $wyde_options['title_mask_color']; 
    }


    $title_bg = get_post_meta( $wyde_page_id, '_meta_title_background', true );

    if($title_bg != ''){//customized page title
            
        if($title_bg != 'none'){
            

            if($title_bg == 'image'){    

                $title_bg_image = get_post_meta( $wyde_page_id, '_meta_title_background_image', true );                           
                
                $title_css[] = 'background-'.get_post_meta( $wyde_page_id, '_meta_title_background_size', true );

                if(get_post_meta( $wyde_page_id, '_meta_title_background_parallax', true ) == 'on') $title_css[] = 'parallax';

            }else if($title_bg == 'video'){

                $title_bg_image = get_post_meta( $wyde_page_id, '_meta_title_background_image', true );      

                $title_bg_video =   get_post_meta( $wyde_page_id, '_meta_title_background_video', true ); 

                $title_css[] = 'background-cover';

            }

            $title_bg_color = get_post_meta( $wyde_page_id, '_meta_title_background_color', true );
            if($title_bg_color!='') $title_styles[] = 'background-color:'.$title_bg_color.';';
    
            $title_overlay = get_post_meta( $wyde_page_id, '_meta_title_overlay', true ); 
            if($title_overlay == 'color') $title_overlay_color = get_post_meta( $wyde_page_id, '_meta_title_overlay_color', true ); 


        }
    }else{//use default theme options

        $title_bg = $wyde_options['title_background_mode'];

        if($title_bg != 'none'){               

            if($title_bg == 'image'){   

                $title_bg_image = $wyde_options['title_background_image']['background-image'];     
                
                $title_css[] = 'background-'.$wyde_options['title_background_image']['background-size'];

                if($wyde_options['title_background_parallax'] == true) $title_css[] = 'parallax';

            }else if($title_bg == 'video'){
                $title_bg_image = $wyde_options['title_background_image']['background-image']; 

                $title_bg_video =   $wyde_options['title_background_video']['url']; 

                $title_css[] = 'background-cover';
            }

            $title_bg_color = $wyde_options['title_background_color'];
            if($title_bg_color!='') $title_styles[] = 'background-color:'.$title_bg_color.';';
    
            $title_overlay = $wyde_options['title_overlay']; 
            if($title_overlay == 'color') $title_overlay_color = $wyde_options['title_overlay_color']; 

        }

    }

    if( !empty($title_bg_image) ){
        $title_styles[] = 'background-image:url(\''. esc_url( $title_bg_image ) .'\');';
    }

    if($title_overlay != '') $title_css[] = 'with-overlay';
    if(!empty($title_mask) && $title_mask != 'none') $title_css[] = 'with-mask';

    if($wyde_options["header_fluid"] == 1) $title_css[] = 'full';

?>
<div class="title-wrapper <?php echo esc_attr( implode(' ', $title_css) ); ?>">
    <?php if( count($title_styles) > 0 ): ?>
    <div class="bg-image" style="<?php echo esc_attr( implode(' ', $title_styles) ); ?>"></div>
    <?php endif; ?>
    <?php if( !empty($title_bg_video) ){ ?>
    <div class="vdobg-wrapper">
        <video class="vdobg" autoplay loop muted poster="<?php echo esc_url( $title_bg_image ); ?>">
            <source src="<?php echo esc_url( $title_bg_video ); ?>" type="video/mp4" />            
        </video>
    </div>
    <?php }?>
    <?php if($title_overlay != ''){ ?>
    <div class="section-overlay<?php echo $title_overlay == 'pattern'? ' pattern-overlay':'';?>"<?php echo ($title_overlay=='color' && !empty($title_overlay_color))?' style="background-color:'.esc_attr( $title_overlay_color ).';"':'';?>></div>
    <?php }?>
    <div class="container">
        <div class="title">
            <h1>
            <?php 
            $title = get_the_title();

		    if( is_home() ) {
			    $title = esc_html( $wyde_options['blog_title'] );
		    }

		    if( is_search() ) {
			    $title = __('Search', 'Vela');
		    }

		    if( is_404() ) {
			    $title = __('Error 404 Page', 'Vela');
		    }

		    if( is_archive() ) {
			    if ( is_day() ) {
				    $title = '<p>'. __( 'Daily Archives', 'Vela' ) . '</p><p>' . get_the_date() . '</p>';
			    } else if ( is_month() ) {
				    $title = '<p>'. __( 'Monthly Archives', 'Vela' ) . '</p><p>' . get_the_date('F Y', 'Vela') . '</p>';
			    } elseif ( is_year() ) {
				    $title = '<p>'. __( 'Yearly Archives', 'Vela' ) . '</p><p>' . get_the_date('Y', 'Vela') . '</p>';
			    } elseif ( is_author() ) {
				    $author = ( isset( $_GET['author_name'] ) ) ? get_user_by( 'slug', $_GET['author_name'] ) : get_user_by(  'id', get_the_author_meta('ID') );
				    $title = $author->display_name;
			    } else {
				    $title = single_cat_title( '', false );
			    }
		    }
		    if( class_exists( 'Woocommerce' ) && is_woocommerce() && ( is_product() || is_shop() ) && ! is_search() ) {
			    if( ! is_product() ) {
				    $title = esc_html( woocommerce_page_title( false ) );
			    }
		    }
            echo $title;
            ?>
            </h1>
        </div>
    </div>
    <?php
    if(!empty($title_mask) && $title_mask != 'none'){
        $mask_left = intval($title_mask);
        $mask_right = 100 - $mask_left;
    ?>
    <span class="mask mask-bottom" style="border-color:<?php echo esc_attr( $title_mask_color );?>;border-left-width:<?php echo esc_attr( $mask_left );?>vw;border-right-width:<?php echo esc_attr( $mask_right );?>vw;"></span>  
    <?php } ?>
</div>
<?php } ?>