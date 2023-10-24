<?php

/**
 * Plugin Name:       WCFM Products Sync
 * Plugin URI:        https://zubitechsol.com/
 * Description:       Simple Solution.
 * Version:           1.0
 * Author:            Zubi Tech Sol
 * Author URI:        https://zubitechsol.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Update URI:        https://example.com/my-plugin/
 * Text Domain:       wcps
 * Domain Path:       /languages
 */
if (!defined('ABSPATH')) {
    die;
}


if (!defined('WCPS_PLUGIN_URL')) {
    define('WCPS_PLUGIN_URL', plugin_dir_url(__FILE__));
}

if (!defined('WCPS_PLUGIN_PATH')) {
    define('WCPS_PLUGIN_PATH', plugin_dir_path(__FILE__));
}
if (!defined('WCPS_PLUGIN_DIRNAME')) {
    define('WCPS_PLUGIN_DIRNAME', dirname(__FILE__));
}

if (!function_exists('wcps_plugin_scripts')) {

    function wcps_plugin_scripts() {

        wp_enqueue_style('wcps_plugin_style', WCPS_PLUGIN_URL . 'assets/css/style.css');

        wp_enqueue_script('wcps_plugin_scripts', WCPS_PLUGIN_URL . 'assets/js/scripts.js', 'jQuery', true);
        wp_localize_script('wcps_plugin_scripts', 'wcps', [
            'admin_ajax' => admin_url('admin-ajax.php'),
            'site_url' => site_url(),
        ]);
    }

    add_action('wp_enqueue_scripts', 'wcps_plugin_scripts');
}

include WCPS_PLUGIN_PATH . 'inc/helper.php';
