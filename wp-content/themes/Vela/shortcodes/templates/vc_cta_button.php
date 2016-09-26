<?php

$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts );

$el_class = $this->getExtraClass( $el_class );

if ( $target == 'same' || $target == '_self' ) {
	$target = '';
}
if ( $target != '' ) {
	$target = ' target="' . esc_attr( $target ) . '"';
}

$icon = ( $icon != '' && $icon != 'none' ) ? ' ' . $icon : '';
$i_icon = ( $icon != '' ) ? ' <i class="icon"> </i>' : '';

$a_class = '';
if ( $el_class != '' ) {
	$tmp_class = explode( " ", $el_class );
	if ( in_array( "prettyphoto", $tmp_class ) ) {
		$a_class .= ' prettyphoto';
		$el_class = str_ireplace( "prettyphoto", "", $el_class );
	}
}

$parent_css = '';
$child_css = '';

if($color != ''){
    $parent_css  = ' style="color:'. esc_attr( $color ).';border-color:'. esc_attr( $color ).';"';
    $child_css  = ' style="background-color:'. esc_attr( $color ).';"';
} 

if ( $href != '' ) {
	$button = '<a class="link-button ' . esc_attr( $icon ) . ' ' . esc_attr( $a_class ) . '" href="' . esc_url( $href ) . '"' . $target . $parent_css.'><span'. $child_css.'></span>' . esc_html( $title ) . $i_icon . '</a>';
} else {
	$button = '';
	$el_class .= ' cta_no_button';
}
$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, 'wpb_call_to_action clear ' . $position . $el_class, $this->settings['base'], $atts );

$data_attr = '';
if($animation) $data_attr = ' data-animation="'. esc_attr( $animation ) .'"';
if($animation_delay) $data_attr .= ' data-animation-delay="'. floatval( $animation_delay ) .'"';

$output .= '<div class="' . esc_attr( $css_class ) . '"'.$data_attr.'>';
if ( $position != 'cta_align_bottom' ) $output .= $button;
$output .= apply_filters( 'wpb_cta_text', '<h2 class="wpb_call_text">' . esc_html( $call_text ) . '</h2>', array( 'content' => $call_text ) );
//$output .= '<h2 class="wpb_call_text">'. $call_text . '</h2>';
if ( $position == 'cta_align_bottom' ) $output .= $button;
$output .= '</div> ' . $this->endBlockComment( '.wpb_call_to_action' ) . "\n";

echo $output;