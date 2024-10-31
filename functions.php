<?php
if (!defined( 'ABSPATH') ) exit; // Exit if accessed directly


require_once WPSCI_PLUGIN_PATH . 'init.php';

// Define custom email tags
function wpsci_custom_email_tags( $message ) {
  global $post;

  if ( isset( $post ) && is_a( $post, 'WP_Post' ) ) {
    $post_id = $post->ID;
    $_key = get_post_meta($post_id, "self-check-in-key", true);
    
    $_url = WPSCI_SITE_URL.'/'.get_option( 'wpsci_public_page' ).'?id='.$post_id.'&key='.$_key;
    $custom_tags = array(
      '%wpsci_form_url%' => $_url,
    );
  
    foreach ( $custom_tags as $tag => $value ) {
      $message['message'] = str_replace( $tag, $value, $message['message'] );
    }
  }

  return $message;
}
add_filter( 'wp_mail', 'wpsci_custom_email_tags' );


/**
 * get documents list from documents.db file
 */
function wpsci_get_document_list() {

  // Initialize the WP Filesystem
  if ( ! function_exists( 'request_filesystem_credentials' ) ) {
    require_once ABSPATH . 'wp-admin/includes/file.php';
  }
  $url = wp_nonce_url('admin.php', 'wpsci_get_document_list');
  $creds = request_filesystem_credentials($url, '', false, false, null);

  if ( ! WP_Filesystem($creds) ) {
    return []; // Could not access filesystem
  }
  global $wp_filesystem;
  
  $file_path = WPSCI_PLUGIN_URL . "assets/documents/documents.db";
  
  // Read the file contents
  $file_contents = $wp_filesystem->get_contents($file_path);
  
  if ( $file_contents === false ) {
    return []; // Could not read the file
  }
  
  // Process the file contents
  $lines = [];
  $file_lines = explode("\n", $file_contents);
  foreach ( $file_lines as $line ) {
    $lines[] = str_getcsv($line);
  }

  return $lines;
}


/**
 * get countries list from states.db file
 */
function wpsci_get_country_list(){

  // Initialize the WP Filesystem
  if ( ! function_exists( 'request_filesystem_credentials' ) ) {
    require_once ABSPATH . 'wp-admin/includes/file.php';
  }
  $url = wp_nonce_url('admin.php', 'wpsci_get_document_list');
  $creds = request_filesystem_credentials($url, '', false, false, null);

  if ( ! WP_Filesystem($creds) ) {
    return []; // Could not access filesystem
  }
  global $wp_filesystem;
  
  $file_path = WPSCI_PLUGIN_URL . "assets/documents/states.db";
  
  // Read the file contents
  $file_contents = $wp_filesystem->get_contents($file_path);
  
  if ( $file_contents === false ) {
    return []; // Could not read the file
  }
  
  // Process the file contents
  $lines = [];
  $file_lines = explode("\n", $file_contents);
  foreach ( $file_lines as $line ) {
    $lines[] = str_getcsv($line);
  }

  return $lines;
}


/**
 * get house list from house.db file
 */
function wpsci_get_house_list(){

  // Initialize the WP Filesystem
  if ( ! function_exists( 'request_filesystem_credentials' ) ) {
    require_once ABSPATH . 'wp-admin/includes/file.php';
  }
  $url = wp_nonce_url('admin.php', 'wpsci_get_document_list');
  $creds = request_filesystem_credentials($url, '', false, false, null);

  if ( ! WP_Filesystem($creds) ) {
    return []; // Could not access filesystem
  }
  global $wp_filesystem;
  
  $file_path = WPSCI_PLUGIN_URL . "assets/documents/house.db";
  
  // Read the file contents
  $file_contents = $wp_filesystem->get_contents($file_path);
  
  if ( $file_contents === false ) {
    return []; // Could not read the file
  }
  
  // Process the file contents
  $lines = [];
  $file_lines = explode("\n", $file_contents);
  foreach ( $file_lines as $line ) {
    $lines[] = str_getcsv($line);
  }

  return $lines;
}


/**
 * get municipalities list from municipalities.db file
 */
