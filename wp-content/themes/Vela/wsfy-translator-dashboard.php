<?php
/*
* Template Name: wsfy-translator-dashboard
*/
global
  $wsfy_user_types_interpreters,
  $wsfy_service_dashboard_columns,
  $wsfy_translator_dashboard_columns;


$role = get_user_role();
if(!is_user_logged_in() || ($role != 'administrator' && !$wsfy_user_types_interpreters[$role])) {
  wp_redirect(site_url());
  exit; 
}
 
wp_enqueue_script('wsfy-translator-dashboard-js', WSFY_URL . '/js/translator-dashboard.js', [
    'jquery','wsfy-jquery-datatables'
  ], WSFY_VERSION, true);  
  
wp_enqueue_script('wsfy-jquery-datatables', WSFY_URL . '/js/jquery.dataTables.min.js', ['jquery'], WSFY_VERSION, true);  

wp_enqueue_style( 'wsfy-jquery-datatables-css', WSFY_URL . '/css/jquery.dataTables.min.css',[], WSFY_VERSION);
   
?>

<?php get_header(); ?>    

<?php
global $wyde_options, $wyde_page_id, $wyde_sidebar_position;
    
if($wyde_options['onepage'] && is_front_page()){
    //if onepage site option enabled, load onepage template part
    $wyde_sidebar_position = '0';
    get_template_part('page', 'onepage');
}else{
    if(have_posts()): the_post();
    $wyde_sidebar_position = get_post_meta( $wyde_page_id, '_meta_sidebar_position', true );
    if( $wyde_sidebar_position == false ) $wyde_sidebar_position = '1';
    ?>
    
    <div class="container main-content <?php echo esc_attr( wyde_get_layout_name($wyde_sidebar_position) ); ?>">
        <div class="row">
            <div class="col-md-<?php echo $wyde_sidebar_position == '1'? '12':'8';?><?php echo $wyde_sidebar_position == '2'? ' col-md-offset-1':'';?> main">
                <div class="page-detail content">
                    <div class="page-detail-inner">
                      <h3 class="text-center">Translator Dashboard</h3>                        
                      <div>
                      
                        <div class="form-group">
                          <div class="form-group">   
                          <label>Display Appointments:</label>
                          <select id="available_requests_select" autocomplete="off">
                            <option value="available_requests">Available</option>
                            <option value="my_requests">My</option>
                            <option value="pending_requests">Pending</option>
                            <option value="rejected_requests">Rejected</option>
                            <option value="canceled_requests">Canceled</option>
                          </select> 
                          </div>                         
                          <table id="wsfy_translator_available_requests" class="display row-selected" cellspacing="0" width="100%">
                          </table>
                        </div>
                      </div>      
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
<?php } ?>
<?php get_footer(); ?>