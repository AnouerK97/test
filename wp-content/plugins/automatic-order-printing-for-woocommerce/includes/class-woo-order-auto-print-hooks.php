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
class Woo_Order_Auto_Print_Hooks {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * The settings of this plugin.
	 *
	 * @since    1.0.4
	 * @var      Array    $settings    The Settings of this plugin.
	 */
	private $settings;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of this plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

		add_action( 'init', array( $this, 'init' ), 11 );

	}

	/**
	 * Initialize the hooks.
	 *
	 * @since    1.0.0
	 */
	public function init() {
		$this->settings = get_woo_order_auto_print_settings();
		if ( 'on' != $this->settings['general']['enable'] ) {
			return;
		}
		add_action( 'woocommerce_checkout_order_processed', array( &$this, 'order_processed' ), PHP_INT_MAX, 3 );
		add_action( 'woocommerce_payment_complete', array( $this, 'payment_complete' ), PHP_INT_MAX );

		$statuses  = wc_get_order_statuses();
		$_statuses = array();
		foreach ( array_keys( $statuses ) as $status ) {
			$status_slug = ( 'wc-' === substr( $status, 0, 3 ) ) ? substr( $status, 3 ) : $status;
			$_statuses[] = $status_slug;
		}

		if ( ! empty( $_statuses ) ) {
			foreach ( $_statuses as $status_from ) {
				foreach ( $_statuses as $status_to ) {
					add_action( "woocommerce_order_status_{$status_from}_to_{$status_to}", array( $this, 'print_order_on_status' ) );
				}
			}
		}

		add_action( 'woo_order_auto_print_run_print_callback', array( $this, 'print_order' ), 10, 3 );

	}

	/**
	 * Initialize print on status change.
	 *
	 * @since    1.0.0
	 * @param      int $order_id       The Order id.
	 */
	public function print_order_on_status( $order_id ) {
		$current_action = current_action();
		if ( 'on' != $this->settings['general']['skip_cron'] ) {
			WC()->queue()->schedule_single(
				time() + 1,
				'woo_order_auto_print_run_print_callback',
				array(
					'order_id'       => $order_id,
					'print_on'       => 'status_change',
					'current_action' => $current_action,
				),
				'woo-order-auto-print-ac'
			);
		} else {
			$this->logh( "Skipped WP cron and called directly {$order_id} order and status_change called" );
			$this->print_order( $order_id, 'status_change', $current_action );
		}

	}

	/**
	 * Initialize print on order procesed hook.
	 *
	 * @since    1.0.0
	 * @param      int    $order_id       The Order id.
	 * @param      mixed  $posted_data       The Order posted data.
	 * @param      object $order       The Order.
	 */
	public function order_processed( $order_id, $posted_data, $order ) {
		$current_action = current_action();
		if ( 'on' != $this->settings['general']['skip_cron'] ) {
			WC()->queue()->schedule_single(
				time() + 1,
				'woo_order_auto_print_run_print_callback',
				array(
					'order_id'       => $order_id,
					'print_on'       => 'immediately',
					'current_action' => $current_action,
				),
				'woo-order-auto-print-ac'
			);
		} else {
			$this->logh( "Skipped WP cron and called directly {$order_id} order and immediately called" );
			$this->print_order( $order_id, 'immediately', $current_action );
		}
	}

	/**
	 * Initialize print on payment complete hook.
	 *
	 * @since    1.0.0
	 * @param      int $order_id       The Order id.
	 */
	public function payment_complete( $order_id ) {
		$current_action = current_action();
		if ( 'on' != $this->settings['general']['skip_cron'] ) {
			WC()->queue()->schedule_single(
				time() + 1,
				'woo_order_auto_print_run_print_callback',
				array(
					'order_id'       => $order_id,
					'print_on'       => 'payment_complete',
					'current_action' => $current_action,
				),
				'woo-order-auto-print-ac'
			);
		} else {
			$this->logh( "Skipped WP cron and called directly {$order_id} order and payment_complete called" );
			$this->print_order( $order_id, 'payment_complete', $current_action );
		}
	}

	/**
	 * Log messages
	 *
	 * @since    1.0.0
	 * @param      string $message       The Message to log.
	 */
	public function logh( $message ) {
		if ( 'on' == $this->settings['general']['enable_log'] ) {
			$logger = wc_get_logger();
			$logger->debug( $message, array( 'source' => 'automatic-order-printing-for-woocommerce' ) );
		}
	}

	/**
	 * Print order
	 *
	 * @since    1.0.0
	 * @param      int    $order_id       The Order id.
	 * @param      string $print_on       The print condition.
	 * @param      string $current_action Current Action.
	 */
	public function print_order( $order_id, $print_on, $current_action ) {
		$this->logh( "{$order_id} order and {$print_on} called, action : {$current_action}" );

		$templates = get_woo_order_auto_print_templates();
		$order     = wc_get_order( $order_id );
		if ( ! empty( $templates ) ) {
			foreach ( $templates as $template_id => $_template ) {
				if ( 'on' != $_template['meta']['enable'] ) {
					continue;
				}
				if ( 'yes' == get_post_meta( $order_id, "_printnode_print_{$template_id}", true ) ) {
					continue;
				}
				$template          = new Woo_Order_Auto_Print_Templates( $order_id, $template_id );
				$template_settings = $template->get_template_settings();
				$print_this        = false;
				if ( 'payment_complete' == $print_on ) {
					if ( isset( $template_settings['print']['print_on_payment_complete'] ) && ! empty( $template_settings['print']['print_on_payment_complete'] ) ) {
						$order_payment_method = $order->get_payment_method();
						if ( in_array( $order_payment_method, $template_settings['print']['print_on_payment_complete'] ) ) {
							$print_this = true;
						}
					}
				}

				if ( 'status_change' == $print_on ) {
					if ( isset( $template_settings['print']['print_on_order_status'] ) && ! empty( $template_settings['print']['print_on_order_status'] ) ) {
						$statuses_array = $template_settings['print']['print_on_order_status'];
						foreach ( $statuses_array as $statuses ) {
							$status_from = ( 'wc-' === substr( $statuses['from'], 0, 3 ) ) ? substr( $statuses['from'], 3 ) : $statuses['from'];
							$status_to   = ( 'wc-' === substr( $statuses['to'], 0, 3 ) ) ? substr( $statuses['to'], 3 ) : $statuses['to'];
							if ( "woocommerce_order_status_{$status_from}_to_{$status_to}" == $current_action ) {
								$print_this = true;
							}
						}
					}
				}

				if ( 'immediately' == $print_on ) {

					if ( isset( $template_settings['print']['print_immediately'] ) && ! empty( $template_settings['print']['print_immediately'] ) ) {
						$order_payment_method = $order->get_payment_method();
						if ( in_array( $order_payment_method, $template_settings['print']['print_immediately'] ) ) {
							$print_this = true;
						}
					}
				}

				if ( true === $print_this ) {
					/**Perpetual code */
					$items = $order->get_items( 'line_item' );
					foreach( $items as $item_key => $item ) {
						$line_item = new WC_Order_Item_Product( $item );
						$product_id = $line_item->get_product_id();
						if( $product_id ) {
							$vendor_id = wcfm_get_vendor_id_by_post( $product_id );
							break;
						}
					}					
					if($vendor_id == $template_settings['print']['print_on_order_branch'] ){
						/**Perpetual code */
						$this->logh( "{$order_id} order and print task start" );
						$template->run_actions();
						$html = $template->generate_html();
						$template->remove_actions();

						if ( 'on' == $this->settings['general']['use_mpdf'] && class_exists( 'Automatic_Order_Printing_For_Woocommerce_Mpdf_Addon_Mpdf' ) ) {
							$mpdf      = new Automatic_Order_Printing_For_Woocommerce_Mpdf_Addon_Mpdf( $order_id, $template_id, $template_settings );
							$file_path = $mpdf->generate_pdf( $task, $html );
						} else {
							$dompdf    = new Woo_Order_Auto_Print_Dompdf( $order_id, $template_id, $template_settings );
							$file_path = $dompdf->generate_pdf( $task, $html );
						}

						$printnode     = new Woo_Order_Auto_Print_Printnode();
						$status        = $printnode->print_pdf( $file_path, $order_id, $template_settings, $template_id );
						$document_name = $template_settings['meta']['document_name'];
						if ( ! empty( $status['status_code'] ) ) {
							$note = sprintf( '%s send to PrintNode Automatically, Status Code : %s', $document_name, $status['status_code'] );
						} else {
							$note = sprintf( '%s faild to send PrintNode, Status Message : %s', $document_name, $status['status'] );
	
						}
											do_action( 'woo_order_auto_print_run_print_printnode_print_status', $status, $document_name, $template, $order, $current_action, $print_on );
						$order->add_order_note( $note );
						$order->save();
						update_post_meta( $order_id, "_printnode_print_{$template_id}", 'yes' );
					}
				}
			}
		}
	}

}
