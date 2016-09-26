<?php

    $atts = vc_map_get_attributes( $this->getShortcode(), $atts );
    extract( $atts );

    $el_class = $this->getExtraClass($el_class);

    $css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, 'wpb_text_column wpb_content_element ' . $el_class . vc_shortcode_custom_css_class( $css, ' ' ), $this->settings['base'], $atts );


    $data_attr = '';
    if($animation) $data_attr = ' data-animation="'. esc_attr( $animation ) .'"';
    if($animation_delay) $data_attr .= ' data-animation-delay="'. floatval( $animation_delay ) .'"';

    $output .= "\n\t".'<div class="'.esc_attr( $css_class ).'"'.$data_attr.'>';
    $output .= "\n\t\t".'<div class="wpb_wrapper">';
    $output .= "\n\t\t\t".wpb_js_remove_wpautop($content, true);
    $output .= "\n\t\t".'</div> ' . $this->endBlockComment('.wpb_wrapper');
    $output .= "\n\t".'</div> ' . $this->endBlockComment('.wpb_text_column');

    echo $output;