<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link  https://www.procomsoftsol.com
 * @since 1.0.0
 *
 * @package    Woo_Order_Auto_Print
 * @subpackage Woo_Order_Auto_Print/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Woo_Order_Auto_Print
 * @subpackage Woo_Order_Auto_Print/admin
 */
class Woo_Order_Auto_Print_Admin {


	/**
	 * The ID of this plugin.
	 *
	 * @since  1.0.0
	 * @var    string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since  1.0.0
	 * @var    string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since 1.0.0
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version     The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

		add_action( 'admin_head', array( $this, 'remove_menus' ) );
		add_filter( 'submenu_file', array( $this, 'submenu_file' ), 99, 2 );
		add_action( 'cmb2_admin_init', array( $this, 'register_options' ) );

		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 11 );

		add_filter( 'cmb2_render_country_state', array( $this, 'render_country_state_callback' ), 10, 5 );

		add_action( 'wp_ajax_woo-order-auto-print', array( $this, 'woo_order_auto_print_task' ) );

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/woo-order-auto-print-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/woo-order-auto-print-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Remove menus.
	 *
	 * @since 1.0.0
	 */
	public function remove_menus() {
		global $submenu;
		if ( isset( $submenu['woocommerce'] ) ) {
			$hide_menus = $this->get_settings_sub();
			foreach ( $submenu['woocommerce'] as $k => $v ) {
				if ( in_array( $v[2], $hide_menus, true ) ) {
					unset( $submenu['woocommerce'][ $k ] );
				}
			}
		}
	}

	/**
	 * Change the submenu file name.
	 *
	 * @since  1.0.0
	 * @param  string $submenu_file Submenu file.
	 * @param  string $parent_file  Parent file.
	 * @return string    $submenu_file  Submenu file.
	 */
	public function submenu_file( $submenu_file, $parent_file ) {
		$sub_menu_items = $this->get_settings_sub();
		if ( 'woocommerce' == $parent_file && isset( $_GET['page'] ) && in_array( $_GET['page'], $sub_menu_items ) ) {
			return 'woo_order_auto_print';
		}
		return $submenu_file;
	}

	/**
	 * Register plugin options for settings
	 *
	 * @since 1.0.0
	 */
	public function register_options() {
		/**
		 * Registers main options page menu item and form.
		 */
		$args = array(
			'id'           => 'woo_order_auto_print',
			'title'        => esc_html__( 'Auto Print Settings', 'automatic-order-printing-for-woocommerce' ),
			'object_types' => array( 'options-page' ),
			'option_key'   => 'woo_order_auto_print',
			'tab_group'    => 'woo_order_auto_print',
			'tab_title'    => esc_html__( 'General', 'automatic-order-printing-for-woocommerce' ),
			'parent_slug'  => 'woocommerce',
		);

		$main_options = new_cmb2_box( $args );

		$fields = $this->get_general_settings();
		foreach ( $fields as $field ) {
			$main_options->add_field( $field );
		}

		/**
		 * Registers strings options page menu item and form.
		 */
		$args = array(
			'id'           => 'woo_order_auto_print_strings',
			'title'        => esc_html__( 'Auto Print Settings', 'automatic-order-printing-for-woocommerce' ),
			'object_types' => array( 'options-page' ),
			'option_key'   => 'woo_order_auto_print_strings',
			'tab_group'    => 'woo_order_auto_print',
			'tab_title'    => esc_html__( 'Strings', 'automatic-order-printing-for-woocommerce' ),
			'parent_slug'  => 'woocommerce',
		);

		$main_options = new_cmb2_box( $args );

		$fields = $this->get_strings_settings();
		foreach ( $fields as $field ) {
			$main_options->add_field( $field );
		}
	}

	/**
	 * Return default woocommerce settings
	 *
	 * @since  1.0.0
	 * @return array     $data
	 */
	public function get_woocommerce_store_address() {
		$fields = array( 'store_address', 'store_address_2', 'store_city', 'default_country', 'store_postcode' );
		$data   = array();
		foreach ( $fields as $field ) {
			$data[ $field ] = get_option( "woocommerce_{$field}", '' );
		}
		return $data;
	}

