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
class Woo_Order_Auto_Print_Dompdf {

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
	 * @var      int    $template_settings    The Settings the template.
	 */
	public $template_settings;

	/**
	 * The Dompdf object
	 *
	 * @since    1.0.0
	 * @var      object    $dompdf    The Dompdf instance.
	 */
	public $dompdf;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      int   $order_id       The order id.
	 * @param      int   $template_id       The template id.
	 * @param      array $template_settings    The template settings.
	 */
	public function __construct( $order_id, $template_id, $template_settings ) {

		// require_once plugin_dir_path( dirname( __FILE__ ) ) . 'vendor/dompdf/dompdf/autoload.inc.php'; // no need.

		$this->dompdf            = new Dompdf\Dompdf();
		$this->order_id          = $order_id;
		$this->template_id       = $template_id;
		$this->template_settings = $template_settings;

	}

	/**
	 * Returns temp dir structure.
	 *
	 * @since    1.0.0
	 * @return      Array $dir       Tem dir.
	 */
	public function get_temp_dir() {
		$upload     = wp_upload_dir();
		$upload_dir = $upload['basedir'];
		$upload_url = $upload['baseurl'];
		$upload_dir = $upload_dir . '/woo-order-auto-print';
		$upload_url = $upload_url . '/woo-order-auto-print';

				$files = array(
					array(
						'base'    => $upload_dir,
						'file'    => '.htaccess',
						'content' => '
order allow,deny
<Files ~ "\.(zip)$">
    allow from all
</Files>',
					),
					array(
						'base'    => $upload_dir,
						'file'    => 'index.html',
						'content' => '',
					),
				);

				foreach ( $files as $file ) {

					if ( wp_mkdir_p( $file['base'] ) && ! file_exists( trailingslashit( $file['base'] ) . $file['file'] ) ) {
											$file_handle = @fopen( trailingslashit( $file['base'] ) . $file['file'], 'w' );

						if ( $file_handle ) {
							fwrite( $file_handle, $file['content'] );
							fclose( $file_handle );
						}
					}
				}

				$time = current_time( 'mysql' );

				$y      = substr( $time, 0, 4 );
				$m      = substr( $time, 5, 2 );
				$subdir = "/$y/$m";

				$upload_dir = $upload_dir . $subdir;
				$upload_url = $upload_url . $subdir;

				if ( ! is_dir( $upload_dir ) ) {
					wp_mkdir_p( $upload_dir );
				}

				$files = array(
					array(
						'base'    => $upload_dir,
						'file'    => '.htaccess',
						'content' => '
order allow,deny
<Files ~ "\.(zip)$">
    allow from all
</Files>',
					),
					array(
						'base'    => $upload_dir,
						'file'    => 'index.html',
						'content' => '',
					),
				);

				foreach ( $files as $file ) {

					if ( wp_mkdir_p( $file['base'] ) && ! file_exists( trailingslashit( $file['base'] ) . $file['file'] ) ) {
											$file_handle = @fopen( trailingslashit( $file['base'] ) . $file['file'], 'w' );

						if ( $file_handle ) {
							fwrite( $file_handle, $file['content'] );
							fclose( $file_handle );
						}
					}
				}

				$dir = array(
					'path' => $upload_dir,
					'url'  => $upload_url,
				);

				return apply_filters( 'woo_order_auto_print_temp_dir', $dir, $this->order_id, $this->template_id );
	}

