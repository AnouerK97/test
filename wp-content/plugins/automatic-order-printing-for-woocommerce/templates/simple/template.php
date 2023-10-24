<?php
/**
 * Template
 *
 * This template can be overridden by copying it to yourtheme/woo-order-auto-print/simple/template.php.
 *
 * @package    Woo_Order_Auto_Print
 * @subpackage Woo_Order_Auto_Print/templates
 */

do_action( 'woo_order_auto_print_before_document', $order, $order_id, $template_id, $template_settings );
?>
<div class="wocp-main">
	<div class="wocp-header">
	<?php
	do_action( 'woo_order_auto_print_order_header', $order, $order_id, $template_id, $template_settings );
	?>
			
	</div>
	<div class="wocp-top-data">
	<?php
	do_action( 'woo_order_auto_print_order_data', $order, $order_id, $template_id, $template_settings );
	?>
		
	</div>
	
	<?php
	if ( 'on' == $template_settings['product_details']['display_products_table'] ) {
		?>
	<div class="order-products-table">
		<?php
		do_action( 'woo_order_auto_print_order_products_table', $order, $order_id, $template_id, $template_settings );
		?>
	</div>
		<?php
	}
	?>
<div class="customer-notes">
	<?php
	do_action( 'woo_order_auto_print_before_customer_notes', $order, $order_id, $template_id, $template_settings );
	$shipping_notes = wpautop( wptexturize( $order->get_customer_note() ) );
	$settings       = get_woo_order_auto_print_settings();
	?>
	<?php if ( $shipping_notes ) : ?>
		<h3><?php echo esc_html( $settings['strings']['customer_notes'] ); ?></h3>
		<?php echo esc_html( strip_tags( $shipping_notes ) ); ?>
	<?php endif; ?>
	<?php do_action( 'woo_order_auto_print_after_customer_notes', $order, $order_id, $template_id, $template_settings ); ?>
</div>				    

<?php
if ( 'on' == $template_settings['footer']['display_footer'] ) {
	?>
		
<div class="document-footer">
	<?php do_action( 'woo_order_auto_print_document_footer', $order, $order_id, $template_id, $template_settings ); ?>
</div>
<?php } ?>    
</div>
