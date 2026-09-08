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
		'heading'   => lily_footer_ml( lily_get_footer_settings()['shop_heading'], lily_get_footer_settings()['shop_heading_ar'] ?? '' ) ?: __( 'Shop', 'lily' ),
		'fallbacks' => lily_footer_fallback_links( 'shop' ),
	),
	'footer_service'   => array(
		'heading'   => lily_footer_ml( lily_get_footer_settings()['help_heading'], lily_get_footer_settings()['help_heading_ar'] ?? '' ) ?: __( 'Help', 'lily' ),
		'fallbacks' => lily_footer_fallback_links( 'help' ),
	),
	'footer_secondary' => array(
		'heading'   => lily_footer_ml( lily_get_footer_settings()['about_heading'], lily_get_footer_settings()['about_heading_ar'] ?? '' ) ?: __( 'Company', 'lily' ),
		'fallbacks' => lily_footer_fallback_links( 'company' ),
	),
);

// Lens Finder CTA reuses the Navigation Settings destination when available.
$lily_finder_show = lily_nav_get_option( 'show_find', 1 );
$lily_finder_url  = trim( (string) lily_nav_get_option( 'find_url', '' ) );

if ( '' === $lily_finder_url ) {
	$lily_finder_url = home_url( '/#find-your-best-lenses' );
}

/* Footer band content — dashboard-controlled (same values as before by default). */
$lily_footer_settings = lily_get_footer_settings();

$lily_footer_since = lily_footer_ml( $lily_footer_settings['since_text'] ?? '', $lily_footer_settings['since_text_ar'] ?? '' );
$lily_footer_cta_label = lily_footer_ml( $lily_footer_settings['cta_label'] ?? '', $lily_footer_settings['cta_label_ar'] ?? '' );

$lily_whatsapp_number = (string) ( $lily_footer_settings['whatsapp_number'] ?? '01060760098' );
$lily_service_number  = (string) ( $lily_footer_settings['service_number'] ?? '01060760098' );

$lily_contact_links = array(
	array(
		'label'  => lily_footer_ml( $lily_footer_settings['whatsapp_label'] ?? '', $lily_footer_settings['whatsapp_label_ar'] ?? '' ) ?: __( 'WhatsApp', 'lily' ),
		'url'    => lily_footer_whatsapp_url( $lily_whatsapp_number ),
		'number' => $lily_whatsapp_number,
		'extra'  => '',
	),
	array(
		'label'  => lily_footer_ml( $lily_footer_settings['service_label'] ?? '', $lily_footer_settings['service_label_ar'] ?? '' ) ?: __( 'Customer Service & Complaints', 'lily' ),
		'url'    => lily_footer_tel_url( $lily_service_number ),
		'number' => $lily_service_number,
		'extra'  => '',
	),
);

/* Social profiles — icons only, dashboard-controlled URLs, rendered in a
 * fixed order. The WhatsApp destination derives from the canonical footer
 * WhatsApp number (Egyptian local numbers get the +20 prefix automatically). */
$lily_social_icon_urls = array(
	'instagram'      => isset( $lily_footer_settings['social_instagram']['url'] ) ? trim( (string) $lily_footer_settings['social_instagram']['url'] ) : '',
	'youtube'        => isset( $lily_footer_settings['social_youtube']['url'] ) ? trim( (string) $lily_footer_settings['social_youtube']['url'] ) : '',
	'facebook'       => isset( $lily_footer_settings['social_facebook']['url'] ) ? trim( (string) $lily_footer_settings['social_facebook']['url'] ) : '',
	'tiktok'         => isset( $lily_footer_settings['social_tiktok']['url'] ) ? trim( (string) $lily_footer_settings['social_tiktok']['url'] ) : '',
	'whatsapp'       => lily_footer_whatsapp_url( $lily_whatsapp_number ),
	'facebook_group' => isset( $lily_footer_settings['social_facebook_group']['url'] ) ? trim( (string) $lily_footer_settings['social_facebook_group']['url'] ) : '',
);

