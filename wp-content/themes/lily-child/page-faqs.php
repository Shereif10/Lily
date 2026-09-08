<?php
/**
 * Lily FAQ page (slug: faqs).
 *
 * FAQ-only template: centered editorial masthead plus a quiet button
 * accordion. Every visible string reads from the Lily dashboard (FAQ
 * Page tab, same settings system as the homepage/about settings) with
 * the approved English FAQ content as fallback. Uses the existing
 * global header/footer and the shared lily-container helper.
 *
 * @package Lily
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* FAQ-only body class so the FAQ CSS scope applies without touching globals. */
add_filter(
	'body_class',
	static function ( $classes ) {
		$classes[] = 'lily-faq-page';
		return $classes;
	}
);

get_header();

$lily_faq_fallbacks = array(
	array(
		'q' => __( 'How do I choose the right lens color for my skin tone and eye color?', 'lily' ),
		'a' => __( 'Warm or olive skin tones suit honey, olive green, and warm brown shades. Fair skin tones suit gray, blue, and emerald green. If your natural eye color is dark, choose colors with stronger pigmentation to cover the original color more clearly.', 'lily' ),
	),
	array(
		'q' => __( 'What is the difference between limbal-ring lenses and lenses without a defined rim?', 'lily' ),
		'a' => __( 'Defined lenses have a dark ring around the outer edge that makes the eyes look larger and more defined, while lenses without a defined rim give a more natural look that blends with your original eye color without changing the appearance of the pupil size.', 'lily' ),
	),
	array(
		'q' => __( 'How do I take care of my lenses and protect them from damage?', 'lily' ),
		'a' => __( 'Wash and dry your hands thoroughly before touching your lenses. Use a sterile contact-lens solution specifically intended for cleaning them daily, and never wash the lenses or their case with tap water. It is recommended to replace the lens case every 1 to 3 months.', 'lily' ),
	),
	array(
		'q' => __( 'How many hours can I wear contact lenses per day?', 'lily' ),
		'a' => __( 'It is recommended to wear contact lenses for approximately 6 to 8 hours per day to help avoid eye fatigue or dryness. They should be removed immediately before sleeping or swimming.', 'lily' ),
	),
	array(
		'q' => __( 'What should I do if I feel burning or a sharp sensation while wearing my lenses?', 'lily' ),
		'a' => __( 'Remove the lens immediately and inspect it for any debris or cracks. Clean it thoroughly with contact-lens solution and make sure it is not inside out before putting it back in. If the irritation continues, allow your eye to rest and consult a doctor.', 'lily' ),
	),
	array(
		'q' => __( 'How can I tell if my lens is inside out?', 'lily' ),
		'a' => __( 'Place the lens on the tip of your finger. If the edges point upward like a regular cup or "U" shape, the lens is positioned correctly. If the edges flare outward like a flat plate, the lens is inside out and should be turned over.', 'lily' ),
	),
	array(
		'q' => __( 'Can I sleep while wearing contact lenses?', 'lily' ),
		'a' => __( 'Sleeping while wearing contact lenses is not recommended, as it can reduce oxygen reaching the cornea and may cause severe dryness or bacterial infections.', 'lily' ),
	),
);

$lily_faq_items = array();
foreach ( $lily_faq_fallbacks as $lily_index => $lily_fallback ) {
	$lily_num      = $lily_index + 1;
	$lily_question = lily_get_option( "faq_{$lily_num}_q", $lily_fallback['q'] );
	$lily_answer   = lily_get_option( "faq_{$lily_num}_a", $lily_fallback['a'] );

	if ( '' === trim( (string) $lily_question ) ) {
		continue; // Never render an empty question row.
	}

	$lily_faq_items[] = array(
		'q' => $lily_question,
		'a' => $lily_answer,
	);
}
?>

<main id="primary" class="site-main lily-faqs">
	<?php lily_container_open( 'lily-faqs__inner' ); ?>

		<header class="lily-faqs-masthead">
			<h1 class="lily-faqs-masthead__title"><?php echo esc_html( lily_get_option( 'faq_title', esc_html__( 'FAQs', 'lily' ) ) ); ?></h1>
			<p class="lily-faqs-masthead__sub"><?php echo esc_html( lily_get_option( 'faq_subtitle', esc_html__( 'Frequently asked questions about colored contact lenses — choosing the right color, care, and everyday use.', 'lily' ) ) ); ?></p>
		</header>

		<div class="lily-faqs__list" data-lily-faqs>
			<?php foreach ( $lily_faq_items as $lily_index => $lily_item ) : ?>
				<div class="lily-faqs__item" data-lily-faq-item>
					<h2 class="lily-faqs__qwrap">
						<button class="lily-faqs__q" type="button" aria-expanded="false" aria-controls="lily-faq-a-<?php echo esc_attr( $lily_index + 1 ); ?>" id="lily-faq-q-<?php echo esc_attr( $lily_index + 1 ); ?>" data-lily-faq-toggle>
							<span class="lily-faqs__qtext"><?php echo esc_html( $lily_item['q'] ); ?></span>
							<span class="lily-faqs__icon" aria-hidden="true"></span>
						</button>
					</h2>
					<div class="lily-faqs__acollapse" id="lily-faq-a-<?php echo esc_attr( $lily_index + 1 ); ?>" role="region" aria-labelledby="lily-faq-q-<?php echo esc_attr( $lily_index + 1 ); ?>" data-lily-faq-panel>
						<div class="lily-faqs__ainner">
							<p><?php echo esc_html( $lily_item['a'] ); ?></p>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>

	<?php lily_container_close(); ?>
</main>

<script>
(function () {
	'use strict';
	var list = document.querySelector('[data-lily-faqs]');
	if (!list || list.dataset.lilyFaqsInit) { return; }
	list.dataset.lilyFaqsInit = '1';

	list.addEventListener('click', function (event) {
		var btn = event.target.closest('[data-lily-faq-toggle]');
		if (!btn || !list.contains(btn)) { return; }
		var item = btn.closest('[data-lily-faq-item]');
		if (!item) { return; }
		var open = item.classList.toggle('is-open');
		btn.setAttribute('aria-expanded', open ? 'true' : 'false');
	});
})();
</script>

<?php
get_footer();
