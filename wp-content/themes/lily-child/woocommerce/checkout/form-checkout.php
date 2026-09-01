<?php
/**
 * Lily checkout form.
 *
 * Two-column editorial layout: customer info left, order summary right.
 * All WooCommerce checkout functionality is preserved.
 *
 * @package Lily
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'woocommerce_before_checkout_form', $checkout );

// If checkout registration is disabled and not logged in, nothing to show.
if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
	echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) ) );
	do_action( 'woocommerce_after_checkout_form', $checkout );
	return;
}

?>
<form name="checkout" method="post" class="checkout woocommerce-checkout lily-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data">

	<div class="lily-checkout__main">

		<div class="lily-checkout__details">
			<?php if ( $checkout->get_checkout_fields() ) : ?>

				<?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>

				<?php if ( ! is_user_logged_in() && $checkout->is_registration_enabled() ) : ?>
					<p class="lily-checkout__login-note"><?php esc_html_e( 'You may create an account after placing your order — no account needed to order.', 'lily' ); ?></p>
				<?php endif; ?>

				<div class="lily-checkout__billing" id="customer_details">
					<?php do_action( 'woocommerce_checkout_billing' ); ?>
				</div>

				<?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>

			<?php endif; ?>
		</div>

		<div class="lily-checkout__summary">
			<h2 class="lily-checkout__summary-title" id="order_review_heading"><?php esc_html_e( 'Order Summary', 'lily' ); ?></h2>

			<?php do_action( 'woocommerce_checkout_before_order_review' ); ?>

			<div id="order_review" class="woocommerce-checkout-review-order">
				<?php do_action( 'woocommerce_checkout_order_review' ); ?>
			</div>

			<?php do_action( 'woocommerce_checkout_after_order_review' ); ?>
		</div>

	</div>

</form>

<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>
