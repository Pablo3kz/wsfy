<?php 
    global $wyde_options;
?>
<span id="logo">
    <?php
    $home_url = home_url();
    $home_url = apply_filters('vela_home_url', $home_url);

    ?>
    <a href="<?php echo esc_url( $home_url ); ?>">
        <?php
        /* Dark Logo */
        $logo = wyde_get_option('logo_image');
        if( is_array($logo) && !empty($logo['url']) ):
            $logo_attrs = array();
            $logo_attrs['class'] = 'dark-logo';                
            if( !empty($logo['url']) ) $logo_attrs['src'] = $logo['url'];
            if( is_array( wyde_get_option('logo_image_retina') ) && isset( wyde_get_option('logo_image_retina')['url'] ) ) $logo_attrs['data-retina'] = wyde_get_option('logo_image_retina')['url'];
            if( !empty($logo['width']) ) $logo_attrs['width'] = $logo['width'];
            if( !empty($logo['height']) ) $logo_attrs['height'] = $logo['height'];
            $logo_attrs['alt'] = get_bloginfo('name');                      
        ?>
        <img<?php echo wyde_get_attributes( $logo_attrs );?> />
        <?php endif; ?>
        <?php 
        /* Light Logo for Dark header */
        $light_logo = wyde_get_option('light_logo_image');
        if( is_array($light_logo) && !empty($light_logo['url']) ):             
            $logo_attrs = array();
            $logo_attrs['class'] = 'light-logo';                
            if( !empty($light_logo['url']) ) $logo_attrs['src'] = $light_logo['url'];
            if( is_array( wyde_get_option('light_logo_image_retina') ) && isset( wyde_get_option('light_logo_image_retina')['url'] ) ) $logo_attrs['data-retina'] = wyde_get_option('light_logo_image_retina')['url'];
            if( !empty($light_logo['width']) ) $logo_attrs['width'] = $light_logo['width'];
            if( !empty($light_logo['height']) ) $logo_attrs['height'] = $light_logo['height'];
            $logo_attrs['alt'] = get_bloginfo('name');
        ?>
        <img<?php echo wyde_get_attributes( $logo_attrs );?> />
        <?php endif; ?>
        <?php 
        /* Dark Sticky Logo */
        $sticky =  wyde_get_option('logo_image_sticky') ? wyde_get_option('logo_image_sticky') : $logo;
        if( is_array($sticky) && !empty($sticky['url']) ): 
            $logo_attrs = array();
            $logo_attrs['class'] = 'dark-sticky';                
            if( !empty($sticky['url']) ) $logo_attrs['src'] = $sticky['url'];
            if( is_array( wyde_get_option('logo_image_sticky_retina') ) && isset( wyde_get_option('logo_image_sticky_retina')['url'] ) ) $logo_attrs['data-retina'] = wyde_get_option('logo_image_sticky_retina')['url'];
            if( !empty($sticky['width']) ) $logo_attrs['width'] = $sticky['width'];
            if( !empty($sticky['height']) ) $logo_attrs['height'] = $sticky['height'];
            $logo_attrs['alt'] = get_bloginfo('name');
        ?>
        <img<?php echo wyde_get_attributes( $logo_attrs );?> />
        <?php endif; ?>
    </a>
</span>