# Lily Child Theme

Fresh WooCommerce child theme for the Lily contact lenses storefront.

## Required dependencies

- Astra parent theme
- WooCommerce
- TranslatePress

The child theme includes an admin notice when WooCommerce is not active.

## Homepage order

1. Announcement Bar
2. Navbar
3. Hero
4. Brands
5. Shop by Collections
6. Shop by Colors
7. Best Sellers
8. Find Your Best Lenses
9. Footer

## Client editing workflow

- Announcement, Hero, Brands, Collections selection, Colors selection, Best Sellers settings, and Lens Finder intro copy are managed in **Lily > Homepage Settings**.
- Collections use WooCommerce product categories as the source of truth.
- Colors use the WooCommerce `pa_color` attribute terms as the source of truth.
- Color marketing images are edited on each `pa_color` term using the native **Color Image** term field.
- Best Sellers are controlled by assigning products to the selected WooCommerce product category.
- Lens Finder product matching uses WooCommerce product attributes plus native product meta fields.

## Notes

No paid plugin dependency is required. No product IDs, product names, prices, collection names, or homepage color cards are hardcoded in templates. Visible theme strings are wrapped for translation and the CSS uses logical properties where practical for RTL support.

## Design system (v0.2.0)

The visual layer lives in `assets/css/lily.css` and is built from tokens:

- Palette: warm ivory surfaces (`--lily-bg`, `--lily-surface`, `--lily-blush`), espresso ink (`--lily-text`, `--lily-espresso`), caramel accent (`--lily-accent`).
- Typography: Fraunces (display) + Jost (UI/body) via Google Fonts; Almarai is swapped in automatically on Arabic/RTL requests (`assets/css/rtl.css`).
- One shared container: `.lily-container` (max 1280px) used by every section.
- Buttons: `.lily-button` (primary / `--secondary` / `--small`), pill shaped.
- Cards: collections, colors and products share one radius/shadow/hover system.

All homepage content is controlled from **Lily > Homepage Settings**, WooCommerce terms and product data. Sections with no configured data hide automatically.

## Site settings changed during visual implementation

- TranslatePress "Floating language switcher" was disabled (`trp_language_switcher_settings[floater][enabled] = false`) because the header already contains the `[language-switcher]` shortcode. Re-enable in TranslatePress if a floating switcher is wanted.
- Astra "Scroll to top" was disabled (Customizer setting `scroll-to-top-enable`) because its default styling clashed with the Lily design. Re-enable in Customizer if wanted.

