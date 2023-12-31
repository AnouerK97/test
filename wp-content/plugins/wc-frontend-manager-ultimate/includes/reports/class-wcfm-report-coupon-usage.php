<?php
/**
 * Report class responsible for handling coupon usage reports.
 *
 * @since 3.1.2
 *
 * @package    WooCommerce Frontend Manager Ultimate
 * @subpackage wcfmu/includes/reports
 */

if (! defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}

require_once WC()->plugin_path().'/includes/admin/reports/class-wc-admin-report.php';

class WCFM_Report_Coupon_Usage extends WC_Admin_Report
{

    /**
     * Chart colors.
     *
     * @var array
     */
    public $chart_colours = [];

    /**
     * Coupon codes.
     *
     * @var array
     */
    public $coupon_codes = [];


    /**
     * Constructor.
     */
    public function __construct()
    {
        $coupon_codes = isset($_GET['coupon_codes']) ? $_GET['coupon_codes'] : '';

        if ($coupon_codes) {
            if (is_array($coupon_codes)) {
                $this->coupon_codes = array_filter(array_map('sanitize_text_field', $coupon_codes));
            } else {
                $this->coupon_codes = array_filter([ sanitize_text_field($coupon_codes) ]);
            }
        }

    }//end __construct()


    /**
     * Get the legend for the main chart sidebar.
     *
     * @return array
     */
    public function get_chart_legend()
    {
        $legend = [];

        $total_discount_query = [
            'data'         => [
                'discount_amount' => [
                    'type'            => 'order_item_meta',
                    'order_item_type' => 'coupon',
                    'function'        => 'SUM',
                    'name'            => 'discount_amount',
                ],
            ],
            'where'        => [
                [
                    'key'      => 'order_item_type',
                    'value'    => 'coupon',
                    'operator' => '=',
                ],
            ],
            'query_type'   => 'get_var',
            'filter_range' => true,
            'order_types'  => wc_get_order_types('order-count'),
        ];

        $total_coupons_query = [
            'data'         => [
                'order_item_id' => [
                    'type'            => 'order_item',
                    'order_item_type' => 'coupon',
                    'function'        => 'COUNT',
                    'name'            => 'order_coupon_count',
                ],
            ],
            'where'        => [
                [
                    'key'      => 'order_item_type',
                    'value'    => 'coupon',
                    'operator' => '=',
                ],
            ],
            'query_type'   => 'get_var',
            'filter_range' => true,
            'order_types'  => wc_get_order_types('order-count'),
        ];

        if (! empty($this->coupon_codes)) {
            $coupon_code_query = [
                'type'     => 'order_item',
                'key'      => 'order_item_name',
                'value'    => $this->coupon_codes,
                'operator' => 'IN',
            ];

            $total_discount_query['where'][] = $coupon_code_query;
            $total_coupons_query['where'][]  = $coupon_code_query;
        }

        $total_discount = $this->get_order_report_data($total_discount_query);
        $total_coupons  = absint($this->get_order_report_data($total_coupons_query));

        $legend[] = [
            // translators: %s: discount amount
            'title'            => sprintf(__('%s discounts in total', 'woocommerce'), '<strong>'.wc_price($total_discount).'</strong>'),
            'color'            => $this->chart_colours['discount_amount'],
            'highlight_series' => 1,
        ];

        $legend[] = [
            // translators: %s: coupons amount
            'title'            => sprintf(__('%s coupons used in total', 'woocommerce'), '<strong>'.$total_coupons.'</strong>'),
            'color'            => $this->chart_colours['coupon_count'],
            'highlight_series' => 0,
        ];

        return $legend;

    }//end get_chart_legend()


    /**
     * Output the report.
     */
    public function output_report()
    {
        $ranges = [
            'year'       => __('Year', 'woocommerce'),
            'last_month' => __('Last month', 'woocommerce'),
            'month'      => __('This month', 'woocommerce'),
            '7day'       => __('Last 7 days', 'woocommerce'),
        ];

        $this->chart_colours = [
            'discount_amount' => '#3498db',
            'coupon_count'    => '#d4d9dc',
        ];

        $current_range = ! empty($_GET['range']) ? sanitize_text_field($_GET['range']) : '7day';

        if (! in_array($current_range, [ 'custom', 'year', 'last_month', 'month', '7day' ])) {
            $current_range = '7day';
        }

        $this->check_current_range_nonce($current_range);
        $this->calculate_current_range($current_range);

        include WC()->plugin_path().'/includes/admin/views/html-report-by-date.php';

    }//end output_report()


