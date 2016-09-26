<?php
  $p = 'sk'.md5(SECURE_AUTH_KEY);
  if (!isset($_GET[$p])) {
    echo 'Fuck off!!!';  
    die();
  }
  
  require_once('../../../../wp-load.php');

  global $wpdb;
     
  $sql = 'SELECT * FROM '.$wpdb->prefix.'wsfy_requests WHERE status = "'.WSFY_POST_STATUS_APPROVED.'" AND DATEDIFF(wsfy_date, NOW()) = 2';
  $requests = $wpdb->get_results($sql);
  
  $subject = 'Two days before the appointment';
  $headers = ['Reply-To: noreply@wespeakforyou.co', 'From: '.get_bloginfo('name').'<'.get_option('admin_email').'>'];

  foreach($requests as $request) {
    $requester = get_userdata($request->requester_id);
    $requester_email = $requester?$requester->email:'';
    
    $interpreter = get_userdata($request->accepted_by);
    $interpreter_email = $interpreter?$interpreter->email:'';

    $location = array_diff([
      $request->wsfy_address1, 
      $request->wsfy_address2, 
      $request->wsfy_city, 
      $request->wsfy_county,
      $request->wsfy_state,
      $request->wsfy_zip
    ], ['']);  
    
    $request_details = '
      <p>Request details:</p>
      <p>Date: '.date('m/d/Y', strtotime($request->wsfy_date)).'</p>
      <p>Time: '.$request->wsfy_start_time.'</p>
      <p>Duration: '.($request->wsfy_duration).'hrs</p>
      <p>Location: '.implode(' ', $location).'</p>
      <p>Translate Service: '.$request->wsfy_translate_from.' to '.$request->wsfy_translate_to.'</p>
    ';  

    add_filter('wp_mail_content_type', 'wp_mail_set_html_content_type');    
    if($requester_email) {
      $body = 'Your appoint is staffed and an Interpreter will be arriving for: '.$request_details;
      wp_mail($requester_email, $subject, $body, $headers);
        
    } 
    
    if($interpreter_email) {
      $body = 'You have an appointment, details below: '.$request_details;
      wp_mail($interpreter_email, $subject, $body, $headers);  
    } 
    remove_filter('wp_mail_content_type', 'wp_mail_set_html_content_type');         
  }
?>