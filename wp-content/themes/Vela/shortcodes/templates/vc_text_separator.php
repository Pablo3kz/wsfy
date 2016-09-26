<?php
extract(shortcode_atts(array(
    'title' => '',
    'title_align' => '',
    'el_width' => '',
    'style' => '',
    'color' => '',
    'el_class' => ''
), $atts));

$class = "vc_separator wpb_content_element";

$class .= ($title_align!='') ? ' vc_'.$title_align : '';
$class .= ($style!='') ? ' vc_sep_'.$style : '';

$border_color = ' style="'.esc_attr( vc_get_css_color('border-color', $color) ).'"';

$inline_css = ' style="width:'.esc_attr( $el_width ).';"';

$class .= $this->getExtraClass($el_class);
$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class, $this->settings['base'], $atts );

?>
<div class="<?php echo esc_attr( $css_class ); ?>"<?php echo $inline_css ;?>>
	<span class="vc_sep_holder vc_sep_holder_l"><span<?php echo $border_color; ?> class="vc_sep_line"></span></span>
	<?php if($title!=''): ?><h4><?php echo esc_html( $title ); ?></h4><?php endif; ?>
	<span class="vc_sep_holder vc_sep_holder_r"><span<?php echo $border_color; ?> class="vc_sep_line"></span></span>
</div>
<?php echo $this->endBlockComment('separator')."\n";