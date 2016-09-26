<?php
/* @param string $meta Meta name.
 * @param array $details Contains the details for the field.
 * @param string $value Contains input value;
 * @param string $context Context where the function is used. Depending on it some actions are preformed.;
 * @return string $element input element html string. */


$element .= '<input type="number" name="'. $single_prefix . esc_attr( Wordpress_Creation_Kit::wck_generate_slug( $details['title'], $details ) ) .'" id="';
if( !empty( $frontend_prefix ) )
	$element .=	$frontend_prefix;
$element .=	esc_attr( Wordpress_Creation_Kit::wck_generate_slug( $details['title'], $details ) ) .'"';
if( !empty( $details['readonly'] ) && $details['readonly'] )
	$element .=	'readonly="readonly"';
$element .=	' step="'. ( ! empty( $details['number-step-value'] ) ? $details['number-step-value'] : 'any' ) .'" type="number" min="'. ( ! empty( $details['min-number-value'] ) ? $details['min-number-value'] : ( $details['min-number-value'] == '0' ? '0' : '' ) ) .'" max="'. ( ! empty( $details['max-number-value'] ) ? $details['max-number-value'] : ( $details['max-number-value'] == '0' ? '0' : '' ) ) .'"';
$element .=	' value="'. esc_attr( $value ) .'" class="mb-number-input mb-field"/>';
?>