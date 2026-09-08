<?php
/**
 * Cart errors page — Lily override.
 *
 * Shown by WooCommerce when the checkout page is opened with invalid cart
 * items (e.g. a prescription product missing OD/OS powers). Prints the real
 * validation notices (with the affected product name) and routes the
 * customer back to the cart.
 *
 * Based on WooCommerce templates/checkout/cart-errors.php.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package Lily
 */

defined( 'ABSPATH' ) || exit;
?>

<?php wc_print_notices(); ?>

<p class="lily-cart-errors__note"><?php esc_html_e( 'There are some issues with the items in your cart. Please go back to the cart page and resolve these issues before checking out.', 'woocommerce' ); ?></p>

<?php do_action( 'woocommerce_cart_has_errors' ); ?>

<p><a class="button wc-backward lily-button" href="<?php echo esc_url( wc_get_cart_url() ); ?>"><?php esc_html_e( 'Return to cart', 'woocommerce' ); ?></a></p>
