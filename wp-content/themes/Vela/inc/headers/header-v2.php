<?php
    global $wyde_options;
?>
<div class="header-top">
    <div class="container">
        <div class="header-left">
            <?php
            if($wyde_options['top_bar_menu']=='1'){
            ?>
            <div class="top-nav dropdown-nav">
                <ul class="menu">
                <?php  wyde_menu('top', 5); ?>
                </ul>
            </div>
            <?php } ?>            
            <?php if($wyde_options['menu_social_icon']=='1'){ ?>
            <?php wyde_social_icons('bottom');?>        
            <?php } ?>
        </div>
        <div class="header-right">
            <?php
            if($wyde_options['top_bar_menu']=='2'){
            ?>
            <div class="top-nav dropdown-nav">
                <ul>
                <?php  wyde_menu('top', 5); ?>
                </ul>
            </div>
            <?php } ?>            
            <?php if($wyde_options['menu_social_icon']=='2'){ ?>
            <?php wyde_social_icons('bottom');?>        
            <?php } ?>
            <?php if($wyde_options['menu_shop_cart'] && function_exists('wyde_woocommerce_menu')){ ?>
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
        </div>
    </div>
</div>