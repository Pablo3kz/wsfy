<?php

    $atts = vc_map_get_attributes( $this->getShortcode(), $atts );
    extract( $atts );

    //$col_name = wpb_translateColumnWidthToSpan($width);
    $col_name = '';
    preg_match( '/(\d+)\/(\d+)/', $width, $matches );
	$w = $width;
	if ( ! empty( $matches ) ) {
		$part_x = (int) $matches[1];
		$part_y = (int) $matches[2];
		if ( $part_x > 0 && $part_y > 0 ) {
			$value = ceil( $part_x / $part_y * 12 );
			if ( $value > 0 && $value <= 12 ) {
				$w = 'vc_col-md-' . $value;
			}
		}
	}

    $col_name = vc_column_offset_class_merge($offset, $w);

    $css_class[] =  'column wpb_column';
    if(!empty($el_class)) $css_class[] = $el_class;
    if(!empty($alt_color)) $css_class[] = 'alt-color';    
    if(!empty($alt_bg_color)) $css_class[] = 'alt-background-color';    
    if(!empty($padding_size)) $css_class[] = $padding_size;    
    if(!empty($text_align)) $css_class[] = 'text-'. $text_align;    


    $style = '';
    if ( ! empty( $font_color ) ) {
	    $style .= vc_get_css_color( 'color', $font_color );
    }

    $item_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $col_name . ' '. implode(' ', $css_class) . vc_shortcode_custom_css_class( $css, ' ' ), $this->settings['base'], $atts );

    $output .= "\n\t".'<div class="'.esc_attr( $item_class ).'"'. (empty( $style ) ? $style : ' style="' . esc_attr( $style ) . '"') .'>';
    $output .= "\n\t\t".'<div class="wpb_wrapper">';
    $output .= "\n\t\t\t".wpb_js_remove_wpautop($content);
    $output .= "\n\t\t".'</div> '.$this->endBlockComment('.wpb_wrapper');
    $output .= "\n\t".'</div> '.$this->endBlockComment($el_class) . "\n";

    echo $output;