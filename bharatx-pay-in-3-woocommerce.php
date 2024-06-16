<?php

/*
 * Plugin Name:       BharatX Pay In 3 for WooCommerce
 * Description:       Easily integrate BharatX Pay In 3 in your WooCommerce store.
 * Version:           0.1.0
 * Author:            Nirzon Taru Karmakar
 * Author URI:        https://www.nirzonkarmakar.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       bharatx-pay-in-3
 * Domain Path:       /languages
 *
 * Requires Plugins:  woocommerce
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
  exit();
}

// Plugin constants
define('BHARATX_PAY_IN_3_VERSION', '0.1.0');
define('BHARATX_PAY_IN_3_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('BHARATX_PAY_IN_3_PLUGIN_URL', plugin_dir_url(__FILE__));
define('BHARATX_PAY_IN_3_PLUGIN_NAME', 'BharatX Pay In 3');
define('BHARATX_PAY_IN_3_LANGUAGE_PREFIX', 'bharatx-pay-in-3');

// Load the plugin
add_action('woocommerce_loaded', 'woocommerce_loaded');

/**
 * WooCommerce recommended check to ensure this plugin is active and working
 * properly with the store.
 *
 * Preemptively checks if all the plugins are loaded.
 *
 * @return void
 */
function woocommerce_loaded() {
  if (!class_exists('WC_Payment_Gateway')) {
    return;
  }

  // Load the gateway class
  include BHARATX_PAY_IN_3_PLUGIN_DIR . 'includes/class-gateway.php';
}

// Add payment gateway to WooCommerce
add_filter('woocommerce_payment_gateways', 'add_bharatx_gateway');

/**
 * Adds a settings link to the WooCommerce settings page.
 *
 * @param array $links
 * @return array
 */
function add_bharatx_gateway($gateways) {
  $gateways[] = 'Bharatx_Payment_Gateway';
  return $gateways;
}

// Add settings link to plugin page
add_filter(
  'plugin_action_links_' . plugin_basename(__FILE__),
  'add_bharatx_settings_link'
);

/**
 * Adds a settings link to the plugin page.
 *
 * @param array $links
 * @return array
 */
function add_bharatx_settings_link($links) {
  $settings_link =
    '<a href="admin.php?page=wc-settings&tab=checkout&section=bharatx-pay-in-3">' .
    __('Settings', 'bharatx-pay-in-3') .
    '</a>';
  array_unshift($links, $settings_link);
  return $links;
}
