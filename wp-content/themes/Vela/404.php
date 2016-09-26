<?php get_header(); ?>
<div class="container main-content no-sidebar">
    <div class="row">
        <div class="col-sm-12">
            <div class="content">
                <p><?php echo wp_kses_post( __( 'It looks like nothing was found at this location. Maybe try a search?', 'Vela' ) ); ?></p>
				<?php get_search_form(); ?>
            </div>
        </div>
    </div>
</div>
<?php get_footer(); ?>