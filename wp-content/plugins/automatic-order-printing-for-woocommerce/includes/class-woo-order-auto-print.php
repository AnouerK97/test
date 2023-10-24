<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://www.procomsoftsol.com
 * @since      1.0.0
 *
 * @package    Woo_Order_Auto_Print
 * @subpackage Woo_Order_Auto_Print/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Woo_Order_Auto_Print
 * @subpackage Woo_Order_Auto_Print/includes
 */
class Woo_Order_Auto_Print {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @var      Woo_Order_Auto_Print_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'WOO_ORDER_AUTO_PRINT_VERSION' ) ) {
			$this->version = WOO_ORDER_AUTO_PRINT_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = WOO_ORDER_AUTO_PRINT_BASENAME;

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

		add_filter( 'plugin_action_links_' . WOO_ORDER_AUTO_PRINT_BASENAME, array( $this, 'plugin_page_settings_link' ), 10, 1 );

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Woo_Order_Auto_Print_Loader. Orchestrates the hooks of the plugin.
	 * - Woo_Order_Auto_Print_i18n. Defines internationalization functionality.
	 * - Woo_Order_Auto_Print_Admin. Defines all hooks for the admin area.
	 * - Woo_Order_Auto_Print_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-woo-order-auto-print-loader.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'vendor/autoload.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'vendor/cmb2/cmb2/init.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'vendor/cmb2-conditionals/cmb2-conditionals.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-woo-order-auto-print-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-woo-order-auto-print-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-woo-order-auto-print-public.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-woo-order-auto-print-functions.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-woo-order-auto-print-cpt.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-woo-order-auto-print-templates.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-woo-order-auto-print-dompdf.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-woo-order-auto-print-printnode.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-woo-order-auto-print-hooks.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-woo-order-auto-print-integration.php';

				require_once plugin_dir_path( dirname( __FILE__ ) ) . 'woo-includes/woo-functions.php';

		$this->loader = new Woo_Order_Auto_Print_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Woo_Order_Auto_Print_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 */
	private function set_locale() {

		$plugin_i18n = new Woo_Order_Auto_Print_I18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Woo_Order_Auto_Print_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		$GLOBALS['Woo_Order_Auto_Print_Admin'] = $plugin_admin;

		$plugin_cpt                          = new Woo_Order_Auto_Print_Cpt( $this->get_plugin_name(), $this->get_version() );
		$GLOBALS['Woo_Order_Auto_Print_Cpt'] = $plugin_cpt;

		$plugin_print_hooks                    = new Woo_Order_Auto_Print_Hooks( $this->get_plugin_name(), $this->get_version() );
		$GLOBALS['Woo_Order_Auto_Print_Hooks'] = $plugin_print_hooks;

		$plugin_print_integration                    = new Woo_Order_Auto_Print_Integration( $this->get_plugin_name(), $this->get_version() );
		$GLOBALS['Woo_Order_Auto_Print_Integration'] = $plugin_print_integration;

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 */
	private function define_public_hooks() {

		$plugin_public = new Woo_Order_Auto_Print_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Woo_Order_Auto_Print_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Plugin page settings.
	 *
	 * @since   1.0.0
	 * @param       Array $links  Plugin Settings page link.
	 * @return      Array $links       Plugin Settings page link.
	 */
	public function plugin_page_settings_link( $links ) {

		$action_links = array(
			'settings'  => '<a href="' . admin_url( 'admin.php?page=woo_order_auto_print' ) . '" aria-label="' . esc_attr__( 'View settings', 'automatic-order-printing-for-woocommerce' ) . '">' . esc_html__( 'Settings', 'automatic-order-printing-for-woocommerce' ) . '</a>',
			'templates' => '<a href="' . admin_url( 'edit.php?post_type=auto-print-templates' ) . '" aria-label="' . esc_attr__( 'View templates', 'automatic-order-printing-for-woocommerce' ) . '">' . esc_html__( 'Templates', 'automatic-order-printing-for-woocommerce' ) . '</a>',
		);

		return array_merge( $action_links, $links );
	}

}
