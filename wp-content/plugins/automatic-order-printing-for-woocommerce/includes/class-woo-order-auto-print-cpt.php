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
class Woo_Order_Auto_Print_Cpt {

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
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of this plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		$this->printers    = get_woo_order_auto_printers();

		add_action( 'init', array( $this, 'register_post_type' ), 90 );

		// Admin notices.
		add_filter( 'post_updated_messages', array( $this, 'post_updated_messages' ) );
		add_filter( 'bulk_post_updated_messages', array( $this, 'bulk_post_updated_messages' ), 10, 2 );

		add_action( 'cmb2_admin_init', array( $this, 'custom_fields' ) );
		add_filter( 'cmb2_render_order_status', array( $this, 'render_order_status_callback' ), 10, 5 );
		add_filter( 'cmb2_sanitize_order_status', array( $this, 'sanitize_order_status' ), 10, 5 );
		add_filter( 'cmb2_types_esc_order_status', array( $this, 'escape_order_status' ), 10, 4 );

		add_filter( 'cmb2_render_preview_template', array( $this, 'render_preview_template_callback' ), 10, 5 );

		add_filter( 'manage_auto-print-templates_posts_columns', array( $this, 'define_columns' ) );
		add_action( 'manage_auto-print-templates_posts_custom_column', array( $this, 'render_columns' ), 10, 2 );

