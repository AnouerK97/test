<?php
if (! defined('ABSPATH')) {
    exit;
}

global $post, $wp;

if (isset($wp->query_vars['wcfm-products-manage']) && ! empty($wp->query_vars['wcfm-products-manage'])) {
    $product_id = $wp->query_vars['wcfm-products-manage'];
    $post       = get_post($product_id);
}

$addon_title          = ! empty($addon['name']) ? $addon['name'] : '';
$title_format         = ! empty($addon['title_format']) ? $addon['title_format'] : '';
$addon_type           = ! empty($addon['type']) ? $addon['type'] : 'multiple_choice';
$addon_type_formatted = ! empty($addon_type) ? $this->convert_type_name($addon_type) : __('Multiple choice', 'wc-frontend-manager-ultimate');
$display              = ! empty($addon['display']) ? $addon['display'] : '';
$restrictions         = ! empty($addon['restrictions']) ? $addon['restrictions'] : '';
$restrictions_type    = ! empty($addon['restrictions_type']) ? $addon['restrictions_type'] : '';
$description_enable   = ! empty($addon['description_enable']) ? $addon['description_enable'] : '';
$description          = ! empty($addon['description']) ? $addon['description'] : '';
$required             = ! empty($addon['required']) ? $addon['required'] : '';
$min                  = ! empty($addon['min']) ? $addon['min'] : '';
$max                  = ! empty($addon['max']) ? $addon['max'] : '';
$adjust_price         = ! empty($addon['adjust_price']) ? $addon['adjust_price'] : '';
$price_type           = ! empty($addon['price_type']) ? $addon['price_type'] : '';
$_price               = ! empty($addon['price']) ? $addon['price'] : '';
$show_heading_col     = 'show';
$display_option_rows_class       = 'show';
$display_non_option_rows_class   = 'hide';
$display_required_setting_class  = 'show';
$display_restrictions_select_col = 'hide';
$display_display_col             = 'show';
$decimal_separator               = wc_get_price_decimal_separator();

if ('multiple_choice' !== $addon_type && 'checkbox' !== $addon_type) {
    $display_option_rows_class     = 'hide';
    $display_non_option_rows_class = 'show';
}

if ('heading' === $addon_type) {
    $display_required_setting_class = 'hide';
    $display_non_option_rows_class  = 'hide';
    $display_restrictions           = 'hide';
}

if ('custom_text' === $addon_type) {
    $display_restrictions_select = 'show';
    $display_dipslay_col         = 'hide';
}

