<?php
    global $wyde_options;
    
?>
<div class="header">
    <div class="container">
        <div class="mobile-nav-icon">
            <i class="fa fa-bars"></i>
        </div>  
        <?php get_template_part('/inc/headers/logo'); ?>          
        <div class="nav-wrapper">
            <nav id="nav" class="nav <?php echo wp_is_mobile()?'mobile-nav':'dropdown-nav'?>">
                <ul class="menu">
                    <?php wyde_primary_menu(); ?>
                </ul>
            </nav>
            <?php if( $wyde_options['menu_shop_cart'] && function_exists('wyde_woocommerce_menu')){ ?>
            <ul id="shop-menu">
            <?php 
            echo wyde_woocommerce_menu(); 
            ?>
            </ul>
            <?php } ?>
            <?php if($wyde_options['menu_search_icon']){ ?>
            <div id="search">
                <?php get_template_part('/inc/ajax-search');?>
            </div>
            <?php } ?>
        </div>
    </div>
</div>