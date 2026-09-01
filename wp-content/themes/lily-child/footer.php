<?php
/**
 * Site footer.
 *
 * Assigned WordPress menus always win. When a footer menu location has no
 * menu assigned, tasteful native Lily fallbacks keep the footer complete:
 * real WooCommerce shop categories and pages resolved from Navigation
 * Settings — no fake links, no duplicate management systems.
 *
 * @package Lily
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Resolve one fallback footer link list.
 *
 * @return array[] Array of [ 'label' => string, 'url' => string ].
 */
function lily_footer_fallback_links( $group ) {
	$links = array();

	if ( 'shop' === $group ) {
		foreach ( array(
			array( 'colored-lenses', __( 'Colored Lenses', 'lily' ) ),
			array( 'clear-lenses', __( 'Clear Lenses', 'lily' ) ),
			array( 'accessories-lens-care', __( 'Accessories & Lens Care', 'lily' ) ),
			array( 'accessories', __( 'Accessories & Lens Care', 'lily' ) ),
		) as $candidate ) {
			$term = get_term_by( 'slug', $candidate[0], 'product_cat' );

			if ( $term && ! is_wp_error( $term ) ) {
				$url = get_term_link( $term );

				if ( ! is_wp_error( $url ) ) {
					$links[] = array(
						'label' => $candidate[1],
						'url'   => $url,
					);
				}

				// The accessories group only needs one resolved category.
				if ( 'accessories' === $candidate[0] || 'accessories-lens-care' === $candidate[0] ) {
					break;
				}
			}
		}
	} elseif ( 'help' === $group ) {
		$pages = array(
			array( 'faqs_page', array( 'faqs', 'faq' ), __( 'FAQs', 'lily' ) ),
			array( 'contact_page', array( 'contact', 'contact-us' ), __( 'Contact Us', 'lily' ) ),
		);

		foreach ( $pages as $page ) {
			$url = lily_nav_page_url( $page[0], $page[1] );

			if ( $url ) {
				$links[] = array(
					'label' => $page[2],
					'url'   => $url,
				);
			}
		}

	$links[] = array(
		'label' => __( 'Find Your Best Lenses', 'lily' ),
		'url'   => home_url( '/#find-your-best-lenses' ),
	);
	} elseif ( 'company' === $group ) {
		$url = lily_nav_page_url( 'about_page', array( 'about', 'about-us', 'about-lily' ) );

		if ( $url ) {
			$links[] = array(
				'label' => __( 'About Lily', 'lily' ),
				'url'   => $url,
			);
		}
	}

	return $links;
}

$lily_footer_groups = array(
	'footer_shop'      => array(
		'heading'   => __( 'Shop', 'lily' ),
		'fallbacks' => lily_footer_fallback_links( 'shop' ),
	),
	'footer_service'   => array(
		'heading'   => __( 'Help', 'lily' ),
		'fallbacks' => lily_footer_fallback_links( 'help' ),
	),
	'footer_secondary' => array(
		'heading'   => __( 'Company', 'lily' ),
		'fallbacks' => lily_footer_fallback_links( 'company' ),
	),
);

// Lens Finder CTA reuses the Navigation Settings destination when available.
$lily_finder_show = lily_nav_get_option( 'show_find', 1 );
$lily_finder_url  = trim( (string) lily_nav_get_option( 'find_url', '' ) );

if ( '' === $lily_finder_url ) {
	$lily_finder_url = home_url( '/#find-your-best-lenses' );
}

// Real Lily contact details and social profiles (client-provided values).
$lily_whatsapp_number = '01060760098';
$lily_whatsapp_url    = 'https://wa.me/201060760098';
$lily_service_url     = 'tel:+201060760098';

