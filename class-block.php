<?php

use Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType;

final class BharatX_Gateway_Blocks extends AbstractPaymentMethodType {
  private $gateway;
  protected $name = 'bharatx_pay_in_3';

  /**
   * Initializes the function by setting the settings and creating a new instance of Bharatx_Payment_Gateway.
   *
   */
  public function initialize() {
    $this->settings = get_option('woocommerce_bharatx_pay_in_3_settings', []);
    $this->gateway = new Bharatx_Payment_Gateway();
  }

  /**
   * Checks if the gateway is active.
   *
   * @return bool Returns true if the gateway is available, false otherwise.
   */
  public function is_active() {
    return $this->gateway->is_available();
  }

  /**
   * Registers the script handles for the BharatX Pay In 3 blocks integration.
   *
   * @return array Script handles for the integration.
   */
  public function get_payment_method_script_handles() {
    wp_register_script(
      'bharatx_pay_in_3-blocks-integration',
      plugin_dir_url(__FILE__) . 'checkout.js',
      [
        'wc-blocks-registry',
        'wc-settings',
        'wp-element',
        'wp-html-entities',
        'wp-i18n',
      ],
      null,
      true
    );
    if (function_exists('wp_set_script_translations')) {
      wp_set_script_translations('bharatx_pay_in_3-blocks-integration');
    }
    return ['bharatx_pay_in_3-blocks-integration'];
  }

  /**
   * Retrieves the payment method data including the title and description.
   *
   * @return array Returns an array with 'title' and 'description' keys.
   */
  public function get_payment_method_data() {
    return [
      'title' => $this->gateway->title,
      'description' => $this->gateway->description,
    ];
  }
}
?>