	/**
	 * Return general settings fields
	 *
	 * @since  1.0.0
	 * @return array     $fields
	 */
	public function get_general_settings() {
		$fields       = array();
		$default_data = $this->get_woocommerce_store_address();

		$fields['enable2'] = array(
			'name' => esc_html__( 'Enable', 'automatic-order-printing-for-woocommerce' ),
			'desc' => esc_html__( 'Enable PrintNode Auto Print', 'automatic-order-printing-for-woocommerce' ),
			'id'   => 'enable',
			'type' => 'checkbox',
		);

		$fields['enable'] = array(
			'name' => esc_html__( 'Enable', 'automatic-order-printing-for-woocommerce' ),
			'desc' => esc_html__( 'Enable PrintNode Auto Print', 'automatic-order-printing-for-woocommerce' ),
			'id'   => 'enable',
			'type' => 'checkbox',
		);

		$fields['printnode_api_key'] = array(
			'name' => esc_html__( 'PrintNode API Key', 'automatic-order-printing-for-woocommerce' ),
			/* translators: %1$s: Link staet, %2$s : Link End. */
			'desc' => sprintf( esc_html__( 'Enter PrintNode API key here. You can get a key from %1$shere%2$s', 'automatic-order-printing-for-woocommerce' ), '<a href="https://app.printnode.com/app/apikeys" target="_blank">', '</a>' ),
			'id'   => 'printnode_api_key',
			'type' => 'text',
		);

		$desc = esc_html__( 'Enable the logging of errors.', 'automatic-order-printing-for-woocommerce' );

		if ( defined( 'WC_LOG_DIR' ) ) {
			$log_url = add_query_arg( 'tab', 'logs', add_query_arg( 'page', 'wc-status', admin_url( 'admin.php' ) ) );
			$log_key = $this->plugin_name . '-' . sanitize_file_name( wp_hash( $this->plugin_name ) ) . '-log';
			$log_url = add_query_arg( 'log_file', $log_key, $log_url );
			/* translators: %1$s: Link staet, %2$s : Link End. */
			$desc .= ' | ' . sprintf( esc_html__( '%1$sView Log%2$s', 'automatic-order-printing-for-woocommerce' ), '<a href="' . esc_url( $log_url ) . '">', '</a>' );
		}

		$fields['enable_log'] = array(
			'name' => esc_html__( 'Enable Logging', 'automatic-order-printing-for-woocommerce' ),
			'desc' => $desc,
			'id'   => 'enable_log',
			'type' => 'checkbox',
		);

		$fields['skip_cron'] = array(
			'name' => esc_html__( 'Skip WP Cron', 'automatic-order-printing-for-woocommerce' ),
			'desc' => esc_html__( 'Skip WP Cron and print directly.( don\'t enable this until WP Cron not working fine on the website. )', 'automatic-order-printing-for-woocommerce' ),
			'id'   => 'skip_cron',
			'type' => 'checkbox',
		);

		$fields['use_mpdf'] = array(
			'name' => esc_html__( 'Use mPDF', 'automatic-order-printing-for-woocommerce' ),
			'desc' => wp_kses_post( "<strong>Use mPDF libreary insted of DomPDF libreary for RTL support. Install <a href='https://wooxperts.club/addons/juhyf676dsad0912kjfd/automatic-order-printing-for-woocommerce-mpdf-addon.zip' target='_blank'>Automatic Order Printing for WooCommerce mPDF Addon</a>. For mPDF Support.</strong>", 'automatic-order-printing-for-woocommerce' ),
			'id'   => 'use_mpdf',
			'type' => 'checkbox',
		);

		$fields['address_title'] = array(
			'name' => esc_html__( 'Store Address', 'automatic-order-printing-for-woocommerce' ),
			'desc' => esc_html__( 'It will display on Invoice and Packing Slips', 'automatic-order-printing-for-woocommerce' ),
			'id'   => 'address_title',
			'type' => 'title',
		);

		$fields['store_name'] = array(
			'name' => esc_html__( 'Store Name', 'automatic-order-printing-for-woocommerce' ),
			'desc' => esc_html__( 'It will display on Invoice and Packing Slips', 'automatic-order-printing-for-woocommerce' ),
			'id'   => 'store_name',
			'type' => 'text',
		);

		$fields['store_address']   = array(
			'name'    => esc_html__( 'Address line 1', 'automatic-order-printing-for-woocommerce' ),
			'desc'    => esc_html__( 'The street address for your business location.', 'automatic-order-printing-for-woocommerce' ),
			'id'      => 'store_address',
			'type'    => 'text',
			'default' => $default_data['store_address'],
		);
		$fields['store_address_2'] = array(
			'name'    => esc_html__( 'Address line 2', 'automatic-order-printing-for-woocommerce' ),
			'desc'    => esc_html__( 'An additional, optional address line for your business location.', 'automatic-order-printing-for-woocommerce' ),
			'id'      => 'store_address_2',
			'type'    => 'text',
			'default' => $default_data['store_address_2'],
		);
		$fields['store_city']      = array(
			'name'    => esc_html__( 'City', 'automatic-order-printing-for-woocommerce' ),
			'desc'    => esc_html__( 'The city in which your business is located.', 'automatic-order-printing-for-woocommerce' ),
			'id'      => 'store_city',
			'type'    => 'text',
			'default' => $default_data['store_city'],
		);
		$fields['country_state']   = array(
			'name'    => esc_html__( 'Country / State', 'automatic-order-printing-for-woocommerce' ),
			'desc'    => esc_html__( 'The country and state or province, if any, in which your business is located.', 'automatic-order-printing-for-woocommerce' ),
			'id'      => 'store_country_state',
			'type'    => 'country_state',
			'default' => $default_data['default_country'],
		);
		$fields['store_postcode']  = array(
			'name'    => esc_html__( 'Postcode / ZIP', 'automatic-order-printing-for-woocommerce' ),
			'desc'    => esc_html__( 'The postal code, if any, in which your business is located.', 'automatic-order-printing-for-woocommerce' ),
			'id'      => 'store_postcode',
			'type'    => 'text',
			'default' => $default_data['store_postcode'],
		);

		$fields['store_logo_title'] = array(
			'name' => esc_html__( 'Logo', 'automatic-order-printing-for-woocommerce' ),
			'desc' => esc_html__( 'It will display on Invoice and Packing Slips', 'automatic-order-printing-for-woocommerce' ),
			'id'   => 'store_logo_title',
			'type' => 'title',
		);

		$fields['store_logo'] = array(
			'name'       => esc_html__( 'Logo', 'automatic-order-printing-for-woocommerce' ),
			'desc'       => esc_html__( 'Header logo.', 'automatic-order-printing-for-woocommerce' ),
			'id'         => 'store_logo',
			'type'       => 'file',
			'query_args' => array(
				'type' => array(
					'image/gif',
					'image/jpeg',
					'image/png',
				),
			),
		);

		$fields['store_footer_title'] = array(
			'name' => esc_html__( 'Footer', 'automatic-order-printing-for-woocommerce' ),
			'desc' => esc_html__( 'It will display on Invoice and Packing Slips', 'automatic-order-printing-for-woocommerce' ),
			'id'   => 'store_footer_title',
			'type' => 'title',
		);

		$fields['store_footer'] = array(
			'name' => esc_html__( 'Footer', 'automatic-order-printing-for-woocommerce' ),
			'desc' => esc_html__( 'Footer Address.', 'automatic-order-printing-for-woocommerce' ),
			'id'   => 'store_footer',
			'type' => 'textarea_small',
		);


		$fields = apply_filters( 'woo_order_auto_print_general_settings_fields', $fields );

		return $fields;
	}

