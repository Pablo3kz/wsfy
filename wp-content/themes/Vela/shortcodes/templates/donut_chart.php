<?php

    global $wyde_color_scheme, $vc_is_inline;

	extract( shortcode_atts( array(
            'title' => '',
            'value' => 80,
            'label_mode' => '',
            'label'  => '',
            'icon_set' => '',
            'icon' => '',
	        'icon_openiconic' => '',
	        'icon_typicons' => '',
	        'icon_entypoicons' => '',
	        'icon_linecons' => '',
	        'icon_entypo' => '',
            'style' => '',
            'type'  => '',
            'bar_color'   => '',
            'bar_border_color'   => '',
            'fill_color'   => '',
            'start'   => '',
            'animation' =>  '',
            'animation_delay' =>  0,
            'el_class' =>  '',
            'css' =>  '',
    ), $atts ) );


	$attrs = array();

    $classes = array();

    $classes[] = 'donut-chart';

    if( !empty($el_class) ){
        $classes[] = $el_class;
    }

    if( !empty($css) ) $classes[] = vc_shortcode_custom_css_class( $css, '' );

	$attrs['class'] = implode(' ', $classes);

        
    if( !empty($value) ){
        $attrs['data-value'] =  intval( $value );
    } 
        
    if($label_mode == 'icon'){
        $attrs['data-icon'] = $icon;
    }else{
        if( !empty($label) ){
            $attrs['data-label'] = $label;
        }
    }

    if( !empty($title) ){
        $attrs['data-title'] = $title;
    } 

    if( !empty($style) ){
        $attrs['data-border'] = $style;
    } 

    if( !empty($bar_color) ){
        $attrs['data-color'] = $bar_color;
    }else{
        $attrs['data-color'] = $wyde_color_scheme;            
    } 

    if( !empty($bar_border_color) ){
        $attrs['data-bgcolor'] = $bar_border_color;
    } 

    if( !empty($fill_color) ){
        $attrs['data-fill'] = $fill_color;
    } 

    if( !empty($start) ){
        $attrs['data-startdegree'] = $start;
    } 

    if( !empty($type) ){
        $attrs['data-type'] = $type;
    } 

    if($animation) $attrs['data-animation'] = $animation;
    if($animation_delay) $attrs['data-animation-delay'] = floatval( $animation_delay );

    if( !empty($icon_set) ){
        vc_icon_element_fonts_enqueue( $icon_set );
        $icon = ${"icon_" . $icon_set};
    } 
?>
<div<?php echo wyde_get_attributes( $attrs );?>></div>