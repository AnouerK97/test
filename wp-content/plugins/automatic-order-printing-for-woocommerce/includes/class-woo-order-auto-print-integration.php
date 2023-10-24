<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.procomsoftsol.com
 * @since    1.0.3
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
class Woo_Order_Auto_Print_Integration {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.3
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.3
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.3
	 * @param      string $plugin_name       The name of this plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

		add_filter( 'woo_order_auto_print_template_get_order_details_fields', array( $this, 'get_order_details_fields' ), 10, 1 );

		add_filter( 'woo_order_auto_print_after_order_data_display', array( $this, 'check_after_order_data_display' ), 10, 4 );

		add_action( 'woo_order_auto_print_after_order_data', array( $this, 'after_order_data' ), 10, 4 );

	}

	/**
	 * Return order details fields
	 *
	 * @since    1.0.3
	 * @param      array $fields Fields.
	 * @return     array     $fields Fields.
	 */
	public function get_order_details_fields( $fields ) {
		include_once ABSPATH . 'wp-admin/includes/plugin.php';

		// for https://wordpress.org/plugins/pi-woocommerce-order-date-time-and-type/.
		if ( is_plugin_active( 'pi-woocommerce-order-date-time-and-type-pro/pi-woocommerce-order-date-time-and-type-pro.php' ) || is_plugin_active( 'pi-woocommerce-order-date-time-and-type/pi-woocommerce-order-date-time-and-type.php' ) ) {
			$fields['display_pi_order_datetime'] = array(
				'name'    => esc_html__( 'Display PI WooCommerce order date time and type Data', 'automatic-order-printing-for-woocommerce' ),
				'id'      => 'display_pi_order_datetime',
				'type'    => 'checkbox',
				'default' => $this->set_default_check_value( true ),
			);
		}

		// for https://wordpress.org/plugins/order-delivery-date-for-woocommerce/.
		if ( is_plugin_active( 'order-delivery-date-for-woocommerce/order_delivery_date.php' ) || is_plugin_active( 'order-delivery-date/order_delivery_date.php' ) ) {
			$fields['display_order_delivery_date'] = array(
				'name'    => esc_html__( 'Display Order Delivery Date by Tyche Softwares', 'automatic-order-printing-for-woocommerce' ),
				'id'      => 'display_order_delivery_date',
				'type'    => 'checkbox',
				'default' => $this->set_default_check_value( true ),
			);
		}

		// for https://woocommerce.com/products/woocommerce-order-delivery/.
		if ( is_plugin_active( 'woocommerce-order-delivery/woocommerce-order-delivery.php' ) ) {
			$fields['display_woocommerce_order_delivery'] = array(
				'name'    => esc_html__( 'Display WooCommerce Order Delivery Data', 'automatic-order-printing-for-woocommerce' ),
				'id'      => 'display_woocommerce_order_delivery',
				'type'    => 'checkbox',
				'default' => $this->set_default_check_value( true ),
			);
		}

		// for https://yithemes.com/themes/plugins/yith-woocommerce-delivery-date/.
		if ( is_plugin_active( 'yith-woocommerce-delivery-date-premium/init.php' ) ) {
			$fields['display_yith_woocommerce_delivery_date'] = array(
				'name'    => esc_html__( 'Display YITH WooCommerce Delivery Date Premium', 'automatic-order-printing-for-woocommerce' ),
				'id'      => 'display_yith_woocommerce_delivery_date',
				'type'    => 'checkbox',
				'default' => $this->set_default_check_value( true ),
			);
		}

		// for https://iconicwp.com/products/woocommerce-delivery-slots/ this plugin details will display automatically.

		$fields['display_custom_order_data'] = array(
			'id'   => 'display_custom_order_data',
			'type' => 'title',
			'desc' => wp_kses_post( "<strong>If in case, any custom order data (third-party plugin's) is not available to print in the receipts, feel free to contact our support team here <a href='https://woocommerce.com/my-account/create-a-ticket/' target='_blank'>https://woocommerce.com/my-account/create-a-ticket/</a>. We are happy to help you.</strong>", 'automatic-order-printing-for-woocommerce' ),
		);

		return $fields;
	}


	/**
	 * Only return default value if we don't have a post ID (in the 'post' query variable)
	 *
	 * @param  bool $default On/Off (true/false).
	 * @return mixed          Returns true or '', the blank default
	 */
	public function set_default_check_value( $default ) {
		return isset( $_GET['post'] ) ? '' : ( $default ? (string) $default : '' );
	}


	/**
	 * Return on  if thirdparty data display
	 *
	 * @since    1.0.3
	 * @param      string $return Return value.
	 * @param      object $order       The Order id.
	 * @param      int    $template_id       The template id.
	 * @param array  $template_settings The template settings.
	 * @return     string     $return
	 */
	public function check_after_order_data_display( $return, $order, $template_id, $template_settings ) {
		if ( 'on' == $template_settings['order_details']['display_pi_order_datetime'] ) {
			$return = 'on';
		}

		if ( 'on' == $template_settings['order_details']['display_order_delivery_date'] ) {
			$return = 'on';
		}

		if ( 'on' == $template_settings['order_details']['display_woocommerce_order_delivery'] ) {
			$return = 'on';
		}

		if ( 'on' == $template_settings['order_details']['display_yith_woocommerce_delivery_date'] ) {
			$return = 'on';
		}

		return $return;
	}

	/**
	 * Order data to display
	 *
	 * @since    1.0.3
	 * @param      object $order       The Order id.
	 * @param      int    $order_id       The Order id.
	 * @param      int    $template_id       The template id.
	 * @param array  $template_settings The template settings.
	 */
	public function after_order_data( $order, $order_id, $template_id, $template_settings ) {
		include_once ABSPATH . 'wp-admin/includes/plugin.php';

		if ( is_plugin_active( 'pi-woocommerce-order-date-time-and-type-pro/pi-woocommerce-order-date-time-and-type-pro.php' ) || is_plugin_active( 'pi-woocommerce-order-date-time-and-type/pi-woocommerce-order-date-time-and-type.php' ) ) {
			if ( isset( $template_settings['order_details']['display_pi_order_datetime'] ) && 'on' == $template_settings['order_details']['display_pi_order_datetime'] ) {
				echo wp_kses_post( '<li>' );
				$this->display_pi_order_datetime( $order );
				echo wp_kses_post( '</li>' );
			}
		}

		if ( is_plugin_active( 'order-delivery-date-for-woocommerce/order_delivery_date.php' ) ) {
			if ( isset( $template_settings['order_details']['display_order_delivery_date'] ) && 'on' == $template_settings['order_details']['display_order_delivery_date'] ) {
				echo wp_kses_post( '<li>' );
				$this->orddd_lite_plugins_data( $order );
				echo wp_kses_post( '</li>' );
			}
		}

		if ( is_plugin_active( 'order-delivery-date/order_delivery_date.php' ) ) {
			if ( isset( $template_settings['order_details']['display_order_delivery_date'] ) && 'on' == $template_settings['order_details']['display_order_delivery_date'] ) {
				$this->orddd_plugins_data( $order );
			}
		}

		if ( is_plugin_active( 'woocommerce-order-delivery/woocommerce-order-delivery.php' ) ) {
			if ( isset( $template_settings['order_details']['display_woocommerce_order_delivery'] ) && 'on' == $template_settings['order_details']['display_woocommerce_order_delivery'] ) {
				$this->display_woocommerce_order_delivery( $order );
			}
		}

		if ( is_plugin_active( 'yith-woocommerce-delivery-date-premium/init.php' ) ) {
			if ( isset( $template_settings['order_details']['display_yith_woocommerce_delivery_date'] ) && 'on' == $template_settings['order_details']['display_yith_woocommerce_delivery_date'] ) {
				echo wp_kses_post( '<li>' );
				$this->display_yith_woocommerce_delivery_date( $order );
				echo wp_kses_post( '</li>' );
			}
		}

	}


	/**
	 * Order data to display
	 *
	 * @since    1.0.3
	 * @param      object $order       The Order id.
	 */
	public function display_pi_order_datetime( $order ) {
		$order_id = version_compare( WC_VERSION, '3.0.0', '<' ) ? $order->id : $order->get_id();

		$type     = get_post_meta( $order_id, 'pi_delivery_type', true );
		$old_date = get_post_meta( $order_id, 'pi_delivery_date', true );
		$date     = get_post_meta( $order_id, 'pi_system_delivery_date', true );
		$time     = get_post_meta( $order_id, 'pi_delivery_time', true );
		$location = get_post_meta( $order_id, 'pickup_location', true );

		$date_label = esc_html__( 'Date', 'pisol-dtt' );
		$time_label = esc_html__( 'Time', 'pisol-dtt' );

		if ( 'delivery' == $type ) {
			$delivery_type = pisol_dtt_get_setting( 'pi_delivery_label', esc_html__( 'Delivery', 'pisol-dtt' ) );

		} elseif ( 'pickup' == $type ) {
			$delivery_type = pisol_dtt_get_setting( 'pi_pickup_label', esc_html__( 'Pickup', 'pisol-dtt' ) );
		} elseif ( 'non-deliverable' == $type ) {
			$delivery_type = '';
		}

		$delivery_type = apply_filters( 'pisol_dtt_delivery_type_label_value', $delivery_type, $type, $order );

		$delivery_type = apply_filters( 'pisol_dtt_delivery_type_filter_by_order', $delivery_type, $type, $order );

		do_action( 'pisol_dtt_before_delivery_details', $order );

		if ( ! empty( $type ) && 'non-deliverable' !== $type ) {
			echo wp_kses_post( '<p class="pi-order-meta-type"> <strong>' . esc_html__( 'Delivery type', 'pisol-dtt' ) . ':</strong> ' . $delivery_type . '</p>' );
		}

		if ( pi_dtt_display_fields::showDateAndTime( $type ) ) :

			if ( ! empty( $date ) ) {
				echo wp_kses_post( '<p class="pi-order-meta-date"> <strong>' . esc_html( $date_label ) . ':</strong> ' . esc_html( pi_dtt_date::translatedDate( $date ) ) . '</p>' );
			} elseif ( ! empty( $old_date ) ) {
				echo wp_kses_post( '<p class="pi-order-meta-date"> <strong>' . esc_html( $date_label ) . ':</strong> ' . esc_html( $old_date ) . '</p>' );
			}

			if ( ! empty( $time ) ) {
				echo wp_kses_post( '<p class="pi-order-meta-time"> <strong>' . esc_html( $time_label ) . ':</strong> ' . esc_html( pisol_dtt_time::formatTimeForDisplay( $time ) ) . '</p>' );
			}

		endif;

		$location = get_post_meta( $order_id, 'pickup_location', true );
		if ( ( 'pickup' == $type || $type == $this->extra_type_support_pickup_location ) && '' != $location ) {
			echo wp_kses_post( '<p class="pi-order-pickup-location"><strong>' . apply_filters( 'pisol_dtt_pickup_location_label', esc_html__( 'Pickup location', 'pisol-dtt' ), $type ) . ':</strong><br> ' . ( $location ) . '</p>' );
		}

		do_action( 'pisol_dtt_after_delivery_details', $order );
	}

	/**
	 * Order data to display
	 *
	 * @since    1.0.3
	 * @param      object $order       The Order id.
	 */
	public function orddd_lite_plugins_data( $order ) {
		global $orddd_lite_date_formats;
		if ( version_compare( get_option( 'wpo_wcpdf_version' ), '2.0.0', '>=' ) ) {
			$order_id = ( version_compare( get_option( 'woocommerce_version' ), '3.0.0', '>=' ) ) ? $order->get_id() : $order->id;
		} else {
			global $wpo_wcpdf;
			$order_export = $wpo_wcpdf->export;
			$order_obj    = $order_export->order;
			$order_id     = $order_obj->id;
		}

		$delivery_date_formatted = orddd_lite_common::orddd_lite_get_order_delivery_date( $order_id );
		if ( '' !== $delivery_date_formatted ) {
			// phpcs:ignore
			echo wp_kses_post( '<p><strong>' . get_option( 'orddd_lite_delivery_date_field_label' ) . ': </strong>' . $delivery_date_formatted );
		}
	}

	/**
	 * Order data to display
	 *
	 * @since    1.0.3
	 * @param      object $order       The Order id.
	 */
	public function orddd_plugins_data( $order ) {
		if ( version_compare( get_option( 'woocommerce_version' ), '3.0.0', '>=' ) ) {
			$order_id = $order->get_id();
		} else {
			$order_id = $order->id;
		}

		$field_date_label = get_option( 'orddd_delivery_date_field_label' );

		$locations_label = get_option( 'orddd_location_field_label' );
		$address         = get_post_meta( $order_id, $locations_label, true );
		if ( '' != $address ) {
			echo wp_kses_post( '<li>' );
			echo wp_kses_post( '<strong>' . $locations_label . ': </strong>' . $address );
			echo wp_kses_post( '</li>' );
		}

		$delivery_date_formatted = orddd_common::orddd_get_order_delivery_date( $order_id );
		echo wp_kses_post( '<li>' );
		echo wp_kses_post( '<strong>' . $field_date_label . ': </strong>' . $delivery_date_formatted );
		echo wp_kses_post( '</li>' );

		$order_page_time_slot = orddd_common::orddd_get_order_timeslot( $order_id );
		if ( '' != $order_page_time_slot && '' != $order_page_time_slot ) {
			echo wp_kses_post( '<li>' );
			echo wp_kses_post( '<strong>' . get_option( 'orddd_delivery_timeslot_field_label' ) . ': </strong>' . $order_page_time_slot );
			echo wp_kses_post( '</li>' );
		}
	}

	/**
	 * Order data to display
	 *
	 * @since    1.0.3
	 * @param      object $order       The Order id.
	 */
	public function display_woocommerce_order_delivery( $order ) {
		$order_id   = $order->get_id();
		$date       = wc_od_get_order_meta( $order_id, '_delivery_date' );
		$time_frame = wc_od_get_order_meta( $order_id, '_delivery_time_frame' );
		if ( $date ) {
			echo wp_kses_post( '<li><strong>' . esc_html__( 'Delivery date:', 'woocommerce-order-delivery' ) . '</strong>' . esc_html( wc_od_localize_date( $date, get_option( 'date_format' ) ) ) . '</li>' );
		}
		if ( $time_frame ) {
			echo wp_kses_post( '<li><strong>' . esc_html__( 'Time frame:', 'woocommerce-order-delivery' ) . '</strong>' . wp_kses_post( wc_od_time_frame_to_string( $time_frame ) ) . '</li>' );
		}
	}

	/**
	 * Order data to display
	 *
	 * @since    1.0.3
	 * @param      object $order       The Order.
	 */
	public function display_yith_woocommerce_delivery_date( $order ) {
		$order_id        = $order->get_id();
		$carrier_label   = $order->get_meta( 'ywcdd_order_carrier' );
		$shipping_date   = $order->get_meta( 'ywcdd_order_shipping_date' );
		$delivery_date   = $order->get_meta( 'ywcdd_order_delivery_date' );
		$time_from       = $order->get_meta( 'ywcdd_order_slot_from' );
		$time_to         = $order->get_meta( 'ywcdd_order_slot_to' );
		$date_format     = get_option( 'date_format' );
		$carrier_id      = $order->get_meta( 'ywcdd_order_carrier_id' );
		$method_id       = $order->get_meta( 'ywcdd_order_processing_method' );
		$order_has_child = apply_filters( 'yith_delivery_date_order_has_child', false, $order_id );
		$disable_option  = $order_has_child ? 'disabled' : '';

		$fields = array(
			'carrier'       => array(
				'label' => esc_html__( 'Carrier', 'yith-woocommerce-delivery-date' ),
				'value' => $carrier_label,
			),
			'shipping_date' => array(
				'label' => esc_html__( 'Shipping Date', 'yith-woocommerce-delivery-date' ),
				'value' => $shipping_date,
			),
			'delivery_date' => array(
				'label' => esc_html__( 'Delivery Date', 'yith-woocommerce-delivery-date' ),
				'value' => $delivery_date,
			),
			'timeslot'      => array(
				'label' => esc_html__( 'Time Slot', 'yith-woocommerce-delivery-date' ),
				'value' => ( empty( $time_from ) || empty( $time_to ) ) ? '' : sprintf( '%s - %s', ywcdd_display_timeslot( $time_from ), ywcdd_display_timeslot( $time_to ) ),
			),
		);

		foreach ( $fields as $key => $field ) {
			$formatted_value = empty( $field['value'] ) ? false : $field['value'];
			if ( ( 'shipping_date' === $key || 'delivery_date' === $key ) && $formatted_value ) {
				$formatted_value = wc_format_datetime( new WC_DateTime( $formatted_value, new DateTimeZone( 'UTC' ) ) );
			}
			if ( $formatted_value ) {
				echo wp_kses_post( sprintf( '<p class="%s"><strong>%s : </strong>%s</p>', $key, $field['label'], $formatted_value ) );//phpcs:ignore WordPress.Security.EscapeOutput
			} else {
				echo wp_kses_post( sprintf( '<p class="%s"><strong>%s : </strong>%s</p>', $key, $field['label'], $no_available_label ) );//phpcs:ignore WordPress.Security.EscapeOutput
			}
		}
	}


}