	/**
	 * Return strings settings fields
	 *
	 * @since  1.0.5
	 * @return array     $fields
	 */
	public function get_strings_settings() {
		$fields = array();

		$fields['from_address']     = array(
			'name'    => esc_html__( 'From Address', 'automatic-order-printing-for-woocommerce' ),
			'default' => esc_html__( 'From Address', 'automatic-order-printing-for-woocommerce' ),
			'id'      => 'from_address',
			'type'    => 'text',
		);
		$fields['billing_address']  = array(
			'name'    => esc_html__( 'Billing Address', 'automatic-order-printing-for-woocommerce' ),
			'default' => esc_html__( 'Billing Address', 'automatic-order-printing-for-woocommerce' ),
			'id'      => 'billing_address',
			'type'    => 'text',
		);
		$fields['shipping_address'] = array(
			'name'    => esc_html__( 'Shipping Address', 'automatic-order-printing-for-woocommerce' ),
			'default' => esc_html__( 'Shipping Address', 'automatic-order-printing-for-woocommerce' ),
			'id'      => 'shipping_address',
			'type'    => 'text',
		);
		$fields['order_number']     = array(
			'name'    => esc_html__( 'Order Number', 'automatic-order-printing-for-woocommerce' ),
			'default' => esc_html__( 'Order Number', 'automatic-order-printing-for-woocommerce' ),
			'id'      => 'order_number',
			'type'    => 'text',
		);
		$fields['order_date']       = array(
			'name'    => esc_html__( 'Order Date', 'automatic-order-printing-for-woocommerce' ),
			'default' => esc_html__( 'Order Date', 'automatic-order-printing-for-woocommerce' ),
			'id'      => 'order_date',
			'type'    => 'text',
		);
		$fields['payment_method']   = array(
			'name'    => esc_html__( 'Payment Method', 'automatic-order-printing-for-woocommerce' ),
			'default' => esc_html__( 'Payment Method', 'automatic-order-printing-for-woocommerce' ),
			'id'      => 'payment_method',
			'type'    => 'text',
		);
		$fields['shipping_method']  = array(
			'name'    => esc_html__( 'Shipping Method', 'automatic-order-printing-for-woocommerce' ),
			'default' => esc_html__( 'Shipping Method', 'automatic-order-printing-for-woocommerce' ),
			'id'      => 'shipping_method',
			'type'    => 'text',
		);
		$fields['email']            = array(
			'name'    => esc_html__( 'Email', 'automatic-order-printing-for-woocommerce' ),
			'default' => esc_html__( 'Email', 'automatic-order-printing-for-woocommerce' ),
			'id'      => 'email',
			'type'    => 'text',
		);
		$fields['tel']              = array(
			'name'    => esc_html__( 'Tel', 'automatic-order-printing-for-woocommerce' ),
			'default' => esc_html__( 'Tel', 'automatic-order-printing-for-woocommerce' ),
			'id'      => 'tel',
			'type'    => 'text',
		);

		$fields['image']          = array(
			'name'    => esc_html__( 'Image', 'automatic-order-printing-for-woocommerce' ),
			'default' => esc_html__( 'Image', 'automatic-order-printing-for-woocommerce' ),
			'id'      => 'image',
			'type'    => 'text',
		);
		$fields['product']        = array(
			'name'    => esc_html__( 'Product', 'automatic-order-printing-for-woocommerce' ),
			'default' => esc_html__( 'Product', 'automatic-order-printing-for-woocommerce' ),
			'id'      => 'product',
			'type'    => 'text',
		);
		$fields['sku']            = array(
			'name'    => esc_html__( 'SKU', 'automatic-order-printing-for-woocommerce' ),
			'default' => esc_html__( 'SKU', 'automatic-order-printing-for-woocommerce' ),
			'id'      => 'sku',
			'type'    => 'text',
		);
		$fields['qty']            = array(
			'name'    => esc_html__( 'Qty', 'automatic-order-printing-for-woocommerce' ),
			'default' => esc_html__( 'Qty', 'automatic-order-printing-for-woocommerce' ),
			'id'      => 'qty',
			'type'    => 'text',
		);
		$fields['price']          = array(
			'name'    => esc_html__( 'Price', 'automatic-order-printing-for-woocommerce' ),
			'default' => esc_html__( 'Price', 'automatic-order-printing-for-woocommerce' ),
			'id'      => 'price',
			'type'    => 'text',
		);
		$fields['total_price']    = array(
			'name'    => esc_html__( 'Total Price', 'automatic-order-printing-for-woocommerce' ),
			'default' => esc_html__( 'Total Price', 'automatic-order-printing-for-woocommerce' ),
			'id'      => 'total_price',
			'type'    => 'text',
		);
		$fields['customer_notes'] = array(
			'name'    => esc_html__( 'Customer Notes', 'automatic-order-printing-for-woocommerce' ),
			'default' => esc_html__( 'Customer Notes', 'automatic-order-printing-for-woocommerce' ),
			'id'      => 'customer_notes',
			'type'    => 'text',
		);

		return $fields;
	}


