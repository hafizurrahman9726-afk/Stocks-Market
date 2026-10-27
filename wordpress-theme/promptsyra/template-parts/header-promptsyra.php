<?php
/**
 * PromptSyra Scoped Header Partial
 *
 * Implements:
 * - PromptSyra.com branding (custom logo + wordmark)
 * - Required Final Menu Structure:
 *   1. Home
 *   2. AI Photo Prompts
 *   3. Trending Prompts
 *   4. Social Media Prompts
 *   5. Important Pages dropdown (About Us, Contact Us, Privacy Policy, Disclaimer, Terms & Conditions)
 * - Accessible Search drawer control + prominent "Explore Prompts" CTA
 * - Keyboard accessible ARIA attributes and responsive mobile navigation drawer
 *
 * @package PromptSyra
 * @version 2.4.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// 1. Resolve 3 Primary Categories with live alias resolution
$ps_cat_photo    = promptsyra_resolve_category_data( 'ai-photo-prompts' );
$ps_cat_trending = promptsyra_resolve_category_data( 'trending-prompts' );
$ps_cat_social   = promptsyra_resolve_category_data( 'social-media-prompts' );

$ps_url_photo    = ! empty( $ps_cat_photo['url'] ) ? $ps_cat_photo['url'] : home_url( '/category/ai-photo-prompts/' );
$ps_url_trending = ! empty( $ps_cat_trending['url'] ) ? $ps_cat_trending['url'] : home_url( '/category/trending-prompts/' );
$ps_url_social   = ! empty( $ps_cat_social['url'] ) ? $ps_cat_social['url'] : home_url( '/category/ai-social-media-prompts/' );

// 2. Active category detection
$ps_is_cat_photo    = is_category( array( 'ai-photo-prompts' ) );
$ps_is_cat_trending = is_category( array( 'trending-prompts' ) );
$ps_is_cat_social   = is_category( array( 'social-media-prompts', 'ai-social-media-prompts' ) );

// 3. Resolve 5 Important Pages
$ps_about_url      = promptsyra_get_verified_page_url( array( 'about-us', 'about' ) );
$ps_contact_url    = promptsyra_get_verified_page_url( array( 'contact-us', 'contact' ) );
$ps_privacy_url    = promptsyra_get_verified_page_url( array( 'privacy-policy', 'privacy' ) );
$ps_disclaimer_url = promptsyra_get_verified_page_url( array( 'disclaimer' ) );
$ps_terms_url      = promptsyra_get_verified_page_url( array( 'terms-and-conditions', 'terms-conditions', 'terms' ) );

// Resilient fallbacks ensuring all 5 links always point to real published page routes
if ( empty( $ps_about_url ) ) {
	$ps_about_url = home_url( '/about-us/' );
}
if ( empty( $ps_contact_url ) ) {
	$ps_contact_url = home_url( '/contact-us/' );
}
if ( empty( $ps_privacy_url ) ) {
	$ps_privacy_url = home_url( '/privacy-policy/' );
}
if ( empty( $ps_disclaimer_url ) ) {
	$ps_disclaimer_url = home_url( '/disclaimer/' );
}
if ( empty( $ps_terms_url ) ) {
	$ps_terms_url = home_url( '/terms-and-conditions/' );
}

$ps_is_about      = is_page( array( 'about-us', 'about' ) );
$ps_is_contact    = is_page( array( 'contact-us', 'contact' ) );
$ps_is_privacy    = is_page( array( 'privacy-policy', 'privacy' ) );
$ps_is_disclaimer = is_page( 'disclaimer' );
$ps_is_terms      = is_page( array( 'terms-and-conditions', 'terms-conditions', 'terms' ) );
$ps_is_important  = $ps_is_about || $ps_is_contact || $ps_is_privacy || $ps_is_disclaimer || $ps_is_terms;

$ps_cta_label   = get_theme_mod( 'promptsyra_hero_cta_label', __( 'Explore Prompts', 'promptsyra' ) );
$ps_cta_url_raw = get_theme_mod( 'promptsyra_hero_cta_url', '#featured-prompts' );
$ps_cta_url     = ! empty( $ps_cta_url_raw ) ? $ps_cta_url_raw : '#featured-prompts';
?>
<header class="ps-site-header" data-ps-header>
	<div class="ps-container ps-header-inner">
		<div class="ps-brand-zone">
			<?php if ( has_custom_logo() ) : ?>
				<div class="ps-custom-logo"><?php the_custom_logo(); ?></div>
			<?php endif; ?>
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="ps-brand-link" rel="home">
				<span class="ps-brand-mark" aria-hidden="true">P</span>
				<span class="ps-brand-text">Prompt<span class="ps-brand-accent">Syra</span><span class="ps-brand-tld">.com</span></span>
			</a>
		</div>

		<nav class="ps-primary-nav" aria-label="<?php esc_attr_e( 'Primary Navigation', 'promptsyra' ); ?>">
			<?php if ( has_nav_menu( 'promptsyra_primary' ) ) : ?>
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'promptsyra_primary',
						'container'      => false,
						'menu_class'     => 'ps-nav-list',
						'fallback_cb'    => false,
						'depth'          => 2,
					)
				);
				?>
			<?php else : ?>
				<ul class="ps-nav-list">
					<li class="ps-nav-item">
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="ps-nav-link <?php echo is_front_page() ? 'is-active' : ''; ?>" <?php echo is_front_page() ? 'aria-current="page"' : ''; ?>>
							<?php esc_html_e( 'Home', 'promptsyra' ); ?>
						</a>
					</li>

					<li class="ps-nav-item">
						<a href="<?php echo esc_url( $ps_url_photo ); ?>" class="ps-nav-link <?php echo $ps_is_cat_photo ? 'is-active' : ''; ?>" <?php echo $ps_is_cat_photo ? 'aria-current="page"' : ''; ?>>
							<?php esc_html_e( 'AI Photo Prompts', 'promptsyra' ); ?>
						</a>
					</li>

					<li class="ps-nav-item">
						<a href="<?php echo esc_url( $ps_url_trending ); ?>" class="ps-nav-link <?php echo $ps_is_cat_trending ? 'is-active' : ''; ?>" <?php echo $ps_is_cat_trending ? 'aria-current="page"' : ''; ?>>
							<?php esc_html_e( 'Trending Prompts', 'promptsyra' ); ?>
						</a>
					</li>

					<li class="ps-nav-item">
						<a href="<?php echo esc_url( $ps_url_social ); ?>" class="ps-nav-link <?php echo $ps_is_cat_social ? 'is-active' : ''; ?>" <?php echo $ps_is_cat_social ? 'aria-current="page"' : ''; ?>>
							<?php esc_html_e( 'Social Media Prompts', 'promptsyra' ); ?>
						</a>
					</li>

					<li class="ps-nav-item ps-has-dropdown" data-ps-dropdown>
						<button
							type="button"
							class="ps-nav-link ps-dropdown-trigger <?php echo $ps_is_important ? 'is-active' : ''; ?>"
							aria-expanded="false"
							aria-controls="ps-menu-important-pages"
						>
							<span><?php esc_html_e( 'Important Pages', 'promptsyra' ); ?></span>
							<svg class="ps-chevron" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
						</button>
						<ul id="ps-menu-important-pages" class="ps-dropdown-menu" hidden>
							<li>
								<a href="<?php echo esc_url( $ps_about_url ); ?>" class="ps-dropdown-link <?php echo $ps_is_about ? 'is-active' : ''; ?>" <?php echo $ps_is_about ? 'aria-current="page"' : ''; ?>>
									<?php esc_html_e( 'About Us', 'promptsyra' ); ?>
								</a>
							</li>
							<li>
								<a href="<?php echo esc_url( $ps_contact_url ); ?>" class="ps-dropdown-link <?php echo $ps_is_contact ? 'is-active' : ''; ?>" <?php echo $ps_is_contact ? 'aria-current="page"' : ''; ?>>
									<?php esc_html_e( 'Contact Us', 'promptsyra' ); ?>
								</a>
							</li>
							<li>
								<a href="<?php echo esc_url( $ps_privacy_url ); ?>" class="ps-dropdown-link <?php echo $ps_is_privacy ? 'is-active' : ''; ?>" <?php echo $ps_is_privacy ? 'aria-current="page"' : ''; ?>>
									<?php esc_html_e( 'Privacy Policy', 'promptsyra' ); ?>
								</a>
							</li>
							<li>
								<a href="<?php echo esc_url( $ps_disclaimer_url ); ?>" class="ps-dropdown-link <?php echo $ps_is_disclaimer ? 'is-active' : ''; ?>" <?php echo $ps_is_disclaimer ? 'aria-current="page"' : ''; ?>>
									<?php esc_html_e( 'Disclaimer', 'promptsyra' ); ?>
								</a>
							</li>
							<li>
								<a href="<?php echo esc_url( $ps_terms_url ); ?>" class="ps-dropdown-link <?php echo $ps_is_terms ? 'is-active' : ''; ?>" <?php echo $ps_is_terms ? 'aria-current="page"' : ''; ?>>
									<?php esc_html_e( 'Terms & Conditions', 'promptsyra' ); ?>
								</a>
							</li>
						</ul>
					</li>
				</ul>
			<?php endif; ?>
		</nav>

		<div class="ps-header-actions">
			<button
				type="button"
				class="ps-search-toggle"
				data-ps-search-toggle
				aria-expanded="false"
				aria-controls="ps-header-search-drawer"
				aria-label="<?php esc_attr_e( 'Toggle Prompt Search', 'promptsyra' ); ?>"
			>
				<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
					<circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
				</svg>
			</button>

			<a href="<?php echo esc_url( $ps_cta_url ); ?>" class="ps-btn-primary ps-header-cta">
				<span><?php echo esc_html( $ps_cta_label ); ?></span>
				<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
			</a>

			<button
				type="button"
				class="ps-mobile-toggle"
				data-ps-mobile-toggle
				aria-expanded="false"
				aria-controls="ps-mobile-drawer"
				aria-label="<?php esc_attr_e( 'Toggle Navigation Menu', 'promptsyra' ); ?>"
			>
				<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
					<path d="M4 7h16M4 12h16M4 17h16"/>
				</svg>
			</button>
		</div>
	</div>

	<div id="ps-header-search-drawer" class="ps-search-drawer" hidden>
		<div class="ps-container">
			<form role="search" method="get" class="ps-inline-search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
				<label for="ps-header-s" class="ps-sr-only"><?php esc_html_e( 'Search AI photo prompts', 'promptsyra' ); ?></label>
				<input
					type="search"
					id="ps-header-s"
					name="s"
					value="<?php echo esc_attr( get_search_query() ); ?>"
					placeholder="<?php esc_attr_e( 'Search prompts by style, lighting, or subject...', 'promptsyra' ); ?>"
					class="ps-search-input"
				/>
				<button type="submit" class="ps-btn-primary"><?php esc_html_e( 'Search', 'promptsyra' ); ?></button>
			</form>
		</div>
	</div>

	<div id="ps-mobile-drawer" class="ps-mobile-drawer" hidden>
		<div class="ps-container ps-mobile-drawer-inner">
			<nav aria-label="<?php esc_attr_e( 'Mobile Navigation', 'promptsyra' ); ?>">
				<ul class="ps-mobile-nav-list">
					<li>
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="<?php echo is_front_page() ? 'is-active' : ''; ?>">
							<?php esc_html_e( 'Home', 'promptsyra' ); ?>
						</a>
					</li>
					<li>
						<a href="<?php echo esc_url( $ps_url_photo ); ?>" class="<?php echo $ps_is_cat_photo ? 'is-active' : ''; ?>">
							<?php esc_html_e( 'AI Photo Prompts', 'promptsyra' ); ?>
						</a>
					</li>
					<li>
						<a href="<?php echo esc_url( $ps_url_trending ); ?>" class="<?php echo $ps_is_cat_trending ? 'is-active' : ''; ?>">
							<?php esc_html_e( 'Trending Prompts', 'promptsyra' ); ?>
						</a>
					</li>
					<li>
						<a href="<?php echo esc_url( $ps_url_social ); ?>" class="<?php echo $ps_is_cat_social ? 'is-active' : ''; ?>">
							<?php esc_html_e( 'Social Media Prompts', 'promptsyra' ); ?>
						</a>
					</li>
					<li class="ps-mobile-group-heading"><?php esc_html_e( 'Important Pages', 'promptsyra' ); ?></li>
					<li>
						<a href="<?php echo esc_url( $ps_about_url ); ?>" class="<?php echo $ps_is_about ? 'is-active' : ''; ?>">
							<?php esc_html_e( 'About Us', 'promptsyra' ); ?>
						</a>
					</li>
					<li>
						<a href="<?php echo esc_url( $ps_contact_url ); ?>" class="<?php echo $ps_is_contact ? 'is-active' : ''; ?>">
							<?php esc_html_e( 'Contact Us', 'promptsyra' ); ?>
						</a>
					</li>
					<li>
						<a href="<?php echo esc_url( $ps_privacy_url ); ?>" class="<?php echo $ps_is_privacy ? 'is-active' : ''; ?>">
							<?php esc_html_e( 'Privacy Policy', 'promptsyra' ); ?>
						</a>
					</li>
					<li>
						<a href="<?php echo esc_url( $ps_disclaimer_url ); ?>" class="<?php echo $ps_is_disclaimer ? 'is-active' : ''; ?>">
							<?php esc_html_e( 'Disclaimer', 'promptsyra' ); ?>
						</a>
					</li>
					<li>
						<a href="<?php echo esc_url( $ps_terms_url ); ?>" class="<?php echo $ps_is_terms ? 'is-active' : ''; ?>">
							<?php esc_html_e( 'Terms & Conditions', 'promptsyra' ); ?>
						</a>
					</li>
				</ul>
			</nav>
			<a href="<?php echo esc_url( $ps_cta_url ); ?>" class="ps-btn-primary ps-mobile-cta">
				<?php echo esc_html( $ps_cta_label ); ?>
			</a>
		</div>
	</div>
</header>
