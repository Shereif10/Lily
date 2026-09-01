<?php
/**
 * Lily content pages — one-time creation of the static pages
 * (About, Contact, FAQs, Privacy, Terms, Refund, Cookie, Shipping, Returns).
 *
 * Guarded by a site option so pages are never duplicated. Existing pages
 * with the same slug are reused, never overwritten.
 *
 * Temporary development content; final copy/branding pass comes later.
 *
 * @package Lily
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'init', 'lily_maybe_create_content_pages', 20 );

/**
 * Surface the contact-form result as a body class for the inline notice.
 *
 * @param string[] $classes Body classes.
 * @return string[]
 */
function lily_contact_status_body_class( $classes ) {
	if ( ! isset( $_GET['lily_contact'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- display-only status.
		return $classes;
	}

	$status = sanitize_key( wp_unslash( $_GET['lily_contact'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( 'sent' === $status ) {
		$classes[] = 'lily-contact-status-sent';
	} elseif ( 'error' === $status ) {
		$classes[] = 'lily-contact-status-error';
	}

	return $classes;
}
add_filter( 'body_class', 'lily_contact_status_body_class' );

/**
 * Create the static content pages once.
 */
function lily_maybe_create_content_pages() {
	if ( get_option( 'lily_content_pages_created' ) ) {
		return;
	}

	$pages = lily_content_pages_definition();

	foreach ( $pages as $slug => $page ) {
		$existing = get_page_by_path( $slug );
		if ( $existing instanceof WP_Post && 'publish' === $existing->post_status ) {
			continue; // Reuse — never duplicate.
		}

		$page_id = wp_insert_post(
			array(
				'post_title'     => $page['title'],
				'post_name'      => $slug,
				'post_content'   => $page['content'],
				'post_status'    => 'publish',
				'post_type'      => 'page',
				'comment_status' => 'closed',
				'ping_status'    => 'closed',
			)
		);

		if ( ! empty( $page['is_privacy'] ) && $page_id ) {
			update_option( 'wp_page_for_privacy_policy', $page_id );
		}
	}

	update_option( 'lily_content_pages_created', 1 );
}

/**
 * Page definitions (slug => title/content).
 *
 * @return array
 */
function lily_content_pages_definition() {
	$phone = '01060760098';
	$email = 'hello@lily.example'; // Temporary development placeholder email.

	$contact_info = '<h2>Contact Information</h2>'
		. '<ul class="lily-contact-list">'
		. '<li><strong>Phone:</strong> <a href="tel:' . esc_attr( $phone ) . '">' . esc_html( $phone ) . '</a></li>'
		. '<li><strong>Email:</strong> <a href="mailto:' . esc_attr( $email ) . '">' . esc_html( $email ) . '</a> <em>(temporary development email)</em></li>'
		. '</ul>'
		. '<p>Our customer support team is happy to help with orders, shades, prescription questions and anything else you need.</p>';

	// TranslatePress rewrites form internals for guests, so the form markup is
	// injected client-side from a REST endpoint (see lily.js) into this slot.
	$contact_form = '<h2>Send Us a Message</h2>'
		. '<div class="lily-contact-slot" aria-label="' . esc_attr__( 'Contact form', 'lily' ) . '"></div>'
		. '<p class="lily-form-row"><label for="lily_cf_name">Full Name</label>'
		. '<input class="lily-input" type="text" id="lily_cf_name" name="lily_cf_name" required></p>'
		. '<p class="lily-form-row"><label for="lily_cf_email">Email</label>'
		. '<input class="lily-input" type="email" id="lily_cf_email" name="lily_cf_email" required></p>'
		. '<p class="lily-form-row"><label for="lily_cf_phone">Phone</label>'
		. '<input class="lily-input" type="tel" id="lily_cf_phone" name="lily_cf_phone"></p>'
		. '<p class="lily-form-row"><label for="lily_cf_message">Message</label>'
		. '<textarea class="lily-input" id="lily_cf_message" name="lily_cf_message" rows="5" required></textarea></p>'
		. '<p class="lily-form-hp" aria-hidden="true"><label>Leave this field empty<input type="text" name="lily_cf_hp" tabindex="-1" autocomplete="off"></label></p>'
		. '<p><button type="submit" class="lily-btn">SEND MESSAGE</button></p>'
		. '</form>'
		. '<p class="lily-form-note">Prefer to talk? Call us at ' . esc_html( $phone ) . ' and our support team will be glad to help.</p>';

	$faq_items = array(
		array( 'إزاي أختار لون العدسة المناسب لبشرتي ولون عيني؟', 'البشرة الدافئة/الخمرية تناسبها درجات العسلي، الأخضر الزيتوني، والبني الدافئ. البشرة الفاتحة يناسبها الرمادي، الأزرق، والأخضر الزمردي. إذا كان لون عينك غامق، اختاري الألوان التي تحتوي على درجات كثيفة لتغطية اللون الأصلي بوضوح.' ),
		array( 'إيه الفرق بين العدسات المحددة والغير محددة؟', 'العدسات المحددة تحتوي على إطار داكن حول الحافة الخارجية لتكبير العين وإبرازها، بينما العدسات غير المحددة تمنح نظرة طبيعية مدمجة مع لون عينك الأصلي دون تغيير حجم الحدقة.' ),
		array( 'إزاي أحافظ على العدسة وأحميها من التلف؟', 'اغسلي يديكِ جيداً وجففيها قبل لمس العدسات، استخدمي محلولاً معقماً مخصصاً لتنظيفها يومياً، ولا تغسلي العدسة أو علبتها بماء الصنبور إطلاقاً. يفضل تغيير حافظة العدسات كل 1 إلى 3 أشهر.' ),
		array( 'كم ساعة أقدر ألبس العدسة في اليوم؟', 'يُفضل ارتداء العدسات لمدة تتراوح بين 6 إلى 8 ساعات يومياً لتجنب إجهاد أو جفاف العين، ويجب إزالتها فوراً قبل النوم أو السباحة.' ),
		array( 'لو حسيت بحرقان أو شوكة وأنا لابسة العدسة أعمل إيه؟', 'اخلعي العدسة فوراً، افحصيها للتحقق من عدم وجود شوائب أو شروخ، نظفيها جيداً بالمحلول، وتأكدي أنها ليست مقلوبة على الوجه الخاطئ قبل إعادتها. إذا استمر التهيج، اتركي عينك ترتاح واستشيري طبيباً.' ),
		array( 'إزاي أعرف إن العدسة مقلوبة ولا على وشها المضبوط؟', 'ضعي العدسة على طرف أصبعك؛ إذا كانت أطرافها متجهة للأعلى بشكل كأس منتظم (U) فهي صحيحة، أما إذا كانت الأطراف متجهة للخارج مثل الصحن المفرود فأنها مقلوبة ويجب تعديلها.' ),
		array( 'هل ينفع أنام بالعدسات؟', 'لا يُنصح بالنوم بالعدسات إطلاقاً، حيث يمنع ذلك وصول الأوكسجين للقرنية مما قد يسبب جفافاً شديداً أو التهابات بكتيرية.' ),
	);

	$faq = '<div class="lily-faq" lang="ar" dir="rtl">';
	foreach ( $faq_items as $i => $item ) {
		$faq .= '<details class="lily-faq__item"' . ( 0 === $i ? ' open' : '' ) . '>'
			. '<summary>' . esc_html( $item[0] ) . '</summary>'
			. '<div class="lily-faq__answer"><p>' . esc_html( $item[1] ) . '</p></div>'
			. '</details>';
	}
	$faq .= '</div><p class="lily-faq__more">لديك سؤال آخر؟ تواصلي معنا من خلال صفحة التواصل وسنكون سعداء بمساعدتك.</p>';

	return array(
		'about' => array(
			'title'   => 'About Us',
			'content' => '<p class="lily-page-intro">Lily is a beauty and colored contact lens brand created to make choosing the right look simpler, clearer and more personal.</p>'
				. '<h2>Our Story</h2>'
				. '<p>Lily began with a simple observation: choosing a colored contact lens can feel overwhelming. With so many shades, finishes and collections, it is hard to know which color will truly suit you.</p>'
				. '<p>We focus on original colored contact lenses, distinctive shades and natural-looking results — and we help customers compare different options side by side, so the choice becomes exciting instead of confusing.</p>'
				. '<h2>Our Mission</h2>'
				. '<ul>'
				. '<li>Make color selection easier for every skin tone and eye color.</li>'
				. '<li>Offer clear, honest product comparisons.</li>'
				. '<li>Provide a wide variety of colors for every mood and occasion.</li>'
				. '<li>Help every customer find a look that feels right for her.</li>'
				. '<li>Keep guidance approachable, friendly and free of jargon.</li>'
				. '</ul>'
				. '<h2>Our Vision</h2>'
				. '<p>To become a trusted destination for authentic colored contact lenses and easier color discovery — a place customers return to whenever they want to refresh or reinvent their look.</p>'
				. '<h2>Our Approach</h2>'
				. '<p>Calm. Natural. Fresh. Trustworthy. Approachable. These five words guide how we present every product, write every description and answer every question.</p>',
		),
		'contact' => array(
			'title'   => 'Contact Us',
			'content' => '<p class="lily-page-intro">Questions about an order, a shade, or which lens suits you? The Lily team is here to help.</p>'
				. $contact_info
				. $contact_form,
		),
		'faqs' => array(
			'title'   => 'FAQs',
			'content' => '<p class="lily-page-intro">أكثر الأسئلة شيوعاً حول العدسات اللاصقة الملونة — اختيار اللون، العناية، والاستخدام اليومي.</p>' . $faq,
		),
		'privacy-policy' => array(
			'title'      => 'Privacy Policy',
			'is_privacy' => true,
			'content'    => '<p class="lily-page-intro">This policy explains what information Lily collects and how it is used. <em>[Final legal/privacy details to be added.]</em></p>'
				. '<h2>Introduction</h2><p>We respect your privacy. This temporary policy outlines the categories of information we collect through this website and the general ways we use them. <em>[Final legal/privacy details to be added.]</em></p>'
				. '<h2>Information We Collect</h2><p>When you browse the site we collect basic technical data needed for the website to function. When you place an order we collect the details you provide at checkout: your name, phone number, delivery address, email (optional) and any delivery note you choose to add. <em>[Final legal/privacy details to be added.]</em></p>'
				. '<h2>How We Use Information</h2><p>We use the information we collect to process and deliver orders, contact you about your order, respond to your questions and improve the website experience. <em>[Final legal/privacy details to be added.]</em></p>'
				. '<h2>Orders and Payments</h2><p>Order details are stored so we can fulfil purchases, provide support and keep records required of an online store. Payment handling follows the payment options presented at checkout. <em>[Final payment-processing details to be added.]</em></p>'
				. '<h2>Customer Communication</h2><p>We may contact you by phone or email about your order or to answer messages you send us. We do not send marketing messages without your consent. <em>[Final communication details to be added.]</em></p>'
				. '<h2>Cookies</h2><p>See our <a href="/cookie-policy/">Cookie Policy</a> for a plain explanation of cookies used on this website.</p>'
				. '<h2>Third-Party Services</h2><p>This website runs on standard platforms and services needed to operate an online store. We do not sell your personal data. <em>[Final third-party details to be added.]</em></p>'
				. '<h2>Data Security</h2><p>We take reasonable technical and organisational measures to protect the information we hold. No online system can be guaranteed completely secure. <em>[Final security details to be added.]</em></p>'
				. '<h2>Your Rights</h2><p>You may ask us about the information we hold about you and request corrections. <em>[Final rights details to be added.]</em></p>'
				. '<h2>Policy Updates</h2><p>We may update this policy from time to time; the latest version will always appear on this page.</p>'
				. '<h2>Contact</h2><p>For privacy questions, contact us at ' . esc_html( $email ) . ' or call ' . esc_html( $phone ) . '. <em>(Temporary development contact details.)</em></p>',
		),
		'terms-conditions' => array(
			'title'   => 'Terms &amp; Conditions',
			'content' => '<p class="lily-page-intro">These terms govern your use of the Lily website and any purchase made through it. <em>[Final legal details to be added.]</em></p>'
				. '<h2>Introduction</h2><p>By using this website or placing an order you agree to these terms. Please read them before ordering. <em>[Final legal details to be added.]</em></p>'
				. '<h2>Products and Product Information</h2><p>We describe our products as accurately as possible. Colours may appear slightly different depending on your screen. Product descriptions are for general guidance and are not medical advice.</p>'
				. '<h2>Orders</h2><p>Submitting an order is an offer to purchase. We may contact you to confirm details before dispatch. Orders may be declined if information is incomplete or stock is unavailable.</p>'
				. '<h2>Prices and Payment</h2><p>Prices are shown in Egyptian Pounds (EGP) and include the totals displayed at checkout. Payment is made through the options presented at checkout. <em>[Final payment details to be added.]</em></p>'
				. '<h2>Prescription / Powered Lenses</h2><p>When ordering powered lenses you are responsible for selecting the correct power for your right and left eye. If you are unsure, please consult your eye-care specialist before ordering.</p>'
				. '<h2>Shipping</h2><p>Delivery areas, fees and handling are described in our <a href="/shipping-policy/">Shipping Policy</a>.</p>'
				. '<h2>Returns and Exchanges</h2><p>See our <a href="/returns-exchange/">Returns &amp; Exchange</a> and <a href="/refund-policy/">Refund Policy</a> pages.</p>'
				. '<h2>Customer Responsibilities</h2><p>You are responsible for providing accurate delivery information and for using products according to the guidance on their packaging.</p>'
				. '<h2>Website Use</h2><p>You agree not to misuse this website, attempt to disrupt it, or use its content without permission.</p>'
				. '<h2>Intellectual Property</h2><p>All content, branding and imagery on this website belong to Lily or its licensors. <em>[Final IP details to be added.]</em></p>'
				. '<h2>Changes to These Terms</h2><p>We may update these terms; the current version will always appear on this page.</p>'
				. '<h2>Contact</h2><p>Questions about these terms? Email ' . esc_html( $email ) . ' or call ' . esc_html( $phone ) . '. <em>(Temporary development contact details.)</em></p>',
		),
		'refund-policy' => array(
			'title'   => 'Refund Policy',
			'content' => '<p class="lily-page-intro">This temporary policy outlines how refund requests are handled. <em>[Final refund timeframe to be confirmed.]</em></p>'
				. '<h2>Overview</h2><p>We want every customer to be satisfied. If something went wrong with your order, this page explains how to request a refund. <em>[Final policy details to be confirmed.]</em></p>'
				. '<h2>Eligibility</h2><p>Refunds may be considered for issues covered below. Because contact lenses are personal-care products, eligibility rules are strict. <em>[Final return eligibility rules to be confirmed.]</em></p>'
				. '<h2>Non-Eligible Items</h2><p>Opened or used lenses generally cannot be resold and are therefore generally non-refundable, except where required by law or where the product reached you faulty. <em>[Final eligibility rules to be confirmed.]</em></p>'
				. '<h2>How to Request a Refund</h2><p>Contact us by phone or through the contact page with your order number and a short description of the issue, along with photos if the product arrived damaged or incorrect.</p>'
				. '<h2>Inspection</h2><p>Returned items are inspected before any refund decision is made. <em>[Final inspection process to be confirmed.]</em></p>'
				. '<h2>Approved Refunds</h2><p>If a refund is approved it is issued for the value of the returned item as approved by our team. <em>[Final refund terms to be confirmed.]</em></p>'
				. '<h2>Refund Timing</h2><p><em>[Final refund timeframe to be confirmed.]</em></p>'
				. '<h2>Damaged / Incorrect Orders</h2><p>If your order arrived damaged or you received the wrong item, contact us as soon as possible with photos and your order number so we can make it right.</p>'
				. '<h2>Contact</h2><p>Phone: ' . esc_html( $phone ) . ' — or use the contact page. <em>(Temporary development contact details.)</em></p>',
		),
		'cookie-policy' => array(
			'title'   => 'Cookie Policy',
			'content' => '<p class="lily-page-intro">This policy explains how cookies are used on the Lily website. <em>[Final cookie details to be added.]</em></p>'
				. '<h2>What Cookies Are</h2><p>Cookies are small files stored by your browser that help websites remember information between pages and visits.</p>'
				. '<h2>How We Use Cookies</h2><p>We use cookies to keep the store working correctly, to remember your cart and preferences, and to understand how the website is used.</p>'
				. '<h2>Essential Cookies</h2><p>These cookies are required for basic functions such as cart sessions and checkout. The store cannot work properly without them.</p>'
				. '<h2>Preference Cookies</h2><p>These remember choices such as language to make your visit smoother.</p>'
				. '<h2>Analytics Cookies</h2><p>These help us understand which pages are visited so we can improve the store. <em>[Final analytics details to be added.]</em></p>'
				. '<h2>Managing Cookies</h2><p>You can control or delete cookies through your browser settings. Blocking some cookies may affect how the store works.</p>'
				. '<h2>Policy Updates</h2><p>We may update this policy; the current version always appears on this page.</p>'
				. '<h2>Contact</h2><p>Questions? Email ' . esc_html( $email ) . ' or call ' . esc_html( $phone ) . '. <em>(Temporary development contact details.)</em></p>',
		),
		'shipping-policy' => array(
			'title'   => 'Shipping Policy',
			'content' => '<p class="lily-page-intro">Lily currently delivers within Egypt. Everything you need to know about delivery is summarised here.</p>'
				. '<h2>Shipping Areas</h2><p>At checkout you choose your Governorate / Area from the delivery areas we support across Egypt. Some groups cover several detailed locations, which are shown inside the area selection to help you pick correctly.</p>'
				. '<h2>Delivery Address</h2><p>Please provide a detailed address (area, street, building, floor, apartment, landmark) and a reachable phone number so the courier can find you easily.</p>'
				. '<h2>Shipping Fees</h2><p>Shipping fees are calculated based on the selected delivery area, and the exact fee is shown during checkout before you place your order.</p>'
				. '<h2>Order Processing</h2><p>Orders are prepared after confirmation. <em>[Final processing and delivery times to be confirmed.]</em></p>'
				. '<h2>Delivery Issues</h2><p>If your delivery is delayed or something looks wrong, contact us with your order number and we will follow up with the courier.</p>'
				. '<h2>Incorrect Address</h2><p>Please double-check your address before ordering. If you notice a mistake after ordering, contact us immediately — we will do our best to update it before dispatch.</p>'
				. '<h2>Contact</h2><p>Phone: ' . esc_html( $phone ) . ' — or use the contact page. <em>(Temporary development contact details.)</em></p>',
		),
		'returns-exchange' => array(
			'title'   => 'Returns &amp; Exchange',
			'content' => '<p class="lily-page-intro">Because contact lenses are personal-care products, returns and exchanges follow strict rules. This page explains how requests are handled. <em>[Final policy details to be confirmed.]</em></p>'
				. '<h2>Overview</h2><p>We handle every request fairly and clearly. <em>[Final policy details to be confirmed.]</em></p>'
				. '<h2>Eligibility</h2><p><em>[Final return eligibility rules to be confirmed.]</em> As a general principle, products that have been opened or used usually cannot be accepted back for hygiene reasons, except where the product reached you faulty.</p>'
				. '<h2>Exchange Requests</h2><p>Want a different shade? Contact us and we will let you know what is possible for your order. <em>[Final exchange rules to be confirmed.]</em></p>'
				. '<h2>Return Requests</h2><p>See the <a href="/refund-policy/">Refund Policy</a> for how refunds work after a return is accepted.</p>'
				. '<h2>Damaged or Incorrect Items</h2><p>If an item arrived damaged or you received the wrong product, contact us with photos and your order number as soon as possible and we will make it right.</p>'
				. '<h2>How to Request an Exchange</h2><p>Contact us by phone or through the contact page with your order number and the details of the item you would like to exchange.</p>'
				. '<h2>Inspection</h2><p>Returned items are inspected before a final decision is made. <em>[Final inspection process to be confirmed.]</em></p>'
				. '<h2>Final Decision</h2><p><em>[Final decision criteria to be confirmed.]</em></p>'
				. '<h2>Contact</h2><p>Phone: ' . esc_html( $phone ) . ' — or use the contact page. <em>(Temporary development contact details.)</em></p>',
		),
	);
}

/* ── Contact form: REST-served markup (TranslatePress-safe) ─────────── */

add_action( 'rest_api_init', 'lily_register_contact_form_route' );

/**
 * Serve the contact-form markup through REST so it renders intact.
 */
function lily_register_contact_form_route() {
	register_rest_route(
		'lily/v1',
		'/contact-form',
		array(
			'methods'             => 'GET',
			'permission_callback' => '__return_true',
			'callback'            => 'lily_contact_form_rest',
		)
	);
}

/**
 * Build the contact form markup.
 *
 * @return string
 */
function lily_contact_form_markup() {
	$nonce = wp_create_nonce( 'lily_contact_form' );

	return '<h2>Send Us a Message</h2>'
		. '<form class="lily-contact-form" action="" method="post">'
		. '<input type="hidden" name="lily_contact_handler" value="1">'
		. '<input type="hidden" name="lily_contact_nonce" value="' . esc_attr( $nonce ) . '">'
		. '<p class="lily-form-row"><label for="lily_cf_name">Full Name</label>'
		. '<input class="lily-input" type="text" id="lily_cf_name" name="lily_cf_name" required></p>'
		. '<p class="lily-form-row"><label for="lily_cf_email">Email</label>'
		. '<input class="lily-input" type="email" id="lily_cf_email" name="lily_cf_email" required></p>'
		. '<p class="lily-form-row"><label for="lily_cf_phone">Phone</label>'
		. '<input class="lily-input" type="tel" id="lily_cf_phone" name="lily_cf_phone"></p>'
		. '<p class="lily-form-row"><label for="lily_cf_message">Message</label>'
		. '<textarea class="lily-input" id="lily_cf_message" name="lily_cf_message" rows="5" required></textarea></p>'
		. '<p class="lily-form-hp" aria-hidden="true"><label>Leave this field empty<input type="text" name="lily_cf_hp" tabindex="-1" autocomplete="off"></label></p>'
		. '<p><button type="submit" class="lily-btn">SEND MESSAGE</button></p>'
		. '</form>';
}

/**
 * REST callback.
 *
 * @return WP_REST_Response
 */
function lily_contact_form_rest() {
	return rest_ensure_response( array( 'html' => lily_contact_form_markup() ) );
}

/* ── Contact form handler (native WordPress mail, no plugin) ────────── */

add_action( 'template_redirect', 'lily_handle_contact_message' );

/**
 * Handle the simple contact form posted to the Contact page itself.
 */
function lily_handle_contact_message() {
	if ( empty( $_POST['lily_contact_handler'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['lily_contact_nonce'] ) ), 'lily_contact_form' ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
		return;
	}

	$back = wp_get_referer() ? wp_get_referer() : home_url( '/contact/' );

	// Honeypot: bots fill it, humans never see it.
	if ( ! empty( $_POST['lily_cf_hp'] ) ) {
		wp_safe_redirect( add_query_arg( 'lily_contact', 'sent', $back ) );
		exit;
	}

	$name    = isset( $_POST['lily_cf_name'] ) ? sanitize_text_field( wp_unslash( $_POST['lily_cf_name'] ) ) : '';
	$email   = isset( $_POST['lily_cf_email'] ) ? sanitize_email( wp_unslash( $_POST['lily_cf_email'] ) ) : '';
	$phone   = isset( $_POST['lily_cf_phone'] ) ? sanitize_text_field( wp_unslash( $_POST['lily_cf_phone'] ) ) : '';
	$message = isset( $_POST['lily_cf_message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['lily_cf_message'] ) ) : '';

	if ( ! $name || ! $message || ! is_email( $email ) ) {
		wp_safe_redirect( add_query_arg( 'lily_contact', 'error', $back ) );
		exit;
	}

	$body = sprintf(
		"Name: %s\nEmail: %s\nPhone: %s\n\n%s",
		$name,
		$email,
		$phone,
		$message
	);

	wp_mail(
		get_option( 'admin_email' ),
		sprintf( '[Lily Contact] Message from %s', $name ),
		$body,
		array( 'Reply-To: ' . $name . ' <' . $email . '>' )
	);

	wp_safe_redirect( add_query_arg( 'lily_contact', 'sent', $back ) );
	exit;
}
