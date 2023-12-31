<?php
/**
 * WAPO Template
 *
 * @author  Corrado Porzio <corradoporzio@gmail.com>
 * @package YITH\ProductAddOns
 * @version 2.0.0
 */

defined( 'YITH_WAPO' ) || exit; // Exit if accessed directly.

$required      = $addon->get_option( 'required', $x ) === 'yes';
$number_limit  = $addon->get_option( 'number_limit', $x );

$minimum_value = '';
$maximum_value = '';

if ( 'yes' === $number_limit ){
	$minimum_value = $addon->get_option( 'number_limit_min', $x );
	$maximum_value = $addon->get_option( 'number_limit_max', $x );
}
?>

<div id="yith-wapo-option-<?php echo esc_attr( $addon->id ); ?>-<?php echo esc_attr( $x ); ?>" class="yith-wapo-option quantity">

	<?php if ( $addon->get_option( 'show_image', $x ) && $addon->get_option( 'image', $x ) !== '' && ! $hide_option_images && 'yes' !== $setting_hide_images ) : ?>
		<?php
			$post_id_image  = attachment_url_to_postid( $addon->get_option( 'image', $x ) );
			$alt_text_image = get_post_meta( $post_id_image, '_wp_attachment_image_alt', true );
		?>
		<div class="image position-<?php echo esc_attr( $addon_options_images_position ); ?>">
			<img src="<?php echo esc_attr( $addon->get_option( 'image', $x ) ); ?>" alt="<?php echo esc_attr( $alt_text_image ) ?>">
		</div>
	<?php endif; ?>

	<div class="label">
		<label for="yith-wapo-<?php echo esc_attr( $addon->id ); ?>-<?php echo esc_attr( $x ); ?>">

			<!-- LABEL -->
			<?php echo ! $hide_option_label ? wp_kses_post( $addon->get_option( 'label', $x ) ) : ''; ?>
			<?php echo $required ? '<span class="required">*</span>' : ''; ?>

			<!-- PRICE -->
			<?php echo ! $hide_option_prices ? wp_kses_post( $addon->get_option_price_html( $x ) ) : ''; ?>

		</label>
	</div>

	<!-- INPUT -->
	<input type="number"
		id="yith-wapo-<?php echo esc_attr( $addon->id ); ?>-<?php echo esc_attr( $x ); ?>"
		name="yith_wapo[][<?php echo esc_attr( $addon->id . '-' . $x ); ?>]"
		placeholder="0"
		<?php if ( 'yes' === $number_limit ) : ?>
			min="<?php echo esc_attr( $minimum_value ); ?>"
			max="<?php echo esc_attr( $maximum_value ); ?>"
		<?php endif; ?>
		data-price="<?php echo esc_attr( $price ); ?>"
		data-price-sale="<?php echo esc_attr( $price_sale ); ?>"
		data-price-type="<?php echo esc_attr( $price_type ); ?>"
		data-price-method="<?php echo esc_attr( $price_method ); ?>"
		data-first-free-enabled="<?php echo esc_attr( $addon->get_setting( 'first_options_selected', 'no' ) ); ?>"
		data-first-free-options="<?php echo esc_attr( $addon->get_setting( 'first_free_options', 0 ) ); ?>"
		data-addon-id="<?php echo esc_attr( $addon->id ); ?>"
		<?php echo $addon->get_option( 'required', $x ) === 'yes' ? 'required' : ''; ?>
		style="<?php echo esc_attr( $options_width_css ); ?>">

	<?php do_action( 'woocommerce_after_quantity_input_field' ); ?>

	<!-- REQUIRED -->
	<?php if ( $required ) : ?>
		<small class="required-error" style="color: #f00; padding: 5px 0px; display: none;"><?php echo esc_html__( 'This option is required.', 'yith-woocommerce-product-add-ons' ); ?></small>
	<?php endif; ?>

	<!-- TOOLTIP -->
	<?php if ( $addon->get_option( 'tooltip', $x ) !== '' ) : ?>
		<span class="tooltip position-<?php echo esc_attr( get_option( 'yith_wapo_tooltip_position' ) ); ?>" style="<?php echo esc_attr( $options_width_css ); ?>">
			<span><?php echo esc_attr( $addon->get_option( 'tooltip', $x ) ); ?></span>
		</span>
	<?php endif; ?>

	<!-- DESCRIPTION -->
	<?php if ( '' !== $option_description ) : ?>
		<p class="description">
			<?php echo wp_kses_post( $option_description ); ?>
		</p>
	<?php endif; ?>
	<!-- Sold individually -->
	<?php if( 'yes' === $sell_individually ): ?>
		<input type="hidden" name="yith_wapo_sell_individually[<?php echo esc_attr( $addon->id . '-' . $x ); ?>]" value="yes">
	<?php endif; ?>
</div>
