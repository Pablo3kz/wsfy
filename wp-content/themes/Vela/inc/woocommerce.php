<?php
    
if( ! defined( 'ABSPATH' ) ) {
    die;
}

if( ! class_exists( 'Wyde_WooCommerce_Template' ) ) {

class Wyde_WooCommerce_Template
{

    	function __construct() {

            add_filter( 'woocommerce_show_page_title', array( $this, 'shop_title'), 10 );

            remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
    		remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
    		add_action( 'woocommerce_before_main_content', array( $this, 'before_container' ), 10 );
    		add_action( 'woocommerce_after_main_content', array( $this, 'after_container' ), 10 );

            remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
            add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_rating', 1 );//insert rating before add to cart button
            //add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_rating', 10 );//insert rating after add to cart button


            remove_action( 'woocommerce_sidebar' , 'woocommerce_get_sidebar', 10 );
            add_action( 'woocommerce_sidebar', array($this, 'add_sidebar'), 10);
        }

        function shop_title() {
			return false;
		}
	
        function before_container() {
			global $wyde_options, $wyde_page_id, $wyde_sidebar_position;

            if(is_single()){
                 $wyde_sidebar_position = $wyde_options['shop_single_sidebar']; 
            }else{
                $wyde_sidebar_position = get_post_meta( $wyde_page_id, '_meta_sidebar_position', true );
            }
                
            if(!$wyde_sidebar_position) $wyde_sidebar_position = '1';

            if( isset($_GET['sidebar']) && $_GET['sidebar'] == 'false') $wyde_sidebar_position = '1';//For Demo Only

            ?>
            <div class="container main-content <?php echo esc_attr( wyde_get_layout_name($wyde_sidebar_position) ); ?>">
                <div class="row">
                    <?php
                    if($wyde_sidebar_position == '2') wyde_sidebar('shop');
                    ?>
                    <div class="col-md-<?php echo $wyde_sidebar_position == '1'? '12':'8';?> main">
                        <div class="content shop">
        <?php
		}

		
		function after_container() {
        ?>
                             </div>
                         </div>
        <?php
		}

        function add_sidebar(){
            global $wyde_sidebar_position;
            if($wyde_sidebar_position=='3') wyde_sidebar('shop');
            ?>
                    </div>
                </div>
        <?php
        }

}

}

new Wyde_WooCommerce_Template();


function wyde_woocommerce_menu(){
    global $woocommerce;
    $menu_content= sprintf ('<li class="menu-cart navbar-right"><a href="%1$s"><i class="fa fa-shopping-cart"></i><span class="cart-items%2$s">%3$s</span></a><ul class="dropdown-menu"><li class="clear"><span class="view-cart"><a href="%1$s" class="ghost-button"><i class="fa fa-shopping-cart"></i>%4$s</a></span><span class="total">%5$:%6$s</span></li></ul></li>', esc_url( $woocommerce->cart->get_cart_url() ), ( $woocommerce->cart->cart_contents_count > 0 ? '':' empty' ), $woocommerce->cart->cart_contents_count, __('Cart', 'Vela'), __('Total', 'Vela'), $woocommerce->cart->get_cart_total() );
    return $menu_content;
}

add_filter('add_to_cart_fragments', 'woocommerce_add_to_cart_fragment');
function woocommerce_add_to_cart_fragment( $fragments ) {
	$fragments['li.menu-cart'] = wyde_woocommerce_menu();
	return $fragments;
}

// Change number or products per page
add_filter( 'loop_shop_per_page', 'wyde_products_per_page', 20 );
function wyde_products_per_page(){
    global $wyde_options;
    return isset( $wyde_options['product_items'] )? intval( $wyde_options['product_items'] ):12;
}

// Change number or products per row
add_filter('loop_shop_columns', 'shop_columns');
function shop_columns() {
    global $wyde_sidebar_position;
    if($wyde_sidebar_position == '1') return 4;
    else return 3;
}

add_filter( 'woocommerce_output_related_products_args', 'wyde_related_products_args' );
function wyde_related_products_args( $args ) {
    global $wyde_options;
	$args['posts_per_page'] =  isset( $wyde_options['related_product_items'] )? intval( $wyde_options['related_product_items'] ):4; 
	$args['columns'] =  5; 
	return $args;
}