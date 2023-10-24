<?php

/**
 * The supporting functions of the plugin.
 *
 * @link       https://www.procomsoftsol.com
 * @since      1.0.8
 *
 * @package    Woo_Order_Auto_Print
 * @subpackage Woo_Order_Auto_Print/woo-includes
 */
add_action( 'woo_order_auto_print_template_preview_html', 'woo_order_auto_print_template_preview_html' );

/**
 * Display template html.
 *
 * @since 1.0.8
 * @param string $html Template html.
 */
function woo_order_auto_print_template_preview_html( $html ) {
	echo $html;
}