	/**
	 * Returns temp dir structure.
	 *
	 * @since    1.0.0
	 * @param   String $action  Action.
	 * @param   String $html  Document html.
	 * @return      mixed $file_path|true       File path.
	 */
	public function generate_pdf( $action, $html ) {
		$temp_dir      = $this->get_temp_dir();
		$upload_dir    = $temp_dir['path'];
		$upload_url    = $temp_dir['url'];
		$document_name = $this->template_settings['meta']['document_name'] . ' - #' . $this->order_id;
		$name          = apply_filters( 'woo_order_auto_print_document_title', $document_name, $this->order_id, $this->template_id );
		$name          = sanitize_file_name( $name );
		$file_path     = $upload_dir . '/' . $name . '.pdf';
		$file_url      = $upload_url . '/' . $name . '.pdf';

		// $html = utf8_decode( $html );

		$size_array = array(
			'A6' => array( 0, 0, 297.64, 419.53 ),
			'A7' => array( 0, 0, 209.76, 297.64 ),
			'A8' => array( 0, 0, 147.40, 209.76 ),
		);

		$new_size = '';
		$pdf_size = $this->template_settings['meta']['pdf_size'];
		$pdf_size = ( ! empty( $pdf_size ) ) ? $pdf_size : 'A4';

		if ( in_array( $pdf_size, array( 'A6', 'A7', 'A8' ) ) ) {

			$dompdf = new Dompdf\Dompdf();

			$dompdf->set_option( 'tempDir', $upload_dir );
			$dompdf->set_option( 'isHtml5ParserEnabled', true );
			$dompdf->set_option( 'enableCssFloat', true );
			$dompdf->set_option( 'isRemoteEnabled', true );
			$dompdf->set_option( 'defaultFont', 'dejavu sans' );
			$dompdf->set_option( 'enable_font_subsetting', true );
			$dompdf->setPaper( $pdf_size, 'portrait' );

			$GLOBALS['dombodyHeight'] = 0;

			$GLOBALS['dompdf_small_page_size_adjust'] = apply_filters( 'woo_order_auto_print_dompdf_small_page_size_adjust', 50, $action, $html, $this );

			$dompdf->setCallbacks(
				array(
					'myCallbacks' => array(
						'event' => 'end_frame',
						'f'     => function ( $infos ) {
							/*$frame = $infos['frame'];
							if ( strtolower( $frame->get_node()->nodeName ) === 'body' ) {
								$padding_box = $frame->get_border_box();
								$GLOBALS['dombodyHeight'] += $padding_box['h'];
								$GLOBALS['dombodyHeight'] += $GLOBALS['dompdf_small_page_size_adjust']; // Add 15 per page for no more page breaks.
							}*/
							$frame = $infos->get_node();
							if ( strtolower( $frame->nodeName ) === 'body' ) {
								$padding_box = $infos->get_border_box();
								$GLOBALS['dombodyHeight'] += $padding_box['h'];
								$GLOBALS['dombodyHeight'] += $GLOBALS['dompdf_small_page_size_adjust']; // Add 15 per page for no more page breaks.
							}
						},
					),
				)
			);

			$dompdf->loadHtml( $html );
			$dompdf->render();
			unset( $dompdf );
			$new_size    = $size_array[ $pdf_size ];
			$new_size[3] = $GLOBALS['dombodyHeight'];
		}

		$this->dompdf->set_option( 'tempDir', $upload_dir );
		$this->dompdf->set_option( 'isHtml5ParserEnabled', true );
		$this->dompdf->set_option( 'enableCssFloat', true );
		$this->dompdf->set_option( 'isRemoteEnabled', true );
		$this->dompdf->set_option( 'defaultFont', 'dejavu sans' );
		$this->dompdf->set_option( 'enable_font_subsetting', true );

		if ( in_array( $pdf_size, array( 'A6', 'A7', 'A8' ) ) ) {
			$this->dompdf->set_paper( $new_size, 'portrait' );
		} else {
			$this->dompdf->setPaper( $pdf_size, 'portrait' );
		}

		$this->dompdf->loadHtml( $html );
		$this->dompdf->render();

		if ( 'preview' == $action ) {
			$this->dompdf->stream( $file_path, array( 'Attachment' => false ) );
		} elseif ( 'download' == $action ) {
			$this->dompdf->stream( $file_path, array( 'Attachment' => true ) );
		} else {
			@file_put_contents( $file_path, $this->dompdf->output() );
			return $file_path;
		}
		return true;
	}
}