$lily_social_icons = array(
	'instagram'      => array(
		'label'      => __( 'Follow Lily on Instagram', 'lily' ),
		'enabled'    => ! empty( $lily_footer_settings['social_instagram']['enabled'] ),
		'path'       => 'M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z',
	),
	'youtube'        => array(
		'label'      => __( 'Subscribe to Lily on YouTube', 'lily' ),
		'enabled'    => ! empty( $lily_footer_settings['social_youtube']['enabled'] ),
		'path'       => 'M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z',
	),
	'facebook'       => array(
		'label'      => __( 'Follow Lily on Facebook', 'lily' ),
		'enabled'    => ! empty( $lily_footer_settings['social_facebook']['enabled'] ),
		'path'       => 'M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z',
	),
	'tiktok'         => array(
		'label'      => __( 'Follow Lily on TikTok', 'lily' ),
		'enabled'    => ! empty( $lily_footer_settings['social_tiktok']['enabled'] ),
		'path'       => 'M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z',
	),
	'whatsapp'       => array(
		'label'      => __( 'Contact Lily on WhatsApp', 'lily' ),
		'enabled'    => '' !== $lily_social_icon_urls['whatsapp'],
		'path'       => 'M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z',
	),
	'facebook_group' => array(
		'label'      => __( 'Join Lily Facebook Group', 'lily' ),
		'enabled'    => ! empty( $lily_footer_settings['social_facebook_group']['enabled'] ),
		'path'       => 'M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5s-3 1.34-3 3 1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z',
	),
);

/*
 * Policy and legal navigation. Each item resolves its real permalink once a
 * published page exists (Navigation Settings page fields or page slugs);
 * until then it renders as a non-clickable placeholder — no fake URLs.
 */
