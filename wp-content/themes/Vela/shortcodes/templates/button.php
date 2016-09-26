<?php

    $atts = vc_map_get_attributes( $this->getShortcode(), $atts );
    extract( $atts );

    $attrs = array();

    $classes = array();

    switch($style){
        case 'round':
            $classes[] = 'button round';
        break;
        case 'outline':
            $classes[] = 'ghost-button square';
        break;
        case 'round-outline':
            $classes[] = 'ghost-button round';
        break;
        default:
            $classes[] = 'button square';
        break;
    }

    if( !empty( $size ) ){
        $classes[] = $size;
    } 
        

    if( !empty($el_class) ){
        $classes[] = $el_class;
    }

    $attrs['class'] = implode(' ', $classes);

    if( !empty( $color ) ){
        if($style == 'outline' || $style == 'round-outline'){
            $attrs['style'] = 'border-color:'.$color.';color:'.$color.';';
        }else{
            $attrs['style'] = 'border-color:'.$color.';background-color:'.$color.';';
        }
    }

    if($animation) $attrs['data-animation'] = $animation;
    if($animation_delay) $attrs['data-animation-delay'] = floatval( $animation_delay );


    $link = ( $link == '||' ) ? '' : $link;
    
    $link = vc_build_link( $link );

    $attrs['href'] = $link['url']; 

    if( !empty($link['title']) ){
        $attrs['title'] = $link['title'];    
    }

    if( !empty($link['target']) ){
        $attrs['target'] = trim($link['target']);
    } 
?>
<a<?php echo wyde_get_attributes( $attrs );?>>
<?php echo esc_html( trim( $title ) ); ?>
</a>