<?php

defined('ABSPATH') or exit;


if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
    return;
}


function wqrmi_qr_invoice_add_to_gateways($gateways) {
    $gateways[] = 'WC_Gateway_QR_Invoice';
    return $gateways;
}

add_filter('woocommerce_payment_gateways', 'wqrmi_qr_invoice_add_to_gateways');


function wqrmi_invoice_gateway_init() {

    class WC_Gateway_QR_Invoice extends WC_Payment_Gateway {

        /**
         * Constructor for the gateway.
         */
        public function __construct() {

            $payment_gateway_icon = plugins_url( 'assets/icon-qr_bill.png', dirname(__FILE__));
            
            $this->id = 'qr_invoice_gateway';
            $this->icon = apply_filters('woocommerce_qr_invoice_icon', $payment_gateway_icon);
            $this->has_fields = false;
            $this->method_title = __('QR Invoice', 'qr-invoice');
            $this->method_description = __('Allows QR Invoice payments. Orders are marked as "Processing" when received.', 'qr-invoice');

            // Load the settings.
            $this->wqrmi_init_form_fields();
            $this->init_settings();

            // Define user set variables
            $this->title = $this->get_option('title');
            $this->description = $this->get_option('description');
            $this->instructions = $this->get_option('instructions', $this->description);

            // Actions
            add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
            add_action('woocommerce_thankyou_' . $this->id, array($this, 'wqrmi_thankyou_page'));
            add_action('woocommerce_email_before_order_table', array($this, 'wqrmi_email_instructions'), 10, 3);
        }

        /**
         * Initialize Gateway Settings Form Fields
         */
        public function wqrmi_init_form_fields() {

            $this->form_fields = apply_filters('wc_qr-invoice_form_fields', array(
                'enabled' => array(
                    'title' => __('Enable/Disable', 'qr-invoice'),
                    'type' => 'checkbox',
                    'label' => __('Enable QR Invoice Payment', 'qr-invoice'),
                    'default' => 'yes'
                ),
                'title' => array(
                    'title' => __('Title', 'qr-invoice'),
                    'type' => 'text',
                    'description' => __('This controls the title for the payment method the customer sees during checkout.', 'qr-invoice'),
                    'default' => __('Pay by Invoice', 'qr-invoice'),
                    'desc_tip' => false,
                ),
                'description' => array(
                    'title' => __('Description', 'qr-invoice'),
                    'type' => 'textarea',
                    'description' => __('Payment method description that the customer will see on your checkout.', 'qr-invoice'),
                    'default' => __('Please remit payment to Store upon pickup or delivery.', 'qr-invoice'),
                    'desc_tip' => true,
                ),
                'instructions' => array(
                    'title' => __('Instructions', 'qr-invoice'),
                    'type' => 'textarea',
                    'description' => __('Instructions that will be added to the thank you page and emails.', 'qr-invoice'),
                    'default' => '',
                    'desc_tip' => true,
                ),
            ));
        }

        public function wqrmi_thankyou_page() {
            if ($this->instructions) {
                echo wpautop(wptexturize($this->instructions));
            }
        }

        public function wqrmi_email_instructions($order, $sent_to_admin, $plain_text = false) {

            if ($this->instructions && !$sent_to_admin && $this->id === $order->payment_method && $order->has_status('processing')) {
                echo wpautop(wptexturize($this->instructions)) . PHP_EOL;
            }
        }

        public function process_payment($order_id) {

            $order = wc_get_order($order_id);

            $order->update_status('processing', __('Awaiting QR Invoice payment', 'qr-invoice'));

            $order->reduce_order_stock();

            WC()->cart->empty_cart();

            return array(
                'result' => 'success',
                'redirect' => $this->get_return_url($order)
            );
        }

    }

}

add_action('plugins_loaded', 'wqrmi_invoice_gateway_init', 11);
