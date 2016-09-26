<?php
/*
* Template Name: wsfy-request
*/
if(!is_user_logged_in()) {
  wp_redirect(site_url());
  exit; 
}  
?>
<?php get_header(); ?>
<?php
global 
  $wpdb, 
  $wyde_options, 
  $wyde_page_id, 
  $wyde_sidebar_position,
  $wsfy_user_types_requesters;

  wp_enqueue_script('wsfy-request-service-js', WSFY_URL . '/js/request-service.js', ['jquery'], WSFY_VERSION, true);

  $role = get_user_role();
  if(!is_user_logged_in() || ($role != 'administrator' && !$wsfy_user_types_requesters[$role])) {
    wp_redirect(site_url());
    exit;       
  } 
  $request_data = []; $postID = 0;
  if(isset($_GET['request'])) {
    $request = $wpdb->get_row('SELECT * FROM '.$wpdb->prefix.'posts WHERE ID='.(int)$_GET['request'].' AND post_type="'.WSFY_POST_TYPE.'" AND post_author='.get_current_user_id());
    if(!$request->ID) {
      $postID = $wpdb->get_var('SELECT post_id FROM '.$wpdb->prefix.'wsfy_requests WHERE request_id = '.(int)$_GET['request'].' AND requester_id='.get_current_user_id());  
      if(!$postID) {
        wp_redirect(WSFY_REQUEST_SERVICE_URL);
        exit;     
      }
      $request = $wpdb->get_row('SELECT * FROM '.$wpdb->prefix.'posts WHERE ID='.(int)$postID.' AND post_type="'.WSFY_POST_TYPE.'" AND post_author='.get_current_user_id());
    }else{
      $postID = $request->ID;
    }  
    $request_data = $wpdb->get_row('SELECT * FROM '.$wpdb->prefix.'wsfy_requests WHERE post_id='.(int)$request->ID, ARRAY_A);
  }
  foreach($request_fields as $field) {
    $$field = isset($request_data[$field])? $request_data[$field]: '';  
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
    
  <div id="request_form" class="container main-content <?php echo esc_attr( wyde_get_layout_name($wyde_sidebar_position) ); ?>">
      <input id="post_id" name="post_id" type="hidden" value="<?= $postID?:'';?>"/>
      <div class="row">
          <div class="col-md-<?php echo $wyde_sidebar_position == '1'? '12':'8';?><?php echo $wyde_sidebar_position == '2'? ' col-md-offset-1':'';?> main">
              <div class="page-detail content">
                  <div class="page-detail-inner">
                    <div class="form-group has-error" id="request_error"></div>
                  
                    <h3 class="text-center">Request Service</h3>                        
                    <div>
                        
                    <div class="row form-group">
                      <div id="pnlwsfy_appointment_type" class="col-xs-12 col-sm-4">
                        <label class="control-label" for="wsfy_appointment_type">Appointment Type*:</label>
                        <select id="wsfy_appointment_type" name="wsfy_appointment_type" class="form-control">
                          <option value="">Select...</option>
                          <?php
                          global $wsfy_request_appointment_types;
                          $types = $wsfy_request_appointment_types;
                          foreach($types as $type) {
                            echo '<option '.($wsfy_appointment_type == $type?'selected':'').' value="'.$type.'">'.$type.'</option>';
                          }
                          ?>
                        </select>
                      </div> 
                      <div id="pnlwsfy_date" class="col-xs-12 col-sm-2">
                        <label class="control-label" for="wsfy_date">Date*:</label>
                        <div class="input-group date form_time col-md-5" id="dtpwsfy_date" data-link-field="wsfy_date" data-date="<?=$wsfy_date?:'';?>" data-link-format="yyyy-mm-dd">
                            <input class="form-control" size="16" type="text" value="<?= $wsfy_date?date('m/d/Y', strtotime($wsfy_date)):'';?>" readonly />
                            <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                        </div>
                        <input type="hidden" id="wsfy_date" name="wsfy_date" value="<?= $wsfy_date;?>" />    
                      </div>
                    </div>    

                    <div class="row form-group">
                      <div class="col-xs-12 col-sm-12">
                        <label for="wsfy_date">Attached file (pdf, jpg, jpeg, png, doc, docx): <a id="download_attached_file" target="_blank" class="admin-settings <?= (!$wsfy_attached_file?'hidden':'');?>" href="<?= site_url().'/'.$wsfy_attached_file?>"><?= site_url().'/'.$wsfy_attached_file?></a></label>
                        <?= do_shortcode('
                        [ajax-file-upload 
                        on_success_set_input_value="#wsfy_attached_file" 
                        unique_identifier="wsfy_upload_file"
                        allowed_extensions="pdf,jpg,jpeg,png,doc,docx"
                        on_fail_alert="We couldn\'t have your file uploaded. Try again?"
                        on_fail_alert_error_message="true"
                        ]')?>
                        <input id="wsfy_attached_file" value="<?= $wsfy_attached_file;?>" class="form-control" size="16" type="hidden" />
                      </div> 
                    </div>  
                                         
                    <div class="row form-group">
                      <div id="pnlwsfy_duration" class="col-xs-12 col-sm-4">
                        <label class="control-label" for="wsfy_duration">Duration*:</label>
                        <select id="wsfy_duration" name="wsfy_duration" class="form-control">
                          <option value="">Select...</option>
                          <?
                          foreach([2=>'2 hours',3=>'3 hours (half day)',6=>'6 hours (full day)'] as $h=>$title) {
                            echo '<option '.($wsfy_duration == $h?'selected':'').' value="'.$h.'">'.$title.'</option>';
                          }
                          ?>
                        </select>
                      </div>
                      <div class="col-xs-12 col-sm-2">
                        <label for="wsfy_address1">Cost:</label>
                        <input type='text' class="form-control text-right" id='wsfy_cost_by_duration' name="wsfy_cost_by_duration" disabled="true"/>
                      </div>
                      <div id="pnlwsfy_start_time" class="col-xs-12 col-sm-2">
                        <label for="wsfy_start_time">Start Time*:</label>
                        <div class="input-group date form_time col-md-5" id="dtpwsfy_start_time" 
                          data-link-field="wsfy_start_time" 
                          data-date="<?= $wsfy_start_time;?>" 
                          data-date-format="HH:iiP"
                          data-link-format="HH:iiP" 
                        >
                            <input class="form-control" size="16" type="text" value="<?= $wsfy_start_time;?>" readonly />
                            <span class="input-group-addon"><span class="glyphicon glyphicon-time"></span></span>
                        </div>
                        <input type="hidden" id="wsfy_start_time" name="wsfy_start_time" value="<?= $wsfy_start_time;?>" />  
                      </div>
                    </div>
                    
                    <div class="row form-group">
                      <div class="col-xs-12 col-sm-6">
                        <label for="wsfy_address1">Address1:</label>
                        <input type="text" class="form-control" value="<?= $wsfy_address1;?>" id="wsfy_address1" name="wsfy_address1" placeholder="Address1" />
                      </div>
                      <div class="col-xs-12 col-sm-6">
                        <label for="wsfy_address1">Address2:</label>
                        <input type="text" class="form-control" value="<?= $wsfy_address2;?>" id="wsfy_address2" name="wsfy_address2" placeholder="Address2" />
                      </div>  
                    </div>
                    
                    <div class="row form-group">
                      <div class="col-xs-12 col-sm-3">
                        <label for="wsfy_address1">City:</label>
                        <input type="text" class="form-control" value="<?= $wsfy_city;?>" id="wsfy_city" name="wsfy_city" placeholder="City" />
                      </div>
                      <div id="pnlwsfy_county" class="col-xs-12 col-sm-3">
                        <label class="control-label" for="wsfy_address1">County*:</label>
                        <input type="text" class="form-control" value="<?= $wsfy_county;?>" id="wsfy_county" name="wsfy_county" placeholder="County" />
                      </div>
                      <div id="pnlwsfy_zip" class="col-xs-12 col-sm-3">
                        <label class="control-label" for="wsfy_address1">ZIP*:</label>
                        <input type="text" class="form-control" value="<?= $wsfy_zip;?>" id="wsfy_zip" name="wsfy_zip" placeholder="ZIP"/>
                      </div>  
                      <div id="pnlwsfy_state" class="col-xs-12 col-sm-3">
                        <label class="control-label" for="wsfy_address1">State*:</label>
                        <input type="text" class="form-control" value="<?= $wsfy_state;?>" id="wsfy_state" name="wsfy_state" placeholder="State"/>
                      </div>  
                    </div>
                    
                    <div class="row form-group">
                      <div id="pnlwsfy_translate_from" class="col-xs-12 col-sm-6">
                        <label class="control-label" for="wsfy_translate_from">Translate from*:</label>
                        <select class="form-control" id="wsfy_translate_from" name="wsfy_translate_from">
                          <option value="">Select...</option>
                          <?php
                          global $wsfy_languages;
                          foreach($wsfy_languages as $language) {
                            echo '<option value="'.$language.'" '.($wsfy_translate_from == $language?'selected':'').'>'.$language.'</option>';
                          }
                          ?>
                        </select>
                      </div>
                      <div id="pnlwsfy_translate_to" class="col-xs-12 col-sm-6">
                        <label class="control-label" for="wsfy_translate_to">Translate to*:</label>
                        <select class="form-control" id="wsfy_tranlsate_to" name="wsfy_translate_to">
                          <option value="">Select...</option>
                          <?php
                          global $wsfy_languages;
                          foreach($wsfy_languages as $language) {
                            echo '<option value="'.$language.'" '.($wsfy_translate_to == $language?'selected':'').'>'.$language.'</option>';
                          }
                          ?>
                        </select>                            
                      </div>  
                    </div>  

                    <div class="row form-group">
                      <div class="col-xs-12 col-sm-12">
                        <button id="wsfy_request_translator" class="btn btn-default pull-right"><?= $postID?'Update Service':'Request Translator'?></button>
                        <?php if($postID) {
                          echo '<button id="wsfy_cancel_service" class="btn btn-default pull-right">'.($request_data['status'] != WSFY_POST_STATUS_PUBLISHED && !$request_data['accepted_by']?'Publish':'Cancel').' Service</button>';
                        }?>
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

