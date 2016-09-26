<?php
/*
* Template Name: wsfy-service-dashboard
*/
global $wsfy_user_types_requesters; 
global $wsfy_service_dashboard_columns;

$role = get_user_role();
if(!is_user_logged_in() || ($role != 'administrator' && !$wsfy_user_types_requesters[$role])) {
  wp_redirect(site_url());
  exit; 
}  

wp_enqueue_script('wsfy-service-dashboard-js', WSFY_URL . '/js/service-dasboard.js', [
    'jquery','wsfy-jquery-datatables'
  ], WSFY_VERSION, true);  
wp_enqueue_script('wsfy-jquery-datatables', WSFY_URL . '/js/jquery.dataTables.min.js', ['jquery'], WSFY_VERSION, true);  

wp_enqueue_style( 'wsfy-jquery-datatables-css', WSFY_URL . '/css/jquery.dataTables.min.css',[], WSFY_VERSION);
  
?>
<?php get_header(); ?>
<?php
global $wyde_options, $wyde_page_id, $wyde_sidebar_position;

if($_GET['request']) {
  if(!($request = get_post($_GET['request']))) {
    wp_redirect(site_url('request-service'));
    exit;     
  }
  $postID = $request->ID;
  $meta = get_post_meta($request->ID);
  foreach($meta as $name=>$value) {
    $$name = ($value[0]?:'');  
  }
}

    
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
                      <h3 class="text-center">Service Dashboard</h3>                        
                      <div>
                      
                        <div class="form-group">
                        <table id="wsfy_service_dashboard" class="display row-selected" cellspacing="0" width="100%">
                          <thead>
                            <tr>
                              <?php
                                foreach($wsfy_service_dashboard_columns as $field => $title) {
                                  echo '<th>'.$title.'</th>';  
                                }
                              ?>
                            </tr>
                          </thead>
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