<?php
if (!defined( 'ABSPATH') ) exit; // Exit if accessed directly

/**
 * Overview.
 */
if (!class_exists('WP_List_Table')) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class WPSCI_LIST_TABLE extends WP_List_Table {

  function __construct() {
    parent::__construct(array(
      'singular' => 'record',
      'plural' => 'records',
      'ajax' => false,
    ));
  }

  function get_bulk_actions() {
    $actions = array(
      'delete'   => 'Delete',
    );
    return $actions;
  }

  function process_bulk_action() {
    global $wpdb;

    if ('delete' === $this->current_action()) {

      // Sanitize the nonce
      $nonce_value = isset($_POST['wpsci_verify_nonce']) ? sanitize_text_field(wp_unslash($_POST['wpsci_verify_nonce'])) : '';
      if (!wp_verify_nonce($nonce_value, 'wpsci_delete_record_nonce')) {
        wp_die('Security check failed');
      }
      // Handle delete action
      $record_ids = isset($_POST['record']) ? sanitize_text_field($_POST['record']) : array();

      foreach ($record_ids as $record_id) { 

        $sql = "Delete from {$wpdb->base_prefix}wpsci_guests where booking_id='%d'";
        $results = $wpdb->query($wpdb->prepare($sql, $record_id));

        $sql = "Delete from {$wpdb->base_prefix}wpsci_check_in where booking_id='%d'";
        $results = $wpdb->query($wpdb->prepare($sql, $record_id));
      
      }   
    }
  }

  function column_cb($item) {
    return sprintf(
      '<input type="checkbox" name="record[]" value="%s" />',
      $item['booking_id']
    );
  }

  function column_default($item, $column_name) {
    return $item[$column_name];
  }

  function column_booking_id($item) {
    return $item['booking_id'];
  }

  function column_first_guest_name($item) {
    return $item['first_guest_name'];
  }

  function column_arrival_date($item) {
    return $item['arrival_date'];
  }

  function column_departure_date($item) {
    return $item['departure_date'];
  }

  function column_total_guests($item) {
    return $item['total_guests'];
  }

  function column_action($item) {
    return $item['action'];
  }

  function get_total_items() {
    global $wpdb;
    $table = $wpdb->base_prefix . 'wpsci_guests';
    $sql = "SELECT COUNT(DISTINCT booking_id) FROM %i";
    return $wpdb->get_var($wpdb->prepare($sql, $table));
  }

  function prepare_items() {
    $this->process_bulk_action();
    global $wpdb;

    $per_page = 20;
    $current_page = $this->get_pagenum();
    $total_items = $this->get_total_items();
    
    $offset = ($current_page - 1) * $per_page;

    $sql = $wpdb->prepare("SELECT * FROM {$wpdb->base_prefix}wpsci_guests GROUP BY booking_id ORDER BY booking_id DESC LIMIT %d OFFSET %d", $per_page, $offset);
    $results = $wpdb->get_results($sql, ARRAY_A);

    $data = array();
    $i = 0;
    foreach($results as $result) {
        $i++;
        $_key = get_post_meta($result['booking_id'], "self-check-in-key", true);
        if(!$_key) $_key = wpsci_get_check_in_key($result['booking_id']);

        $action = '
            <a id="url_'. $i .'" class="dis-none" href="'. WPSCI_SITE_URL.'/'.get_option( 'wpsci_public_page' ).'?id='.$result['booking_id'].'&key='.$_key .'" target="_blank">
            '. WPSCI_SITE_URL.'/'.get_option( 'wpsci_public_page' ).'?id='.$result['booking_id'].'&key='.$_key .'
            </a>

            <button onclick="copyToClipboard(\'#url_'. $i.'\')" id="url_btn_copy'. $i .'" type="button" class="icon-button v-align-middle mg-2x" title="'.esc_html__( 'Copy URL', 'self-check-in' ) .'">
              <span class="dashicons dashicons-share"></span>
            </button>
            <button onclick="copyToClipboard(\'#url_'. $i.'\')" id="url_btn_copied'. $i .'" type="button" class="icon-button v-align-middle mg-2x" style="display:none;" title="'.esc_html__( 'Copied', 'self-check-in' ) .'">
              <span class="dashicons dashicons-share"></span>
            </button>

            <a href="'. menu_page_url('self-check-in', false).'&edit='.$result['booking_id'].'">
            <button type="button" name="edit_overview" class="icon-button" title="'.esc_html__( 'Edit', 'self-check-in' ) .'">
              <span class="dashicons dashicons-edit-page"></span>
            </button>
            </a>

            <form method="POST" id="delete_record_form" class="dis-in-block v-align-middle mg-2x" enctype="multipart/form-data">
              '.wp_nonce_field('wpsci_delete_record_nonce', 'wpsci_verify_nonce').'
              <input type="hidden" name="booking_id" value="'. $result['booking_id'] .'">
              <button type="submit" name="wpsci_delete_record" class="icon-button" title="'.esc_html__( 'Delete', 'self-check-in' ) .'">
                <span class="dashicons dashicons-trash"></span>
              </button>
            </form>
        ';

        $data[]=array(
            'id'=> $result['booking_id'],
            'booking_id'=> $result['booking_id'],
            'first_guest_name'=> $result['first_name'] . ' ' . $result['last_name'],
            'arrival_date'=> wpsci_get_check_in($result['booking_id']),
            'departure_date'=> wpsci_get_check_out($result['booking_id']),
            'total_guests'=> wpsci_get_total_guests($result['booking_id'], true),
            'action'=> $action,
        );
    }

    $columns = $this->get_columns();
    $hidden = array();
    $sortable = $this->get_sortable_columns();

    $this->_column_headers = array($columns, $hidden, $sortable);
    $this->items = $data;

    $this->set_pagination_args(array(
        'total_items' => $total_items,
        'per_page'    => $per_page,
        'total_pages' => ceil($total_items / $per_page),
    ));
  }

  function get_columns() {
    return array(
      'cb'          => '<input type="checkbox" />',
      'booking_id' =>esc_html__('Booking ID', 'self-check-in' ),
      'first_guest_name'   =>esc_html__('First Guest Name', 'self-check-in' ),
      'arrival_date'   =>esc_html__('Arrival Date', 'self-check-in' ),
      'departure_date'   =>esc_html__('Departure Date', 'self-check-in' ),
      'total_guests'   =>esc_html__('Total Guests', 'self-check-in' ),
      'action'   =>esc_html__('Action', 'self-check-in' ),
    );
  }

  function get_sortable_columns() {
    return array(
      'booking_id' => array('Booking ID', false),
    );
  }
}

