<?php
/**
 * Author Archive Template — PromptSyra.com (v2.3.0)
 *
 * Displays real WordPress author profile data (portrait, editorial role,
 * biography, and attributed articles) for Syra Ansari, Arjun Patel, Neha Joshi,
 * and existing site authors without fabricating credentials or exposing emails.
 *
 * @package PromptSyra
 * @version 2.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$ps_queried_author = get_queried_object();
$ps_author_id      = ( $ps_queried_author instanceof WP_User ) ? (int) $ps_queried_author->ID : (int) get_the_author_meta( 'ID' );
$ps_author_name    = ( $ps_queried_author instanceof WP_User ) ? $ps_queried_author->display_name : get_the_author();
$ps_author_role    = function_exists( 'promptsyra_get_user_editorial_role' ) ? promptsyra_get_user_editorial_role( $ps_author_id ) : __( 'Author', 'promptsyra' );
$ps_author_bio     = trim( (string) get_the_author_meta( 'description', $ps_author_id ) );
$ps_custom_photo   = trim( (string) get_user_meta( $ps_author_id, 'promptsyra_profile_photo_url', true ) );
$ps_avatar_url     = ! empty( $ps_custom_photo ) ? $ps_custom_photo : get_avatar_url( $ps_author_id, array( 'size' => 192 ) );
$ps_about_url      = function_exists( 'promptsyra_get_verified_page_url' ) ? promptsyra_get_verified_page_url( array( 'about-us', 'about' ) ) : '';
$ps_post_count     = count_user_posts( $ps_author_id, 'post', true );
$ps_portrait_alt   = sprintf(
	/* translators: 1: Author name, 2: Editorial role */
	__( 'Portrait of %1$s, %2$s at PromptSyra.com', 'promptsyra' ),
	$ps_author_name,
	$ps_author_role
);
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
	<?php get_template_part( 'template-parts/header', 'promptsyra' ); ?>

	<main id="ps-main-content" class="ps-front-main">
		<section class="ps-archive-banner">
			<div class="ps-container">
				<nav class="ps-breadcrumbs" aria-label="<?php esc_attr_e( 'Breadcrumb', 'promptsyra' ); ?>">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>">PromptSyra.com</a>
					<span aria-hidden="true">/</span>
					<?php if ( ! empty( $ps_about_url ) ) : ?>
						<a href="<?php echo esc_url( $ps_about_url ); ?>"><?php esc_html_e( 'About Us', 'promptsyra' ); ?></a>
						<span aria-hidden="true">/</span>
					<?php else : ?>
						<span><?php esc_html_e( 'Authors', 'promptsyra' ); ?></span>
						<span aria-hidden="true">/</span>
					<?php endif; ?>
					<span><?php echo esc_html( $ps_author_name ); ?></span>
				</nav>

				<div class="ps-author-archive-card">
					<?php if ( ! empty( $ps_avatar_url ) ) : ?>
						<img
							src="<?php echo esc_url( $ps_avatar_url ); ?>"
							alt="<?php echo esc_attr( $ps_portrait_alt ); ?>"
							class="ps-author-archive-portrait"
							width="96"
							height="96"
							decoding="async"
						/>
					<?php endif; ?>

					<div>
						<h1 class="ps-section-title"><?php echo esc_html( $ps_author_name ); ?></h1>
						<p class="ps-author-archive-role"><?php echo esc_html( $ps_author_role ); ?> · PromptSyra.com</p>

						<?php if ( ! empty( $ps_author_bio ) ) : ?>
							<p class="ps-editorial-lead"><?php echo esc_html( $ps_author_bio ); ?></p>
						<?php elseif ( current_user_can( 'edit_user', $ps_author_id ) ) : ?>
							<p class="ps-admin-flag">
								<a href="<?php echo esc_url( get_edit_user_link( $ps_author_id ) ); ?>">
									<?php esc_html_e( 'Admin Notice: Add author biography in Users -> Profile.', 'promptsyra' ); ?>
								</a>
							</p>
						<?php endif; ?>

						<p class="ps-author-archive-meta">
							<?php
							printf(
								/* translators: %d: published article count */
								esc_html( _n( '%d Published Article', '%d Published Articles', (int) $ps_post_count, 'promptsyra' ) ),
								(int) $ps_post_count
							);
							?>
						</p>
					</div>
				</div>
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
							$cat_title = ! empty( $p_cats ) ? $p_cats[0]->name : __( 'AI Prompts', 'promptsyra' );
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
										<span><?php echo esc_html( $cat_title ); ?></span>
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
						<p><?php esc_html_e( 'No published articles are currently attributed to this contributor yet.', 'promptsyra' ); ?></p>
						<p style="margin-top:0.5rem;">
							<a href="<?php echo esc_url( home_url( '/#featured-prompts' ) ); ?>">
								<?php esc_html_e( 'Browse Featured AI Prompts →', 'promptsyra' ); ?>
							</a>
						</p>
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