		add_action( 'save_post', array( $this, 'save_post' ), 10, 3 );
		add_action( 'delete_post', array( $this, 'delete_post' ) );
		add_action( 'wp_trash_post', array( $this, 'trash_post' ) );
		add_action( 'untrashed_post', array( $this, 'untrash_post' ) );

	}

	/**
	 * Register new post type
	 *
	 * @since      1.0.0
	 */
	public function register_post_type() {

		$labels = array(
			'name'                  => esc_html_x( 'Auto Print Templates', 'Post Type General Name', 'automatic-order-printing-for-woocommerce' ),
			'singular_name'         => esc_html_x( 'Auto Print Templates', 'Post Type Singular Name', 'automatic-order-printing-for-woocommerce' ),
			'menu_name'             => esc_html__( 'Auto Print Templates', 'automatic-order-printing-for-woocommerce' ),
			'name_admin_bar'        => esc_html__( 'Auto Print Templates', 'automatic-order-printing-for-woocommerce' ),
			'archives'              => esc_html__( 'Item Archives', 'automatic-order-printing-for-woocommerce' ),
			'attributes'            => esc_html__( 'Item Attributes', 'automatic-order-printing-for-woocommerce' ),
			'parent_item_colon'     => esc_html__( 'Parent Item:', 'automatic-order-printing-for-woocommerce' ),
			'all_items'             => esc_html__( 'Auto Print Templates', 'automatic-order-printing-for-woocommerce' ),
			'add_new_item'          => esc_html__( 'Add New', 'automatic-order-printing-for-woocommerce' ),
			'add_new'               => esc_html__( 'Add New', 'automatic-order-printing-for-woocommerce' ),
			'new_item'              => esc_html__( 'New Item', 'automatic-order-printing-for-woocommerce' ),
			'edit_item'             => esc_html__( 'Edit Auto Print Template', 'automatic-order-printing-for-woocommerce' ),
			'update_item'           => esc_html__( 'Update Auto Print Template', 'automatic-order-printing-for-woocommerce' ),
			'view_item'             => esc_html__( 'View Auto Print Template', 'automatic-order-printing-for-woocommerce' ),
			'view_items'            => esc_html__( 'View Auto Print Templates', 'automatic-order-printing-for-woocommerce' ),
			'search_items'          => esc_html__( 'Search Auto Print Template', 'automatic-order-printing-for-woocommerce' ),
			'not_found'             => esc_html__( 'Not found', 'automatic-order-printing-for-woocommerce' ),
			'not_found_in_trash'    => esc_html__( 'Not found in Trash', 'automatic-order-printing-for-woocommerce' ),
			'featured_image'        => esc_html__( 'Featured Image', 'automatic-order-printing-for-woocommerce' ),
			'set_featured_image'    => esc_html__( 'Set featured image', 'automatic-order-printing-for-woocommerce' ),
			'remove_featured_image' => esc_html__( 'Remove featured image', 'automatic-order-printing-for-woocommerce' ),
			'use_featured_image'    => esc_html__( 'Use as featured image', 'automatic-order-printing-for-woocommerce' ),
			'insert_into_item'      => esc_html__( 'Insert into Auto Print Template', 'automatic-order-printing-for-woocommerce' ),
			'uploaded_to_this_item' => esc_html__( 'Uploaded to this Auto Print Template', 'automatic-order-printing-for-woocommerce' ),
			'items_list'            => esc_html__( 'Cloud Prin Templates list', 'automatic-order-printing-for-woocommerce' ),
			'items_list_navigation' => esc_html__( 'Auto Print Template list navigation', 'automatic-order-printing-for-woocommerce' ),
			'filter_items_list'     => esc_html__( 'Filter items list', 'automatic-order-printing-for-woocommerce' ),
		);
		$args   = array(
			'label'               => esc_html__( 'Auto Print Templates', 'automatic-order-printing-for-woocommerce' ),
			'description'         => esc_html__( 'Auto Print Templates', 'automatic-order-printing-for-woocommerce' ),
			'labels'              => $labels,
			'supports'            => array( 'title' ),
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => 'woocommerce',
			'menu_position'       => 90,
			'menu_icon'           => 'dashicons-printer',
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => false,
			'can_export'          => false,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'capability_type'     => 'product',
		);
		register_post_type( 'auto-print-templates', $args );

	}

	/**
	 * Change messages when a post type is updated.
	 *
	 * @param  array $messages Array of messages.
	 * @return array
	 */
	public function post_updated_messages( $messages ) {
		global $post;

		$messages['auto-print-templates'] = array(
			0  => '', // Unused. Messages start at index 1.
			1  => esc_html__( 'Auto Print Template updated.', 'automatic-order-printing-for-woocommerce' ),
			2  => esc_html__( 'Custom field updated.', 'automatic-order-printing-for-woocommerce' ),
			3  => esc_html__( 'Custom field deleted.', 'automatic-order-printing-for-woocommerce' ),
			4  => esc_html__( 'Auto Print Template  updated.', 'automatic-order-printing-for-woocommerce' ),
			5  => esc_html__( 'Revision restored.', 'automatic-order-printing-for-woocommerce' ),
			6  => esc_html__( 'Auto Print Template published.', 'automatic-order-printing-for-woocommerce' ),
			7  => esc_html__( 'Auto Print Template saved.', 'automatic-order-printing-for-woocommerce' ),
			8  => esc_html__( 'Auto Print Template submitted.', 'automatic-order-printing-for-woocommerce' ),
			9  => sprintf(
				/* translators: 1: date */
				esc_html__( 'Auto Print Template scheduled for: %1$s.', 'automatic-order-printing-for-woocommerce' ),
				'<strong>' . date_i18n( esc_html__( 'M j, Y @ G:i', 'automatic-order-printing-for-woocommerce' ), strtotime( $post->post_date ) ) . '</strong>'
			),
			/* translators: %s: product url */
			10 => esc_html__( 'Auto Print Template draft updated.', 'automatic-order-printing-for-woocommerce' ),
		);

		return $messages;
	}

	/**
	 * Specify custom bulk actions messages for different post types.
	 *
	 * @param  array $bulk_messages Array of messages.
	 * @param  array $bulk_counts Array of how many objects were updated.
	 * @return array
	 */
	public function bulk_post_updated_messages( $bulk_messages, $bulk_counts ) {
		$bulk_messages['auto-print-templates'] = array(
			/* translators: %s: cloud prints count */
			'updated'   => _n( '%s cloud print Template updated.', '%s cloud prints updated.', $bulk_counts['updated'], 'automatic-order-printing-for-woocommerce' ),
			/* translators: %s: cloud prints count */
			'locked'    => _n( '%s cloud print Template not updated, somebody is editing it.', '%s cloud prints not updated, somebody is editing them.', $bulk_counts['locked'], 'automatic-order-printing-for-woocommerce' ),
			/* translators: %s: product count */
			'deleted'   => _n( '%s cloud print Template permanently deleted.', '%s cloud prints permanently deleted.', $bulk_counts['deleted'], 'automatic-order-printing-for-woocommerce' ),
			/* translators: %s: cloud prints count */
			'trashed'   => _n( '%s cloud print Template moved to the Trash.', '%s cloud prints moved to the Trash.', $bulk_counts['trashed'], 'automatic-order-printing-for-woocommerce' ),
			/* translators: %s: cloud prints count */
			'untrashed' => _n( '%s cloud print Template restored from the Trash.', '%s cloud prints restored from the Trash.', $bulk_counts['untrashed'], 'automatic-order-printing-for-woocommerce' ),
		);

		return $bulk_messages;
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
	 * Custom fields for post type.
	 *
	 * @since      1.0.0
	 */
	public function custom_fields() {

		/**
		 * Registers fields.
		 */
		$args     = array(
			'id'           => 'woo_order_auto_print_meta',
			'title'        => esc_html__( 'Auto Print Settings', 'automatic-order-printing-for-woocommerce' ),
			'object_types' => array( 'auto-print-templates' ),
		);
		$meta_box = new_cmb2_box( $args );

		$fields = $this->get_meta_fields();
		foreach ( $fields as $field ) {
			$meta_box->add_field( $field );
		}

		$args     = array(
			'id'           => 'woo_order_auto_print_store',
			'title'        => esc_html__( 'Store Details', 'automatic-order-printing-for-woocommerce' ),
			'object_types' => array( 'auto-print-templates' ),
		);
		$meta_box = new_cmb2_box( $args );

		$fields = $this->get_store_fields();
		foreach ( $fields as $field ) {
			$meta_box->add_field( $field );
		}

		$args     = array(
			'id'           => 'woo_order_auto_print_billing',
			'title'        => esc_html__( 'Billing Details', 'automatic-order-printing-for-woocommerce' ),
			'object_types' => array( 'auto-print-templates' ),
		);
		$meta_box = new_cmb2_box( $args );

		$fields = $this->get_billing_fields();
		foreach ( $fields as $field ) {
			$meta_box->add_field( $field );
		}

		$args     = array(
			'id'           => 'woo_order_auto_print_shipping',
			'title'        => esc_html__( 'Shipping Details', 'automatic-order-printing-for-woocommerce' ),
			'object_types' => array( 'auto-print-templates' ),
		);
		$meta_box = new_cmb2_box( $args );

		$fields = $this->get_shipping_fields();
		foreach ( $fields as $field ) {
			$meta_box->add_field( $field );
		}

		$args     = array(
			'id'           => 'woo_order_auto_print_order_details',
			'title'        => esc_html__( 'Order Details', 'automatic-order-printing-for-woocommerce' ),
			'object_types' => array( 'auto-print-templates' ),
		);
		$meta_box = new_cmb2_box( $args );

		$fields = $this->get_order_details_fields();
		foreach ( $fields as $field ) {
			$meta_box->add_field( $field );
		}

		$args     = array(
			'id'           => 'woo_order_auto_print_products',
			'title'        => esc_html__( 'Products Details', 'automatic-order-printing-for-woocommerce' ),
			'object_types' => array( 'auto-print-templates' ),
		);
		$meta_box = new_cmb2_box( $args );

		$fields = $this->get_product_details_fields();
		foreach ( $fields as $field ) {
			$meta_box->add_field( $field );
		}

		$args     = array(
			'id'           => 'woo_order_auto_print_footer',
			'title'        => esc_html__( 'Footer', 'automatic-order-printing-for-woocommerce' ),
			'object_types' => array( 'auto-print-templates' ),
		);
		$meta_box = new_cmb2_box( $args );

		$fields = $this->get_footer_fields();
		foreach ( $fields as $field ) {
			$meta_box->add_field( $field );
		}

		$args     = array(
			'id'           => 'woo_order_auto_print_conditions',
			'title'        => esc_html__( 'Print Conditions', 'automatic-order-printing-for-woocommerce' ),
			'object_types' => array( 'auto-print-templates' ),
			'desc'         => 'desc here',
		);
		$meta_box = new_cmb2_box( $args );

		$title_field = array(
			'desc' => esc_html__( 'Order will print on first sucessfull condition. ', 'automatic-order-printing-for-woocommerce' ),
			'type' => 'title',
			'id'   => 'print_conditions_title',
		);

		$meta_box->add_field( $title_field );

		$fields = $this->get_print_fields();
		foreach ( $fields as $field ) {
			$meta_box->add_field( $field );
		}

				$args     = array(
					'id'           => 'woo_order_auto_print_custom_css',
					'title'        => esc_html__( 'Custom CSS', 'automatic-order-printing-for-woocommerce' ),
					'object_types' => array( 'auto-print-templates' ),
					'desc'         => 'Custom CSS',
				);
				$meta_box = new_cmb2_box( $args );

				$title_field = array(
					'desc' => esc_html__( 'You can add custom css for the template. ', 'automatic-order-printing-for-woocommerce' ),
					'type' => 'title',
					'id'   => 'print_custom_css_title',
				);

				$meta_box->add_field( $title_field );

				$fields = $this->get_custom_css_fields();
				foreach ( $fields as $field ) {
					$meta_box->add_field( $field );
				}

	}

	/**
	 * Return meta fields
	 *
	 * @since      1.0.0
	 * @return     array     $fields
	 */
	public function get_meta_fields() {
		$fields = array();

		$fields['enable'] = array(
			'name'    => esc_html__( 'Enable', 'automatic-order-printing-for-woocommerce' ),
			'desc'    => esc_html__( 'Enable Auto Print', 'automatic-order-printing-for-woocommerce' ),
			'id'      => 'enable',
			'type'    => 'checkbox',
			'default' => $this->set_default_check_value( true ),
		);

		$fields['document_name'] = array(
			'name'    => esc_html__( 'Document Name', 'automatic-order-printing-for-woocommerce' ),
			'desc'    => esc_html__( 'Please enter document name', 'automatic-order-printing-for-woocommerce' ),
			'id'      => 'document_name',
			'type'    => 'text',
			'default' => 'invoice',
		);

		$fields['pdf_size'] = array(
			'name'    => esc_html__( 'PDF Size', 'automatic-order-printing-for-woocommerce' ),
			'desc'    => esc_html__( 'Select PDF Size', 'automatic-order-printing-for-woocommerce' ),
			'id'      => 'pdf_size',
			'type'    => 'select',
			'default' => 'A4',
			'options' => array(
				'A1'     => esc_html__( 'A1 (594 x 841 mm)', 'automatic-order-printing-for-woocommerce' ),
				'A2'     => esc_html__( 'A2 (420 x 594 mm)', 'automatic-order-printing-for-woocommerce' ),
				'A3'     => esc_html__( 'A3 (297 x 420 mm)', 'automatic-order-printing-for-woocommerce' ),
				'A4'     => esc_html__( 'A4 (210 x 297 mm)', 'automatic-order-printing-for-woocommerce' ),
				'A5'     => esc_html__( 'A5 (148 x 210 mm)', 'automatic-order-printing-for-woocommerce' ),
				'A6'     => esc_html__( 'A6 (105 x 148 mm)', 'automatic-order-printing-for-woocommerce' ),
				'A7'     => esc_html__( 'A7 (74 x 105 mm)', 'automatic-order-printing-for-woocommerce' ),
				'A8'     => esc_html__( 'A8 (52 x 74 mm)', 'automatic-order-printing-for-woocommerce' ),
				'LETTER' => esc_html__( 'Letter (216 x 279 mm)', 'automatic-order-printing-for-woocommerce' ),
			),
		);

				$fields['enable_fit_to_page'] = array(
					'name'    => esc_html__( 'Enable Fit to Page', 'automatic-order-printing-for-woocommerce' ),
					'desc'    => esc_html__( 'Enable Fit to Page option', 'automatic-order-printing-for-woocommerce' ),
					'id'      => 'enable_fit_to_page',
					'type'    => 'checkbox',
					'default' => $this->set_default_check_value( true ),
				);

				$printers  = get_woo_order_auto_printers();
				$_printers = array( '' => esc_html__( 'Select Printer', 'automatic-order-printing-for-woocommerce' ) );
				foreach ( $printers as $printer ) {
					$_printers[ $printer->id ] = $printer->name;
				}

				$fields['printer'] = array(
					'name'    => esc_html__( 'Printer', 'automatic-order-printing-for-woocommerce' ),
					'desc'    => esc_html__( 'Select Printer', 'automatic-order-printing-for-woocommerce' ),
					'id'      => 'printer',
					'type'    => 'select',
					'options' => $_printers,
				);

				foreach ( $printers as $printer ) {
					$printer_id = $printer->id;
					if ( isset( $printer->capabilities['papers'] ) && ! empty( $printer->capabilities['papers'] ) ) {
						$_papers = array( '' => esc_html__( 'Select Papers', 'automatic-order-printing-for-woocommerce' ) );
						foreach ( $printer->capabilities['papers'] as $k => $v ) {
							$_papers[ $k ] = $k;
						}
						$fields[ "printer_{$printer_id}_papers" ] = array(
							'name'       => esc_html__( 'Select Papers', 'automatic-order-printing-for-woocommerce' ),
							'desc'       => esc_html__( 'Select Papers', 'automatic-order-printing-for-woocommerce' ),
							'id'         => "printer_{$printer_id}_papers",
							'type'       => 'select',
							'options'    => $_papers,
							'attributes' => array(
								'required'               => true,
								'data-conditional-id'    => 'printer',
								'data-conditional-value' => $printer_id,
							),
						);
					}

					if ( isset( $printer->capabilities['bins'] ) && ! empty( $printer->capabilities['bins'] ) ) {
							$bins = array( '' => esc_html__( 'Use Default', 'automatic-order-printing-for-woocommerce' ) );
						foreach ( $printer->capabilities['bins'] as $k => $v ) {
								$bins[ $v ] = $v;
						}
							$fields[ "printer_{$printer_id}_bins" ] = array(
								'name'       => esc_html__( 'Select Bin', 'automatic-order-printing-for-woocommerce' ),
								'desc'       => esc_html__( 'Select Bin', 'automatic-order-printing-for-woocommerce' ),
								'id'         => "printer_{$printer_id}_bins",
								'type'       => 'select',
								'options'    => $bins,
								'attributes' => array(
									'required'            => true,
									'data-conditional-id' => 'printer',
									'data-conditional-value' => $printer_id,
								),
							);
					}
				}

				$fields['copies'] = array(
					'name'    => esc_html__( 'Copies', 'automatic-order-printing-for-woocommerce' ),
					'desc'    => esc_html__( 'Select Print Copies', 'automatic-order-printing-for-woocommerce' ),
					'id'      => 'copies',
					'type'    => 'select',
					'options' => array(
						1  => 1,
						2  => 2,
						3  => 3,
						4  => 4,
						5  => 5,
						6  => 6,
						7  => 7,
						8  => 8,
						9  => 9,
						10 => 10,
					),
				);

				$_orders = $this->get_sample_orders();

								$_templates = woo_order_auto_print_get_template_names();

				$fields['sample_order'] = array(
					'name'    => esc_html__( 'Sample Order', 'automatic-order-printing-for-woocommerce' ),
					'desc'    => esc_html__( 'Select sample order for preview', 'automatic-order-printing-for-woocommerce' ),
					'id'      => 'sample_order',
					'type'    => 'select',
					'options' => $_orders,
				);

								$fields['print_template'] = array(
									'name'    => esc_html__( 'Select Template', 'automatic-order-printing-for-woocommerce' ),
									'desc'    => esc_html__( 'Select Print template', 'automatic-order-printing-for-woocommerce' ),
									'id'      => 'print_template',
									'type'    => 'select',
									'options' => $_templates,
								);

								$fields['preview_template'] = array(
									'name' => esc_html__( 'Preview Template', 'automatic-order-printing-for-woocommerce' ),
									'id'   => 'preview_template',
									'type' => 'preview_template',
								);

								$fields = apply_filters( 'woo_order_auto_print_template_get_meta_fields', $fields );

								return $fields;
	}


	/**
	 * Return store fields
	 *
	 * @since      1.0.0
	 * @return     array     $fields
	 */
	public function get_store_fields() {
		$fields = array();

		$fields['display_store_logo'] = array(
			'name'    => esc_html__( 'Display Store Logo', 'automatic-order-printing-for-woocommerce' ),
			'desc'    => esc_html__( 'Display store logo on the print', 'automatic-order-printing-for-woocommerce' ),
			'id'      => 'display_store_logo',
			'type'    => 'checkbox',
			'default' => $this->set_default_check_value( true ),
		);

		$fields['display_document_name'] = array(
			'name'    => esc_html__( 'Display Document Name', 'automatic-order-printing-for-woocommerce' ),
			'desc'    => esc_html__( 'Display document name on the print', 'automatic-order-printing-for-woocommerce' ),
			'id'      => 'display_document_name',
			'type'    => 'checkbox',
			'default' => $this->set_default_check_value( true ),
		);

		$fields['store_logo'] = array(
			'name'       => esc_html__( 'Store Logo', 'automatic-order-printing-for-woocommerce' ),
			'desc'       => esc_html__( 'Select store logo for the print. (It will take from plugin settings if you leave blank)', 'automatic-order-printing-for-woocommerce' ),
			'id'         => 'store_logo',
			'type'       => 'file',
			'query_args' => array(
				'type' => array(
					'image/gif',
					'image/jpeg',
					'image/png',
				),
			),
		);

		$fields['display_from_Address'] = array(
			'name'    => esc_html__( 'Display From Address', 'automatic-order-printing-for-woocommerce' ),
			'desc'    => esc_html__( 'Display store addres on the print.', 'automatic-order-printing-for-woocommerce' ),
			'id'      => 'display_from_Address',
			'type'    => 'checkbox',
			'default' => $this->set_default_check_value( true ),
		);

		$fields = apply_filters( 'woo_order_auto_print_template_get_store_fields', $fields );

		return $fields;
	}


	/**
	 * Return billing fields
	 *
	 * @since      1.0.0
	 * @return     array     $fields
	 */
	public function get_billing_fields() {
		$fields = array();

		$fields['display_billing_address'] = array(
			'name'    => esc_html__( 'Display Billing Address', 'automatic-order-printing-for-woocommerce' ),
			'desc'    => esc_html__( 'Display billing address on the Print', 'automatic-order-printing-for-woocommerce' ),
			'id'      => 'display_billing_address',
			'type'    => 'checkbox',
			'default' => $this->set_default_check_value( true ),
		);

		$fields['display_billing_email'] = array(
			'name' => esc_html__( 'Display email address', 'automatic-order-printing-for-woocommerce' ),
			'desc' => esc_html__( 'Display billing email address', 'automatic-order-printing-for-woocommerce' ),
			'id'   => 'display_billing_email',
			'type' => 'checkbox',
		);

		$fields['display_billing_phone'] = array(
			'name'    => esc_html__( 'Display Phone Number', 'automatic-order-printing-for-woocommerce' ),
			'desc'    => esc_html__( 'Display billing phone number', 'automatic-order-printing-for-woocommerce' ),
			'id'      => 'display_billing_phone',
			'type'    => 'checkbox',
			'default' => $this->set_default_check_value( true ),
		);

		$fields = apply_filters( 'woo_order_auto_print_template_get_billing_fields', $fields );

		return $fields;

	}

	/**
	 * Return shipping fields
	 *
	 * @since      1.0.0
	 * @return     array     $fields
	 */
	public function get_shipping_fields() {
		$fields = array();

		$fields['display_shipping_address'] = array(
			'name'    => esc_html__( 'Display Shipping Address', 'automatic-order-printing-for-woocommerce' ),
			'desc'    => esc_html__( 'Display shipping address on the Print', 'automatic-order-printing-for-woocommerce' ),
			'id'      => 'display_shipping_address',
			'type'    => 'checkbox',
			'default' => $this->set_default_check_value( true ),
		);

		$fields = apply_filters( 'woo_order_auto_print_template_get_shipping_fields', $fields );

		return $fields;

	}

	/**
	 * Return order details fields
	 *
	 * @since      1.0.0
	 * @return     array     $fields
	 */
	public function get_order_details_fields() {
		$fields = array();

		$fields['display_order_number'] = array(
			'name'    => esc_html__( 'Display Order Number', 'automatic-order-printing-for-woocommerce' ),
			'id'      => 'display_order_number',
			'type'    => 'checkbox',
			'default' => $this->set_default_check_value( true ),
		);

		$fields['display_order_date'] = array(
			'name'    => esc_html__( 'Display Order Date', 'automatic-order-printing-for-woocommerce' ),
			'id'      => 'display_order_date',
			'type'    => 'checkbox',
			'default' => $this->set_default_check_value( true ),
		);

		$fields['display_payment_method'] = array(
			'name'    => esc_html__( 'Display Order Payment Method', 'automatic-order-printing-for-woocommerce' ),
			'id'      => 'display_payment_method',
			'type'    => 'checkbox',
			'default' => $this->set_default_check_value( true ),
		);

		$fields['display_shipping_method'] = array(
			'name'    => esc_html__( 'Display Order Shipping Method', 'automatic-order-printing-for-woocommerce' ),
			'id'      => 'display_shipping_method',
			'type'    => 'checkbox',
			'default' => $this->set_default_check_value( true ),
		);

		$fields = apply_filters( 'woo_order_auto_print_template_get_order_details_fields', $fields );

		return $fields;

	}

	/**
	 * Return order details fields
	 *
	 * @since      1.0.0
	 * @return     array     $fields
	 */
	public function get_product_details_fields() {
		$fields = array();

		$fields['display_products_table'] = array(
			'name'    => esc_html__( 'Display Products Table', 'automatic-order-printing-for-woocommerce' ),
			'id'      => 'display_products_table',
			'type'    => 'checkbox',
			'default' => $this->set_default_check_value( true ),
		);

		$fields['display_products_image'] = array(
			'name' => esc_html__( 'Display Product Image', 'automatic-order-printing-for-woocommerce' ),
			'id'   => 'display_products_image',
			'type' => 'checkbox',
		);

		$fields['display_products_name'] = array(
			'name'    => esc_html__( 'Display Product Name', 'automatic-order-printing-for-woocommerce' ),
			'id'      => 'display_products_name',
			'type'    => 'checkbox',
			'default' => $this->set_default_check_value( true ),
		);

		$fields['display_products_sku'] = array(
			'name'    => esc_html__( 'Display Product SKU', 'automatic-order-printing-for-woocommerce' ),
			'id'      => 'display_products_sku',
			'type'    => 'checkbox',
			'default' => $this->set_default_check_value( true ),
		);

		$fields['display_products_qty'] = array(
			'name'    => esc_html__( 'Display Product Quantity', 'automatic-order-printing-for-woocommerce' ),
			'id'      => 'display_products_qty',
			'type'    => 'checkbox',
			'default' => $this->set_default_check_value( true ),
		);

		$fields['display_products_price'] = array(
			'name'    => esc_html__( 'Display Product Price', 'automatic-order-printing-for-woocommerce' ),
			'id'      => 'display_products_price',
			'type'    => 'checkbox',
			'default' => $this->set_default_check_value( true ),
		);

		$fields['display_products_total'] = array(
			'name'    => esc_html__( 'Display Product Total', 'automatic-order-printing-for-woocommerce' ),
			'id'      => 'display_products_total',
			'type'    => 'checkbox',
			'default' => $this->set_default_check_value( true ),
		);

		$fields['display_order_total'] = array(
			'name'    => esc_html__( 'Display Order Totals', 'automatic-order-printing-for-woocommerce' ),
			'id'      => 'display_order_total',
			'type'    => 'checkbox',
			'default' => $this->set_default_check_value( true ),
		);

		$fields = apply_filters( 'woo_order_auto_print_template_get_product_details_fields', $fields );

		return $fields;
	}

	/**
	 * Return footer fields
	 *
	 * @since      1.0.0
	 * @return     array     $fields
	 */
	public function get_footer_fields() {
		$fields = array();

		$fields['display_footer'] = array(
			'name'    => esc_html__( 'Display Footer', 'automatic-order-printing-for-woocommerce' ),
			'id'      => 'display_footer',
			'type'    => 'checkbox',
			'default' => $this->set_default_check_value( true ),
		);

		$fields = apply_filters( 'woo_order_auto_print_template_get_footer_fields', $fields );

		return $fields;

	}


	/**
	 * Return print fields
	 *
	 * @since      1.0.0
	 * @return     array     $fields
	 */
	public function get_print_fields() {
		$fields = array();

		$payment_gateways = WC()->payment_gateways->payment_gateways();

		$gateways = array();
		if ( ! empty( $payment_gateways ) ) {
			foreach ( $payment_gateways as $key => $val ) {
				$gateways[ $key ] = $val->get_title();
			}
		}

		$statuses = wc_get_order_statuses();

		$fields['print_immediately'] = array(
			'name'    => esc_html__( 'Print Immediately When Order Created', 'automatic-order-printing-for-woocommerce' ),
			'desc'    => esc_html__( 'Select payment methods for which to print as soon as an order is placed', 'automatic-order-printing-for-woocommerce' ),
			'id'      => 'print_immediately',
			'type'    => 'multicheck',
			'options' => $gateways,
		);

		$fields['print_on_payment_complete'] = array(
			'name'    => esc_html__( 'Print on Payment Complete', 'automatic-order-printing-for-woocommerce' ),
			'desc'    => esc_html__( 'Select payment methods for which to print on payment complete', 'automatic-order-printing-for-woocommerce' ),
			'id'      => 'print_on_payment_complete',
			'type'    => 'multicheck',
			'options' => $gateways,
		);

		$fields['print_on_order_status'] = array(
			'name'       => esc_html__( 'Print on Order Status', 'automatic-order-printing-for-woocommerce' ),
			'desc'       => esc_html__( 'Print order on selected status', 'automatic-order-printing-for-woocommerce' ),
			'id'         => 'print_on_order_status',
			'type'       => 'order_status',
			'repeatable' => true,
			'options'    => $statuses,
		);


		global $WCFMmp;

		$all_stores = $WCFMmp->wcfmmp_vendor->wcfmmp_search_vendor_list(true, '', '', '', '', '', '', []);
		uasort($all_stores, function($a, $b) {
			return strcmp($a, $b);
		});
		
		$fields['print_on_order_branch'] = array(
			'name'       => esc_html__( 'Print Order on branche', 'automatic-order-printing-for-woocommerce' ),
			'desc'       => esc_html__( 'Print Order on branche name', 'automatic-order-printing-for-woocommerce' ),
			'id'         => 'print_on_order_branch',
			'type'       => 'select',
			'repeatable' => false,
			'options'    => $all_stores,
		);

		
		

		$fields = apply_filters( 'woo_order_auto_print_template_get_print_fields', $fields );

		return $fields;
	}

		/**
		 * Return custom css fields
		 *
		 * @since      1.0.8
		 * @return     array     $fields
		 */
	public function get_custom_css_fields() {
		$fields = array();

				$fields['custom_css'] = array(
					'name'       => esc_html__( 'Custom CSS', 'automatic-order-printing-for-woocommerce' ),
					'desc'       => esc_html__( 'Add custom CSS here.', 'automatic-order-printing-for-woocommerce' ),
					'id'         => 'custom_css',
					'type'       => 'textarea_code',
					'attributes' => array(
						'data-codeeditor' => json_encode(
							array(
								'codemirror' => array(
									'mode' => 'css',
								),
							)
						),
					),
				);

				$fields = apply_filters( 'woo_order_auto_print_template_get_custom_css_fields', $fields );

				return $fields;
	}

	/**
	 * Return sample orders
	 *
	 * @since      1.0.0
	 * @return     array     $_orders
	 */
	public function get_sample_orders() {
		$args    = array(
			'limit'   => 10,
			'orderby' => 'date',
			'order'   => 'DESC',
		);
		$orders  = wc_get_orders( $args );
		$_orders = array();
		if ( $orders ) {
			foreach ( $orders as $order ) {
				if ( is_a( $order, 'WC_Order' ) ) {
					$order_id = $order->get_id();
					$buyer    = $order_id;
					if ( $order->get_billing_first_name() || $order->get_billing_last_name() ) {
						/* translators: 1: first name 2: last name */
						$buyer = trim( sprintf( esc_html_x( '%1$s %2$s', 'full name', 'automatic-order-printing-for-woocommerce' ), $order->get_billing_first_name(), $order->get_billing_last_name() ) );
					} elseif ( $order->get_billing_company() ) {
						$buyer = trim( $order->get_billing_company() );
					} elseif ( $order->get_customer_id() ) {
						$user  = get_user_by( 'id', $order->get_customer_id() );
						$buyer = ucwords( $user->display_name );
					}
					$buyer                = apply_filters( 'woocommerce_admin_order_buyer_name', $buyer, $order );
					$_orders[ $order_id ] = '#' . esc_attr( $order->get_order_number() ) . ' ' . esc_html( $buyer );
				}
			}
		}
		return $_orders;
	}

	/**
	 * Callback function for order status field display.
	 *
	 * @since    1.0.0
	 * @param      object $field   The field object.
	 * @param      array  $value    Field value.
	 * @param      string $object_id  Object id.
	 * @param      string $object_type Object type.
	 * @param      string $field_type  Field type.
	 */
	public function render_order_status_callback( $field, $value, $object_id, $object_type, $field_type ) {
		$value = wp_parse_args(
			$value,
			array(
				'from' => '',
				'to'   => '',
			)
		);
		?>
		<div>
			<?php
			echo esc_html__( 'Order status from &nbsp;&nbsp;', 'pcss-woo-order-delivery-details' );
			echo wp_kses(
				$field_type->select( // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
					array(
						'name'        => $field_type->_name( '[from]' ),
						'id'          => $field_type->_id( '_from' ),
						'value'       => esc_attr( $value['from'] ),
						'desc'        => '',
						'options'     => $this->cmb2_select_options( $field->args['options'], $value['from'] ),
						'placeholder' => esc_html__( 'Status from', 'pcss-woo-order-delivery-details' ),
					)
				),
				array(
					'select' => array(
						'option'        => array(),
						'name'          => array(),
						'id'            => array(),
						'value'         => array(),
						'class'         => array(),
						'data-iterator' => array(),
						'autocomplete'  => array(),
						'placeholder'   => array(),
						'options'       => array(),
						'data-hash'     => array(),
					),
					'option' => array(
						'value'    => array(),
						'selected' => array(),
					),
				)
			);
			echo esc_html__( ' &nbsp;&nbsp; To  &nbsp;&nbsp;', 'pcss-woo-order-delivery-details' );
			echo wp_kses(
				$field_type->select( // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
					array(
						'name'        => $field_type->_name( '[to]' ),
						'id'          => $field_type->_id( '_to' ),
						'value'       => esc_attr( $value['to'] ),
						'desc'        => '',
						'options'     => $this->cmb2_select_options( $field->args['options'], $value['to'] ),
						'placeholder' => esc_html__( 'Status to', 'pcss-woo-order-delivery-details' ),
					)
				),
				array(
					'select' => array(
						'option'        => array(),
						'name'          => array(),
						'id'            => array(),
						'value'         => array(),
						'class'         => array(),
						'data-iterator' => array(),
						'autocomplete'  => array(),
						'placeholder'   => array(),
						'options'       => array(),
						'data-hash'     => array(),
					),
					'option' => array(
						'value'    => array(),
						'selected' => array(),
					),
				)
			);
			?>
		</div>
		<?php
	}

	/**
	 * Sanitize order status option.
	 *
	 * @since    1.0.0
	 * @param      string $check      Default value.
	 * @param      array  $meta_value    Field Value.
	 * @param      string $object_id     Object id.
	 * @param      array  $field_args    Field args.
	 * @param      object $sanitize_object Sanitize Object.
	 * @return     array    $meta_value   Meta value.
	 */
	public function sanitize_order_status( $check, $meta_value, $object_id, $field_args, $sanitize_object ) {

		// if not repeatable, bail out.
		if ( ! is_array( $meta_value ) || ! $field_args['repeatable'] ) {
			return $check;
		}

		foreach ( $meta_value as $key => $val ) {
			$meta_value[ $key ] = array_filter( array_map( 'sanitize_text_field', $val ) );
		}

		return array_filter( $meta_value );
	}


	/**
	 * Escape order status option.
	 *
	 * @since    1.0.0
	 * @param      string $check      Default value.
	 * @param      array  $meta_value    Field Value.
	 * @param      array  $field_args    Field args.
	 * @param      object $field_object field Object.
	 * @return     array    $meta_value   Meta value.
	 */
	public function escape_order_status( $check, $meta_value, $field_args, $field_object ) {
		// if not repeatable, bail out.
		if ( ! is_array( $meta_value ) || ! $field_args['repeatable'] ) {
			return $check;
		}

		foreach ( $meta_value as $key => $val ) {
			$meta_value[ $key ] = array_filter( array_map( 'esc_attr', $val ) );
		}

		return array_filter( $meta_value );
	}

	/**
	 * Return select options.
	 *
	 * @since    1.0.0
	 * @param      array  $options    Field Options.
	 * @param      string $value    Field value.
	 * @return     string    $options_string   Options.
	 */
	public function cmb2_select_options( $options, $value ) {
		$options_string = '<option value="">' . esc_html__( 'Select Status', 'pcss-woo-order-delivery-details' ) . '</option>';
		if ( ! empty( $options ) ) {
			foreach ( $options as $k => $v ) {
				$options_string .= '<option value="' . $k . '" ' . selected( $value, $k, false ) . '>' . $v . '</option>';
			}
		}
		return $options_string;
	}


	/**
	 * Callback function for order status field display.
	 *
	 * @since    1.0.0
	 * @param      object $field   The field object.
	 * @param      array  $value    Field value.
	 * @param      string $object_id  Object id.
	 * @param      string $object_type Object type.
	 * @param      string $field_type  Field type.
	 */
	public function render_preview_template_callback( $field, $value, $object_id, $object_type, $field_type ) {
		$sample_order = get_post_meta( $object_id, 'sample_order', true );
		$order_id     = '';
		if ( ! empty( $sample_order ) && get_post_type( $sample_order ) == 'shop_order' ) {
			$order_id = $sample_order;
		} else {
			$orders = $this->get_sample_orders();
			if ( ! empty( $orders ) ) {
				$orders   = array_keys( $orders );
				$order_id = $orders[0];
			}
		}

		if ( ! empty( $order_id ) ) {

			$url              = wp_nonce_url( admin_url( 'admin-ajax.php?action=woo-order-auto-print&task=preview&order_id=' . ( $order_id ) . '&template_id=' . $object_id ), 'automatic-order-printing-for-woocommerce' );
						$url1 = wp_nonce_url( admin_url( 'admin-ajax.php?action=woo-order-auto-print&task=preview_html&order_id=' . ( $order_id ) . '&template_id=' . $object_id ), 'automatic-order-printing-for-woocommerce' );

			?>
					<a class="button" target="_blank" href="<?php echo esc_url( $url ); ?>"><?php echo esc_html__( 'Preview Template', 'automatic-order-printing-for-woocommerce' ); ?></a>
										
										<a class="button" target="_blank" href="<?php echo esc_url( $url1 ); ?>"><?php echo esc_html__( 'Preview Template as HTML', 'automatic-order-printing-for-woocommerce' ); ?></a>
				<?php
		}

	}

	/**
	 * Define which columns to show on this screen.
	 *
	 * @param array $columns Existing columns.
	 * @return array
	 */
	public function define_columns( $columns ) {
		$new_columns = array();
		foreach ( $columns as $key => $val ) {
			$new_columns[ $key ] = $val;
			if ( 'title' == $key ) {
				$new_columns['status']  = esc_html__( 'Status', 'automatic-order-printing-for-woocommerce' );
				$new_columns['printer'] = esc_html__( 'Printer', 'automatic-order-printing-for-woocommerce' );
			}
		}
		return $new_columns;
	}

	/**
	 * Render individual columns.
	 *
	 * @param string $column Column ID to render.
	 * @param int    $post_id Post ID being shown.
	 */
	public function render_columns( $column, $post_id ) {
		if ( 'status' == $column ) {
			$enable = get_post_meta( $post_id, 'enable', true );
			if ( 'on' == $enable ) {
				echo esc_html__( 'Enabled', 'automatic-order-printing-for-woocommerce' );
			} else {
				echo esc_html__( 'Disabled', 'automatic-order-printing-for-woocommerce' );
			}
		}
		if ( 'printer' == $column ) {
			$printer_id = get_post_meta( $post_id, 'printer', true );
			if ( ! empty( $printer_id ) ) {
				$papers = get_post_meta( $post_id, "printer_{$printer_id}_papers", true );
				foreach ( $this->printers as $printer ) {
					if ( $printer_id == $printer->id ) {
						echo esc_html( sprintf( '%s : %s', $printer->name, $papers ) );

					}
				}
			}
		}
	}

	/**
	 * Delete transient
	 *
	 * @since      1.0.0
	 * @param  int $post_id Post id.
	 */
	public function maybe_delete_transient( $post_id ) {
		if ( 'auto-print-templates' == get_post_type( $post_id ) ) {
			delete_transient( 'get_woo_order_auto_print_templates' );
		}
	}

	/**
	 * Run on save post
	 *
	 * @since      1.0.0
	 * @param  int    $post_id Post id.
	 * @param  object $post Post object.
	 * @param  string $update Update.
	 */
	public function save_post( $post_id, $post, $update ) {
		if ( ! $post_id ) {
			return;
		}
		$this->maybe_delete_transient( $post_id );
	}


	/**
	 * Run on Delete post
	 *
	 * @since      1.0.0
	 * @param  int $post_id Post id.
	 */
	public function delete_post( $post_id ) {
		if ( ! $post_id ) {
			return;
		}
		$this->maybe_delete_transient( $post_id );
	}

	/**
	 * Run on trash post
	 *
	 * @since      1.0.0
	 * @param  int $post_id Post id.
	 */
	public function trash_post( $post_id ) {
		if ( ! $post_id ) {
			return;
		}
		$this->maybe_delete_transient( $post_id );
	}

	/**
	 * Run on untrash post
	 *
	 * @since      1.0.0
	 * @param  int $post_id Post id.
	 */
	public function untrash_post( $post_id ) {
		if ( ! $post_id ) {
			return;
		}
		$this->maybe_delete_transient( $post_id );
	}
}
