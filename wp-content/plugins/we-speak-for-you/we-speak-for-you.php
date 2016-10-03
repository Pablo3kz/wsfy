<?php
/*
Plugin Name: We Speak For You
Description: 
Version: 1.0
Author: 
Author URI: 
Plugin URI: 
*/
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

define('WSFY_VERSION', '1.0.0');
define('WSFY_DIR', plugin_dir_path(__FILE__));
define('WSFY_URL', plugin_dir_url(__FILE__));

define('WSFY_POST_TYPE', 'wsfy_request');

define('WSFY_ROLE_ADMIN', 'administrator');

define('WSFY_POST_STATUS_PUBLISHED', 'published');
define('WSFY_POST_STATUS_CANCELED', 'canceled');
define('WSFY_POST_STATUS_ACCEPTED', 'accepted');
define('WSFY_POST_STATUS_PENDING', 'published');
define('WSFY_POST_STATUS_APPROVED', 'approved');

define('WSFY_REQUEST_SERVICE_URL', site_url('request-service'));
define('WSFY_REQUESTER_DASHBOARD_URL', site_url('service-dashboard'));
define('WSFY_TRANSLATOR_DASHBOARD_URL', site_url('translator-dashboard'));
define('WSFY_ADMIN_DASHBOARD_URL', site_url('administrator-dashboard'));


global
   $wpdb,
   $wsfy_user_types_requesters, 
   $wsfy_user_types_interpreters,
   $wsfy_requester_fields,
   $wsfy_interpreter_fields,
   $wsfy_post_statuses,
   $wsfy_request_appointment_types,
   $wsfy_request_appointment_sub_types,
   $wsfy_cost_by_duration,
   $wsfy_languages,
   $wsfy_service_dashboard_columns,
   $request_fields,
   $wsfy_translator_dashboard_columns,
   $wsfy_admin_dashboard_columns,
   $wsfy_cost_by_requster_type;

$wsfy_languages = [
  'Afrikaans',
  'Albanian',
  'English',
  'Russian',
  'Turkish',
  'Zulu'
];

$wsfy_user_types_requesters = [
  'medical_requester' => 'Medical requester',
  'legal_requester' => 'Legal requester',
  'insurance_requester' => 'Insurance requester', 
  'agency_requester' => 'Agency requester',
  'other_requester' => 'Other requester',
];


$wsfy_user_types_interpreters = [
  'qualified_interpreter' => 'Qualified Interpreter',
  'medical_interpreter' => 'Medical Interpreter',
  'administrative_hearing_interpreter' => 'Administrative Hearing Interpreter',
  'court_interpreter' => 'Court Interpreter',
  'federal_interpreter' => 'Federal Interpreter'
];

$wsfy_request_appointment_types = [
  'medical',
  'legal',
  'insurance',
  'workers-comp',
  'agency',
  'other'
];

$wsfy_request_appointment_sub_types = [
  'workers-comp' => [
    'deposition preps',
    'half day deposition',
    'full day deposition',
    'transcript read backs',
    'settlements and stipulations',
    'telephonic settlements and stipulations',
    'worker`s compensation board appearances',
  ]
];

$wsfy_interpreters_allowed_appointment_types = [
  'qualified_interpreter' => ['medical', 'other'],
  'medical_interpreter' => ['medical', 'other'],
  'administrative_hearing_interpreter' => $wsfy_request_appointment_types,
  'court_interpreter' => $wsfy_request_appointment_types,
  'federal_interpreter' => $wsfy_request_appointment_types
];

$wsfy_post_statuses = [
  'published'  => 'Published By The requester',
  'canceled'  => 'Canceled By The requester',
  'accepted' => 'Accepted By The Interpretator',
  'approved' => 'The Accepted Request Is Approved By The Administrator'
];

$wsfy_requester_fields = [
  ['name' => 'first_name', 'label' => 'First Name', 'isRequired' => true],
  ['name' => 'last_name', 'label' => 'Last Name', 'isRequired' => true],
  ['name' => 'email', 'label' => 'Email', 'isRequired' => true, 'type' => 'email'],
  ['name' => 'password', 'label' => 'Password', 'isRequired' => true, 'type' => 'password'],
  ['name' => 'confirm_password', 'label' => 'ConfirmPassword', 'isRequired' => true, 'type' => 'password'],
  ['name' => 'phone', 'label' => 'Phone', 'isRequired' => true],
  ['name' => 'county', 'label' => 'County', 'isRequired' => true],
  ['name' => 'zip', 'label' => 'ZIP', 'isRequired' => true],
  ['name' => 'state', 'label' => 'State', 'isRequired' => true],
  ['name' => 'company_name', 'label' => 'Company Name', 'isRequired' => true]
];

$wsfy_interpreter_fields = [
  ['name' => 'first_name', 'label' => 'First Name', 'isRequired' => true],
  ['name' => 'last_name', 'label' => 'Last Name', 'isRequired' => true],
  ['name' => 'email', 'label' => 'Email', 'isRequired' => true, 'type' => 'email'],
  ['name' => 'password', 'label' => 'Password', 'isRequired' => true, 'type' => 'password'],
  ['name' => 'confirm_password', 'label' => 'ConfirmPassword', 'isRequired' => true, 'type' => 'password'],
  ['name' => 'phone', 'label' => 'Phone', 'isRequired' => true],
  ['name' => 'county', 'label' => 'County', 'isRequired' => true],
  ['name' => 'zip', 'label' => 'ZIP', 'isRequired' => true],
  ['name' => 'state', 'label' => 'State', 'isRequired' => true],
  ['name' => 'language', 'label' => 'Interpret to', 'isRequired' => false, 'type' => 'select', 'options' => $wsfy_languages, 'multiple' => true],
  ['name' => 'qualifications', 'label' => 'Qualifications', 'isRequired' => false, 'type' => 'memo',
    'roles' => [
       'qualified_interpreter'
    ]
  ],
  ['name' => 'certification_number', 'label' => 'Certification Number', 'isRequired' => false, 
   'roles' => [
      'medical_interpreter',
      'administrative_hearing_interpreter',
      'court_interpreter','federal_interpreter'
    ]
  ],
  ['name' => 'certification_exp_date', 'label' => 'Certification Expiration Date', 'isRequired' => false, 
    'roles' => [
      'medical_interpreter',
      'administrative_hearing_interpreter',
      'court_interpreter','federal_interpreter'
    ]
  ],
];

$wsfy_cost_by_duration = [];
function wsfy_cost_by_duration()
{
  global 
    $wpdb,
    $wsfy_cost_by_duration;
    
  if(!is_plugin_active('we-speak-for-you/we-speak-for-you.php')) {
    return;
  }
  
  $rows = $wpdb->get_results('SELECT * FROM '.$wpdb->prefix.'wsfy_cost_by_duration ORDER BY appointment_type, duration');
  foreach($rows as $row) {
    $wsfy_cost_by_duration[$row->appointment_type][$row->duration] = $row->cost; 
  }
}
wsfy_cost_by_duration();

$wsfy_cost_by_requster_type = [];
function wsfy_cost_by_requster_type()
{
  global 
    $wpdb,
    $wsfy_cost_by_requster_type;
  
  if(!is_plugin_active('we-speak-for-you/we-speak-for-you.php')) {
    return;
  }
  
  $rows = $wpdb->get_results('SELECT * FROM '.$wpdb->prefix.'wsfy_cost_by_requester_type ORDER BY appointment_type, appointment_sub_type');
  foreach($rows as $row) {
    $wsfy_cost_by_requster_type[$row->appointment_type][$row->appointment_sub_type][$row->requester_type] = $row->cost; 
  }
}
wsfy_cost_by_requster_type();


$wsfy_service_dashboard_columns = [
  'wsfy_appointment_type' => 'Appointment Type',
  'wsfy_translation_type' => 'Translation Type',
  'wsfy_date' => 'Date',
  'status' => 'Status',
  'request_id' =>'Order #'
];

$wsfy_translator_dashboard_columns = [
  'wsfy_appointment_type' => 'Appointment Type',
  'wsfy_translation_type' => 'Translation Type',
  'wsfy_date' => 'Date',
  'wsfy_location' => 'Location',
  'wsfy_time' => 'Time',
  'wsfy_length' => 'Length',
  'duration' => 'Duration',
  'wsfy_pay_rate' =>'Pay Rate'
];

$wsfy_admin_dashboard_columns = [
  'wsfy_appointment_type' => 'Appointment Type',
  'wsfy_translation_type' => 'Translation Type',
  'wsfy_date' => 'Date',
  'status' => 'Status',
  'wsfy_location' => 'Location',
  'wsfy_time' => 'Time',
  'wsfy_length' => 'Length',
  'wsfy_cost' => 'Cost',
  'wsfy_pay_rate' =>'Pay Rate',
  'request_id' =>'Order #'
];