function wpsci_get_municipal_list(){

  // Initialize the WP Filesystem
  if ( ! function_exists( 'request_filesystem_credentials' ) ) {
    require_once ABSPATH . 'wp-admin/includes/file.php';
  }
  $url = wp_nonce_url('admin.php', 'wpsci_get_document_list');
  $creds = request_filesystem_credentials($url, '', false, false, null);

  if ( ! WP_Filesystem($creds) ) {
    return []; // Could not access filesystem
  }
  global $wp_filesystem;
  
  $file_path = WPSCI_PLUGIN_URL . "assets/documents/municipalities.db";
  
  // Read the file contents
  $file_contents = $wp_filesystem->get_contents($file_path);
  
  if ( $file_contents === false ) {
    return []; // Could not read the file
  }
  
  // Process the file contents
  $lines = [];
  $file_lines = explode("\n", $file_contents);
  foreach ( $file_lines as $line ) {
    $lines[] = str_getcsv($line);
  }

  return $lines;
}

/**
 * 
 */
function wpsci_check_field($id){
  global $wpdb;

  $sql = "SELECT * FROM {$wpdb->base_prefix}wpsci_guests WHERE booking_id = %d";
  $result = $wpdb->get_results($wpdb->prepare($sql, $id), ARRAY_A);
  if($result){
    
    $c = 0;
    for ($i=0; $i < sizeof($result); $i++) { 
      if($result[$i]['first_name'] && $result[$i]['last_name'] && $result[$i]['sex'] && $result[$i]['dob'] && $result[$i]['country'] && $result[$i]['citizenship'] && $result[$i]['house']){
        $c++;
      }
    }
    if($c==sizeof($result)){
      if($result[0]['doc_type'] && $result[0]['doc_number'] && $result[0]['doc_issue_place']){
        return 1;
      }
    }else{
      return 0;
    }
  }else{
    return 0;
  }
}


/**
 * 
 */
function wpsci_get_check_in($id){
  global $wpdb;

  $sql = "select meta_value from {$wpdb->base_prefix}postmeta where post_id='%d' and meta_key ='mphb_check_in_date'";
  $check_in_date = $wpdb->get_row($wpdb->prepare($sql, $id), ARRAY_A);
  if($check_in_date){
    return gmdate("d-m-Y", strtotime($check_in_date['meta_value']));
  }else{

    $sql = "select * from {$wpdb->base_prefix}wpsci_check_in where booking_id='%d'";
    $check_in_date = $wpdb->get_row($wpdb->prepare($sql, $id), ARRAY_A);
    return gmdate("d-m-Y", strtotime($check_in_date['arrival_date']));
  }
}


/**
 * 
 */
function wpsci_get_check_out($id){
  global $wpdb;

  $sql = "select meta_value from {$wpdb->base_prefix}postmeta where post_id='%d' and meta_key ='mphb_check_out_date'";
  $check_out_date = $wpdb->get_row($wpdb->prepare($sql, $id), ARRAY_A);
  if($check_out_date){

    return gmdate("d-m-Y", strtotime($check_out_date['meta_value']));
  }else{

    $sql = "select * from {$wpdb->base_prefix}wpsci_check_in where booking_id='%d'";
    $check_out_date = $wpdb->get_row($wpdb->prepare($sql, $id), ARRAY_A);
    return gmdate("d-m-Y", strtotime($check_out_date['departure_date']));
  }
}


/**
 * 
 */
function wpsci_get_check_in_key($id){
  global $wpdb;

  $sql = "select * from {$wpdb->base_prefix}wpsci_check_in where booking_id='%d'";
  $data = $wpdb->get_row($wpdb->prepare($sql, $id), ARRAY_A);
  
  if($data)
  return $data['check_in_key'];
}


/**
 * 
 */
function wpsci_get_receipt($id){
  global $wpdb;

  $sql = "select * from {$wpdb->base_prefix}wpsci_check_in where booking_id='%d'";
  $data = $wpdb->get_row($wpdb->prepare($sql, $id), ARRAY_A);

  if($data){
    return $data['receipt'];
  }
  $meta_receipt = get_post_meta($id, "self-check-in-receipt", true);
  return $meta_receipt;
}

/**
 * 
 */
function wpsci_check_booking_type($id){
  global $wpdb;

  $sql = "select * from {$wpdb->base_prefix}wpsci_check_in where booking_id='%d'";
  $data=$wpdb->get_row($wpdb->prepare($sql, $id), ARRAY_A);
  
  if($data){
    return 'wpsci';
  }
  return 'mphb';
}

/**
 * 
 */
