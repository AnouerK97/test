<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.procomsoftsol.com
 * @since      1.0.0
 *
 * @package    Woo_Order_Auto_Print
 * @subpackage Woo_Order_Auto_Print/includes
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
class Woo_Order_Auto_Print_Templates {

	/**
	 * The ID of the order.
	 *
	 * @since    1.0.0
	 * @var      int    $order_id    The ID the order.
	 */
	public $order_id;

	/**
	 * The ID of the template.
	 *
	 * @since    1.0.0
	 * @var      int    $template_id    The ID the template.
	 */
	public $template_id;

	/**
	 * The Settings of the template.
	 *
	 * @since    1.0.0
	 * @var      array    $template_settings    The Settings the template.
	 */
	public $template_settings;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      int $order_id       The order id.
	 * @param      int $template_id    The template id.
	 */
	public function __construct( $order_id, $template_id ) {

		$this->order_id          = $order_id;
		$this->template_id       = $template_id;
		$this->template_settings = $this->get_template_settings();
		$this->order             = wc_get_order( $order_id );

	}

	/**
	 * Run template Actions
	 *
	 * @since    1.0.0
	 */
	public function run_actions() {
		add_action( 'woo_order_auto_print_template_styles', array( $this, 'template_styles' ), 10, 3 );
		add_action( 'woo_order_auto_print_template_content', array( $this, 'template_content' ), 10, 3 );
		add_action( 'woo_order_auto_print_order_header', array( $this, 'order_header' ), 10, 4 );
		add_action( 'woo_order_auto_print_store_logo', array( $this, 'store_logo' ), 10, 4 );
		add_action( 'woo_order_auto_print_store_address', array( $this, 'store_address' ), 10, 4 );
		add_action( 'woo_order_auto_print_order_data', array( $this, 'order_data' ), 10, 4 );
		add_action( 'woo_order_auto_print_document_footer', array( $this, 'document_footer' ), 10, 4 );
		add_action( 'woo_order_auto_print_order_products_table', array( $this, 'products_table' ), 10, 4 );
	}

	/**
	 * Run template Actions
	 *
	 * @since    1.0.0
	 */
	public function remove_actions() {
		remove_action( 'woo_order_auto_print_template_styles', array( $this, 'template_styles' ), 10, 3 );
		remove_action( 'woo_order_auto_print_template_content', array( $this, 'template_content' ), 10, 3 );
		remove_action( 'woo_order_auto_print_order_header', array( $this, 'order_header' ), 10, 4 );
		remove_action( 'woo_order_auto_print_store_logo', array( $this, 'store_logo' ), 10, 4 );
		remove_action( 'woo_order_auto_print_store_address', array( $this, 'store_address' ), 10, 4 );
		remove_action( 'woo_order_auto_print_order_data', array( $this, 'order_data' ), 10, 4 );
		remove_action( 'woo_order_auto_print_document_footer', array( $this, 'document_footer' ), 10, 4 );
		remove_action( 'woo_order_auto_print_order_products_table', array( $this, 'products_table' ), 10, 4 );
	}

	/**
	 * Display products table
	 *
	 * @since    1.0.0
	 * @return     array    $settings       The template settings.
	 */
	public function get_template_settings() {
		$settings = array();
		$_options = array();
		$options  = array(
			'meta'            => 'woo_order_auto_print_meta',
			'store'           => 'woo_order_auto_print_store',
			'billing'         => 'woo_order_auto_print_billing',
			'shipping'        => 'woo_order_auto_print_shipping',
			'order_details'   => 'woo_order_auto_print_order_details',
			'product_details' => 'woo_order_auto_print_products',
			'footer'          => 'woo_order_auto_print_footer',
			'print'           => 'woo_order_auto_print_conditions',
			'custom_css'      => 'woo_order_auto_print_custom_css',
		);

		$all_meta = get_post_meta( $this->template_id );
		if ( $all_meta ) {
			foreach ( $options as $k => $v ) {
				$fields_defaults = $GLOBALS['Woo_Order_Auto_Print_Cpt']->{"get_{$k}_fields"}();
				if ( $fields_defaults ) {
					foreach ( $fields_defaults as $k1 => $v1 ) {
						if ( 'strings' == $k ) {
							$settings[ $k ][ $v1['id'] ] = $v1['default'];
						} else {
							$settings[ $k ][ $v1['id'] ] = '';
						}
						if ( isset( $all_meta[ $v1['id'] ] ) ) {
							$_options[ $k ][ $v1['id'] ] = maybe_unserialize( $all_meta[ $v1['id'] ][0] );
						}
					}
				}
			}
		}

		foreach ( $_options as  $k => $v ) {
			$settings[ $k ] = wp_parse_args( $v, $settings[ $k ] );
		}

				$settings = apply_filters( 'woo_order_auto_print_template_settings', $settings, $this->template_id, $this->order_id );

		return $settings;

	}

