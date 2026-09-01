<?php
/**
 * Lily Shipping — Egyptian delivery areas + COD-ready checkout.
 *
 * One native WooCommerce Shipping Zone (Egypt) with a Flat Rate method.
 * The customer picks a Governorate / Area at checkout; the real
 * WooCommerce shipping total is recalculated from the Lily rate table.
 *
 * @package Lily
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Lily delivery areas => shipping cost (EGP).
 *
 * Single source of truth for development. Filter with
 * `lily_shipping_rates` to adjust without editing this list.
 *
 * @return array
 */
function lily_shipping_rates() {
	return apply_filters(
		'lily_shipping_rates',
		array(
			'cairo'           => 70,
			'giza'            => 70,
			'new-cities'      => 80,
			'giza-suburbs'    => 110,
			'alexandria'      => 90,
			'qalyubia'        => 90,
			'dakahlia'        => 90,
			'sharqia'         => 90,
			'gharbia'         => 90,
			'monufia'         => 90,
			'beheira'         => 90,
			'kafr-el-sheikh'  => 90,
			'damietta'        => 90,
			'new-damietta'    => 100,
			'port-said'       => 90,
			'ismailia'        => 90,
			'suez'            => 90,
			'fayoum'          => 90,
			'beni-suef'       => 100,
			'minya'           => 100,
			'assiut'          => 120,
			'sohag'           => 120,
			'qena'            => 120,
			'luxor'           => 120,
			'aswan'           => 150,
			'new-valley'      => 150,
			'marsa-matrouh'   => 150,
			'north-sinai'     => 170,
			'south-sinai'     => 170,
			'sharm-el-sheikh' => 170,
			'red-sea'         => 170,
			'hurghada'        => 170,
			'north-coast'     => 170,
		)
	);
}

/**
 * Detailed supported locations per shipping group (from the Lily
 * delivery spreadsheet). Display-only: appended inside parentheses in
 * the Governorate / Area dropdown. Groups absent here stay clean.
 * Filter with `lily_shipping_area_details` to adjust.
 *
 * @return array slug => comma-separated detail locations.
 */
function lily_shipping_area_details() {
	return apply_filters(
		'lily_shipping_area_details',
		array(
			'new-cities'   => __( 'New Cairo, New Capital, El Shorouk, El Obour, Madinaty, El Rehab', 'lily' ),
			'giza-suburbs' => __( '6th of October, Sheikh Zayed, Hadayek October, October Gardens', 'lily' ),
		)
	);
}

/**
 * Human label for an area slug.
 *
 * @param string $slug Area slug.
 * @return string
 */
function lily_shipping_area_label( $slug ) {
	$labels = array(
		'cairo'           => __( 'Cairo', 'lily' ),
		'giza'            => __( 'Giza', 'lily' ),
		'new-cities'      => __( 'New Cities', 'lily' ),
		'giza-suburbs'    => __( 'Giza Suburbs', 'lily' ),
		'alexandria'      => __( 'Alexandria', 'lily' ),
		'qalyubia'        => __( 'Qalyubia', 'lily' ),
		'dakahlia'        => __( 'Dakahlia', 'lily' ),
		'sharqia'         => __( 'Sharqia', 'lily' ),
		'gharbia'         => __( 'Gharbia', 'lily' ),
		'monufia'         => __( 'Monufia', 'lily' ),
		'beheira'         => __( 'Beheira', 'lily' ),
		'kafr-el-sheikh'  => __( 'Kafr El Sheikh', 'lily' ),
		'damietta'        => __( 'Damietta', 'lily' ),
		'new-damietta'    => __( 'New Damietta', 'lily' ),
		'port-said'       => __( 'Port Said', 'lily' ),
		'ismailia'        => __( 'Ismailia', 'lily' ),
		'suez'            => __( 'Suez', 'lily' ),
		'fayoum'          => __( 'Fayoum', 'lily' ),
		'beni-suef'       => __( 'Beni Suef', 'lily' ),
		'minya'           => __( 'Minya', 'lily' ),
		'assiut'          => __( 'Assiut', 'lily' ),
		'sohag'           => __( 'Sohag', 'lily' ),
		'qena'            => __( 'Qena', 'lily' ),
		'luxor'           => __( 'Luxor', 'lily' ),
		'aswan'           => __( 'Aswan', 'lily' ),
		'new-valley'      => __( 'New Valley', 'lily' ),
		'marsa-matrouh'   => __( 'Marsa Matrouh', 'lily' ),
		'north-sinai'     => __( 'North Sinai', 'lily' ),
		'south-sinai'     => __( 'South Sinai', 'lily' ),
		'sharm-el-sheikh' => __( 'Sharm El Sheikh', 'lily' ),
		'red-sea'         => __( 'Red Sea', 'lily' ),
		'hurghada'        => __( 'Hurghada', 'lily' ),
		'north-coast'     => __( 'North Coast', 'lily' ),
	);

	$label = isset( $labels[ $slug ] ) ? $labels[ $slug ] : $slug;

	// Spreadsheet-derived detail locations, display-only (never duplicates options).
	$details = lily_shipping_area_details();
	if ( isset( $details[ $slug ] ) && '' !== $details[ $slug ] ) {
		$label .= ' (' . $details[ $slug ] . ')';
	}

	return $label;
}