$lily_policy_items = array(
	'footer_service'   => array(
		array( 'label' => __( 'Shipping & Delivery Policy', 'lily' ), 'slugs' => array( 'shipping-delivery-policy', 'shipping-policy', 'shipping' ) ),
		array( 'label' => __( 'Returns & Exchange Policy', 'lily' ), 'slugs' => array( 'returns-exchange', 'returns-and-exchange', 'return-policy' ) ),
	),
	'footer_secondary' => array(
		array( 'label' => __( 'Privacy Policy', 'lily' ), 'privacy' => true ),
		array( 'label' => __( 'Terms & Conditions', 'lily' ), 'slugs' => array( 'terms', 'terms-conditions', 'terms-and-conditions' ) ),
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
			$lily_footer_brand_logo = absint( $lily_footer_settings['brand_logo'] ?? 0 );
			if ( $lily_footer_brand_logo && 'attachment' === get_post_type( $lily_footer_brand_logo ) ) {
				printf( '<a class="lily-footer-logo" href="%1$s">%2$s</a>', esc_url( home_url( '/' ) ), wp_get_attachment_image( $lily_footer_brand_logo, 'full' ) );
			} elseif ( has_custom_logo() ) {
				the_custom_logo();
			} else {
				printf(
					'<a class="lily-site-title" href="%1$s">%2$s</a>',
					esc_url( home_url( '/' ) ),
					esc_html( get_bloginfo( 'name' ) )
				);
			}
			?>
			<p class="lily-footer-since"><?php echo esc_html( '' !== trim( $lily_footer_since ) ? $lily_footer_since : __( 'Since 2014', 'lily' ) ); ?></p>
			<?php
			$lily_footer_taglines = array(
				lily_footer_ml( $lily_footer_settings['brand_tagline_1'] ?? '', $lily_footer_settings['brand_tagline_1_ar'] ?? '' ),
				lily_footer_ml( $lily_footer_settings['brand_tagline_2'] ?? '', $lily_footer_settings['brand_tagline_2_ar'] ?? '' ),
				lily_footer_ml( $lily_footer_settings['brand_tagline_3'] ?? '', $lily_footer_settings['brand_tagline_3_ar'] ?? '' ),
			);
			$lily_footer_taglines = array_filter(
				$lily_footer_taglines,
				static function ( $line ) {
					return '' !== trim( (string) $line );
				}
			);
			?>
			<?php if ( ! empty( $lily_footer_taglines ) ) : ?>
				<p class="lily-footer-tagline">
					<?php echo esc_html( implode( ' ', $lily_footer_taglines ) ); ?>
				</p>
			<?php else : ?>
			<p class="lily-footer-tagline">
				<?php
				// The Site Identity tagline stays the configured source; the
				// editorial copy below is temporary development fallback.
				$lily_tagline = trim( (string) get_bloginfo( 'description' ) );

				if ( '' === $lily_tagline ) {
					$lily_is_ar = ( function_exists( 'lily_is_rtl_request' ) && lily_is_rtl_request() ) || is_rtl();
					$lily_tagline = $lily_is_ar
						? 'منذ 2014، تقدم Lily عدسات مختارة بعناية ومستلزمات العناية بالعدسات، برؤية بسيطة تجمع بين الجودة والأناقة والاهتمام بالتفاصيل.'
						: esc_html__( 'Since 2014, Lily has been bringing carefully selected lenses and everyday eye-care essentials together with a simple, thoughtful approach to beauty and style.', 'lily' );
				}

				echo esc_html( $lily_tagline );
				?>
			</p>
			<?php endif; ?>
			<?php if ( $lily_finder_show ) : ?>
				<a class="lily-footer-cta" href="<?php echo esc_url( $lily_finder_url ); ?>">
					<?php echo esc_html( '' !== trim( $lily_footer_cta_label ) ? $lily_footer_cta_label : __( 'Find Your Best Lenses', 'lily' ) ); ?><span class="lily-footer-cta__arrow" aria-hidden="true">&rarr;</span>
				</a>
			<?php endif; ?>

			<nav class="lily-footer-menu lily-footer-contact" aria-label="<?php echo esc_attr( lily_footer_ml( $lily_footer_settings['contact_heading'] ?? '', $lily_footer_settings['contact_heading_ar'] ?? '' ) ?: __( 'Contact', 'lily' ) ); ?>">
				<h2><?php echo esc_html( lily_footer_ml( $lily_footer_settings['contact_heading'] ?? '', $lily_footer_settings['contact_heading_ar'] ?? '' ) ?: __( 'Contact', 'lily' ) ); ?></h2>
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

			<?php if ( ! empty( $lily_social_icons ) ) : ?>
				<nav class="lily-footer-menu lily-footer-social" aria-label="<?php esc_attr_e( 'Follow Us', 'lily' ); ?>">
					<h2><?php esc_html_e( 'Follow Us', 'lily' ); ?></h2>
					<ul>
						<?php foreach ( $lily_social_icons as $lily_social_key => $lily_social ) : ?>
							<?php
							if ( empty( $lily_social['enabled'] ) || '' === trim( (string) ( $lily_social_icon_urls[ $lily_social_key ] ?? '' ) ) ) {
								continue;
							}
							?>
							<li>
								<a
									href="<?php echo esc_url( $lily_social_icon_urls[ $lily_social_key ] ); ?>"
									target="_blank"
									rel="noopener noreferrer"
									aria-label="<?php echo esc_attr( $lily_social['label'] ); ?>"
									title="<?php echo esc_attr( $lily_social['label'] ); ?>"
								>
									<svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor" aria-hidden="true" focusable="false"><path d="<?php echo esc_attr( $lily_social['path'] ); ?>"/></svg>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				</nav>
			<?php endif; ?>
		</div>

		<div class="lily-footer-bottom">
			<p class="lily-copyright">
				<?php
				$lily_copyright = lily_footer_ml( $lily_footer_settings['copyright_text'] ?? '', $lily_footer_settings['copyright_text_ar'] ?? '' );

				if ( '' !== trim( $lily_copyright ) ) {
					echo esc_html( $lily_copyright );
				} else {
					printf(
						/* translators: 1: year, 2: site name. */
						esc_html__( 'Copyright %1$s %2$s. All rights reserved.', 'lily' ),
						esc_html( gmdate( 'Y' ) ),
						esc_html( get_bloginfo( 'name' ) )
					);
				}
				?>
			</p>
			<p class="lily-footer-note"><?php echo esc_html( lily_footer_ml( $lily_footer_settings['bottom_note'] ?? '', $lily_footer_settings['bottom_note_ar'] ?? '' ) ?: __( 'Cash on Delivery Available', 'lily' ) ); ?></p>
		</div>
	<?php lily_container_close(); ?>
</footer>
<?php wp_footer(); ?>
</body>
</html>
