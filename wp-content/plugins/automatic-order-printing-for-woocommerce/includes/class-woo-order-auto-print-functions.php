<?php
/**
 * The functions of the plugin.
 *
 * @link       https://www.procomsoftsol.com
 * @since      1.0.0
 *
 * @package    Woo_Order_Auto_Print
 * @subpackage Woo_Order_Auto_Print/includes
 */

/**
 * Return plugin settings.
 *
 * @since    1.0.0
 * @return      array $setting       Plugin settings.
 */
function get_woo_order_auto_print_settings() {
	$settings    = array();
		$options = array(
			'general' => 'woo_order_auto_print',
			'strings' => 'woo_order_auto_print_strings',
		);
		foreach ( $options as $k => $v ) {
			$fields_defaults = $GLOBALS['Woo_Order_Auto_Print_Admin']->{"get_{$k}_settings"}();
			if ( $fields_defaults ) {
				foreach ( $fields_defaults as $k1 => $v1 ) {
					if ( 'strings' == $k ) {
						$settings[ $k ][ $v1['id'] ] = $v1['default'];
					} else {
						$settings[ $k ][ $v1['id'] ] = '';
					}
				}
			}
		}

		foreach ( $options as  $k => $v ) {
			$settings[ $k ] = wp_parse_args( get_option( $v ), $settings[ $k ] );
		}

		return $settings;
}
/**
 * Returns print templates.
 *
 * @since    1.0.0
 * @return      array $templates       Print templats.
 */
function get_woo_order_auto_print_templates() {
	$templates = get_transient( 'get_woo_order_auto_print_templates' );
	if ( false !== $templates ) {
		return $templates;
	}
	$args      = array(
		'post_type'   => 'auto-print-templates',
		'post_status' => 'publish',
		'numberposts' => -1,
	);
	$posts     = get_posts( $args );
	$templates = array();
	if ( $posts ) {
		foreach ( $posts as $post ) {
			$template_id               = $post->ID;
			$template                  = new Woo_Order_Auto_Print_Templates( null, $template_id );
			$template_settings         = $template->get_template_settings();
			$templates[ $template_id ] = $template_settings;
		}
	}
	set_transient( 'get_woo_order_auto_print_templates', $templates, DAY_IN_SECONDS );
	return $templates;
}
/**
 * Returns printers
 *
 * @since    1.0.0
 * @return      array $printers       Printers.
 */
function get_woo_order_auto_printers() {
	$settings = get_woo_order_auto_print_settings();
	$api_key  = $settings['general']['printnode_api_key'];
	if ( ! empty( $api_key ) ) {
		try {
			$credentials = new PrintNode\Credentials();
			$credentials->setApiKey( $api_key );
			$request              = new PrintNode\Request( $credentials );
			$printers             = $request->getPrinters();
						$printers = apply_filters( 'woo_order_auto_print_printers', $printers, $api_key, $settings );
			return $printers;
		} catch ( Exception $e ) {
			return new WP_Error( 'broke', __( 'Invalid PrintNode API Key', 'automatic-order-printing-for-woocommerce' ) );
		}
	}
	return new WP_Error( 'broke', __( 'Invalid PrintNode API Key', 'automatic-order-printing-for-woocommerce' ) );
}

/**
 * Like wc_get_template, but returns the HTML instead of outputting.
 *
 * @param string $template_name Template name.
 * @param array  $args          Arguments. (default: array).
 * @param string $template_path Template path. (default: '').
 * @param string $default_path  Default path. (default: '').
 *
 * @return string
 */
function woo_order_auto_print_get_template_html( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
	ob_start();
	woo_order_auto_print_get_template( $template_name, $args, $template_path, $default_path );
	return ob_get_clean();
}

/**
 * Get templates passing attributes and including the file.
 *
 * @param string $template_name Template name.
 * @param array  $args          Arguments. (default: array).
 * @param string $template_path Template path. (default: '').
 * @param string $default_path  Default path. (default: '').
 */
function woo_order_auto_print_get_template( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
	if ( ! empty( $args ) && is_array( $args ) ) {
		extract( $args ); // @codingStandardsIgnoreLine
	}

	$located = woo_order_auto_print_locate_template( $template_name, $template_path, $default_path );

	if ( ! file_exists( $located ) ) {
		/* translators: %s template */
		_doing_it_wrong( __FUNCTION__, sprintf( esc_html__( '%s does not exist.', 'automatic-order-printing-for-woocommerce' ), '<code>' . esc_html( $located ) . '</code>' ), '2.1' );
		return;
	}

	// Allow 3rd party plugin filter template file from their plugin.
	$located = apply_filters( 'woo_order_auto_print_get_template', $located, $template_name, $args, $template_path, $default_path );

	do_action( 'woo_order_auto_print_before_template_part', $template_name, $template_path, $located, $args );

	include $located;

	do_action( 'woo_order_auto_print_after_template_part', $template_name, $template_path, $located, $args );
}

/**
 * Locate a template and return the path for inclusion.
 *
 * @param string $template_name Template name.
 * @param string $template_path Template path. (default: '').
 * @param string $default_path  Default path. (default: '').
 * @return string
 */
function woo_order_auto_print_locate_template( $template_name, $template_path = '', $default_path = '' ) {
	if ( ! $template_path ) {
		$template_path = apply_filters( 'woo_order_auto_print_template_path', 'woo-order-auto-print/' );
	}

	if ( ! $default_path ) {
		$default_path = WOO_ORDER_AUTO_PRINT_DIR . '/templates/';
	}

	// Look within passed path within the theme - this is priority.
	$template = locate_template(
		array(
			trailingslashit( $template_path ) . $template_name,
			$template_name,
		)
	);

	// Get default template/.
	if ( ! $template ) {
		$template = $default_path . $template_name;
	}

	// Return what we found.
	return apply_filters( 'woo_order_auto_print_locate_template', $template, $template_name, $template_path );
}

/**
 * Wrapper for set_time_limit to see if it is enabled.
 *
 * @since 1.0.0
 * @param int $limit Time limit.
 */
function woo_order_auto_print_set_time_limit( $limit = 0 ) {
	if ( function_exists( 'set_time_limit' ) && false === strpos( ini_get( 'disable_functions' ), 'set_time_limit' ) && ! ini_get( 'safe_mode' ) ) { // phpcs:ignore PHPCompatibility.IniDirectives.RemovedIniDirectives.safe_modeDeprecatedRemoved
		@set_time_limit( $limit ); // @codingStandardsIgnoreLine
	}
}


/**
 * Return template names.
 *
 * @return array
 */
function woo_order_auto_print_get_template_names() {
	$templates = array(
		'simple'   => esc_html__( 'Default Template', 'automatic-order-printing-for-woocommerce' ),
		'advanced' => esc_html__( 'Advanced Template', 'automatic-order-printing-for-woocommerce' ),
	);
	return apply_filters( 'woo_order_auto_print_get_template_names', $templates );
}
