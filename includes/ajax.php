<?php
if (!defined( 'ABSPATH') ) exit; // Exit if accessed directly

/** 
 * Handle ajax request
 */
class wpsci_ajax{
  
    private static $instance;

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct(){

        add_action('wp_ajax_get_municipal_data', array($this, 'wpsci_get_municipal_data_handler'));
        add_action('wp_ajax_nopriv_get_municipal_data', array($this, 'wpsci_get_municipal_data_handler'));

        add_action('wp_ajax_check_booking_id', array($this, 'wpsci_check_booking_id_handler'));
        add_action('wp_ajax_nopriv_check_booking_id', array($this, 'wpsci_check_booking_id_handler'));
    }

    /**
     * get municipal data as per province
     */
    public function wpsci_get_municipal_data_handler() {
        global $wpdb;

        // Sanitize the nonce
        $nonce_value = isset($_POST['verify_nonce']) ? sanitize_text_field(wp_unslash($_POST['verify_nonce'])) : '';
        if (!wp_verify_nonce($nonce_value, 'wpsci_ajax_nonce')) {
            wp_send_json_error(0);
            wp_die();
        }
        $list = wpsci_get_municipal_list();
    
        $items = array_filter($list, function($item) {
        return $item[2] === sanitize_text_field($_POST['province']);
        });
    
        $options = '';
        foreach ($items as $key => $item) {
    
        $options .= '<option value="'.$item[0].'" data-provice="'.$item[2].'">'. $item[1] .'</option>';
        }
    
        wp_send_json_success($options);
        wp_die();
    }

    /**
     * validate booking id
     */
    function wpsci_check_booking_id_handler() {
        global $wpdb;

        // Sanitize the nonce
        $nonce_value = isset($_POST['verify_nonce']) ? sanitize_text_field(wp_unslash($_POST['verify_nonce'])) : '';
        if (!wp_verify_nonce($nonce_value, 'wpsci_ajax_nonce')) {
            wp_send_json_error(0);
            wp_die();
        }

        if(isset($_POST['booking_id']) && sanitize_text_field($_POST['booking_id']) !==''){
        $booking_id = sanitize_text_field($_POST['booking_id']);
        
        $sql = "select * from {$wpdb->base_prefix}posts where ID = '%d' AND post_type = 'mphb_booking'";
        $result = $wpdb->get_row($wpdb->prepare($sql, $booking_id), ARRAY_A);
        
        if($result){
            wp_send_json_success('unavailable');
            wp_die();
        }
        
        $sql = "select * from {$wpdb->base_prefix}wpsci_check_in where booking_id = '%d'";
        $result = $wpdb->get_row($wpdb->prepare($sql, $booking_id), ARRAY_A);
        
        if($result){
            wp_send_json_success('unavailable');
            wp_die();
        }
        }else{
        
            $sql = "SELECT * FROM {$wpdb->base_prefix}wpsci_check_in ORDER BY id DESC";
            $result = $wpdb->get_row($wpdb->prepare($sql), ARRAY_A);
            if($result){
        
                $check_id = $result['id']+1;
                $sql = "SELECT * FROM {$wpdb->base_prefix}wpsci_check_in WHERE booking_id = %d";
                $result = $wpdb->get_row($wpdb->prepare($sql, $check_id), ARRAY_A);
                if($result){
                    wp_send_json_success('unavailable');
                    wp_die();
                }
        
                $sql = "SELECT * FROM {$wpdb->base_prefix}posts WHERE ID = %d AND post_type = 'mphb_booking'";
                $mphb_result = $wpdb->get_row($wpdb->prepare($sql, $check_id), ARRAY_A);
                if($mphb_result){
                    wp_send_json_success('unavailable');
                    wp_die();
                }
            }
        }
    
        wp_send_json_success('available');
        wp_die();
    }

}

$wpsci_instance = wpsci_ajax::getInstance();