/* ── Checkout fields: minimal Lily customer information ─────────────── */

add_filter( 'woocommerce_checkout_fields', 'lily_checkout_fields' );

/**
 * Reduce checkout to Full Name, Phone, Governorate/Area, Detailed Address.
 *
 * @param array $fields Checkout fields.
 * @return array
 */
function lily_checkout_fields( $fields ) {
	$rates = lily_shipping_rates();

	$area_options = array( '' => __( 'Select your area', 'lily' ) );
	foreach ( array_keys( $rates ) as $slug ) {
		$area_options[ $slug ] = lily_shipping_area_label( $slug );
	}

	$fields['billing'] = array(
		'billing_first_name' => array(
			'label'    => __( 'Full Name', 'lily' ),
			'required' => true,
			'class'    => array( 'lily-checkout-field' ),
			'priority' => 10,
		),
		'billing_phone'      => array(
			'label'    => __( 'Phone Number', 'lily' ),
			'required' => true,
			'type'     => 'tel',
			'class'    => array( 'lily-checkout-field' ),
			'priority' => 20,
		),
		'billing_email'      => array(
			'label'       => __( 'Email (optional)', 'lily' ),
			'required'    => false,
			'type'        => 'email',
			'class'       => array( 'lily-checkout-field' ),
			'priority'    => 30,
			'custom_attributes' => array( 'autocomplete' => 'email' ),
		),
		'billing_country'    => array(
			'type'     => 'country',
			'required' => true,
			'class'    => array( 'lily-checkout-field', 'lily-checkout-field--hidden' ),
			'priority' => 40,
		),
		'lily_delivery_area' => array(
			'label'             => __( 'Governorate / Area', 'lily' ),
			'required'          => true,
			'type'              => 'select',
			'class'             => array( 'lily-checkout-field' ),
			'options'           => $area_options,
			'priority'          => 50,
			'custom_attributes' => array( 'data-lily-shipping-area' => '1' ),
		),
		'billing_address_1'  => array(
			'label'       => __( 'Detailed Address', 'lily' ),
			'required'    => true,
			'type'        => 'textarea',
			'class'       => array( 'lily-checkout-field' ),
			'priority'    => 60,
			'placeholder' => __( 'Area, street, building, floor, apartment, landmark…', 'lily' ),
			'custom_attributes' => array( 'rows' => 3 ),
		),
		'lily_delivery_note' => array(
			'label'       => __( 'Delivery Note', 'lily' ),
			'required'    => false,
			'type'        => 'textarea',
			'class'       => array( 'lily-checkout-field' ),
			'priority'    => 70,
			'placeholder' => __( 'Any delivery instructions for the courier', 'lily' ),
			'custom_attributes' => array( 'rows' => 2 ),
		),
	);

	unset( $fields['shipping'] );

	return $fields;
}

add_filter( 'default_checkout_billing_country', 'lily_default_country', 10, 2 );

/**
 * Default the (hidden) country to Egypt.
 *
 * @param string $value Current value.
 * @param string $input Input key.
 * @return string
 */
function lily_default_country( $value, $input ) {
	if ( 'billing_country' === $input && '' === $value ) {
		return 'EG';
	}
	return $value;
}

/* ── Persist the selected area for shipping calculation ─────────────── */

add_action( 'woocommerce_checkout_update_order_review', 'lily_capture_delivery_area' );

/**
 * Store the chosen area in the session so package rates recalculate.
 * Lily delivers within Egypt only — pin the destination country.
 *
 * @param string $posted_data Raw posted data string.
 */