	/**
	 * Generate template html
	 *
	 * @since    1.0.0
	 * @return      string
	 */
	public function generate_html() {
		ob_start();
		if ( isset( $this->template_settings['meta']['print_template'] ) && ! empty( $this->template_settings['meta']['print_template'] ) ) {
			$template = $this->template_settings['meta']['print_template'];
		} else {
			$template = 'simple';
		}
		$file_path = "{$template}/html-wrapper.php";
		woo_order_auto_print_get_template(
			$file_path,
			array(
				'order'             => $this->order,
				'order_id'          => $this->order_id,
				'template_id'       => $this->template_id,
				'template_settings' => $this->template_settings,
				'title'             => $this->get_title(),
				'classes'           => $this->get_template_class(),
			)
		);
		return ob_get_clean();
	}


	/**
	 * Return template content
	 *
	 * @since    1.0.0
	 * @param      int   $order_id       The Order id.
	 * @param      int   $template_id       The template id.
	 * @param array $template_settings The template settings.
	 */
	public function template_content( $order_id, $template_id, $template_settings ) {
		$pdf_size  = $template_settings['meta']['pdf_size'];
		$pdf_size  = ( ! empty( $pdf_size ) ) ? strtolower( $pdf_size ) : '';
		$file_name = 'template.php';
		if ( isset( $template_settings['meta']['print_template'] ) && ! empty( $template_settings['meta']['print_template'] ) ) {
					$template = $template_settings['meta']['print_template'];
		} else {
			$template = 'simple';
		}
		$file_path = "{$template}/$file_name";
		if ( ! empty( $pdf_size ) ) {
			$_file_name = "{$template}/template_{$pdf_size}.php";
			$located    = woo_order_auto_print_locate_template( $_file_name );
			if ( file_exists( $located ) ) {
				$file_path = $_file_name;
			}
		}

		$this->order = wc_get_order( $order_id );

		woo_order_auto_print_get_template(
			$file_path,
			array(
				'order'             => $this->order,
				'order_id'          => $this->order_id,
				'template_id'       => $this->template_id,
				'template_settings' => $this->template_settings,
			)
		);

	}

	/**
	 * Display template styles
	 *
	 * @since    1.0.0
	 * @param      int   $order_id       The Order id.
	 * @param      int   $template_id       The template id.
	 * @param array $template_settings The template settings.
	 */
	public function template_styles( $order_id, $template_id, $template_settings ) {

		if ( isset( $template_settings['meta']['print_template'] ) && ! empty( $template_settings['meta']['print_template'] ) ) {
					$template = $template_settings['meta']['print_template'];
		} else {
			$template = 'simple';
		}
		$file_path = "{$template}/style.css";
		woo_order_auto_print_get_template(
			$file_path
		);

		if ( in_array( $template_settings['meta']['pdf_size'], array( 'A5', 'A6', 'A7', 'A8' ) ) ) {
			?>
			@page {
			 margin-left: 1cm;
			 margin-right: 1cm;
			}
			table.wocp-header-table img{
			max-width:150px;
			}
			<?php
		}
		if ( in_array( $template_settings['meta']['pdf_size'], array( 'A7', 'A8' ) ) ) {
			?>
			@page {
			 margin: 0.5cm;
			}
			table.wocp-header-table .store-address{
				text-align:left;	
			}
			table.products-table tr th:last-child,
			table.products-table tr td:last-child,
			table.otder-totals td{
				width:75px;
			}
			<?php
		}

		if ( isset( $template_settings['custom_css']['custom_css'] ) && ! empty( $template_settings['custom_css']['custom_css'] ) ) {
			echo wp_kses_post($template_settings['custom_css']['custom_css']); // @codingStandardsIgnoreLine
		}

	}

