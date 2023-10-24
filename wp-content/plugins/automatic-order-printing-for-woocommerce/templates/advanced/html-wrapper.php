<?php
/**
 * HTML Wraper for templates
 *
 * This template can be overridden by copying it to yourtheme/woo-order-auto-print/advanced/html-wrapper.php.
 *
 * @package    Woo_Order_Auto_Print
 * @subpackage Woo_Order_Auto_Print/templates
 */

?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title><?php echo esc_html( $title ); ?></title>
	<style type="text/css"><?php do_action( 'woo_order_auto_print_template_styles', $order_id, $template_id, $template_settings ); ?></style>
</head>
<body class="<?php echo esc_attr( $classes ); ?>">
<?php do_action( 'woo_order_auto_print_template_content', $order_id, $template_id, $template_settings ); ?>
</body>
</html>
