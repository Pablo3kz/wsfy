<?php
    $atts = vc_map_get_attributes( $this->getShortcode(), $atts );
    extract( $atts );

    $attrs = array();

    $classes = array();

    $classes[] = 'portfolio-grid grid';

    $classes[] = $view;

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
        
    global $paged, $post;
        
    if( is_front_page() || is_home() ) {
		$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : ( ( get_query_var( 'page' ) ) ? get_query_var( 'page' ) : 1 );
	} else {
		$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
	}
           
    if ( $count != '' && ! is_numeric( $count ) ) $count = 12;

    list( $query_args, $loop ) = vc_build_loop_query( $posts_query );

    $query_args['post_type'] = 'portfolio';
    $query_args['paged'] = intval( $paged );
    $query_args['has_password'] = false;
    $query_args['suppress_filters'] = false;
    $query_args['posts_per_page'] = intval( $count );

    $portfolio_posts = new WP_Query( $query_args );

    $item_index = ($paged-1) * intval( $count );
                      
    $col_class = '';
    if($view == 'masonry'){
        $masonry_layout = $this->get_masonry_layout();
        $layout_count = count($masonry_layout);
    }else{
        $col_class = 'col-sm-'.  absint( floor(12/ intval( $columns ) ) );
    }

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
            <li class="selected"><a href="#all" title=""><?php echo __('All', 'Vela');?></a></li>
            <?php   
            $terms = array();
            if( isset( $query_args['tax_query'] ) && is_array( $query_args['tax_query'] ) ){
                foreach( $query_args['tax_query'] as $tax){
                    if( isset( $tax['taxonomy'] ) &&  $tax['taxonomy'] == 'portfolio_category'){                   
                        $terms = $tax['terms'];
                    }
                }
            }

            $filters = get_terms('portfolio_category', array('include' => $terms ) );
            if ( is_array($filters) )
            {   
                foreach ( $filters as $filter ) {
                    echo sprintf('<li><a href="#%s" title="">%s</a></li>', esc_attr( urldecode($filter->slug) ), esc_html( $filter->name ));
                }
            }
            ?>
        </ul>
    </div>
    <?php endif; ?>
    <div class="item-wrapper">
        <ul class="view effect-<?php echo esc_attr( $hover_effect );?> row">
        <?php 
        
        while ($portfolio_posts->have_posts() ) : $portfolio_posts->the_post();

            $item_classes = array();   

            if( $view == 'masonry' ){
                $key = ($item_index % $layout_count);
                if( !empty($masonry_layout[$key]) ) $item_classes[] = $masonry_layout[$key];
            }else{
                $item_classes[] = $col_class;
            }
   
            $cate_names = array();   
      
            $categories = get_the_terms( get_the_ID(), 'portfolio_category' );
            
            if (is_array( $categories )) { 
                foreach ( $categories as $item ) 
                {
                    $item_classes[] = urldecode($item->slug);
                    $cate_names[] = $item->name;
                }
            }
            ?>
            <li class="item <?php echo esc_attr( implode(' ', $item_classes) ); ?>">
                <figure>
                    <?php 
                    if($view == 'masonry'){            
                        $thumb = wp_get_attachment_image_src( get_post_thumbnail_id(get_the_ID()), 'blog-full' ); 
                        if($thumb){ 
                            echo sprintf('<div class="cover-image" style="background-image:url(%s);"></div>', esc_url( $thumb[0] ));
                        }
                    }else{
                        $thumb = wp_get_attachment_image_src( get_post_thumbnail_id(get_the_ID()), 'blog-large' ); 
                        if($thumb){ 
                            echo sprintf('<img class="cover-image" src="%s" alt="%s" />', esc_url( $thumb[0] ), esc_attr( get_the_title() ));
                        }
                    }
                    ?>
                    <figcaption>
                        <h3><?php echo esc_html( get_the_title() );?></h3>
                        <p><?php echo esc_html( implode(', ', $cate_names) ) ?></p>
                        <a href="<?php echo esc_url( get_permalink() );?>"></a>
                    </figcaption>
                </figure>
            </li>
        <?php
            $item_index++;                    
        endwhile;
        wp_reset_postdata();        
        ?>      
        </ul>
    </div>
    <?php if( $show_more == 'true' ) wyde_infinitescroll($portfolio_posts->max_num_pages); ?>
</div>