if ('heading' === $addon_type) {
    $show_heading_col = 'hide';
}
?>
<div class="wc-pao-addon closed">
    <div class="wc-pao-addon-header">
        <div class="wc-pao-col1">
            <span class="wc-pao-addon-sort-handle dashicons dashicons-menu"></span>
            <h2 class="wc-pao-addon-name"><?php echo esc_html($addon_title); ?></h2>
            <small class="wc-pao-addon-type"><?php echo esc_html($addon_type_formatted); ?></small>
        </div>

        <div class="wc-pao-col2">
            <button type="button" class="wc-pao-remove-addon button"><?php esc_html_e('Remove', 'wc-frontend-manager-ultimate'); ?></button>
            <span class="wc-pao-addon-toggle" title="<?php esc_attr_e('Click to toggle', 'wc-frontend-manager-ultimate'); ?>" aria-hidden="true"></span>
            <input type="hidden" name="product_addon_position[<?php echo esc_attr($loop); ?>]" class="wc-pao-addon-position" value="<?php echo esc_attr($loop); ?>" />
        </div>

        <?php do_action('woocommerce_product_addons_panel_option_heading', isset($post) ? $post : null, $addon, $loop); ?>
    </div>

    <div class="wc-pao-addon-content">
        <div class="wc-pao-addon-main-settings-1">
            <div class="wc-pao-col1">
                <label for="wc-pao-addon-content-type-<?php echo esc_attr($loop); ?>">
                    <?php
                    esc_html_e('Type', 'wc-frontend-manager-ultimate');
                    ?>
                </label>
                <select id="wc-pao-addon-content-type-<?php echo esc_attr($loop); ?>" name="product_addon_type[<?php echo esc_attr($loop); ?>]" class="wc-pao-addon-type-select wcfm-select">
                    <option <?php selected('multiple_choice', $addon_type); ?> value="multiple_choice"><?php esc_html_e('Multiple Choice', 'wc-frontend-manager-ultimate'); ?></option>
                    <option <?php selected('checkbox', $addon_type); ?> value="checkbox"><?php esc_html_e('Checkboxes', 'wc-frontend-manager-ultimate'); ?></option>
                    <option <?php selected('custom_text', $addon_type); ?> value="custom_text"><?php esc_html_e('Short Text', 'wc-frontend-manager-ultimate'); ?></option>
                    <option <?php selected('custom_textarea', $addon_type); ?> value="custom_textarea"><?php esc_html_e('Long Text', 'wc-frontend-manager-ultimate'); ?></option>
                    <option <?php selected('file_upload', $addon_type); ?> value="file_upload"><?php esc_html_e('File Upload', 'wc-frontend-manager-ultimate'); ?></option>
                    <option <?php selected('custom_price', $addon_type); ?> value="custom_price"><?php esc_html_e('Name Your Price', 'wc-frontend-manager-ultimate'); ?></option>
                    <option <?php selected('input_multiplier', $addon_type); ?> value="input_multiplier"><?php esc_html_e('Quantity', 'wc-frontend-manager-ultimate'); ?></option>
                    <option <?php selected('heading', $addon_type); ?> value="heading"><?php esc_html_e('Heading', 'wc-frontend-manager-ultimate'); ?></option>
                </select>
            </div>

            <div class="wc-pao-col2-1  <?php echo esc_attr($display_display_col); ?>">
                <label for="wc-pao-addon-content-display-<?php echo esc_attr($loop); ?>">
                    <?php
                    esc_html_e('Display as', 'wc-frontend-manager-ultimate');
                    ?>
                </label>
                <select id="wc-pao-addon-content-display-<?php echo esc_attr($loop); ?>" name="product_addon_display[<?php echo esc_attr($loop); ?>]" class="wc-pao-addon-display-select wcfm-select">
                    <option <?php selected('select', $display); ?> value="select"><?php esc_html_e('Dropdowns', 'wc-frontend-manager-ultimate'); ?></option>
                    <option <?php selected('radiobutton', $display); ?> value="radiobutton"><?php esc_html_e('Radio Buttons', 'wc-frontend-manager-ultimate'); ?></option>
                    <option <?php selected('images', $display); ?> value="images"><?php esc_html_e('Images', 'wc-frontend-manager-ultimate'); ?></option>
                </select>
            </div>

            <div class="wc-pao-col2-2  <?php echo esc_attr($display_restrictions_select_col); ?>">
                <label for="wc-pao-addon-content-restriction-<?php echo esc_attr($loop); ?>">
                    <?php
                    esc_html_e('Restriction', 'wc-frontend-manager-ultimate');
                    ?>
                </label>
                <select id="wc-pao-addon-content-restriction-<?php echo esc_attr($loop); ?>" name="product_addon_restrictions_type[<?php echo esc_attr($loop); ?>]" class="wc-pao-addon-restrictions-select wcfm-select">
                    <option <?php selected('any_text', $restrictions_type); ?> value="any_text"><?php esc_html_e('Any Text', 'wc-frontend-manager-ultimate'); ?></option>
                    <option <?php selected('only_letters', $restrictions_type); ?> value="only_letters"><?php esc_html_e('Only Letters', 'wc-frontend-manager-ultimate'); ?></option>
                    <option <?php selected('only_numbers', $restrictions_type); ?> value="only_numbers"><?php esc_html_e('Only Numbers', 'wc-frontend-manager-ultimate'); ?></option>
                    <option <?php selected('only_letters_numbers', $restrictions_type); ?> value="only_letters_numbers"><?php esc_html_e('Only Letters and Numbers', 'wc-frontend-manager-ultimate'); ?></option>
                    <option <?php selected('email', $restrictions_type); ?> value="email"><?php esc_html_e('Only Email Address', 'wc-frontend-manager-ultimate'); ?></option>
                </select>
            </div>
        </div>

        <div class="wc-pao-addon-main-settings-2">
            <div class="wc-pao-col1">
                <div class="wc-pao-addon-title">
                    <label for="wc-pao-addon-content-name-<?php echo esc_attr($loop); ?>">
                        <?php esc_html_e('Title', 'wc-frontend-manager-ultimate'); ?>
                    </label>
                    <input type="text" class="wc-pao-addon-content-name" id="wc-pao-addon-content-name-<?php echo esc_attr($loop); ?>" name="product_addon_name[<?php echo esc_attr($loop); ?>]" value="<?php echo esc_attr($addon_title); ?>" />
                </div>
            </div>

            <div class="wc-pao-col2 <?php echo esc_attr($show_heading_col); ?>">
                <label for="wc-pao-addon-content-title-format-<?php echo esc_attr($loop); ?>">
                    <?php
                    esc_html_e('Format title', 'wc-frontend-manager-ultimate');
                    ?>
                </label>
                <select id="wc-pao-addon-content-title-format" name="product_addon_title_format[<?php echo esc_attr($loop); ?>]" class="wcfm-select">
                    <option <?php selected('label', $title_format); ?> value="label"><?php esc_html_e('Label', 'wc-frontend-manager-ultimate'); ?></option>
                    <option <?php selected('heading', $title_format); ?> value="heading"><?php esc_html_e('Heading', 'wc-frontend-manager-ultimate'); ?></option>
                    <option <?php selected('hide', $title_format); ?> value="hide"><?php esc_html_e('Hide', 'wc-frontend-manager-ultimate'); ?></option>
                </select>
            </div>
        </div>

        <div class="wc-pao-addons-secondary-settings">
            <div class="wc-pao-row wc-pao-addon-description-setting">
                <label for="wc-pao-addon-description-enable-<?php echo esc_attr($loop); ?>">
                    <input type="checkbox" id="wc-pao-addon-description-enable-<?php echo esc_attr($loop); ?>" class="wc-pao-addon-description-enable wcfm-checkbox" name="product_addon_description_enable[<?php echo esc_attr($loop); ?>]" <?php checked($description_enable, 1); ?> />
                    <?php
                    esc_html_e('Add description', 'wc-frontend-manager-ultimate');

                        $display_description_box = ! empty($description_enable) ? 'show' : 'hide';
                    ?>
                </label>
                <textarea cols="20" id="wc-pao-addon-description-<?php echo esc_attr($loop); ?>" class="wc-pao-addon-description <?php echo esc_attr($display_description_box); ?>" rows="3" name="product_addon_description[<?php echo esc_attr($loop); ?>]"><?php echo esc_textarea($description); ?></textarea>
            </div>
            
            <div class="wc-pao-row wc-pao-addon-required-setting <?php echo esc_attr($display_required_setting_class); ?>">
                <label for="wc-pao-addon-required-<?php echo esc_attr($loop); ?>">
                    <input type="checkbox" id="wc-pao-addon-required-<?php echo esc_attr($loop); ?>" class="wcfm-checkbox" name="product_addon_required[<?php echo esc_attr($loop); ?>]" <?php checked($required, 1); ?> />
                    <?php esc_html_e('Required field', 'wc-frontend-manager-ultimate'); ?>
                </label>
            </div>
        </div>

        <?php do_action('woocommerce_product_addons_panel_before_options', $post, $addon, $loop); ?>

        <div class="wc-pao-addon-content-option-rows style_if_appointment <?php echo esc_attr($display_option_rows_class); ?>">
            <div class="wc-pao-addon-content-option-inner">
                <div class="wc-pao-addon-content-headers">
                    <div class="wc-pao-addon-content-option-header">
                        <?php esc_html_e('Option', 'wc-frontend-manager-ultimate'); ?>
                    </div>

                    <div class="wc-pao-addon-content-price-header">
                        <div class="wc-pao-addon-content-price-wrap">
                            <?php esc_html_e('Price', 'wc-frontend-manager-ultimate'); ?>
                            <?php echo wc_help_tip(__('Choose how to calculate price: apply a flat fee regardless of quantity, charge per quantity ordered, or charge a percentage of the total', 'wc-frontend-manager-ultimate')); ?>
                        </div>
                    </div>
                </div>

                <div class="wc-pao-addon-content-options-container">
                    <?php
                    if (! empty($addon['options'])) {
                        foreach ($addon['options'] as $option) {
                            include 'html-addon-option.php';
                        }
                    }
                    ?>
                </div>

                <div class="wc-pao-addon-content-footer">
                    <button type="button" class="wc-pao-add-option button"><?php esc_html_e('Add Option', 'wc-frontend-manager-ultimate'); ?></button>
                </div>
            </div>
        </div>

        <?php
        // Checks the addon type to determine restriction name.
        $display_restrictions = 'show';
        $display_adjust_price = 'show';
        $restriction_name     = __('Restrictions', 'wc-frontend-manager-ultimate');

        switch ($addon_type) {
            case 'checkbox':
                $display_adjust_price = 'hide';
                break;

            case 'custom_price':
                $restriction_name     = __('Limit price range', 'wc-frontend-manager-ultimate');
                $display_adjust_price = 'hide';
                break;

            case 'input_multiplier':
                $restriction_name = __('Limit quantity range', 'wc-frontend-manager-ultimate');
                break;

            case 'custom_text':
                $restriction_name = __('Limit character length', 'wc-frontend-manager-ultimate');
                break;

            case 'custom_textarea':
                $restriction_name = __('Limit character length', 'wc-frontend-manager-ultimate');
                break;

            default:
                $display_restrictions = 'hide';
                break;
        }//end switch
        ?>
        <div class="wc-pao-addon-content-non-option-rows <?php echo esc_attr($display_non_option_rows_class); ?>">
            <div class="wc-pao-row wc-pao-addon-restrictions-container <?php echo esc_attr($display_restrictions); ?>">
                <label for="wc-pao-addon-restrictions-<?php echo esc_attr($loop); ?>">
                    <input type="checkbox" id="wc-pao-addon-restrictions-<?php echo esc_attr($loop); ?>" class="wc-pao-addon-restrictions wcfm-checkbox" name="product_addon_restrictions[<?php echo esc_attr($loop); ?>]" <?php checked($restrictions, 1); ?> /><span class="wc-pao-addon-restriction-name">
                    <?php
                    esc_html($restriction_name);

                    $display_restrictions_settings = ! empty($restrictions) ? 'show' : 'hide';
                    $display_min_max               = ( ! empty($restrictions_type) && 'email' !== $restrictions_type ) ? 'show' : 'hide';
                    ?>
                    </span>
                </label>
                <div class="wc-pao-addon-restrictions-settings <?php echo esc_attr($display_restrictions_settings); ?>">
                    <div class="wc-pao-addon-min-max <?php echo esc_attr($display_min_max); ?>">
                        <input type="number" name="product_addon_min[<?php echo esc_attr($loop); ?>]" value="<?php echo esc_attr($min); ?>" placeholder="0" min="0" />&nbsp;<span>&mdash;</span>&nbsp;
                        <input type="number" name="product_addon_max[<?php echo esc_attr($loop); ?>]" value="<?php echo esc_attr($max); ?>" placeholder="999" min="0" />
                        &nbsp;<em><?php esc_html_e('Enter a minimum and maximum value for the limit range.', 'wc-frontend-manager-ultimate'); ?></em>
                    </div>
                </div>
            </div>

            <?php
            $display_adjust_price_settings = ! empty($adjust_price) ? 'show' : 'hide';
            ?>
            <div class="wc-pao-row wc-pao-addon-adjust-price-container <?php echo esc_attr($display_adjust_price); ?>">
                <label for="wc-pao-addon-adjust-price-<?php echo esc_attr($loop); ?>">
                    <input type="checkbox" id="wc-pao-addon-adjust-price-<?php echo esc_attr($loop); ?>" class="wc-pao-addon-adjust-price wcfm-checkbox" name="product_addon_adjust_price[<?php echo esc_attr($loop); ?>]" <?php checked($adjust_price, 1); ?> />
                    <?php
                    esc_html_e('Adjust price', 'wc-frontend-manager-ultimate');
                    echo wc_help_tip(__('Choose how to calculate price: apply a flat fee regardless of quantity, charge per quantity ordered, or charge a percentage of the total', 'wc-frontend-manager-ultimate'));
                    ?>
                </label>
                <div class="wc-pao-addon-adjust-price-settings <?php echo esc_attr($display_adjust_price_settings); ?>">
                    <select id="wc-pao-addon-adjust-price-select-<?php echo esc_attr($loop); ?>" name="product_addon_price_type[<?php echo esc_attr($loop); ?>]" class="wc-pao-addon-adjust-price-select wcfm-select">
                        <option <?php selected('flat_fee', $price_type); ?> value="flat_fee"><?php esc_html_e('Flat Fee', 'wc-frontend-manager-ultimate'); ?></option>
                        <option <?php selected('quantity_based', $price_type); ?> value="quantity_based"><?php esc_html_e('Quantity Based', 'wc-frontend-manager-ultimate'); ?></option>
                        <option <?php selected('percentage_based', $price_type); ?> value="percentage_based"><?php esc_html_e('Percentage Based', 'wc-frontend-manager-ultimate'); ?></option>
                    </select>

                    <input type="text" name="product_addon_price[<?php echo esc_attr($loop); ?>]" value="<?php echo esc_attr($_price); ?>" placeholder="0<?php echo esc_attr($decimal_separator); ?>00" class="wc-pao-addon-adjust-price-value" />
                </div>
            </div>
        </div>
    </div>
</div>
