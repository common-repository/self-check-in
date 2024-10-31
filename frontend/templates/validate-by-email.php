<?php
if (!defined( 'ABSPATH') ) exit; // Exit if accessed directly
?>
<div class="entry-content" id="verify_email">
    <div class="wpsci-main-wrapper ">
        <div class="table-res">
            <form method="POST" id="verify_email_form" enctype="multipart/form-data">
                <?php wp_nonce_field('wpsci_verify_email_nonce', 'wpsci_verify_nonce');?>
                <div class="table-res" id="guest_form_add">
                    <section id="mphb-customer-details" class="mphb-checkout-section mphb-customer-details">
                        <h3 class="mphb-customer-details-title">
                            <?php esc_html_e( 'Please enter booking email to verify.', 'self-check-in' );?>
                        </h3>
                        
                        <p class="mphb-customer-first-name mphb-customer-name mphb-text-control">
                            <label for="mphb_first_name">
                            <?php esc_html_e( 'Email', 'self-check-in' );?>&nbsp; <abbr title="required">*</abbr>
                            </label><br>
                            <input name="email" id="email" class="regular-text" type="text">
                        </p>
                    
                    </section>
                    <div class="col-12 mt-3 mb-3">
                        <button type="submit" name="wpsci_verify_email" class="button button-primary"><?php esc_html_e( 'Verify', 'self-check-in' );?></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>