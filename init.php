<?php
if (!defined( 'ABSPATH') ) exit; // Exit if accessed directly

/**
 * Translation hook
 */
function wpsci_plugin_init() {
  $pluginDir = plugin_dir_path( __FILE__ );
  $pluginDir = plugin_basename( $pluginDir ); 
  load_plugin_textdomain( 'self-check-in', false, $pluginDir . '/languages' );
}
add_action('init', 'wpsci_plugin_init');

//Including assets//

add_action('init', 'wpsci_register_scripts');

add_action('wp_enqueue_scripts', 'wpsci_enqueue_public_scripts');
add_action('admin_enqueue_scripts', 'wpsci_enqueue_admin_scripts', 10);

function wpsci_register_scripts()
{
  $version   = '1.0.0';

  wp_register_style('digital-styles', WPSCI_PLUGIN_URL . 'assets/css/digital-style.css', [], $version);
  wp_register_script('digital-script', WPSCI_PLUGIN_URL . 'assets/js/digital-script.js', ['jquery'], $version, 10);
  wp_localize_script('digital-script', 'wpsci', array(
    'ajaxurl' => admin_url('admin-ajax.php'),
    'verify_nonce' => wp_create_nonce('wpsci_ajax_nonce')
  ));
}

function wpsci_enqueue_public_scripts()
{
  wp_enqueue_style('digital-styles');
  wp_enqueue_script('digital-script');
}

function wpsci_enqueue_admin_scripts()
{
  wp_enqueue_style('digital-styles');
  wp_enqueue_script('digital-script');
}


//
require_once WPSCI_PLUGIN_PATH . 'includes/ajax.php';