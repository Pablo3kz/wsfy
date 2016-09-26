<?php
        
    $atts = vc_map_get_attributes( $this->getShortcode(), $atts );
    extract( $atts );

    $attrs = array();

    $classes = array();

    $classes[] = 'gmaps';

    if( !empty($el_class) ){
        $classes[] = $el_class;
    }
        
	$attrs['class'] = implode(' ', $classes);

    $icon_id = preg_replace( '/[^\d]/', '', $icon );

    $icon_url = wp_get_attachment_url($icon_id);

    if(!$icon_url) $icon_url = get_template_directory_uri().'/images/pin.png';


    if(!empty($icon_url)) $attrs['data-icon'] = esc_url( $icon_url );

    $attrs['data-maps'] = $gmaps;
       
    if(!empty($color)) $attrs['data-color'] = $color;

    $height = str_replace( array( 'px', ' ' ), array( '', '' ), $height );
    if ( is_numeric( $height ) ){
        $attrs['style'] = 'height:'.$height.'px';
        $attrs['data-height'] = absint( $height );
    } 
        
?>
<div<?php echo wyde_get_attributes( $attrs );?>>
    <div class="map-canvas"></div>
</div>