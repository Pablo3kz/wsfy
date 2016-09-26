<?php

    global $wyde_options, $wyde_header_overlay, $wyde_page_id;
    
    $header_classes = array();

    $header_classes[] = 'header-v'.$wyde_options['header_layout'];

    $menu_style = get_post_meta( $wyde_page_id, '_meta_menu_style', true );

    if($menu_style == ''){
        $menu_style = $wyde_options['menu_style'];
    }

    if( !empty($menu_style) ) {
       $header_classes[] = $menu_style;
    }

    if($wyde_options["header_sticky"]){
        $header_classes[] = 'sticky';
    } 

    if( $wyde_header_overlay ){
        $header_classes[] = 'transparent';
    }

    if($wyde_options["header_fluid"] || $wyde_options["header_layout"]=='5'){
        $header_classes[] = 'full';
    }

    if( intval($wyde_options["header_layout"]) >= 3 && intval($wyde_options["header_layout"]) <= 5){
        $header_classes[] = 'logo-top';
    }else if( intval($wyde_options["header_layout"]) >= 6 && intval($wyde_options["header_layout"]) <= 7){
        $header_classes[] = 'logo-center';
    }

    if(wp_is_mobile()){
        $header_classes[] = 'mobile';
    } 

?>
<header id="header" class="<?php echo esc_attr(  implode(' ', $header_classes) ); ?>">
    <div class="header-wrapper">
        <?php
        get_template_part( 'inc/headers/header', 'v'. intval( $wyde_options['header_layout'] ) );
        ?> 
    </div>
</header>