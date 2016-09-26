<?php get_header(); ?>
<?php
if(have_posts()): the_post();
global $wyde_options, $wyde_sidebar_position, $wyde_title_area;

if(get_post_meta( $post->ID, '_meta_post_custom_sidebar', true )=='on'){
    $wyde_sidebar_position = get_post_meta( $post->ID, '_meta_post_sidebar_position', true );
}

if($wyde_sidebar_position == false) $wyde_sidebar_position = $wyde_options['portfolio_sidebar'];

?>
<div class="container main-content <?php echo esc_attr( wyde_get_layout_name($wyde_sidebar_position) ); ?><?php echo $wyde_title_area == 'hide'?' big-padding-top':''?>">
    <div class="portfolio-detail content">
        <?php
        if(post_password_required($post->ID)){
            the_content();
        }else{  
            $portfolio_layout = get_post_meta( $post->ID, '_meta_portfolio_layout', true );
            if(!$portfolio_layout) $portfolio_layout = $wyde_options['portfolio_layout'];
            $layout_class = '';
            $item_col = '';
            switch($portfolio_layout){
                case '2':
                    $layout_class = 'gallery';
                    $image_size = 'large';
                    $item_col = 'col-sm-4';
                    break;
                case '3':
                    $layout_class = 'large';
                    $image_size = 'full';
                    $item_col = 'col-sm-12';
                    break;
                default:
                    $layout_class = 'flexslider';
                    $image_size = 'full';
                    break;
            }

            $lightbox_size = isset($wyde_options['portfolio_lightbox_size']) ? $wyde_options['portfolio_lightbox_size'] : 'full';

            $has_images = has_post_thumbnail();

            $images = get_post_meta($post->ID, '_meta_gallery_images', true);
                
            $embed_url = esc_url( get_post_meta($post->ID, '_meta_embed_url', true ) );
    
            $embed_code = '';
            if( !empty( $embed_url )){
        
                $embed_code = wp_oembed_get($embed_url, array(
                        'width'     => '1020',
                        'height'    => '575'
                ));

            }

            if($has_images || $images || !empty( $embed_code ) ){
            ob_start();
        ?>
        <div class="media-wrapper">
            <div class="<?php echo $layout_class; ?>" <?php if($portfolio_layout == '1'){ ?> data-autoheight="1" data-effect="fade" <?php } ?>>
                <ul class="slides<?php echo ($item_col != ''? ' row':'');?>">
                    <?php if(!empty( $embed_code )){ ?>
                    <li class="featured-video <?php echo esc_attr( $item_col );?>">
                        <div class="video-wrapper">
                        <?php echo $embed_code;?>
                        </div>
	                </li>
	                <?php } ?>
                    <?php if($has_images): ?>
                    <?php   $thumb_id = get_post_thumbnail_id($post->ID); ?>
                    <?php   $thumb = wp_get_attachment_image_src($thumb_id, $image_size ); ?>
	                <li class="featured-image <?php echo esc_attr( $item_col );?>">
                        <?php  
                            $full =  wp_get_attachment_image_src($thumb_id, $lightbox_size); 
                        ?>
                        <a href="<?php echo esc_url( $full[0] );?>" rel="prettyPhoto[portfolio]">
                            <img src="<?php echo esc_url( $thumb[0] ); ?>" alt="<?php echo esc_attr( get_the_title() );?>" />
                        </a>
	                </li>
	                <?php endif; ?>
                    <?php if(is_array($images)): ?>
                    <?php   foreach($images as $image_id): ?>
                    <?php       $image_thumb = wp_get_attachment_image_src($image_id, $image_size); ?>
	                <li class="<?php echo $item_col;?>">
                        <?php   
                                $image_full =  wp_get_attachment_image_src($image_id, $lightbox_size );
                        ?>
                        <a href="<?php echo esc_url( $image_full[0] );?>" rel="prettyPhoto[portfolio]">
                            <img src="<?php echo esc_url( $image_thumb[0] ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" />
                        </a>
	                </li>
                    <?php   endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
        <?php                
            $media_content = ob_get_clean();
        }

        if($portfolio_layout != '3'){
           echo $media_content;
        }

        ob_start();
        ?>
        <div class="sidebar<?php echo $wyde_sidebar_position == '1'? '':' col-md-3'; ?><?php echo ($wyde_sidebar_position == '3'? ' col-md-offset-1':'');?>">
                <div class="content">
                    <div class="toggle">
                        <?php 
                        $client_name =  get_post_meta($post->ID, '_meta_client_name', true );
                        $client_detail = get_post_meta($post->ID, '_meta_client_detail', true );
                        $client_website = get_post_meta($post->ID, '_meta_client_website', true );
                        ?>
                        <?php if( !empty( $client_name )){ ?>
                        <section class="toggle-item">                    
                            <h4 class="toggle-header"><i></i><?php echo __('Client', 'Vela'); ?></h4>
                            <div class="toggle-content">
                                <h5 class="name"><?php echo esc_html( $client_name ); ?></h5>
                                <p><?php echo esc_textarea( $client_detail );?></p> 
                                <?php if( !empty( $client_website )){ ?> 
                                <p class="post-meta"><a href="<?php echo esc_url( $client_website );?>" title="<?php echo esc_attr( $client_name ); ?>" target="_blank" class="tooltip-item"><i class="fa fa-globe"></i><?php echo __('Website', 'Vela');?></a></p> 
                                <?php }?>
                            </div>
                        </section>
                        <?php }?>
                        <?php 
                        $categories = get_the_terms( $post->ID, 'portfolio_category' );
                        ?>
                        <?php if($categories){ ?>
                        <section class="toggle-item">                    
                            <h4 class="toggle-header"><i></i><?php echo __('Categories', 'Vela'); ?></h4>
                            <div class="toggle-content">
                                <ul>
                                <?php foreach ( $categories as $item ) {?>
                                    <li><?php echo esc_html( $item->name );?></li>
                                <?php } ?>  
                                </ul>
                            </div>
                        </section>
                        <?php }?>
                        <?php 
                        $skills = get_the_terms( $post->ID, 'portfolio_skill' );
                        ?>
                        <?php if($skills){ ?>
                        <section class="toggle-item">                    
                            <h4 class="toggle-header"><i></i><?php echo __('Skills', 'Vela'); ?></h4>
                            <div class="toggle-content">
                                <ul>
                                <?php foreach ( $skills as $item ) {?>
                                    <li><?php echo esc_html( $item->name );?></li>
                                <?php } ?>  
                                </ul>
                            </div>
                        </section>
                        <?php }?>
                    </div>
                    <?php $project_url = get_post_meta($post->ID, '_meta_project_url', true );?>
                    <?php if( !empty( $project_url )){ ?>
                    <p><a href="<?php echo esc_url( $project_url );?>" title="Visit Site" class="button launch-project" target="_blank"><?php echo __('Visit Site', 'Vela');?></a></p>
                    <?php }?>
                </div>
            </div>
        <?php
            $sidebar_content = ob_get_clean();        
        ?>
        <div class="row">
            <?php if($wyde_sidebar_position == '2') echo $sidebar_content;?>            
            <div class="col-md-<?php echo $wyde_sidebar_position == '1'? '12':'8';?><?php echo $wyde_sidebar_position == '2'? ' col-md-offset-1':'';?> main">
                <div class="portfolio-detail-inner">
                    <?php
                    if($portfolio_layout == '3'){
                        echo $media_content;
                    }
                    the_title('<h2 class="post-title">', '</h2>');
                    ?>
                    <div class="post-content">
                    <?php 
                    the_content();
                    ?>
                    </div>
                <?php if($wyde_options['portfolio_nav']) wyde_portfolio_nav(); ?>
                </div>
                <?php if($wyde_options['portfolio_related']) wyde_related_portfolio(); ?>
            </div>
            <?php if($wyde_sidebar_position == '3') echo $sidebar_content;?>            
        </div>
        <?php } ?>
    </div>
</div>
<?php endif; ?>
<?php get_footer(); ?>