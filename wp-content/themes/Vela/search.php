<?php get_header(); ?>
<?php
global $wyde_options, $wyde_page_id, $wyde_sidebar_position;   
 
$wyde_sidebar_position = $wyde_options['search_sidebar'];

if($wyde_options['search_items']) {
    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

	global $query_string;
	query_posts( $query_string.'&posts_per_page='. intval( $wyde_options['search_items'] ) .'&paged='. intval($paged) );
}
?>
<div class="container main-content <?php echo esc_attr( wyde_get_layout_name($wyde_sidebar_position) ); ?>">
    <div class="row">
        <?php if($wyde_sidebar_position=='2') wyde_sidebar(); ?>
        <div class="col-md-<?php echo $wyde_sidebar_position == '1' ? '12':'8';?><?php echo $wyde_sidebar_position == '2'? ' col-md-offset-1':'';?> main">
            <div class="content">
                <div class="full-search">
                <?php get_search_form(); ?>
                </div>
                <?php
                if(strlen( trim(get_search_query()) ) != 0 ):
                ?>
                <p class="search-query">
				<?php echo __('Search Results for', 'Vela')?>: <?php echo get_search_query(); ?>    
                </p>
                <?php
                if ( have_posts() ) {
                ?>
                <div class="view large clear">
                <?php
                    while(have_posts()): the_post();
                    ?>
                    <div id="post-<?php the_ID(); ?>" class="search-item clear">
                        <div class="item-header clear">
                            <?php if($wyde_options['search_show_image'] && (has_post_thumbnail() || get_post_type() == 'post')) {?>
                            <span class="thumb">
                                <a href="<?php echo esc_url( get_permalink() );?>" target="_blank">
                                <?php echo wyde_get_post_thumbnail(get_the_ID(), 'thumbnail');?>
                                </a>
                            </span>
                            <?php }else{ ?>
                            <span class="type-icon">
                                <a href="<?php echo esc_url( get_permalink() );?>" target="_blank">
                                <?php echo wyde_get_type_icon();?>
                                 </a>
                            </span>
                            <?php } ?>
                            <div>
                                <h5>
                                    <a href="<?php echo esc_url( get_permalink() );?>"><?php the_title();?></a>
                                </h5>
                                <?php wyde_search_meta();?>                                     
                            </div>
                        </div>                                            
                        <?php if(get_post_type()!='page'): ?>
                        <div class="post-content">
                        <?php the_excerpt(); ?>
                        </div>
                        <?php endif; ?>  
                    </div>       
                    <?php endwhile; ?>
                </div>
                <?php 
                wyde_pagination();
                }else{
                ?>
                <p>
                <?php echo __('No result found.', 'Vela');?>
                </p>
                <?php }
                endif;
                ?>
            </div>
        </div>
        <?php if($wyde_sidebar_position=='3') wyde_sidebar(); ?>
    </div>
</div>
<?php get_footer(); ?>