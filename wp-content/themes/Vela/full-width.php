<?php
// Template Name: Full Width
get_header();?>
<?php
global $wyde_sidebar_position;
//no page sidebar
$wyde_sidebar_position = '0';

if($wyde_options['onepage'] && is_front_page()){
    //if onepage site option enabled, load onepage template part
    get_template_part('page', 'onepage');
}else{
    if(have_posts()): the_post();
?>
<div class="main-content full-width">
    <?php
    if(!post_password_required()){
    the_content();
    }else{ ?>
    <div class="section">
        <div class="container">
        <?php  the_content(); ?>
        </div>
    </div>
    <?php } ?>
</div>
<?php endif; ?>
<?php } ?>
<?php get_footer(); ?>