<?php
if (! defined('ABSPATH')) {
    exit;
}

$has_addons = ( ! empty($product_addons) && 0 < count($product_addons) ) ? 'wc-pao-has-addons' : '';
?>
<div id="product_addons_data" class="panel woocommerce_options_panel">
    <?php do_action('woocommerce_product_addons_panel_start'); ?>
    <div class="wc-pao-field-header">
        <h2><?php esc_html_e('Add-on fields', 'wc-frontend-manager-ultimate'); ?></h2>
        <p class="wc-pao-toolbar <?php echo esc_attr($has_addons); ?>">
            <a href="#" class="wc-pao-expand-all"><?php esc_html_e('Expand all', 'wc-frontend-manager-ultimate'); ?></a>&nbsp;/&nbsp;<a href="#" class="wc-pao-close-all"><?php esc_html_e('Close all', 'wc-frontend-manager-ultimate'); ?></a>
        </p>
    </div>
    <div class="wcfm-clearfix"></div>
    <p class="description"><?php _e('Add fields to get additional information from customers', 'wc-frontend-manager-ultimate'); ?></p>
    <div class="wcfm-clearfix"></div>

    <div class="wc-pao-addons <?php echo esc_attr($has_addons); ?>">
    <?php
    $loop = 0;
    foreach ($product_addons as $addon) {
        include 'html-addon.php';
        $loop++;
    }
    ?>
    </div>

    <div class="wc-pao-actions">
        <button type="button" class="button wc-pao-add-field"><?php esc_html_e('Add Field', 'wc-frontend-manager-ultimate'); ?></button>

        <div class="wc-pao-toolbar__import-export">
            <button type="button" class="button wc-pao-import-addons"><?php esc_html_e('Import', 'wc-frontend-manager-ultimate'); ?></button>
            <button type="button" class="button wc-pao-export-addons"><?php esc_html_e('Export', 'wc-frontend-manager-ultimate'); ?></button>
        </div>
    </div>
    <div class="wc-pao-import-export-container">
        <textarea name="export_product_addon" class="wc-pao-export-field" cols="20" rows="5" readonly="readonly"><?php echo esc_textarea(serialize($product_addons)); ?></textarea>

        <textarea name="import_product_addon" class="wc-pao-import-field" cols="20" rows="5" placeholder="<?php esc_attr_e('Paste exported form data here and then save to import fields. The imported fields will be appended.', 'wc-frontend-manager-ultimate'); ?>"></textarea>
    </div>
    <?php if ($exists) : ?>
        <div class="wc-pao-product-global-addon">
            <p>
            <label for="_product_addons_exclude_global"><?php esc_html_e('Exclude add-ons', 'wc-frontend-manager-ultimate'); ?>&nbsp;&nbsp;<input id="_product_addons_exclude_global" name="_product_addons_exclude_global" class="checkbox" type="checkbox" value="1" <?php checked($exclude_global, 1); ?>/></label>&nbsp;&nbsp;
            <em><?php esc_html_e('Hide additional add-ons that may apply to this product.', 'wc-frontend-manager-ultimate'); ?></em>
            </p>
        </div>
    <?php endif; ?>
</div>
