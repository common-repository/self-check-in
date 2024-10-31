<?php
if (!defined( 'ABSPATH') ) exit; // Exit if accessed directly

  $sql = "SELECT post_title,post_name FROM {$wpdb->base_prefix}posts WHERE post_type='page' AND post_status='%s'";
  $status = 'publish';
  $pages = $GLOBALS['wpdb']->get_results($wpdb->prepare($sql, $status), ARRAY_A);

  $wpsci_plugin = get_option( 'wpsci_plugin' );
  $_document_field = get_option( 'wpsci_document_field' );
  $_wpsci_guests_email = get_option( 'wpsci_guests_email' );
  $_wpsci_guests_phone = get_option( 'wpsci_guests_phone' );

?>

<div class="wrap">

  <?php 
  /**
   * include tabs template
   */
  include_once WPSCI_PLUGIN_PATH . 'admin/tabs/tabs.php';?>

  <div class="metabox-holder">        
    <div class="postbox" id="setting_wrap">
      <h3><?php echo esc_html__( 'Plugin Settings', 'self-check-in' );?></h3>
      <div class="inside">
        <div class="digital-setting wrap">
          <form method="post" action="<?php echo esc_html(admin_url('admin-post.php')); ?>">
            <input type="hidden" name="action" value="wpsci_settings">
            <?php wp_nonce_field('wpsci_settings_nonce'); ?>

            <table class="form-table">
              <tr>
                <th><label for="digital_plugin"><?php echo esc_html__( 'Plugin Enable/Disable', 'self-check-in' );?></label></th>
                <td>
                  <select name="digital_plugin" id="digital_plugin" class="digital_plugin">
                    <option value="1" <?php if($wpsci_plugin==1) echo "selected";?>><?php echo esc_html__( 'Enable', 'self-check-in' );?></option>
                    <option value="0" <?php if($wpsci_plugin==0) echo "selected";?>><?php echo esc_html__( 'Disable', 'self-check-in' );?></option>
                  </select> 
                </td>
              </tr>
              <tr>
                <th><label for="_document_field"><?php echo esc_html__( 'Document Upload Field', 'self-check-in' );?></label></th>
                <td>
                  <select name="_document_field" id="_document_field" class="digital_plugin">
                    <option value="1" <?php if($_document_field==1) echo "selected";?>><?php echo esc_html__( 'Enable', 'self-check-in' );?></option>
                    <option value="0" <?php if($_document_field==0) echo "selected";?>><?php echo esc_html__( 'Disable', 'self-check-in' );?></option>
                  </select> 
                </td>
              </tr>
              <tr>
                <th><label for=""><?php echo esc_html__( 'Guests Email', 'self-check-in' );?></label></th>
                <td>
                  <select name="_wpsci_guests_email" id="_wpsci_guests_email" class="digital_plugin">
                    <option value="1" <?php if($_wpsci_guests_email==1) echo "selected";?>><?php echo esc_html__( 'Enable', 'self-check-in' );?></option>
                    <option value="0" <?php if($_wpsci_guests_email==0) echo "selected";?>><?php echo esc_html__( 'Disable', 'self-check-in' );?></option>
                  </select> 
                </td>
              </tr>
              <tr>
                <th><label for=""><?php echo esc_html__( 'Guests Phone', 'self-check-in' );?></label></th>
                <td>
                  <select name="_wpsci_guests_phone" id="_wpsci_guests_phone" class="digital_plugin">
                    <option value="1" <?php if($_wpsci_guests_phone==1) echo "selected";?>><?php echo esc_html__( 'Enable', 'self-check-in' );?></option>
                    <option value="0" <?php if($_wpsci_guests_phone==0) echo "selected";?>><?php echo esc_html__( 'Disable', 'self-check-in' );?></option>
                  </select> 
                </td>
              </tr>
              <tr>
                <th><label for="digital_public_page"><?php echo esc_html__( 'Select Public Page', 'self-check-in' );?></label></th>
                <td>
                  <select name="digital_public_page" id="digital_public_page">
                    <option value=""><?php echo esc_html__( 'Select', 'self-check-in' );?></option>
                    <?php
                    foreach ($pages as $key => $page) {
                    ?><option value="<?php echo esc_html($page['post_name']);?>" <?php if(get_option( 'wpsci_public_page' )==$page['post_name']) echo "selected";?>><?php echo esc_html($page['post_title']);?></option>
                    <?php 
                    }
                    ?>
                  </select>
                </td>
              </tr>
            </table>
            <?php submit_button(); ?>
          </form>
          <br>
          <p>
            <ul>
              <li><?php echo esc_html__( 'Shortcode for Public Page', 'self-check-in' );?>:</li>
              <li><b>[wpsci_form]</b></li>
            </ul>
          </p>
          <?php if(wpsci_is_mphb_active()){?>
          <p>
            <ul>
              <li>MPHB <?php echo esc_html__( 'Add-on', 'self-check-in' );?>:</li>
              <li><?php echo esc_html__( 'Get form link with following email tag', 'self-check-in' );?></li>
              <li><b>%wpsci_form_url%</b></li>
            </ul>
          </p>
          <?php }?>
        </div>
      </div>
    </div>
  </div>
</div>