$lily_contact_links = array(
	array(
		'label'  => __( 'WhatsApp', 'lily' ),
		'url'    => $lily_whatsapp_url,
		'number' => $lily_whatsapp_number,
		'extra'  => '',
	),
	array(
		'label'  => __( 'Customer Service & Complaints', 'lily' ),
		'url'    => $lily_service_url,
		'number' => $lily_whatsapp_number,
		'extra'  => '',
	),
);

$lily_social_links = array(
	array( 'label' => __( 'Facebook', 'lily' ), 'url' => 'https://www.facebook.com/share/1BhEmgfAvG/?mibextid=wwXIfr' ),
	array( 'label' => __( 'Facebook Group', 'lily' ), 'url' => 'https://www.facebook.com/share/g/195YLb3DNv/?mibextid=wwXIfr' ),
	array( 'label' => __( 'TikTok', 'lily' ), 'url' => 'https://www.tiktok.com/@lily_original_lenses?_r=1&_t=ZS-992IHcql4B6' ),
	array( 'label' => __( 'YouTube', 'lily' ), 'url' => 'https://youtube.com/@lilyoriginallenses?si=g5aTwOvLzAkibB8c' ),
);

/*
 * Policy and legal navigation. Each item resolves its real permalink once a
 * published page exists (Navigation Settings page fields or page slugs);
 * until then it renders as a non-clickable placeholder — no fake URLs.
 */
$lily_policy_items = array(
	'footer_service'   => array(
		array( 'label' => __( 'Shipping Policy', 'lily' ), 'slugs' => array( 'shipping-policy', 'shipping' ) ),
		array( 'label' => __( 'Returns & Exchange', 'lily' ), 'slugs' => array( 'returns-exchange', 'returns-and-exchange', 'return-policy' ) ),
	),
	'footer_secondary' => array(
		array( 'label' => __( 'Privacy Policy', 'lily' ), 'privacy' => true ),
		array( 'label' => __( 'Terms & Conditions', 'lily' ), 'slugs' => array( 'terms', 'terms-conditions', 'terms-and-conditions' ) ),
		array( 'label' => __( 'Refund Policy', 'lily' ), 'slugs' => array( 'refund-policy', 'refunds' ) ),
		array( 'label' => __( 'Cookie Policy', 'lily' ), 'slugs' => array( 'cookie-policy', 'cookies-policy' ) ),
	),
);

