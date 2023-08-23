// Create a custom settings section
add_filter('woocommerce_get_sections_general', 'add_surcharge_section', 10, 1);
function add_surcharge_section($sections) {
  $sections['surcharges'] = 'Payment Surcharges';
  return $sections;
}

add_filter('woocommerce_get_settings_general', 'add_surcharge_settings', 10, 2);
function add_surcharge_settings($settings, $current_section) {
  if ($current_section === 'surcharges') {
    $settings = [];
    $settings[] = [
      'title' => 'Payment Method Surcharge',
      'type' => 'title',
      'desc' => 'Add surcharge amounts for payment methods',
      'id' => 'surcharge_options'
    ];
    $settings[] = [
      'title' => 'PayPal Surcharge',
      'desc' => 'Enter the surcharge for PayPal',
      'id' => 'paypal_surcharge',
      'type' => 'text',
      'default' => '0'
    ];
    // Repeat for other payment methods
    $settings[] = ['type' => 'sectionend', 'id' => 'surcharge_options'];
  }
  return $settings;
}

// Function to add surcharge based on payment method
add_action('woocommerce_cart_calculate_fees', 'add_payment_method_surcharge');
function add_payment_method_surcharge() {
  if (is_admin() && !defined('DOING_AJAX')) return;

  $chosen_gateway = WC()->session->get('chosen_payment_method');
  $surcharge = 0;

  if ($chosen_gateway == 'paypal') {
    $surcharge = floatval(get_option('paypal_surcharge'));
  }

  // Repeat for other payment methods

  if ($surcharge > 0) {
    WC()->cart->add_fee('Payment Method Surcharge', $surcharge);
  }
}
