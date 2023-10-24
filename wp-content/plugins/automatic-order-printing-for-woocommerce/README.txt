=== Automatic Order Printing for WooCommerce ===
Contributors: Procom
Donate link: https://www.procomsoftsol.com
Tags: Woocommerce Order Auto Print
Requires at least: 3.0.1
Tested up to: 5.9
Stable tag: 4.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Automatic Order Printing for WooCommerce allows to you print your order invoices, packing slips automatically using PrintNode.

== Description ==

Automatic Order Printing for WooCommerce allows to you print your order invoices, packing slips automatically using PrintNode.

== Installation ==


1. Upload `automatic-order-printing-for-woocommerce` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress

== Changelog ==

2023-05-13 - version 1.0.9
* Added : Advanced template option
* Added : Template html preview option
* Fixed : Multi site issues
* Fixed : CMB2 conflict in the admin screen
* Tweak : Added new filter woo_order_auto_print_printers to modify printers
* Tweak : Added new filter woo_order_auto_print_printnode_request to modify printnode request
* Tweak : Added new action woo_order_auto_print_run_print_printnode_print_status for thirdparty plugins to update order status messages


2022-03-30 - version 1.0.8
* Added : Fit to page option on template settings.
* Added : custom css option to template.
* Added : Printer bin selection option to template.
* fixed : GDPR issues.
* Tweak : Added new filter woo_order_auto_print_template_settings to modify template settings.
* Tweak : updated .pot file.
* Fixed : PrintNode options issue.

2021-12-17 - version 1.0.7
* Added : mPDF support for RTL Support.
* Tweak : Added new filter woo_order_auto_print_printnode_options to modify PrintNode options.
* Tweak : updated .pot file
* Fixed : PrintNode options issue.

2021-09-14 - version 1.0.6
* Tweak : Updated dom libreary to support PHP 8 support.
* Tweak : updated code to fix issues in the thirdparty plugin support
* Tweak : fixed some warnings in the template

2021-06-16 - version 1.0.5
* Added : String translation from settings.
* Tweak : modify template functions to remove unnecessary divs to display on the printout
* Tweak : updated .pot file

2021-05-25 - version 1.0.4
* Added : Option to skip WP Cron and print directly.

2021-05-21 - version 1.0.3
* Added : Added thirdparty plugins data  ( order delivery slots, delivery times etc. )
* Fix : Print issue with order status change
	

2021-04-19 - version 1.0.2
* Tweak : Added action hooks (woo_order_auto_print_before_order_data, woo_order_auto_print_after_order_data) for add thirdparty plugins data

2021-04-12 - version 1.0.1
* Fix : Fatal error when checking orders for preview. added if ( is_a( $order, 'WC_Order' ) )

2021-01-28 - version 1.0.0
* Initial Release