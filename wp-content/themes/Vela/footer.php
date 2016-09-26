<?php
global $wyde_options, $wyde_page_id;
$footer_col = $wyde_options['footer_bar_columns']=='2'? 'col-sm-6':'col-sm-12';
?>          
        </div><!--#content-->
        <?php if( get_post_meta( $wyde_page_id, '_meta_page_footer', true ) != 'hide' ){  ?>
        <footer id="footer">
            <?php
                get_sidebar('footer');
            ?>
            <?php if($wyde_options['footer_bar']){ ?>
            <?php
                $footer_classes = array();
                if($wyde_options['footer_bar_columns'] == '1') $footer_classes[] = 'footer-center';
                if(!$wyde_options['footer_widget']) $footer_classes[] = 'footer-large';
            ?>
            <div id="footer-bottom" class="<?php echo esc_attr( implode(' ', $footer_classes) );?>">
                <div class="container">
                    <?php if($wyde_options['footer_bar_menu'] == '1'){ ?>
                    <div id="footer-nav" class="<?php echo esc_attr( $footer_col );?>">
                        <ul class="footer-menu">
                            <?php wyde_menu('footer', 1); ?>
                        </ul>
                    </div>
                    <?php }else if($wyde_options['footer_bar_menu'] == '2'){ ?>
                    <div id="footer-nav" class="<?php echo esc_attr( $footer_col );?>">
                        <?php wyde_social_icons(); ?>
                    </div>
                    <?php } ?>
                    <?php if($wyde_options['footer_text_show']){ ?>
                    <div id="footer-text" class="<?php echo esc_attr( $footer_col );?>">
                    <?php echo wp_kses_post( $wyde_options['footer_text'] ); ?>
                    </div>
                    <?php } ?>
                </div>
            </div>
            <?php } ?>
	    </footer>
        <?php } ?>
        <?php if($wyde_options["totop_button"]){ ?>
        <a id="toTop" href="#">
            <span class="border">
                <i class="fa fa-angle-up"></i>
            </span>
        </a>
        <?php } ?>
        <?php //include_once get_template_directory() . '/style-selector.php'; //for demo only ?>
        <?php wyde_footer_content(); ?>
        </div><!--.page-inner-->
    </div><!--#page-->    
    <?php wp_footer(); ?>
</div><!--#container-->
</body>
</html>