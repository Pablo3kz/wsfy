<?php
    
    $atts = vc_map_get_attributes( $this->getShortcode(), $atts );
    extract( $atts );

    $attrs = array();

    $classes = array();

    $classes[] = 'link-button';

    if( !empty( $size ) ){
        $classes[] = $size;
    } 

    if( !empty( $style ) ){
        $classes[] = $style;
    } 

    if( !empty($el_class) ){
        $classes[] = $el_class;
    }

	$attrs['class'] = implode(' ', $classes);

    if( !empty( $color ) ){
        $attrs['style'] = 'color:'.$color.';border-color:'.$color.';';
    }

    if( !empty( $hover_color ) ){
        $attrs['data-hover-color'] = $hover_color;
    }

    if($animation) $attrs['data-animation'] = $animation;
    if($animation_delay) $attrs['data-animation-delay'] = floatval( $animation_delay );

    if( !empty($icon_set) ){
        vc_icon_element_fonts_enqueue( $icon_set );
        $icon = ${"icon_" . $icon_set};
    } 

    $link = ( $link == '||' ) ? '' : $link;
       
    $link = vc_build_link( $link );

    $attrs['href'] = empty($link['url'])? '#':$link['url']; 

    if( !empty($link['title']) ){
        $attrs['title'] = $link['title']; 
    } 

    if( !empty($link['target']) ){
        $attrs['target'] = trim($link['target']);
    } 

    $inline_css = '';
    if( !empty( $color )){
        $inline_css = ' style="background-color:'.esc_attr( $color ).';"';
    }
?>
<a<?php echo wyde_get_attributes( $attrs );?>>
    <span<?php echo $inline_css;?>></span>
    <?php if( !empty($icon) ):?>
    <i class="<?php echo esc_attr( $icon );?>"></i>
    <?php endif; ?>
    <?php if( !empty($title) ):?>
    <?php echo esc_html( $title );?>
    <?php endif; ?>
</a>