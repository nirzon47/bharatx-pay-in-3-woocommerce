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
  include plugin_dir_path(__FILE__) . 'class-gateway.php';
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
    '<a href="admin.php?page=wc-settings&tab=checkout&section=bharatx_pay_in_3">' .
    __('Settings', 'bharatx_pay_in_3') .
    '</a>';
  array_unshift($links, $settings_link);
  return $links;
}

// Hook the custom function to the 'before_woocommerce_init' action
add_action(
  'before_woocommerce_init',
  'declare_cart_checkout_blocks_compatibility'
);

/**
 * Custom function to declare compatibility with cart_checkout_blocks feature
 */
function declare_cart_checkout_blocks_compatibility() {
  // Check if the required class exists
  if (class_exists('\Automattic\WooCommerce\Utilities\FeaturesUtil')) {
    // Declare compatibility for 'cart_checkout_blocks'
    \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility(
      'cart_checkout_blocks',
      __FILE__,
      true
    );
  }
}

// Hook the custom function to the 'woocommerce_blocks_loaded' action
add_action('woocommerce_blocks_loaded', 'bharatx_register_payment_method');

/**
 * Function to register a payment method type
 * with WooCommerce Blocks Checkout
 *
 */
function bharatx_register_payment_method() {
  // Check if the required class exists
  if (
    !class_exists(
      'Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType'
    )
  ) {
    return;
  }

  // Include the custom Blocks Checkout class
  require_once plugin_dir_path(__FILE__) . 'class-block.php';

  // Hook the registration function to the 'woocommerce_blocks_payment_method_type_registration' action
  add_action('woocommerce_blocks_payment_method_type_registration', function (
    Automattic\WooCommerce\Blocks\Payments\PaymentMethodRegistry $payment_method_registry
  ) {
    // Register an instance of My_Custom_Gateway_Blocks
    $payment_method_registry->register(new BharatX_Gateway_Blocks());
  });
}

?>
