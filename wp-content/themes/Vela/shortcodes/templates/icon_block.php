<?php 

    $atts = vc_map_get_attributes( $this->getShortcode(), $atts );
    extract( $atts );

    $attrs = array();

    $classes = array();

    $classes[] = 'icon-block';

    $classes[] = 'effect-'.$hover;
    $classes[] = 'icon-'.$size;
    $classes[] = 'icon-'.$style;

    if( !empty($el_class) ){
        $classes[] = $el_class;
    }

    if( !empty($css) ) $classes[] = vc_shortcode_custom_css_class( $css, '' );

	$attrs['class'] = implode(' ', $classes);

    if( ! empty( $color ) ){
        $attrs['style'] = 'background-color:'.$color.';border-color:'.$color.';';
    }

    if($animation) $attrs['data-animation'] = $animation;
    if($animation_delay) $attrs['data-animation-delay'] = floatval( $animation_delay );

    if( !empty($icon_set) ){
        vc_icon_element_fonts_enqueue( $icon_set );
        $icon = ${"icon_" . $icon_set};
    } 

    $link = ( $link == '||' ) ? '' : $link;
       
    $link = vc_build_link( $link );

    $link_attrs = array();

    if( !empty($link['url']) ){
        
        $link_attrs['href'] = $link['url']; 
    
        if( !empty($link['target']) ){
            $link_attrs['target'] = trim($link['target']);
        } 
    }
       
    if( !empty($link['title']) ){
        $attrs['title'] = $link['title'];    
    }

    $inline_css = '';
    if( isset( $attrs['style'] )){
        $inline_css = ' style="'.esc_attr( $attrs['style'] ).'"';
    }

?>
<span<?php echo wyde_get_attributes( $attrs ); ?>>
    <?php if( !empty($link['url']) ):?>
    <a<?php echo wyde_get_attributes( $link_attrs ); ?>>
    <?php endif; ?>
        <?php if( !empty($icon) ):?>
        <i class="<?php echo esc_attr( $icon );?>"></i>
        <?php endif; ?>
    <?php if( !empty($link['url']) ):?>
    </a>
    <?php endif; ?>
    <span class="border"<?php echo $inline_css;?>></span>
</span>