<?php get_header(); ?>
<?php
global $wyde_options, $wyde_page_id, $wyde_sidebar_position;
    
if($wyde_options['onepage'] && is_front_page()){
    //if onepage site option enabled, load onepage template part
    $wyde_sidebar_position = '0';
    get_template_part('page', 'onepage');
}else{
    if(have_posts()): the_post();
    $wyde_sidebar_position = get_post_meta( $wyde_page_id, '_meta_sidebar_position', true );
    if( $wyde_sidebar_position == false ) $wyde_sidebar_position = '1';
    ?>
    <div class="container main-content <?php echo esc_attr( wyde_get_layout_name($wyde_sidebar_position) ); ?>">
        <div class="row">
            <?php if($wyde_sidebar_position == '2') wyde_sidebar(); ?>
            <div class="col-md-<?php echo $wyde_sidebar_position == '1'? '12':'8';?><?php echo $wyde_sidebar_position == '2'? ' col-md-offset-1':'';?> main">
                <div class="page-detail content">
                    <div class="page-detail-inner">
                        <?php the_content(); ?>
                        <?php wp_link_pages(array( 'before' => '<div class="page-links">', 'after' => '</div>', 'link_before' => '<span>', 'link_after'  => '</span>' )); ?>
                    </div>
                    <?php 
                    if ($wyde_options['page_comments'] && (comments_open() || get_comments_number()) && !is_woocommerce()) {
					    comments_template();
				    }
                    ?>
                </div>
            </div>
            <?php if($wyde_sidebar_position == '3') wyde_sidebar(); ?>
        </div>
    </div>
    <?php endif; ?>
<?php } ?>
<?php get_footer(); ?>