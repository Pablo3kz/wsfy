<?php
    global $wyde_options, $wyde_blog_layout, $wyde_sidebar_position;
    if(!$wyde_blog_layout) $wyde_blog_layout = $wyde_options['blog_layout'];

    $has_images = has_post_thumbnail();

    $content_col = '';
    if(!is_single() && $wyde_blog_layout=='medium') $content_col = ' col-sm-6';
        
    
    $image_size = 'blog-medium';
    if(is_single() || $wyde_blog_layout == 'large'){
        if($wyde_sidebar_position != '1') 
            $image_size = 'blog-large';
        else 
            $image_size = 'full';
    }
        
    $post_class = array();
    $post_class[] = $has_images ? 'has-cover':'no-cover';
    $post_class[] = $wyde_blog_layout != 'masonry' ? 'clear' : '';

?>
<article id="post-<?php the_ID(); ?>" <?php post_class( implode(' ', $post_class) ); ?>>
    <div class="post-header<?php echo esc_attr( $content_col ); ?>">
        <?php if(!is_single() && wyde_get_option('blog_meta_date') ){ ?>
        <div class="post-date">
            <span class="date"><?php echo get_the_date(); ?></span>
        </div>
        <?php }?>
        <div class="image-wrapper">
            <?php
            if($has_images){
                wyde_post_thumbnails($image_size);
            }else if(!is_single()){
                wyde_post_title();
            }
            ?>
        </div>
    </div>
    <div class="post-detail<?php echo esc_attr( $content_col ); ?>">
        <?php
        if(is_single() || $has_images){
            wyde_post_title();
        }
        wyde_post_meta();
        ?>
        <?php if(is_single()){ ?>
        <div class="post-content">
        <?php the_content(); ?>
        <?php wp_link_pages(array( 'before' => '<div class="page-links">', 'after' => '</div>', 'link_before' => '<span>', 'link_after'  => '</span>' )); ?>
	    </div>
        <?php }else{ ?>
        <p class="post-more"><a class="ghost-button" href="<?php  echo esc_url( get_permalink() ) ;?>"><?php echo __('Read More', 'Vela'); ?></a></p>
        <?php } ?>
    </div>
</article>