<?php
/**
 * Front Page Template — PromptSyra.com Complete Homepage Redesign
 *
 * Replaces the legacy AIPromptZyra homepage while keeping single-post
 * templates (single.php) 100% untouched.
 *
 * Dynamic WordPress Data Only:
 * - Real WP_Query against published posts (never hardcoded demo posts)
 * - Real approved category archives (ai-photo-prompts, trending-prompts, social-media-prompts)
 * - Real WordPress user profile data for Syra Ansari, Arjun Patel, and Neha Joshi
 * - Configurable social channels & editable Customizer copy
 *
 * @package PromptSyra
 * @version 2.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Approved three primary categories.
$ps_cat_photo    = promptsyra_resolve_category_data( 'ai-photo-prompts' );
$ps_cat_trending = promptsyra_resolve_category_data( 'trending-prompts' );
$ps_cat_social   = promptsyra_resolve_category_data( 'social-media-prompts' );
$ps_primary_cats = array( $ps_cat_photo, $ps_cat_trending, $ps_cat_social );

// Hero settings from WordPress Customizer.
$ps_eyebrow      = get_theme_mod( 'promptsyra_hero_eyebrow', __( 'AI Photo Prompts & Creative Ideas', 'promptsyra' ) );
$ps_heading      = get_theme_mod( 'promptsyra_hero_heading', __( 'Turn Your Ideas Into Stunning AI Creations', 'promptsyra' ) );
$ps_description  = get_theme_mod( 'promptsyra_hero_description', __( 'Discover useful AI photo prompts for portraits, creative photography, social media visuals, and more.', 'promptsyra' ) );
$ps_search_ph    = get_theme_mod( 'promptsyra_hero_search_placeholder', __( 'Search photo prompts (portraits, studio lighting, social media...)', 'promptsyra' ) );
$ps_cta_label    = get_theme_mod( 'promptsyra_hero_cta_label', __( 'Explore Prompts', 'promptsyra' ) );
$ps_cta_url      = get_theme_mod( 'promptsyra_hero_cta_url', '#featured-prompts' );
$ps_point_1      = get_theme_mod( 'promptsyra_utility_point_1', __( 'Structured, Copy-Ready Text', 'promptsyra' ) );
$ps_point_2      = get_theme_mod( 'promptsyra_utility_point_2', __( 'Portrait, Creative & Social Styles', 'promptsyra' ) );
$ps_point_3      = get_theme_mod( 'promptsyra_utility_point_3', __( 'Practical Customization Notes', 'promptsyra' ) );

// Hero showcase visual composition images (supplied official photography assets).
$ps_hero_img_1_override = wp_get_attachment_image_url( absint( get_theme_mod( 'promptsyra_hero_card_image_1', 0 ) ), 'medium_large' );
$ps_hero_img_2_override = wp_get_attachment_image_url( absint( get_theme_mod( 'promptsyra_hero_card_image_2', 0 ) ), 'medium_large' );
$ps_hero_img_3_override = wp_get_attachment_image_url( absint( get_theme_mod( 'promptsyra_hero_card_image_3', 0 ) ), 'medium_large' );

$ps_hero_img_1 = ! empty( $ps_hero_img_1_override ) ? $ps_hero_img_1_override : 'https://promptsyra.com/wp-content/uploads/2026/10/promptsyra-hero-portrait.webp';
$ps_hero_img_2 = ! empty( $ps_hero_img_2_override ) ? $ps_hero_img_2_override : 'https://promptsyra.com/wp-content/uploads/2026/10/promptsyra-creative-studio.webp';
$ps_hero_img_3 = ! empty( $ps_hero_img_3_override ) ? $ps_hero_img_3_override : 'https://promptsyra.com/wp-content/uploads/2026/10/promptsyra-social-visuals.webp';

// Visibility flags.
$ps_show_categories = (bool) get_theme_mod( 'promptsyra_show_categories', true );
$ps_show_featured   = (bool) get_theme_mod( 'promptsyra_show_featured', true );
$ps_show_howto      = (bool) get_theme_mod( 'promptsyra_show_howto', true );
$ps_show_editorial  = (bool) get_theme_mod( 'promptsyra_show_editorial', true );
$ps_show_community  = (bool) get_theme_mod( 'promptsyra_show_community', true );
$ps_show_faq        = (bool) get_theme_mod( 'promptsyra_show_faq', true );
$ps_show_author     = (bool) get_theme_mod( 'promptsyra_show_author', true );

// Featured AI Prompts active filter slug (optional GET filter or client-side tab).
$ps_requested_cat = isset( $_GET['ps_cat'] ) ? sanitize_title( wp_unslash( $_GET['ps_cat'] ) ) : '';
$ps_allowed_slugs = array( 'ai-photo-prompts', 'trending-prompts', 'social-media-prompts', 'ai-social-media-prompts' );
if ( ! in_array( $ps_requested_cat, $ps_allowed_slugs, true ) ) {
	$ps_requested_cat = '';
}

$ps_posts_count = max( 3, min( 12, absint( get_theme_mod( 'promptsyra_featured_posts_count', 6 ) ) ) );
$ps_query_args  = array(
	'post_type'           => 'post',
	'post_status'         => 'publish',
	'posts_per_page'      => $ps_posts_count,
	'ignore_sticky_posts' => true,
	'no_found_rows'       => true,
);
if ( ! empty( $ps_requested_cat ) ) {
	if ( in_array( $ps_requested_cat, array( 'social-media-prompts', 'ai-social-media-prompts' ), true ) && ! empty( $ps_cat_social['slug'] ) ) {
		$ps_query_args['category_name'] = $ps_cat_social['slug'];
	} else {
		$ps_query_args['category_name'] = $ps_requested_cat;
	}
}
$ps_featured_query = new WP_Query( $ps_query_args );

// Official Community Channels (only genuine configured URLs).
$ps_community_channels = array();
$ps_channel_defs       = array(
	'instagram' => array(
		'title' => __( 'Instagram', 'promptsyra' ),
		'desc'  => __( 'Browse visual portrait studies, lighting references, and featured prompt previews.', 'promptsyra' ),
		'cta'   => __( 'Visit Instagram', 'promptsyra' ),
	),
	'facebook'  => array(
		'title' => __( 'Facebook', 'promptsyra' ),
		'desc'  => __( 'Follow new article updates, photography prompt collections, and community discussions.', 'promptsyra' ),
		'cta'   => __( 'Visit Facebook', 'promptsyra' ),
	),
	'youtube'   => array(
		'title' => __( 'YouTube', 'promptsyra' ),
		'desc'  => __( 'Watch step-by-step prompt customization walkthroughs and visual comparisons.', 'promptsyra' ),
		'cta'   => __( 'Watch Channel', 'promptsyra' ),
	),
	'telegram'  => array(
		'title' => __( 'Telegram', 'promptsyra' ),
		'desc'  => __( 'Receive direct updates when new AI photo prompt collections are published.', 'promptsyra' ),
		'cta'   => __( 'Open Channel', 'promptsyra' ),
	),
	'whatsapp'  => array(
		'title' => __( 'WhatsApp', 'promptsyra' ),
		'desc'  => __( 'Subscribe to our official channel for curated photo prompt updates.', 'promptsyra' ),
		'cta'   => __( 'Subscribe', 'promptsyra' ),
	),
	'x'         => array(
		'title' => __( 'X (Twitter)', 'promptsyra' ),
		'desc'  => __( 'Follow concise prompt notes, photography framing ideas, and site updates.', 'promptsyra' ),
		'cta'   => __( 'Follow on X', 'promptsyra' ),
	),
);
foreach ( $ps_channel_defs as $ch_key => $ch_meta ) {
	$ch_url = trim( (string) get_theme_mod( 'promptsyra_social_' . $ch_key, '' ) );
	if ( ! empty( $ch_url ) ) {
		$ps_community_channels[ $ch_key ] = array_merge( $ch_meta, array( 'url' => $ch_url ) );
	}
}

// FAQs from Customizer.
$ps_faqs = array();
foreach ( promptsyra_get_default_faqs() as $idx => $default_faq ) {
	$q_val = trim( (string) get_theme_mod( 'promptsyra_faq_q_' . $idx, $default_faq['q'] ) );
	$a_val = trim( (string) get_theme_mod( 'promptsyra_faq_a_' . $idx, $default_faq['a'] ) );
	if ( ! empty( $q_val ) && ! empty( $a_val ) ) {
		$ps_faqs[] = array(
			'q' => $q_val,
			'a' => $a_val,
		);
	}
}

// Three Designated Editorial Authors.
$ps_author_syra  = promptsyra_resolve_designated_author( 'syra' );
$ps_author_arjun = promptsyra_resolve_designated_author( 'arjun' );
$ps_author_neha  = promptsyra_resolve_designated_author( 'neha' );
$ps_all_authors  = array( $ps_author_syra, $ps_author_arjun, $ps_author_neha );
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<?php wp_head(); ?>
</head>
<body <?php body_class( 'ps-front-body' ); ?>>
<?php wp_body_open(); ?>

<div class="ps-page-shell">
	<a class="ps-skip-link" href="#ps-main-content"><?php esc_html_e( 'Skip to main content', 'promptsyra' ); ?></a>

	<?php get_template_part( 'template-parts/header', 'promptsyra' ); ?>

	<main id="ps-main-content" class="ps-front-main">
		<!-- B. Hero Section -->
		<section class="ps-hero-section" aria-labelledby="ps-hero-heading">
			<div class="ps-container ps-hero-grid">
				<div class="ps-hero-copy">
					<?php if ( ! empty( $ps_eyebrow ) ) : ?>
						<p class="ps-eyebrow"><?php echo esc_html( $ps_eyebrow ); ?></p>
					<?php endif; ?>

					<h1 id="ps-hero-heading" class="ps-hero-title">
						<?php echo esc_html( $ps_heading ); ?>
					</h1>

					<?php if ( ! empty( $ps_description ) ) : ?>
						<p class="ps-hero-desc"><?php echo esc_html( $ps_description ); ?></p>
					<?php endif; ?>

					<form role="search" method="get" class="ps-hero-search" action="<?php echo esc_url( home_url( '/' ) ); ?>">
						<label for="ps-hero-search-input" class="ps-sr-only">
							<?php esc_html_e( 'Search AI photo prompts', 'promptsyra' ); ?>
						</label>
						<svg class="ps-search-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
							<circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
						</svg>
						<input
							type="search"
							id="ps-hero-search-input"
							name="s"
							value="<?php echo esc_attr( get_search_query() ); ?>"
							placeholder="<?php echo esc_attr( $ps_search_ph ); ?>"
							class="ps-hero-search-field"
						/>
						<button type="submit" class="ps-btn-primary ps-hero-search-btn">
							<?php esc_html_e( 'Search', 'promptsyra' ); ?>
						</button>
					</form>

					<div class="ps-hero-support-bar">
						<div class="ps-hero-support-top">
							<a href="<?php echo esc_url( ! empty( $ps_cta_url ) ? $ps_cta_url : '#featured-prompts' ); ?>" class="ps-hero-explore-btn">
								<span><?php echo esc_html( $ps_cta_label ); ?></span>
								<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
							</a>
							<span class="ps-hero-support-caption">
								<?php esc_html_e( 'Browse published collections & adaptation notes', 'promptsyra' ); ?>
							</span>
						</div>

						<ul class="ps-hero-benefits-list" aria-label="<?php esc_attr_e( 'PromptSyra Feature Benefits', 'promptsyra' ); ?>">
							<?php foreach ( array( $ps_point_1, $ps_point_2, $ps_point_3 ) as $pt_text ) : ?>
								<?php if ( ! empty( $pt_text ) ) : ?>
									<li class="ps-hero-benefit-item">
										<span class="ps-hero-benefit-icon" aria-hidden="true">
											<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
										</span>
										<span class="ps-hero-benefit-text"><?php echo esc_html( $pt_text ); ?></span>
									</li>
								<?php endif; ?>
							<?php endforeach; ?>
						</ul>
					</div>
				</div>

				<!-- Photography Visual Composition -->
				<div class="ps-hero-showcase">
					<div class="ps-photo-collage">
						<article class="ps-showcase-card ps-card-main">
							<div class="ps-showcase-media">
								<img
									src="<?php echo esc_url( $ps_hero_img_1 ); ?>"
									alt="<?php esc_attr_e( 'Portrait Study · 85mm Soft Rim Light AI Photography Prompt Example', 'promptsyra' ); ?>"
									class="ps-showcase-img ps-showcase-img--portrait"
									width="944"
									height="1024"
									loading="eager"
									fetchpriority="high"
									decoding="async"
								/>
							</div>
							<div class="ps-showcase-caption">
								<span class="ps-showcase-kicker"><?php esc_html_e( 'Portrait Study · 85mm Soft Rim Light', 'promptsyra' ); ?></span>
								<p class="ps-showcase-snippet"><?php esc_html_e( '"Natural window key light, shallow depth of field, warm editorial skin tones, 85mm f/1.8..."', 'promptsyra' ); ?></p>
							</div>
						</article>

						<div class="ps-showcase-side">
							<article class="ps-showcase-card ps-card-sub">
								<div class="ps-showcase-media">
									<img
										src="<?php echo esc_url( $ps_hero_img_2 ); ?>"
										alt="<?php esc_attr_e( 'Creative Studio Setup AI Photography Prompt Example', 'promptsyra' ); ?>"
										class="ps-showcase-img ps-showcase-img--sub"
										width="581"
										height="504"
										loading="eager"
										decoding="async"
									/>
								</div>
								<div class="ps-showcase-caption">
									<span class="ps-showcase-kicker"><?php esc_html_e( 'Creative Studio Setup', 'promptsyra' ); ?></span>
								</div>
							</article>

							<article class="ps-showcase-card ps-card-sub">
								<div class="ps-showcase-media">
									<img
										src="<?php echo esc_url( $ps_hero_img_3 ); ?>"
										alt="<?php esc_attr_e( '9:16 Social Visuals AI Photography Prompt Example', 'promptsyra' ); ?>"
										class="ps-showcase-img ps-showcase-img--sub"
										width="581"
										height="507"
										loading="lazy"
										decoding="async"
									/>
								</div>
								<div class="ps-showcase-caption">
									<span class="ps-showcase-kicker"><?php esc_html_e( '9:16 Social Visuals', 'promptsyra' ); ?></span>
								</div>
							</article>
						</div>
					</div>
				</div>
			</div>
		</section>

		<!-- C. Main Category Discovery Cards (Strictly 3 Approved Categories) -->
		<?php if ( $ps_show_categories ) : ?>
			<?php
			$ps_cat_card_themes = array(
				0 => array(
					'theme_class' => 'ps-cat-theme-photo',
					'focus_line'  => __( 'Portraits · Studio Lighting · Realism', 'promptsyra' ),
					'cta_label'   => __( 'Explore Photo Prompts', 'promptsyra' ),
				),
				1 => array(
					'theme_class' => 'ps-cat-theme-trending',
					'focus_line'  => __( 'Seasonal Concepts · Emerging Styles', 'promptsyra' ),
					'cta_label'   => __( 'Explore Seasonal Ideas', 'promptsyra' ),
				),
				2 => array(
					'theme_class' => 'ps-cat-theme-social',
					'focus_line'  => __( '9:16 Stories · Reels · Thumbnails', 'promptsyra' ),
					'cta_label'   => __( 'Explore Social Visuals', 'promptsyra' ),
				),
			);
			?>
			<section class="ps-categories-section" aria-labelledby="ps-categories-heading">
				<div class="ps-container">
					<div class="ps-categories-head">
						<div>
							<p class="ps-section-kicker"><?php esc_html_e( 'Browse by Visual Focus', 'promptsyra' ); ?></p>
							<h2 id="ps-categories-heading" class="ps-section-title"><?php esc_html_e( 'Primary Prompt Collections', 'promptsyra' ); ?></h2>
						</div>
						<p class="ps-categories-lead">
							<?php esc_html_e( 'Three focused collections organized by photography style, creative direction, and display format.', 'promptsyra' ); ?>
						</p>
					</div>

					<div class="ps-category-trio">
						<?php foreach ( $ps_primary_cats as $cat_idx => $cat_item ) : ?>
							<?php
							$card_meta = isset( $ps_cat_card_themes[ $cat_idx ] ) ? $ps_cat_card_themes[ $cat_idx ] : $ps_cat_card_themes[0];
							$has_link  = ( ! empty( $cat_item['exists'] ) && ! empty( $cat_item['url'] ) );
							?>
							<article class="ps-category-card <?php echo esc_attr( $card_meta['theme_class'] ); ?>">
								<div class="ps-cat-visual-band">
									<div class="ps-category-icon" aria-hidden="true">
										<?php if ( 0 === $cat_idx ) : ?>
											<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
												<path d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3l-2.5-3z"/>
												<circle cx="12" cy="13" r="3.5"/>
												<path d="M18 10h.01"/>
											</svg>
										<?php elseif ( 1 === $cat_idx ) : ?>
											<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
												<path d="m12 3 1.9 4.6L18.5 9.5l-4.6 1.9L12 16l-1.9-4.6L5.5 9.5l4.6-1.9L12 3z"/>
												<path d="M19 16v4"/><path d="M17 18h4"/>
												<path d="M5 17v2"/><path d="M4 18h2"/>
											</svg>
										<?php else : ?>
											<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
												<rect width="12" height="20" x="6" y="2" rx="2.5"/>
												<path d="M9.5 6.5h5"/>
												<rect width="6" height="5" x="9" y="9.5" rx="1"/>
												<path d="M10 17.5h4"/>
											</svg>
										<?php endif; ?>
									</div>
									<span class="ps-cat-focus-kicker"><?php echo esc_html( $card_meta['focus_line'] ); ?></span>
								</div>

								<div class="ps-category-body">
									<h3 class="ps-category-title">
										<?php if ( $has_link ) : ?>
											<a href="<?php echo esc_url( $cat_item['url'] ); ?>" class="ps-category-link">
												<?php echo esc_html( $cat_item['name'] ); ?>
											</a>
										<?php else : ?>
											<span><?php echo esc_html( $cat_item['name'] ); ?></span>
										<?php endif; ?>
									</h3>
									<p class="ps-category-desc"><?php echo esc_html( $cat_item['description'] ); ?></p>
								</div>

								<div class="ps-cat-card-footer">
									<div class="ps-category-meta">
										<?php if ( $cat_item['exists'] && (int) $cat_item['count'] > 0 ) : ?>
											<span class="ps-cat-count-label">
												<?php
												printf(
													/* translators: %d: published article count */
													esc_html( _n( '%d Published Guide', '%d Published Guides', (int) $cat_item['count'], 'promptsyra' ) ),
													(int) $cat_item['count']
												);
												?>
											</span>
										<?php elseif ( $cat_item['exists'] ) : ?>
											<span><code>/<?php echo esc_html( $cat_item['slug'] ); ?>/</code></span>
										<?php elseif ( current_user_can( 'manage_categories' ) ) : ?>
											<a href="<?php echo esc_url( admin_url( 'themes.php?page=promptsyra-setup' ) ); ?>" class="ps-admin-hint">
												<?php esc_html_e( 'Create in Setup', 'promptsyra' ); ?>
											</a>
										<?php else : ?>
											<span><code>/<?php echo esc_html( $cat_item['slug'] ); ?>/</code></span>
										<?php endif; ?>
									</div>

									<?php if ( $has_link ) : ?>
										<span class="ps-cat-explore-cue" aria-hidden="true">
											<span><?php echo esc_html( $card_meta['cta_label'] ); ?></span>
											<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
										</span>
									<?php endif; ?>
								</div>
							</article>
						<?php endforeach; ?>
					</div>
				</div>
			</section>
		<?php endif; ?>

		<!-- D. Featured AI Prompts Section (Real WP_Query Posts Only) -->
		<?php if ( $ps_show_featured ) : ?>
			<section id="featured-prompts" class="ps-featured-section" aria-labelledby="ps-featured-heading">
				<div class="ps-container">
					<div class="ps-section-header">
						<div>
							<p class="ps-section-kicker"><?php esc_html_e( 'Published Prompt Library', 'promptsyra' ); ?></p>
							<h2 id="ps-featured-heading" class="ps-section-title"><?php esc_html_e( 'Featured AI Prompts', 'promptsyra' ); ?></h2>
						</div>

						<!-- First Category Choices: AI Photo Prompts, Trending Prompts, Social Media Prompts -->
						<div class="ps-filter-bar" role="navigation" aria-label="<?php esc_attr_e( 'Filter Featured Prompts by Category', 'promptsyra' ); ?>">
							<?php foreach ( $ps_primary_cats as $filter_cat ) : ?>
								<?php
								$is_active_tab = ( $ps_requested_cat === $filter_cat['slug'] );
								$tab_href      = add_query_arg( 'ps_cat', $filter_cat['slug'], home_url( '/#featured-prompts' ) );
								?>
								<a
									href="<?php echo esc_url( $tab_href ); ?>"
									class="ps-filter-tab <?php echo $is_active_tab ? 'is-active' : ''; ?>"
									<?php echo $is_active_tab ? 'aria-current="page"' : ''; ?>
								>
									<?php echo esc_html( $filter_cat['name'] ); ?>
								</a>
							<?php endforeach; ?>
							<a
								href="<?php echo esc_url( home_url( '/#featured-prompts' ) ); ?>"
								class="ps-filter-tab <?php echo empty( $ps_requested_cat ) ? 'is-active' : ''; ?>"
								<?php echo empty( $ps_requested_cat ) ? 'aria-current="page"' : ''; ?>
							>
								<?php esc_html_e( 'All Latest', 'promptsyra' ); ?>
							</a>
						</div>
					</div>

					<?php if ( $ps_featured_query->have_posts() ) : ?>
						<div class="ps-posts-grid">
							<?php
							while ( $ps_featured_query->have_posts() ) :
								$ps_featured_query->the_post();
								$post_cats  = get_the_category();
								$cat_label  = ! empty( $post_cats ) ? $post_cats[0]->name : __( 'AI Prompts', 'promptsyra' );
								?>
								<article <?php post_class( 'ps-post-card' ); ?>>
									<a href="<?php the_permalink(); ?>" class="ps-post-media-link" tabindex="-1" aria-hidden="true">
										<?php if ( has_post_thumbnail() ) : ?>
											<?php
											the_post_thumbnail(
												'medium_large',
												array(
													'class'   => 'ps-post-thumb',
													'loading' => 'lazy',
												)
											);
											?>
										<?php else : ?>
											<div class="ps-post-thumb-fallback">
												<span>PromptSyra.com</span>
											</div>
										<?php endif; ?>
									</a>

									<div class="ps-post-card-body">
										<div class="ps-post-meta-line">
											<span><?php echo esc_html( $cat_label ); ?></span>
											<span aria-hidden="true">·</span>
											<time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>">
												<?php echo esc_html( get_the_date() ); ?>
											</time>
										</div>

										<h3 class="ps-post-card-title">
											<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
										</h3>

										<p class="ps-post-card-excerpt">
											<?php echo esc_html( wp_trim_words( get_the_excerpt(), 18, '…' ) ); ?>
										</p>

										<a href="<?php the_permalink(); ?>" class="ps-post-read-link">
											<span><?php esc_html_e( 'View Prompt & Notes', 'promptsyra' ); ?></span>
											<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
										</a>
									</div>
								</article>
							<?php endwhile; ?>
							<?php wp_reset_postdata(); ?>
						</div>
					<?php else : ?>
						<div class="ps-empty-posts">
							<p>
								<?php esc_html_e( 'No published prompt articles match this filter yet. Browse another category or check back as new prompts are published.', 'promptsyra' ); ?>
							</p>
							<?php if ( ! empty( $ps_requested_cat ) ) : ?>
								<a href="<?php echo esc_url( home_url( '/#featured-prompts' ) ); ?>" class="ps-btn-secondary">
									<?php esc_html_e( 'Show All Published Prompts', 'promptsyra' ); ?>
								</a>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				</div>
			</section>
		<?php endif; ?>

		<!-- E. How to Use AI Prompts (Connected 3-Step Workflow) -->
		<?php if ( $ps_show_howto ) : ?>
			<section class="ps-howto-section" aria-labelledby="ps-howto-heading">
				<div class="ps-container">
					<div class="ps-howto-panel">
						<div class="ps-howto-intro">
							<p class="ps-section-kicker"><?php esc_html_e( 'Practical 3-Step Workflow', 'promptsyra' ); ?></p>
							<h2 id="ps-howto-heading" class="ps-section-title"><?php esc_html_e( 'How to Use AI Prompts', 'promptsyra' ); ?></h2>
							<p class="ps-howto-sub">
								<?php esc_html_e( 'Every prompt on PromptSyra is structured in plain photographic language so you can select a visual concept, copy the text cleanly, and adapt it inside your preferred generator.', 'promptsyra' ); ?>
							</p>
						</div>

						<ol class="ps-workflow-steps">
							<li class="ps-workflow-step">
								<div class="ps-workflow-node-row">
									<span class="ps-workflow-num">01</span>
									<span class="ps-workflow-icon" aria-hidden="true">
										<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
											<path d="M4 8V6a2 2 0 0 1 2-2h2"/><path d="M4 16v2a2 2 0 0 0 2 2h2"/>
											<path d="M16 4h2a2 2 0 0 1 2 2v2"/><path d="M16 20h2a2 2 0 0 0 2-2v-2"/>
											<circle cx="12" cy="12" r="3"/>
										</svg>
									</span>
								</div>
								<h3 class="ps-step-title"><?php esc_html_e( 'Select a Matching Style', 'promptsyra' ); ?></h3>
								<p class="ps-step-desc">
									<?php esc_html_e( 'Choose a prompt that matches the intended subject and visual style.', 'promptsyra' ); ?>
								</p>
								<span class="ps-step-cue"><?php esc_html_e( 'Portraits · Creative Scenes · Social Formats', 'promptsyra' ); ?></span>
							</li>

							<li class="ps-workflow-step">
								<div class="ps-workflow-node-row">
									<span class="ps-workflow-num">02</span>
									<span class="ps-workflow-icon" aria-hidden="true">
										<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
											<rect width="13" height="13" x="9" y="9" rx="2"/>
											<path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>
											<path d="m12.5 15.5 2 2 3.5-3.5"/>
										</svg>
									</span>
								</div>
								<h3 class="ps-step-title"><?php esc_html_e( 'Copy & Review Notes', 'promptsyra' ); ?></h3>
								<p class="ps-step-desc">
									<?php esc_html_e( 'Copy the complete prompt text and review any optional customization notes.', 'promptsyra' ); ?>
								</p>
								<span class="ps-step-cue"><?php esc_html_e( 'Full Prompt Text · Camera & Lighting Notes', 'promptsyra' ); ?></span>
							</li>

							<li class="ps-workflow-step">
								<div class="ps-workflow-node-row">
									<span class="ps-workflow-num">03</span>
									<span class="ps-workflow-icon" aria-hidden="true">
										<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
											<line x1="4" x2="20" y1="7" y2="7"/><line x1="4" x2="20" y1="17" y2="17"/>
											<circle cx="9" cy="7" r="2"/><circle cx="15" cy="17" r="2"/>
										</svg>
									</span>
								</div>
								<h3 class="ps-step-title"><?php esc_html_e( 'Paste & Tailor Details', 'promptsyra' ); ?></h3>
								<p class="ps-step-desc">
									<?php esc_html_e( 'Paste it into a compatible image-generation tool and adjust the subject, composition, lighting, or aspect ratio when necessary.', 'promptsyra' ); ?>
								</p>
								<span class="ps-step-cue"><?php esc_html_e( 'Subject · Composition · Lighting · Aspect Ratio', 'promptsyra' ); ?></span>
							</li>
						</ol>

						<div class="ps-workflow-note-bar" role="note">
							<span class="ps-workflow-note-icon" aria-hidden="true">
								<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
									<circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/>
								</svg>
							</span>
							<p class="ps-workflow-note-text">
								<strong><?php esc_html_e( 'Cross-Tool Interpretation Note:', 'promptsyra' ); ?></strong>
								<?php esc_html_e( ' Different AI tools interpret the same prompt wording differently. Treat each prompt as a structured starting point and refine lighting, framing, or aspect ratio inside your chosen generator rather than expecting identical outputs.', 'promptsyra' ); ?>
							</p>
						</div>
					</div>
				</div>
			</section>
		<?php endif; ?>

		<!-- F. Why Choose PromptSyra? (Asymmetric Editorial Value Showcase) -->
		<?php if ( $ps_show_editorial ) : ?>
			<?php
			$ps_sample_prompt_full = 'Editorial three-quarter portrait of a young woman wearing a warm oatmeal knit sweater, captured from eye level with an 85mm prime lens and shallow f/1.8 depth of field, illuminated by soft directional north-window daylight with gentle shadow roll-off, set against a muted warmstone studio backdrop with natural skin texture and balanced filmic color tones.';
			?>
			<section class="ps-editorial-section" aria-labelledby="ps-editorial-heading">
				<div class="ps-container">
					<div class="ps-editorial-head">
						<p class="ps-section-kicker"><?php esc_html_e( 'Editorial Purpose & Approach', 'promptsyra' ); ?></p>
						<h2 id="ps-editorial-heading" class="ps-section-title"><?php esc_html_e( 'Why Choose PromptSyra?', 'promptsyra' ); ?></h2>
						<p class="ps-editorial-lead">
							<?php esc_html_e( 'Writing descriptive photo prompts from scratch often takes repeated trial and error. PromptSyra organizes clear photographic phrasing and practical adaptation notes so you start with a dependable structure.', 'promptsyra' ); ?>
						</p>
					</div>

					<div class="ps-why-bento">
						<!-- Area 1: Clear, Copy-Ready Prompt Formatting + Live Functional Sample Specimen -->
						<article class="ps-why-lead-card">
							<div class="ps-why-lead-copy">
								<span class="ps-why-area-label"><?php esc_html_e( '01 / Structured Phrasing', 'promptsyra' ); ?></span>
								<h3 class="ps-why-card-title"><?php esc_html_e( 'Clear, Copy-Ready Prompt Formatting', 'promptsyra' ); ?></h3>
								<p class="ps-why-card-desc">
									<?php esc_html_e( 'Instead of cluttered comma-stuffed keyword lists, prompts on PromptSyra are written in clean, readable English—separating subject description, camera perspective, lighting direction, and backdrop mood so you can copy them directly or edit any clause in seconds.', 'promptsyra' ); ?>
								</p>
								<ul class="ps-why-check-list">
									<li><?php esc_html_e( 'Plain-English clauses organized in logical photographic order', 'promptsyra' ); ?></li>
									<li><?php esc_html_e( 'Easy to spot which words control lens depth, light, and background', 'promptsyra' ); ?></li>
									<li><?php esc_html_e( 'Clean one-click copy formatting without stray labels or numbering', 'promptsyra' ); ?></li>
								</ul>
							</div>

							<div class="ps-prompt-specimen">
								<div class="ps-specimen-header">
									<div class="ps-specimen-title-group">
										<span class="ps-specimen-eyebrow"><?php esc_html_e( 'Anatomical Prompt Example', 'promptsyra' ); ?></span>
										<strong class="ps-specimen-heading"><?php esc_html_e( '85mm Natural Window Portrait', 'promptsyra' ); ?></strong>
									</div>
									<button
										type="button"
										class="ps-specimen-copy-btn"
										data-ps-sample-copy
										data-prompt="<?php echo esc_attr( $ps_sample_prompt_full ); ?>"
										data-label-default="<?php esc_attr_e( 'Copy Sample Prompt', 'promptsyra' ); ?>"
										data-label-copied="<?php esc_attr_e( 'Copied Sample', 'promptsyra' ); ?>"
									>
										<svg class="ps-specimen-copy-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1" aria-hidden="true">
											<rect width="13" height="13" x="9" y="9" rx="2"/>
											<path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>
										</svg>
										<span data-ps-sample-btn-text><?php esc_html_e( 'Copy Sample Prompt', 'promptsyra' ); ?></span>
									</button>
									<span class="ps-sr-only" aria-live="polite" data-ps-sample-status></span>
								</div>

								<dl class="ps-specimen-breakdown">
									<div class="ps-specimen-row">
										<dt class="ps-specimen-key"><?php esc_html_e( 'Subject', 'promptsyra' ); ?></dt>
										<dd class="ps-specimen-val"><?php esc_html_e( 'Editorial three-quarter portrait of a young woman wearing a warm oatmeal knit sweater,', 'promptsyra' ); ?></dd>
									</div>
									<div class="ps-specimen-row">
										<dt class="ps-specimen-key"><?php esc_html_e( 'Framing', 'promptsyra' ); ?></dt>
										<dd class="ps-specimen-val"><?php esc_html_e( 'captured from eye level with an 85mm prime lens and shallow f/1.8 depth of field,', 'promptsyra' ); ?></dd>
									</div>
									<div class="ps-specimen-row">
										<dt class="ps-specimen-key"><?php esc_html_e( 'Lighting', 'promptsyra' ); ?></dt>
										<dd class="ps-specimen-val"><?php esc_html_e( 'illuminated by soft directional north-window daylight with gentle shadow roll-off,', 'promptsyra' ); ?></dd>
									</div>
									<div class="ps-specimen-row">
										<dt class="ps-specimen-key"><?php esc_html_e( 'Backdrop', 'promptsyra' ); ?></dt>
										<dd class="ps-specimen-val"><?php esc_html_e( 'set against a muted warmstone studio backdrop with natural skin texture and balanced filmic color tones.', 'promptsyra' ); ?></dd>
									</div>
								</dl>
							</div>
						</article>

						<!-- Area 2: Ideas for Different Photo Styles -->
						<article class="ps-why-sub-card">
							<span class="ps-why-area-label"><?php esc_html_e( '02 / Visual Variety', 'promptsyra' ); ?></span>
							<h3 class="ps-why-card-title"><?php esc_html_e( 'Ideas for Different Photo Styles', 'promptsyra' ); ?></h3>
							<p class="ps-why-card-desc">
								<?php esc_html_e( 'Browse distinct photographic directions spanning everyday studio portraits, conceptual compositions, seasonal themes, and vertical social layouts.', 'promptsyra' ); ?>
							</p>

							<div class="ps-style-matrix">
								<div class="ps-style-tile">
									<span class="ps-style-tile-icon" aria-hidden="true">
										<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><circle cx="12" cy="8" r="4"/><path d="M5 20c1.5-4 4.2-6 7-6s5.5 2 7 6"/></svg>
									</span>
									<div>
										<h4 class="ps-style-tile-title"><?php esc_html_e( 'Portrait Photography', 'promptsyra' ); ?></h4>
										<p class="ps-style-tile-note"><?php esc_html_e( 'Natural window light, studio headshots & 85mm depth', 'promptsyra' ); ?></p>
									</div>
								</div>

								<div class="ps-style-tile">
									<span class="ps-style-tile-icon" aria-hidden="true">
										<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg>
									</span>
									<div>
										<h4 class="ps-style-tile-title"><?php esc_html_e( 'Creative Compositions', 'promptsyra' ); ?></h4>
										<p class="ps-style-tile-note"><?php esc_html_e( 'Editorial color palettes, textures & styled studio sets', 'promptsyra' ); ?></p>
									</div>
								</div>

								<div class="ps-style-tile">
									<span class="ps-style-tile-icon" aria-hidden="true">
										<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><circle cx="12" cy="12" r="4"/><path d="M12 2v2"/><path d="M12 20v2"/><path d="m4.93 4.93 1.41 1.41"/><path d="m17.66 17.66 1.41 1.41"/><path d="M2 12h2"/><path d="M20 12h2"/></svg>
									</span>
									<div>
										<h4 class="ps-style-tile-title"><?php esc_html_e( 'Seasonal Concepts', 'promptsyra' ); ?></h4>
										<p class="ps-style-tile-note"><?php esc_html_e( 'Festive attire, golden-hour outdoor & cultural themes', 'promptsyra' ); ?></p>
									</div>
								</div>

								<div class="ps-style-tile">
									<span class="ps-style-tile-icon" aria-hidden="true">
										<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><rect width="12" height="18" x="6" y="3" rx="2"/><path d="M10 17h4"/></svg>
									</span>
									<div>
										<h4 class="ps-style-tile-title"><?php esc_html_e( 'Social Media Visuals', 'promptsyra' ); ?></h4>
										<p class="ps-style-tile-note"><?php esc_html_e( '9:16 stories, reel covers & clean thumbnail framing', 'promptsyra' ); ?></p>
									</div>
								</div>
							</div>
						</article>

						<!-- Area 3: Helpful Customization Guidance -->
						<article class="ps-why-sub-card">
							<span class="ps-why-area-label"><?php esc_html_e( '03 / Adaptable Variables', 'promptsyra' ); ?></span>
							<h3 class="ps-why-card-title"><?php esc_html_e( 'Helpful Customization Guidance', 'promptsyra' ); ?></h3>
							<p class="ps-why-card-desc">
								<?php esc_html_e( 'Rather than locking you into rigid templates, articles highlight the key variables you can swap to tailor a scene to your own concept:', 'promptsyra' ); ?>
							</p>

							<dl class="ps-custom-ledger">
								<div class="ps-custom-row">
									<dt><?php esc_html_e( 'Subject details', 'promptsyra' ); ?></dt>
									<dd><?php esc_html_e( 'Wardrobe colors, pose, expression, or everyday props', 'promptsyra' ); ?></dd>
								</div>
								<div class="ps-custom-row">
									<dt><?php esc_html_e( 'Lighting', 'promptsyra' ); ?></dt>
									<dd><?php esc_html_e( 'Soft window daylight, studio rim light, or warm dusk glow', 'promptsyra' ); ?></dd>
								</div>
								<div class="ps-custom-row">
									<dt><?php esc_html_e( 'Background', 'promptsyra' ); ?></dt>
									<dd><?php esc_html_e( 'Neutral studio canvas, outdoor street depth, or interior room', 'promptsyra' ); ?></dd>
								</div>
								<div class="ps-custom-row">
									<dt><?php esc_html_e( 'Framing', 'promptsyra' ); ?></dt>
									<dd><?php esc_html_e( 'Close-up portrait crop, waist-up editorial, or wide angle', 'promptsyra' ); ?></dd>
								</div>
								<div class="ps-custom-row">
									<dt><?php esc_html_e( 'Aspect ratio', 'promptsyra' ); ?></dt>
									<dd><?php esc_html_e( '4:5 feed portrait, 9:16 mobile story, or 16:9 widescreen', 'promptsyra' ); ?></dd>
								</div>
							</dl>

							<p class="ps-custom-tip">
								<strong><?php esc_html_e( 'Practical Tip:', 'promptsyra' ); ?></strong>
								<?php esc_html_e( ' Adjust one or two variables at a time so lighting and composition stay balanced as your generator interprets the updated prompt.', 'promptsyra' ); ?>
							</p>
						</article>
					</div>
				</div>
			</section>
		<?php endif; ?>

		<!-- G. Join Our Community (Instagram, Facebook, WhatsApp Channel) -->
		<section class="ps-community-section" aria-labelledby="ps-community-heading">
			<div class="ps-container">
				<div class="ps-community-header">
					<p class="ps-section-kicker"><?php esc_html_e( 'Connect & Follow', 'promptsyra' ); ?></p>
					<h2 id="ps-community-heading" class="ps-community-section-title"><?php esc_html_e( 'Join Our Community', 'promptsyra' ); ?></h2>
					<p class="ps-community-lead">
						<?php esc_html_e( 'Stay connected with PromptSyra across your favorite platforms for copy-ready AI photo prompts, lighting inspiration, and prompt engineering tips.', 'promptsyra' ); ?>
					</p>
				</div>

				<div class="ps-community-grid">
					<!-- Instagram Visual Gallery Card -->
					<article class="ps-community-card ps-community-card--instagram">
						<div class="ps-community-card-top">
							<div class="ps-community-icon ps-community-icon--instagram" aria-hidden="true">
								<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
									<rect width="20" height="20" x="2" y="2" rx="5" ry="5"/>
									<path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/>
									<line x1="17.5" x2="17.51" y1="6.5" y2="6.5"/>
								</svg>
							</div>
							<span class="ps-community-badge ps-badge-instagram"><?php esc_html_e( 'Visual Gallery', 'promptsyra' ); ?></span>
						</div>
						<h3 class="ps-community-title"><?php esc_html_e( 'Instagram Visual Gallery', 'promptsyra' ); ?></h3>
						<p class="ps-community-handle">@promptsyra</p>
						<p class="ps-community-desc">
							<?php esc_html_e( 'Explore curated portrait prompts, rim lighting setups, cinematic camera angles, and visual styling ideas for Midjourney, FLUX, and modern image models.', 'promptsyra' ); ?>
						</p>
						<div class="ps-community-actions">
							<a href="https://www.instagram.com/promptsyra" target="_blank" rel="noopener noreferrer" class="ps-btn ps-btn-instagram ps-community-btn">
								<span><?php esc_html_e( 'Follow on Instagram', 'promptsyra' ); ?></span>
								<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" x2="21" y1="14" y2="3"/></svg>
							</a>
							<a href="<?php echo esc_url( home_url( '/instagram/' ) ); ?>" class="ps-community-sublink">
								<span><?php esc_html_e( 'Explore Instagram Page →', 'promptsyra' ); ?></span>
							</a>
						</div>
					</article>

					<!-- Facebook Community Updates Card -->
					<article class="ps-community-card ps-community-card--facebook">
						<div class="ps-community-card-top">
							<div class="ps-community-icon ps-community-icon--facebook" aria-hidden="true">
								<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
									<path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/>
								</svg>
							</div>
							<span class="ps-community-badge ps-badge-facebook"><?php esc_html_e( 'Community Updates', 'promptsyra' ); ?></span>
						</div>
						<h3 class="ps-community-title"><?php esc_html_e( 'Facebook Community Updates', 'promptsyra' ); ?></h3>
						<p class="ps-community-handle">PromptSyra</p>
						<p class="ps-community-desc">
							<?php esc_html_e( 'Join creator discussions, discover newly published prompt tutorials, get category highlights, and connect with other AI photography enthusiasts.', 'promptsyra' ); ?>
						</p>
						<div class="ps-community-actions">
							<a href="https://www.facebook.com/people/PromptSyra/61595584906422/" target="_blank" rel="noopener noreferrer" class="ps-btn ps-btn-facebook ps-community-btn">
								<span><?php esc_html_e( 'Follow on Facebook', 'promptsyra' ); ?></span>
								<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" x2="21" y1="14" y2="3"/></svg>
							</a>
							<a href="<?php echo esc_url( home_url( '/facebook/' ) ); ?>" class="ps-community-sublink">
								<span><?php esc_html_e( 'Explore Facebook Page →', 'promptsyra' ); ?></span>
							</a>
						</div>
					</article>

					<!-- WhatsApp Official Channel Card -->
					<article class="ps-community-card ps-community-card--whatsapp">
						<div class="ps-community-card-top">
							<div class="ps-community-icon ps-community-icon--whatsapp" aria-hidden="true">
								<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
									<path d="M3 21l1.65-3.8a9 9 0 1 1 3.4 2.9L3 21"/>
									<path d="M9 10a.5.5 0 0 0 1 0V9a.5.5 0 0 0-1 0v1a5 5 0 0 0 5 5h1a.5.5 0 0 0 0-1h-1a.5.5 0 0 0 0 1"/>
								</svg>
							</div>
							<span class="ps-community-badge ps-badge-whatsapp"><?php esc_html_e( 'Official Channel', 'promptsyra' ); ?></span>
						</div>
						<h3 class="ps-community-title"><?php esc_html_e( 'WhatsApp Official Channel', 'promptsyra' ); ?></h3>
						<p class="ps-community-handle">PromptSyra Broadcast</p>
						<p class="ps-community-desc">
							<?php esc_html_e( 'Get instant mobile notifications whenever trending prompts and camera setups drop. 100% private one-way channel directly on WhatsApp.', 'promptsyra' ); ?>
						</p>
						<div class="ps-community-actions">
							<a href="https://whatsapp.com/channel/0029VbDkVtP7T8bZzLpWMq2d" target="_blank" rel="noopener noreferrer" class="ps-btn ps-btn-whatsapp ps-community-btn">
								<span><?php esc_html_e( 'Join WhatsApp Channel', 'promptsyra' ); ?></span>
								<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" x2="21" y1="14" y2="3"/></svg>
							</a>
							<a href="<?php echo esc_url( home_url( '/whatsapp-channel/' ) ); ?>" class="ps-community-sublink">
								<span><?php esc_html_e( 'Channel Details & FAQs →', 'promptsyra' ); ?></span>
							</a>
						</div>
					</article>
				</div>
			</div>
		</section>

		<!-- H. Frequently Asked Questions + I. Author & Editorial Feature -->
		<?php if ( $ps_show_faq || $ps_show_author ) : ?>
			<section class="ps-faq-author-section">
				<div class="ps-container ps-faq-author-grid">
					<?php if ( $ps_show_faq && ! empty( $ps_faqs ) ) : ?>
						<div class="ps-faq-column" aria-labelledby="ps-faq-heading">
							<p class="ps-section-kicker"><?php esc_html_e( 'Helpful Answers', 'promptsyra' ); ?></p>
							<h2 id="ps-faq-heading" class="ps-section-title"><?php esc_html_e( 'Frequently Asked Questions', 'promptsyra' ); ?></h2>

							<div class="ps-faq-accordion" data-ps-accordion>
								<?php foreach ( $ps_faqs as $f_index => $faq ) : ?>
									<?php
									$btn_id   = 'ps-faq-btn-' . ( $f_index + 1 );
									$panel_id = 'ps-faq-panel-' . ( $f_index + 1 );
									$is_open  = ( 0 === $f_index );
									?>
									<div class="ps-faq-item">
										<h3 class="ps-faq-q-wrap">
											<button
												type="button"
												id="<?php echo esc_attr( $btn_id ); ?>"
												class="ps-faq-trigger"
												aria-expanded="<?php echo $is_open ? 'true' : 'false'; ?>"
												aria-controls="<?php echo esc_attr( $panel_id ); ?>"
											>
												<span><?php echo esc_html( $faq['q'] ); ?></span>
												<span class="ps-faq-icon" aria-hidden="true"><?php echo $is_open ? '−' : '+'; ?></span>
											</button>
										</h3>
										<div
											id="<?php echo esc_attr( $panel_id ); ?>"
											class="ps-faq-panel"
											role="region"
											aria-labelledby="<?php echo esc_attr( $btn_id ); ?>"
											<?php echo $is_open ? '' : 'hidden'; ?>
										>
											<p><?php echo esc_html( $faq['a'] ); ?></p>
										</div>
									</div>
								<?php endforeach; ?>
							</div>
						</div>
					<?php endif; ?>

					<?php if ( $ps_show_author ) : ?>
						<?php
						$ps_about_writers_url = promptsyra_get_verified_page_url( array( 'about-us', 'about' ) );
						$ps_syra_alt          = sprintf(
							/* translators: 1: Author name, 2: Editorial role */
							__( 'Portrait of %1$s, %2$s at PromptSyra.com', 'promptsyra' ),
							$ps_author_syra['display_name'],
							$ps_author_syra['role_label']
						);
						?>
						<aside class="ps-author-column" aria-labelledby="ps-author-heading">
							<div class="ps-lead-author-card">
								<p class="ps-section-kicker"><?php esc_html_e( 'Editorial Feature', 'promptsyra' ); ?></p>
								<div class="ps-author-header">
									<?php if ( ! empty( $ps_author_syra['avatar_url'] ) ) : ?>
										<img
											src="<?php echo esc_url( $ps_author_syra['avatar_url'] ); ?>"
											alt="<?php echo esc_attr( $ps_syra_alt ); ?>"
											class="ps-author-avatar"
											width="80"
											height="80"
											loading="lazy"
											decoding="async"
										/>
									<?php else : ?>
										<div class="ps-author-avatar-placeholder" aria-hidden="true">SA</div>
									<?php endif; ?>

									<div>
										<h2 id="ps-author-heading" class="ps-author-name">
											<?php echo esc_html( $ps_author_syra['display_name'] ); ?>
										</h2>
										<p class="ps-author-role"><?php echo esc_html( $ps_author_syra['role_label'] ); ?></p>
									</div>
								</div>

								<?php if ( ! empty( $ps_author_syra['bio'] ) ) : ?>
									<p class="ps-author-bio"><?php echo esc_html( $ps_author_syra['bio'] ); ?></p>
								<?php else : ?>
									<p class="ps-author-bio ps-author-bio-neutral">
										<?php esc_html_e( 'Browse published AI photo prompts and customization guides curated for PromptSyra.com.', 'promptsyra' ); ?>
									</p>
								<?php endif; ?>

								<?php if ( ! empty( $ps_author_syra['archive_url'] ) ) : ?>
									<a href="<?php echo esc_url( $ps_author_syra['archive_url'] ); ?>" class="ps-btn-primary ps-author-archive-btn">
										<span><?php esc_html_e( 'View Articles by Syra Ansari', 'promptsyra' ); ?></span>
										<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
									</a>
								<?php elseif ( ! empty( $ps_about_writers_url ) ) : ?>
									<a href="<?php echo esc_url( $ps_about_writers_url . '#ps-meet-writers-heading' ); ?>" class="ps-btn-primary ps-author-archive-btn">
										<span><?php esc_html_e( 'Meet Our Editorial Writers', 'promptsyra' ); ?></span>
										<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
									</a>
								<?php endif; ?>

								<!-- Designated 3 Editorial Profiles Directory -->
								<div class="ps-contributors-box">
									<h3 class="ps-contributors-heading"><?php esc_html_e( 'PromptSyra Editorial Authors', 'promptsyra' ); ?></h3>
									<ul class="ps-contributors-list">
										<?php foreach ( $ps_all_authors as $ed_author ) : ?>
											<li class="ps-contributor-row">
												<span class="ps-contributor-name"><?php echo esc_html( $ed_author['display_name'] ); ?></span>
												<?php if ( ! empty( $ed_author['archive_url'] ) ) : ?>
													<a href="<?php echo esc_url( $ed_author['archive_url'] ); ?>" class="ps-contributor-link">
														<?php esc_html_e( 'Author Archive →', 'promptsyra' ); ?>
													</a>
												<?php elseif ( ! empty( $ps_about_writers_url ) ) : ?>
													<a href="<?php echo esc_url( $ps_about_writers_url . '#ps-meet-writers-heading' ); ?>" class="ps-contributor-link">
														<?php esc_html_e( 'Writer Profile →', 'promptsyra' ); ?>
													</a>
												<?php endif; ?>
											</li>
										<?php endforeach; ?>
									</ul>
								</div>
							</div>
						</aside>
					<?php endif; ?>
				</div>
			</section>

			<?php
			// Valid FAQPage Structured Data ONLY when FAQ section is visible, populated, and enabled.
			$ps_enable_faq_schema = (bool) get_theme_mod( 'promptsyra_enable_faq_schema', true );
			if ( $ps_show_faq && $ps_enable_faq_schema && ! empty( $ps_faqs ) ) :
				$ps_faq_entities = array();
				foreach ( $ps_faqs as $faq_row ) {
					$ps_faq_entities[] = array(
						'@type'          => 'Question',
						'name'           => wp_strip_all_tags( $faq_row['q'] ),
						'acceptedAnswer' => array(
							'@type' => 'Answer',
							'text'  => wp_strip_all_tags( $faq_row['a'] ),
						),
					);
				}
				$ps_schema_payload = array(
					'@context'   => 'https://schema.org',
					'@type'      => 'FAQPage',
					'mainEntity' => $ps_faq_entities,
				);
				?>
				<script type="application/ld+json"><?php echo wp_json_encode( $ps_schema_payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ); ?></script>
			<?php endif; ?>
		<?php endif; ?>
	</main>

	<?php get_template_part( 'template-parts/footer', 'promptsyra' ); ?>
</div>

<?php wp_footer(); ?>
</body>
</html>
