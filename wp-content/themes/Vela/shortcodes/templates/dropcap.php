<?php
        
    extract( shortcode_atts( array(
        'color'  => '',
    ), $atts ) );

    $attrs = array();

	$attrs['class'] = 'dropcap';

    if($color){
        $attrs['style'] = 'color:'. $color;
    }
?>
<span<?php echo wyde_get_attributes( $attrs );?>><?php echo wpb_js_remove_wpautop($content, true);?></span>