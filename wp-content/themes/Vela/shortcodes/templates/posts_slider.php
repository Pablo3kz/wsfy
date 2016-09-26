<?php

    global $wyde_blog_layout;

    $atts = vc_map_get_attributes( $this->getShortcode(), $atts );
    extract( $atts );
        

    $wyde_blog_layout = 'medium';

    $attrs = array();

    $classes = array();

    $classes[] = 'posts-slider medium';


    if( !empty($el_class) ){
        $classes[] = $el_class;
    }

	$attrs['class'] = implode(' ', $classes);

    if($animation) $attrs['data-animation'] = $animation;
    if($animation_delay) $attrs['data-animation-delay'] = floatval( $animation_delay );

    $slider_attrs = array();

    $slider_attrs['class'] = 'owl-carousel';

    $slider_attrs['data-auto-play'] = ($auto_play =='true' ? 'true':'false');
    $slider_attrs['data-navigation'] = ($show_navigation =='true' ? 'true':'false');
    $slider_attrs['data-pagination'] = ($show_pagination =='true' ? 'true':'false');

    if ( $count != '' && ! is_numeric( $count ) ) $count = - 1;

    list( $query_args, $loop ) = vc_build_loop_query( $posts_query );
                    
    $query_args['has_password'] = false;
    $query_args['ignore_sticky_posts'] = 1;
    $query_args['posts_per_page'] = intval( $count );

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
    <div class="view clear">
        <div<?php echo wyde_get_attributes( $slider_attrs );?>>
        <?php while ($blog_posts->have_posts() ) : $blog_posts->the_post(); ?>
            <div class="slide">
            <?php  get_template_part( 'content', get_post_format()); ?>
            </div>
        <?php endwhile; ?>     
        <?php wp_reset_postdata(); ?>
        </div>
    </div>
</div>