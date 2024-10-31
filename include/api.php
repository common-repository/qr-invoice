<?php

function wqrmi_attach_order_invoice( $attachments, $email_id, $order ) {

    global $wqrmi_auth_url, $wqrmi_qr_bill_url, $wqrmi_qr_reference_url, $wqrmi_options;

    $access_token            = $wqrmi_options['wqrmi_access_token'];
    $send_invoice_swiss_post = $wqrmi_options['wqrmi_send_invoice'];
    $payment_method          = $order->get_payment_method();

    if ( $email_id == 'customer_processing_order' && $access_token  && $payment_method == 'qr_invoice_gateway' ) {

        $order_id                       = $order->get_id();
        $order_data                     = $order->get_data();
        $order_date                     = $order_data['date_created']->date('Y-m-d H:i:s');
        $order_date_created             = $order_data['date_created']->date('Y-m-d');
        $user_id                        = ( $order->get_user_id() ) ? $order->get_user_id() : '';
        $order_items                    = $order->get_items();
        $language                       = explode( '_', get_locale() );
        $profile_id                     = $wqrmi_options['wqrmi_profile_id'];
        $invoice_template_name          = $wqrmi_options['wqrmi_template_invoice'];
        $invoice_payable                = $wqrmi_options['wqrmi_payable_invoice'];
        $invoice_type                   = $wqrmi_options['wqrmi_type_invoice'];
        $invoice_vat                    = ( $wqrmi_options['wqrmi_vat_invoice'] ) ? "true" : "false";
        $shipping_price                 = ( $order->get_shipping_total() > 0 ) ? $order->get_shipping_total() : 0; 
        $shipping_price_vat             = ( $order->get_shipping_tax() > 0 ) ? round($order->get_shipping_tax(), 2) : 0; 
        $wqrmi_letter_dispatch_priority = $wqrmi_options['wqrmi_letter_dispatch_priority'];
        $wqrmi_my_climate               = $wqrmi_options['wqrmi_my_climate'];
        $wqrmi_color                    = $wqrmi_options['wqrmi_color'];
        $wqrmi_franking_envelopes       = ( $wqrmi_options['wqrmi_franking_envelopes'] ) ? $wqrmi_options['wqrmi_franking_envelopes'] : 'none';
        $wqrmi_address                  = $wqrmi_options['wqrmi_address'];
        $wqrmi_postal_tariff            = $wqrmi_options['wqrmi_postal_tariff'];
        $wqrmi_remarks_to_printshop     = $wqrmi_options['wqrmi_remarks_to_printshop'];
        $wqrmi_reference_type           = $wqrmi_options['wqrmi_reference_type'];
        $wqrmi_reference_custom_id      = $wqrmi_options['wqrmi_reference_custom_id'];
        $wqrmi_reference_date           = $wqrmi_options['wqrmi_reference_date'];
        $wqrmi_shipping_percentage_vat  = ($wqrmi_options['wqrmi_shipping_percentage_vat']) ? $wqrmi_options['wqrmi_shipping_percentage_vat'] : 0 ;
        
        if ( ! empty( $profile_id ) ) {
            $creaditor_params = [ "profile_id" => "{$profile_id}" ];
        } else {
            $creaditor_params = [ "default_profile" => "true" ];
        }
        
        $general_params = [
            "plugin_ID"               => 'd8ba4e91-9c60-4ea5-b5cd-115859f39459',
            "language"                => "{$language[0]}",
            "amount"                  => "{$order_data['total']}",
            "shipping_charge"         => "{$shipping_price}", 
            "shipping_percentage_vat" => "{$wqrmi_shipping_percentage_vat}", 
            "currency"                => "{$order_data['currency']}",
            "message"                 => "",
            "creditor"                => $creaditor_params,
            "debtor"                  => [
                "first_name"          => "{$order_data['billing']['first_name']}",
                "last_name"           => "{$order_data['billing']['last_name']}",
                "street"              => "{$order_data['billing']['address_1']}",
                "house_number"        => "{$order_data['billing']['address_2']}",
                "zip"                 => "{$order_data['billing']['postcode']}",
                "city"                => "{$order_data['billing']['city']}",
                "country"             => "{$order_data['billing']['country']}",
            ],
            "billing_information"     => [
                "bill_number"         => "{$order_id}",
                "bill_date"           => "{$order_date}",
                "order_reference"     => "",
                "uid_number"          => "{$user_id}",
            ],            
        ];
                
        if( $wqrmi_reference_type != '' ) {
            $body_request = array(
                'headers' => array(
                    'Authorization' => 'Bearer ' . $access_token,    
                ),
            );

            $wqrmi_qr_reference_url = $wqrmi_qr_reference_url . '/?type=' . $wqrmi_reference_type . '&id=' . $wqrmi_reference_custom_id . '&date=' . $wqrmi_reference_date; 
            $reference_response     = wp_remote_get( $wqrmi_qr_reference_url, $body_request );
            $reference_response     = json_decode( $reference_response['body'] );                 

            if( $reference_response->status == 200 && ! empty( $reference_response->data->reference ) ) {
               $general_params['reference'] = $reference_response->data->reference;
            }   
        }                

        if ( ! empty( $order_items ) && $invoice_type != 1 ) {
            
            $_tax = new WC_Tax();
            
            foreach ( $order_items as $item ) {
                $product           = $item->get_product();
                $product_tax_class = $product->get_tax_class();
                $tax               = $_tax->get_rates( $product_tax_class );
                $tax_rate          = reset( $tax );
                if ( $tax_rate['rate'] ) {
                    $tax_product = $tax_rate['rate'];
                } else {
                    $tax_product = 0;
                }
                
                $products[] = [
                    "name"            => $item->get_name(),
                    "unit_price"      => $product->get_price(),
                    "quantity"        => $item->get_quantity(),
                    "vat"             => $tax_product,
                ];
            }

            if ( ! empty( $invoice_template_name ) ) {
                $invoice_template_default = "false";
            } else {
                $invoice_template_default = "true";
            }
            
            if(!empty($invoice_payable) && is_numeric($invoice_payable)){
                $invoice_payable = date('Y-m-d H:i:s', strtotime($order_date.'+'.$invoice_payable.'days'));
            }
            else{
                $invoice_payable = '';
            }

            if ( ! empty( $products ) ) { 
                $general_params['template'] = [
                    "name"       => "{$invoice_template_name}",
                    "default"    => "{$invoice_template_default}",
                    "payable_by" => "{$invoice_payable}",
                    "vat_over"   => "{$invoice_vat}",
                    "products"   => $products,
                ];
            }
        }


        if ( $send_invoice_swiss_post == 1 ) {
            
            $general_params['letter_dispatch'] = [
                "priority"           => "{$wqrmi_letter_dispatch_priority}",
                "my_climate"         => "{$wqrmi_my_climate}",
                "color"              => "{$wqrmi_color}",
                "franking_envelopes" => "{$wqrmi_franking_envelopes}",
            ];

            if ( ! empty( $wqrmi_address ) && ! empty( $wqrmi_postal_tariff ) ) {
                
                $collective_address = [
                    "address"       => "{$wqrmi_address}",
                    "postal_tariff" => "{$wqrmi_postal_tariff}",
                ];
                    
                $general_params['letter_dispatch']['collective_address'] = $collective_address;
            }

            $general_params['remarks_to_printshop'] = "{$wqrmi_remarks_to_printshop}";
            
        }

        $params = json_encode( $general_params );

        $body_request = array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $access_token,
                'Content-Type'  => 'application/json',
                'Accept'        => 'application/json',
            ),
            'body'    => $params,
            'method'  => 'POST'
        );

        $response         = wp_remote_post( $wqrmi_qr_bill_url, $body_request );
        $response         = json_decode( $response['body'] );
        $return_file_name = $response->data->file;

        if ( $return_file_name ) {
            
            $params = [
                'file' => $return_file_name,
            ];

            $params = json_encode( $params );

            $body_request = array(
                'headers' => array(
                        'Authorization' => 'Bearer ' . $access_token,
                ),
            );

            $response = wp_remote_get( $wqrmi_qr_bill_url . '?file=' . $return_file_name, $body_request );

            if ( $response['body'] ) {

                $upload_dir             = wp_upload_dir();
                //$invoice_file_name      = '/qrmodul-invoice/invoice_' . $order_id . '_' . $return_file_name . '.pdf';
                
                $invoice_file_name      = '/qrmodul-invoice/'
                        .esc_html__( "QRbill", "qr-invoice" )
                        .'_'.$order_data['billing']['first_name']
                        .'_'.$order_data['billing']['company']
                        .'_'.$order_date_created
                        .'_'.$order_id.'.pdf';
                $invoice_file_name = str_replace(' ', '_', $invoice_file_name);
                
                $invoice_file_full_path = $upload_dir['basedir'] . $invoice_file_name;
                $invoice_url            = str_replace( site_url(), '', $upload_dir['baseurl'] ) . $invoice_file_name;

                file_put_contents( $invoice_file_full_path, $response['body'] );
                update_post_meta( $order_id, 'qrmodul-invoice-url', $invoice_url );

                if ( $send_invoice_swiss_post != 1 ) {
                    $attachments[] = $invoice_file_full_path;
                }

            }
        }
    }

    return $attachments;
}

add_filter( 'woocommerce_email_attachments', 'wqrmi_attach_order_invoice', 10, 3 );
