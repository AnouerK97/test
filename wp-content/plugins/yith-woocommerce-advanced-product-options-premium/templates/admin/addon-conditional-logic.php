<?php
/**
 * Addon Conditional Logic Template
 *
 * @author  Corrado Porzio <corradoporzio@gmail.com>
 * @package YITH\ProductAddOns
 * @version 2.0.0
 *
 * @var int $block_id
 * @var int $addon_id
 * @var YITH_WAPO_Addon $addon
 */

defined( 'YITH_WAPO' ) || exit; // Exit if accessed directly.

$conditional_array        = array( 0 => '-' );
$addons_array             = array( 'addons' => array(
		'label'   => esc_html__( 'Add-ons', 'yith-woocommerce-product-add-ons' ),
		'options' => array(),
	), );
$variations_array         = array( 'variations' => array(
	'label'   => esc_html__( 'Variations', 'yith-woocommerce-product-add-ons' ),
	'options' => array(),
), );

$conditional_addons       = yith_wapo_get_addons_by_block_id( $block_id );
$total_conditional_addons = count( $conditional_addons );
if ( $total_conditional_addons > 0 ) {
	$conditional_array = array_merge( $conditional_array, $addons_array );
	foreach ( $conditional_addons as $key => $conditional_addon ) {
		if ( $conditional_addon->id !== $addon_id ) {
			$conditional_array['addons']['options'][ $conditional_addon->id ] = $conditional_addon->get_setting( 'title' );
			$options_total = is_array( $conditional_addon->options ) && isset( array_values( $conditional_addon->options )[0] ) ? count( array_values( $conditional_addon->options )[0] ) : 1;
			for ( $x = 0; $x < $options_total; $x++ ) {
				if ( isset( $conditional_addon->options['label'][ $x ] ) ) {
					$option_name = $conditional_addon->options['label'][ $x ];
					if ( apply_filters( 'yith_wapo_reduce_conditional_option_name', true ) && strlen( $option_name ) > 25 ) {
						$option_name = substr( $option_name, 0, 22 ) . '...';
					}
					$conditional_array['addons']['options'][ $conditional_addon->id . '-' . $x ] = $conditional_addon->get_setting( 'title' ) . ' - ' . $option_name;
				}
			}
		}
	}
}

// Get variations.
if ( apply_filters('yith_wapo_include_variations_on_conditional_logic', true ) ) {

	$selected_products            = array();
	$selected_categories          = array();
	$original_selected_products   = ! empty( $block->get_rule( 'show_in_products' ) ) ? (array) $block->get_rule( 'show_in_products' ) : array();
	$original_selected_categories = ! empty( $block->get_rule( 'show_in_categories' ) ) ? (array) $block->get_rule( 'show_in_categories' ) : array();
	$has_categories               = 0;

	if ( 'products' === $show_in && ! empty( $original_selected_products ) ) {
		$selected_products = $original_selected_products;
		foreach ( $selected_products as $index => $product_id ) {
			$product = wc_get_product( $product_id );
			if ( $product instanceof WC_Product_Variation ) {
				$selected_products[ $index ] = $product->get_parent_id();
			}
		}
	}
	if ( 'products' === $show_in && ! empty( $original_selected_categories ) ) {
		foreach ( $original_selected_categories as $index => $category_id ) {
			$category = get_term( $category_id, 'product_cat' );
			if ( is_object( $category ) ) {
				$selected_categories[ $index ] = ! empty( $category->slug ) ? $category->slug : '';
			}
		}
	}
	$args         = array(
		'type'     => 'variable',
		'status'   => 'publish',
		'orderby'  => 'name',
		'order'    => 'ASC',
		'limit'    => apply_filters( 'yith_wapo_conditional_logic_variations_limit', 20 ),
		'include'  => $selected_products,
		'category' => $selected_categories,
	);
	$all_products = wc_get_products( $args );

	if ( $all_products ) {
		if ( ! empty( $all_products ) ) {
			$conditional_array = array_merge( $conditional_array, $variations_array );
			foreach ( $all_products as $key => $product ) {
				if ( $product->get_type() === 'variable' ) {
					$variations    = $product->get_available_variations();
					$variations_id = wp_list_pluck( $variations, 'variation_id' );
					foreach ( $variations_id as $vkey => $variation_id ) {
						$variation = wc_get_product( $variation_id );
						if ( $variation instanceof WC_Product_Variation ) {
							$variable = wc_get_product( $variation->get_parent_id() );
							if ( $variable ) {
								$variable_cat   = $variable->get_category_ids();
								$has_categories = count( array_intersect( $original_selected_categories, $variable_cat ) );

								if ( 'all' === $show_in || 'products' === $show_in
									&& ( ( ! empty( $original_selected_products ) || ! empty( $selected_categories ) )
									|| in_array( strval( $variation_id ), $original_selected_products, true )
									|| $has_categories > 0 ) ) {
									$conditional_array[ 'v-' . $product->get_id() . '-' . $variation_id ] =
										'[ ' . esc_html__( 'Variation', 'yith-woocommerce-product-add-ons' ) . ' ] '
										. wp_strip_all_tags( $variation->get_formatted_name() );
								}
							}
						}
					}
				}
			}
		}
	}
}
?>

