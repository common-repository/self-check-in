<?php
if (!defined( 'ABSPATH') ) exit; // Exit if accessed directly

$id = filter_input( INPUT_GET, 'edit', FILTER_SANITIZE_NUMBER_INT );
$res_check_in = wpsci_get_checkin_data($id);

//for guests
$sql = "SELECT * FROM {$wpdb->base_prefix}wpsci_guests WHERE booking_id = %d";
$res = $GLOBALS['wpdb']->get_results($wpdb->prepare($sql, $id), ARRAY_A);
if(!$res){
  wpsci_redirect(esc_url(menu_page_url('self-check-in', false)));
}
?>

<div class="wrap">

  <?php 
  /**
   * include tabs template
   */
  include_once WPSCI_PLUGIN_PATH . 'admin/tabs/tabs.php';?>
  
  <div class="metabox-holder" id="overview_wrap">
 
    <div class="inside postbox" id="edit_wrap">
      <form method="POST" id="update_form" enctype="multipart/form-data">
        <?php wp_nonce_field('wpsci_update_overview_nonce', 'wpsci_verify_nonce');?>
        <div class="master-details-div">
          <div class="dates-div w-50">
            <div><h3></span> <?php esc_html_e( 'Check-In', 'self-check-in' );?></h3></div>     
            <div class="master-dates">
              <table class="form-table">
                <tbody>
                  <?php if(wpsci_check_booking_type($id)=='wpsci'){?>
                  <tr>
                    <th><label for="updated_booking_id"><?php esc_html_e('Booking ID', 'self-check-in' );?></label></th>
                    <td colspan="1">
                      <input type="number" name="updated_booking_id" id="updated_booking_id" class=" regular-text" value="<?php echo esc_html($id);?>" data-id="<?php echo esc_html($id);?>">
                      <small class="field-alert hide"><?php esc_html_e('Booking ID not available', 'self-check-in' );?></small>
                    </td>
                  </tr>
                  <?php }?>
                  <tr>
                    <th><label for="arrival_date"><?php esc_html_e('Arrival Date', 'self-check-in' );?></label></th>
                    <td colspan="1">
                      <input type="date" name="arrival_date" id="arrival_date" class=" regular-text" value="<?php echo esc_html(gmdate("Y-m-d", strtotime(wpsci_get_check_in($id))));?>">
                    </td>
                  </tr>
                  <tr>
                    <th><label for="departure_date"><?php esc_html_e('Departure Date', 'self-check-in' );?></label></th>
                    <td colspan="1">
                      <input type="date" name="departure_date" id="departure_date" class=" regular-text" value="<?php echo esc_html(gmdate("Y-m-d", strtotime(wpsci_get_check_out($id))));?>">
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
            <div class="add-row">
              <span class="addrow-btn">
                <button class="button button-success add-guest" data-path = "<?php echo esc_url(WPSCI_PLUGIN_URL) . 'assets/documents/'; ?>" type="button"><?php esc_html_e('Add Row', 'self-check-in' );?></button>
              </span>
            </div>
        </div>
        <hr>
        <input type="hidden" name="check_in_id" value="<?php echo $res_check_in ? esc_html($res_check_in['id']) : '';?>" readonly>
        <input type="hidden" name="booking_id" value="<?php echo esc_html($id);?>" readonly>
        <?php
            $i = 0;
            foreach ($res as $key => $value) {
              $i++;
              ?>
          <div class="table-count" id="table_<?php echo esc_html($i); ?>" data-count ="<?php echo esc_html($i); ?>">
          <table class="form-table" >
            <div class="guest-information-heading guest-info-d-flex align-items-center w-50">
                <div><h3><span class="guest_count"><?php echo esc_html($i); ?>.</span> <?php esc_html_e( 'Guest Information', 'self-check-in' );?></h3></div>
                <?php 
                  if($i>1){
                    echo"<div class='remove-sign' data-id='table_'".esc_html($i)."'>X</div>";
                  }
                ?>                
            </div>
            <tbody>
              <tr>
                <th><label for=""><?php esc_html_e( 'Name', 'self-check-in' );?></label></th>
                <td colspan="1">
                  <div class="mphb-ctrl-wrapper mphb-ctrl mphb-ctrl-text" data-type="text" data-inited="true">
                    <input name="first_name[]" id="first_name<?php echo esc_html($i); ?>" value="<?php echo (wpsci_check_booking_type($id) == 'mphb' && $i==1) ? esc_html(get_post_meta($id, 'mphb_first_name', true)) : esc_html($value['first_name']); ?>" class="regular-text <?php echo (wpsci_check_booking_type($id) == 'mphb' && $i==1) ? 'disabled-field':'';?>" type="text">
                  </div>
                </td>
              </tr>
              <tr>
                <th><label for=""><?php esc_html_e( 'Surname', 'self-check-in' );?></label></th>
                <td colspan="1">
                  <div class="mphb-ctrl-wrapper mphb-ctrl mphb-ctrl-text" data-type="text" data-inited="true">
                    <input name="last_name[]" id="last_name<?php echo esc_html($i); ?>" value="<?php echo (wpsci_check_booking_type($id) == 'mphb' && $i==1) ? esc_html(get_post_meta($id, 'mphb_last_name', true)) : esc_html($value['last_name']); ?>" class=" regular-text <?php echo (wpsci_check_booking_type($id) == 'mphb' && $i==1) ? 'disabled-field':'';?>" type="text">
                  </div>
                </td>
              </tr>

              <?php if($i == 1){
                ?>
                <tr>
                  <th><label for="email"><?php esc_html_e( 'Email', 'self-check-in' );?></label></th>
                  <td colspan="1">
                    <div class="mphb-ctrl-wrapper mphb-ctrl mphb-ctrl-text" data-type="text" data-inited="true">
                      <input name="email[]" id="email<?php echo esc_html($i); ?>" value="<?php echo wpsci_check_booking_type($id) == 'mphb' ? esc_html(get_post_meta($id, 'mphb_email', true)) : esc_html($value['email']); ?>" class="regular-text" type="text" <?php echo wpsci_check_booking_type($id) == 'mphb' ? 'readonly' : ''; ?>>
                    </div>
                  </td>
                </tr>
                <?php
              }elseif(get_option('wpsci_guests_email') =='1'){?>
                <tr>
                  <th><label for="email"><?php esc_html_e( 'Email', 'self-check-in' );?></label></th>
                  <td colspan="1">
                    <div class="mphb-ctrl-wrapper mphb-ctrl mphb-ctrl-text" data-type="text" data-inited="true">
                    <input name="email[]" id="email<?php echo esc_html($i); ?>" value="<?php echo esc_html($value['email']); ?>" class="regular-text" type="text">
                    </div>
                  </td>
                </tr>
              <?php }?>

              <?php if($i == 1){
                ?>
                <tr>
                  <th><label for=""><?php esc_html_e( 'Phone', 'self-check-in' );?></label></th>
                  <td colspan="1">
                    <div class="mphb-ctrl-wrapper mphb-ctrl mphb-ctrl-text" data-type="text" data-inited="true">
                      <input name="phone[]" id="phone<?php echo esc_html($i); ?>" value="<?php echo wpsci_check_booking_type($id) == 'mphb' ? esc_html(get_post_meta($id, 'mphb_phone', true)) : esc_html($value['phone']); ?>" class=" regular-text" type="text" <?php echo wpsci_check_booking_type($id) == 'mphb' ? 'readonly' : ''; ?>>
                    </div>
                  </td>
                </tr>
                <?php
              }elseif(get_option('wpsci_guests_phone') =='1'){?>
                <tr>
                  <th><label for=""><?php esc_html_e( 'Phone', 'self-check-in' );?></label></th>
                  <td colspan="1">
                    <div class="mphb-ctrl-wrapper mphb-ctrl mphb-ctrl-text" data-type="text" data-inited="true">
                      <input name="phone[]" id="phone<?php echo esc_html($i); ?>" value="<?php echo esc_html($value['phone']); ?>" class=" regular-text" type="text">
                    </div>
                  </td>
                </tr>
              <?php }?>
              
              <tr>
                <th><label for=""><?php esc_html_e( 'Sex', 'self-check-in' );?></label></th>
                <td colspan="1">
                  <div class="mphb-ctrl-wrapper mphb-ctrl mphb-ctrl-text" data-type="text" data-inited="true">
                    <select name="sex[]" id="sex<?php echo esc_html($i); ?>">
                      <option value=""><?php esc_html_e( 'Select', 'self-check-in' );?></option>
                      <option value="male" <?php if("male"==$value["sex"]) echo "selected";?>><?php esc_html_e( 'Male', 'self-check-in' );?></option>
                      <option value="female" <?php if("female"==$value["sex"]) echo "selected";?>><?php esc_html_e( 'Female', 'self-check-in' );?></option>
                    </select>
                  </div>
                </td>
              </tr>
              <tr>
                <th><label for=""><?php esc_html_e( 'Date of Birth', 'self-check-in' );?></label></th>
                <td colspan="1">
                  <div class="mphb-ctrl-wrapper mphb-ctrl mphb-ctrl-text" data-type="text" data-inited="true">
                    <input name="dob[]" id="dob<?php echo esc_html($i); ?>" value="<?php echo esc_html($value['dob']); ?>" class=" regular-text" type="date">
                  </div>
                </td>
              </tr>
              <tr>
                <th><label for=""><?php esc_html_e( 'Country of Birth', 'self-check-in' );?></label></th>
                <td colspan="1">
                  <div class="mphb-ctrl-wrapper mphb-ctrl mphb-ctrl-text" data-type="text" data-inited="true">
                    <select name="country_code[]" class="ccode" id="country_code<?php echo esc_html($i); ?>" onchange="get_country_name(<?php echo esc_html($i);?>)">
                      <option value=""><?php esc_html_e( 'Select', 'self-check-in' );?></option>
                      <?php 
                      $line = wpsci_get_country_list();
                      for ($j=0; $j < sizeof($line); $j++) { 
                        $item = $line[$j];
                        ?>
                        <option value="<?php echo esc_html($item[0]);?>" data-val="<?php echo esc_html($item[1]);?>" <?php if($item[0]==$value["country_code"]) echo "selected";?>><?php echo esc_html($item[1]);?></option>
                        <?php
                      }
                      ?>
                    </select>
                    <input type="hidden" name="country[]" id="country<?php echo esc_html($i); ?>" value="<?php echo esc_html($value['country']);?>">
                  </div>
                </td>
              </tr>
              <?php if($value["country_code"]==100000100){?>
              <tr id="provinces<?php echo esc_html($i); ?>">
                <th><label for=""><?php esc_html_e( 'Province of Birth', 'self-check-in' );?></label></th>
                <td colspan="1">
                  <div class="mphb-ctrl-wrapper mphb-ctrl mphb-ctrl-text" data-type="text" data-inited="true">
                    <select name="provinces[]" id="provinces<?php echo esc_html($i); ?>" onchange="get_municipal(<?php echo esc_html($i);?>)">
                      <option value=""><?php esc_html_e( 'Select', 'self-check-in' );?></option>
                      <?php 
                      $province=array();
                      $line = wpsci_get_municipal_list();
                      for ($j=0; $j < sizeof($line); $j++) { 
                        $item = $line[$j];
            
                        $province[$item[2]]=$item[3];
                      
                      }
                      asort($province);
                      foreach ($province as $key=>$val) { 
                        ?>
                      <option value="<?php echo esc_html($key);?>" <?php if($key==$value["provinces"]) echo "selected";?>><?php echo esc_html($val); ?></option>
                      <?php
                      }
                      ?>
                    </select>
                  </div>
                </td>
              </tr>
              <tr id="municipal<?php echo esc_html($i); ?>">
                <th><label for=""><?php esc_html_e( 'Municipality of Birth', 'self-check-in' );?></label></th>
                <td colspan="1">
                  <div class="mphb-ctrl-wrapper mphb-ctrl mphb-ctrl-text" data-type="text" data-inited="true">
                    <select name="municipalities[]" id="municipalities<?php echo esc_html($i); ?>">
                      <option value=""><?php esc_html_e( 'Select', 'self-check-in' );?></option>
                      <?php 
                      $municipal_list = wpsci_get_selected_municipal($value["provinces"]);
                      foreach($municipal_list as $key => $item){
                        ?>
                        <option value="<?php echo esc_html($item[0]);?>" data-provice="<?php echo esc_html($item[2]);?>" <?php if($item[0]==$value["municipalities"]) echo "selected";?>><?php echo esc_html($item[1]);?></option>
                        <?php
                      }
                      ?>
                    </select>
                  </div>
                </td>
              </tr>
              <?php }else {?>
                <tr id="provinces<?php echo esc_html($i); ?>" class="dis-none">
                  <th><label for=""><?php esc_html_e( 'Province of Birth', 'self-check-in' );?></label></th>
                  <td colspan="1">
                    <div class="mphb-ctrl-wrapper mphb-ctrl mphb-ctrl-text" data-type="text" data-inited="true">
                      <select name="provinces[]" id="provinces<?php echo esc_html($i); ?>" onchange="get_municipal(<?php echo esc_html($i);?>)">
                        <option value=""><?php esc_html_e( 'Select', 'self-check-in' );?></option>
                        <?php 
                        $province=array();
                        $line = wpsci_get_municipal_list();
                        for ($j=0; $j < sizeof($line); $j++) { 
                          $item = $line[$j];
              
                          $province[$item[2]]=$item[3];
                        
                        }
                        asort($province);
                        foreach ($province as $key=>$val) { 
                          
                          ?>
                        <option value="<?php echo esc_html($key);?>" <?php if($key==$value["provinces"]) echo "selected";?>><?php echo esc_html($val); ?></option>
                        <?php
                        }
                        ?>
                      </select>
                    </div>
                  </td>
                </tr>
                <tr id="municipal<?php echo esc_html($i); ?>" class="dis-none">
                  <th><label for=""><?php esc_html_e( 'Municipality of Birth', 'self-check-in' );?></label></th>
                  <td colspan="1">
                    <div class="mphb-ctrl-wrapper mphb-ctrl mphb-ctrl-text" data-type="text" data-inited="true">
                      <select name="municipalities[]" id="municipalities<?php echo esc_html($i); ?>">
                        <option value=""><?php esc_html_e( 'Select', 'self-check-in' );?></option>
                        
                      </select>
                    </div>
                  </td>
                </tr>
              <?php }?>
              <tr>
                <th><label for=""><?php esc_html_e( 'Guest Type', 'self-check-in' );?></label></th>
                <td colspan="1">
                  <div class="mphb-ctrl-wrapper mphb-ctrl mphb-ctrl-text" data-type="text" data-inited="true">
                    <select name="house[]" id="house<?php echo esc_html($i); ?>">
                      <option value=""><?php esc_html_e( 'Select', 'self-check-in' );?></option>
                      <?php 
                      $line = wpsci_get_house_list();
                      for ($j=0; $j < sizeof($line); $j++) { 
                        $item = $line[$j];
                        ?>
                      <option class="<?php if($i>1){if($item[0]==16 || $item[0]==17 ||$item[0]==18) echo "dis-none";}?>" value="<?php echo esc_html($item[0]);?>" <?php if($item[0]==$value["house"]) echo "selected";?>><?php echo esc_html($item[1]);?></option>
                      <?php
                      }
                      ?>
                    </select>
                  </div>
                </td>
              </tr>
              <tr>
                <th><label for=""><?php esc_html_e( 'Citizenship', 'self-check-in' );?></label></th>
                <td colspan="1">
                  <div class="mphb-ctrl-wrapper mphb-ctrl mphb-ctrl-text" data-type="text" data-inited="true">
                    <select name="citizenship[]" id="citizenship<?php echo esc_html($i+1); ?>">
                      <option value=""><?php esc_html_e( 'Select', 'self-check-in' );?></option>
                      <?php 
                      $line = wpsci_get_country_list();
                      for ($j=0; $j < sizeof($line); $j++) { 
                        $item = $line[$j];
                        ?>
                        <option value="<?php echo esc_html($item[0]);?>" data-val="<?php echo esc_html($item[1]);?>" <?php if($item[0]==$value["citizenship"]) echo "selected";?>><?php echo esc_html($item[1]);?></option>
                        <?php
                      }
                      ?>
                    </select>
                  </div>
                </td>
              </tr>
              <tr class="<?php if($i>1) echo "dis-none";?>">
                <th><label for=""><?php esc_html_e( 'Document Type', 'self-check-in' );?></label></th>
                <td colspan="1">
                  <div class="mphb-ctrl-wrapper mphb-ctrl mphb-ctrl-text" data-type="text" data-inited="true">
                    <select name="doc_type[]" id="doc_type<?php echo esc_html($i); ?>">
                      <option value=""><?php esc_html_e( 'Select', 'self-check-in' );?></option>
                      <?php 
                      $line = wpsci_get_document_list();
                      for ($j=0; $j < sizeof($line); $j++) { 
                        $item = $line[$j];
                        ?>
                        <option value="<?php echo esc_html($item[0]);?>" <?php if($item[0]==$value["doc_type"]) echo "selected";?>><?php echo esc_html($item[1]);?></option>
                        <?php
                      }
                      ?>
                    </select>
                  </div>
                </td>
              </tr>
              <tr class="<?php if($i>1) echo "dis-none";?>">
                <th><label for=""><?php esc_html_e( 'Document Number', 'self-check-in' );?></label></th>
                <td colspan="1">
                  <div class="mphb-ctrl-wrapper mphb-ctrl mphb-ctrl-text" data-type="text" data-inited="true">
                    <input name="doc_number[]" id="doc_number<?php echo esc_html($i); ?>" value="<?php echo esc_html($value['doc_number']); ?>" class=" regular-text" type="text">
                  </div>
                </td>
              </tr>
              <tr id="poid_country<?php echo esc_html($i);?>" class="<?php if($i>1) echo "dis-none";?>">
                <th><label for=""><?php esc_html_e( 'Place of Issue of Document', 'self-check-in' );?></label></th>
                <td colspan="1">
                  <div class="mphb-ctrl-wrapper mphb-ctrl mphb-ctrl-text" data-type="text" data-inited="true">
                  <select name="doc_issue_place[]" id="doc_issue_place<?php echo esc_html($i); ?>" onchange="get_doc_place(<?php echo esc_html($i);?>)">
                    <option value=""><?php esc_html_e( 'Select', 'self-check-in' );?></option>
                    <?php 
                    $line = wpsci_get_country_list();
                    for ($j=0; $j < sizeof($line); $j++) { 
                      $item = $line[$j];
                      ?>
                      <option value="<?php echo esc_html($item[0]);?>" data-val="<?php echo esc_html($item[1]);?>" <?php if($item[0]==$value["doc_issue_place"]) echo "selected";?>><?php echo esc_html($item[1]);?></option>
                      <?php
                    }
                    ?>
                  </select>
                  </div>
                </td>
              </tr>
              <?php if($value["doc_issue_place"]==100000100){?>
              <tr id="doc_provinces<?php echo esc_html($i); ?>">
                <th><label for=""><?php esc_html_e( 'Province of Issue Document', 'self-check-in' );?></label></th>
                <td colspan="1">
                  <div class="mphb-ctrl-wrapper mphb-ctrl mphb-ctrl-text" data-type="text" data-inited="true">
                    <select name="doc_issue_province[]" id="doc_issue_province<?php echo esc_html($i); ?>" onchange="get_doc_municipal(<?php echo esc_html($i);?>)">
                      <option value=""><?php esc_html_e( 'Select', 'self-check-in' );?></option>
                      <?php 
                      $province=array();
                      $line = wpsci_get_municipal_list();
                      for ($j=0; $j < sizeof($line); $j++) { 
                        $item = $line[$j];
            
                        $province[$item[2]]=$item[3];
                      
                      }
                      asort($province);
                      foreach ($province as $key=>$val) { 
                        
                        ?>
                      <option value="<?php echo esc_html($key);?>" <?php if($key==$value["doc_issue_province"]) echo "selected";?>><?php echo esc_html($val); ?></option>
                      <?php
                      }
                      ?>
                    </select>
                  </div>
                </td>
              </tr>
              <tr id="doc_municipal<?php echo esc_html($i); ?>">
                <th><label for=""><?php esc_html_e( 'Municipality of Issue Document', 'self-check-in' );?></label></th>
                <td colspan="1">
                  <div class="mphb-ctrl-wrapper mphb-ctrl mphb-ctrl-text" data-type="text" data-inited="true">
                    <select name="doc_issue_municipality[]" id="doc_issue_municipality<?php echo esc_html($i); ?>">
                      <option value=""><?php esc_html_e( 'Select', 'self-check-in' );?></option>
                      <?php 
                      $municipal_list = wpsci_get_selected_municipal($value["doc_issue_province"]);
                      foreach($municipal_list as $key => $item){
                        ?>
                        <option value="<?php echo esc_html($item[0]);?>" data-provice="<?php echo esc_html($item[2]);?>" <?php if($item[0]==$value["doc_issue_municipality"]) echo "selected";?>><?php echo esc_html($item[1]);?></option>
                        <?php
                      }
                      ?>
                    </select>
                  </div>
                </td>
              </tr>
              <?php }else {?>
                <tr id="doc_provinces<?php echo esc_html($i); ?>" class="dis-none">
                <th><label for=""><?php esc_html_e( 'Province of Issue Document', 'self-check-in' );?></label></th>
                <td colspan="1">
                  <div class="mphb-ctrl-wrapper mphb-ctrl mphb-ctrl-text" data-type="text" data-inited="true">
                    <select name="doc_issue_province[]" id="doc_issue_province<?php echo esc_html($i); ?>" onchange="get_doc_municipal(<?php echo esc_html($i);?>)">
                      <option value=""><?php esc_html_e( 'Select', 'self-check-in' );?></option>
                      <?php 
                      $province=array();
                      $line = wpsci_get_municipal_list();
                      for ($j=0; $j < sizeof($line); $j++) { 
                        $item = $line[$j];
            
                        $province[$item[2]]=$item[3];
                      
                      }
                      asort($province);
                      foreach ($province as $key=>$val) { 
                        
                        ?>
                      <option value="<?php echo esc_html($key);?>" <?php if($key==$value["doc_issue_province"]) echo "selected";?>><?php echo esc_html($val); ?></option>
                      <?php
                      }
                      ?>
                    </select>
                  </div>
                </td>
              </tr>
              <tr id="doc_municipal<?php echo esc_html($i); ?>" class="dis-none">
                <th><label for=""><?php esc_html_e( 'Municipality of Issue Document', 'self-check-in' );?></label></th>
                <td colspan="1">
                  <div class="mphb-ctrl-wrapper mphb-ctrl mphb-ctrl-text" data-type="text" data-inited="true">
                    <select name="doc_issue_municipality[]" id="doc_issue_municipality<?php echo esc_html($i); ?>">
                      <option value=""><?php esc_html_e( 'Select', 'self-check-in' );?></option>
                      
                    </select>
                  </div>
                </td>
              </tr>
              <?php }
                
              if(get_option( 'wpsci_document_field' )){
              ?>
              <tr>
                <th><label for=""><?php esc_html_e( 'Upload document image', 'self-check-in' );?></label></th>
                <td colspan="1">
                  <div class="mphb-ctrl-wrapper mphb-ctrl mphb-ctrl-text doc-image-wrapper" data-type="text" data-inited="true">

                    <div class="image-preview-container">
                      <input type="hidden" name="removed_images[]" class="removed_images" value="">
                      <input name="doc_img[<?php echo esc_html($i-1); ?>][]" id="doc_img<?php echo esc_html($i); ?>" class="doc_img regular-text" type="file" accept=".png,.jpg,application/pdf"  multiple>
                      <input name="doc_img_real[]" value="<?php echo $value['doc_image'] ? esc_html(stripslashes(str_replace('"', "'", $value['doc_image']))) : '';?>" type="hidden" id="doc_img_real<?php echo esc_html($i);?>">
                      <div class="image-preview-div">
                        <div class="image-preview">
                          <?php if( $value['doc_image']){ 
                            $files = unserialize($value['doc_image']);?>
                            
                            <div class="old-images">
                              <?php foreach($files as $file){

                                $path_extension = pathinfo($file, PATHINFO_EXTENSION);
                                if($file && $path_extension == 'pdf'){ ?>

                                  <span class="pdffile">
                                    <a download href="<?php echo esc_url(WPSCI_UPLOAD_URL)."/".esc_html($file); ?>" target="_blank">
                                    <svg height="90px" width="90px" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 512 512" xml:space="preserve" fill="#000000"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path style="fill:#E2E5E7;" d="M128,0c-17.6,0-32,14.4-32,32v448c0,17.6,14.4,32,32,32h320c17.6,0,32-14.4,32-32V128L352,0H128z"></path> <path style="fill:#B0B7BD;" d="M384,128h96L352,0v96C352,113.6,366.4,128,384,128z"></path> <polygon style="fill:#CAD1D8;" points="480,224 384,128 480,128 "></polygon> <path style="fill:#F15642;" d="M416,416c0,8.8-7.2,16-16,16H48c-8.8,0-16-7.2-16-16V256c0-8.8,7.2-16,16-16h352c8.8,0,16,7.2,16,16 V416z"></path> <g> <path style="fill:#FFFFFF;" d="M101.744,303.152c0-4.224,3.328-8.832,8.688-8.832h29.552c16.64,0,31.616,11.136,31.616,32.48 c0,20.224-14.976,31.488-31.616,31.488h-21.36v16.896c0,5.632-3.584,8.816-8.192,8.816c-4.224,0-8.688-3.184-8.688-8.816V303.152z M118.624,310.432v31.872h21.36c8.576,0,15.36-7.568,15.36-15.504c0-8.944-6.784-16.368-15.36-16.368H118.624z"></path> <path style="fill:#FFFFFF;" d="M196.656,384c-4.224,0-8.832-2.304-8.832-7.92v-72.672c0-4.592,4.608-7.936,8.832-7.936h29.296 c58.464,0,57.184,88.528,1.152,88.528H196.656z M204.72,311.088V368.4h21.232c34.544,0,36.08-57.312,0-57.312H204.72z"></path> <path style="fill:#FFFFFF;" d="M303.872,312.112v20.336h32.624c4.608,0,9.216,4.608,9.216,9.072c0,4.224-4.608,7.68-9.216,7.68 h-32.624v26.864c0,4.48-3.184,7.92-7.664,7.92c-5.632,0-9.072-3.44-9.072-7.92v-72.672c0-4.592,3.456-7.936,9.072-7.936h44.912 c5.632,0,8.96,3.344,8.96,7.936c0,4.096-3.328,8.704-8.96,8.704h-37.248V312.112z"></path> </g> <path style="fill:#CAD1D8;" d="M400,432H96v16h304c8.8,0,16-7.2,16-16v-16C416,424.8,408.8,432,400,432z"></path> </g></svg>
                                    </a>
                                  </span>
                                  
                                <?php }else if($file!=""){
                                  ?>
                                  <div class="image-preview-item">
                                    <img class="preview-image" src="<?php echo esc_url(WPSCI_UPLOAD_URL)."/".esc_html($file); ?>">
                                    <span class="remove-btn old-img" data-val="<?php echo esc_html($file); ?>">&times;</span>
                                  </div>
                                  <?php
                                }
                              } ?>
                            </div>
                          <?php }?>
                        </div>
                      </div>
                    </div>
                  </div>

                </td>
              </tr>
              <?php }?>
            </tbody>
          </table>
          <hr>
          </div>
            <?php } ?>
          
        <!-- to display the additional rows -->
        <div class="additional-table-rows" id="additional-table-rows">

        </div>

          <div class="add-row">
            <center><button class="button add-guest" data-path = "<?php echo esc_url(WPSCI_PLUGIN_URL) . 'assets/documents/'; ?>" type="button"><?php esc_html_e('Add Row', 'self-check-in' );?></button></center>
          </div>

        <hr>

        <div class="col-12 mt-3 mb-3">
          <button type="submit" name="wpsci_update_overview" class="button button-primary"><?php esc_html_e( 'Update', 'self-check-in' );?></button>
          <a href="<?php echo esc_url(menu_page_url('self-check-in', false));?>" class="button button-secondary"><?php esc_html_e( 'Back', 'self-check-in' );?></a>
          </a>
        </div>
      </form>
    </div>
  </div>
</div>