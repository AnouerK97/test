<?php

function wcps_global_product_meta_box() {

    add_meta_box(
            'global_product', __('Global Product', 'wcps'), 'wcps_global_product_meta_box_callback', 'product', 'side', 'high'
    );
}

add_action('add_meta_boxes', 'wcps_global_product_meta_box');

function wcps_global_product_meta_box_callback() {
    $product_id = !empty($_GET['post']) ? $_GET['post'] : 0;
    $is_global = 'no';
    if ($product_id > 0) {
        $is_global = get_post_meta($product_id, 'wcps_is_global_product', TRUE);
        if (!$is_global) {
            $is_global = 'no';
        }
    }
    ?>
    <label for="wcps_is_global_product">
        <input type="checkbox" id="wcps_is_global_product" name="wcps_is_global_product" value="yes" <?php echo $is_global == 'yes' ? 'checked="checked"' : ''; ?>>Enable for global product
    </label>

    <?php
}

function wcps_save_gloabal_field($product_id) {
    $is_global = !empty($_POST['wcps_is_global_product']) ? 'yes' : 'no';
    update_post_meta($product_id, 'wcps_is_global_product', $is_global);
}

add_action('save_post_product', 'wcps_save_gloabal_field', 10, 1);

//wcfmmp_product_multivendor_clone
//wcfmmp_product_multivendor_bulk_clone

function wcps_is_cloned_products($user_id = 0) {
    $is_cloned = get_user_meta($user_id, 'is_cloned', true);
    if (!$is_cloned) {
        return 'no';
    }
    return $is_cloned;
}

function wcfmmp_admin_wcfm_vendor_settings_after_callback($vendor_id) {
    $is_cloned = wcps_is_cloned_products($vendor_id);
    if ($is_cloned == 'no') {
        $product_ids = wcps_get_global_products();
        if (!empty($product_ids)) {
            wcps_clone_products($product_ids, $vendor_id);
        }
        update_user_meta($vendor_id, 'is_cloned', 'yes');
    }
}

add_action('wcfmmp_admin_wcfm_vendor_settings_after', 'wcfmmp_admin_wcfm_vendor_settings_after_callback');

function wcps_clone_products($product_ids, $vendor_id) {
    $product_ids = isset($product_ids) ? wc_clean(wp_unslash($product_ids)) : '';
    $reg_ids = [];
    if (is_array($product_ids) && !empty($product_ids)) {
        foreach ($product_ids as $product_id) {
            if ($product_id) {
                $product = wc_get_product($product_id);

                if (false === $product) {
                    /* translators: %s: product id */
                    //echo '{"status": false, "message": "' . sprintf( __( 'Product creation failed, could not find original product: %s', 'woocommerce' ), $product_id ) . '" }';
                    continue;
                }
                $WCFMmp_Product_Multivendor = new WCFMmp_Product_Multivendor();

                $duplicate = $WCFMmp_Product_Multivendor->wcfmmp_product_clone($product_id);
                $reg_ids[] = $duplicate->get_id();
                // Hook rename to match other woocommerce_product_* hooks, and to move away from depending on a response from the wp_posts table.
                do_action('woocommerce_product_duplicate', $duplicate, $product);
                do_action('after_wcfmmp_product_multivendor_clone', $duplicate->get_id(), $product);
                $post = [
                    'ID' => $duplicate->get_id(),
                    'post_author' => $vendor_id,
                    'post_status' => 'publish',
                ];
                $post_id = wp_update_post($post, true);
            }
        }
    }
    return $reg_ids;
}

function wcps_get_global_products($is_ids = FALSE) {
    $parms = [];
    $parms['post_type'] = 'product';
    $parms['post_status'] = 'publish';
    $parms['meta_query'] = [
        [
            'key' => 'wcps_is_global_product',
            'value' => 'yes',
            'compare' => '='
        ]
    ];
    $get_products = new WP_Query($parms);
    wp_reset_query();
    if (!is_wp_error($get_products)) {
        $products = $get_products->get_posts();

        if ($is_ids) {
            $ids = [];
            foreach ($products as $key => $product) {
                $ids[] = $product->ID;
            }
            return $ids;
        }
        return $products;
    }
    return [];
}

function wcps_add_cloned_product_relation($new_pro_id, $product) {
    update_post_meta($new_pro_id, 'parent_pro_id', $product->get_id());
}

add_action('after_wcfmmp_product_multivendor_clone', 'wcps_add_cloned_product_relation', 10, 2);

function wcps_get_cloned_products($parent_id = 0) {
    $parms = [];
    $parms['post_type'] = 'product';
//    $parms['post_status'] = 'publish';
    if (!$store_id) {
        $parms['meta_query'] = [
            [
                'key' => 'parent_pro_id',
                'value' => $parent_id,
                'compare' => '='
            ]
        ];
    }
    $products = new WP_Query($parms);
    wp_reset_query();
    if ($products->post_count) {
        return $products->get_posts();
    }
    return [];
}

function wcps_sync_products($product_id, $product, $update) {
    if ($product_id > 0) {
        $is_global = get_post_meta($product_id, 'wcps_is_global_product', TRUE);
        if (!$is_global) {
            $is_global = 'no';
        }
        if ($is_global == 'yes') {
            $get_products = wcps_get_cloned_products($product_id);
            if (!empty($get_products)) {
                foreach ($get_products as $get_product) {
                    $c_product_id = $get_product->ID;

                    $get_meta_data = wcps_get_post_meta($product_id);
                    if ($get_meta_data) {
                        foreach ($get_meta_data as $meta_key => $meta_value) {
                            update_post_meta($c_product_id, $meta_key, $meta_value);
                        }
                    }
                    wcps_update_product($c_product_id);
                }
            }
        }
    }
}

add_action('save_post_product', 'wcps_sync_products', 10, 3);

function wcps_get_post_meta($post_id, $meta_key = '') {
    if (!$post_id) {
        return FALSE;
    }
    if (!empty($meta_key)) {
        $get_meta = get_post_meta($post_id, $meta_key, TRUE);
        return $get_meta;
    }
    $get_metas = get_post_meta($post_id);
    $gen_metas = [];
    foreach ($get_metas as $meta_key => $meta_value) {
        $meta_value = $meta_value[0];
        $meta_value_g = @unserialize($meta_value);
        if ($meta_value_g) {
            $gen_metas[$meta_key] = $meta_value_g;
        } else {
            $gen_metas[$meta_key] = $meta_value;
        }
    }
    if (!empty($gen_metas)) {
        return $gen_metas;
    }
    return FALSE;
}

function wcps_update_product($product_id) {
    $post = [
        'ID' => $product_id,
        'post_title' => $data->post_title,
        'post_content' => $data->post_content,
        'post_excerpt' => $data->post_excerpt
    ];
    wp_update_post($post);
}

function wcps_before_menu_products_query($parms, $store_id) {
    if (!$store_id) {
        $parms['meta_query'] = [
            [
                'key' => 'wcps_is_global_product',
                'value' => 'yes',
                'compare' => '='
            ]
        ];
    }
    return $parms;
}

add_filter('cs_before_menu_products_query', 'wcps_before_menu_products_query', 10, 2);
