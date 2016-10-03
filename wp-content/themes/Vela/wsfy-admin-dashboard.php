<?php
/*
* Template Name: wsfy-admin-dashboard
*/
global
  $wsfy_user_types_interpreters,
  $wsfy_user_types_requesters,
  $wsfy_service_dashboard_columns,
  $wsfy_translator_dashboard_columns,
  $wsfy_cost_by_duration;


$role = get_user_role();
if(!is_user_logged_in() || $role != 'administrator') {
  wp_redirect(site_url());
  exit; 
}

wp_enqueue_script('wsfy-administrator-dashboard-js', WSFY_URL . '/js/administrator-dashboard.js', [
    'jquery','wsfy-jquery-datatables'
  ], WSFY_VERSION, true);     

wp_enqueue_script('wsfy-jquery-datatables', WSFY_URL . '/js/jquery.dataTables.min.js', ['jquery'], WSFY_VERSION, true);  

wp_enqueue_style( 'wsfy-jquery-datatables-css', WSFY_URL . '/css/jquery.dataTables.min.css',[], WSFY_VERSION);

wp_enqueue_script('wsfy-datatables-buttons', WSFY_URL . '/js/jquery.dataTables.min.js', ['wsfy-jquery-datatables'], WSFY_VERSION, true);
wp_enqueue_script('wsfy-datatables-buttons', WSFY_URL . '/js/dataTables.buttons.min.js', ['wsfy-jquery-datatables'], WSFY_VERSION, true);
wp_enqueue_script('wsfy-buttons-html5', WSFY_URL . '/js/buttons.html5.min.js', ['wsfy-jquery-datatables'], WSFY_VERSION, true);
wp_enqueue_script('wsfy-buttons-colVis', WSFY_URL . '/js/buttons.colVis.min.js', ['wsfy-jquery-datatables'], WSFY_VERSION, true);

  
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
                      <h3 class="text-center">Administrator Dashboard</h3>                        
                      <div style="position: relative;">
                        <div class="row">
                          <div class="col-xs-12 col-sm-6 col-md-6 mb10">
                              <label>Display Appointments:</label>
                              <select id="available_requests_select" autocomplete="off">
                                <option value="all_requests">All</option>
                                <option value="pending_requests">Pending</option>
                              </select>                         
                          </div>
                          <div class="col-xs-12 col-sm-6 col-md-6">
                            <button id="export_requests" class="btn btn-default mr-xs pull-right" type="button">Export</button>
                          </div>
                        </div>
                        <div class="row">
                          <div class="col-xs-12 col-sm-12 col-md-12">
                          <table id="wsfy_requests" class="display row-selected" cellspacing="0" width="100%"></table>
                          </div>
                        </div>
                        
<div class="form-group admin-settings">
  <?php
    $rows = $wpdb->get_results('SELECT * FROM '.$wpdb->prefix.'wsfy_appointment_payouts_by_duration');
    $data = [];
    foreach($rows as $row) {
      $data[$row->user_type][$row->duration] = $row->payout; 
    } 
  ?>
  <h3 class="text-center">Payouts Settings</h3>
  <div class="row">
    <?php
      foreach($data as $translator_type=>$payout_details) {
        echo '
        <div class="col-xs-12 col-sm-4 col-md-4 mb20">
        <div id="pnl_'.$translator_type.'">
          <div class="form-group">                       
            <div class="row">
              <div class="col-xs-12 col-sm-12 col-md-12">
                <h4>'.$wsfy_user_types_interpreters[$translator_type].'</h4>
              </div>
            </div>
          </div>
          <div class="form-group">  
            <div class="row">
              <div class="col-xs-3 col-sm-3">
                <label for="">Duration:</label>   
                </div> 
              <div class="col-xs-3 col-sm-3">
                <label for="">Payout:</label>   
              </div>
            </div>
          </div>';
        foreach($payout_details as $duration => $payout) {
          echo '
            <div>
              <div class="form-group">  
              <div class="row setting-row">
                <div class="col-xs-3 col-sm-3">
                  <input name="duration" type="text" disabled="true" value="'.$duration.'"/>
                  </div> 
                <div class="col-xs-3 col-sm-3">
                  <input name="payout" type="text" value="'.$payout.'"/>        
                </div>
              </div>
              </div>  
            </div>
            ';         
        }
        echo '
            <div class="row">
              <div class="col-xs-12 col-sm-6 col-md-5">
                <input type="button" id="'.$translator_type.'" type="button" class="btn btn-primary pull-right btn-payout-setting" data-dismiss="modal" value="Save"/>      
              </div>
            </div>      
        </div>
        </div>
        ';
      }
    ?>
  </div>  
  <div class="clearfix"></div>
  <br/>
  <h3 class="text-center">Cost By Duration Settings</h3>
  <div class="row">
    <?php
      foreach($wsfy_cost_by_duration as $requester_type=>$cost_details) {
        echo '
        <div class="col-xs-12 col-sm-4 col-md-4 mb20">
        <div id="pnlcost_'.$requester_type.'">
          <div class="form-group">                       
            <div class="row">
              <div class="col-xs-12 col-sm-12 col-md-12">
                <h4>'.ucfirst($requester_type).'</h4>
              </div>
            </div>
          </div>
          <div class="form-group">  
            <div class="row">
              <div class="col-xs-3 col-sm-3">
                <label for="">Duration:</label>   
                </div> 
              <div class="col-xs-3 col-sm-3">
                <label for="">Cost:</label>   
              </div>
            </div>
          </div>';
        foreach($cost_details as $duration => $cost) {
          echo '
            <div>
              <div class="form-group">  
              <div class="row setting-row">
                <div class="col-xs-3 col-sm-3">
                  <input name="duration" type="text" disabled="true" value="'.$duration.'"/>
                  </div> 
                <div class="col-xs-3 col-sm-3">
                  <input name="cost" type="text" value="'.$cost.'"/>        
                </div>
              </div>
              </div>  
            </div>
            ';         
        }
        echo '
            <div class="row">
              <div class="col-xs-12 col-sm-6 col-md-5">
                <input type="button" id="'.$requester_type.'" type="button" class="btn btn-primary pull-right btn-cost-setting" data-dismiss="modal" value="Save"/>      
              </div>
            </div>      
        </div>
        </div>
        ';
      }
    ?>  
  </div>  
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