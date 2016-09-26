<?php

    global $wyde_blog_layout, $paged;

    $atts = vc_map_get_attributes( $this->getShortcode(), $atts );
    extract( $atts );
        
    $wyde_blog_layout = $view;

    $attrs = array();

    $classes = array();

    $classes[] = 'blog-posts';

    if($view == 'masonry'){
        $classes[] = 'grid';
    }

    $classes[] = $view;

    if($pagination == '2'){
        $classes[] = 'scrollmore';
    }

    if( !empty($el_class) ){
        $classes[] = $el_class;
    }
        
    $attrs['class'] = implode(' ', $classes);

    if($animation) $attrs['data-animation'] = $animation;
    if($animation_delay) $attrs['data-animation-delay'] = floatval( $animation_delay );

    if( is_front_page() || is_home() ) {
		$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : ( ( get_query_var( 'page' ) ) ? get_query_var( 'page' ) : 1 );
	} else {
		$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
	}

    if ( $count != '' && ! is_numeric( $count ) ) $count = - 1;
    
    list( $query_args, $loop ) = vc_build_loop_query( $posts_query );

    $query_args['paged'] = intval( $paged );
    $query_args['posts_per_page'] = intval( $count );

    $blog_posts = new WP_Query( $query_args );
       
    $item_html = array();         
          
    $class_name = '';

    if($view == 'masonry'){
        $class_name = 'col-sm-'.  absint( floor(12/ intval( $columns ) ) );
    } else {
        $class_name = 'clear';
    }

?>      
<div<?php echo wyde_get_attributes($attrs);?>>
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
    <div class="item-wrapper">
        <ul class="view row">
        <?php while ($blog_posts->have_posts() ) : $blog_posts->the_post(); ?>
            <li class="item <?php echo esc_attr( $class_name );?>">
            <?php get_template_part( 'content', get_post_format()); ?>    
            </li>
        <?php endwhile; ?>
        <?php wp_reset_postdata(); ?>
        </ul>
    </div>
    <?php wyde_pagination($pagination, $blog_posts->max_num_pages); ?>
</div>