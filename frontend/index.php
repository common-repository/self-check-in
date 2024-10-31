<?php
if (!defined( 'ABSPATH') ) exit; // Exit if accessed directly

//fetch guests record
$sql = "SELECT * FROM ".$wpdb->base_prefix."wpsci_guests WHERE booking_id = '%d'";
$result = $GLOBALS['wpdb']->get_results($wpdb->prepare($sql, $booking_id), ARRAY_A);
    
echo '<div class="wpsci-main-wrapper ">
    <input type="hidden" name="number_of_guest" id="number_of_guest" value="'.esc_html($number_of_guest).'" readonly>';
?>

<?php
if(!$result){

    /**
     * add guests deatils
     */
    include_once(plugin_dir_path( __FILE__ ) . 'templates/insert-guests.php');

}else{

    /**
     * guests details table
     */
    include_once(plugin_dir_path( __FILE__ ) . 'templates/guests-list.php');

    /**
     * update guests details
     */
    include_once(plugin_dir_path( __FILE__ ) . 'templates/update-guests.php');
}

echo '</div>';