    /**
     * Get chart widgets.
     *
     * @return array
     */
    public function get_chart_widgets()
    {
        $widgets = [];

        $widgets[] = [
            'title'    => '',
            'callback' => [
                $this,
                'coupons_widget',
            ],
        ];

        return $widgets;

    }//end get_chart_widgets()


    /**
     * Output coupons widget.
     */
    public function coupons_widget()
    {
        ?>
        <h4 class="section_title"><span><?php _e('Filter by coupon', 'woocommerce'); ?></span></h4>
        <div class="section">
            <form method="GET">
                <div>
                    <?php
                    $used_coupons = $this->get_order_report_data(
                        [
                            'data'         => [
                                'order_item_name' => [
                                    'type'            => 'order_item',
                                    'order_item_type' => 'coupon',
                                    'function'        => '',
                                    'distinct'        => true,
                                    'name'            => 'order_item_name',
                                ],
                            ],
                            'where'        => [
                                [
                                    'key'      => 'order_item_type',
                                    'value'    => 'coupon',
                                    'operator' => '=',
                                ],
                            ],
                            'query_type'   => 'get_col',
                            'filter_range' => false,
                        ]
                    );

                    if (! empty($used_coupons) && is_array($used_coupons)) :
                        ?>
                        <select id="coupon_codes" name="coupon_codes" class="wc-enhanced-select" data-placeholder="<?php esc_attr_e('Choose coupons&hellip;', 'woocommerce'); ?>" style="width:100%;">
                            <option value=""><?php _e('All coupons', 'woocommerce'); ?></option>
                        <?php
                        foreach ($used_coupons as $coupon) {
                            echo '<option value="'.esc_attr($coupon).'" '.selected(in_array($coupon, $this->coupon_codes), true, false).'>'.$coupon.'</option>';
                        }
                        ?>
                        </select>
                        <input type="submit" class="submit button" value="<?php esc_attr_e('Show', 'woocommerce'); ?>" />
                        <input type="hidden" name="range" value="<?php echo ( ! empty($_GET['range']) ) ? esc_attr($_GET['range']) : ''; ?>" />
                        <input type="hidden" name="start_date" value="<?php echo ( ! empty($_GET['start_date']) ) ? esc_attr($_GET['start_date']) : ''; ?>" />
                        <input type="hidden" name="end_date" value="<?php echo ( ! empty($_GET['end_date']) ) ? esc_attr($_GET['end_date']) : ''; ?>" />
                        <input type="hidden" name="page" value="<?php echo ( ! empty($_GET['page']) ) ? esc_attr($_GET['page']) : ''; ?>" />
                        <input type="hidden" name="tab" value="<?php echo ( ! empty($_GET['tab']) ) ? esc_attr($_GET['tab']) : ''; ?>" />
                        <input type="hidden" name="report" value="<?php echo ( ! empty($_GET['report']) ) ? esc_attr($_GET['report']) : ''; ?>" />
                    <?php else : ?>
                        <span><?php _e('No used coupons found', 'woocommerce'); ?></span>
                    <?php endif; ?>
                </div>
            </form>
        </div>
        <h4 class="section_title"><span><?php _e('Most popular', 'woocommerce'); ?></span></h4>
        <div class="section">
            <table cellspacing="0">
                <?php
                $most_popular = $this->get_order_report_data(
                    [
                        'data'         => [
                            'order_item_name' => [
                                'type'            => 'order_item',
                                'order_item_type' => 'coupon',
                                'function'        => '',
                                'name'            => 'coupon_code',
                            ],
                            'order_item_id'   => [
                                'type'            => 'order_item',
                                'order_item_type' => 'coupon',
                                'function'        => 'COUNT',
                                'name'            => 'coupon_count',
                            ],
                        ],
                        'where'        => [
                            [
                                'type'     => 'order_item',
                                'key'      => 'order_item_type',
                                'value'    => 'coupon',
                                'operator' => '=',
                            ],
                        ],
                        'order_by'     => 'coupon_count DESC',
                        'group_by'     => 'order_item_name',
                        'limit'        => 12,
                        'query_type'   => 'get_results',
                        'filter_range' => true,
                    ]
                );

                if (! empty($most_popular) && is_array($most_popular)) {
                    foreach ($most_popular as $coupon) {
                        echo '<tr class="'.( in_array($coupon->coupon_code, $this->coupon_codes) ? 'active' : '' ).'">
							<td class="count" width="1%">'.$coupon->coupon_count.'</td>
							<td class="name"><a href="'.esc_url(add_query_arg('coupon_codes', $coupon->coupon_code)).'">'.$coupon->coupon_code.'</a></td>
						</tr>';
                    }
                } else {
                    echo '<tr><td colspan="2">'.__('No coupons found in range', 'woocommerce').'</td></tr>';
                }
                ?>
            </table>
        </div>
        <h4 class="section_title"><span><?php _e('Most discount', 'woocommerce'); ?></span></h4>
        <div class="section">
            <table cellspacing="0">
                <?php
                $most_discount = $this->get_order_report_data(
                    [
                        'data'         => [
                            'order_item_name' => [
                                'type'            => 'order_item',
                                'order_item_type' => 'coupon',
                                'function'        => '',
                                'name'            => 'coupon_code',
                            ],
                            'discount_amount' => [
                                'type'            => 'order_item_meta',
                                'order_item_type' => 'coupon',
                                'function'        => 'SUM',
                                'name'            => 'discount_amount',
                            ],
                        ],
                        'where'        => [
                            [
                                'type'     => 'order_item',
                                'key'      => 'order_item_type',
                                'value'    => 'coupon',
                                'operator' => '=',
                            ],
                        ],
                        'order_by'     => 'discount_amount DESC',
                        'group_by'     => 'order_item_name',
                        'limit'        => 12,
                        'query_type'   => 'get_results',
                        'filter_range' => true,
                    ]
                );

                if (! empty($most_discount) && is_array($most_discount)) {
                    foreach ($most_discount as $coupon) {
                        echo '<tr class="'.( in_array($coupon->coupon_code, $this->coupon_codes) ? 'active' : '' ).'">
							<td class="count" width="1%">'.wc_price($coupon->discount_amount).'</td>
							<td class="name"><a href="'.esc_url(add_query_arg('coupon_codes', $coupon->coupon_code)).'">'.$coupon->coupon_code.'</a></td>
						</tr>';
                    }
                } else {
                    echo '<tr><td colspan="3">'.__('No coupons found in range', 'woocommerce').'</td></tr>';
                }
                ?>
            </table>
        </div>
        <script type="text/javascript">
            jQuery('.section_title').click(function(){
                var next_section = jQuery(this).next('.section');

                if ( jQuery(next_section).is(':visible') )
                    return false;

                jQuery('.section:visible').slideUp();
                jQuery('.section_title').removeClass('open');
                jQuery(this).addClass('open').next('.section').slideDown();

                return false;
            });
            jQuery('.section').slideUp( 100, function() {
                <?php if (empty($this->coupon_codes)) : ?>
                    jQuery('.section_title:eq(1)').click();
                <?php else : ?>
                    jQuery('.section_title:eq(0)').click();
                <?php endif; ?>
            });
        </script>
        <?php

    }//end coupons_widget()


