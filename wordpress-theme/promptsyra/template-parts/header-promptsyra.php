<?php
/**
 * PromptSyra Scoped Header Partial
 *
 * Implements:
 * - PromptSyra.com branding (custom logo + wordmark)
 * - Home, AI Prompts dropdown, Categories dropdown (3 approved slugs only)
 * - Conditional Tools dropdown (shown ONLY when a real 'promptsyra_tools' menu has items)
 * - Blog, About, Contact (verified real URLs only — zero broken links)
 * - Accessible Search control + prominent "Explore Prompts" CTA
 * - Keyboard accessible ARIA attributes and responsive mobile navigation drawer
 *
 * @package PromptSyra
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$ps_categories  = array(
	promptsyra_resolve_category_data( 'ai-photo-prompts' ),
	promptsyra_resolve_category_data( 'trending-prompts' ),
	promptsyra_resolve_category_data( 'social-media-prompts' ),
);
$ps_posts_page  = (int) get_option( 'page_for_posts' );
$ps_blog_url    = $ps_posts_page > 0 ? get_permalink( $ps_posts_page ) : home_url( '/#featured-prompts' );
$ps_about_url   = promptsyra_get_verified_page_url( array( 'about-us', 'about' ) );
$ps_contact_url = promptsyra_get_verified_page_url( array( 'contact-us', 'contact' ) );
$ps_is_about    = is_page( array( 'about-us', 'about' ) );
$ps_is_contact  = is_page( array( 'contact-us', 'contact' ) );
$ps_cta_label   = get_theme_mod( 'promptsyra_hero_cta_label', __( 'Explore Prompts', 'promptsyra' ) );
$ps_cta_url_raw = get_theme_mod( 'promptsyra_hero_cta_url', '#featured-prompts' );
$ps_cta_url     = ! empty( $ps_cta_url_raw ) ? $ps_cta_url_raw : '#featured-prompts';
$ps_has_tools   = has_nav_menu( 'promptsyra_tools' );
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
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="ps-nav-link <?php echo is_front_page() ? 'is-active' : ''; ?>">
							<?php esc_html_e( 'Home', 'promptsyra' ); ?>
						</a>
					</li>

					<li class="ps-nav-item ps-has-dropdown" data-ps-dropdown>
						<button type="button" class="ps-nav-link ps-dropdown-trigger" aria-expanded="false" aria-controls="ps-menu-ai-prompts">
							<span><?php esc_html_e( 'AI Prompts', 'promptsyra' ); ?></span>
							<svg class="ps-chevron" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
						</button>
						<ul id="ps-menu-ai-prompts" class="ps-dropdown-menu" hidden>
							<li>
								<a href="<?php echo esc_url( home_url( '/#featured-prompts' ) ); ?>" class="ps-dropdown-link">
									<?php esc_html_e( 'Featured AI Prompts', 'promptsyra' ); ?>
								</a>
							</li>
							<?php foreach ( $ps_categories as $ps_cat ) : ?>
								<?php if ( $ps_cat['exists'] && ! empty( $ps_cat['url'] ) ) : ?>
									<li>
										<a href="<?php echo esc_url( $ps_cat['url'] ); ?>" class="ps-dropdown-link">
											<?php echo esc_html( $ps_cat['name'] ); ?>
										</a>
									</li>
								<?php endif; ?>
							<?php endforeach; ?>
						</ul>
					</li>

					<li class="ps-nav-item ps-has-dropdown" data-ps-dropdown>
						<button type="button" class="ps-nav-link ps-dropdown-trigger" aria-expanded="false" aria-controls="ps-menu-categories">
							<span><?php esc_html_e( 'Categories', 'promptsyra' ); ?></span>
							<svg class="ps-chevron" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
						</button>
						<ul id="ps-menu-categories" class="ps-dropdown-menu" hidden>
							<?php foreach ( $ps_categories as $ps_cat ) : ?>
								<li>
									<?php if ( $ps_cat['exists'] && ! empty( $ps_cat['url'] ) ) : ?>
										<a href="<?php echo esc_url( $ps_cat['url'] ); ?>" class="ps-dropdown-link">
											<?php echo esc_html( $ps_cat['name'] ); ?>
										</a>
									<?php else : ?>
										<span class="ps-dropdown-link ps-dropdown-unconfigured">
											<?php echo esc_html( $ps_cat['name'] ); ?>
										</span>
									<?php endif; ?>
								</li>
							<?php endforeach; ?>
						</ul>
					</li>

					<?php if ( $ps_has_tools ) : ?>
						<li class="ps-nav-item ps-has-dropdown" data-ps-dropdown>
							<button type="button" class="ps-nav-link ps-dropdown-trigger" aria-expanded="false" aria-controls="ps-menu-tools">
								<span><?php esc_html_e( 'Tools', 'promptsyra' ); ?></span>
								<svg class="ps-chevron" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
							</button>
							<div id="ps-menu-tools" class="ps-dropdown-menu" hidden>
								<?php
								wp_nav_menu(
									array(
										'theme_location' => 'promptsyra_tools',
										'container'      => false,
										'menu_class'     => 'ps-sublist',
										'depth'          => 1,
									)
								);
								?>
							</div>
						</li>
					<?php endif; ?>

					<?php if ( ! empty( $ps_blog_url ) ) : ?>
						<li class="ps-nav-item">
							<a href="<?php echo esc_url( $ps_blog_url ); ?>" class="ps-nav-link"><?php esc_html_e( 'Blog', 'promptsyra' ); ?></a>
						</li>
					<?php endif; ?>

					<?php if ( ! empty( $ps_about_url ) ) : ?>
						<li class="ps-nav-item">
							<a href="<?php echo esc_url( $ps_about_url ); ?>" class="ps-nav-link <?php echo $ps_is_about ? 'is-active' : ''; ?>" <?php echo $ps_is_about ? 'aria-current="page"' : ''; ?>><?php esc_html_e( 'About Us', 'promptsyra' ); ?></a>
						</li>
					<?php endif; ?>

					<?php if ( ! empty( $ps_contact_url ) ) : ?>
						<li class="ps-nav-item">
							<a href="<?php echo esc_url( $ps_contact_url ); ?>" class="ps-nav-link <?php echo $ps_is_contact ? 'is-active' : ''; ?>" <?php echo $ps_is_contact ? 'aria-current="page"' : ''; ?>><?php esc_html_e( 'Contact Us', 'promptsyra' ); ?></a>
						</li>
					<?php endif; ?>
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
					<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'promptsyra' ); ?></a></li>
					<li class="ps-mobile-group-heading"><?php esc_html_e( 'Main Categories', 'promptsyra' ); ?></li>
					<?php foreach ( $ps_categories as $ps_cat ) : ?>
						<?php if ( $ps_cat['exists'] && ! empty( $ps_cat['url'] ) ) : ?>
							<li><a href="<?php echo esc_url( $ps_cat['url'] ); ?>"><?php echo esc_html( $ps_cat['name'] ); ?></a></li>
						<?php endif; ?>
					<?php endforeach; ?>
					<?php if ( ! empty( $ps_blog_url ) ) : ?>
						<li><a href="<?php echo esc_url( $ps_blog_url ); ?>"><?php esc_html_e( 'Blog', 'promptsyra' ); ?></a></li>
					<?php endif; ?>
					<?php if ( ! empty( $ps_about_url ) ) : ?>
						<li><a href="<?php echo esc_url( $ps_about_url ); ?>"><?php esc_html_e( 'About Us', 'promptsyra' ); ?></a></li>
					<?php endif; ?>
					<?php if ( ! empty( $ps_contact_url ) ) : ?>
						<li><a href="<?php echo esc_url( $ps_contact_url ); ?>"><?php esc_html_e( 'Contact Us', 'promptsyra' ); ?></a></li>
					<?php endif; ?>
				</ul>
			</nav>
			<a href="<?php echo esc_url( $ps_cta_url ); ?>" class="ps-btn-primary ps-mobile-cta">
				<?php echo esc_html( $ps_cta_label ); ?>
			</a>
		</div>
	</div>
</header>
