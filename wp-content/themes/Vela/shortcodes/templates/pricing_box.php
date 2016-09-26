<?php

    $atts = vc_map_get_attributes( $this->getShortcode(), $atts );
    extract( $atts );

    $attrs = array();

    $classes = array();

    $classes[] = 'pricing-box';

    if($featured == 'true') $classes[] = 'featured';

    if( !empty($el_class) ){
        $classes[] = $el_class;
    }

    $attrs['class'] = implode(' ', $classes);

    $styles = array();

    if( !empty($bg_color) ) $styles[] = 'background-color:'.$bg_color;
    if( !empty($text_color) ) $styles[] = 'color:'.$text_color;

    $attrs['style'] = implode(';', $styles);

    if($animation) $attrs['data-animation'] = $animation;
    if($animation_delay) $attrs['data-animation-delay'] = floatval( $animation_delay );
?>
<div<?php echo wyde_get_attributes( $attrs );?>>
    <?php if( !empty($heading) ): ?>      
    <div class="price-heading">
        <h3><?php echo esc_html( $heading );?></h3>
        <?php if( !empty($sub_heading) ): ?>
        <h5><?php echo esc_html( $sub_heading );?></h5>
        <?php endif; ?>
    </div>
    <?php endif; ?>
    <?php if( !empty($price) ): ?> 
    <div class="price-block">
        <h4 class="price-value"><?php echo esc_html( $price ); ?></h4>
        <span class="price-unit"><?php echo esc_html( $price_unit ); ?></span>
    </div>
    <?php endif; ?>
    <div class="price-content">
    <?php if( !empty($content) ): ?>
    <?php echo wpb_js_remove_wpautop($content, true); ?>
    <?php endif;?>
    </div>
    <div class="price-button">
    <?php if( !empty($button_text) ): ?> 
    <?php
        $link = ( $link == '||' ) ? '' : $link;
        $link = vc_build_link( $link );

        $link_attrs = array();
        $link_attrs['href'] = empty($link['url'])? '#':$link['url']; 

        if( !empty($link['title']) ){
           $link_attrs['title'] = $link['title']; 
        } 
        if( !empty($link['target']) ){
            $link_attrs['target'] = trim($link['target']);
        } 

    ?>
        <a<?php echo wyde_get_attributes( $link_attrs );?>><?php echo esc_html( $button_text ); ?></a>
    <?php endif;?>
    </div>
</div>