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
class Woo_Order_Auto_Print_Printnode {

	/**
	 * The AIP Key
	 *
	 * @since    1.0.0
	 * @var      string    $api_key    The API Key.
	 */
	public $api_key;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$settings      = get_woo_order_auto_print_settings();
		$this->api_key = $settings['general']['printnode_api_key'];

	}

	/**
	 * Log messages
	 *
	 * @since    1.0.0
	 * @param      string $message       The Message to log.
	 */
	public function logh( $message ) {
		$settings = get_woo_order_auto_print_settings();
		if ( 'on' == $settings['general']['enable_log'] ) {
			$logger = wc_get_logger();
			if ( is_array( $message ) ) {
				ob_start();
				print_r( $message );
				$message = ob_get_clean();
			}
			$logger->debug( $message, array( 'source' => 'automatic-order-printing-for-woocommerce' ) );
		}
	}

	/**
	 * Print PDF file
	 *
	 * @since    1.0.0
	 * @param      string $file_path  The pdf file path.
	 * @param      int    $order_id       The Order id.
	 * @param array  $template_settings The template settings.
	 * @param      int    $template_id       The template id.
	 * @return string $status  The print sttus.
	 */
	public function print_pdf( $file_path, $order_id, $template_settings, $template_id ) {
		woo_order_auto_print_set_time_limit( 0 );
		$this->logh( "Order : {$order_id} Print Started" );
		$template_printer_id = $template_settings['meta']['printer'];
		if ( empty( $template_printer_id ) ) {
			return $this->logh( 'empty template id' );
		}
		$printers = $this->get_printers();

		if ( is_wp_error( $printers ) ) {
			return $this->logh( $printers );
		}

		$selected_printer = '';

		foreach ( $printers as $printer ) {
			if ( $printer->id == $template_printer_id ) {
				$selected_printer = $printer;
			}
		}

		if ( empty( $selected_printer ) ) {
			$this->logh( $printers );
			return $this->logh( "empty selected printer : {$template_printer_id}" );
		}
		$options = array();
		if ( isset( $template_settings['meta'][ "printer_{$template_printer_id}_papers" ] ) ) {
			$options['paper'] = $template_settings['meta'][ "printer_{$template_printer_id}_papers" ];
		}

		if ( isset( $template_settings['meta'][ "printer_{$template_printer_id}_bins" ] ) && ! empty( $template_settings['meta'][ "printer_{$template_printer_id}_bins" ] ) ) {
				$options['fit_to_page'] = true;
		}

		if ( 'on' == $template_settings['meta']['enable_fit_to_page'] ) {
			$options['bin'] = $template_settings['meta'][ "printer_{$template_printer_id}_bins" ];
		}

		$data = file_get_contents( $file_path );

		$document_name   = $template_settings['meta']['document_name'] . ' - #' . $order_id;
		$print_job_title = apply_filters( 'woo_order_auto_print_document_title', $document_name, $order_id, $template_id );

		$credentials = new PrintNode\Credentials();
		$credentials->setApiKey( $this->api_key );

		$request                = new PrintNode\Request( $credentials );
		$print_job              = new PrintNode\PrintJob();
		$print_job->printer     = $selected_printer;
		$print_job->contentType = 'pdf_base64'; // @codingStandardsIgnoreLine
		$print_job->content     = base64_encode( $data );
		$print_job->source      = 'Auto Print/1.0';
		$print_job->title       = $print_job_title;

				$request = apply_filters( 'woo_order_auto_print_printnode_request', $request, $order_id, $template_id, $template_settings );

		$options = apply_filters( 'woo_order_auto_print_printnode_options', $options, $order_id, $template_id, $template_settings );

		if ( ! empty( $options ) ) {
			$print_job->options = $options;
		}
		$status      = '';
		$status_code = '';
		$print_job   = apply_filters( 'woo_order_auto_print_printnode_printjob', $print_job, $file_path, $order_id, $template_id, $template_settings );
		for ( $i = 1; $i <= $template_settings['meta']['copies']; $i++ ) {
			try {
				$response = $request->post( $print_job );
				$status   = $response->getStatusMessage();
				if ( method_exists( $response, 'getStatusCode' ) ) {
					$status_code = $response->getStatusCode();
				}
				ob_start();
					echo esc_html( "Order : {$order_id} " );
					print_r( $response ); // @codingStandardsIgnoreLine
				$dd = ob_get_clean();
				$this->logh( $dd );

			} catch ( Exception $e ) {
				$status = $e->getMessage();
				if ( method_exists( $response, 'getStatusCode' ) ) {
					$status_code = $response->getStatusCode();
				}
				ob_start();
					echo esc_html( 'Status Code : ' . $status_code . ' Status Message : ' . $status );
					echo esc_html( "Order : {$order_id} " );
					print_r( $e ); // @codingStandardsIgnoreLine
				$dd = ob_get_clean();
				$this->logh( $dd );
			}
		}
		return array(
			'status'      => $status,
			'status_code' => $status_code,
		);
	}

	/**
	 * Get printers
	 *
	 * @since    1.0.0
	 * @return      array $printers       printers.
	 */
	public function get_printers() {
		try {
			$credentials = new PrintNode\Credentials();
			$credentials->setApiKey( $this->api_key );
			$request              = new PrintNode\Request( $credentials );
			$printers             = $request->getPrinters();
						$printers = apply_filters( 'woo_order_auto_print_printers', $printers, $api_key, array() );
			return $printers;
		} catch ( Exception $e ) {
			return new WP_Error( 'broke', __( 'Invalid PrintNode API Key', 'automatic-order-printing-for-woocommerce' ) );
		}
	}

}
