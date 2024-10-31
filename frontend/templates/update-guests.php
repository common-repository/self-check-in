<?php
if (!defined( 'ABSPATH') ) exit; // Exit if accessed directly
?>
<div class="entry-content dis-none" id="edit_wrap">
    <div class="wpsci-main-wrapper ">
        <div class="table-res" id="guest_form_edit">
            <form method="POST" id="update_form" enctype="multipart/form-data">
                <?php wp_nonce_field('wpsci_submit_guests_nonce', 'wpsci_verify_nonce');?>
                <div class="table-res" id="guest_form_add">
                    <h4><?php esc_html_e( 'Guest Information for Booking', 'self-check-in' );?> #<?php echo esc_html($booking_id);?></h4>
                    <?php 
                    $i = 0;
                    foreach ($result as $key => $value) {
                    $i++;?>
                    <section id="mphb-customer-details" class="mphb-checkout-section mphb-customer-details">
                    <h3 class="mphb-customer-details-title">
                        <?php echo esc_html($i);?>. <?php esc_html_e( 'Guest Information', 'self-check-in' );?>
                    </h3>
    
                    <p class="mphb-required-fields-tip">
                        <small>
                        <?php esc_html_e( 'Required fields are followed by', 'self-check-in' );?> <abbr title="required">*</abbr>
                        </small>
                    </p>
                    <p class="mphb-customer-first-name mphb-customer-name mphb-text-control">
                        <label for="mphb_first_name">
                        <?php esc_html_e( 'Name', 'self-check-in' );?>&nbsp; <abbr title="required">*</abbr>
                        </label><br>
                        <input name="first_name[]" id="first_name<?php echo esc_html($i);?>" value="<?php echo (wpsci_check_booking_type($booking_id) == 'mphb' && $i==1) ? esc_html(get_post_meta($booking_id, 'mphb_first_name', true)) : esc_html($value['first_name']);?>" class=" regular-text <?php echo (wpsci_check_booking_type($booking_id) == 'mphb' && $i==1) ? 'disabled-field':'';?>" type="text" <?php echo (wpsci_check_booking_type($booking_id) == 'mphb' && $i==1) ? 'readonly':'';?>>
                    </p>
                    <p class="mphb-customer-last-name mphb-text-control">
                        <label for="">
                        <?php esc_html_e( 'Surname', 'self-check-in' );?>&nbsp; <abbr title="required">*</abbr>
                        </label><br>
                        <input name="last_name[]" id="last_name<?php echo esc_html($i);?>" value="<?php echo (wpsci_check_booking_type($booking_id) == 'mphb' && $i==1) ? esc_html(get_post_meta($booking_id, 'mphb_last_name', true)) : esc_html($value['last_name']);?>" class=" regular-text <?php echo (wpsci_check_booking_type($booking_id) == 'mphb' && $i==1) ? 'disabled-field':'';?>" type="text" <?php echo (wpsci_check_booking_type($booking_id) == 'mphb' && $i==1) ? 'readonly':'';?>>
                    </p>

                    <?php if($i==1){?>
                        <p class="mphb-customer-last-name mphb-text-control">
                            <label for="">
                            <?php esc_html_e( 'Email', 'self-check-in' );?>&nbsp; <abbr title="required">*</abbr>
                            </label><br>
                            <input name="email[]" id="email<?php echo esc_html($i);?>" value="<?php echo (wpsci_check_booking_type($booking_id) == 'mphb' && $i==1) ? esc_html(get_post_meta($booking_id, 'mphb_email', true)) : esc_html($value['email']);?>" class=" regular-text <?php echo (wpsci_check_booking_type($booking_id) == 'mphb' && $i==1) ? 'disabled-field':'';?>" type="text" <?php echo (wpsci_check_booking_type($booking_id) == 'mphb' && $i==1) ? 'readonly':'';?>>
                        </p>
                    <?php }elseif(get_option('wpsci_guests_email') =='1'){?>
                        <p class="mphb-customer-last-name mphb-text-control">
                            <label for="">
                            <?php esc_html_e( 'Email', 'self-check-in' );?>&nbsp; <abbr title="required">*</abbr>
                            </label><br>
                            <input name="email[]" id="email<?php echo esc_html($i);?>" value="<?php echo (wpsci_check_booking_type($booking_id) == 'mphb' && $i==1) ? esc_html(get_post_meta($booking_id, 'mphb_email', true)) : esc_html($value['email']);?>" class=" regular-text <?php echo (wpsci_check_booking_type($booking_id) == 'mphb' && $i==1) ? 'disabled-field':'';?>" type="text" <?php echo (wpsci_check_booking_type($booking_id) == 'mphb' && $i==1) ? 'readonly':'';?>>
                        </p>
                    <?php }?>

                    <?php if($i==1){?>
                        <p class="mphb-customer-last-name mphb-text-control">
                            <label for="">
                            <?php esc_html_e( 'Phone', 'self-check-in' );?>&nbsp; <abbr title="required">*</abbr>
                            </label><br>
                            <input name="phone[]" id="phone<?php echo esc_html($i);?>" value="<?php echo (wpsci_check_booking_type($booking_id) == 'mphb' && $i==1) ? esc_html(get_post_meta($booking_id, 'mphb_phone', true)) : esc_html($value['phone']);?>" class=" regular-text <?php echo (wpsci_check_booking_type($booking_id) == 'mphb' && $i==1) ? 'disabled-field':'';?>" type="text" <?php echo (wpsci_check_booking_type($booking_id) == 'mphb' && $i==1) ? 'readonly':'';?>>
                        </p>
                    <?php }elseif(get_option('wpsci_guests_phone') =='1'){?>
                        <p class="mphb-customer-last-name mphb-text-control">
                            <label for="">
                            <?php esc_html_e( 'Phone', 'self-check-in' );?>&nbsp; <abbr title="required">*</abbr>
                            </label><br>
                            <input name="phone[]" id="phone<?php echo esc_html($i);?>" value="<?php echo (wpsci_check_booking_type($booking_id) == 'mphb' && $i==1) ? esc_html(get_post_meta($booking_id, 'mphb_phone', true)) : esc_html($value['phone']);?>" class=" regular-text <?php echo (wpsci_check_booking_type($booking_id) == 'mphb' && $i==1) ? 'disabled-field':'';?>" type="text" <?php echo (wpsci_check_booking_type($booking_id) == 'mphb' && $i==1) ? 'readonly':'';?>>
                        </p>
                    <?php }?>

                    <p class="mphb-customer-last-name mphb-text-control">
                        <label for="">
                        <?php esc_html_e( 'Sex', 'self-check-in' );?>&nbsp; <abbr title="required">*</abbr>
                        </label><br>
                        <select name="sex[]" id="sex<?php echo esc_html($i); ?>">
                        <option value=""><?php esc_html_e( 'Select', 'self-check-in' );?></option>
                        <option value="male" <?php if("male"==$value["sex"]) echo "selected";?>><?php esc_html_e( 'Male', 'self-check-in' );?></option>
                        <option value="female" <?php if("female"==$value["sex"]) echo "selected";?>><?php esc_html_e( 'Female', 'self-check-in' );?></option>
                        </select>
                    </p>
                    <p class="mphb-customer-last-name mphb-text-control">
                        <label for="">
                        <?php esc_html_e( 'Date of Birth', 'self-check-in' );?>&nbsp; <abbr title="required">*</abbr>
                        </label><br>
                        <input name="dob[]" id="dob<?php echo esc_html($i);?>" value="<?php echo esc_html($value['dob']);?>" class=" regular-text" type="date">
                    </p>
                    <p class="mphb-customer-last-name mphb-text-control">
                        <label for="">
                        <?php esc_html_e( 'Country of Birth', 'self-check-in' );?>&nbsp; <abbr title="required">*</abbr>
                        </label><br>
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
                    </p>
                    <?php if($value["country_code"]==100000100){?>
                    <p class="mphb-customer-last-name mphb-text-control" id="provinces<?php echo esc_html($i);?>">
                        <label for="">
                        <?php esc_html_e( 'Province of Birth', 'self-check-in' );?>&nbsp; <abbr title="required">*</abbr>
                        </label><br>
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
                            <option value="<?php echo esc_html($key)?>" <?php if($key==$value["provinces"]) echo "selected";?>><?php echo esc_html($val); ?></option>
                            <?php
                        }
                        ?>
                        </select>
                    </p>
                    <p class="mphb-customer-last-name mphb-text-control" id="municipal<?php echo esc_html($i);?>">
                        <label for="">
                        <?php esc_html_e( 'Municipality of Birth', 'self-check-in' );?>&nbsp; <abbr title="required">*</abbr>
                        </label><br>
                        <select name="municipalities[]" id="municipalities<?php echo esc_html($i); ?>">
                        <option value=""><?php esc_html_e( 'Select', 'self-check-in' );?></option>
                        <?php 
                        $municipal_list = wpsci_get_selected_municipal($value["provinces"]);
                        foreach($municipal_list as $key => $item) {
                            ?>
                            <option value="<?php echo esc_html($item[0]);?>" data-provice="<?php echo esc_html($item[2]);?>" <?php if($item[0]==$value["municipalities"]) echo "selected";?>><?php echo esc_html($item[1]);?></option>
                            <?php
                        }
                        ?>
                        </select>
                    </p>
                    <?php } else{?>
                    <p class="mphb-customer-last-name mphb-text-control dis-none" id="provinces<?php echo esc_html($i);?>">
                        <label for="">
                        <?php esc_html_e( 'Province of Birth', 'self-check-in' );?>&nbsp; <abbr title="required">*</abbr>
                        </label><br>
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
                            <option value="<?php echo esc_html($key)?>" <?php if($key==$value["provinces"]) echo "selected";?>><?php echo esc_html($val); ?></option>
                            <?php
                        }
                        ?>
                        </select>
                    </p>
                    <p class="mphb-customer-last-name mphb-text-control dis-none" id="municipal<?php echo esc_html($i);?>">
                        <label for="">
                        <?php esc_html_e( 'Municipality of Birth', 'self-check-in' );?>&nbsp; <abbr title="required">*</abbr>
                        </label><br>
                        <select name="municipalities[]" id="municipalities<?php echo esc_html($i); ?>">
                        <option value=""><?php esc_html_e( 'Select', 'self-check-in' );?></option>
                        
                        </select>
                    </p>
                    <?php }?>
                    <p class="mphb-customer-last-name mphb-text-control guest_type"  id="guest_type<?php echo esc_html($i); ?>">
                        <label for="">
                        <?php esc_html_e( 'Guest Type', 'self-check-in' );?>&nbsp; <abbr title="required">*</abbr>
                        </label><br>
                        <select name="house[]" id="house<?php echo esc_html($i); ?>" <?php echo $i==1 ? 'readonly':'';?> required>
                        <option value=""><?php esc_html_e( 'Select', 'self-check-in' );?></option>
                        <?php 
                        $line = wpsci_get_house_list();
                        for ($j=0; $j < sizeof($line); $j++) { 
                            $item = $line[$j];
                            if($i!=1){
                                if($j<=2) continue;
                            }
                            ?>
                            <option value="<?php echo esc_html($item[0]);?>" <?php if($item[0]==$value["house"]) echo "selected";?>><?php echo esc_html($item[1]);?></option>
                            <?php
                            if($i==1){
                                if($j==2) break;
                            }
                        }
                        ?>
                        </select>
                    </p>
                    <p class="mphb-customer-last-name mphb-text-control">
                        <label for="">
                        <?php esc_html_e( 'Citizenship', 'self-check-in' );?>&nbsp; <abbr title="required">*</abbr>
                        </label><br>
                        <select name="citizenship[]" id="citizenship<?php echo esc_html($i); ?>">
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
                    </p>
                    <p class="mphb-customer-last-name mphb-text-control <?php if($i>1) echo "dis-none";?>">
                        <label for="">
                        <?php esc_html_e( 'Document Type', 'self-check-in' );?>&nbsp; <abbr title="required">*</abbr>
                        </label><br>
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
                    </p>
                    <p class="mphb-customer-last-name mphb-text-control <?php if($i>1) echo "dis-none";?>">
                        <label for="">
                        <?php esc_html_e( 'Document Number', 'self-check-in' );?>&nbsp; <abbr title="required">*</abbr>
                        </label><br>
                        <input name="doc_number[]" id="doc_number<?php echo esc_html($i);?>" value="<?php echo esc_html($value['doc_number']);?>" class=" regular-text" type="text">
                    </p>
                    <p class="mphb-customer-last-name mphb-text-control <?php if($i>1) echo "dis-none";?>" id="poid_country<?php echo esc_html($i);?>">
                        <label for="">
                        <?php esc_html_e( 'Place of Issue of Document', 'self-check-in' );?>&nbsp; <abbr title="required">*</abbr>
                        </label><br>
                        <select name="doc_issue_place[]" id="doc_issue_place<?php echo esc_html($i); ?>" onchange="get_doc_place(<?php echo esc_html($i); ?>)">
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
                    </p>
                    <?php if($value["doc_issue_place"]==100000100){?>
                    <p class="mphb-customer-last-name mphb-text-control" id="doc_provinces<?php echo esc_html($i);?>">
                        <label for="">
                        <?php esc_html_e( 'Province of Issue Document', 'self-check-in' );?>&nbsp; <abbr title="required">*</abbr>
                        </label><br>
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
                            <option value="<?php echo esc_html($key)?>" <?php if($key==$value["doc_issue_province"]) echo "selected";?>><?php echo esc_html($val); ?></option>
                            <?php
                        }
                        ?>
                        </select>
                    </p>
                    <p class="mphb-customer-last-name mphb-text-control" id="doc_municipal<?php echo esc_html($i);?>">
                        <label for="">
                        <?php esc_html_e( 'Municipality of Issue Document', 'self-check-in' );?>&nbsp; <abbr title="required">*</abbr>
                        </label><br>
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
                    </p>
                    <?php } else{?>
                    <p class="mphb-customer-last-name mphb-text-control dis-none" id="doc_provinces<?php echo esc_html($i);?>">
                        <label for="">
                        <?php esc_html_e( 'Province of Issue Document', 'self-check-in' );?>&nbsp; <abbr title="required">*</abbr>
                        </label><br>
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
                            <option value="<?php echo esc_html($key)?>" <?php if($key==$value["doc_issue_province"]) echo "selected";?>><?php echo esc_html($val); ?></option>
                            <?php
                        }
                        ?>
                        </select>
                    </p>
                    <p class="mphb-customer-last-name mphb-text-control dis-none" id="doc_municipal<?php echo esc_html($i);?>">
                        <label for="">
                        <?php esc_html_e( 'Municipality of Issue Document', 'self-check-in' );?>&nbsp; <abbr title="required">*</abbr>
                        </label><br>
                        <select name="doc_issue_municipality[]" id="doc_issue_municipality<?php echo esc_html($i); ?>">
                            <option value=""><?php esc_html_e( 'Select', 'self-check-in' );?></option>
                        
                        </select>
                    </p>
                    <?php }?>
                    <?php if(get_option( 'wpsci_document_field' )){?>
                    <div class="public-img mb-3">
                        <div class="mphb-customer-last-name mphb-text-control doc-image-wrapper">
                            
                            <label for="">
                            <?php esc_html_e( 'Upload document image', 'self-check-in' );?>&nbsp; <abbr title="required">*</abbr>
                            </label><br>

                            <div class="image-preview-container">

                                <input type="hidden" name="removed_images[]" class="removed_images" value="">
                                <input name="doc_img[<?php echo esc_html($i-1);?>][]" id="doc_img<?php echo esc_html($i);?>" class="doc_img regular-text" type="file" accept=".png,.jpg,application/pdf" multiple>
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
                                                
                                                <?php }
                                            elseif($file!=""){?>
                                            <div class="image-preview-item">
                                                <img class="preview-image" src="<?php echo esc_html(WPSCI_UPLOAD_URL)."/".esc_html($file) ?>">
                                                <span class="remove-btn old-img" data-val="<?php echo esc_html($file); ?>">&times;</span>
                                            </div>
                                            <?php }
                                            } ?>
                                        </div>
                                        <?php }?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php }?>
                    </section>
                    <?php }?>
                    <div class="col-12 mt-3 mb-3">
                        <button type="submit" name="wpsci_submit_guests" class="button button-primary"><?php esc_html_e( 'Update', 'self-check-in' );?></button>
                        <button type="button" onclick="back_form()" class="button button-primary"><?php esc_html_e( 'Back', 'self-check-in' );?></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>