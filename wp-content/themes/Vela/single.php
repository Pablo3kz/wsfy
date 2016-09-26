<?php get_header(); ?>
<?php
if(have_posts()): the_post();
global $wyde_options, $wyde_sidebar_position, $wyde_title_area;

if(get_post_meta( $post->ID, '_meta_post_custom_sidebar', true )=='on'){
    $wyde_sidebar_position = get_post_meta( $post->ID, '_meta_post_sidebar_position', true );
}

if(!$wyde_sidebar_position) $wyde_sidebar_position = $wyde_options['blog_single_sidebar'];
?>
<div class="container main-content <?php echo esc_attr( wyde_get_layout_name($wyde_sidebar_position) ); ?><?php echo $wyde_title_area == 'hide'?' big-padding-top':''?>">    
    <div class="row">  
        <?php if($wyde_sidebar_position=='2') wyde_sidebar(); ?>
        <div class="col-md-<?php echo $wyde_sidebar_position == '1'? '12':'8';?><?php echo $wyde_sidebar_position == '2'? ' col-md-offset-1':'';?> main">
            <div class="blog-detail content">
                <?php
                if(post_password_required($post->ID)){
                    the_content();
                }else{  
                ?>
                <div class="blog-detail-inner">
                <?php
                    get_template_part('content', get_post_format());                                        
                    if($wyde_options['blog_single_tags']) the_tags('<div class="post-tags"><i class="fa fa-tags"></i> <span class="tag-links">', ', ', '</span></div>' );
                    if($wyde_options['blog_single_author']) wyde_post_author();
                    if($wyde_options['blog_single_nav']) wyde_post_nav();
                ?>
                </div>
                <?php
                    if($wyde_options['blog_single_related']) wyde_related_posts();
				    if ($wyde_options['blog_single_comment'] && (comments_open() || get_comments_number()) ) {
					    comments_template();
				    }
                }
                ?>
            </div>
        </div>
        <?php if($wyde_sidebar_position=='3') wyde_sidebar(); ?>
    </div>
</div>
<?php endif; ?>
<?php get_footer(); ?>