function lily_capture_delivery_area( $posted_data ) {
	$area = '';
	parse_str( (string) $posted_data, $parsed );
	if ( isset( $parsed['lily_delivery_area'] ) ) {
		$area = wc_clean( wp_unslash( $parsed['lily_delivery_area'] ) );
	} elseif ( isset( $_POST['lily_delivery_area'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$area = wc_clean( wp_unslash( $_POST['lily_delivery_area'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
	}

	if ( WC()->session ) {
		$previous = (string) WC()->session->get( 'lily_delivery_area' );

		if ( '' !== $area && isset( lily_shipping_rates()[ $area ] ) ) {
			WC()->session->set( 'lily_delivery_area', $area );
		} elseif ( '' === $area ) {
			WC()->session->set( 'lily_delivery_area', null );
		}

		// The destination hash may not change when only the area changes —
		// clear WooCommerce's cached package rates so the new price applies.
		if ( $area !== $previous ) {
			WC()->session->set( 'shipping_for_package_0', null );
			WC()->session->set( 'shipping_for_package_1', null );
			WC()->session->set( 'chosen_shipping_methods', null );
		}
	}

	// Pin the shipping destination to Egypt so the Lily zone always matches,
	// and mark shipping as calculated — Lily has no visible shipping-address
	// fields, so without this flag WooCommerce keeps saying
	// "Enter your address to view shipping options."
	if ( WC()->customer ) {
		WC()->customer->set_billing_country( 'EG' );
		WC()->customer->set_shipping_country( 'EG' );
		WC()->customer->set_shipping_state( '' );
		WC()->customer->set_calculated_shipping( true );
		WC()->customer->save();
	}
}

add_filter( 'woocommerce_package_rates', 'lily_area_shipping_rates', 10, 2 );

/**
 * Lily has no visible shipping-address fields, so checkout.js keeps posting
 * has_full_address=false and WooCommerce resets has_calculated_shipping()
 * after our update hook runs — suppressing the shipping block. The area
 * select IS the shipping destination: mark shipping as calculated whenever
 * totals are recalculated with a destination country pinned.
 *
 * @param WC_Cart $cart Cart object.
 */
function lily_mark_shipping_calculated( $cart ) {
	if ( function_exists( 'WC' ) && WC()->customer ) {
		// The checkout form still posts the default US country; re-pin the
		// destination to Egypt after WooCommerce has applied posted values.
		WC()->customer->set_billing_country( 'EG' );
		WC()->customer->set_shipping_country( 'EG' );
		WC()->customer->set_shipping_state( '' );
		WC()->customer->set_calculated_shipping( true );

		// Destination changed after packages were calculated — refresh them.
		if ( WC()->session ) {
			WC()->session->set( 'shipping_for_package_0', null );
			WC()->session->set( 'shipping_for_package_1', null );
		}
		WC()->cart->calculate_shipping();
	}
}
add_action( 'woocommerce_after_calculate_totals', 'lily_mark_shipping_calculated', 20 );

/**
 * Belt-and-braces: on Checkout with the Egypt destination pinned, always
 * display the shipping block (rates come from lily_area_shipping_rates).
 *
 * @param bool $show Whether shipping should be shown.
 * @return bool
 */
function lily_force_show_shipping( $show ) {
	if ( function_exists( 'WC' ) && WC()->customer && 'EG' === WC()->customer->get_shipping_country() ) {
		return true;
	}
	return $show;
}
add_filter( 'woocommerce_cart_show_shipping', 'lily_force_show_shipping', 10, 1 );

/**
 * Apply the Lily rate for the selected area to the real shipping total.
 *
 * @param array $rates   Available rates.
 * @param array $package Package.
 * @return array
 */
function lily_area_shipping_rates( $rates, $package ) {
	if ( ! WC()->session ) {
		return $rates;
	}

	$area  = WC()->session->get( 'lily_delivery_area' );
	$rates_table = lily_shipping_rates();

	if ( ! $area || ! isset( $rates_table[ $area ] ) ) {
		// No area chosen yet: hide misleading zero-cost rate if possible.
		return $rates;
	}

	foreach ( $rates as $rate ) {
		if ( 'flat_rate' === $rate->method_id ) {
			$rate->set_cost( (float) $rates_table[ $area ] );
			$rate->set_label( lily_shipping_area_label( $area ) );
		}
	}

	return $rates;
}

/* ── Order persistence ──────────────────────────────────────────────── */

add_filter( 'woocommerce_checkout_posted_data', 'lily_capture_delivery_area_from_data' );

/**
 * Keep the session area in sync on direct checkout POSTs as well.
 *
 * @param array $data Posted checkout data.
 * @return array
 */
function lily_capture_delivery_area_from_data( $data ) {
	if ( isset( $data['lily_delivery_area'] ) ) {
		lily_capture_delivery_area( http_build_query( array( 'lily_delivery_area' => $data['lily_delivery_area'] ) ) );
	}
	return $data;
}

add_action( 'woocommerce_checkout_create_order', 'lily_save_area_on_order', 20, 2 );

/**
 * Persist area + detailed address onto the order.
 *
 * @param WC_Order $order   Order object.
 * @param array    $data    Posted checkout data.
 */
function lily_save_area_on_order( $order, $data ) {
	$area = isset( $data['lily_delivery_area'] ) ? wc_clean( $data['lily_delivery_area'] ) : '';

	if ( ! $area && WC()->session ) {
		$area = (string) WC()->session->get( 'lily_delivery_area' );
	}

	if ( $area && isset( lily_shipping_rates()[ $area ] ) ) {
		// Store the clean group name (without display-only detail parentheses).
		$label = trim( preg_replace( '/\s*\([^)]*\)$/u', '', lily_shipping_area_label( $area ) ) );
		$order->set_billing_state( $label );
		$order->set_shipping_state( $label );
		$order->update_meta_data( '_lily_delivery_area', $area );
		$order->update_meta_data( '_lily_delivery_area_label', $label );
	}

	// Optional courier instructions travel with the order.
	if ( ! empty( $data['lily_delivery_note'] ) ) {
		$order->update_meta_data( '_lily_delivery_note', sanitize_textarea_field( wp_unslash( $data['lily_delivery_note'] ) ) );
	}

	// Mirror billing details onto shipping (ship-to-billing store).
	$order->set_shipping_first_name( $order->get_billing_first_name() );
	$order->set_shipping_address_1( $order->get_billing_address_1() );
	$order->set_shipping_phone( $order->get_billing_phone() );
}

add_action( 'woocommerce_checkout_update_customer', 'lily_save_area_on_customer', 10, 2 );

/**
 * Keep the area on the customer record between updates.
 *
 * @param WC_Customer $customer Customer object.
 * @param array       $data     Posted data.
 */
function lily_save_area_on_customer( $customer, $data ) {
	if ( isset( $data['lily_delivery_area'] ) ) {
		$area = wc_clean( $data['lily_delivery_area'] );
		if ( $area && isset( lily_shipping_rates()[ $area ] ) ) {
			WC()->session->set( 'lily_delivery_area', $area );
		}
	}
}

/* ── Validation ─────────────────────────────────────────────────────── */

add_action( 'woocommerce_after_checkout_validation', 'lily_checkout_validate', 10, 2 );

/**
 * Friendly inline errors for the Lily-required fields.
 *
 * @param array $data   Posted data.
 * @param WP_Error $errors Validation errors.
 */
function lily_checkout_validate( $data, $errors ) {
	$areas = lily_shipping_rates();

	if ( empty( $data['lily_delivery_area'] ) || ! isset( $areas[ $data['lily_delivery_area'] ] ) ) {
		$errors->add( 'lily_area', __( 'Please select your Governorate / Area for delivery.', 'lily' ) );
	}

	if ( empty( $data['billing_phone'] ) ) {
		$errors->add( 'lily_phone', __( 'Please enter your phone number.', 'lily' ) );
	}

	if ( empty( $data['billing_address_1'] ) || strlen( trim( $data['billing_address_1'] ) ) < 5 ) {
		$errors->add( 'lily_address', __( 'Please enter your detailed address.', 'lily' ) );
	}
}

/* ── Thank you / order details: show the chosen area ────────────────── */

/**
 * Display the optional delivery note to the store/delivery team in the admin.
 *
 * @param WC_Order $order Order object.
 */
function lily_admin_show_delivery_note( $order ) {
	$note = $order->get_meta( '_lily_delivery_note' );
	if ( $note ) {
		echo '<p class="form-field form-field-wide lily-admin-delivery-note"><strong>' . esc_html__( 'Delivery Note', 'lily' ) . ':</strong> ' . nl2br( esc_html( $note ) ) . '</p>';
	}
}
add_action( 'woocommerce_admin_order_data_after_billing_address', 'lily_admin_show_delivery_note', 10, 1 );

add_action( 'woocommerce_order_details_after_customer_address', 'lily_order_show_area', 10, 2 );

/**
 * Show the delivery note on the order confirmation / view-order pages.
 *
 * @param WC_Order $order Order object.
 */
function lily_order_show_note( $order ) {
	$note = $order->get_meta( '_lily_delivery_note' );
	if ( $note ) {
		echo '<p class="lily-order-area"><strong>' . esc_html__( 'Delivery Note', 'lily' ) . ':</strong> ' . esc_html( $note ) . '</p>';
	}
}
add_action( 'woocommerce_order_details_after_order_table', 'lily_order_show_note', 20 );

/**
 * Display the delivery area on order views.
 *
 * @param string   $address_type Address type.
 * @param WC_Order $order        Order.
 */
function lily_order_show_area( $address_type, $order ) {	if ( 'shipping' !== $address_type ) {
		return;
	}
	$label = $order->get_meta( '_lily_delivery_area_label' );
	if ( $label ) {
		echo '<p class="lily-order-area"><strong>' . esc_html__( 'Governorate / Area', 'lily' ) . ':</strong> ' . esc_html( $label ) . '</p>';
	}
}
