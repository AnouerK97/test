<?php
/**
 * The plugin bootstrap file
 *
 * @link    https://www.procomsoftsol.com
 * @since   1.0.0
 * @package Woo_Order_Auto_Print
 *
 * @wordpress-plugin
 * Plugin Name:       Automatic Order Printing for WooCommerce
 * Plugin URI:        https://www.procomsoftsol.com
 * Description:       Automatic Order Printing for WooCommerce allows to you print your order invoices, packing slips automatically using PrintNode.
 * Version:           1.0.9
 * Author:            Procom
 * Author URI:        https://www.procomsoftsol.com
 * Developer:         Procom
 * Developer URI:     https://www.procomsoftsol.com
 * Text Domain:       automatic-order-printing-for-woocommerce
 * Domain Path:       /languages
 * Requires at least: 4.6
 * Tested up to: 6.2
 * WC requires at least: 4.0.0
 * WC tested up to:   7.7
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * Woo: 7607307:0a7cdecc24e3e921059f47af2c7790f5
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'WOO_ORDER_AUTO_PRINT_VERSION', '1.0.9' );


/**
 * Currently plugin file.
 */
define( 'WOO_ORDER_AUTO_PRINT_FILE', __FILE__ );

/**
 * Currently plugin basename.
 */
define( 'WOO_ORDER_AUTO_PRINT_BASENAME', plugin_basename( WOO_ORDER_AUTO_PRINT_FILE ) );

/**
 * Currently plugin dir.
 */
define( 'WOO_ORDER_AUTO_PRINT_DIR', untrailingslashit( plugin_dir_path( __FILE__ ) ) );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-woo-order-auto-print-activator.php
 */
function activate_woo_order_auto_print() {
	include_once plugin_dir_path( __FILE__ ) . 'includes/class-woo-order-auto-print-activator.php';
	Woo_Order_Auto_Print_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-woo-order-auto-print-deactivator.php
 */
function deactivate_woo_order_auto_print() {
	include_once plugin_dir_path( __FILE__ ) . 'includes/class-woo-order-auto-print-deactivator.php';
	Woo_Order_Auto_Print_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_woo_order_auto_print' );
register_deactivation_hook( __FILE__, 'deactivate_woo_order_auto_print' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-woo-order-auto-print.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since 1.0.0
 */
function run_woo_order_auto_print() {
	$plugin = new Woo_Order_Auto_Print();
	$plugin->run();

}

/**
* Check if WooCommerce is active
*/
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) || ( is_multisite() && array_key_exists( 'woocommerce/woocommerce.php', get_site_option( 'active_sitewide_plugins' ) ) ) ) {
	run_woo_order_auto_print();
} else {
	add_action( 'admin_notices', 'woo_order_auto_print_installed_notice' );
}


/**
 * Display Woocommerce Activation notice.
 */
function woo_order_auto_print_installed_notice() {     ?>
	<div class="error">
	  <p><?php echo esc_html__( 'Automatic Order Printing for WooCommerce requires the WooCommerce. Please install or activate woocommere', 'automatic-order-printing-for-woocommerce' ); ?></p>
	</div>
	<?php
}
