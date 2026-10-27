<?php
/**
 * Backup of PromptSyra v2.0.0 single-standalone.php prior to v2.1.0 Single-Post Rebuild.
 * Kept for instant reference and rollback safety.
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
<body <?php body_class( 'ps-single-body' ); ?>>
<?php wp_body_open(); ?>

<div class="ps-page-shell">
	<?php get_template_part( 'template-parts/header', 'promptsyra' ); ?>

	<main id="ps-main-content" class="ps-front-main">
		<?php
		while ( have_posts() ) :
			the_post();
			$ps_post_cats = get_the_category();
			$ps_author_id = (int) get_the_author_meta( 'ID' );
			?>
			<article id="post-<?php the_ID(); ?>" <?php post_class( 'ps-single-article' ); ?>>
				<div class="ps-container ps-single-container">
					<nav class="ps-breadcrumbs" aria-label="<?php esc_attr_e( 'Breadcrumb', 'promptsyra' ); ?>">
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>">PromptSyra.com</a>
						<?php if ( ! empty( $ps_post_cats ) ) : ?>
							<span aria-hidden="true">/</span>
							<a href="<?php echo esc_url( get_category_link( $ps_post_cats[0]->term_id ) ); ?>">
								<?php echo esc_html( $ps_post_cats[0]->name ); ?>
							</a>
						<?php endif; ?>
					</nav>

					<header class="ps-single-header">
						<h1 class="ps-single-title"><?php the_title(); ?></h1>
						<div class="ps-post-meta-line">
							<span>
								<?php esc_html_e( 'By', 'promptsyra' ); ?>
								<a href="<?php echo esc_url( get_author_posts_url( $ps_author_id ) ); ?>" class="ps-single-author-link">
									<?php the_author(); ?>
								</a>
							</span>
							<span aria-hidden="true">·</span>
							<time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>">
								<?php echo esc_html( get_the_date() ); ?>
							</time>
						</div>
					</header>

					<?php if ( has_post_thumbnail() ) : ?>
						<figure class="ps-single-featured-media">
							<?php the_post_thumbnail( 'large', array( 'class' => 'ps-single-featured-img' ) ); ?>
						</figure>
					<?php endif; ?>

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
				</div>
			</article>
		<?php endwhile; ?>
	</main>

	<?php get_template_part( 'template-parts/footer', 'promptsyra' ); ?>
</div>

<?php wp_footer(); ?>
</body>
</html>