foreach ( $lily_policy_items as $lily_policy_group => $lily_group_items ) {
	foreach ( $lily_group_items as $lily_item ) {
		$url = empty( $lily_item['privacy'] )
			? lily_nav_page_url( '', isset( $lily_item['slugs'] ) ? $lily_item['slugs'] : array() )
			: get_privacy_policy_url();

		$lily_footer_groups[ $lily_policy_group ]['fallbacks'][] = array(
			'label'       => $lily_item['label'],
			'url'         => $url,
			'placeholder' => '' === $url,
		);
	}
}
?>
</main>
<footer class="lily-site-footer" role="contentinfo">
	<?php lily_container_open( 'lily-site-footer__inner' ); ?>
		<div class="lily-footer-brand">
			<?php
			if ( has_custom_logo() ) {
				the_custom_logo();
			} else {
				printf(
					'<a class="lily-site-title" href="%1$s">%2$s</a>',
					esc_url( home_url( '/' ) ),
					esc_html( get_bloginfo( 'name' ) )
				);
			}
			?>
			<p class="lily-footer-since"><?php echo is_rtl() ? esc_html__( 'منذ 2014', 'lily' ) : esc_html__( 'Since 2014', 'lily' ); ?></p>
			<p class="lily-footer-tagline">
				<?php
				// The Site Identity tagline stays the configured source; the
				// editorial copy below is temporary development fallback.
				$lily_tagline = trim( (string) get_bloginfo( 'description' ) );

				if ( '' === $lily_tagline ) {
					$lily_tagline = is_rtl()
						? 'منذ 2014، تقدم Lily عدسات مختارة بعناية ومستلزمات العناية بالعدسات، برؤية بسيطة تجمع بين الجودة والأناقة والاهتمام بالتفاصيل.'
						: 'Since 2014, Lily has been bringing carefully selected lenses and everyday eye-care essentials together with a simple, thoughtful approach to beauty and style.';
				}

				echo esc_html( $lily_tagline );
				?>
			</p>
			<?php if ( $lily_finder_show ) : ?>
				<a class="lily-footer-cta" href="<?php echo esc_url( $lily_finder_url ); ?>">
					<?php esc_html_e( 'Find Your Best Lenses', 'lily' ); ?><span class="lily-footer-cta__arrow" aria-hidden="true">&rarr;</span>
				</a>
			<?php endif; ?>

			<nav class="lily-footer-menu lily-footer-contact" aria-label="<?php esc_attr_e( 'Contact', 'lily' ); ?>">
				<h2><?php esc_html_e( 'Contact', 'lily' ); ?></h2>
				<ul>
					<?php foreach ( $lily_contact_links as $lily_contact ) : ?>
						<li>
							<a href="<?php echo esc_url( $lily_contact['url'] ); ?>"<?php echo 0 === strpos( $lily_contact['url'], 'https://wa.me' ) ? ' target="_blank" rel="noopener"' : ''; ?>>
								<?php echo esc_html( $lily_contact['label'] ); ?>
								<span class="lily-footer-number" dir="ltr"><?php echo esc_html( $lily_contact['number'] ); ?></span>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			</nav>
		</div>

		<div class="lily-footer-menus">
			<?php foreach ( $lily_footer_groups as $location => $group ) : ?>
				<nav class="lily-footer-menu" aria-label="<?php echo esc_attr( $group['heading'] ); ?>">
					<h2><?php echo esc_html( $group['heading'] ); ?></h2>
					<?php if ( has_nav_menu( $location ) ) : ?>
						<?php
						wp_nav_menu(
							array(
								'theme_location' => $location,
								'container'      => false,
								'fallback_cb'    => false,
								'depth'          => 1,
							)
						);
						?>
					<?php elseif ( ! empty( $group['fallbacks'] ) ) : ?>
						<ul>
							<?php foreach ( $group['fallbacks'] as $link ) : ?>
								<li>
									<?php if ( ! empty( $link['placeholder'] ) ) : ?>
										<span class="lily-footer-placeholder"><?php echo esc_html( $link['label'] ); ?></span>
									<?php else : ?>
										<a href="<?php echo esc_url( $link['url'] ); ?>"><?php echo esc_html( $link['label'] ); ?></a>
									<?php endif; ?>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</nav>
			<?php endforeach; ?>

			<?php if ( ! empty( $lily_social_links ) ) : ?>
				<nav class="lily-footer-menu lily-footer-social" aria-label="<?php esc_attr_e( 'Follow Us', 'lily' ); ?>">
					<h2><?php esc_html_e( 'Follow Us', 'lily' ); ?></h2>
					<ul>
						<?php foreach ( $lily_social_links as $lily_social ) : ?>
							<li><a href="<?php echo esc_url( $lily_social['url'] ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $lily_social['label'] ); ?></a></li>
						<?php endforeach; ?>
					</ul>
				</nav>
			<?php endif; ?>
		</div>

		<div class="lily-footer-bottom">
			<p class="lily-copyright">
				<?php
				printf(
					/* translators: 1: year, 2: site name. */
					esc_html__( 'Copyright %1$s %2$s. All rights reserved.', 'lily' ),
					esc_html( gmdate( 'Y' ) ),
					esc_html( get_bloginfo( 'name' ) )
				);
				?>
			</p>
			<p class="lily-footer-note"><?php esc_html_e( 'Cash on Delivery Available', 'lily' ); ?></p>
		</div>
	<?php lily_container_close(); ?>
</footer>
<?php wp_footer(); ?>
</body>
</html>
