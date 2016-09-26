<?php get_header(); ?>
<?php
if(have_posts()):

global $wyde_options, $wyde_page_id, $wyde_sidebar_position;

$wyde_blog_layout = $wyde_options['blog_layout'];

$grid_columns = $wyde_options['blog_grid_columns'];
if(!$grid_columns){
    $grid_columns = 3;
}

$wyde_sidebar_position = $wyde_options['blog_sidebar'];

$author_id    = get_the_author_meta('ID');
$name         = get_the_author_meta('display_name', $author_id);
$avatar       = get_avatar( get_the_author_meta('email', $author_id), '82' );
$description  = get_the_author_meta('description', $author_id);

if(empty($description)) {
		$description = '<p>'.sprintf( __( '%s has created %s entries.', 'Vela' ), $name, count_user_posts( $author_id ) ).'</p>';
	}
?>
<div class="container main-content <?php echo esc_attr(  wyde_get_layout_name($wyde_sidebar_position) ); ?>">
    <div class="row">
        <?php
        if($wyde_sidebar_position=='2') wyde_sidebar();
        ?>
        <div class="col-md-<?php echo $wyde_sidebar_position=='1'? '12':'8';?><?php echo $wyde_sidebar_position=='2'? ' col-md-offset-1':'';?> main">
            <div class="content">                            
                <div class="about-author clear">
                    <?php echo sprintf('<a href="%s">%s</a>', get_author_posts_url($author_id), $avatar); ?>
                    <div class="author-detail">
                        <h4><?php echo __('About', 'flora'); ?> <?php echo esc_html($name); ?></h4>
                        <?php if( current_user_can('edit_users') || get_current_user_id() == $author_id ): ?>
                        <span class="edit-profile"><a href="<?php echo admin_url( 'profile.php?user_id=' . $author_id ); ?>" target="_blank"><?php echo __( 'Edit Profile', 'flora' ); ?></a></span>
                        <?php endif; ?>
                        <div class="author-description">
                        <?php echo wp_kses_post( $description ); ?>
                        </div>
                    </div>
                </div>
                <h5 class="group-name"><?php echo __('Entries By', 'Vela'). ' ' . esc_html( $name );?></h5>
                <div class="blog-posts<?php echo $wyde_blog_layout=='masonry'? ' grid':'';?><?php echo $wyde_options['blog_pagination']=='2'? ' scrollmore':'';?>">
                    <div class="item-wrapper">
                        <ul class="view <?php echo esc_attr( $wyde_blog_layout ); ?> row">
                            <?php while (have_posts()) : the_post(); ?>
                            <?php 
                            $post_class = '';
                            if($wyde_blog_layout == 'masonry'){
                                if( in_array('sticky', get_post_class()) ){
                                    $post_class .= 'col-sm-12';
                                }else{
                                    $post_class .= 'col-sm-'. absint( floor( 12 / intval($grid_columns) ) );
                                }
                            }else{
                                $post_class = 'clear';
                            }
                            ?>
                            <li class="item <?php echo esc_attr( $post_class );?>">
                            <?php get_template_part( 'content', get_post_format()); ?>
                            </li>
                            <?php endwhile; ?>
                        </ul>
                        <?php wyde_pagination(); ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
        if($wyde_sidebar_position=='3') wyde_sidebar();
        ?>
    </div>
</div>
<?php endif; ?>
<?php get_footer(); ?>