?>

<div class="wrap">
    
  <?php 
  /**
   * include tabs template
   */
  include_once WPSCI_PLUGIN_PATH . 'admin/tabs/tabs.php';?>

  <div class="metabox-holder" id="overview_wrap">

    <div class="inside">
      <p align="right">
        <button class="create-check-in button button-secondary" onclick="open_modal('#modalCreateCheckIn')">
          <?php echo esc_html__( 'Create Check-In', 'self-check-in' );?>
        </button>
      </p>

      <form method="post">
        <?php
          $wpsci_list_table = new WPSCI_LIST_TABLE();
          $wpsci_list_table->prepare_items();
          $wpsci_list_table->display();
        ?>
      </form>
    </div>
  </div>
</div>


<!-- The Modal -->
<div id="modalCreateCheckIn" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <h3><?php echo esc_html__( 'Create Check-In', 'self-check-in' );?>
      <span onclick="close_modal('#modalCreateCheckIn')" class="close">&times;</span>
      </h3>
    </div>
    <div class="modal-body">
      <form method="POST" id="check_in_form">
        <?php wp_nonce_field('wpsci_save_check_in_nonce', 'wpsci_verify_nonce');?>
        <table class="form-table">
          <tbody>
            <tr>
              <th><label for=""><?php echo esc_html__( 'Booking ID', 'self-check-in' );?></label></th>
              <td colspan="1">
                <div class="mphb-ctrl-wrapper mphb-ctrl mphb-ctrl-text" data-type="text" data-inited="true">
                  <input name="custom_booking_id" id="custom_booking_id" class=" regular-text" type="number" placeholder="Optional" min="1">
                  <small class="field-alert hide"><?php echo esc_html__('Booking ID not available', 'self-check-in' );?></small>
                </div>
              </td>
            </tr>
            <tr>
              <th><label for=""><?php echo esc_html__( 'First Guest Name', 'self-check-in' );?></label></th>
              <td colspan="1">
                <div class="mphb-ctrl-wrapper mphb-ctrl mphb-ctrl-text" data-type="text" data-inited="true">
                  <input name="first_name" id="" class=" regular-text" type="text" required>
                </div>
              </td>
            </tr>
            <tr>
              <th><label for=""><?php echo esc_html__( 'First Guest Surname', 'self-check-in' );?></label></th>
              <td colspan="1">
                <div class="mphb-ctrl-wrapper mphb-ctrl mphb-ctrl-text" data-type="text" data-inited="true">
                  <input name="last_name" id="" class=" regular-text" type="text" required>
                </div>
              </td>
            </tr>
            <tr>
              <th><label for=""><?php echo esc_html__( 'First Guest Email', 'self-check-in' );?></label></th>
              <td colspan="1">
                <div class="mphb-ctrl-wrapper mphb-ctrl mphb-ctrl-text" data-type="text" data-inited="true">
                  <input name="email" id="" class=" regular-text" type="email" required>
                </div>
              </td>
            </tr>
            <tr>
              <th><label for=""><?php echo esc_html__( 'First Guest Phone', 'self-check-in' );?></label></th>
              <td colspan="1">
                <div class="mphb-ctrl-wrapper mphb-ctrl mphb-ctrl-text" data-type="text" data-inited="true">
                  <input name="phone" id="" class=" regular-text" type="tel" required>
                </div>
              </td>
            </tr>
            <tr>
              <th><label for=""><?php echo esc_html__( 'Arrival Date', 'self-check-in' );?></label></th>
              <td colspan="1">
                <div class="mphb-ctrl-wrapper mphb-ctrl mphb-ctrl-text" data-type="text" data-inited="true">
                  <input name="arrival_date" id="" class=" regular-text" type="date" required>
                </div>
              </td>
            </tr>
            <tr>
              <th><label for=""><?php echo esc_html__( 'Departure Date', 'self-check-in' );?></label></th>
              <td colspan="1">
                <div class="mphb-ctrl-wrapper mphb-ctrl mphb-ctrl-text" data-type="text" data-inited="true">
                  <input name="departure_date" id="" class=" regular-text" type="date" required>
                </div>
              </td>
            </tr>
            <tr>
              <th><label for=""><?php echo esc_html__( 'Number of Guests', 'self-check-in' );?></label></th>
              <td colspan="1">
                <div class="mphb-ctrl-wrapper mphb-ctrl mphb-ctrl-text" data-type="text" data-inited="true">
                  <input name="number_of_guests" id="" class=" regular-text" type="number" min="1" required>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
        <hr>
        <div class="col-12 mt-3 mb-3">
          <input type="hidden" name="wpsci_save_check_in" value="1">
          <button type="submit" name="wpsci_save_check_in" id="wpsci_save_check_in" class="button button-primary">
            <?php echo esc_html__( 'Save', 'self-check-in' );?>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
