<?php

class WqrmiSettingPage {

	/**
	 * Holds the values to be used in the fields callbacks
	 */
	private $options;

	/**
	 * Start up
	 */
	public function __construct() {

		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
			add_action( 'admin_menu', array( $this, 'add_wqrmi_settings_page' ) );
			add_action( 'admin_init', array( $this, 'wqrmi_settings_page_init' ) );
		}

	}

	/**
	 * Add options page
	 */
	public function add_wqrmi_settings_page() {
		// This page will be under "Settings"
		add_options_page(
			esc_html__( "Settings Admin", "qr-invoice" ),
			esc_html__( "QR Invoice Plugin", "qr-invoice" ),
			'manage_options',
			'wqrmi-options',
			array( $this, 'create_wqrmi_setting_page' )
		);
	}

	/**
	 * Options page callback
	 */
	public function create_wqrmi_setting_page() {
		// Set class property
		$this->options = get_option( 'wqrmi_option' );
		global $wqrmi_api_docs_url, $wqrmi_reg_url, $wqrmi_email;
	?>

        <div class="wrap">
            <h1><?php echo esc_html__( "QR Invoice Plugin", "qr-invoice" ); ?></h1>
            <p>
		<?php echo esc_html__( "Thank you for installing the QR Invoice plugin.", "qr-invoice" ); ?>
                <br>
		<?php echo esc_html__( "This plugin requires an account and the API key from QRModul.ch.", "qr-invoice" ); ?>
                <br>
		<?php echo esc_html__( "Please register", "qr-invoice" ); ?> <a target="_blank" href="<?php echo esc_url( $wqrmi_reg_url ); ?>"><?php echo esc_html__( "here", "qr-invoice" ); ?></a>, <?php echo esc_html__( "open an account with a few clicks and enter the key.", "qr-invoice" ); ?>
            </p>

            <div class="marketing-box" style="border:2px solid red; padding-left: 15px; padding-right: 15px; margin-bottom: 20px; display: inline-block;">
                <p>
                    <?php echo esc_html__( "QR Modul Plugin for WooCommerce does offer to send the invoices via postal mail.", "qr-invoice" ); ?>
                    <br>
                    <?php echo esc_html__( "Based on surveys this does shorten the time it takes until your customers pay the Invoices on average.", "qr-invoice" ); ?>
                </p>
                <p>
                    <?php echo esc_html__( "Should you like to add your Logo on the envelops or do specific marketing campaigns by attaching brochures or flyers to your Invoices, please feel free to get in contact by e-Mail", "qr-invoice" ); ?>
                    <br>
                    <a href="<?php echo esc_url( 'mailto:' . $wqrmi_email, array( 'mailto' ) ); ?>"><?php echo esc_html( $wqrmi_email ); ?></a> 
                </p>
            </div>

            <form method="post" action="options.php">
                <?php
                    // This prints out all hidden setting fields
                    settings_fields( 'wqrmi_data_option_group' );
                    do_settings_sections( 'wqrmi-options' );
                    submit_button();
                ?>
            </form>
        </div>

	<?php
	}

	/**
	 * Register and add settings
	 */
	public function wqrmi_settings_page_init() {
		register_setting(
			'wqrmi_data_option_group', // Option group
			'wqrmi_option', // Option name
			'wqrmi_option_email',
			array( $this, 'sanitize' ) // Sanitize
		);


		add_settings_section(
			'setting_section_id', 
			'', 
			array( $this, 'print_section_info' ), 
			'wqrmi-options' 
		);


		add_settings_field(
			'wqrmi_client_id', 
			esc_html__( "Client ID", "qr-invoice" ), 
			array( $this, 'wqrmi_client_id_callback' ), 
			'wqrmi-options', 
			'setting_section_id' 
		);


		add_settings_field(
			'wqrmi_client_secret', 
			esc_html__( "Client Secret", "qr-invoice" ), 
			array( $this, 'wqrmi_client_secret_callback' ), 
			'wqrmi-options', 
			'setting_section_id' 
		);


		add_settings_field(
			'wqrmi_token_duration', 
			esc_html__( "Client Token Duration (seconds)", "qr-invoice" ), 
			array( $this, 'wqrmi_token_duration_callback' ), 
			'wqrmi-options', 
			'setting_section_id' 
		);


		add_settings_field(
			'wqrmi_profile_id', 
			esc_html__( "Profile ID", "qr-invoice" ), 
			array( $this, 'wqrmi_profile_id_callback' ), 
			'wqrmi-options', 
			'setting_section_id' 
		);


		add_settings_field(
			'wqrmi_send_invoice', 
			esc_html__( "Send Invoice", "qr-invoice" ), 
			array( $this, 'wqrmi_send_invoice_callback' ), 
			'wqrmi-options', 
			'setting_section_id' 
		);


		add_settings_field(
			'wqrmi_letter_dispatch_priority', 
			esc_html__( "Letter Dispatch Priority", "qr-invoice" ), 
			array( $this, 'wqrmi_letter_dispatch_priority_callback' ), 
			'wqrmi-options', 
			'setting_section_id' 
		);


		add_settings_field(
			'wqrmi_my_climate', 
			esc_html__( "My Climate", "qr-invoice" ), 
			array( $this, 'wqrmi_my_climate_callback' ), 
			'wqrmi-options', 
			'setting_section_id' 
		);


		add_settings_field(
			'wqrmi_color', 
			esc_html__( "Color", "qr-invoice" ), 
			array( $this, 'wqrmi_color_callback' ), 
			'wqrmi-options', 
			'setting_section_id' 
		);


		add_settings_field(
			'wqrmi_franking_envelopes', 
			esc_html__( "Franking Envelopes", "qr-invoice" ), 
			array( $this, 'wqrmi_franking_envelopes_callback' ), 
			'wqrmi-options', 
			'setting_section_id' 
		);


		add_settings_field(
			'wqrmi_address', 
			esc_html__( "Address", "qr-invoice" ), 
			array( $this, 'wqrmi_address_callback' ), 
			'wqrmi-options', 
			'setting_section_id' 
		);


		add_settings_field(
			'wqrmi_postal_tariff', 
			esc_html__( "Postal Tariff", "qr-invoice" ), 
			array( $this, 'wqrmi_postal_tariff_callback' ), 
			'wqrmi-options', 
			'setting_section_id' 
		);


		add_settings_field(
			'wqrmi_remarks_to_printshop', 
			esc_html__( "Remarks to Printshop", "qr-invoice" ), 
			array( $this, 'wqrmi_remarks_to_printshop_callback' ), 
			'wqrmi-options', 
			'setting_section_id' 
		);


		add_settings_field(
			'wqrmi_type_invoice', 
			esc_html__( "Type Invoice", "qr-invoice" ), 
			array( $this, 'wqrmi_type_invoice_callback' ), 
			'wqrmi-options', 
			'setting_section_id' 
		);


		add_settings_field(
			'wqrmi_template_invoice', 
			esc_html__( "Invoice Template Name", "qr-invoice" ), 
			array( $this, 'wqrmi_template_invoice_callback' ), 
			'wqrmi-options', 
			'setting_section_id' 
		);
                
                
                add_settings_field(
			'wqrmi_payable_invoice', 
			esc_html__( "Invoice Payable Until", "qr-invoice" ), 
			array( $this, 'wqrmi_payable_invoice_callback' ), 
			'wqrmi-options', 
			'setting_section_id'
		);


		add_settings_field(
			'wqrmi_vat_invoice', 
			esc_html__( "Invoice VAT", "qr-invoice" ), 
			array( $this, 'wqrmi_vat_invoice_callback' ), 
			'wqrmi-options', 
			'setting_section_id' 
		);
                
                add_settings_field(
			'wqrmi_shipping_percentage_vat', 
			esc_html__( "VAT on Shipping Costs", "qr-invoice" ), 
			array( $this, 'wqrmi_shipping_percentage_vat_callback' ), 
			'wqrmi-options', 
			'setting_section_id' 
		);
                
                
                add_settings_section(
			'setting_section_id2', 
			'', 
			array( $this, 'print_section_reference_info' ), 
			'wqrmi-options' 
		);

                
                add_settings_field(
			'wqrmi_reference_type', 
			esc_html__( "Reference Type", "qr-invoice" ), 
			array( $this, 'wqrmi_reference_type_callback' ), 
			'wqrmi-options', 
			'setting_section_id2' 
		);
                
                
                add_settings_field(
			'wqrmi_reference_custom_id', 
			esc_html__( "Reference Custom Id", "qr-invoice" ), 
			array( $this, 'wqrmi_reference_custom_id_callback' ), 
			'wqrmi-options', 
			'setting_section_id2' 
		);
                
                
                add_settings_field(
			'wqrmi_reference_date', 
			esc_html__( "Reference Date", "qr-invoice" ), 
			array( $this, 'wqrmi_reference_date_callback' ), 
			'wqrmi-options', 
			'setting_section_id2' 
		);
                
                
                add_settings_field(
			'wqrmi_test_connection', 
			esc_html__( "Connection", "qr-invoice" ), 
			array( $this, 'wqrmi_test_connection_callback' ), 
			'wqrmi-options', 
			'setting_section_id2' 
		);
	}

	/**
	 * Sanitize each setting field as needed
	 *
	 * @param array $input Contains all settings fields as array keys
	 */
	public function sanitize( $input ) {

		return $input;
	}

	/**
	 * Print the Section text
	 */
	public function print_section_info() {
		print  esc_html__( "Enter your settings below:", "qr-invoice" );
	}
        
        
        public function print_section_reference_info() {
		print  esc_html__( "Enter your reference info:", "qr-invoice" );
	}


	public function wqrmi_client_id_callback() {
		printf(
			'<input type="text" id="wqrmi_client_id" name="wqrmi_option[wqrmi_client_id]" value="%s" />',
			isset( $this->options['wqrmi_client_id'] ) ? esc_attr( $this->options['wqrmi_client_id'] ) : ''
		);
	}

	public function wqrmi_client_secret_callback() {
		printf(
			'<input type="text" id="wqrmi_client_secret" name="wqrmi_option[wqrmi_client_secret]" value="%s" />',
			isset( $this->options['wqrmi_client_secret'] ) ? esc_attr( $this->options['wqrmi_client_secret'] ) : ''
		);
	}


	public function wqrmi_token_duration_callback() {
		printf(
			'<input type="text" id="wqrmi_token_duration" name="wqrmi_option[wqrmi_token_duration]" value="%s" />',
			isset( $this->options['wqrmi_token_duration'] ) ? esc_attr( $this->options['wqrmi_token_duration'] ) : '2592000'
		);
	}


	public function wqrmi_profile_id_callback() {
		printf(
			'<input type="text" id="wqrmi_profile_id" name="wqrmi_option[wqrmi_profile_id]" value="%s" />',
			isset( $this->options['wqrmi_profile_id'] ) ? esc_attr( $this->options['wqrmi_profile_id'] ) : ''
		);

		echo "<br><small>- " . esc_html__( "If this field is left blank, then the default profile is used.", "qr-invoice" ) . "</small>";
		echo "<br><small>- " . esc_html__( "To get Profile ID you can on the page", "qr-invoice" ) . " <a target='_blank' href='" . esc_url( "https://kmuqr.quickapps.mx/en/cockpit/master-data" ) . "'>" . esc_html__( "Master data", "qr-invoice" ) . "</a>.</small>";
	}


	public function wqrmi_send_invoice_callback() {
            
                if ( isset( $this->options['wqrmi_send_invoice'] ) && $this->options['wqrmi_send_invoice'] == 1 ) {
                        $checked = 1;
                } else {
                        $checked = '';
                }

                ?>
                <label><input <?php checked( $checked, 1 ); ?> type="checkbox" id="wqrmi_send_invoice" name="wqrmi_option[wqrmi_send_invoice]" value="1"/> <?php echo esc_html__( "Mail (SwissPost A/B Priority)", "qr-invoice" ); ?>
                </label>
                <br>
                <small>- <?php echo esc_html__( "If checked, then invoice will send via Mail. Chargeable option is billed via QR module.", "qr-invoice" ); ?></small>
                <br>
                <small>- <?php echo esc_html__( "By default, invoices are sent via WooCommerce as an email including the QR payment part.", "qr-invoice" ); ?></small>

	<?php
	}


	public function wqrmi_letter_dispatch_priority_callback() {

		$selected_standart = "";
		$selected_express  = "";

		if ( isset( $this->options['wqrmi_letter_dispatch_priority'] ) ) {
			$wqrmi_letter_dispatch_priority = $this->options['wqrmi_letter_dispatch_priority'];
			if ( $wqrmi_letter_dispatch_priority == 'standard' ) {
				$selected_standart = 'selected="selected"';
			}

			if ( $wqrmi_letter_dispatch_priority == 'express' ) {
				$selected_express = 'selected="selected"';
			}
		}


		echo '<select name="wqrmi_option[wqrmi_letter_dispatch_priority]">
               <option value="none">None</option>
               <option ' . $selected_standart . ' value="standard">' . esc_html__( "Standard", "qr-invoice" ) . '</option>
               <option ' . $selected_express . ' value="express">' . esc_html__( "Express", "qr-invoice" ) . '</option></select>';

		echo "<br><small>- " . esc_html__( "The letter's priority. The possible values are: 'standard' or 'express'.", "qr-invoice" ) . "</small>";

	}


	public function wqrmi_my_climate_callback() {

		$selected_my_climate_yes = "";
		$selected_my_climate_no  = "";

		if ( isset( $this->options['wqrmi_my_climate'] ) ) {
			$wqrmi_my_climate = $this->options['wqrmi_my_climate'];
			if ( $wqrmi_my_climate == 'true' ) {
				$selected_my_climate_yes = 'selected="selected"';
			}

			if ( $wqrmi_my_climate == 'false' ) {
				$selected_my_climate_no = 'selected="selected"';
			}
		}

		echo '<select name="wqrmi_option[wqrmi_my_climate]" id="wqrmi_my_climate">
                <option ' . $selected_my_climate_yes . ' value="true">' . esc_html__( "Yes", "qr-invoice" ) . '</option>
                <option ' . $selected_my_climate_no . ' value="false">' . esc_html__( "No", "qr-invoice" ) . '</option></select>';

		echo "<br><small>- " . esc_html__( "A Flag indicating if the post office should use environmentally friendly papel.", "qr-invoice" ) . "</small>";

	}


	public function wqrmi_color_callback() {

		$selected_color_colors = "";
		$selected_color_black  = "";

		if ( isset( $this->options['wqrmi_color'] ) ) {
			$wqrmi_color = $this->options['wqrmi_color'];
			if ( $wqrmi_color == 'color' ) {
				$selected_color_colors = 'selected="selected"';
			}

			if ( $wqrmi_color == 'black_and_white' ) {
				$selected_color_black = 'selected="selected"';
			}
		}

		echo '<select name="wqrmi_option[wqrmi_color]">
               <option ' . $selected_color_colors . ' value="color">' . esc_html__( "Color", "qr-invoice" ) . '</option>
               <option ' . $selected_color_black . ' value="black_and_white">' . esc_html__( "Black and White", "qr-invoice" ) . '</option></select>';

		echo "<br><small>- " . esc_html__( "The letter's color. The possible values are: 'Color' or 'Black and White'", "qr-invoice" ) . "</small>";

	}


	public function wqrmi_franking_envelopes_callback() {

		$selected_a = "";
		$selected_b = "";

		if ( isset( $this->options['wqrmi_franking_envelopes'] ) ) {
			$wqrmi_franking_envelopes = $this->options['wqrmi_franking_envelopes'];
			if ( $wqrmi_franking_envelopes == 'A' ) {
				$selected_a = 'selected="selected"';
			}

			if ( $wqrmi_franking_envelopes == 'B' ) {
				$selected_b = 'selected="selected"';
			}
		}


		echo '<select name="wqrmi_option[wqrmi_franking_envelopes]" id="wqrmi_franking_envelopes">
               <option value="">None</option>
               <option ' . $selected_a . ' value="A">A</option>
               <option ' . $selected_b . ' value="B">B</option></select>';

		echo "<br><small>- " . esc_html__( "The letter's franking evelopes. The possible values are: 'A','B' or 'none'. IF the generated file is payment part only then 'none' is the valid option.", "qr-invoice" ) . "</small>";
		echo "<br><small>- " . esc_html__( "If the generated file is a template invoice then 'A', 'B' or 'none' are valid options.", "qr-invoice" ) . "</small>";

	}


	public function wqrmi_address_callback() {
		printf(
			'<input type="text" id="wqrmi_address" name="wqrmi_option[wqrmi_address]" value="%s" />',
			isset( $this->options['wqrmi_address'] ) ? esc_attr( $this->options['wqrmi_address'] ) : ''
		);

		echo "<br><small>- " . esc_html__( "The address to send the letter. The address cannot conatin more than 4 lines with a max of 35 characters for each line.", "qr-invoice" ) . "</small>";
	}


	public function wqrmi_postal_tariff_callback() {
		$selected_postpac_priority = "";
		$selected_swiss_express    = "";

		if ( isset( $this->options['wqrmi_postal_tariff'] ) ) {
			$wqrmi_postal_tariff = $this->options['wqrmi_postal_tariff'];
			if ( $wqrmi_postal_tariff == 'postpac_priority' ) {
				$selected_postpac_priority = 'selected="selected"';
			}

			if ( $wqrmi_postal_tariff == 'swiss_express' ) {
				$selected_swiss_express = 'selected="selected"';
			}
		}


		echo '<select name="wqrmi_option[wqrmi_postal_tariff]" id="wqrmi_postal_tariff">
               <option value="">None</option>
               <option ' . $selected_postpac_priority . ' value="postpac_priority">Postpac Priority</option>
               <option ' . $selected_swiss_express . ' value="swiss_express">Swiss Express</option></select>';

		echo "<br><small>- " . esc_html__( "The collective address' postal tariff. The possible values are: 'postpac_priority' or 'swiss_express'.", "qr-invoice" ) . "</small>";

	}


	public function wqrmi_remarks_to_printshop_callback() {
		printf(
			'<input type="text" id="wqrmi_remarks_to_printshop" name="wqrmi_option[wqrmi_remarks_to_printshop]" value="%s" />',
			isset( $this->options['wqrmi_remarks_to_printshop'] ) ? esc_attr( $this->options['wqrmi_remarks_to_printshop'] ) : ''
		);

		echo "<br><small>- " . esc_html__( "The Letter dispatch's remarks to the printshop. The remarks cannot contain more than 2 lines with a max of 45 characters for each line.", "qr-invoice" ) . "</small>";
	}


	public function wqrmi_type_invoice_callback() {
		if ( isset( $this->options['wqrmi_type_invoice'] ) && $this->options['wqrmi_type_invoice'] == 1 ) {
			$checked = 1;
		} else {
			$checked = '';
		}
		?>
        <label><input <?php checked( $checked, 1 ); ?> type="checkbox" id="wqrmi_type_invoice"  name="wqrmi_option[wqrmi_type_invoice]" value="1"/> <?php echo esc_html__( "QR Payment part only", "qr-invoice" ); ?>
        </label>
        <br>
        <small>- <?php echo esc_html__( "If checked, then invoices will display only payment part", "qr-invoice" ); ?></small>
        <br>
        <small>- <?php echo esc_html__( "By default, invoices displays products part and payment part.", "qr-invoice" ); ?></small>
		<?php
	}


	public function wqrmi_vat_invoice_callback() {
		if ( isset( $this->options['wqrmi_vat_invoice'] ) && $this->options['wqrmi_vat_invoice'] == 1 ) {
			$checked = 1;
		} else {
			$checked = '';
		}
		?>
        <label>
            <input <?php checked( $checked, 1 ); ?> type="checkbox" id="wqrmi_vat_invoice" name="wqrmi_option[wqrmi_vat_invoice]" value="1"/>
            <?php echo esc_html__( "Calculate VAT", "qr-invoice" ); ?>
        </label>
        <br>
        <small>- <?php echo esc_html__( "A flag to indicate the system if it should calculate the VAT based on the amount or over the amount.", "qr-invoice" ); ?></small>
		<?php
	}
        

	public function wqrmi_template_invoice_callback() {
                global $wqrmi_template_url;
                
		printf(
			'<input type="text" id="wqrmi_template_invoice" name="wqrmi_option[wqrmi_template_invoice]" value="%s" />',
			isset( $this->options['wqrmi_template_invoice'] ) ? esc_attr( $this->options['wqrmi_template_invoice'] ) : ''
		);

		echo "<br><small>- " . esc_html__( "If this field is left blank, then the default template invoice is used.", "qr-invoice" ) . "</small>";
		echo "<br><small>- " . __( "To get Invoice Template Name you can on the page <a target='_blank' href='" . esc_url( $wqrmi_template_url ) . "'>Invoice Template</a> in the column Template Name.", "wqrmi" ) . "</small>";
	}
        
        
        public function wqrmi_payable_invoice_callback() {
                global $wqrmi_template_url;
                
		printf(
			'<input type="number" id="wqrmi_payable_invoice" name="wqrmi_option[wqrmi_payable_invoice]" value="%s" />',
			isset( $this->options['wqrmi_payable_invoice'] ) ? esc_attr( $this->options['wqrmi_payable_invoice'] ) : ''
		);

		echo "<br><small>- " . esc_html__( "The number of days from the date of creation of the order, during which payment must be made according to the invoice.", "qr-invoice" ) . "</small>";
		
	}
        
        
         public function wqrmi_shipping_percentage_vat_callback() {
                global $wqrmi_template_url;
                
		printf(
			'<input type="text" id="wqrmi_shipping_percentage_vat" name="wqrmi_option[wqrmi_shipping_percentage_vat]" value="%s" />',
			isset( $this->options['wqrmi_shipping_percentage_vat'] ) ? esc_attr( $this->options['wqrmi_shipping_percentage_vat'] ) : ''
		);
                echo ' %';
		echo "<br><small>- " . esc_html__( "Please enter percentage of Vat. Example - 7.7", "qr-invoice" ) . "</small>";
		
	}
        
        
        public function wqrmi_reference_type_callback() {
		$selected_qrr = "";
		$selected_scor    = "";

		if ( isset( $this->options['wqrmi_reference_type'] ) ) {
			$wqrmi_reference_type = $this->options['wqrmi_reference_type'];
			if ( $wqrmi_reference_type == 'QRR' ) {
				$selected_qrr = 'selected="selected"';
			}

			if ( $wqrmi_reference_type == 'SCOR' ) {
				$selected_scor = 'selected="selected"';
			}
		}


	       echo '<select name="wqrmi_option[wqrmi_reference_type]" id="wqrmi_reference_type">
               <option value="">None</option>
               <option ' . $selected_qrr . ' value="QRR">QRR</option>
               <option ' . $selected_scor . ' value="SCOR">SCOR</option></select>';

		echo "<br><small>- " . esc_html__( "The type of reference to generate. The possible values are: 'QRR','SCOR'.", "qr-invoice" ) . "</small>";

	}
        
        
        
        public function wqrmi_reference_custom_id_callback() {
		printf(
			'<input maxlength="10" type="text" id="wqrmi_reference_custom_id" name="wqrmi_option[wqrmi_reference_custom_id]" value="%s" />',
			isset( $this->options['wqrmi_reference_custom_id'] ) ? esc_attr( $this->options['wqrmi_reference_custom_id'] ) : ''
		);
                
                echo "<br><small>- " . esc_html__( "The id to use to generate reference. i if the reference type is QRR the maximum length of this id is 10, other hand if is SCOR the length is 6.", "qr-invoice" ) . "</small>";
	}
        
        
         public function wqrmi_reference_date_callback() {
		printf(
			'<input type="text" id="wqrmi_reference_date" name="wqrmi_option[wqrmi_reference_date]" value="%s" />',
			isset( $this->options['wqrmi_reference_date'] ) ? esc_attr( $this->options['wqrmi_reference_date'] ) : ''
		);
                
                echo "<br><small>- " . esc_html__( "The date to use to generate reference. The format of date is DD/MM/YYYY", "qr-invoice" ) . "</small>";
	}


	public function wqrmi_test_connection_callback() {
		if ( $this->options['wqrmi_client_id'] && $this->options['wqrmi_client_secret'] && $this->options['wqrmi_token_duration'] ) {
			echo '<button type="button" id="wqrmi_test_connection">Connection & Authorization</button>';
			printf(
				'<input type="hidden" id="wqrmi_access_token" name="wqrmi_option[wqrmi_access_token]" value="%s" />',
				isset( $this->options['wqrmi_access_token'] ) ? esc_attr( $this->options['wqrmi_access_token'] ) : ''
			);
		} else {
			echo esc_html__( "Please fill out all fields above!", "qr-invoice" );
		}
	}

}