$wsfy_request_required_fields = [
  'wsfy_appointment_type' => 'Appointment Type',
  'wsfy_appointment_sub_type' => 'Appointment Type',
  'wsfy_date' => 'Date',
  'wsfy_duration' => 'Duration',
  'wsfy_start_time' => 'Start Time',
  'wsfy_county' => 'County',
  'wsfy_state' => 'State',
  'wsfy_zip' => 'ZIP',
  'wsfy_translate_from' => 'Translate from',
  'wsfy_translate_to' => 'Translate to'
];

if(!function_exists('wp_handle_upload')){
  require_once( ABSPATH . 'wp-admin/includes/file.php' );
}

$request_fields = get_table_fields_names($wpdb->prefix.'wsfy_requests');
function get_table_fields_names($table)
{
  global $wpdb;
  
  if(!is_plugin_active('we-speak-for-you/we-speak-for-you.php')) {
    return;
  } 
   
  $columns = $wpdb->get_col("DESC {$table}", 0);
  
  return $columns;  
}

function wsfy_load()
{
    if(is_admin()) {
      require_once(WSFY_DIR.'includes/admin.php');
    }  
 
    require_once(WSFY_DIR.'includes/core.php');
}
wsfy_load();

function create_tables() {
	global $wpdb;

  require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
  
  $table_name = $wpdb->prefix . 'wsfy_requests';
  $sql = "CREATE TABLE $table_name (
  request_id int(11) NOT NULL AUTO_INCREMENT,
  wsfy_appointment_type varchar(35) NOT NULL DEFAULT '',
  wsfy_appointment_sub_type varchar(100) NOT NULL DEFAULT '',
  wsfy_date date DEFAULT NULL,
  wsfy_duration tinyint(2) NOT NULL DEFAULT '0',
  wsfy_start_time varchar(10) NOT NULL DEFAULT '',
  wsfy_address1 varchar(100) NOT NULL DEFAULT '',
  wsfy_address2 varchar(100) DEFAULT NULL,
  wsfy_city varchar(50) NOT NULL DEFAULT '',
  wsfy_county varchar(30) DEFAULT '',
  wsfy_zip varchar(5) NOT NULL DEFAULT '',
  wsfy_state varchar(20) NOT NULL DEFAULT '',
  wsfy_translate_from varchar(50) NOT NULL,
  wsfy_translate_to varchar(30) NOT NULL DEFAULT '',
  post_id int(11) NOT NULL DEFAULT '0',
  status varchar(20) NOT NULL DEFAULT '',
  status_updated_at datetime DEFAULT NULL,
  created_at datetime DEFAULT NULL,
  requester_id int(11) NOT NULL DEFAULT '0',
  accepted_by int(11) NOT NULL DEFAULT '0',
  accepted_by_user_type varchar(50) NOT NULL DEFAULT '',
  wsfy_cost_by_duration varchar(20) NOT NULL DEFAULT '',
  wsfy_attached_file varchar(255) NOT NULL DEFAULT '',
  started_at datetime DEFAULT NULL,
  finished_at datetime DEFAULT NULL,
  PRIMARY KEY (request_id),
  KEY post_id (post_id),
  KEY requester_id (requester_id),
  KEY accepted_by (accepted_by)  
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";  
	dbDelta( $sql );
  
  $table_name = $wpdb->prefix . 'wsfy_cost_by_duration';
  $sql = "CREATE TABLE $table_name (
  cbd_id int(11) NOT NULL AUTO_INCREMENT,
  appointment_type varchar(30) NOT NULL DEFAULT '',
  duration tinyint(4) NOT NULL DEFAULT '0',
  cost varchar(20) NOT NULL DEFAULT '',
  PRIMARY KEY (cbd_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
  dbDelta( $sql );
  
  $wpdb->get_results("INSERT INTO $table_name (appointment_type, duration, cost) VALUES 
  ('medical', 2, '150'),
  ('medical', 3, '300'),
  ('medical', 6, '600'), 
  ('legal', 2, '0'), 
  ('legal', 3, '400'),
  ('legal', 6, '800'),
  ('insurance', 2, '150'),
  ('insurance', 3, '400'),
  ('insurance', 6, '800'),
  ('agency', 2, '150'),
  ('agency', 3, '250'),
  ('agency', 6, '500'),
  ('other', 2, 'Contact for cost'),
  ('other', 3, 'Contact for cost'),
  ('other', 6, 'Contact for cost');");
  
  $table_name = $wpdb->prefix . 'wsfy_appointment_payouts_by_duration';
  $sql = "CREATE TABLE $table_name (
  payout_id int(11) NOT NULL AUTO_INCREMENT,
  user_type varchar(50) NOT NULL DEFAULT '',
  duration tinyint(4) NOT NULL DEFAULT '0',
  payout int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (payout_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
  dbDelta( $sql );
  $wpdb->get_results("INSERT INTO $table_name (user_type, duration, payout) VALUES 
  ('qualified_interpreter', 2, 50),
  ('qualified_interpreter', 3, 75),
  ('qualified_interpreter', 6, 150),
  ('medical_interpreter', 2, 90),
  ('medical_interpreter', 3, 120),
  ('medical_interpreter', 6, 240),
  ('administrative_hearing_interpreter', 2, 90),
  ('administrative_hearing_interpreter', 3, 175),
  ('administrative_hearing_interpreter', 6, 330),
  ('court_interpreter', 2, 90),
  ('court_interpreter', 3, 175),
  ('court_interpreter', 6, 330),
  ('federal_interpreter', 2, 0),
  ('federal_interpreter', 3, 0),
  ('federal_interpreter', 6, 0);");  

  $table_name = $wpdb->prefix . 'wsfy_rejected_requests';
  $sql = "CREATE TABLE $table_name (
  rr_id int(11) NOT NULL AUTO_INCREMENT,
  request_id int(11) NOT NULL DEFAULT '0',
  interpreter_id int(11) NOT NULL DEFAULT '0',
  rejected_at datetime DEFAULT NULL,
  rejected_by int(11) NOT NULL DEFAULT '0',
  rejected_text varchar(999) NOT NULL DEFAULT '',
  PRIMARY KEY (rr_id),
  KEY request_id_interpreter_id (request_id,interpreter_id),
  KEY request_id (request_id),
  KEY interpreter_id (interpreter_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
  dbDelta( $sql ); 
  
  $table_name = $wpdb->prefix . 'wsfy_denied_requests';
  $sql = "CREATE TABLE $table_name (
  dr_id int(11) NOT NULL AUTO_INCREMENT,
  request_id int(11) NOT NULL DEFAULT '0',
  interpreter_id int(11) NOT NULL DEFAULT '0',
  denied_at datetime DEFAULT NULL,
  PRIMARY KEY (dr_id),
  KEY request_id (request_id),
  KEY interpreter_id (interpreter_id),
  KEY request_id_interpreter_ud (request_id,interpreter_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
  dbDelta( $sql ); 
  
  $table_name = $wpdb->prefix . 'wsfy_canceled_requests'; 
  $sql = "CREATE TABLE $table_name (
  cr_id int(11) NOT NULL AUTO_INCREMENT,
  request_id int(11) NOT NULL DEFAULT '0',
  interpreter_id int(11) NOT NULL DEFAULT '0',
  canceled_at datetime DEFAULT NULL,
  canceled_text varchar(999) NOT NULL DEFAULT '',
  PRIMARY KEY (cr_id),
  KEY request_id (request_id),
  KEY interpreter_id (interpreter_id),
  KEY request_id_interpreter_id (request_id,interpreter_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
  dbDelta( $sql );
  
  $table_name = $wpdb->prefix . 'wsfy_cost_by_requester_type'; 
  $sql = "CREATE TABLE $table_name (
  cbrt_id int(11) NOT NULL AUTO_INCREMENT,
  appointment_type varchar(30) NOT NULL DEFAULT '',
  appointment_sub_type varchar(100) NOT NULL DEFAULT '',
  requester_type varchar(35) NOT NULL DEFAULT '',
  cost varchar(20) NOT NULL DEFAULT '',
  PRIMARY KEY (`cbrt_id`),
  UNIQUE KEY `cbrt_id` (`cbrt_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
  dbDelta( $sql );
  $wpdb->get_results("INSERT INTO $table_name (appointment_type, appointment_sub_type, requester_type, cost) VALUES 
  ('workers-comp', 'deposition preps', 'legal_requester', 'contact for cost'),
  ('workers-comp', 'half day deposition', 'legal_requester', '400'),
  ('workers-comp', 'full day deposition', 'legal_requester', '800'),
  ('workers-comp', 'transcript read backs', 'legal_requester', 'contact for cost'),
  ('workers-comp', 'settlements and stipulations', 'legal_requester', 'contact for cost'),
  ('workers-comp', 'telephonic settlements and stipulations', 'legal_requester', 'contact for cost'),
  ('workers-comp', 'worker`s compensation board appearances', 'legal_requester', 'contact for cost'),
  ('workers-comp', 'deposition preps', 'insurance_requester', '165'),
  ('workers-comp', 'half day deposition', 'insurance_requester', '400'),
  ('workers-comp', 'full day deposition', 'insurance_requester', '800'),
  ('workers-comp', 'transcript read backs', 'insurance_requester', '275'),
  ('workers-comp', 'settlements and stipulations', 'insurance_requester', '275'),
  ('workers-comp', 'telephonic settlements and stipulations', 'insurance_requester', '275'),
  ('workers-comp', 'worker`s compensation board appearances', 'insurance_requester', '195'),
  ('workers-comp', 'deposition preps', 'agency_requester', '130'),
  ('workers-comp', 'half day deposition', 'agency_requester', '250'),
  ('workers-comp', 'full day deposition', 'agency_requester', '500'),
  ('workers-comp', 'transcript read backs', 'agency_requester', '150'),
  ('workers-comp', 'settlements and stipulations', 'agency_requester', '150'),
  ('workers-comp', 'telephonic settlements and stipulations', 'agency_requester', '120'),
  ('workers-comp', 'worker`s compensation board appearances', 'agency_requester', '120'),
  ('workers-comp', 'deposition preps', 'medical_requester', '165'),
  ('workers-comp', 'half day deposition', 'medical_requester', '400'),
  ('workers-comp', 'full day deposition', 'medical_requester', '800'),
  ('workers-comp', 'transcript read backs', 'medical_requester', '275'),
  ('workers-comp', 'settlements and stipulations', 'medical_requester', '275'),
  ('workers-comp', 'telephonic settlements and stipulations', 'medical_requester', '275'),
  ('workers-comp', 'worker`s compensation board appearances', 'medical_requester', '195');");
}
register_activation_hook( __FILE__, 'create_tables' );
register_activation_hook(__FILE__, 'wsfy_activation');

register_deactivation_hook(__FILE__, 'wsfy_deactivation');
 
function wsfy_activation()
{
  global $wsfy_user_types_requesters, $wsfy_user_types_interpreters;
  
  $capabilities = [
    'read' => true, // true allows this capability
    'edit_posts' => true, // Allows user to edit their own posts
    'edit_pages' => true, // Allows user to edit pages
    'edit_others_posts' => true, // Allows user to edit others posts not just their own
    'create_posts' => true, // Allows user to create new posts
    'manage_categories' => true, // Allows user to manage post categories
    'publish_posts' => true, // Allows the user to publish, otherwise posts stays in draft mode
    'edit_themes' => false, // false denies this capability. User can’t edit your theme
    'install_plugins' => false, // User cant add new plugins
    'update_plugin' => false, // User can’t update any plugins
    'update_core' => false // user cant perform core updates  
  ];
  foreach($wsfy_user_types_requesters as $role => $type) {
    add_role( $role, $type, $capabilities );  
  }

  foreach($wsfy_user_types_interpreters as $role => $type) {
    add_role( $role, $type, $capabilities );  
  }  
}
 
function wsfy_deactivation()
{
  global 
    $wsfy_user_types_requesters,
    $wsfy_user_types_interpreters,
    $wpdb;
  
  foreach($wsfy_user_types_requesters as $role => $type) {
    remove_role($role);  
  }

  foreach($wsfy_user_types_interpreters as $role => $type) {
    remove_role($role);  
  } 
  
  $wsfy_table = [
    'wsfy_requests',
    'wsfy_cost_by_duration',
    'wsfy_appointment_payouts_by_duration',
    'wsfy_rejected_requests',
    'wsfy_denied_requests',
    'wsfy_canceled_requests',
    'wsfy_cost_by_requester_type'
  ];
  foreach($wsfy_table as $table_name) {
    $wpdb->query('DROP TABLE IF EXISTS '.$wpdb->prefix.$table_name);  
  }   
}


function wsfy_scripts()
{
  global
    $wsfy_cost_by_duration,
    $wsfy_cost_by_requster_type,
    $wsfy_request_appointment_sub_types;

  wp_enqueue_style( 'custom-register-css', WSFY_URL . '/css/register.css',[], WSFY_VERSION);
  wp_register_script('custom-register-js', WSFY_URL . '/js/register.js', ['jquery'], WSFY_VERSION, true);
  wp_localize_script('custom-register-js', 'wsfy_data', 
    [
      'admin_url' => get_admin_url(),
      'site_url' => site_url(),
      'cost_by_duration' => $wsfy_cost_by_duration,
      'cost_by_requster_type' => $wsfy_cost_by_requster_type,
      'request_appointment_sub_types' => $wsfy_request_appointment_sub_types,
      'request_service_url' => WSFY_REQUEST_SERVICE_URL,
      'user_type' => get_user_role()
    ]
  );
  wp_enqueue_script( 'custom-register-js');
  
  wp_enqueue_style('wsfy-main-css', WSFY_URL . '/css/main.css',[], WSFY_VERSION);
  wp_enqueue_script('wsfy-main-js', WSFY_URL . '/js/main.js', ['jquery'], WSFY_VERSION, true);
  
  wp_enqueue_style( 'date-time-picker-css', WSFY_URL . '/css/datetimepicker.min.css',[], WSFY_VERSION);
  wp_enqueue_script('date-time-picker-js', WSFY_URL . '/js/datetimepicker.min.js', ['jquery'], WSFY_VERSION, true);
  wp_enqueue_script('jquery-ui-datepicker');
  wp_enqueue_style( 'jquery-ui-smoothness',
    wpcf7_plugin_url( 'includes/js/jquery-ui/themes/smoothness/jquery-ui.min.css' ), array(), '1.10.3', 'screen' );    
    
}
add_action( 'wp_enqueue_scripts', 'wsfy_scripts' );

function getRolesOptions($roles, $user_type = '')
{
  $html = '';
  $html .= '
    <div id="pnluser_type" class="form-group">
  	<p>
  		<label for="user_type" class="control-label">'.$user_type.' Type*<br />
      <span class="dropdown-select">
        <select class="form-control user-data" name="user_type" id="user_type" onchange="$(\'#pnlRoleFields\').attr(\'class\',this.value);" required>
      
        ';
        foreach($roles as $role => $type) {
          $html.= '<option value="'.$role.'">'.$type.'</option>';
        }
  $html .= '</select>
      </span>
      </label>  		
  	</p>
    </div>';  
  return $html;
}

function get_control_by_type($control_data)
{
  $cntrl = '';
  switch($control_data['type']) {
    case 'select':
      $cntrl .= '<select name="'.$control_data['name'].'" id="'.$control_data['name'].'" class="form-control user-data" '.($control_data['multiple']?' multiple ':'').'>';
      foreach($control_data['options'] as $value) {
        $cntrl .= '<option value="'.$value.'">'.$value.'</option>';
      }
      $cntrl .= '</select>';
    break;
    case 'memo':
      $cntrl .= '<textarea name="'.$control_data['name'].'" id="'.$control_data['name'].'" class="form-control user-data" ></textarea>';
    break;
    case 'password':
      $cntrl .= '<input type="password" name="'.$control_data['name'].'" id="'.$control_data['name'].'" class="form-control user-data" '.($control_data['isRequired']?'required':'').' />'; 
    break;
    default:
      $cntrl .= '<input type="text" name="'.$control_data['name'].'" id="'.$control_data['name'].'" class="form-control user-data" '.($control_data['isRequired']?'required':'').' />'; 
  }
  return $cntrl;
}

function getRolesFieldsHTML($fields)
{
  $html = ''; $style='';
  foreach($fields as $data) {
    $html .= 
  	'
    <div id="pnl'.$data['name'].'" class="form-group">
      <p class="p'.$data['name'].(isset($data['roles'])?' hidden':'').'">
    		<label for="'.$data['name'].'" class="control-label">'.$data['label'].($data['isRequired']?'*':'').'<br />
    		'.get_control_by_type($data).'
        </label>
    	</p>
    </div>';
    
    if(isset($data['roles'])) {
      foreach($data['roles'] as $role) {
       $style .= '.'.$role.' .p'.$data['name'].' {display: inline !important;}';
      } 
    }
  }   
  return $html.($style?'<style>'.$style.'</style>':'');
}

function ajax_get_requester_fields()
{
  global $wsfy_user_types_requesters, 
         $wsfy_requester_fields;
         
  $roles = array_intersect_key($wsfy_user_types_requesters, get_editable_roles());  
  
  $html .= '<h3  class="register">Requester Registration</h3>'; 
  $html .= getRolesOptions($roles, 'Requester');
  $html .= getRolesFieldsHTML($wsfy_requester_fields);
     
  $response = ['success' => true, 'html' => $html];
  echo json_encode($response);
  die();
}
add_action('wp_ajax_get_requester_fields', 'ajax_get_requester_fields');
add_action('wp_ajax_nopriv_get_requester_fields', 'ajax_get_requester_fields' );

function ajax_get_interpreter_fields()
{
  global $wsfy_user_types_interpreters,
         $wsfy_interpreter_fields;
         
  $roles = array_intersect_key($wsfy_user_types_interpreters, get_editable_roles());  
  
  $html .= '<h3  class="register">Interpreter Registration</h3>';  
  $html .= getRolesOptions($roles, 'Interpreter');
  $html .= getRolesFieldsHTML($wsfy_interpreter_fields);
     
  $response = ['success' => true, 'html' => $html];
  echo json_encode($response);
  die();
}
add_action('wp_ajax_get_interpreter_fields', 'ajax_get_interpreter_fields');
add_action('wp_ajax_nopriv_get_interpreter_fields', 'ajax_get_interpreter_fields' );

function validate_profile_data(&$data)
{
  global
     $wsfy_user_types_requesters, 
     $wsfy_user_types_interpreters,
     $wsfy_requester_fields,
     $wsfy_interpreter_fields,
     $wsfy_post_statuses;  
  
  $errors = [];
  
  if(!$data['user_type']) {
    $errors['user_type'] = 'Appointment Type is required.';
  } else {
    $user_types = array_merge($wsfy_user_types_requesters, $wsfy_user_types_interpreters);
    if(!isset($user_types[$data['user_type']])) {
      $errors['user_type'] = 'Unknown Appointment Type';
    }
  }
   
  $userFields = 'wsfy_'.strtolower($data['i_am_a']).'_fields';
  foreach($$userFields as $f_data) {
    if(!$f_data['isRequired']) {
      continue;
    }
    if(!$data[$f_data['name']]) {
      $errors[$f_data['name']] = $f_data['label'].' is required.';
    }  
  }
    
  foreach($data as $key => $value) {
    if(is_array($value)) {
      continue;
    }
    $data[$key] = sanitize_text_field($value);
  }
  
  if($data['password'] != $data['confirm_password']){
    $errors['password'] = 'Passwords don\'t match';  
  }
  
  if(username_exists($data['user_login'])) {
    $errors['user_login'] = 'Username is already in use'; 
  }
  
  if(!validate_username($data ['user_login'])) {
    $errors['user_login'] = 'Invalid username';
  }
  
  if(empty($data ['user_login'])) {
    $errors['user_login'] = 'Username is empty';
  }
  
  if(!is_email($data ['email'])) {
    $errors['email'] = 'Invalid email';  
  }
  
  if(email_exists($data ['email'])) {
    $errors['email'] = 'Email is already in use'; 
  }  
  
  return $errors; 
}

function ajax_wsfy_register()
{
  $userData = $_POST['form_data'];
  $userData['user_login'] = 'User'.md5($userData ['email']);
   
  if($errors = validate_profile_data($userData)) {
    $response = ['success' => false, 'errors' => $errors];
    echo json_encode($response);
    die();      
  }

  #$userData['user_pass'] = wp_generate_password();
  $user_id = wp_insert_user([
      'user_login'		=> $userData ['user_login'],
      'user_pass'	 		=> $userData ['password'],
      'user_email'		=> $userData ['email'],
      'first_name'		=> $userData ['first_name'],
      'last_name'			=> $userData ['last_name'],
      'role'          => $userData['user_type'],
      'user_registered'	=> date('Y-m-d H:i:s')]);
      
  register_user_meta($user_id, $userData); 
  
  signup_notifications($userData);
  user_registered_notify_admin($userData);

  $msg = '<p>You are successfully registered.</p>
          <p>Email with the credentials has been sent.</p>';
          
  $response = ['success' => true, 'msg' => $msg,'redirect_to' => site_url()];
  echo json_encode($response);
  die();
}
add_action('wp_ajax_wsfy_register', 'ajax_wsfy_register');
add_action('wp_ajax_nopriv_wsfy_register', 'ajax_wsfy_register' );

function register_user_meta($user_id, $data)
{
  global 
    $wsfy_requester_fields,
    $wsfy_interpreter_fields;

  unset($data['confirm_password']);
  $userFields = 'wsfy_'.strtolower($data['i_am_a']).'_fields';
  foreach($$userFields as $f_data) {
    if(!$data[$f_data['name']]) {
      continue;
    }
    if(is_array($data[$f_data['name']])) {
      $value = json_encode($data[$f_data['name']]);
    } else {
      $value = $data[$f_data['name']];
    }
    update_user_meta(
      $user_id,
      $f_data['name'],
      $value);  
  }
}

function user_registered_notify_admin($registered_user)
{
  global 
    $wsfy_user_types_interpreters, 
    $wsfy_user_types_requesters,
    $wsfy_interpreter_fields,
    $wsfy_requester_fields;
  
  $roles = array_merge($wsfy_user_types_interpreters, $wsfy_user_types_requesters);
  $admin_email = get_option('admin_email');
  $siteTitle = get_bloginfo( 'name' );
  $body = '
    <p>Hello!</p>
    <p>On the <a href="'.site_url().'">'.$siteTitle.'</a> was registered a new '.$roles[$registered_user['user_type']].':</p>';
  $user_data_fields = array_merge($wsfy_interpreter_fields, $wsfy_requester_fields);   
  foreach($user_data_fields as $data) {
    if(!$registered_user[$data['name']]) {
      continue;
    }
    $body .= '<p>'.$data['label'].': '.$registered_user[$data['name']].'</p>';
  }  
  $body .= '<br/><p>Have a nice day!</p>';
  
  add_filter('wp_mail_content_type', 'wp_mail_set_html_content_type');    
  
  wp_mail(
      $admin_email,
      'New user registration',
      $body);
  
  remove_filter('wp_mail_content_type', 'wp_mail_set_html_content_type');  
}

function signup_notifications($data)
{
  $siteTitle = get_bloginfo('name');
  $body = '
  <h2 style="font-size: 14px;">Welcome to '.$siteTitle.'</h2>
  <br/>
  <p style="font-size: 12px;">You can sign in using your credentials:</p> 
  <p style="font-size: 12px;">Email: '.$data['email'].'</p> 
  <p style="font-size: 12px;">Password: '.$data['password'].'</p>
  <p style="font-size: 12px;"><a href="'.site_url().'">'.$siteTitle.'</a></p>
  ';
    
    add_filter('wp_mail_content_type', 'wp_mail_set_html_content_type');    
    
    wp_mail(
        $data['email'],
        'Registration for '.$siteTitle,
        $body);
    
    remove_filter('wp_mail_content_type', 'wp_mail_set_html_content_type');
}

function wp_mail_set_html_content_type() {
	return 'text/html';
}

function success_msg_html($data)
{
  if($data['post_id']) {
    $html = 'Request has been updated succesfully.';
    return $html;  
  }
  
  $data = array_map(function($v){
    return esc_textarea($v);  
  }, $data);
  
  $location = array_diff([
    $data['wsfy_address1'], 
    $data['wsfy_address2'], 
    $data['wsfy_city'], 
    $data['wsfy_county'],
    $data['wsfy_state'],
    $data['wsfy_zip']
  ], ['']);
  
  $html = '
    <p>Almost there!</p><br/>
    <p>We have sent you request. Sit back, relax, and we will notify you when we found the right fit.</p><br/>
    <p>Request details:</p>
    <p><b>Date:</b> '.date('m/d/Y', strtotime($data['wsfy_date'])).'</p>
    <p><b>Time:</b> '.$data['wsfy_start_time'].'</p>
    <p><b>Duration:</b> '.($data['wsfy_duration']).'hrs</p>
    <p><b>Location:</b> '.implode(' ', $location).'</p>
    <p><b>Translate Service:</b> '.$data['wsfy_translate_from'].' to '.$data['wsfy_translate_to'].'</p>
  ';
  return $html;
}

function ajax_wsfy_request_change_status()
{
  global $wpdb;
  $data = $_POST['form_data'];
  
  $request = is_post_editable($data['post_id']);
  
  $values['status'] = ($request->post_status == WSFY_POST_STATUS_CANCELED? WSFY_POST_STATUS_PUBLISHED: WSFY_POST_STATUS_CANCELED);
  $values['status_updated_at'] = date('Y-m-d H:i:s');
  $wpdb->update( $wpdb->prefix.'wsfy_requests', $values, ['post_id'=>$request->ID]);

  $values = [
    'ID' => $request->ID,
    'post_status' => $values['status']
  ];
  wp_update_post($values);
  
  $response = [
    'success' => true,
    'msg' => success_msg_html($data), 
  ];
  $response['redirect_to'] = WSFY_REQUESTER_DASHBOARD_URL;  
  
  echo json_encode($response);
  die();  
}
add_action('wp_ajax_wsfy_request_change_status', 'ajax_wsfy_request_change_status');

function is_post_editable($post_id)
{
    $request = get_post($post_id);
    
    if($request->post_author != get_current_user_id() || $request->post_type != WSFY_POST_TYPE) {
      $response = ['success' => false, 'error' => 'You have no permissions to edit this request.'];
      echo json_encode($response); 
      die();     
    }
    return $request;  
}

function validate_request_data($data)
{
  global
    $wsfy_request_required_fields,
    $wsfy_request_appointment_sub_types;
  
  $errors = [];
  foreach($wsfy_request_required_fields as $field => $label) {
    if(!$data[$field]) {
      $errors[$field] = $label.' is required';
    }
  }
  
  if($data['wsfy_date']) {
    $now = time();
    $date = strtotime($data['wsfy_date'].' 23:59:59');
    $diff = $now - $date;
    if($diff > 0) {
      $errors['wsfy_date'] = 'Date should be in the future.';
    }
  } 
  
  if($wsfy_request_appointment_sub_types[$data['wsfy_appointment_type']]) {
    unset($errors['wsfy_duration']);     
    if(!$data['wsfy_appointment_sub_type']) {
      $errors['wsfy_appointment_sub_type'] = 'Appointment type is required.';
      unset($errors['wsfy_duration']); 
    }  
  } else {
    unset($errors['wsfy_appointment_sub_type']);      
  } 
  return $errors;
}

function ajax_wsfy_request_translator()
{
  global $request_fields, $wpdb;
  
  $data = $_POST['form_data'];
  
  if($data['post_id']) {
    $request = is_post_editable($data['post_id']);
    $postData['ID'] = $request->ID;
  }

  if($errors = validate_request_data($data)) {
    $response = ['success' => false, 'errors' => $errors];
    echo json_encode($response);
    die();      
  }  

  if($data['wsfy_attached_file']) {
    $data['wsfy_attached_file'] = str_replace(site_url(), '', $data['wsfy_attached_file']);  
  }
  
  $postData['post_author'] = get_current_user_id();
  $postData['post_status'] = WSFY_POST_STATUS_PUBLISHED;
  $postData['post_type'] = WSFY_POST_TYPE;
  
  if($postID = wp_insert_post($postData)) {
    foreach($request_fields as $field) {
      if($request->ID) {
        update_post_meta($postID, $field, $data[$field]);  
      } else {
        add_post_meta($postID, $field, $data[$field]);
      }
    }
    $values = array_intersect_key($data, array_flip($request_fields));
    array_walk($values, function(&$item, $key){
      global $wpdb;
      $item = $wpdb->prepare($item);
    });
    
    
    $values['post_id'] = $postID;
    $values['status_updated_at'] = date('Y-m-d H:i:s');
    $values['requester_id'] = $postData['post_author'];
    
    if($request->ID) {
      $wpdb->update( $wpdb->prefix.'wsfy_requests', $values, ['post_id'=>$request->ID]);
    } else {
      $values['created_at'] = date('Y-m-d H:i:s');
      $values['status'] = WSFY_POST_STATUS_PUBLISHED;
      $wpdb->insert($wpdb->prefix.'wsfy_requests', $values);
      change_request_status_notify('request_created_admin', (object)$values);
      change_request_status_notify('request_created_requester', (object)$values);
    }
    $response = [
      'success' => true,
      'msg' => success_msg_html($data), 
    ];
    if(!$data['post_id']){
      $response['redirect_to'] = WSFY_REQUESTER_DASHBOARD_URL;# . '?request='.$wpdb->insert_id;
    }
    
    echo json_encode($response);
    die();
  }
  $response = ['success' => false];
  echo json_encode($response);
  die();  
}
add_action('wp_ajax_wsfy_request_translator', 'ajax_wsfy_request_translator');


function get_data_table_options($data_table__columns)
{
  $tableData = $_REQUEST;
  $order = ' ORDER BY '.array_keys($data_table__columns)[(int)$tableData['order'][0]['column']].' '.$tableData['order'][0]['dir'];
  $limit = ' LIMIT '.$tableData['start'].', '.$tableData['length'];
    
  return [$order, $limit];
}

function get_service_dashboard_aData()
{
  global 
    $wpdb,
    $wsfy_service_dashboard_columns;
  

  list($order, $limit) = get_data_table_options($wsfy_service_dashboard_columns);
  
  $sql = 'SELECT 
  wsfy_appointment_type,
  CONCAT(wsfy_translate_from, " to ", wsfy_translate_to) AS wsfy_translation_type,
  wsfy_date,
  status,
  request_id,
  wsfy_appointment_sub_type
  FROM '.$wpdb->prefix.'wsfy_requests WHERE requester_id='.get_current_user_id().$order.$limit;
  
  $data = $wpdb->get_results($sql);
  return $data;
}

function ajax_wsfy_service_dashboard_data()
{
  global $wpdb;
  
  $sql = 'SELECT COUNT(*) FROM '.$wpdb->prefix.'wsfy_requests WHERE requester_id='.get_current_user_id();
 
  $iTotalRecords = $iTotalDisplayRecords = $wpdb->get_col($sql)[0];
  
  $aaData = get_service_dashboard_adata();
  
  $output = array(
			'sEcho' => intval($_GET['sEcho']),
			'iTotalRecords' => $iTotalRecords,
			'iTotalDisplayRecords' =>$iTotalDisplayRecords,
			'aaData' => array()
	);
  $output['aaData'] = $aaData;
  echo json_encode($output); 
  die();
}
add_action('wp_ajax_wsfy_service_dashboard_data', 'ajax_wsfy_service_dashboard_data');

function get_user_role() {
    global $current_user;

    $user_roles = $current_user->roles;
    $user_role = array_shift($user_roles);

    return $user_role;
}

function ajax_wsfy_export_requests()
{
  global
    $wpdb,
    $wsfy_admin_dashboard_columns;
  
  if(get_user_role() != WSFY_ROLE_ADMIN) {
    $response = ['success' => false, 'masg' => 'You have no access to export data.'];
    echo json_encode($response);     
    die();
  }
  
  list($order, $limit) = get_data_table_options($wsfy_admin_dashboard_columns);
  
  $whereAND = [];
  if($_GET['request_type'] == 'pending_requests'){
    $whereAND[] = 'status = "'.WSFY_POST_STATUS_ACCEPTED.'"';
    $whereAND[] = 'accepted_by <> 0';
  }
  
  $sql = 'SELECT
    r.request_id,  
    ur.display_name AS requester,
    ui.display_name AS interpreter,
    IF(wsfy_appointment_sub_type<>"", CONCAT(wsfy_appointment_type,"/",wsfy_appointment_sub_type), wsfy_appointment_type) AS appointment_type, 
    CONCAT(wsfy_translate_from, " to ", wsfy_translate_to) AS wsfy_translation_type,  
    wsfy_date,    
    r.status,    
    CONCAT(wsfy_county, ", ", wsfy_state) AS wsfy_location,
    wsfy_start_time AS wsfy_time,
    CONCAT(wsfy_duration, "hrs") AS wsfy_length,
    r.wsfy_cost_by_duration AS wsfy_cost,
    (SELECT payout FROM '.$wpdb->prefix.'wsfy_appointment_payouts_by_duration WHERE user_type LIKE r.accepted_by_user_type AND duration = r.wsfy_duration) AS wsfy_pay_rate
    FROM '.$wpdb->prefix.'wsfy_requests AS r 
    LEFT JOIN '.$wpdb->prefix.'users AS ur ON ur.ID = r.requester_id
    LEFT JOIN '.$wpdb->prefix.'users AS ui ON ui.ID = r.accepted_by 
    '.($whereAND? ' WHERE '.implode($whereAND,' AND '): '').$order; 
 
  $head = [
    'Order #',
    'Requester Name',
    'Interpreter Name',
    'Appointment Type',
    'Translation Type',
    'Date',
    'Status',
    'Location',
    'Time',
    'Length',
    'Cost',
    'Payout'
  ];
  $rows = $wpdb->get_results($sql);
  
  $fname = 'exports/export_'.date('Y-m-d-H-i-s').'.csv';
  
  $f = fopen(WSFY_DIR.$fname, 'w');
  fputcsv($f, $head);
  foreach($rows as $row) {
    fputcsv($f, [
      $row->request_id,
      $row->requester,
      $row->interpreter,
      $row->appointment_type,
      $row->wsfy_translation_type,
      $row->wsfy_date,
      $row->status,
      $row->wsfy_location,
      $row->wsfy_time,
      $row->wsfy_length,      
      $row->wsfy_cost,
      $row->wsfy_pay_rate,
    ], ',');
  }
  fclose($f);
    
  $response = ['success' => true, 'file_url' => WSFY_URL.$fname];
  echo json_encode($response); 
  die();   
}
add_action('wp_ajax_wsfy_export_requests', 'ajax_wsfy_export_requests');

function construct_requests_widget()
{
  global
    $wpdb,
    $wsfy_interpreters_allowed_appointment_types,
    $wsfy_translator_dashboard_columns,
    $wsfy_admin_dashboard_columns;

  $tableData = $_REQUEST;
  $role = get_user_role();
  $join = '';
  
  $wsfy_location = 'CONCAT(wsfy_county, ", ", wsfy_state)';  
  switch($tableData['search']['value']) {
    case 'canceled_requests': 
      $join = 'JOIN '.$wpdb->prefix.'wsfy_canceled_requests AS cr ON cr.request_id = r.request_id ';
      $whereAND[] = 'cr.interpreter_id = '.get_current_user_id();
    break;    
    case 'rejected_requests': 
      #$whereAND[] = 'status = "'.WSFY_POST_STATUS_PUBLISHED.'"';
      #$whereAND[] = 'accepted_by = 0';
      $join = 'JOIN '.$wpdb->prefix.'wsfy_rejected_requests AS rr ON rr.request_id = r.request_id ';
      $whereAND[] = 'rr.interpreter_id = '.get_current_user_id();
    break;    
    case 'my_requests': 
      $whereAND[] = 'status = "'.WSFY_POST_STATUS_APPROVED.'"';
      $whereAND[] = 'accepted_by = '.get_current_user_id();
      $wsfy_location = 'CONCAT(wsfy_address1," ",wsfy_address2," ",wsfy_city," ",wsfy_county," ",wsfy_state," ",wsfy_zip)';
    break;
    case 'pending_requests':
      $whereAND[] = 'status = "'.WSFY_POST_STATUS_ACCEPTED.'"';
      if($role != WSFY_ROLE_ADMIN) { 
        $whereAND[] = 'accepted_by = '.get_current_user_id();
      } else {
        $whereAND[] = 'accepted_by <> 0';
      }
    break;
    case 'all_requests':
    break;    
    default:
      $whereAND[] = 'status = "'.WSFY_POST_STATUS_PUBLISHED.'"';
      $whereAND[] = 'accepted_by = 0';
      $whereAND[] = '(SELECT COUNT(*) FROM '.$wpdb->prefix.'wsfy_rejected_requests WHERE r.request_id = request_id AND interpreter_id = "'.get_current_user_id().'") = 0';
      $whereAND[] = '(SELECT COUNT(*) FROM '.$wpdb->prefix.'wsfy_denied_requests WHERE r.request_id = request_id AND interpreter_id = "'.get_current_user_id().'") = 0';
      $whereAND[] = '(SELECT COUNT(*) FROM '.$wpdb->prefix.'wsfy_canceled_requests WHERE r.request_id = request_id AND interpreter_id = "'.get_current_user_id().'") = 0';
  }
  
  if($role != WSFY_ROLE_ADMIN) {
    $whereAND[] = 'wsfy_appointment_type IN '.'("'.implode('","',$wsfy_interpreters_allowed_appointment_types[$role]).'")';
    list($order, $limit) = get_data_table_options($wsfy_translator_dashboard_columns);
  } else {
    list($order, $limit) = get_data_table_options($wsfy_admin_dashboard_columns);
  }  
  
  $sql = 'SELECT COUNT(*) FROM '.$wpdb->prefix.'wsfy_requests AS r '.$join;
  if(!empty($whereAND)) {
    $sql .= ' WHERE '.implode(' AND ', $whereAND);
  }
  $iTotalRecords = $iTotalDisplayRecords = $wpdb->get_col($sql)[0];
  
  $sql = 'SELECT
    wsfy_appointment_type,
    CONCAT(wsfy_translate_from, " to ", wsfy_translate_to) AS wsfy_translation_type,
    wsfy_date,
    '.$wsfy_location.' AS wsfy_location,
    wsfy_start_time AS wsfy_time,
    CONCAT(wsfy_duration, "hrs") AS wsfy_length,
    (SELECT payout FROM '.$wpdb->prefix.'wsfy_appointment_payouts_by_duration WHERE user_type LIKE '.($role == WSFY_ROLE_ADMIN?'r.accepted_by_user_type':'"'.$role.'"').' AND duration = r.wsfy_duration) AS wsfy_pay_rate,
    r.wsfy_cost_by_duration AS wsfy_cost,
    wsfy_attached_file,
    post_id,
    r.status,
    TIMEDIFF(finished_at, started_at) AS duration,
    wsfy_appointment_sub_type,
    r.request_id
  FROM '.$wpdb->prefix.'wsfy_requests AS r '.$join;
  
  if(!empty($whereAND)) {
    $sql .= ' WHERE '.implode(' AND ', $whereAND);
  }
  $sql .= $order.$limit;
  
  $aaData = $wpdb->get_results($sql);
  
  $output = [
			'sEcho' => intval($_GET['sEcho']),
			'iTotalRecords' => $iTotalRecords,
			'iTotalDisplayRecords' =>$iTotalDisplayRecords,
			'aaData' => []
	];
  $output['aaData'] = $aaData;
  echo json_encode($output); 
  die();  
}

function ajax_wsfy_translator_available_requests()
{
  construct_requests_widget();
}
add_action('wp_ajax_wsfy_translator_available_requests', 'ajax_wsfy_translator_available_requests');

function ajax_wsfy_get_request_details()
{
  global
    $wpdb,
    $wsfy_user_types_interpreters;
    
  $post_id = $_POST['post_id'];
  
  if(!$post_id) {
    echo json_encode(['success' =>false, 'msg' => 'Request ID is required.']);
    die();  
  }
  
  $data = $wpdb->get_row($wpdb->prepare("
        SELECT r.*, rr.rejected_text, rr.rr_id, cr.cr_id, cr.canceled_text
        FROM {$wpdb->prefix}wsfy_requests AS r 
        LEFT JOIN {$wpdb->prefix}wsfy_rejected_requests AS rr ON rr.request_id = r.request_id AND rr.interpreter_id = ".get_current_user_id()." 
        LEFT JOIN {$wpdb->prefix}wsfy_canceled_requests AS cr ON cr.request_id = r.request_id AND cr.interpreter_id = ".get_current_user_id()." 
        WHERE post_id = %d", $post_id), ARRAY_A);
        
  $location = array_diff([
    $data['wsfy_address1'], 
    $data['wsfy_address2'], 
    $data['wsfy_city'], 
    $data['wsfy_county'],
    $data['wsfy_state'],
    $data['wsfy_zip']
  ], ['']);
  
  if($data['status'] == WSFY_POST_STATUS_APPROVED && $data['accepted_by'] == get_current_user_id()) {
    $wsfy_location = implode(' ', $location);
  } else {
    $wsfy_location = $data['wsfy_county'].' '.$data['wsfy_state'];
  }
  
  $is_interpreter = false;
  $role = get_user_role();
  $cost = $data['wsfy_cost_by_duration'];
  if($wsfy_user_types_interpreters[$role]) {
    $is_interpreter = true;
    $cost = $wpdb->get_var('SELECT payout FROM '.$wpdb->prefix.'wsfy_appointment_payouts_by_duration WHERE user_type = "'.$role.'" AND duration = '.(int)$data['wsfy_duration']);
  }

  $show_cancel_start_buttons = false; $show_finish_button = false; $hide_start_button = false;
  if($wsfy_user_types_interpreters[$role]) {
    $show_cancel_start_buttons = (
      $data['status'] == WSFY_POST_STATUS_APPROVED) && 
      $data['accepted_by'] == get_current_user_id() && 
      empty($data['started_at']);
    $show_finish_button = !empty($data['started_at']) && empty($data['finished_at']);
    
    $hide_start_button = date_create($data['wsfy_date']) > date_create(date('Y-m-d'));
  }
   
  $html = '
    <input id="accepted_post_id" type="hidden" value="'.$post_id.'" />
    <p><b>Date:</b> '.date('m/d/Y', strtotime($data['wsfy_date'])).'</p>
    <p><b>Duration:</b> '.($data['wsfy_duration']).'hrs</p>
    <p><b>Cost:</b> '.(is_numeric($cost)?'$':'').$cost.'</p>
    <p><b>Start Time:</b> '.$data['wsfy_start_time'].'</p>
    <p><b>Location:</b> '.$wsfy_location.'</p>
    <p><b>Translate Service:</b> '.$data['wsfy_translate_from'].' to '.$data['wsfy_translate_to'].'</p>'.
    
    ($data['status'] == WSFY_POST_STATUS_ACCEPTED ?'<p id="p_rejected_text" class="hidden"><b>Rejection reason:</b><textarea id="rejected_text" /></p>':'').
    ($show_cancel_start_buttons?'<p id="p_rejected_text" class="hidden"><b>Cancellation reason:</b><textarea id="rejected_text" /></p>':'').
    ($data['rejected_text']?'<p><b>Rejection reason:</b> '.$data['rejected_text'].'</p>':'').
    ($data['canceled_text']?'<p><b>Canceled reason:</b> '.$data['canceled_text'].'</p>':'');
    
  if($role == WSFY_ROLE_ADMIN) {
    $hide_buttons = ($data['status'] != WSFY_POST_STATUS_ACCEPTED);  
  } else {  
    $hide_buttons = (
      !empty($data['cr_id']) || 
      !empty($data['rr_id']) || 
      ($data['status'] == WSFY_POST_STATUS_ACCEPTED) ||
      ($data['accepted_by'])
    );
  }
  
  echo json_encode([
    'success' => true, 
    'html' => $html, 
    'hide_buttons' => $hide_buttons, 
    'show_cancel_start_buttons' => $show_cancel_start_buttons,
    'show_finish_button' => $show_finish_button,
    'hide_start_button' => $hide_start_button
    ]); 
  die();  
}
add_action('wp_ajax_wsfy_get_request_details', 'ajax_wsfy_get_request_details');

function change_request_status_notify($action, $request_data, $extra_params = [])
{
  global 
    $wsfy_user_types_interpreters, 
    $wsfy_user_types_requesters,
    $wsfy_interpreter_fields,
    $wsfy_requester_fields;
  
  $admin_email = get_option('admin_email');
  $siteTitle = get_bloginfo( 'name' );
  
  $requester = get_userdata($request_data->requester_id);
  $requester_email = $requester?$requester->email:'';
  
  $interpreter = get_userdata($request_data->accepted_by);
  $interpreter_email = $interpreter?$interpreter->email:'';

  $location = array_diff([
    $request_data->wsfy_address1, 
    $request_data->wsfy_address2, 
    $request_data->wsfy_city, 
    $request_data->wsfy_county,
    $request_data->wsfy_state,
    $request_data->wsfy_zip
  ], ['']);  
    
  $request_details = '
    <p>Request details:</p>
    <p>Date: '.date('m/d/Y', strtotime($request_data->wsfy_date)).'</p>
    <p>Time: '.$request_data->wsfy_start_time.'</p>
    <p>Duration: '.($request_data->wsfy_duration).'hrs</p>
    <p>Location: '.implode(' ', $location).'</p>
    <p>Translate Service: '.$request_data->wsfy_translate_from.' to '.$request_data->wsfy_translate_to.'</p>
  ';  

  switch($action) {
    case 'accept'://The interpreter accepted a request. Sends to the admin
    case 'accept_requester'://The interpreter accepted a request. Sends to the requester
      $subject = 'Request accepted';
      $body = '
        <p>Hello!</p>
        <p>On the <a href="'.site_url().'">'.$siteTitle.'</a> was accepted the request: </p>'
        .$request_details;
      $body .= '<br/><p>Have a nice day!</p>'; 
      $emails = ($action == 'accept'?[$admin_email]:[$requester_email]);   
    break;
    case 'approve_interpreter'://The admin approved an accepted request. Sends to the interpreter+
      $subject = 'Request approved';
      $body = '
        <p>Hello!</p>
        <p>Your previosly accepted request has been approved by the administrator.</p>'
        .$request_details;
      $body .= '<br/><p>Have a nice day!</p>'; 
      $emails = [$interpreter_email];   
    break;    
    case 'approve_requester'://The admin approved an accepted request. Sends to the requester+
      $subject = 'Request staffed';
      $body = '
        <p>Hello!</p>
        <p>Your request has been staffed.</p>'
        .$request_details;
      $body .= '<br/><p>Have a nice day!</p>'; 
      $emails = [$requester_email];   
    break;  
    case 'rejected_requester'://The admin rejected an accepted request. Sends to the interpreter+
      $subject = 'Request rejected';
      $body = '
        <p>Hello!</p>
        <p>Your request has been rejected. The rejection reason is:</p>
        <p>'.$extra_params['rejected_text'].':</p>'
        .$request_details;
      $body .= '<br/><p>Have a nice day!</p>'; 
      $emails = [$interpreter_email];   
    break;    
    case 'request_created_admin'://The requester submitted a request. Sends to the admin+
      $subject = 'New request submission';
      $body = '
        <p>Hello!</p>
        <p>From <a href="'.site_url().'">'.$siteTitle.'</a> a new one request was submitted.</p>';
      $body .= $request_details;
      $body .= '<br/><p>Have a nice day!</p>';    
      $emails = [$admin_email];   
    break;     
    case 'request_created_requester'://The requester submitted a request. Sends to the request owner+
      $subject = 'Request processed';
      $body = '
        <p>Hello, '.$requester->display_name.'!</p>
        <p>Your request has been processed.</p>'.$request_details;  
      $body .= '<br/><p>Have a nice day!</p>';    
      $emails = [$requester_email];   
    break;
    case 'canceled_translator':
      $subject = 'Request was canceled';
      $body = '
        <p>Hello!</p>
        <p>The request has been canceled by the translator.</p>'.$request_details;  
      $body .= '<br/><p>Have a nice day!</p>';    
      $emails = [$admin_email];   
    break;
    default: return;
  }
  add_filter('wp_mail_content_type', 'wp_mail_set_html_content_type');    
  wp_mail($emails, $subject, $body);
  remove_filter('wp_mail_content_type', 'wp_mail_set_html_content_type');     
}

function change_request_status()
{
  global 
    $wpdb,
    $wsfy_interpreters_allowed_appointment_types,
    $wsfy_user_types_interpreters;

  $post_id = $_POST['post_id'];

  if(!$post_id) {
    echo json_encode(['success' =>false, 'msg' => 'Request ID is required.']);
    die();  
  }

  $role = get_user_role();
  
  $request = $wpdb->get_row($wpdb->prepare("
        SELECT r.*, rr.rejected_text, rr.rr_id, rr.interpreter_id, dr.dr_id
        FROM {$wpdb->prefix}wsfy_requests AS r 
        LEFT JOIN {$wpdb->prefix}wsfy_rejected_requests AS rr ON rr.request_id = r.request_id AND rr.interpreter_id = ".get_current_user_id()."
        LEFT JOIN {$wpdb->prefix}wsfy_denied_requests AS dr ON dr.request_id = r.request_id AND dr.interpreter_id = ".get_current_user_id()."
        WHERE post_id = %d", $post_id));
  
  if($role != WSFY_ROLE_ADMIN && (array_search($request->wsfy_appointment_type, $wsfy_interpreters_allowed_appointment_types[$role]) === false)) {
    echo json_encode(['success' => false, 'msg' => 'You have no access to edit the request with appointment type "'.$request->wsfy_appointment_type.'".']);
    die();
  }
  
  switch($_POST['request_action']) {
    case 'cancel_request':
      if($request->status != WSFY_POST_STATUS_APPROVED || $request->accepted_by != get_current_user_id()) {
        echo json_encode(['success' => false, 'msg' => 'You can not cacnel the request.']);
        die();        
      }
      
      if($request->started_at) {
        echo json_encode(['success' => false, 'msg' => 'The request already started.']);
        die();        
      }
      
      $status = WSFY_POST_STATUS_PUBLISHED;
      $status_msg = 'canceled';
      $values['accepted_by'] = 0;
      $values['accepted_by_user_type'] = '';
 
      $canceled_text = strip_tags($_POST['rejected_text']);
      $wpdb->query($wpdb->prepare('INSERT INTO '.$wpdb->prefix.'wsfy_canceled_requests (request_id, interpreter_id, canceled_at, canceled_text) VALUES (%d, %d, %s, %s)',
        $request->request_id,
        $request->accepted_by,
        date('y-m-d H:i:s'),
        $canceled_text)
      );
      change_request_status_notify('canceled_translator', $request, ['canceled_text' => $canceled_text]);     
    break;
    
    case 'reject_request': 
      if($role != WSFY_ROLE_ADMIN) {
        echo json_encode(['success' => false, 'msg' => 'You have no access to reject the request.']);
        die();
      } 
      if($request->status != WSFY_POST_STATUS_ACCEPTED) {
        echo json_encode(['success' => false, 'msg' => 'The request is not acceptable.']);
        die();
      }         
      $status = WSFY_POST_STATUS_PUBLISHED;
      $status_msg = 'rejected'; 
      $values['accepted_by'] = 0;
      $values['accepted_by_user_type'] = '';
      $rejected_text = strip_tags($_POST['rejected_text']);
      
      $wpdb->query($wpdb->prepare('INSERT INTO '.$wpdb->prefix.'wsfy_rejected_requests (request_id, interpreter_id, rejected_at, rejected_by, rejected_text) VALUES (%d, %d, %s, %d, %s)',
        $request->request_id,
        $request->accepted_by,
        date('y-m-d H:i:s'),
        get_current_user_id(),
        $rejected_text)
      );
      change_request_status_notify('rejected_requester', $request, ['rejected_text' => $rejected_text]);
    break;
    case 'approve_request': 
      if($role != WSFY_ROLE_ADMIN) {
        echo json_encode(['success' => false, 'msg' => 'You have no access to approve the request.']);
        die();
      }
      if($request->status != WSFY_POST_STATUS_ACCEPTED) {
        echo json_encode(['success' => false, 'msg' => 'The request is not approvable.']);
        die();
      }
           
      $status = WSFY_POST_STATUS_APPROVED;
      $status_msg = 'approved';
      change_request_status_notify('approve_interpreter', $request); 
      change_request_status_notify('approve_requester', $request); 
    break;
    case 'deny_request': 
      if(!empty($request->dr_id)) {
        echo json_encode(['success' => false, 'msg' => 'The request was denied already.']);
        die();        
      }
      $status = WSFY_POST_STATUS_PUBLISHED;
      $status_msg = 'denied';
      $wpdb->query($wpdb->prepare('INSERT INTO '.$wpdb->prefix.'wsfy_denied_requests (request_id, interpreter_id, denied_at) VALUES (%d, %d, %s)',
        $request->request_id,
        get_current_user_id(),
        date('y-m-d H:i:s'))
      );  
    break; 
    case 'accept_request':
      if($request->rr_id) {
        echo json_encode(['success' => false, 'msg' => 'You can not accept the rejected request.']);
        die();        
      }
      if($request->status != WSFY_POST_STATUS_PUBLISHED || !empty($request->dr_id) || !empty($request->rr_id)) {
        echo json_encode(['success' => false, 'msg' => 'The request is not acceptable.']);
        die();
      } 
      
      $status = WSFY_POST_STATUS_ACCEPTED;
      $status_msg = 'accepted';
      $values['accepted_by'] = get_current_user_id();
      $values['accepted_by_user_type'] = $role;
      if($role != WSFY_ROLE_ADMIN) {
        change_request_status_notify('accept', $request);
        change_request_status_notify('accept_requester', $request);  
      } 
    break;
    case 'start_request':
      if($request->started_at) {
        echo json_encode(['success' => false, 'msg' => 'The request is already started.']);
        die();         
      }
      if($request->status != WSFY_POST_STATUS_APPROVED || empty($wsfy_user_types_interpreters[$role])) {
        echo json_encode(['success' => false, 'msg' => 'You can not start the request.']);
        die();
      }      
      $status = $request->status;
      $status_msg = 'started';
      $values['started_at'] = date('Y-m-d H:i:s');
    break;
    case 'finish_request':
      if(empty($request->started_at)) {
        echo json_encode(['success' => false, 'msg' => 'The request is not started yet.']);
        die();         
      }
      if($request->status != WSFY_POST_STATUS_APPROVED || empty($wsfy_user_types_interpreters[$role])) {
        echo json_encode(['success' => false, 'msg' => 'You can not finish the request.']);
        die();
      }      
      $status = $request->status;
      $status_msg = 'finished';
      $values['finished_at'] = date('Y-m-d H:i:s');
    break;
    
    default:
      echo json_encode(['success' => false, 'msg' => 'Action not found.']);
      die();         
  }
  
  $values['status'] = $status;
  $values['status_updated_at'] = date('Y-m-d H:i:s');
  
  if($wpdb->update($wpdb->prefix.'wsfy_requests', $values, ['post_id'=>$request->post_id]) === false) {
    echo json_encode(['success' => false, 'msg' => 'Error saving in the DB']);
    die();    
  }

  $values = [
    'ID' => $request->post_id,
    'post_status' => $values['status']
  ];
  wp_update_post($values);
  
  $response = [
    'success' => true,
    'msg' => 'The request was '.$status_msg.' successfully.', 
  ];
  
  echo json_encode($response);
  die();    
}

function ajax_wsfy_accept_request()
{
  change_request_status();
}
add_action('wp_ajax_wsfy_accept_request', 'ajax_wsfy_accept_request');

function ajax_wsfy_save_payout_settings()
{
  global $wpdb;
  
  if(get_user_role() != WSFY_ROLE_ADMIN) {
    echo json_encode(['success' =>false, 'msg' => 'You have no access to change payout settings.']); 
    die();
  }
  $data = $_POST['data'];

  $sql = '';
  $sql .= 'UPDATE '.$wpdb->prefix.'wsfy_appointment_payouts_by_duration SET payout = CASE';
  foreach($data as $row) {
    $sql .= ' WHEN user_type = '.$wpdb->prepare('%s', $row['user_type']).' AND duration = '.(int)$row['duration'].' THEN '.(int)$row['payout'];
  }
  $sql .= ' ELSE payout END';
  
  if($wpdb->query($sql)!==false) {
    $response = [
      'success' => true,
      'msg' => 'Payout settings has been updated successfully', 
    ];
    
    echo json_encode($response);
    die();    
  } else {
    echo json_encode(['success' =>false, 'msg' => 'Something went wrong :((.']); 
    die();    
  }
} 
add_action('wp_ajax_wsfy_save_payout_settings', 'ajax_wsfy_save_payout_settings');

function ajax_wsfy_save_cost_settings()
{
  global $wpdb;
  
  if(get_user_role() != WSFY_ROLE_ADMIN) {
    echo json_encode(['success' =>false, 'msg' => 'You have no access to change cost settings.']); 
    die();
  }
  $data = $_POST['data'];

  $sql = '';
  $sql .= 'UPDATE '.$wpdb->prefix.'wsfy_cost_by_duration SET cost = CASE';
  foreach($data as $row) {
    $sql .= ' WHEN appointment_type = '.$wpdb->prepare('%s', $row['appointment_type']).' AND duration = '.(int)$row['duration'].' THEN '.(int)$row['cost'];
  }
  $sql .= ' ELSE cost END';
  
  if($wpdb->query($sql)!==false) {
    $response = [
      'success' => true,
      'msg' => 'Cost settings has been updated successfully', 
    ];
    
    echo json_encode($response);
    die();    
  } else {
    echo json_encode(['success' =>false, 'msg' => 'Something went wrong :((.']); 
    die();    
  }
} 
add_action('wp_ajax_wsfy_save_cost_settings', 'ajax_wsfy_save_cost_settings');


add_filter('login_redirect', function($redirect_to, $requested_redirect_to, $user) {
  global 
    $wsfy_user_types_interpreters,
    $wsfy_user_types_requesters;
    
    if(empty($user->roles)) {
      return;
    }
    if(!empty($wsfy_user_types_interpreters[$user->roles[0]])) {
      return WSFY_TRANSLATOR_DASHBOARD_URL;
    }
    if(!empty($wsfy_user_types_requesters[$user->roles[0]])) {
      return WSFY_REQUESTER_DASHBOARD_URL;
    }
    if(in_array('administrator', $user->roles)) {
      return WSFY_ADMIN_DASHBOARD_URL;
    }
}, 3, 100);

/*
//Add login/logout link to naviagation menu
function add_login_out_item_to_menu( $items, $args ){

	//change theme location with your them location name
	if( is_admin() ||  $args->theme_location != 'primary' )
		return $items; 

	$redirect = ( is_home() ) ? false : get_permalink();
	if( is_user_logged_in( ) )
		$link = '<a href="' . wp_logout_url( $redirect ) . '" title="' .  __( 'Logout' ) .'">' . __( 'Logout' ) . '</a>';
	else  $link = '<a href="' . wp_login_url( $redirect  ) . '" title="' .  __( 'Login' ) .'">' . __( 'Login' ) . '</a>';

	return $items.= '<li id="log-in-out-link" class="menu-item menu-type-link">'. $link . '</li>';

  $current_user = wp_get_current_user();
  if(is_user_logged_in()) {
    return $items.= '<li id="log-in-out-link" class="menu-item menu-type-link">Hello, '.$current_user->display_name.'.</li>';
  }

  
}add_filter( 'wp_nav_menu_items', 'add_login_out_item_to_menu', 50, 2 );*/

function admin_custom_bar_menu() 
{
  global $wp_admin_bar;   
  if(!is_object($wp_admin_bar)) {
    return;
  }  
  
  $nodes = $wp_admin_bar->get_nodes();
  foreach( $nodes as $node ) {
    // 'top-secondary' is used for the User Actions right side menu
    if(!$node->parent || 'top-secondary' == $node->parent) {
      $wp_admin_bar->remove_menu($node->id);
    }           
  }
  
  $current_user = wp_get_current_user();
  $title_logout = is_admin() ? 'Logout' : 'Howdy, '.$current_user->display_name;
  $url_logout = is_admin() ? wp_logout_url() : get_edit_profile_url( get_current_user_id() );
  $wp_admin_bar->add_menu( [
    'id'    => 'wp-custom-logout',
    'title' => $title_logout,
    'parent'=> 'top-secondary',
    'href'  => $url_logout
  ]);
}
add_action('admin_bar_menu', 'admin_custom_bar_menu', 200);