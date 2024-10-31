<?php
if (!defined( 'ABSPATH') ) exit; // Exit if accessed directly

if ( ! function_exists( 'wp_handle_upload' ) ) {
  require_once( ABSPATH . 'wp-admin/includes/file.php' );
}

/**
 * frontend page shortcode
 */
function wpsci_form_function() {
  ob_start();
  global $wpdb;
  
  $verified = false; //to check if the client verified his/her mail id
  //verify guests with email
  if(isset($_POST['wpsci_verify_email'])){

    // Sanitize the nonce
    $nonce_value = isset($_POST['wpsci_verify_nonce']) ? sanitize_text_field(wp_unslash($_POST['wpsci_verify_nonce'])) : '';
    if (!wp_verify_nonce($nonce_value, 'wpsci_verify_email_nonce')) {
      wp_die('Security check failed');
    }
    $id = filter_input( INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT );

    if($id && wpsci_check_booking_type($id)=='wpsci'){
      
        $sql = "SELECT gt.booking_id, ch.check_in_key 
        FROM ".$wpdb->prefix."wpsci_guests gt JOIN ".$wpdb->prefix."wpsci_check_in ch 
        ON ch.booking_id = gt.booking_id 
        WHERE gt.booking_id='%d' 
        AND gt.email = '%s' LIMIT 1";

        $result = $wpdb->get_row($wpdb->prepare($sql, sanitize_text_field($_POST['id']), sanitize_email($_POST['email'])), ARRAY_A);
        if ($result) {
          $url = WPSCI_SITE_URL.'/'.get_option( 'wpsci_public_page' ).'?id='.sanitize_text_field($_POST['id']).'&key='.$result['check_in_key'];
          $verified=true;
          wp_redirect($url);
          exit;
        }
    }else{

      $table_name = $wpdb->prefix ."postmeta";
      $sql = "SELECT * FROM `".$table_name."` where
        `post_id`='%d' AND
        `meta_key` = 'mphb_email' AND
        `meta_value` = '%s'";
        if ($wpdb->get_results($wpdb->prepare($sql, sanitize_text_field($_POST['id']), sanitize_email($_POST['email'])))) {
          $_key = get_post_meta(sanitize_text_field($_POST['id']), "self-check-in-key", true);
          $url = WPSCI_SITE_URL.'/'.get_option( 'wpsci_public_page' ).'?id='.sanitize_text_field($_POST['id']).'&key='.$_key;
          $verified=true;
          wp_redirect($url);
          exit;
        }
    }
  }

 
  if(isset($_GET['id']) && sanitize_text_field($_GET['id'])!=''){

    $post_id = filter_input( INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT );
    
    $booking_id = wpsci_get_booking_id($post_id);
    $number_of_guest = wpsci_get_total_guests($booking_id);
    $first_member = wpsci_get_first_member($booking_id);
    
    $customer_id = get_post_meta($post_id, "mphb_customer_id", true);


    
    //insert/update guest data
    if(isset($_POST['wpsci_submit_guests'])){

      // Sanitize the nonce
      $nonce_value = isset($_POST['wpsci_verify_nonce']) ? sanitize_text_field(wp_unslash($_POST['wpsci_verify_nonce'])) : '';
      if (!wp_verify_nonce($nonce_value, 'wpsci_submit_guests_nonce')) {
        wp_die('Security check failed');
      }
      $count = count( (array)$_POST['first_name'] );
    
      $wpdb->query(
        $wpdb->prepare(
          "DELETE FROM {$wpdb->base_prefix}wpsci_guests
            WHERE booking_id = %d", sanitize_text_field($booking_id))
      );
    
      
      for($i=0;$i<$count;$i++){

        //save document
        $destinationPath = WPSCI_UPLOAD_PATH . "/" . gmdate('Y') . "/" . gmdate('m') . "/";

        if (!file_exists($destinationPath)) {
          if (!wp_mkdir_p($destinationPath, 0777, true)) {
            die("Failed to create directory: ".esc_html($destinationPath));
          }
        }

        $doc_images = [];
        $doc_image = null;
        if(!empty($_FILES['doc_img']['name'][$i][0])){
          
          for ($j=0; $j < count( (array)$_FILES['doc_img']['name'][$i] ); $j++) {
            $image = sanitize_file_name($_FILES['doc_img']['name'][$i][$j]);
            
            if($image){
              // Extract the file extension
              $file_info = pathinfo($image);
              $extension = isset($file_info['extension']) ? $file_info['extension'] : '';

              $new_file_name = $file_info['filename'] . '_' . time() . '.' . $extension;

              // Validate the tmp_name
              $tmp_name = sanitize_text_field($_FILES['doc_img']['tmp_name'][$i][$j]);
              if (!is_uploaded_file($tmp_name)) {
                
                $doc_images[] = '';
                continue;
              }

              // Validate the error code
              $error = intval($_FILES['doc_img']['error'][$i][$j]);
              if (!is_int($error) || $error !== UPLOAD_ERR_OK) {
                
                $doc_images[] = '';
                continue;
              }

              // Validate the file size
              $size = intval($_FILES['doc_img']['size'][$i][$j]);
              if (!is_numeric($size) || $size <= 0) {
                
                $doc_images[] = '';
                continue;
              }

              // Prepare the file array
              $file = [
                'name'     => $new_file_name,
                'type'     => sanitize_mime_type($_FILES['doc_img']['type'][$i][$j]),
                'tmp_name' => $tmp_name,
                'error'    => $error,
                'size'     => $size,
              ];

              // Allowed document formats
              $allowed_mime_types = ['image/jpeg', 'image/png', 'application/pdf'];

              if (in_array($file['type'], $allowed_mime_types, true)){

                $upload_overrides = ['test_form' => false];
                $movefile = wp_handle_upload($file, $upload_overrides);
        
                if ($movefile && !isset($movefile['error'])) {
                  $doc_images[] = gmdate('Y') . "/" . gmdate('m') . "/" . basename($movefile['file']);
                } else {
                  $doc_images[] = '';
                }
              }else{
                $doc_images[] = '';
              }
              
            }else{
              $doc_images[] = '';
            }
          }

          $reversed_doc_image = str_replace("'", '"', stripslashes(sanitize_text_field($_POST['doc_img_real'][$i])));
          if($reversed_doc_image==""){
            $unserialized_data=array();
          }else{
            $unserialized_data = unserialize($reversed_doc_image);
          }
          
          $array = array_merge($unserialized_data, $doc_images);
          
          $doc_image = serialize($array);
          
        }else{
          if(isset($_POST['doc_img_real'][$i]) && sanitize_text_field($_POST['doc_img_real'][$i]) !=="")
          $doc_image = stripslashes(str_replace("'", '"', sanitize_text_field($_POST['doc_img_real'][$i])));
        }

        // finalise the images
        if(isset($_POST['removed_images'][$i]) && sanitize_text_field($_POST['removed_images'][$i])!=""){
          $unserialized_data = unserialize($doc_image);
          $removed_images = explode(',', sanitize_text_field($_POST['removed_images'][$i]));
          foreach($removed_images as $removed_image){
            if (($key = array_search($removed_image, $unserialized_data)) !== false) {
              unset($unserialized_data[$key]);
            }
          }

          $doc_image = serialize($unserialized_data);
        }
        
        $post_email = isset($_POST['email'][$i]) ? sanitize_email($_POST['email'][$i]) : null;
        $post_phone = isset($_POST['phone'][$i]) ? sanitize_text_field($_POST['phone'][$i]) : null;

        $wpdb->query($wpdb->prepare(
          "INSERT INTO {$wpdb->base_prefix}wpsci_guests " .
            '(booking_id, first_name, last_name, email, phone, sex, dob, country, country_code, house, provinces, municipalities, citizenship, doc_type, doc_number, doc_issue_place, doc_issue_province, doc_issue_municipality, doc_image) ' .
            'VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)',
            sanitize_text_field($booking_id),
          sanitize_text_field($_POST['first_name'][$i]),
          sanitize_text_field($_POST['last_name'][$i]),
          $post_email,
          $post_phone,
          sanitize_text_field($_POST['sex'][$i]),
          sanitize_text_field($_POST['dob'][$i]),
          sanitize_text_field($_POST['country'][$i]),
          sanitize_text_field($_POST['country_code'][$i]),
          sanitize_text_field($_POST['house'][$i]),
          sanitize_text_field($_POST['provinces'][$i]),
          sanitize_text_field($_POST['municipalities'][$i]),
          sanitize_text_field($_POST['citizenship'][$i]),
          sanitize_text_field($_POST['doc_type'][$i]),
          sanitize_text_field($_POST['doc_number'][$i]),
          sanitize_text_field($_POST['doc_issue_place'][$i]),
          sanitize_text_field($_POST['doc_issue_province'][$i]),
          sanitize_text_field($_POST['doc_issue_municipality'][$i]),
          sanitize_text_field($doc_image),
        ));
      }

      $_key = wpsci_get_check_in_key(sanitize_text_field($booking_id));
      $url = WPSCI_SITE_URL.'/'.get_option( 'wpsci_public_page' ).'?id='.sanitize_text_field($booking_id).'&key='.$_key;
      wpsci_redirect($url);
    }

 

    //validation
    if($verified===true){
      //if verified
      include_once(plugin_dir_path( __FILE__ ) . 'frontend/index.php');

    }else if(get_current_user_id()==$customer_id){
      
      include_once(plugin_dir_path( __FILE__ ) . 'frontend/index.php');

    }else if(isset($_GET['key']) && sanitize_text_field($_GET['key'])!=''){

      //validate with key
      if(get_post_meta($post_id, "self-check-in-key", true) == sanitize_text_field($_GET['key']) || wpsci_get_check_in_key($post_id)== sanitize_text_field($_GET['key'])){

        include_once(plugin_dir_path( __FILE__ ) . 'frontend/index.php');

      }else{

        echo "<h4 align='center'>".esc_html__( 'Page Not Found', 'self-check-in' )."!</h4>";
      }
    }elseif(isset($_GET['id']) && sanitize_text_field($_GET['id'])!=''){

      //validate with email
      include_once(plugin_dir_path( __FILE__ ) . 'frontend/templates/validate-by-email.php');
    }else{

      echo "<h4 align='center'>".esc_html__( 'Page Not Found', 'self-check-in' )."!</h4>";
    }

  }else{
    
    if(isset($_GET['id']) && sanitize_text_field($_GET['id'])!=''){
      echo "<h4 align='center'>".esc_html__( 'Page Not Found', 'self-check-in' )."!</h4>";
    }
  }
  return ob_get_clean();
}

//validate activation
if(get_option( 'wpsci_plugin' )==1){


  function wpsci_shortcode_init(){
    add_shortcode( 'wpsci_form', 'wpsci_form_function' );
  }

  //shortcode hook
  add_action('init', 'wpsci_shortcode_init');

}