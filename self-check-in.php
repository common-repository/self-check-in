<?php

/**
 * Self Check-In
 *
 * @package     Self Check-In
 * @author      movingWords Srl
 * @copyright   movingWords Srl
 * @license     GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name: Self Check-In
 * Plugin URI:  https://wpselfcheckin.it
 * Description: Create a self check-in form for each type of accommodation, collect and store data from accommodation cards with ease on WordPress.
 * Version:     1.0.0
 * Author:      movingWords Srl
 * Author URI:  https://movingwords.it
 * Text Domain: self-check-in
 * Domain Path: /languages
 * License:     GPL v2 or later
 * Tested up to: 6.6.1
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

//plugin short prefix- wpsci
define('WPSCI_SITE_URL', get_site_url());
define('WPSCI_UPLOAD_PATH', wp_upload_dir()['basedir']);
define('WPSCI_UPLOAD_URL', wp_upload_dir()['baseurl']);
define('WPSCI_PLUGIN_PATH', plugin_dir_path( __FILE__ ));
define('WPSCI_PLUGIN_URL', plugin_dir_url( __FILE__ ));

//plugin action hook
register_activation_hook(__FILE__, 'wpsci_plugin_activation');
function wpsci_plugin_activation() {
    
    require_once(plugin_dir_path( __FILE__ ). 'activation.php');
}

//adding functions file
require_once(plugin_dir_path( __FILE__ ). 'functions.php');