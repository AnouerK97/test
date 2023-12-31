<?php
/**
 * WAPO Template
 *
 * @author  Corrado Porzio <corradoporzio@gmail.com>
 * @package YITH\ProductAddOns
 * @version 2.0.0
 */

defined( 'YITH_WAPO' ) || exit; // Exit if accessed directly.

$required = $addon->get_option( 'required', $x ) === 'yes';
$checked  = $addon->get_option( 'default', $x ) === 'yes';

?>

<div id="yith-wapo-option-<?php echo esc_attr( $addon->id ); ?>-<?php echo esc_attr( $x ); ?>"
	class="yith-wapo-option selection-<?php echo esc_attr( $selection_type ); ?> <?php echo $checked ? 'selected' : ''; ?>"
	data-replace-image="<?php echo esc_attr( $image_replacement ); ?>">

	<label for="yith-wapo-<?php echo esc_attr( $addon->id ); ?>-<?php echo esc_attr( $x ); ?>">

		<!-- LEFT/ABOVE IMAGE -->
		<?php
		if ( 'left' === $addon_options_images_position || 'above' === $addon_options_images_position ) {
			include YITH_WAPO_DIR . '/templates/front/option-image.php'; }
		?>

		<span class="checkboxbutton <?php echo $checked ? 'checked' : ''; ?>">

			<!-- INPUT -->
			<input type="checkbox"
				id="yith-wapo-<?php echo esc_attr( $addon->id ); ?>-<?php echo esc_attr( $x ); ?>"
				name="yith_wapo[][<?php echo esc_attr( $addon->id . '-' . $x ); ?>]"
				value="<?php echo esc_attr( $addon->get_option( 'label', $x ) ); ?>"
				data-price="<?php echo esc_attr( $price ); ?>"
				data-price-sale="<?php echo esc_attr( $price_sale ); ?>"
				data-price-type="<?php echo esc_attr( $price_type ); ?>"
				data-price-method="<?php echo esc_attr( $price_method ); ?>"
				data-first-free-enabled="<?php echo esc_attr( $addon->get_setting( 'first_options_selected', 'no' ) ); ?>"
				data-first-free-options="<?php echo esc_attr( $addon->get_setting( 'first_free_options', 0 ) ); ?>"
				data-addon-id="<?php echo esc_attr( $addon->id ); ?>"
				<?php echo $required ? 'required' : ''; ?>
				<?php echo $checked ? 'checked="checked"' : ''; ?>>

		</span>

		<!-- RIGHT IMAGE -->
		<?php
		if ( 'right' === $addon_options_images_position ) {
			include YITH_WAPO_DIR . '/templates/front/option-image.php'; }
		?>

		<!-- LABEL -->
		<span class="yith-wapo-addon-label">
			<?php echo ! $hide_option_label ? wp_kses_post( $addon->get_option( 'label', $x ) ) : ''; ?>
				<?php echo $required ? '<span class="required">*</span>' : ''; ?>

			<!-- PRICE -->
			<?php echo ! $hide_option_prices ? wp_kses_post( $addon->get_option_price_html( $x ) ) : ''; ?>
		</span>

		<?php if ( $required ) : ?>
			<small class="required-error" style="color: #f00; padding: 5px 0px; display: none;"><?php echo esc_html__( 'This option is required.', 'yith-woocommerce-product-add-ons' ); ?></small>
		<?php endif; ?>

	</label>

	<!-- UNDER IMAGE -->
	<?php
	if ( 'under' === $addon_options_images_position ) {
		include YITH_WAPO_DIR . '/templates/front/option-image.php'; }
	?>

	<!-- DESCRIPTION -->
	<?php if ( '' !== $option_description ) : ?>
		<p class="description"><?php echo wp_kses_post( $option_description ); ?></p>
	<?php endif; ?>

	<!-- TOOLTIP -->
	<?php if ( 'yes' === get_option( 'yith_wapo_show_tooltips' ) && '' !== $addon->get_option( 'tooltip', $x ) ) : ?>
		<span class="tooltip position-<?php echo esc_attr( get_option( 'yith_wapo_tooltip_position' ) ); ?>" style="width: 100%">
			<span><?php echo wp_kses_post( $addon->get_option( 'tooltip', $x ) ); ?></span>
		</span>
	<?php endif; ?>
	<!-- Sold individually -->
	<?php if( 'yes' === $sell_individually ): ?>
		<input type="hidden" name="yith_wapo_sell_individually[<?php echo esc_attr( $addon->id . '-' . $x ); ?>]" value="yes">
	<?php endif; ?>
</div>