	/**
	 * Return template title
	 *
	 * @since    1.0.0
	 * @return      string
	 */
	public function get_title() {
		$title = $this->template_settings['meta']['document_name'] . ' - #' . $this->order_id;
		return apply_filters( 'woo_order_auto_print_document_title', $title, $this->order_id, $this->template_id );
	}

	/**
	 * Return template class
	 *
	 * @since    1.0.0
	 * @return      string
	 */
	public function get_template_class() {
		return apply_filters( 'woo_order_auto_print_document_classes', sanitize_title( $this->template_settings['meta']['document_name'] ), $this->order_id, $this->template_id );
	}

	/**
	 * Display order header
	 *
	 * @since    1.0.0
	 * @param      object $order       The Order id.
	 * @param      int    $order_id       The Order id.
	 * @param      int    $template_id       The template id.
	 * @param array  $template_settings The template settings.
	 */
	public function order_header( $order, $order_id, $template_id, $template_settings ) {

		if ( 'on' == $template_settings['store']['display_store_logo'] || 'on' == $template_settings['store']['display_document_name'] || 'on' == $template_settings['store']['display_from_Address'] ) {
			?>
		<table class="wocp-header-table">
		<tr>	
			<?php
			do_action( 'woo_order_auto_print_store_logo', $order, $order_id, $template_id, $template_settings );
			if ( in_array( $template_settings['meta']['pdf_size'], array( 'A7', 'A8' ) ) ) {
				echo '</tr><tr>';
			}
			do_action( 'woo_order_auto_print_store_address', $order, $order_id, $template_id, $template_settings );
			?>
		</tr>
	</table>
			<?php
		}
	}

	/**
	 * Display store logo
	 *
	 * @since    1.0.0
	 * @param      object $order       The Order id.
	 * @param      int    $order_id       The Order id.
	 * @param      int    $template_id       The template id.
	 * @param array  $template_settings The template settings.
	 */
	public function store_logo( $order, $order_id, $template_id, $template_settings ) {
		$settings = get_woo_order_auto_print_settings();
		if ( 'on' == $template_settings['store']['display_store_logo'] || 'on' == $template_settings['store']['display_document_name'] ) {
			echo '<td class="store-logo">';
			if ( 'on' == $template_settings['store']['display_store_logo'] ) {
				$template_logo = $template_settings['store']['store_logo'];
				if ( empty( $template_logo ) ) {
					$template_logo = $settings['general']['store_logo'];
				}
				if ( ! empty( $template_logo ) ) {
					$image = wp_sprintf( '<img src="%s" class="header-store-logo" />', $template_logo );
					echo wp_kses_post( apply_filters( 'woo_order_auto_print_order_store_logo_image', $image, $order, $template_id, $template_settings ) );
				}
			}
			if ( 'on' == $template_settings['store']['display_document_name'] ) {
				$document_name = $template_settings['meta']['document_name'];
				echo wp_kses_post( apply_filters( 'woo_order_auto_print_order_document_name', wp_sprintf( '<h2>%s</h2>', $document_name ), $order, $template_id, $template_settings ) );
			}
			echo '</td>';
		}
	}

	/**
	 * Display store address
	 *
	 * @since    1.0.0
	 * @param      object $order       The Order id.
	 * @param      int    $order_id       The Order id.
	 * @param      int    $template_id       The template id.
	 * @param array  $template_settings The template settings.
	 */
	public function store_address( $order, $order_id, $template_id, $template_settings ) {
		$settings = get_woo_order_auto_print_settings();
		if ( 'on' == $template_settings['store']['display_from_Address'] ) {
			echo '<td class="store-address">';
			$country_setting = ( isset( $settings['general']['country_state'] ) ) ? $settings['general']['country_state'] : '';
			$country         = '';
			$state           = '';
			if ( strstr( $country_setting, ':' ) ) {
				$country_setting = explode( ':', $country_setting );
				$country         = current( $country_setting );
				$state           = end( $country_setting );
			} else {
				$country = $country_setting;
			}

			$_address['company']   = $settings['general']['store_name'];
			$_address['address_1'] = $settings['general']['store_address'];
			$_address['address_2'] = $settings['general']['store_address_2'];
			$_address['city']      = $settings['general']['store_city'];
			$_address['state']     = $state;
			$_address['country']   = $country;
			$_address['postcode']  = $settings['general']['store_postcode'];

			$address = WC()->countries->get_formatted_address( $_address );
			$address = apply_filters( 'woo_order_auto_print_store_address_format', $address, $_address );
			?>
			<h3><?php echo esc_html( $settings['strings']['from_address'] ); ?></h3>
			<?php
			echo wp_kses_post( $address );
			echo '</td>';
		}

	}

