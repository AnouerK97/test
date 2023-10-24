<?php
/**
 * WAPO Template
 *
 * @author  Corrado Porzio <corradoporzio@gmail.com>
 * @package YITH\ProductAddOns
 * @version 2.0.0
 */

defined( 'YITH_WAPO' ) || exit; // Exit if accessed directly.

$required   = $addon->get_option( 'required', $x ) === 'yes';
$checked    = $addon->get_option( 'default', $x ) === 'yes';
$selected   = $checked ? 'selected' : '';
$color_type = $addon->get_option( 'color_type', $x, 'single' );

?>

<div id="yith-wapo-option-<?php echo esc_attr( $addon->id ); ?>-<?php echo esc_attr( $x ); ?>"
	class="yith-wapo-option selection-<?php echo esc_attr( $selection_type ); ?> <?php echo esc_attr( $selected ); ?>"
	data-replace-image="<?php echo esc_attr( $image_replacement ); ?>">

	<input type="checkbox"
		id="yith-wapo-<?php echo esc_attr( $addon->id ); ?>-<?php echo esc_attr( $x ); ?>"
		class="yith-proteo-standard-checkbox"
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
		<?php echo $checked ? 'checked="checked"' : ''; ?>
		style="display: none;">

	<!-- UNDER IMAGE -->
	<?php
	if ( 'above' === $addon_options_images_position || 'left' === $addon_options_images_position ) {
		include YITH_WAPO_DIR . '/templates/front/option-image.php'; }
	?>

	<!-- LABEL -->
	<label for="yith-wapo-<?php echo esc_attr( $addon->id ); ?>-<?php echo esc_attr( $x ); ?>">
		<span class="color" style="background: 
		<?php
		if ( 'double' === $color_type ) {
			echo '-webkit-linear-gradient( left, ' . esc_attr( $addon->get_option( 'color', $x ) ) . ' 50%, ' . esc_attr( $addon->get_option( 'color_b', $x ) ) . ' 50%)';
		} else {
			echo esc_attr( $addon->get_option( 'color', $x ) ); }
		?>
			;">
			<?php
			if ( 'image' === $color_type ) {
				echo '<img src="' . esc_attr( $addon->get_option( 'color_image', $x ) ) . '">'; }
			?>
		</span>
		<small><?php echo ! $hide_option_label ? wp_kses_post( $addon->get_option( 'label', $x ) ) : ''; ?></small>
		<?php echo ! $hide_option_prices ? wp_kses_post( $addon->get_option_price_html( $x ) ) : ''; ?>
	</label>

	<!-- REQUIRED -->
	<?php if ( $required ) : ?>
		<small class="required-error" style="color: #f00; padding: 5px 0px; display: none;">
			<?php echo esc_html__( 'This option is required.', 'yith-woocommerce-product-add-ons' ); ?>
		</small>
	<?php endif; ?>

	<!-- TOOLTIP -->
	<?php if ( 'yes' === get_option( 'yith_wapo_show_tooltips' ) && '' !== $addon->get_option( 'tooltip', $x ) ) : ?>
		<span class="tooltip position-<?php echo esc_attr( get_option( 'yith_wapo_tooltip_position' ) ); ?>" style="width: 100%;">
			<span><?php echo wp_kses_post( $addon->get_option( 'tooltip', $x ) ); ?></span>
		</span>
	<?php endif; ?>

	<!-- UNDER IMAGE -->
	<?php
	if ( 'under' === $addon_options_images_position || 'right' === $addon_options_images_position ) {
		include YITH_WAPO_DIR . '/templates/front/option-image.php'; }
	?>

	<!-- DESCRIPTION -->
	<?php if ( '' !== $option_description ) : ?>
		<p class="description"><?php echo wp_kses_post( $option_description ); ?></p>
	<?php endif; ?>
	<!-- Sold individually -->
	<?php if( 'yes' === $sell_individually ): ?>
		<input type="hidden" name="yith_wapo_sell_individually[<?php echo esc_attr( $addon->id . '-' . $x ); ?>]" value="yes">
	<?php endif; ?>
</div>
