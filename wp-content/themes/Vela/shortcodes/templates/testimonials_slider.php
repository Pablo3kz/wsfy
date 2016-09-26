<?php
    
    $atts = vc_map_get_attributes( $this->getShortcode(), $atts );
    extract( $atts );

    $attrs = array();

    $classes = array();

    $classes[] = 'testimonials-slider';

    if($show_navigation == 'true') $classes[] = 'show-navigation';    

    if( !empty($el_class) ){
        $classes[] = $el_class;
    }

	$attrs['class'] = implode(' ', $classes);

    if($animation) $attrs['data-animation'] = $animation;
    if($animation_delay) $attrs['data-animation-delay'] = floatval( $animation_delay );

    $slider_attrs = array();

    $slider_attrs['class'] = 'flexslider';
    $slider_attrs['data-auto-play'] = ($auto_play =='true' ? 'true':'false');
    $slider_attrs['data-auto-height'] = 'false';
    $slider_attrs['data-navigation'] = ($show_navigation =='true' ? 'true':'false');
    $slider_attrs['data-pagination'] = ($show_pagination =='true' ? 'true':'false');
    $slider_attrs['data-effect'] = "fade";

    if ( $count != '' && ! is_numeric( $count ) ) $count = - 1;

    list( $query_args, $loop ) = vc_build_loop_query( $posts_query );
        
    $query_args['post_type'] = 'testimonial';
    $query_args['posts_per_page'] = intval( $count );       
    $query_args['has_password'] = false;       


    $blog_posts = new WP_Query( $query_args );

?>
<div<?php echo wyde_get_attributes( $attrs );?>>
    <?php if( $title || $content): ?>      
    <div class="content-header">
        <?php if( !empty($title) ): ?>
        <h2><?php echo esc_html( $title );?></h2>
        <?php endif;?>
        <?php if( !empty($content) ): ?>
        <div class="post-desc"><?php echo wpb_js_remove_wpautop($content, true);?></div>
        <?php endif;?>
    </div>
    <?php endif; ?>
    <div<?php echo wyde_get_attributes( $slider_attrs ); ?>>
        <ul class="slides">
            <?php while ($blog_posts->have_posts() ) : $blog_posts->the_post(); ?>
            <li>
                <div class="testimonial">
                    <div class="testimonial-content">
                        <i class="post-format-icon fa fa-quote-right"></i>
                        <div><?php echo wp_kses_post( get_the_content() ); ?></div>
                    </div>
                    <div class="testimonial-meta">
                        <div class="testimonial-name">
                            <h4><?php echo esc_html( get_the_title() ); ?></h4>
                            <p>
                            <?php
                            $position =  get_post_meta( get_the_ID(), '_meta_testimonial_position', true );
                            $company =  get_post_meta( get_the_ID(), '_meta_testimonial_company', true );
                            $website =  get_post_meta( get_the_ID(), '_meta_testimonial_website', true );

                            if($position){
                               echo '<span>'.esc_html( $position ).'</span>';
                            } 
    
                            if($company){
                                echo ' — ';
                                if($website){
                                    echo '<a href="'.esc_url( $website ).'" target="_blank">';
                                } 
                                echo esc_html( $company );
                                if($website){
                                    echo '</a>';
                                } 
                            }
                            ?>
                            </p>
                        </div>
                        <?php
                        $image = get_post_meta( get_the_ID(), '_meta_testimonial_image', true);
                        if($image){
                            $image_id = get_post_meta( get_the_ID(), '_meta_testimonial_image_id', true);
                            $image_attr = wp_get_attachment_image_src($image_id, array(150, 150));
                            if($image_attr[0]) $image = $image_attr[0];
                        } 
                        if($image){
                            echo sprintf('<div class="image-border"><img src="%s" alt="%s" /></div>', esc_url( $image ), esc_attr( get_the_title() ));
                        }
                        ?>
                    </div>
                </div>
            </li>
            <?php endwhile; ?>       
            <?php wp_reset_postdata(); ?>
        </ul>
    </div>
</div>
       



