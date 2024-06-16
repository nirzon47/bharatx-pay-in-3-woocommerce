<?php

//  If this file is called directly, abort.
if (!defined('ABSPATH')) {
  exit();
}

/**
 * Class Bharatx_Payment_Gateway
 *
 * Extends the WC_Payment_Gateway class to add the custom payment gateway.
 */
class Bharatx_Payment_Gateway extends WC_Payment_Gateway {
  /**
   * Constructor method
   *
   * @return void
   */
  public function __construct() {
    $this->id = BHARATX_PAY_IN_3_LANGUAGE_PREFIX;
    $this->method_title = __(
      'BharatX Pay In 3',
      BHARATX_PAY_IN_3_LANGUAGE_PREFIX
    );
    $this->method_description = __(
      'Use this payment method to pay with BharatX Pay In 3. Easily split your payments.',
      BHARATX_PAY_IN_3_LANGUAGE_PREFIX
    );

    $this->init_form_fields();
    $this->init_settings();

    // Add action to save settings on save
    add_action('woocommerce_update_options_payment_gateways_' . $this->id, [
      $this,
      'process_admin_options',
    ]);
  }

  /**
   * Form fields in admin settings page
   *
   * @return void
   */
  public function init_form_fields() {
    $this->form_fields = [
      'enabled' => [
        'title' => __('Enable/Disable', BHARATX_PAY_IN_3_LANGUAGE_PREFIX),
        'type' => 'checkbox',
        'label' => __(
          'Enable BharatX Pay In 3',
          BHARATX_PAY_IN_3_LANGUAGE_PREFIX
        ),
        'default' => 'yes',
      ],
      'title' => [
        'title' => __('Title', BHARATX_PAY_IN_3_LANGUAGE_PREFIX),
        'type' => 'text',
        'description' => __(
          'This controls the title which the user sees during checkout.',
          BHARATX_PAY_IN_3_LANGUAGE_PREFIX
        ),
        'default' => __('BharatX Pay In 3', BHARATX_PAY_IN_3_LANGUAGE_PREFIX),
        'desc_tip' => true,
      ],
      'description' => [
        'title' => __('Description', BHARATX_PAY_IN_3_LANGUAGE_PREFIX),
        'type' => 'text',
        'description' => __(
          'This controls the description which the user sees during checkout.',
          BHARATX_PAY_IN_3_LANGUAGE_PREFIX
        ),
        'default' => __(
          'Use this payment method to pay with BharatX Pay In 3. Easily split your payments.',
          BHARATX_PAY_IN_3_LANGUAGE_PREFIX
        ),
        'desc_tip' => true,
      ],
      'apiKey' => [
        'title' => __('API Key', BHARATX_PAY_IN_3_LANGUAGE_PREFIX),
        'type' => 'text',
        'description' => __(
          'Contact contact@bharatx.tech for API Key.',
          BHARATX_PAY_IN_3_LANGUAGE_PREFIX
        ),
        'default' => '',
        'desc_tip' => true,
        'custom_attributes' => ['required' => 'required'],
      ],
      'apiSecret' => [
        'title' => __('API Secret', BHARATX_PAY_IN_3_LANGUAGE_PREFIX),
        'type' => 'password',
        'description' => __(
          'Contact contact@bharatx.tech for API Secret.',
          BHARATX_PAY_IN_3_LANGUAGE_PREFIX
        ),
        'default' => '',
        'desc_tip' => true,
        'custom_attributes' => ['required' => 'required'],
      ],
    ];
  }

  /**
   * Process the payment
   *
   * @param int $order_id
   * @return array
   */
  public function process_payment($order_id) {
    $order = wc_get_order($order_id);

    // TODO: Implement payment processing logic here

    // Mark the order as processed
    $order->payment_complete();

    // Redirect to the thank you page
    return [
      'result' => 'success',
      'redirect' => $this->get_return_url($order),
    ];
  }
}