<div id="tab-conditional-logic" style="display: none;">

	<!-- Option field -->
	<div class="field-wrap">
		<label for="addon-enable-rules"><?php echo esc_html__( 'Set conditions to show or hide this set of options', 'yith-woocommerce-product-add-ons' ); ?></label>
		<div class="field">
			<?php
			yith_plugin_fw_get_field(
				array(
					'id'    => 'addon-enable-rules',
					'name'  => 'addon_enable_rules',
					'class' => 'enabler',
					'type'  => 'onoff',
					'value' => $addon->get_setting( 'enable_rules', 'no' ),
				),
				true
			);
			?>
			<span class="description">
				<?php echo esc_html__( 'Enable to set rules to hide or show the options.', 'yith-woocommerce-product-add-ons' ); ?>
			</span>
		</div>
	</div>
	<!-- End option field -->

	<!-- Option field -->
	<div class="field-wrap enabled-by-addon-enable-rules" style="display: none;">
		<label for="conditional-logic-rules"><?php echo esc_html__( 'Display rules', 'yith-woocommerce-product-add-ons' ); ?></label>

		<div id="conditional-display-rules">
			<div class="field display-rules">
				<?php
				yith_plugin_fw_get_field(
					array(
						'id'      => 'addon-conditional-logic-display',
						'name'    => 'addon_conditional_logic_display',
						'type'    => 'select',
						'class'   => 'wc-enhanced-select',
						'value'   => $addon->get_setting( 'conditional_logic_display', 'show' ),
						'options' => array(
							'show' => esc_html__( 'Show', 'yith-woocommerce-product-add-ons' ),
							'hide' => esc_html__( 'Hide', 'yith-woocommerce-product-add-ons' ),
						),
					),
					true
				);
				?>
				<span><?php echo esc_html__( 'this set of options if', 'yith-woocommerce-product-add-ons' ); ?></span>
				<?php
				yith_plugin_fw_get_field(
					array(
						'id'      => 'addon-conditional-logic-display-if',
						'name'    => 'addon_conditional_logic_display_if',
						'type'    => 'select',
						'class'   => 'wc-enhanced-select',
						'value'   => $addon->get_setting( 'conditional_logic_display_if', 'all' ),
						'options' => array(
							'all' => esc_html__( 'All of these rules', 'yith-woocommerce-product-add-ons' ),
							'any' => esc_html__( 'Any of these rules', 'yith-woocommerce-product-add-ons' ),
						),
					),
					true
				);
				?>
				<span><?php echo esc_html__( 'match', 'yith-woocommerce-product-add-ons' ); ?>:</span>
			</div>
		</div>

		<div id="conditional-rules">
			<?php
				$conditional_rule_addon  = (array) $addon->get_setting( 'conditional_rule_addon' );
				$conditional_rules_count = count( $conditional_rule_addon );
			for ( $y = 0; $y < $conditional_rules_count; $y++ ) :
				$conditional_rule = isset( $conditional_rule_addon[ $y ] ) ? $conditional_rule_addon[ $y ] : '';
				?>
				<div class="field rule">
				<?php
					yith_plugin_fw_get_field(
						array(
							'id'      => 'addon-conditional-rule-addon',
							'name'    => 'addon_conditional_rule_addon[]',
							'type'    => 'select',
							'class'   => 'wc-enhanced-select addon-conditional-rule-addon',
							'value'   => $conditional_rule,
							'options' => $conditional_array,
						),
						true
					);
				?>
					<span><?php echo esc_html__( 'is', 'yith-woocommerce-product-add-ons' ); ?></span>
					<?php
					yith_plugin_fw_get_field(
						array(
							'id'      => 'addon-conditional-rule-addon-is',
							'name'    => 'addon_conditional_rule_addon_is[]',
							'class'   => 'wc-enhanced-select addon-conditional-rule-addon-is',
							'type'    => 'select',
							'class'   => 'wc-enhanced-select addon-conditional-rule-addon-is',
							'value'   => isset( $addon->get_setting( 'conditional_rule_addon_is' )[ $y ] ) ? $addon->get_setting( 'conditional_rule_addon_is' )[ $y ] : '',
							'options' => array(
								''             => '-',
								'selected'     => esc_html__( 'Selected', 'yith-woocommerce-product-add-ons' ),
								'not-selected' => esc_html__( 'Not selected', 'yith-woocommerce-product-add-ons' ),
								'empty'        => esc_html__( 'Empty', 'yith-woocommerce-product-add-ons' ),
								'not-empty'    => esc_html__( 'Not empty', 'yith-woocommerce-product-add-ons' ),
							),
						),
						true
					);
					?>
					<img src="<?php echo esc_attr( YITH_WAPO_URL ); ?>/assets/img/delete.png" class="delete-rule" style="width: 8px; height: 10px; padding: 0px 10px; cursor: pointer;">
				</div>
			<?php endfor; ?>
			<div id="add-conditional-rule"><a href="#">+ <?php echo esc_html__( 'Add rule', 'yith-woocommerce-product-add-ons' ); ?></a></div>
		</div>

	</div>
	<!-- End option field -->

	<script type="text/javascript">
		jQuery('#add-conditional-rule a').click( function() {
			var ruleTemplate = jQuery('#conditional-rules .field.rule:first-child');
			var clonedOption = ruleTemplate.clone( false );
			clonedOption.find('input[type=number]').val('');
			clonedOption.insertBefore('#add-conditional-rule');
			clonedOption.find('.select2').remove();
			clonedOption.find('.addon-conditional-rule-addon').select2({
			   width: '200px',
			});
			clonedOption.find('.addon-conditional-rule-addon-is').select2({
				 width: '150px',
			});
		});
		jQuery('#conditional-rules').on( 'click', '.delete-rule', function() {
			jQuery(this).parent().remove();
		});
	</script>

</div>