	/**
	 * Display billing data
	 *
	 * @since    1.0.0
	 * @param      object $order       The Order id.
	 * @param      int    $order_id       The Order id.
	 * @param      int    $template_id       The template id.
	 * @param array  $template_settings The template settings.
	 */
	public function get_billing_data_to_display( $order, $order_id, $template_id, $template_settings ) {
		if ( 'on' == $template_settings['billing']['display_billing_address'] || 'on' == $template_settings['billing']['display_billing_email'] || 'on' == $template_settings['billing']['display_billing_phone'] ) {
			$settings = get_woo_order_auto_print_settings();
			?>
			<td class="order-billing-address">
			<h3><?php echo esc_html( $settings['strings']['billing_address'] ); ?></h3>
			<?php
				do_action( 'woo_order_auto_print_before_billing_address', $order, $order_id, $template_id, $template_settings );
			if ( 'on' == $template_settings['billing']['display_billing_address'] ) {
				echo wp_kses_post( $order->get_formatted_billing_address() );
			}
			if ( 'on' == $template_settings['billing']['display_billing_email'] ) {
				echo wp_sprintf( '<p>%s: %s</p>', esc_html( $settings['strings']['email'] ), esc_html( $order->get_billing_email() ) );
			}
			if ( 'on' == $template_settings['billing']['display_billing_phone'] ) {
				echo wp_sprintf( '<p>%s: %s</p>', esc_html( $settings['strings']['tel'] ), esc_html( $order->get_billing_phone() ) );
			}
				do_action( 'woo_order_auto_print_after_billing_address', $order, $order_id, $template_id, $template_settings );
			?>
				
			</td>
			<?php
		}
	}

	/**
	 * Display shipping data
	 *
	 * @since    1.0.0
	 * @param      object $order       The Order id.
	 * @param      int    $order_id       The Order id.
	 * @param      int    $template_id       The template id.
	 * @param array  $template_settings The template settings.
	 */
	public function get_shipping_data_to_display( $order, $order_id, $template_id, $template_settings ) {
		if ( 'on' == $template_settings['shipping']['display_shipping_address'] ) {
			$settings = get_woo_order_auto_print_settings();
			?>
			<td class="order-shipping-address">
			<h3><?php echo esc_html( $settings['strings']['shipping_address'] ); ?></h3>
			<?php
			do_action( 'woo_order_auto_print_before_shipping_address', $order, $order_id, $template_id, $template_settings );
					echo wp_kses_post( $order->get_formatted_shipping_address() );
					do_action( 'woo_order_auto_print_after_shipping_address', $order, $order_id, $template_id, $template_settings );
			?>
				
			</td>
			<?php
		}
	}

	/**
	 * Order data to display
	 *
	 * @since    1.0.0
	 * @param      object $order       The Order id.
	 * @param      int    $order_id       The Order id.
	 * @param      int    $template_id       The template id.
	 * @param array  $template_settings The template settings.
	 */
	public function get_order_data_to_display( $order, $order_id, $template_id, $template_settings ) {
		if ( 'on' == $template_settings['order_details']['display_order_number'] ||
			'on' == $template_settings['order_details']['display_order_date'] ||
			'on' == $template_settings['order_details']['display_payment_method'] ||
			'on' == $template_settings['order_details']['display_shipping_method'] ||
			'on' == apply_filters( 'woo_order_auto_print_after_order_data_display', 'off', $order, $template_id, $template_settings ) ) {
				$settings = get_woo_order_auto_print_settings();
			?>
			<td class="order-details">
				<ul>
					<?php
					do_action( 'woo_order_auto_print_before_order_data', $order, $order_id, $template_id, $template_settings );
					if ( 'on' == $template_settings['order_details']['display_order_number'] ) {
						?>
						<li class="order-number"><strong><?php echo esc_html( $settings['strings']['order_number'] ); ?></strong><?php echo esc_html( $order->get_order_number() ); ?></li>
						<?php
					}
					if ( 'on' == $template_settings['order_details']['display_order_date'] ) {
						$order_date  = $order->get_date_created();
						$date_format = apply_filters( 'woo_order_auto_print_date_format', get_option( 'date_format' ) );
						$date        = $order_date->date_i18n( $date_format );
						?>
						<li class="order-date"><strong><?php echo esc_html( $settings['strings']['order_date'] ); ?></strong><?php echo esc_html( $date ); ?></li>
						<?php
					}
					if ( 'on' == $template_settings['order_details']['display_payment_method'] ) {
						?>
						 <li class="payment-method"><strong><?php echo esc_html( $settings['strings']['payment_method'] ); ?></strong><?php echo esc_html( $order->get_payment_method_title() ); ?></li>   
							<?php
					}
					if ( 'on' == $template_settings['order_details']['display_shipping_method'] ) {
						?>
						 <li class="shipping-method"><strong><?php echo esc_html( $settings['strings']['shipping_method'] ); ?></strong><?php echo esc_html( $order->get_shipping_method() ); ?></li>   
							<?php
					}
					do_action( 'woo_order_auto_print_after_order_data', $order, $order_id, $template_id, $template_settings );
					?>
				</ul>
			</td>
			<?php
		}
	}