if ( is_admin() ) {
	$WqrmiSettingPage = new WqrmiSettingPage();
}

function wqrmi_print_scripts() {
	if ( isset( $_GET['page'] ) && $_GET['page'] == 'wqrmi-options' ) {
            $plugin_url_css = plugins_url( '/css/jquery-ui.min.css', __FILE__ );
            wp_enqueue_script('jquery-ui-datepicker');
            wp_enqueue_style('jquery-ui-css', $plugin_url_css);
	?>
        <script type='text/javascript'>
            jQuery(document).ready(function ($) {
                wqrmi_validate_fields();
                jQuery('#wqrmi_test_connection').click(function () {
                    jQuery.ajax({
                        type: "POST",
                        url: "<?php echo admin_url( 'admin-ajax.php' ); ?>",
                        data: {'action': 'wqrmi_connect_auth'},
                        success: function (response) {
                            response = jQuery.parseJSON(response);
                            if (response.status == '200') {
                                jQuery('#wqrmi_access_token').val(response.wqrmi_access_token);
                                alert(response.message);
                                jQuery('input#submit').click();
                            } else {
                                alert(response.message);
                            }
                        }
                    });
                });


                jQuery('#wqrmi_type_invoice').change(function () {
                    wqrmi_validate_fields();
                });
                
                jQuery('#wqrmi_reference_type').change(function(){
                    var thisElementVal = jQuery(this).val();
                    if(thisElementVal == 'QRR'){
                        jQuery('#wqrmi_reference_custom_id').attr('maxlength', '10').val('');
                    }
                    if(thisElementVal == 'SCOR'){
                        jQuery('#wqrmi_reference_custom_id').attr('maxlength', '6').val('');
                    }
                });
                
                
                jQuery('#wqrmi_reference_date').datepicker({
                    dateFormat : 'dd/mm/yy'
                });
                
            });

            function wqrmi_validate_fields() {
                var thisElement = jQuery('#wqrmi_type_invoice');
                if (thisElement.prop('checked')) {
                    jQuery('#wqrmi_franking_envelopes option[value="A"], #wqrmi_franking_envelopes option[value="B"]').hide();
                    jQuery('#wqrmi_franking_envelopes').val('');
                    jQuery('#wqrmi_address, #wqrmi_postal_tariff').attr('required', 'required');
                    jQuery('#wqrmi_my_climate option').attr('selected', false);
                    jQuery('#wqrmi_my_climate option[value="true"]').hide();
                    jQuery('#wqrmi_my_climate').val('false');
                } else {
                    jQuery('#wqrmi_franking_envelopes option[value="A"], #wqrmi_franking_envelopes option[value="B"]').show();
                    jQuery('#wqrmi_my_climate option[value="true"]').show();
                    jQuery('#wqrmi_address, #wqrmi_postal_tariff').attr('required', false);
                    jQuery('#wqrmi_address, #wqrmi_postal_tariff').val('');
                }
            }
            
            
           
        </script>
	<?php
	}
}

add_action( 'admin_footer', 'wqrmi_print_scripts' );



