<?php

global $wyde_sidebar_position;
    
$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts );
    

$attrs = array();

$classes = array();

$classes[] = 'row';

if( defined('ULTIMATE_VERSION') && ULTIMATE_VERSION ){ // requires for vc ultimate add-ons row options
    $classes[] = 'wpb_row';
}
    
if(!empty($el_class)) $classes[] =  $el_class;
if(!empty($alt_color)) $classes[] = 'alt-color';    
if(!empty($alt_bg_color)) $classes[] = 'alt-background-color';    
if(!empty($padding_size)) $classes[] = $padding_size;    
if(!empty($parallax)) $classes[] = 'parallax';    
if(!empty($full_width)) $classes[] = 'full';    
if(!empty($full_screen)) $classes[] = 'fullscreen';    
if(!empty($vertical_align)) $classes[] = 'v-align v-'.$vertical_align;

$background = '';
if( !empty($css) ){
    preg_match_all('~\bbackground-image\s*:(.*?)\(\s*(\'|")?(?<image>.*?)\3?\s*\)~i', $css, $matches);
    if( $matches && isset($matches['image']) && isset($matches['image'][0]) ) {

        //Generate inline CSS   
        $styles = explode(';', wyde_get_inline_css($css) );
        for ($i = 0; $i < count($styles); $i++) {
                if ( strrpos($styles[$i], 'url') !== false ) {
		        unset( $styles[$i] );
	        }
        }

        $attrs['style'] = implode(';', $styles);

        $background .= sprintf('<div class="bg-image" style="background-image:url(%s);"></div>', $matches['image'][0]);
            
    }else{
        $classes[] = vc_shortcode_custom_css_class( $css, '' );
    }
}

$attrs['class'] = preg_replace( '/\s+/', ' ', apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, implode( ' ', array_filter( array_unique( $classes ) ) ), $this->settings['base'], $atts ) );

$overlay = '';
if($background_overlay){
    if($background_overlay == 'pattern'){
        $background .= '<div class="section-overlay pattern-overlay"></div>';
    }else{
        $background .= sprintf('<div class="section-overlay"%s></div>', (!empty($overlay_color)?' style="background-color:'.esc_attr( $overlay_color ).';"':''));
    }
}

if( $background ){
    $background = '<div class="bg-wrapper">'.$background.'</div>';
}

$mask_shape = '';
if(!empty($mask)){
    $mask_left = intval($mask_style);
    $mask_right = 100 - $mask_left;
    $mask_shape = sprintf('<span class="mask mask-%s" style="border-color:%s;border-left-width:%svw;border-right-width:%svw;"></span>', esc_attr( $mask ), esc_attr( $mask_color ), esc_attr( $mask_left ), esc_attr( $mask_right ));  
} 


$output =  sprintf('<div%s>%s%s%s</div>', wyde_get_attributes( $attrs ), $background, wpb_js_remove_wpautop($content), $mask_shape);  

echo $output;