<?php
    global $wyde_options, $wyde_blog_layout, $wyde_sidebar_position;
    if(!$wyde_blog_layout) $wyde_blog_layout = $wyde_options['blog_layout'];

    $has_images = has_post_thumbnail();

    $images = get_post_meta($post->ID, '_meta_gallery_images', true);



    $content_col='';
    if(!is_single() && $wyde_blog_layout=='medium') $content_col = ' col-sm-6';

    $image_size = 'blog-medium';
    if(is_single() || $wyde_blog_layout == 'large'){
        if($wyde_sidebar_position != '1') 
            $image_size = 'blog-large';
        else 
            $image_size = 'full';
    }
        
    $post_class = array();
    $post_class[] = ($has_images || is_array($images)) ? 'has-cover':'no-cover';
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
            <?php if($has_images || is_array($images)){ ?>
			<div class="flexslider" data-auto-height="<?php echo is_single()?1:0;?>">
				<ul class="slides">
                    <?php if($has_images){ ?>
                    <?php   $thumb_id = get_post_thumbnail_id($post->ID); ?>
                    <?php   $thumb = wp_get_attachment_image_src($thumb_id, $image_size ); ?>
                    <?php   if($thumb[0]){ ?>
	                <li>
                        <?php if(!is_single()){ ?> 
                        <a href="<?php echo esc_url(get_permalink()); ?>">
                            <img src="<?php echo esc_url( $thumb[0] ); ?>" alt="<?php echo esc_attr( get_the_title() );?>" />
                        </a>
                        <?php }else{ ?> 
                        <?php 
                        if($image_size!='full') $image_full =  wp_get_attachment_image_src($id, 'full' );
                        else $image_full = $thumb;
                        ?>
                        <a href="<?php echo esc_url( $image_full[0] );?>" rel="prettyPhoto[blog]">
                            <img src="<?php echo esc_url( $thumb[0] ); ?>" alt="<?php echo esc_attr( get_the_title() );?>" />
                        </a>
                        <?php } ?> 
	                </li>
                    <?php   } ?>
	                <?php } ?>
                    <?php if(is_array($images)){   ?>
                    <?php   foreach($images as $id){ ?>
                    <?php       if($id != $thumb_id){ ?>
                    <?php       $image_source = wp_get_attachment_image_src($id, $image_size); ?>
					<li>
                        <?php if(!is_single()){ ?> 
                        <a href="<?php echo esc_url(get_permalink()); ?>">
                            <img src="<?php echo esc_url( $image_source[0] ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" />
                        </a>
                        <?php }else{ ?> 
                        <?php 
                        if($image_size!='full') $image_full =  wp_get_attachment_image_src($id, 'full' );
                        else $image_full = $image_source;
                        ?>
                        <a href="<?php echo esc_url( $image_full[0] );?>" rel="prettyPhoto[blog]">
                            <img src="<?php echo esc_url( $image_source[0] ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" />
                        </a>
                        <?php } ?> 
					</li>
                    <?php   } ?>
                    <?php   } ?>
                    <?php } ?>
				</ul>
			</div>
            <?php 
            }else if(!is_single()){
                wyde_post_title();
            }   
            ?>
        </div>
    </div>
    <div class="post-detail<?php echo esc_attr( $content_col ); ?>">
        <?php
        if(is_single() || $has_images || is_array($images)){
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
        <div class="post-summary">
        <?php the_excerpt(); ?>
        </div>
        <p class="post-more"><a class="ghost-button" href="<?php  echo esc_url( get_permalink() ) ;?>"><?php echo __('Read More', 'Vela'); ?></a></p>
        <?php } ?>
    </div>
</article>