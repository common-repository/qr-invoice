<?php


function wqrmi_custom_column_name( $columns ) {

	$columns['qrmodule-invoice'] = __( 'Qr-Modul Invoice', "qr-invoice" );

	return $columns;

}

add_filter( 'manage_edit-shop_order_columns', 'wqrmi_custom_column_name' );


function wqrmi_custom_column_value( $column_name, $id ) {

	if ( $column_name == 'qrmodule-invoice' ) {
		$qrmodul_invoice_url = get_post_meta( $id, 'qrmodul-invoice-url', true );
		if ( $qrmodul_invoice_url ) {
			echo "<a target='_blank' download href='" . esc_url( $qrmodul_invoice_url ) . "'>" .esc_html__( "Download Invoice", "qr-invoice" ). "</a>";
		} else {
			echo esc_html__( "no invoice...", "qr-invoice" );
		}
	}

}

add_action( 'manage_shop_order_posts_custom_column', 'wqrmi_custom_column_value', 1, 2 );


function wqrmi_woocommerce_submenu() {

	if ( class_exists( 'WooCommerce' ) ) {
		add_submenu_page( 'woocommerce', esc_html__( "QR Invoice Plugin", "qr-invoice" ), esc_html__( "QR Invoice Plugin", "qr-invoice" ), 'manage_options', 'options-general.php?page=wqrmi-options' );
	}

}

add_action( 'admin_menu', 'wqrmi_woocommerce_submenu' );


