<?php
    
    $atts = vc_map_get_attributes( $this->getShortcode(), $atts );
    extract( $atts );

    $attrs = array();

    $classes = array();

    $classes[] = 'team-slider';

    if( !empty($el_class) ){
        $classes[] = $el_class;
    }

    $attrs['class'] = implode(' ', $classes);

    if($animation) $attrs['data-animation'] = $animation;
    if($animation_delay) $attrs['data-animation-delay'] = floatval( $animation_delay );

    $slider_attrs = array();

    $slider_attrs['class'] = 'owl-carousel';

    $slider_attrs['data-items'] = intval( $visible_items );
    $slider_attrs['data-auto-play'] = ($auto_play =='true' ? 'true':'false');
    $slider_attrs['data-navigation'] = ($show_navigation =='true' ? 'true':'false');
    $slider_attrs['data-pagination'] = ($show_pagination =='true' ? 'true':'false');

    if ( $count != '' && ! is_numeric( $count ) ) $count = - 1;

    list( $query_args, $loop ) = vc_build_loop_query( $posts_query );

    $query_args['post_type'] = 'team-member';
    $query_args['posts_per_page'] = intval( $count );       
    $query_args['has_password'] = false;       


    $blog_posts = new WP_Query( $query_args );

    $image_size = 'medium';
    if( intval( $visible_items ) < 3) $image_size = 'large';

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
    <div<?php echo wyde_get_attributes( $slider_attrs );?>>
        <?php while ($blog_posts->have_posts() ) : $blog_posts->the_post(); ?>
        <div class="team-member">
            <div class="member-image">
                <span>
                <?php    
                $image = get_post_meta( get_the_ID(), '_meta_member_image', true);
                if($image){
                    $image_id = get_post_meta( get_the_ID(), '_meta_member_image_id', true);
                    $image_attr = wp_get_attachment_image_src($image_id, $image_size);
                    if($image_attr[0]) $image = $image_attr[0];
                } 
                if($image){
                    echo sprintf('<img src="%s" alt="%s" />', esc_url( $image ), esc_attr( get_the_title() ));
                }
                ?>
                </span>
                <p class="social-link">
                <?php    
                $email =  get_post_meta( get_the_ID(), '_meta_member_email', true );
                $website =  get_post_meta( get_the_ID(), '_meta_member_website', true );

                if($email){
                    echo '<a href="mailto:'.sanitize_email( $email ).'" title="'.__('Email', 'Vela').'" target="_blank" class="tooltip-item"><i class="fa fa-envelope"></i></a>';
                }
                if($website){
                    echo '<a href="'.esc_url( $website ).'" title="'.__('Website', 'Vela').'" target="_blank" class="tooltip-item"><i class="fa fa-globe"></i></a>';
                }

                $socials_icons = wyde_get_social_icons();
                $socials = get_post_meta( get_the_ID(), '_meta_member_socials', true );

                foreach ( (array) $socials as $key => $entry ) {
                    if ( isset( $entry['url'] ) && !empty( $entry['url'] ) ){
                        echo sprintf('<a href="%s" title="%s" target="_blank" class="tooltip-item"><i class="%s"></i></a>', esc_url( $entry['url'] ), esc_attr( $entry['social'] ), esc_attr( array_search($entry['social'], $socials_icons) ));
                    }
                }
                
                ?>
                </p>
            </div>
            <div class="member-detail">
                <h4><?php echo esc_html( get_the_title() ); ?></h4>
                <p class="member-meta"><?php echo esc_html( get_post_meta( get_the_ID(), '_meta_member_position', true ) ); ?></p>
                <div class="member-content">
                    <?php echo wyde_get_the_excerpt(130, '<a href="#">'. __('More', 'Vela') .'</a>'); ?>
                </div>
                <div class="member-full-content" style="display: none;">
                    <?php echo get_the_content();?>
                </div>
            </div>
        </div>
        <?php endwhile;?>
        <?php wp_reset_postdata(); ?>
    </div>
</div>