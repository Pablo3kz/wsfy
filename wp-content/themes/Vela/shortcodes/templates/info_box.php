<?php
        
    $atts = vc_map_get_attributes( $this->getShortcode(), $atts );
    extract( $atts );

    $attrs = array();

    $classes = array();

    $classes[] = 'info-box clear';

    if( !empty($icon_position) ) $classes[] = 'icon-'.$icon_position;
    if( !empty($icon_size) ) $classes[] = 'icon-'.$icon_size;        
    if( !empty($style) ) $classes[] = 'border-'.$style;

    if( !empty($el_class) ){
        $classes[] = $el_class;
    }

    if( !empty($css) ) $classes[] = vc_shortcode_custom_css_class( $css, '' );

	$attrs['class'] = implode(' ', $classes);

    if($animation) $attrs['data-animation'] = $animation;
    if($animation_delay) $attrs['data-animation-delay'] = floatval( $animation_delay );

    if( !empty($icon_set) ){
        vc_icon_element_fonts_enqueue( $icon_set );
        $icon = ${"icon_" . $icon_set};
    } 

    $icon_col = $content_col = ' col-sm-12';

    if($icon_position != 'top'){
        $icon_col = ' col-sm-3';
        $content_col = ' col-sm-9';
    }

    $inline_css = '';
    if( !empty( $color ) ){
        $inline_css = sprintf(' style="border-color:%1$s;color:%1$s;"', esc_attr( $color ));
    }
?>
<div<?php echo wyde_get_attributes( $attrs );?>>
    <div class="box-icon<?php echo $icon_col;?>"<?php echo $inline_css;?>>
        <?php if( !empty( $icon ) ):?>
        <span class="icon-wrapper"><i class="<?php echo esc_attr( $icon );?>"></i></span>
        <?php endif;?>
    </div>
    <div class="box-content<?php echo $content_col;?>">
        <?php if( !empty( $title ) ):?>
        <h3><?php echo esc_html( $title );?></h3>
        <?php endif;?>
        <?php if( !empty( $content ) ):?>
        <?php echo wpb_js_remove_wpautop($content, true); ?>
        <?php endif;?>
    </div>
</div>


