<?php
if (!defined( 'ABSPATH') ) exit; // Exit if accessed directly

if ( ! function_exists( 'wp_handle_upload' ) ) {
  require_once( ABSPATH . 'wp-admin/includes/file.php' );
}

/**
 * admin menu callback function
 */
function wpsci_plugin_setting_page() { 
  global $wpdb;
  global $wp;

  /**
   * Save data for overview page
   */
  if(isset($_POST['wpsci_update_overview']) && isset($_POST['booking_id'])){

    // Sanitize the nonce
    $nonce_value = isset($_POST['wpsci_verify_nonce']) ? sanitize_text_field(wp_unslash($_POST['wpsci_verify_nonce'])) : '';
    if (!wp_verify_nonce($nonce_value, 'wpsci_update_overview_nonce')) {
      wp_die('Security check failed');
    }

    $count = count( (array)$_POST['first_name'] );

    $wpdb->query(
      $wpdb->prepare(
        "DELETE FROM {$wpdb->base_prefix}wpsci_guests
          WHERE booking_id = %d", sanitize_text_field($_POST['booking_id']))
    );
    
    
    for($i=0;$i<$count;$i++){

      $destinationPath = WPSCI_UPLOAD_PATH . "/" . gmdate('Y') . "/" . gmdate('m') . "/";
      if (!file_exists($destinationPath)) {
        if (!wp_mkdir_p($destinationPath, 0777, true)) {
          die("Failed to create directory: ".esc_html($destinationPath));
        }
      }

      $doc_images = [];
      $doc_image = null;
      if(!empty($_FILES['doc_img']['name'][$i][0])){
        for ($j=0; $j < count( (array)$_FILES['doc_img']['name'][$i] ) ; $j++) { 
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
      if(isset($_POST['removed_images'][$i]) && sanitize_text_field($_POST['removed_images'][$i])!==""){
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
      
      $_booking_id = sanitize_text_field($_POST['booking_id']);
      if(isset($_POST['updated_booking_id']) && wpsci_check_booking_type($_booking_id)=='wpsci'){
        $_booking_id = sanitize_text_field($_POST['updated_booking_id']);
      }

      $wpdb->query($wpdb->prepare(
        "INSERT INTO {$wpdb->base_prefix}wpsci_guests " .
          '(booking_id, first_name, last_name, email, phone, sex, dob, country, country_code, house, provinces, municipalities, citizenship, doc_type, doc_number, doc_issue_place, doc_issue_province, doc_issue_municipality, doc_image) ' .
          'VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)',
          sanitize_text_field($_booking_id),
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
        sanitize_text_field($doc_image)
      ));
    }


    //updating data in checks-in
    $id = filter_input( INPUT_POST, 'booking_id', FILTER_SANITIZE_NUMBER_INT );

    if($id && wpsci_check_booking_type($id)=='wpsci'){

      $sql = $wpdb->prepare(
        "UPDATE {$wpdb->base_prefix}wpsci_check_in SET 
            `first_name` = %s,
            `last_name` = %s,
            `arrival_date` = %s,
            `departure_date` = %s,
            `booking_id` = %s,
            `number_of_guests` = %d
         WHERE 
            `id` = %d AND
            `booking_id` = %s",

        sanitize_text_field($_POST['first_name'][0]), 
        sanitize_text_field($_POST['last_name'][0]), 
        sanitize_text_field($_POST['arrival_date']), 
        sanitize_text_field($_POST['departure_date']), 
        sanitize_text_field($_booking_id), 
        sanitize_text_field($count), 
        sanitize_text_field($_POST['check_in_id']), 
        sanitize_text_field($id)
      );
      $wpdb->query($sql);
    }else{
      update_post_meta(sanitize_text_field($id), "mphb_check_in_date", sanitize_text_field($_POST['arrival_date']));
      update_post_meta(sanitize_text_field($id), "mphb_check_out_date", sanitize_text_field($_POST['departure_date']));

      $data = get_post_meta(sanitize_text_field($id), '_mphb_booking_price_breakdown', true);
      $data = json_decode($data);
      if(wpsci_get_total_guests($id != $count)){
        $data->rooms[0]->room->adults = $count;
        update_post_meta(sanitize_text_field($id), "_mphb_booking_price_breakdown", wp_json_encode($data));
      }

    }

    wpsci_redirect(admin_url('admin.php?page=self-check-in&saved=1'));
  }

  /**
   * delete the records
   */
  if(isset($_POST['wpsci_delete_record'])){

    // Sanitize the nonce
    $nonce_value = isset($_POST['wpsci_verify_nonce']) ? sanitize_text_field(wp_unslash($_POST['wpsci_verify_nonce'])) : '';
    if (!wp_verify_nonce($nonce_value, 'wpsci_delete_record_nonce')) {
      wp_die('Security check failed');
    }

    $wpdb->query(
      $wpdb->prepare(
        "DELETE FROM {$wpdb->base_prefix}wpsci_guests
          WHERE booking_id = %d", sanitize_text_field($_POST['booking_id']))
    );

    $wpdb->query(
      $wpdb->prepare(
        "DELETE FROM {$wpdb->base_prefix}wpsci_check_in
          WHERE booking_id = %d", sanitize_text_field($_POST['booking_id']))
    );

    wpsci_redirect(admin_url('admin.php?page=self-check-in&deleted=1'));
  }


  /**
   * Created check in / save data
   */
  if(isset($_POST['wpsci_save_check_in'])){

    // Sanitize the nonce
    $nonce_value = isset($_POST['wpsci_verify_nonce']) ? sanitize_text_field(wp_unslash($_POST['wpsci_verify_nonce'])) : '';
    if (!wp_verify_nonce($nonce_value, 'wpsci_save_check_in_nonce')) {
      wp_die('Security check failed');
    }

    $random_id = 10000;
    $check_in_key=wp_generate_password(12,false);

    $number_of_guests = sanitize_text_field($_POST['number_of_guests']);
    if($number_of_guests > 0 && $number_of_guests < 50){

      $wpdb->query($wpdb->prepare(
        "INSERT INTO {$wpdb->base_prefix}wpsci_check_in " .
          '(first_name, last_name, booking_id, arrival_date, departure_date, number_of_guests, check_in_key) ' .
          'VALUES (%s, %s, %s, %s, %s, %s, %s)',
        sanitize_text_field($_POST['first_name']),
        sanitize_text_field($_POST['last_name']),
        sanitize_text_field($random_id),
        sanitize_text_field($_POST['arrival_date']),
        sanitize_text_field($_POST['departure_date']),
        $number_of_guests,
        sanitize_text_field($check_in_key)
      ));
  
      $last_insert_id = $wpdb->insert_id;
      $new_booking_id = $wpdb->insert_id;
  
      if(isset($_POST['custom_booking_id']) && intval($_POST['custom_booking_id']) > 0)
      $new_booking_id = sanitize_text_field($_POST['custom_booking_id']);
  
      if ($last_insert_id) {
        $wpdb->update(
          $wpdb->base_prefix."wpsci_check_in",
          array(
            'booking_id' => $new_booking_id,
          ),
          array(
            'id' => $last_insert_id,
          )
        );
      }
      
      for($i=0; $i< $number_of_guests; $i++){
        if($i==0){
          $first_name = sanitize_text_field($_POST['first_name']);
          $last_name = sanitize_text_field($_POST['last_name']);
          $post_email = isset($_POST['email']) ? sanitize_email($_POST['email']) : null;
          $post_phone = isset($_POST['phone']) ? sanitize_text_field($_POST['phone']) : null;
        }else{
          $post_email = null;
          $post_phone = null;
          $first_name = null;
          $last_name = null;
        }
        $wpdb->query($wpdb->prepare(
          "INSERT INTO {$wpdb->base_prefix}wpsci_guests " .
            '(booking_id, first_name, last_name, email, phone) ' .
            'VALUES (%s, %s, %s, %s, %s)',
          $new_booking_id,
          $first_name,
          $last_name,
          $post_email,
          $post_phone
        ));
      }

      wpsci_redirect(admin_url('admin.php?page=self-check-in&saved=1'));
    }
    wpsci_redirect(admin_url('admin.php?page=self-check-in&saved=0'));
    
  }


  $wp->parse_request();
  $page_url = home_url( $wp->request );
  $main_page = '';
  if(isset($_GET['page']))
  $main_page = sanitize_key($_GET['page']);
  ?>

  <?php if(isset($_GET['tab']) && sanitize_text_field( wp_unslash( $_GET['tab'] ) ) =='setting'){

    /**
     * setttings tab
     */
    include_once(plugin_dir_path( __FILE__ ) . 'admin/tabs/settings/settings.php');
    
  }else{

    /**
     * overview tab
     */
    if(isset($_GET['edit']) && sanitize_text_field( wp_unslash( $_GET['edit'] ) )!==''){

      include_once(plugin_dir_path( __FILE__ ) . 'admin/tabs/overview/edit-overview.php');
    }else{
      
      include_once(plugin_dir_path( __FILE__ ) . 'admin/tabs/overview/overview.php');
    }
  }?>


<?php }

/**
 * admin menu hook function
 */
function wpsci_plugin_setting_function() {
  $title = esc_html__( 'Self Check-In', 'self-check-in' );

  add_menu_page( $title, $title,'manage_options', 'self-check-in', 'wpsci_plugin_setting_page', $icon_url='', 30 );
  
}

//admin menu hook
add_action('admin_menu', 'wpsci_plugin_setting_function',20);