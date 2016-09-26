<?php

    global $wyde_blog_layout, $paged;

    $atts = vc_map_get_attributes( $this->getShortcode(), $atts );
    extract( $atts );

    $wyde_blog_layout = 'masonry';

    $attrs = array();

    $classes = array();

    $classes[] = 'posts-grid grid';

    $classes[] = 'grid-'. intval( $columns ) .'-cols';

    $classes[] = 'scrollmore';

    if($hide_filter != 'true'){
        $classes[] = 'filterable';
    }        

    if( !empty($el_class) ){
        $classes[] = $el_class;
    }
        
	$attrs['class'] = implode(' ', $classes);

    if($show_more == 'true'){
        $attrs['data-trigger'] = "false";
    }

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
    $query_args['has_password'] = false;
    $query_args['ignore_sticky_posts'] = 1;
    $query_args['posts_per_page'] = intval( $count );       
            
    $blog_posts = new WP_Query( $query_args );

    $col_class = 'col-md-'.  absint( floor(12/ intval( $columns ) ) );
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
    <?php if( $hide_filter != 'true' ): ?>
    <div class="post-filter clear">
        <ul class="filter">
            <li class="selected"><a href="#all" title="">All</a></li>
            <?php   
            $terms = get_terms("category");
            if (is_array($terms))
            {   
                foreach ( $terms as $term ) {
                    $term_link = urldecode($term->slug);
                    echo sprintf('<li><a href="#%s" title="">%s</a></li>', esc_attr( $term_link ), esc_html( $term->name ));
                }
            }
            ?>
        </ul>
    </div>
    <?php endif; ?>
    <div class="item-wrapper">
        <ul class="view row">
        <?php 
        while ($blog_posts->have_posts() ) : $blog_posts->the_post(); 

            $item_classes = array();     
            $item_classes[] = $col_class;

            $categories = get_the_category();
            if ( is_array( $categories ) ) { 
                foreach ( $categories as $item ) 
                {
                    $item_classes[] = urldecode($item->slug);
                }
            }
        ?>
            <li class="item <?php echo esc_attr( implode(' ', $item_classes) ); ?>">
            <?php get_template_part( 'content', get_post_format()); ?>    
            </li>
        <?php endwhile; ?>
        <?php wp_reset_postdata(); ?>
        </ul>
    </div>
    <?php if( $show_more == 'true' ) wyde_infinitescroll($blog_posts->max_num_pages); ?>
</div>