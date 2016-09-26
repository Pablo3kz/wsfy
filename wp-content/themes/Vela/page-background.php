<?php
global $wyde_options, $wyde_page_id;

$page_bg_css = array();
$page_bg_styles = array();
$page_bg_video = '';
$page_overlay = '';
$page_overlay_color = '';

$page_bg = get_post_meta( $wyde_page_id, '_meta_background', true );

if($page_bg != ''){//customized background
    if($page_bg != 'none'){

        $page_bg_css[] = 'background';

       

        if($page_bg == 'image'){   

            $page_bg_image =   get_post_meta( $wyde_page_id, '_meta_background_image', true ); 

            if($page_bg_image != ''){
                $page_bg_styles[] = 'background-image:url(\''. esc_url( $page_bg_image ) .'\');';
            }        
            
            $page_bg_css[] = 'background-'.get_post_meta( $wyde_page_id, '_meta_background_size', true );

            if(get_post_meta( $wyde_page_id, '_meta_background_parallax', true ) == 'on') $page_bg_css[] = 'parallax';

        }else if($page_bg == 'video'){
            $page_bg_image =   get_post_meta( $wyde_page_id, '_meta_background_image', true ); 
            
            if($page_bg_image != ''){
                $page_bg_styles[] = 'background-image:url(\''. esc_url( $page_bg_image ) .'\');';
            }    
            $page_bg_video =   get_post_meta( $wyde_page_id, '_meta_background_video', true ); 
            $page_bg_css[] = 'background-cover';
        }

        $page_bg_color = get_post_meta( $wyde_page_id, '_meta_background_color', true );
        if($page_bg_color!='') $page_bg_styles[] = 'background-color:'.$page_bg_color.';';
    
        $page_overlay = get_post_meta( $wyde_page_id, '_meta_background_overlay', true ); 
        if($page_overlay != '') $page_bg_css[] = 'with-overlay';
        if($page_overlay == 'color') $page_overlay_color = get_post_meta( $wyde_page_id, '_meta_background_overlay_color', true ); 

    }
}else{//use default theme options

    $page_bg = $wyde_options['page_background_mode'];
    
    if($page_bg != 'none'){
    
        $page_bg_css[] = 'background';        

        if($page_bg == 'image'){     

            $page_bg_image = $wyde_options['page_background_image']['background-image']; 
            if($page_bg_image != ''){
                $page_bg_styles[] = 'background-image:url(\''. esc_url( $page_bg_image ) .'\');';
            } 

            $page_bg_css[] = 'background-'.$wyde_options['page_background_image']['background-size'];

            if($wyde_options['page_background_parallax'] == true) $page_bg_css[] = 'parallax';

        }else if($page_bg == 'video'){
            
            $page_bg_image = $wyde_options['page_background_image']['background-image']; 
            if($page_bg_image != ''){
                $page_bg_styles[] = 'background-image:url(\''. esc_url( $page_bg_image ) .'\');';
            }
            $page_bg_video =   $wyde_options['page_background_video']['url']; 
            $page_bg_css[] = 'background-cover';
        }

        $page_bg_color = $wyde_options['page_background_color'];
        if($page_bg_color!='') $page_bg_styles[] = 'background-color:'.$page_bg_color.';';
        
        $page_overlay = $wyde_options['page_overlay']; 
        if($page_overlay != '') $page_bg_css[] = 'with-overlay';
        if($page_overlay == 'color') $page_overlay_color = $wyde_options['page_overlay_color']; 

    }
}
?>
<div class="page-inner <?php echo esc_attr( implode(' ', $page_bg_css) ); ?>" style="<?php echo esc_attr( implode(' ', $page_bg_styles) ); ?>">
    <?php if($page_bg_video){ ?>
    <div class="vdobg-wrapper">
        <video class="vdobg" autoplay loop muted poster="<?php echo esc_url( $page_bg_image ); ?>">
            <source src="<?php echo esc_url( $page_bg_video ); ?>" type="video/mp4" />
        </video>
    </div>
    <?php }?>
    <?php if($page_overlay){ ?>
    <div class="section-overlay<?php echo $page_overlay == 'pattern'? ' pattern-overlay':'';?>"<?php echo ($page_overlay=='color' && !empty($page_overlay_color))? ' style="background-color:'. esc_attr( $page_overlay_color ).';"':'';?>>
    </div>
    <?php }?>