	/**
	 * Display order data
	 *
	 * @since    1.0.0
	 * @param      object $order       The Order id.
	 * @param      int    $order_id       The Order id.
	 * @param      int    $template_id       The template id.
	 * @param array  $template_settings The template settings.
	 */
	public function order_data( $order, $order_id, $template_id, $template_settings ) {
		?>
		<table class="wocp-top-data-table">
		<tr>
			<?php
				$this->get_billing_data_to_display( $order, $order_id, $template_id, $template_settings );

			if ( in_array( $template_settings['meta']['pdf_size'], array( 'A6', 'A7', 'A8' ) ) ) {
				echo '</tr><tr>';
			}

			if ( 'on' == $template_settings['shipping']['display_shipping_address'] ) {
				$this->get_shipping_data_to_display( $order, $order_id, $template_id, $template_settings );
				if ( in_array( $template_settings['meta']['pdf_size'], array( 'A6', 'A7', 'A8' ) ) ) {
					echo '</tr><tr>';
				}
			}
				$this->get_order_data_to_display( $order, $order_id, $template_id, $template_settings );
			?>
		</tr>
	</table>
		<?php
	}

	/**
	 * Display document footer
	 *
	 * @since    1.0.0
	 * @param      object $order       The Order id.
	 * @param      int    $order_id       The Order id.
	 * @param      int    $template_id       The template id.
	 * @param array  $template_settings The template settings.
	 */
	public function document_footer( $order, $order_id, $template_id, $template_settings ) {
		$settings = get_woo_order_auto_print_settings();
		if ( 'on' == $template_settings['footer']['display_footer'] ) {
			echo wp_kses_post( apply_filters( 'woo_order_auto_print_document_footer_format', wpautop( $settings['general']['store_footer'] ), $order, $template_settings ) );
		}
	}

