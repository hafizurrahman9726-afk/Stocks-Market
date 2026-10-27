<?php
/**
 * Main Index Fallback Template — PromptSyra.com
 *
 * Required by WordPress Theme Installer (Appearance -> Themes -> Upload Theme).
 * Renders blog listings, archives, and fallback queries cleanly.
 *
 * @package PromptSyra
 * @version 2.0.0
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
<body <?php body_class( 'ps-archive-body' ); ?>>
<?php wp_body_open(); ?>

<div class="ps-page-shell">
	<a class="ps-skip-link" href="#ps-main-content"><?php esc_html_e( 'Skip to main content', 'promptsyra' ); ?></a>

	<?php get_template_part( 'template-parts/header', 'promptsyra' ); ?>

	<main id="ps-main-content" class="ps-front-main">
		<section class="ps-archive-banner">
			<div class="ps-container">
				<h1 class="ps-section-title">
					<?php
					if ( is_home() && ! is_front_page() ) {
						single_post_title();
					} elseif ( is_archive() ) {
						the_archive_title();
					} else {
						esc_html_e( 'Latest AI Photo Prompts', 'promptsyra' );
					}
					?>
				</h1>
			</div>
		</section>

		<section class="ps-featured-section">
			<div class="ps-container">
				<?php if ( have_posts() ) : ?>
					<div class="ps-posts-grid">
						<?php
						while ( have_posts() ) :
							the_post();
							$p_cats    = get_the_category();
							$cat_label = ! empty( $p_cats ) ? $p_cats[0]->name : __( 'AI Prompts', 'promptsyra' );
							?>
							<article <?php post_class( 'ps-post-card' ); ?>>
								<a href="<?php the_permalink(); ?>" class="ps-post-media-link" tabindex="-1" aria-hidden="true">
									<?php if ( has_post_thumbnail() ) : ?>
										<?php the_post_thumbnail( 'medium_large', array( 'class' => 'ps-post-thumb', 'loading' => 'lazy' ) ); ?>
									<?php else : ?>
										<div class="ps-post-thumb-fallback"><span>PromptSyra.com</span></div>
									<?php endif; ?>
								</a>
								<div class="ps-post-card-body">
									<div class="ps-post-meta-line">
										<span><?php echo esc_html( $cat_label ); ?></span>
										<span aria-hidden="true">·</span>
										<time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
									</div>
									<h2 class="ps-post-card-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
									<p class="ps-post-card-excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 18, '…' ) ); ?></p>
									<a href="<?php the_permalink(); ?>" class="ps-post-read-link">
										<span><?php esc_html_e( 'View Prompt & Notes', 'promptsyra' ); ?></span>
										<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
									</a>
								</div>
							</article>
						<?php endwhile; ?>
					</div>

					<div class="ps-pagination">
						<?php the_posts_pagination( array( 'mid_size' => 2 ) ); ?>
					</div>
				<?php else : ?>
					<div class="ps-empty-posts">
						<p><?php esc_html_e( 'No published content found.', 'promptsyra' ); ?></p>
					</div>
				<?php endif; ?>
			</div>
		</section>
	</main>

	<?php get_template_part( 'template-parts/footer', 'promptsyra' ); ?>
</div>

<?php wp_footer(); ?>
</body>
</html>
