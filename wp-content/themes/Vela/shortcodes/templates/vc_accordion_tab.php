<?php

    $atts = vc_map_get_attributes( $this->getShortcode(), $atts );
    extract( $atts );

    if( !empty($type) ){
        vc_icon_element_fonts_enqueue( $type );
        $icon = ${"icon_" . $type};
    } 

    $el_class = $this->getExtraClass($el_class);

    if($icon) $icon = sprintf('<i class="%s"></i>', esc_attr( $icon ) );

    $css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, 'wpb_accordion_section group '. $el_class, $this->settings['base'], $atts );

    $output .= "\n\t\t\t" . '<div class="'.esc_attr( $css_class ).'">';
        $output .= "\n\t\t\t\t" . '<h3 class="wpb_accordion_header ui-accordion-header"><a href="#'.sanitize_title($title).'">'.$icon. esc_html( $title ).'</a></h3>';
        $output .= "\n\t\t\t\t" . '<div class="wpb_accordion_content ui-accordion-content vc_clearfix">';
            $output .= ($content=='' || $content==' ') ? __("Empty section. Edit page to add content here.", "js_composer") : "\n\t\t\t\t" . wpb_js_remove_wpautop( $content );
            $output .= "\n\t\t\t\t" . '</div>';
        $output .= "\n\t\t\t" . '</div> ' . $this->endBlockComment('.wpb_accordion_section') . "\n";

    echo $output;