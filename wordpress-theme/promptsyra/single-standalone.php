<?php
/**
 * Single Post Template — PromptSyra.com (v2.1.0)
 *
 * Recreates the reference 2-column editorial & AI prompt article layout
 * (main article column + 340px sticky right sidebar) with PromptSyra.com branding
 * while dynamically rendering each WordPress post's real title, content, metadata,
 * featured image, author profile, tables, interactive prompt cards, and comments.
 *
 * @package PromptSyra
 * @version 2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<?php wp_head(); ?>
</head>
<body <?php body_class( 'ps-single-body' ); ?>>
<?php wp_body_open(); ?>

<div class="ps-page-shell">
	<?php get_template_part( 'template-parts/header', 'promptsyra' ); ?>

	<main id="ps-main-content" class="ps-single-main">
		<?php
		while ( have_posts() ) :
			the_post();

			$ps_post_id          = (int) get_the_ID();
			$ps_post_cats        = get_the_category( $ps_post_id );
			$ps_primary_cat      = ! empty( $ps_post_cats ) ? $ps_post_cats[0] : null;
			$ps_author_id        = (int) get_the_author_meta( 'ID' );
			$ps_author_name      = get_the_author();
			$ps_author_role      = function_exists( 'promptsyra_get_user_editorial_role' ) ? promptsyra_get_user_editorial_role( $ps_author_id ) : __( 'Author', 'promptsyra' );
			$ps_author_url       = get_author_posts_url( $ps_author_id );
			$ps_author_bio       = trim( (string) get_the_author_meta( 'description', $ps_author_id ) );
			$ps_custom_photo     = trim( (string) get_user_meta( $ps_author_id, 'promptsyra_profile_photo_url', true ) );
			$ps_avatar_url       = ! empty( $ps_custom_photo ) ? $ps_custom_photo : get_avatar_url( $ps_author_id, array( 'size' => 160 ) );
			$ps_author_alt       = sprintf(
				/* translators: 1: Author name, 2: Editorial role */
				__( 'Portrait of %1$s, %2$s at PromptSyra.com', 'promptsyra' ),
				$ps_author_name,
				$ps_author_role
			);
			$ps_author_initials  = strtoupper( substr( preg_replace( '/[^a-zA-Z0-9]/', '', (string) $ps_author_name ), 0, 2 ) );
			$ps_author_initials  = '' !== $ps_author_initials ? $ps_author_initials : 'PS';
			$ps_read_time        = function_exists( 'promptsyra_get_reading_time' ) ? promptsyra_get_reading_time( $ps_post_id ) : __( '1 min read', 'promptsyra' );
			$ps_pub_timestamp    = (int) get_the_time( 'U' );
			$ps_mod_timestamp    = (int) get_the_modified_time( 'U' );
			$ps_show_updated     = ( $ps_mod_timestamp > ( $ps_pub_timestamp + DAY_IN_SECONDS ) );
			?>

			<!-- 2-Column Article + Sticky Sidebar Container -->
			<div class="ps-single-layout">
				<!-- Left Column: Main Article -->
				<div class="ps-single-main-col">
					<article id="post-<?php the_ID(); ?>" <?php post_class( 'ps-single-article-card' ); ?>>
						<header class="ps-single-article-header">
							<h1 class="ps-entry-title"><?php the_title(); ?></h1>

							<?php if ( has_excerpt() ) : ?>
								<p class="ps-entry-lead"><?php echo esc_html( get_the_excerpt() ); ?></p>
							<?php endif; ?>

							<!-- Author + Publication Date + Reading Time Meta Bar -->
							<div class="ps-entry-meta-bar">
								<div class="ps-meta-author-box">
									<a href="<?php echo esc_url( $ps_author_url ); ?>" class="ps-meta-avatar-link" aria-label="<?php echo esc_attr( $ps_author_name ); ?>">
										<?php if ( ! empty( $ps_avatar_url ) ) : ?>
											<img src="<?php echo esc_url( $ps_avatar_url ); ?>" alt="<?php echo esc_attr( $ps_author_alt ); ?>" class="ps-meta-avatar" width="44" height="44" loading="eager" decoding="async" />
										<?php else : ?>
											<span class="ps-meta-avatar-fallback" aria-hidden="true"><?php echo esc_html( $ps_author_initials ); ?></span>
										<?php endif; ?>
									</a>
									<div class="ps-meta-author-info">
										<span class="ps-meta-byline">
											<?php esc_html_e( 'By', 'promptsyra' ); ?>
											<a href="<?php echo esc_url( $ps_author_url ); ?>" class="ps-meta-author-link"><?php echo esc_html( $ps_author_name ); ?></a>
										</span>
										<span class="ps-meta-role-tag"><?php echo esc_html( $ps_author_role ); ?></span>
									</div>
								</div>

								<div class="ps-meta-details">
									<span class="ps-meta-item">
										<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
											<rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
											<line x1="16" y1="2" x2="16" y2="6"></line>
											<line x1="8" y1="2" x2="8" y2="6"></line>
											<line x1="3" y1="10" x2="21" y2="10"></line>
										</svg>
										<?php if ( $ps_show_updated ) : ?>
											<span>
												<?php esc_html_e( 'Updated:', 'promptsyra' ); ?>
												<strong>
													<time datetime="<?php echo esc_attr( get_the_modified_date( DATE_W3C ) ); ?>">
														<?php echo esc_html( get_the_modified_date() ); ?>
													</time>
												</strong>
											</span>
										<?php else : ?>
											<span>
												<?php esc_html_e( 'Published:', 'promptsyra' ); ?>
												<strong>
													<time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>">
														<?php echo esc_html( get_the_date() ); ?>
													</time>
												</strong>
											</span>
										<?php endif; ?>
									</span>

									<span class="ps-meta-dot" aria-hidden="true">•</span>

									<span class="ps-meta-item">
										<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
											<circle cx="12" cy="12" r="10"></circle>
											<polyline points="12 6 12 12 16 14"></polyline>
										</svg>
										<span>
											<?php esc_html_e( 'Read time:', 'promptsyra' ); ?>
											<strong><?php echo esc_html( $ps_read_time ); ?></strong>
										</span>
									</span>
								</div>
							</div>
						</header>

						<?php if ( has_post_thumbnail() ) : ?>
							<figure class="ps-entry-featured-media">
								<?php
								the_post_thumbnail(
									'large',
									array(
										'class'         => 'ps-featured-media-img',
										'fetchpriority' => 'high',
									)
								);
								$ps_thumb_caption = wp_get_attachment_caption( get_post_thumbnail_id() );
								if ( ! empty( $ps_thumb_caption ) ) :
									?>
									<figcaption class="ps-entry-featured-caption">
										<?php echo esc_html( $ps_thumb_caption ); ?>
									</figcaption>
								<?php endif; ?>
							</figure>
						<?php endif; ?>

						<!-- Dynamic Post Content (Preserves /hello-world/ & renders tables, TOC, and AI Prompt Cards) -->
						<div class="entry-content ps-single-entry">
							<?php
							the_content();

							wp_link_pages(
								array(
									'before' => '<div class="ps-page-links">' . esc_html__( 'Pages:', 'promptsyra' ),
									'after'  => '</div>',
								)
							);
							?>
						</div>
					</article>

					<?php
					// Previous / Next Post Navigation (shown when adjacent published posts exist).
					$ps_prev_post = get_previous_post();
					$ps_next_post = get_next_post();
					if ( ( $ps_prev_post instanceof WP_Post ) || ( $ps_next_post instanceof WP_Post ) ) :
						?>
						<nav class="navigation post-navigation ps-post-navigation" aria-label="<?php esc_attr_e( 'Post navigation', 'promptsyra' ); ?>">
							<div class="ps-post-nav-grid">
								<?php if ( $ps_prev_post instanceof WP_Post ) : ?>
									<a href="<?php echo esc_url( get_permalink( $ps_prev_post->ID ) ); ?>" class="ps-post-nav-card is-prev" rel="prev">
										<span class="ps-post-nav-label"><?php esc_html_e( '← Previous Article', 'promptsyra' ); ?></span>
										<span class="ps-post-nav-title"><?php echo esc_html( get_the_title( $ps_prev_post->ID ) ); ?></span>
									</a>
								<?php else : ?>
									<div></div>
								<?php endif; ?>

								<?php if ( $ps_next_post instanceof WP_Post ) : ?>
									<a href="<?php echo esc_url( get_permalink( $ps_next_post->ID ) ); ?>" class="ps-post-nav-card is-next" rel="next">
										<span class="ps-post-nav-label"><?php esc_html_e( 'Next Article →', 'promptsyra' ); ?></span>
										<span class="ps-post-nav-title"><?php echo esc_html( get_the_title( $ps_next_post->ID ) ); ?></span>
									</a>
								<?php endif; ?>
							</div>
						</nav>
					<?php endif; ?>

					<?php
					// Related Articles from the same primary category.
					if ( $ps_primary_cat instanceof WP_Term ) :
						$ps_related = new WP_Query(
							array(
								'post_type'           => 'post',
								'post_status'         => 'publish',
								'posts_per_page'      => 3,
								'post__not_in'        => array( $ps_post_id ),
								'cat'                 => (int) $ps_primary_cat->term_id,
								'ignore_sticky_posts' => true,
								'no_found_rows'       => true,
							)
						);

						if ( $ps_related->have_posts() ) :
							?>
							<section class="ps-single-related" aria-labelledby="ps-related-heading">
								<h2 id="ps-related-heading" class="ps-single-related-heading">
									<?php esc_html_e( 'Related Prompts', 'promptsyra' ); ?>
								</h2>
								<div class="ps-single-related-grid">
									<?php
									while ( $ps_related->have_posts() ) :
										$ps_related->the_post();
										?>
										<article <?php post_class( 'ps-related-mini-card' ); ?>>
											<a href="<?php the_permalink(); ?>" class="ps-related-mini-thumb-link" tabindex="-1" aria-hidden="true">
												<?php if ( has_post_thumbnail() ) : ?>
													<?php the_post_thumbnail( 'medium_large', array( 'class' => 'ps-related-mini-thumb', 'loading' => 'lazy' ) ); ?>
												<?php else : ?>
													<div class="ps-related-mini-fallback"><span>PromptSyra.com</span></div>
												<?php endif; ?>
											</a>
											<div class="ps-related-mini-body">
												<h3 class="ps-related-mini-title">
													<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
												</h3>
												<time class="ps-related-mini-date" datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>">
													<?php echo esc_html( get_the_date() ); ?>
												</time>
											</div>
										</article>
									<?php endwhile; ?>
									<?php wp_reset_postdata(); ?>
								</div>
							</section>
							<?php
						endif;
					endif;

					// Preserve WordPress comments when enabled on the post.
					if ( comments_open() || get_comments_number() ) :
						?>
						<div class="ps-single-comments-wrap">
							<?php comments_template(); ?>
						</div>
					<?php endif; ?>
				</div>

				<!-- Right Column: Sticky Reference Sidebar (Post Author + Trending Prompts + Categories) -->
				<aside class="ps-single-sidebar-col" aria-label="<?php esc_attr_e( 'Article Sidebar', 'promptsyra' ); ?>">
					<div class="ps-single-sidebar">
						<!-- Sidebar Widget 1: Post Author (Uses strictly real WordPress user profile data) -->
						<div class="ps-sidebar-widget">
							<h2 class="ps-widget-title"><?php esc_html_e( 'Post Author', 'promptsyra' ); ?></h2>
							<div class="ps-sidebar-author-card">
								<div class="ps-sidebar-author-avatar-wrap">
									<a href="<?php echo esc_url( $ps_author_url ); ?>" aria-label="<?php echo esc_attr( $ps_author_name ); ?>">
										<?php if ( ! empty( $ps_avatar_url ) ) : ?>
											<img src="<?php echo esc_url( $ps_avatar_url ); ?>" alt="<?php echo esc_attr( $ps_author_alt ); ?>" class="ps-sidebar-author-avatar" width="76" height="76" loading="lazy" decoding="async" />
										<?php else : ?>
											<span class="ps-sidebar-author-fallback" aria-hidden="true"><?php echo esc_html( $ps_author_initials ); ?></span>
										<?php endif; ?>
									</a>
								</div>

								<h3 class="ps-sidebar-author-name">
									<a href="<?php echo esc_url( $ps_author_url ); ?>"><?php echo esc_html( $ps_author_name ); ?></a>
								</h3>
								<p class="ps-meta-role-tag" style="margin:0 0 0.5rem;"><?php echo esc_html( $ps_author_role ); ?></p>

								<?php if ( ! empty( $ps_author_bio ) ) : ?>
									<p class="ps-sidebar-author-bio"><?php echo esc_html( $ps_author_bio ); ?></p>
								<?php endif; ?>

								<a href="<?php echo esc_url( $ps_author_url ); ?>" class="ps-sidebar-author-btn">
									<span><?php esc_html_e( 'View All Guides', 'promptsyra' ); ?></span>
									<span aria-hidden="true">→</span>
								</a>
							</div>
						</div>

						<!-- Sidebar Widget 2: Trending Prompts -->
						<div class="ps-sidebar-widget">
							<h2 class="ps-widget-title"><?php esc_html_e( 'Trending Prompts', 'promptsyra' ); ?></h2>
							<?php
							$ps_trending_query = new WP_Query(
								array(
									'post_type'           => 'post',
									'post_status'         => 'publish',
									'posts_per_page'      => 4,
									'post__not_in'        => array( $ps_post_id ),
									'ignore_sticky_posts' => true,
									'no_found_rows'       => true,
								)
							);

							if ( $ps_trending_query->have_posts() ) :
								?>
								<ul class="ps-trending-list">
									<?php
									while ( $ps_trending_query->have_posts() ) :
										$ps_trending_query->the_post();
										?>
										<li class="ps-trending-item">
											<a href="<?php the_permalink(); ?>" class="ps-trending-link">
												<span class="ps-trending-thumb">
													<?php if ( has_post_thumbnail() ) : ?>
														<?php the_post_thumbnail( 'thumbnail', array( 'loading' => 'lazy' ) ); ?>
													<?php else : ?>
														<span class="ps-trending-thumb-fallback">PS</span>
													<?php endif; ?>
												</span>
												<span class="ps-trending-info">
													<span class="ps-trending-post-title"><?php the_title(); ?></span>
													<time class="ps-trending-date" datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>">
														<?php echo esc_html( get_the_date() ); ?>
													</time>
												</span>
											</a>
										</li>
									<?php endwhile; ?>
									<?php wp_reset_postdata(); ?>
								</ul>
							<?php else : ?>
								<p class="ps-trending-empty">
									<?php esc_html_e( 'More PromptSyra guides will appear here as new articles are published.', 'promptsyra' ); ?>
								</p>
							<?php endif; ?>
						</div>

						<!-- Sidebar Widget 3: Categories (PromptSyra Approved Categories + Existing WP Categories Fallback) -->
						<div class="ps-sidebar-widget">
							<h2 class="ps-widget-title"><?php esc_html_e( 'Categories', 'promptsyra' ); ?></h2>
							<?php
							$ps_sidebar_cats = array();
							if ( function_exists( 'promptsyra_get_approved_categories' ) && function_exists( 'promptsyra_resolve_category_data' ) ) {
								foreach ( array_keys( promptsyra_get_approved_categories() ) as $ps_slug ) {
									$ps_resolved = promptsyra_resolve_category_data( $ps_slug );
									if ( ! empty( $ps_resolved['exists'] ) && ! empty( $ps_resolved['url'] ) ) {
										$ps_sidebar_cats[] = array(
											'name' => (string) $ps_resolved['name'],
											'url'  => (string) $ps_resolved['url'],
										);
									}
								}
							}

							if ( empty( $ps_sidebar_cats ) ) {
								$ps_wp_cats = get_categories(
									array(
										'orderby'    => 'count',
										'order'      => 'DESC',
										'hide_empty' => false,
										'number'     => 5,
									)
								);
								foreach ( $ps_wp_cats as $ps_wp_cat ) {
									$ps_cat_link = get_category_link( $ps_wp_cat->term_id );
									if ( ! is_wp_error( $ps_cat_link ) ) {
										$ps_sidebar_cats[] = array(
											'name' => $ps_wp_cat->name,
											'url'  => $ps_cat_link,
										);
									}
								}
							}
							?>
							<?php if ( ! empty( $ps_sidebar_cats ) ) : ?>
								<ul class="ps-sidebar-cat-list">
									<?php foreach ( $ps_sidebar_cats as $ps_sc ) : ?>
										<li>
											<a href="<?php echo esc_url( $ps_sc['url'] ); ?>" class="ps-sidebar-cat-link">
												<span><?php echo esc_html( $ps_sc['name'] ); ?></span>
												<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
													<polyline points="9 18 15 12 9 6"></polyline>
												</svg>
											</a>
										</li>
									<?php endforeach; ?>
								</ul>
							<?php endif; ?>
						</div>
					</div>
				</aside>
			</div>
		<?php endwhile; ?>
	</main>

	<?php get_template_part( 'template-parts/footer', 'promptsyra' ); ?>
</div>

<?php wp_footer(); ?>
</body>
</html>
