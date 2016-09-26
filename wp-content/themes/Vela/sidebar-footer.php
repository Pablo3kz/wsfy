<?php
global $wyde_options;

if($wyde_options['footer_widget'] == 1){
    
$footer_classes = array();

$footer_background = '';

if( isset($wyde_options['footer_background_parallax']) && $wyde_options['footer_background_parallax'] )  $footer_classes[] = 'parallax';

$footer_columns = intval( $wyde_options['footer_widget_columns'] );

array_push($footer_classes, 'grid-'.$footer_columns.'-col');

?>
<div id="footer-widget" class="<?php echo esc_attr( implode(' ', $footer_classes) ); ?>" style="">
    <div class="container">
            <?php
            $col = 'sm';
            if($footer_columns > 2) $col = 'md';
            for($i = 1; $i <= $footer_columns; $i++){
            ?>
                <div class="column col-<?php echo $col; ?>-<?php echo absint( floor( 12 / $footer_columns ) );?>">
                    <div class="content">
                    <?php dynamic_sidebar('footer'.$i); ?>
                    </div>
                </div>
            <?php } ?>
    </div>
</div>
<?php } ?>