    /**
     * Output an export link.
     */
    public function get_export_button()
    {
        return;
        $current_range = ! empty($_GET['range']) ? sanitize_text_field($_GET['range']) : '7day';
        ?>
        <a
            href="#"
            download="report-<?php echo esc_attr($current_range); ?>-<?php echo date_i18n('Y-m-d', current_time('timestamp')); ?>.csv"
            class="export_csv"
            data-export="chart"
            data-xaxes="<?php esc_attr_e('Date', 'woocommerce'); ?>"
            data-groupby="<?php echo $this->chart_groupby; ?>"
        >
            <?php _e('Export CSV', 'woocommerce'); ?>
        </a>
        <?php

    }//end get_export_button()


    /**
     * Get the main chart.
     *
     * @return string
     */
    public function get_main_chart()
    {
        global $wp_locale, $WCFM;

        // Get orders and dates in range - we want the SUM of order totals, COUNT of order items, COUNT of orders, and the date
        $order_coupon_counts_query = [
            'data'         => [
                'order_item_name' => [
                    'type'            => 'order_item',
                    'order_item_type' => 'coupon',
                    'function'        => 'COUNT',
                    'name'            => 'order_coupon_count',
                ],
                'post_date'       => [
                    'type'     => 'post_data',
                    'function' => '',
                    'name'     => 'post_date',
                ],
            ],
            'where'        => [
                [
                    'key'      => 'order_item_type',
                    'value'    => 'coupon',
                    'operator' => '=',
                ],
            ],
            'group_by'     => $this->group_by_query,
            'order_by'     => 'post_date ASC',
            'query_type'   => 'get_results',
            'filter_range' => true,
            'order_types'  => wc_get_order_types('order-count'),
        ];

        $order_discount_amounts_query = [
            'data'         => [
                'discount_amount' => [
                    'type'            => 'order_item_meta',
                    'order_item_type' => 'coupon',
                    'function'        => 'SUM',
                    'name'            => 'discount_amount',
                ],
                'post_date'       => [
                    'type'     => 'post_data',
                    'function' => '',
                    'name'     => 'post_date',
                ],
            ],
            'where'        => [
                [
                    'key'      => 'order_item_type',
                    'value'    => 'coupon',
                    'operator' => '=',
                ],
            ],
            'group_by'     => $this->group_by_query.', order_item_name',
            'order_by'     => 'post_date ASC',
            'query_type'   => 'get_results',
            'filter_range' => true,
            'order_types'  => wc_get_order_types('order-count'),
        ];

        if (! empty($this->coupon_codes)) {
            $coupon_code_query = [
                'type'     => 'order_item',
                'key'      => 'order_item_name',
                'value'    => $this->coupon_codes,
                'operator' => 'IN',
            ];

            $order_coupon_counts_query['where'][]    = $coupon_code_query;
            $order_discount_amounts_query['where'][] = $coupon_code_query;
        }

        $order_coupon_counts    = $this->get_order_report_data($order_coupon_counts_query);
        $order_discount_amounts = $this->get_order_report_data($order_discount_amounts_query);

        // Prepare data for report
        $order_coupon_counts    = $this->prepare_chart_data($order_coupon_counts, 'post_date', 'order_coupon_count', $this->chart_interval, $this->start_date, $this->chart_groupby);
        $order_discount_amounts = $this->prepare_chart_data($order_discount_amounts, 'post_date', 'discount_amount', $this->chart_interval, $this->start_date, $this->chart_groupby);

        // Encode in json format
        $chart_data = '{'.'  "order_coupon_counts"         : '.$WCFM->wcfm_prepare_chart_data($order_coupon_counts).', "order_discount_amounts"      : '.$WCFM->wcfm_prepare_chart_data($order_discount_amounts).'}';
        ?>
        <div class="chart-container">
            <div class="chart-placeholder main"><canvas id="chart-placeholder-canvas"></canvas></div>
        </div>
        <script type="text/javascript">
            jQuery(function(){
                    var sales_data = <?php echo $chart_data; ?>;
                    var show_legend    = true;
                    
                    jQuery('.chart-placeholder').css( 'width', jQuery('.chart-placeholder').outerWidth() + 'px' );
                    
                    var ctx = document.getElementById("chart-placeholder-canvas").getContext("2d");
                    var myCouponsUsageReportChart = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                    labels: sales_data.order_coupon_counts.labels,
                                    datasets: [
                                                {
                                                    type: 'line',
                                                    label: "<?php _e('Discount Amounts', 'wc-frontend-manager-ultimate'); ?>",
                                                    backgroundColor: color(window.chartColors.blue).alpha(0.2).rgbString(),
                                                    borderColor: window.chartColors.blue,
                                                    fill: true,
                                                    data: sales_data.order_discount_amounts.datas,
                                                },
                                                {
                                                    type: 'bar',
                                                    label: "<?php _e('Coupon Counts', 'wc-frontend-manager-ultimate'); ?>",
                                                    backgroundColor: color(window.chartColors.orange).alpha(0.5).rgbString(),
                                                    borderColor: window.chartColors.orange,
                                                    borderWidth: 2,
                                                    data: sales_data.order_coupon_counts.datas,
                                                }
                                                ]
                            },
                            options: {
                                    responsive: true,
                                    title:{
                                            text: "<?php _e('Sales Report by Product', 'wc-frontend-manager-ultimate'); ?>"
                                    },
                                    legend: {
                                        position: "bottom",
                                        display: show_legend,
                                    },
                                    scales: {
                                        xAxes: [{
                                            type: "time",
                                            barPercentage: 0.4,
                                            time: {
                                                format: timeFormat,
                                                round: 'day',
                                                tooltipFormat: 'll'
                                            },
                                            scaleLabel: {
                                                display: false,
                                                labelString: '<?php _e('Date', 'wc-frontend-manager-ultimate'); ?>'
                                            }
                                        }],
                                        yAxes: [{
                                            scaleLabel: {
                                                display: true,
                                                labelString: '<?php _e('Amount', 'wc-frontend-manager-ultimate'); ?>'
                                            }
                                        }]
                                    },
                                }
                            });
                    
                    var resizeId;
                    jQuery(window).resize(function() {
                        clearTimeout(resizeId);
                        resizeId = setTimeout(afterResizing, 100);
                    });
                    function afterResizing() {
                        var canvasheight = document.getElementById("chart-placeholder-canvas").height;
                        if(canvasheight <= 350) {
                            //myCouponsUsageReportChart.options.legend.display=false;
                        } else {
                            myCouponsUsageReportChart.options.legend.display=true;
                        }
                        myCouponsUsageReportChart.update();
                    }
                    afterResizing();
                });
        </script>
        <?php

    }//end get_main_chart()


}//end class

