<?php
if (!defined( 'ABSPATH') ) exit; // Exit if accessed directly
?>
<div class="table-res container" id="guest_table">
    <h4><?php echo esc_html__( 'Guest Information for Booking', 'self-check-in' );?> #<?php echo esc_html($booking_id);?></h4>
    <table class="form-table">
    <thead>
        <th width="10%"><?php echo esc_html__( 'Sr No.', 'self-check-in' );?></th>
        <th width="15%"><?php echo esc_html__( 'Name', 'self-check-in' );?></th>
        <th width="15%"><?php echo esc_html__( 'Date of Birth', 'self-check-in' );?></th>
        <th width="15%"><?php echo esc_html__( 'Country of Birth', 'self-check-in' );?></th>
        <th width="15%"><?php echo esc_html__( 'Document Type', 'self-check-in' );?></th>
        <th width="15%"><?php echo esc_html__( 'Document Number', 'self-check-in' );?></th>
        <?php if(get_option( 'wpsci_document_field' )){?>
            <th width="15%"><?php echo esc_html__( 'Document Image', 'self-check-in' );?></th>
        <?php }?>
    </thead>
    <tbody>
        <?php
        $i = 0;
        foreach ($result as $key => $value) {
            $i++;
        ?>

        <tr class="mphb-customer-field-wrap">
            <td>
                <?php echo esc_html($i);?>
            </td>
            <td>
                <?php echo esc_html($value['first_name']).' '.esc_html($value['last_name']);?>
            </td>
            <td>
                <?php if($value['dob'] && $value['dob']!='0000-00-00')
                echo esc_html(gmdate("d-m-Y", strtotime($value['dob'])));?>
            </td>
            <td>
                <?php echo esc_html($value['country']);?>
            </td>
            <td>
                <?php echo esc_html($value['doc_type']);?>
            </td>
            <td>
                <?php echo esc_html($value['doc_number']);?>
            </td>
            <?php if(get_option( 'wpsci_document_field' )){?>
            <td>
                <?php 
                if($value['doc_image']){
                $files = unserialize($value['doc_image']);?>
                <span class="image-preview">
                    <?php
                    
                    foreach($files as $file){
                        $path_extension = pathinfo($file, PATHINFO_EXTENSION);
                        if($file && $path_extension == 'pdf'){ ?>

                            <span class="pdffile">
                                <a download href="<?php echo esc_url(WPSCI_UPLOAD_URL)."/".esc_html($file); ?>" target="_blank">
                                <svg height="90px" width="90px" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 512 512" xml:space="preserve" fill="#000000"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path style="fill:#E2E5E7;" d="M128,0c-17.6,0-32,14.4-32,32v448c0,17.6,14.4,32,32,32h320c17.6,0,32-14.4,32-32V128L352,0H128z"></path> <path style="fill:#B0B7BD;" d="M384,128h96L352,0v96C352,113.6,366.4,128,384,128z"></path> <polygon style="fill:#CAD1D8;" points="480,224 384,128 480,128 "></polygon> <path style="fill:#F15642;" d="M416,416c0,8.8-7.2,16-16,16H48c-8.8,0-16-7.2-16-16V256c0-8.8,7.2-16,16-16h352c8.8,0,16,7.2,16,16 V416z"></path> <g> <path style="fill:#FFFFFF;" d="M101.744,303.152c0-4.224,3.328-8.832,8.688-8.832h29.552c16.64,0,31.616,11.136,31.616,32.48 c0,20.224-14.976,31.488-31.616,31.488h-21.36v16.896c0,5.632-3.584,8.816-8.192,8.816c-4.224,0-8.688-3.184-8.688-8.816V303.152z M118.624,310.432v31.872h21.36c8.576,0,15.36-7.568,15.36-15.504c0-8.944-6.784-16.368-15.36-16.368H118.624z"></path> <path style="fill:#FFFFFF;" d="M196.656,384c-4.224,0-8.832-2.304-8.832-7.92v-72.672c0-4.592,4.608-7.936,8.832-7.936h29.296 c58.464,0,57.184,88.528,1.152,88.528H196.656z M204.72,311.088V368.4h21.232c34.544,0,36.08-57.312,0-57.312H204.72z"></path> <path style="fill:#FFFFFF;" d="M303.872,312.112v20.336h32.624c4.608,0,9.216,4.608,9.216,9.072c0,4.224-4.608,7.68-9.216,7.68 h-32.624v26.864c0,4.48-3.184,7.92-7.664,7.92c-5.632,0-9.072-3.44-9.072-7.92v-72.672c0-4.592,3.456-7.936,9.072-7.936h44.912 c5.632,0,8.96,3.344,8.96,7.936c0,4.096-3.328,8.704-8.96,8.704h-37.248V312.112z"></path> </g> <path style="fill:#CAD1D8;" d="M400,432H96v16h304c8.8,0,16-7.2,16-16v-16C416,424.8,408.8,432,400,432z"></path> </g></svg>
                                </a>
                            </span>
                            
                        <?php }elseif($file){

                        echo "<img src='".esc_html(WPSCI_UPLOAD_URL)."/".esc_html($file)."' class='preview-image'>";
                        }
                    }
                    ?>
                </span>
                <?php }?>
            </td>
            <?php }?>
        </tr>
        <?php }?>
    </tbody>
    </table>
    <div class="col-12 mt-3 mb-3">
    <form method="POST" id="edit_form" enctype="multipart/form-data">
        <input type="hidden" name="booking_id">
        <input type="hidden" name="guest">
        <button type="button" name="edit" onclick="edit_form()" class="button button-primary"><?php echo esc_html__( 'Edit', 'self-check-in' );?></button>
    </form>
    </div>
</div>