	/**
	 * Return menu names to remove
	 *
	 * @since  1.0.0
	 * @return array
	 */
	public function get_settings_sub() {
		return array( 'woo_order_auto_print_strings' );
	}

	/**
	 * Callback function for Country states field display.
	 *
	 * @since 1.0.0
	 * @param object $field       The field object.
	 * @param array  $value       Field value.
	 * @param string $object_id   Object id.
	 * @param string $object_type Object type.
	 * @param string $field_type  Field type.
	 */
	public function render_country_state_callback( $field, $value, $object_id, $object_type, $field_type ) {
		$country_setting = (string) $value;
		if ( strstr( $country_setting, ':' ) ) {
			$country_setting = explode( ':', $country_setting );
			$country         = current( $country_setting );
			$state           = end( $country_setting );
		} else {
			$country = $country_setting;
			$state   = '*';
		}
		ob_start();
		WC()->countries->country_dropdown_options( $country, $state );
		$options      = ob_get_clean();
		$allowed_html = wp_kses_allowed_html( 'post' );
		echo wp_kses(
			$field_type->select(  // WPCS: input var ok, CSRF ok, sanitization ok.
				array(
					'name'    => esc_attr( $field_type->_name() ),
					'id'      => esc_attr( $field_type->_id() ),
					'options' => wp_kses(
						$options,
						array(
							'option' => array(
								'value'    => array(),
								'selected' => array(),
							),
						)
					), // WPCS: input var ok, CSRF ok, sanitization ok.
					'desc'    => '<p>' . esc_html( strip_tags( $field_type->_desc() ) ) . '</p>', // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				)
			),
			array(
				'p'      => array(),
				'select' => array(
					'name'   => array(),
					'id'     => array(),
					'class'  => array(),
					'option' => array(),
				),
				'option' => array(
					'selected' => array(),
					'value'    => array(),
				),
			)
		); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Registers meta box for printing options
	 *
	 * @since 1.0.0
	 */
	public function add_meta_boxes() {
		add_meta_box( 'woo-order-auto-print-meta', esc_html__( 'Order Auto Print', 'automatic-order-printing-for-woocommerce' ), array( $this, 'create_metabox_content' ), 'shop_order', 'side', 'default' );
	}

	/**
	 *  Create content for metabox
	 *
	 * @since 1.0.0
	 * @param object $post       The post object.
	 */
	public function create_metabox_content( $post ) {
		$templates = get_woo_order_auto_print_templates();
		$order_id  = $post->ID;
		?>
		<h3><?php echo esc_html__( 'Print / Preview / Download', 'automatic-order-printing-for-woocommerce' ); ?></h3>
		<table>
			<?php
			if ( ! empty( $templates ) ) {
				foreach ( $templates as $template_id => $template ) {
					if ( 'on' == $template['meta']['enable'] ) {
						$url    = wp_nonce_url( admin_url( 'admin-ajax.php?action=woo-order-auto-print&task=taskreplace&order_id=' . ( $order_id ) . '&template_id=' . $template_id ), 'automatic-order-printing-for-woocommerce' );
						$_tasks = array(
							'print'    => array(
								'title' => esc_html__( 'Print', 'automatic-order-printing-for-woocommerce' ),
								'icon'  => 'dashicons-printer',
							),
							'preview'  => array(
								'title' => esc_html__( 'Preview', 'automatic-order-printing-for-woocommerce' ),
								'icon'  => 'dashicons-visibility',
							),
							'download' => array(
								'title' => esc_html__( 'Download', 'automatic-order-printing-for-woocommerce' ),
								'icon'  => 'dashicons-download',
							),
						);
						$title  = get_the_title( $template_id );
						?>
							<tr>
								<td>
						<?php echo esc_html( $title ); ?>
								</td>
								<td align="right">
									<div class="woo-order-auto-print-admin-actions">
						<?php
						foreach ( $_tasks as $k => $v ) {
							$href = str_replace( 'taskreplace', $k, $url );
							?>
											<a class="wocp-admin-action-<?php echo esc_attr( $k ); ?>" href="<?php echo esc_url( $href ); ?>" title="<?php echo esc_attr( $v['title'] . ' - ' . $title ); ?>" target="<?php echo ( 'print' != $k ) ? esc_attr( '_blank' ) : ''; ?>"><span class="dashicons <?php echo esc_attr( $v['icon'] ); ?>"></span></a>
							<?php
						}
						?>
									</div>
								</td>
							</tr>
						<?php
					}
				}
			}
			?>
		</table>
		   <?php
	}

	/**
	 * Doing ajax print task.
	 *
	 * @since 1.0.0
	 */
	public function woo_order_auto_print_task() {

		if ( isset( $_GET['_wpnonce'] ) && wp_verify_nonce( sanitize_text_field( $_GET['_wpnonce'] ), 'automatic-order-printing-for-woocommerce' ) ) { // WPCS: input var ok, sanitization ok.
			$order_id    = ( isset( $_GET['order_id'] ) ) ? sanitize_text_field( wp_unslash( $_GET['order_id'] ) ) : '';
			$template_id = ( isset( $_GET['template_id'] ) ) ? sanitize_text_field( wp_unslash( $_GET['template_id'] ) ) : '';
			$task        = ( isset( $_GET['task'] ) ) ? sanitize_text_field( wp_unslash( $_GET['task'] ) ) : '';
			if ( ! empty( $order_id ) && ! empty( $template_id ) ) {
				$template          = new Woo_Order_Auto_Print_Templates( $order_id, $template_id );
				$template_settings = $template->get_template_settings();
				$template->run_actions();
				$settings = get_woo_order_auto_print_settings();
				$html     = $template->generate_html();
				$template->remove_actions();

				if ( 'preview_html' == $task ) {
					do_action( 'woo_order_auto_print_template_preview_html', $html );
					die();
				}

				if ( 'on' == $settings['general']['use_mpdf'] && class_exists( 'Automatic_Order_Printing_For_Woocommerce_Mpdf_Addon_Mpdf' ) ) {
					$mpdf = new Automatic_Order_Printing_For_Woocommerce_Mpdf_Addon_Mpdf( $order_id, $template_id, $template_settings );
					$ret  = $mpdf->generate_pdf( $task, $html );
				} else {
					$dompdf = new Woo_Order_Auto_Print_Dompdf( $order_id, $template_id, $template_settings );
					$ret    = $dompdf->generate_pdf( $task, $html );
				}
				if ( 'print' == $task ) {
					$file_path = $ret;
					$printnode = new Woo_Order_Auto_Print_Printnode();
					$status    = $printnode->print_pdf( $file_path, $order_id, $template_settings, $template_id );
					wp_send_json_success( $status );
				}
			} else {
				wp_send_json_error();
			}
		} else {
			wp_send_json_error();
		}
		die();
	}
}