function wpsci_get_total_guests($id, $listing = false){
  global $wpdb;

  if($listing){
    $sql = "select count(id) as count from {$wpdb->base_prefix}wpsci_guests where booking_id='%d'";
    $data = $wpdb->get_row($wpdb->prepare($sql, $id), ARRAY_A);
    return $data['count'];
  }

  $sql = "select meta_value from {$wpdb->base_prefix}postmeta where post_id='%d' and meta_key ='_mphb_booking_price_breakdown'";
  $result = $wpdb->get_row($wpdb->prepare($sql, $id), ARRAY_A);
  
  if($result){
    $data = json_decode($result['meta_value'], true);
    $sum = 0;
    foreach ($data['rooms'] as $key => $value) {
      $sum += $value['room']['adults'];
    }
    return $sum;

  }else{

    $sql = "select * from {$wpdb->base_prefix}wpsci_check_in where booking_id='%d'";
    $data = $wpdb->get_row($wpdb->prepare($sql, $id), ARRAY_A);
    return $data['number_of_guests'];
  }
}

/**
 * 
 */
function wpsci_get_booking_id($id){
  global $wpdb;

  $sql = "select * from {$wpdb->base_prefix}postmeta where post_id = '%d'";
  $result = $wpdb->get_row($wpdb->prepare($sql, $id), ARRAY_A);
  
  if($result){
    return $result['post_id'];
  }
  else{
    return $id;
  }
}

/**
 * 
 */
function wpsci_get_first_member($id){
  global $wpdb;

  $sql = "select meta_value from {$wpdb->base_prefix}postmeta where post_id='%d' and meta_key IN('mphb_first_name', 'mphb_last_name')";
  $result= $wpdb->get_results($wpdb->prepare($sql, $id), ARRAY_A);

  if($result){
    return $result;
  }
}

/**
 * check if MPHB is active
 */
function wpsci_is_mphb_active(){
  if(menu_page_url( 'mphb_booking_menu', false )){
    return true;
  }else{
    return false;
  }
}


/**
 * 
 * get selected municipal
 */
function wpsci_get_selected_municipal($str){

  $list = wpsci_get_municipal_list();

  $items = array_filter($list, function($item) use ($str) {
    return isset($item[2]) && $item[2] === $str;
  });
  
  return $items;
}


/**
 * 
 * plugin settings
 */
add_action('admin_post_wpsci_settings', 'wpsci_settings_handler');
add_action('admin_post_nopriv_wpsci_settings', 'wpsci_settings_handler');

function wpsci_settings_handler(){

  // Check if user has permission
  if (!current_user_can('manage_options')) {
    wp_die(esc_html__('You do not have sufficient permissions to access this page.', 'self-check-in'));
  }

  // Verify nonce
  check_admin_referer('wpsci_settings_nonce');
  
  update_option( 'wpsci_plugin', sanitize_text_field($_POST['digital_plugin']) );
  update_option( 'wpsci_document_field', sanitize_text_field($_POST['_document_field']) );
  update_option( 'wpsci_guests_email', sanitize_email($_POST['_wpsci_guests_email']) );
  update_option( 'wpsci_guests_phone', sanitize_text_field($_POST['_wpsci_guests_phone']) );
  update_option( 'wpsci_public_page',sanitize_text_field($_POST['digital_public_page']) );
  
  wpsci_redirect(admin_url('admin.php?page=self-check-in&tab=setting&saved=1'));
}


/**
 * 
 * get check-in data
 */
function wpsci_get_checkin_data($id){
  global $wpdb;

  $sql = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->base_prefix}wpsci_check_in WHERE booking_id = %d", $id), ARRAY_A);

  if($sql)
  return $sql;
  else
  return array();
}

/**
 * 
 * redirect function
 */
function wpsci_redirect($url = null){
  $_url = menu_page_url('self-check-in', false);
  if($url) $_url = $url;

  ob_end_clean();
  if (headers_sent()) {
    echo '<meta http-equiv="refresh" content="0;url='.esc_url($_url).'">';
    exit;
  } else {
    wp_redirect($_url);
    exit;
  }
}

//Including Admin and View Page//
if(is_admin()){
  
  require_once WPSCI_PLUGIN_PATH . 'admin-page.php';
}else{
  
  require_once WPSCI_PLUGIN_PATH . 'public-page.php'; 
}