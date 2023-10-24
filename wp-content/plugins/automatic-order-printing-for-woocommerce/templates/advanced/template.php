<?php
/**
 * Template
 *
 * This template can be overridden by copying it to yourtheme/woo-order-auto-print/advanced/template.php.
 *
 * @package    Woo_Order_Auto_Print
 * @subpackage Woo_Order_Auto_Print/templates
 */
$settings = get_woo_order_auto_print_settings();
do_action( 'woo_order_auto_print_before_document', $order, $order_id, $template_id, $template_settings );
?>
<div class="wocp-main">
	<div class="wocp-header">
		<?php
		if ( 'on' == $template_settings['store']['display_store_logo'] || 'on' == $template_settings['store']['display_document_name'] || 'on' == $template_settings['store']['display_from_Address'] ) {
			?>
			<table class="wocp-header-table">
				<tr>	
					<?php
					if ( 'on' == $template_settings['store']['display_store_logo'] || 'on' == $template_settings['store']['display_document_name'] ) {
						echo '<td class="store-logo">';
						if ( 'on' == $template_settings['store']['display_store_logo'] ) {
							$template_logo = $template_settings['store']['store_logo'];
							if ( empty( $template_logo ) ) {
								$template_logo = $settings['general']['store_logo'];
							}
							if ( ! empty( $template_logo ) ) {
								$image = wp_sprintf( '<img src="%s" class="header-store-logo" />', $template_logo );
								echo wp_kses_post( apply_filters( 'woo_order_auto_print_order_store_logo_image', $image, $order, $template_id, $template_settings ) );
							}
						}
						if ( 'on' == $template_settings['store']['display_document_name'] ) {
							$document_name = $template_settings['meta']['document_name'];
							echo wp_kses_post( apply_filters( 'woo_order_auto_print_order_document_name', wp_sprintf( '<h2>%s</h2>', $document_name ), $order, $template_id, $template_settings ) );
						}
						echo '</td>';
					}
					if ( in_array( $template_settings['meta']['pdf_size'], array( 'A7', 'A8' ) ) ) {
						echo '</tr><tr>';
					}
					if ( 'on' == $template_settings['store']['display_from_Address'] ) {
						echo '<td class="store-address">';
						$country_setting = ( isset( $settings['general']['country_state'] ) ) ? $settings['general']['country_state'] : '';
						$country         = '';
						$state           = '';
						if ( strstr( $country_setting, ':' ) ) {
							$country_setting = explode( ':', $country_setting );
							$country         = current( $country_setting );
							$state           = end( $country_setting );
						} else {
							$country = $country_setting;
						}

						$_address['company']   = $settings['general']['store_name'];
						$_address['address_1'] = $settings['general']['store_address'];
						$_address['address_2'] = $settings['general']['store_address_2'];
						$_address['city']      = $settings['general']['store_city'];
						$_address['state']     = $state;
						$_address['country']   = $country;
						$_address['postcode']  = $settings['general']['store_postcode'];

						$address = WC()->countries->get_formatted_address( $_address );
						$address = apply_filters( 'woo_order_auto_print_store_address_format', $address, $_address );
						?>
					<h3><?php echo esc_html( $settings['strings']['from_address'] ); ?></h3>
						<?php
						echo wp_kses_post( $address );
						echo '</td>';
					}
					?>
				</tr>
			</table>
			<?php
		}
		?>

	</div>
	<div class="wocp-top-data">
		<table class="wocp-top-data-table">
			<tr>
				<?php
				if ( 'on' == $template_settings['billing']['display_billing_address'] || 'on' == $template_settings['billing']['display_billing_email'] || 'on' == $template_settings['billing']['display_billing_phone'] ) {
					?>
					<td class="order-billing-address">
						<h3><?php echo esc_html( $settings['strings']['billing_address'] ); ?></h3>
						<?php
						do_action( 'woo_order_auto_print_before_billing_address', $order, $order_id, $template_id, $template_settings );
						if ( 'on' == $template_settings['billing']['display_billing_address'] ) {
							echo wp_kses_post( $order->get_formatted_billing_address() );
						}
						if ( 'on' == $template_settings['billing']['display_billing_email'] ) {
							echo wp_sprintf( '<p>%s: %s</p>', esc_html( $settings['strings']['email'] ), esc_html( $order->get_billing_email() ) );
						}
						if ( 'on' == $template_settings['billing']['display_billing_phone'] ) {
							echo wp_sprintf( '<p>%s: %s</p>', esc_html( $settings['strings']['tel'] ), esc_html( $order->get_billing_phone() ) );
						}
						do_action( 'woo_order_auto_print_after_billing_address', $order, $order_id, $template_id, $template_settings );
						?>

					</td>
					<?php
				}

				if ( in_array( $template_settings['meta']['pdf_size'], array( 'A6', 'A7', 'A8' ) ) ) {
					echo '</tr><tr>';
				}

				if ( 'on' == $template_settings['shipping']['display_shipping_address'] ) {
					?>
					<td class="order-shipping-address">
						<h3><?php echo esc_html( $settings['strings']['shipping_address'] ); ?></h3>
						<?php
						do_action( 'woo_order_auto_print_before_shipping_address', $order, $order_id, $template_id, $template_settings );
						echo wp_kses_post( $order->get_formatted_shipping_address() );
						do_action( 'woo_order_auto_print_after_shipping_address', $order, $order_id, $template_id, $template_settings );
						?>

					</td>
					<?php
					if ( in_array( $template_settings['meta']['pdf_size'], array( 'A6', 'A7', 'A8' ) ) ) {
						echo '</tr><tr>';
					}
				}
				if ( 'on' == $template_settings['order_details']['display_order_number'] ||
						'on' == $template_settings['order_details']['display_order_date'] ||
						'on' == $template_settings['order_details']['display_payment_method'] ||
						'on' == $template_settings['order_details']['display_shipping_method'] ||
						'on' == apply_filters( 'woo_order_auto_print_after_order_data_display', 'off', $order, $template_id, $template_settings ) ) {
					?>
					<td class="order-details">
						<ul>
							<?php
							do_action( 'woo_order_auto_print_before_order_data', $order, $order_id, $template_id, $template_settings );
							if ( 'on' == $template_settings['order_details']['display_order_number'] ) {
								?>
								<li class="order-number"><strong><?php echo esc_html( $settings['strings']['order_number'] ); ?></strong><?php echo esc_html( $order->get_order_number() ); ?></li>
								<?php
							}
							if ( 'on' == $template_settings['order_details']['display_order_date'] ) {
								$order_date  = $order->get_date_created();
								$date_format = apply_filters( 'woo_order_auto_print_date_format', get_option( 'date_format' ) );
								$date        = $order_date->date_i18n( $date_format );
								?>
								<li class="order-date"><strong><?php echo esc_html( $settings['strings']['order_date'] ); ?></strong><?php echo esc_html( $date ); ?></li>
								<?php
							}
							if ( 'on' == $template_settings['order_details']['display_payment_method'] ) {
								?>
								<li class="payment-method"><strong><?php echo esc_html( $settings['strings']['payment_method'] ); ?></strong><?php echo esc_html( $order->get_payment_method_title() ); ?></li>   
								<?php
							}
							if ( 'on' == $template_settings['order_details']['display_shipping_method'] ) {
								?>
								<li class="shipping-method"><strong><?php echo esc_html( $settings['strings']['shipping_method'] ); ?></strong><?php echo esc_html( $order->get_shipping_method() ); ?></li>   
								<?php
							}
							do_action( 'woo_order_auto_print_after_order_data', $order, $order_id, $template_id, $template_settings );
							?>
						</ul>
					</td>
					<?php
				}
				?>
			</tr>
		</table>


	</div>

	<?php
	if ( 'on' == $template_settings['product_details']['display_products_table'] ) {
		?>
		<div class="order-products-table">
			<?php
			$table_settings = $template_settings['product_details'];
			?>
			<table class="products-table">
				<thead class="products-table-head">
					<tr>
						<?php
						if ( 'on' == $table_settings['display_products_image'] ) {
							?>
							<th class="product-image"><?php echo esc_html( $settings['strings']['image'] ); ?></th>
						<?php } if ( 'on' == $table_settings['display_products_name'] ) { ?>
							<th class="product-title"><?php echo esc_html( $settings['strings']['product'] ); ?></th>
						<?php } if ( 'on' == $table_settings['display_products_sku'] ) { ?>
							<th class="product-sku"><?php echo esc_html( $settings['strings']['sku'] ); ?></th>
						<?php } if ( 'on' == $table_settings['display_products_qty'] ) { ?>
							<th class="product-qty"><?php echo esc_html( $settings['strings']['qty'] ); ?></th>
						<?php } if ( 'on' == $table_settings['display_products_price'] ) { ?>
							<th class="product-price"><?php echo esc_html( $settings['strings']['price'] ); ?></th>
						<?php } if ( 'on' == $table_settings['display_products_total'] ) { ?>
							<th class="product-total-price"><?php echo esc_html( $settings['strings']['total_price'] ); ?></th>
						<?php } ?>   
					</tr>
				</thead>
				<tbody>
					<?php
					foreach ( $order->get_items() as $item_id => $item ) {
						$product       = $item->get_product();
						$sku           = '';
						$purchase_note = '';
						$image         = '';

						if ( ! apply_filters( 'woocommerce_order_item_visible', true, $item ) ) {
							continue;
						}

						if ( is_object( $product ) ) {
							$sku           = $product->get_sku();
							$purchase_note = $product->get_purchase_note();
							$image         = $product->get_image();
						}
						?>
						<tr class="<?php echo esc_attr( apply_filters( 'woo_order_auto_print_order_item_class', 'order_item', $item, $order ) ); ?>">
							<?php if ( 'on' == $table_settings['display_products_image'] ) { ?>
								<td class="product-image">
									<?php
									echo wp_kses_post( apply_filters( 'woo_order_auto_print_order_item_thumbnail', $image, $item ) );
									?>
								</td>
							<?php } if ( 'on' == $table_settings['display_products_name'] ) { ?>
								<td class="product-title">
									<?php
									// Product name.
									echo wp_kses_post( apply_filters( 'woo_order_auto_print_order_item_name', $item->get_name(), $item ) );
									do_action( 'woo_order_auto_print_order_item_meta_start', $item_id, $item, $order );

									wc_display_item_meta(
										$item,
										array(
											'label_before' => '<strong class="wc-item-meta-label">',
										)
									);

									// allow other plugins to add additional product information here.
									do_action( 'woo_order_auto_print_order_item_meta_end', $item_id, $item, $order );
									?>
								</td>
							<?php } if ( 'on' == $table_settings['display_products_sku'] ) { ?>
								<td class="product-sku">
									<?php
									if ( $sku ) {
										echo wp_kses_post( $sku );
									}
									?>
								</td>
							<?php } if ( 'on' == $table_settings['display_products_qty'] ) { ?>
								<td class="product-qty">
									<?php
									$qty          = $item->get_quantity();
									$refunded_qty = $order->get_qty_refunded_for_item( $item_id );

									if ( $refunded_qty ) {
										$qty_display = '<del>' . esc_html( $qty ) . '</del> <ins>' . esc_html( $qty - ( $refunded_qty * -1 ) ) . '</ins>';
									} else {
										$qty_display = esc_html( $qty );
									}
									echo wp_kses_post( apply_filters( 'woo_order_auto_print_order_item_quantity', $qty_display, $item ) );
									?>
								</td>
							<?php } if ( 'on' == $table_settings['display_products_price'] ) { ?>
								<td class="product-price">
									<?php
									echo wp_kses_post( wc_price( $order->get_item_subtotal( $item, false, true ), array( 'currency' => $order->get_currency() ) ) ); // WPCS: input var ok, sanitization ok.
									?>
								</td>
							<?php } if ( 'on' == $table_settings['display_products_total'] ) { ?>
								<td class="product-total-price">
									<?php
									echo wp_kses_post( wc_price( $item->get_total(), array( 'currency' => $order->get_currency() ) ) ); // WPCS: input var ok, sanitization ok.
									?>
								</td>
							<?php } ?>
						</tr>
						<?php
					}
					?>
				</tbody>
			</table>
			<?php
			if ( 'on' == $table_settings['display_order_total'] ) {
				$order_item_totals         = $order->get_order_item_totals();
						$order_item_totals = apply_filters( 'woo_order_auto_print_order_totals', $order_item_totals, $order, $order_id, $template_id, $template_settings );
				?>
				<table class="otder-totals">
					<?php
					if ( $order_item_totals ) {
						foreach ( $order_item_totals as $key => $total ) {
							$label = $total['label'];
							$colon = strrpos( $label, ':' );
							if ( false !== $colon ) {
								$label = substr_replace( $label, '', $colon, 1 );
							}
							?>
							<tr class="<?php echo esc_attr( $key ); ?>">
								<th class="description"><?php echo esc_html($label); ?></th> <?php // @codingStandardsIgnoreLine ?>
								<td><?php echo wp_kses_post( $total['value'] ); ?></td> <?php // WPCS: input var ok, sanitization ok. ?>
							</tr>
							<?php
						}
					}
					?>
				</table>
				<?php
			}
			?>
		</div>
		<?php
	}
	?>
	<div class="customer-notes">
		<?php
		do_action( 'woo_order_auto_print_before_customer_notes', $order, $order_id, $template_id, $template_settings );
		$shipping_notes = wpautop( wptexturize( $order->get_customer_note() ) );
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