	/**
	 * Display products table
	 *
	 * @since    1.0.0
	 * @param      object $order       The Order id.
	 * @param      int    $order_id       The Order id.
	 * @param      int    $template_id       The template id.
	 * @param array  $template_settings The template settings.
	 */
	public function products_table( $order, $order_id, $template_id, $template_settings ) {
		$table_settings = $template_settings['product_details'];
		if ( 'on' != $table_settings['display_products_table'] ) {
			return '';
		}

		$settings = get_woo_order_auto_print_settings();
		?>
		<table class="products-table">
			<thead class="products-table-head">
				<tr>
				<?php
				if ( 'on' == $table_settings['display_products_image'] ) {
					?>
						<th class="product-image"><?php echo esc_html( $settings['strings']['image'] ); ?></th>
				<?php } if ( 'on' == $table_settings['display_products_name'] ) { ?>
					<th class="product-title"><?php echo esc_html( $settings['strings']['product'] ); ?></th>
				 <?php } if ( 'on' == $table_settings['display_products_sku'] ) { ?>
					<th class="product-sku"><?php echo esc_html( $settings['strings']['sku'] ); ?></th>
				 <?php } if ( 'on' == $table_settings['display_products_qty'] ) { ?>
					<th class="product-qty"><?php echo esc_html( $settings['strings']['qty'] ); ?></th>
				 <?php } if ( 'on' == $table_settings['display_products_price'] ) { ?>
					<th class="product-price"><?php echo esc_html( $settings['strings']['price'] ); ?></th>
				 <?php } if ( 'on' == $table_settings['display_products_total'] ) { ?>
					<th class="product-total-price"><?php echo esc_html( $settings['strings']['total_price'] ); ?></th>
				 <?php } ?>   
				</tr>
			</thead>
			<tbody>
			<?php
			foreach ( $order->get_items() as $item_id => $item ) {
				$product       = $item->get_product();
				$sku           = '';
				$purchase_note = '';
				$image         = '';

				if ( ! apply_filters( 'woocommerce_order_item_visible', true, $item ) ) {
					continue;
				}

				if ( is_object( $product ) ) {
					$sku           = $product->get_sku();
					$purchase_note = $product->get_purchase_note();
					$image         = $product->get_image();
				}
				?>
						<tr class="<?php echo esc_attr( apply_filters( 'woo_order_auto_print_order_item_class', 'order_item', $item, $order ) ); ?>">
						<?php if ( 'on' == $table_settings['display_products_image'] ) { ?>
						<td class="product-image">
							<?php
							echo wp_kses_post( apply_filters( 'woo_order_auto_print_order_item_thumbnail', $image, $item ) );
							?>
						</td>
						<?php } if ( 'on' == $table_settings['display_products_name'] ) { ?>
						<td class="product-title">
							<?php
								// Product name.
								echo wp_kses_post( apply_filters( 'woo_order_auto_print_order_item_name', $item->get_name(), $item ) );
								do_action( 'woo_order_auto_print_order_item_meta_start', $item_id, $item, $order );

								wc_display_item_meta(
									$item,
									array(
										'label_before' => '<strong class="wc-item-meta-label">',
									)
								);

								// allow other plugins to add additional product information here.
								do_action( 'woo_order_auto_print_order_item_meta_end', $item_id, $item, $order );
							?>
						</td>
						<?php } if ( 'on' == $table_settings['display_products_sku'] ) { ?>
						<td class="product-sku">
							<?php
							if ( $sku ) {
								echo wp_kses_post( $sku );
							}
							?>
						</td>
						<?php } if ( 'on' == $table_settings['display_products_qty'] ) { ?>
						<td class="product-qty">
							<?php
							$qty          = $item->get_quantity();
							$refunded_qty = $order->get_qty_refunded_for_item( $item_id );

							if ( $refunded_qty ) {
								$qty_display = '<del>' . esc_html( $qty ) . '</del> <ins>' . esc_html( $qty - ( $refunded_qty * -1 ) ) . '</ins>';
							} else {
								$qty_display = esc_html( $qty );
							}
							echo wp_kses_post( apply_filters( 'woo_order_auto_print_order_item_quantity', $qty_display, $item ) );
							?>
						</td>
						<?php } if ( 'on' == $table_settings['display_products_price'] ) { ?>
						<td class="product-price">
							<?php
								echo wp_kses_post( wc_price( $order->get_item_subtotal( $item, false, true ), array( 'currency' => $order->get_currency() ) ) ); // WPCS: input var ok, sanitization ok.
							?>
						</td>
						<?php } if ( 'on' == $table_settings['display_products_total'] ) { ?>
						<td class="product-total-price">
							<?php
							echo wp_kses_post( wc_price( $item->get_total(), array( 'currency' => $order->get_currency() ) ) ); // WPCS: input var ok, sanitization ok.
							?>
						</td>
						<?php } ?>
						</tr>
						<?php
			}
			?>
			</tbody>
		</table>
		<?php
		if ( 'on' == $table_settings['display_order_total'] ) {
			$totals = apply_filters( 'woo_order_auto_print_order_totals', $order->get_order_item_totals(), $order, $order_id, $template_id, $template_settings );
			?>
			<table class="otder-totals">
			<?php
			if ( $totals ) {
				foreach ( $totals as $key => $total ) {
					$label = $total['label'];
					$colon = strrpos( $label, ':' );
					if ( false !== $colon ) {
						$label = substr_replace( $label, '', $colon, 1 );
					}
					?>
							<tr class="<?php echo esc_attr( $key ); ?>">
								<th class="description"><?php echo esc_html($label); ?></th> <?php // @codingStandardsIgnoreLine ?>
								<td><?php echo wp_kses_post( $total['value'] ); ?></td> <?php // WPCS: input var ok, sanitization ok. ?>
							</tr>
							<?php
				}
			}
			?>
			</table>
			<?php
		}
	}



}
