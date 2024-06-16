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
    // Load the settings.
    $this->init_settings();

    $this->id = 'bharatx_pay_in_3';
    $this->method_title = __('BharatX Pay In 3', 'bharatx-pay-in-3');
    $this->method_description = __(
      'Use this payment method to pay with BharatX Pay In 3. Easily split your payments.',
      'bharatx-pay-in-3'
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
        'title' => __('Enable/Disable', 'bharatx-pay-in-3'),
        'type' => 'checkbox',
        'label' => __('Enable BharatX Pay In 3', 'bharatx-pay-in-3'),
        'default' => 'yes',
      ],
      'title' => [
        'title' => __('Title', 'bharatx-pay-in-3'),
        'type' => 'text',
        'description' => __(
          'This controls the title which the user sees during checkout.',
          'bharatx-pay-in-3'
        ),
        'default' => __('BharatX Pay In 3', 'bharatx-pay-in-3'),
        'desc_tip' => true,
      ],
      'description' => [
        'title' => __('Description', 'bharatx-pay-in-3'),
        'type' => 'text',
        'description' => __(
          'This controls the description which the user sees during checkout.',
          'bharatx-pay-in-3'
        ),
        'default' => __(
          'Use this payment method to pay with BharatX Pay In 3. Easily split your payments.',
          'bharatx-pay-in-3'
        ),
        'desc_tip' => true,
      ],
      'apiKey' => [
        'title' => __('API Key', 'bharatx-pay-in-3'),
        'type' => 'text',
        'description' => __(
          'Contact contact@bharatx.tech for API Key.',
          'bharatx-pay-in-3'
        ),
        'default' => '',
        'desc_tip' => true,
        'custom_attributes' => ['required' => 'required'],
      ],
      'apiSecret' => [
        'title' => __('API Secret', 'bharatx-pay-in-3'),
        'type' => 'password',
        'description' => __(
          'Contact contact@bharatx.tech for API Secret.',
          'bharatx-pay-in-3'
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
    global $woocommerce;
    $order = new WC_Order($order_id);

    // API request to BharatX to create a transaction.
    $response = $this->create_bharatx_transaction($order_id, $order);

    if ($response['status'] == 'success') {
      // Mark as on-hold (we're awaiting the payment).
      $order->update_status('on-hold', 'Awaiting BharatX payment');

      // Reduce stock levels.
      wc_reduce_stock_levels($order_id);

      // Remove cart.
      $woocommerce->cart->empty_cart();

      // Return thank you page redirect.
      return [
        'result' => 'success',
        'redirect' => $this->get_return_url($order),
      ];
    } else {
      wc_add_notice('Payment error: ' . $response['message'], 'error');
      return;
    }
  }

  /**
   * Create a transaction on BharatX
   *
   * @param int $order_id
   * @param WC_Order $order
   * @return array
   */
  private function create_bharatx_transaction($order_id, $order) {
    $url = 'https://web-v2.bharatx.tech/api/merchant/transaction';
    $credentials = base64_encode(
      $this->settings['apiKey'] . ':' . $this->settings['apiSecret']
    );

    $billing_address = $order->get_address('billing');
    $shipping_address = $order->get_address('shipping');
    $redirectUrl = $this->get_return_url($order);
    $phoneNumber = $billing_address['phone'];

    // Validations
    if ($phoneNumber == '' || strlen($phoneNumber) < 10) {
      return [
        'status' => 'error',
        'message' => 'BharatX Pay In 3 requires a valid phone number',
      ];
    }

    if (strlen($phoneNumber) === 10) {
      $phoneNumber = '+91' . $billing_address['phone'];
    }

    if ($billing_address['country'] != 'IN') {
      return [
        'status' => 'error',
        'message' => 'BharatX Pay In 3 only supports India',
      ];
    }

    $user = [
      'name' =>
        $billing_address['first_name'] . ' ' . $billing_address['last_name'],
      'email' => $billing_address['email'],
      'phoneNumber' => $phoneNumber,
      'panNumber' => 'string', // Add logic to get PAN number if needed
    ];

    $transaction = [
      'id' => (string) $order->get_id(),
      'amount' => (int) $order->get_total(),
      'mode' => 'TEST',
      'notes' => new stdClass(), // Empty object for notes
    ];

    $body = [
      'transaction' => $transaction,
      'user' => $user,
    ];
    $json_body = json_encode($body);

    $response = wp_remote_post($url, [
      'headers' => [
        'Authorization' => 'Basic ' . $credentials,
        'Content-Type' => 'application/json',
      ],
      'body' => $json_body,
      'timeout' => 60,
    ]);

    if (is_wp_error($response)) {
      return ['status' => 'error', 'message' => $response->get_error_message()];
    }

    $response_body = wp_remote_retrieve_body($response);
    $result = json_decode($response_body, true);

    return ['status' => 'success', 'message' => $result['message']];
  }
}
