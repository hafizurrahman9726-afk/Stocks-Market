<?php
/**
 * Backup of PromptSyra v2.1.0 single-standalone.php prior to v2.2.0 Single-Post Final UI Fix
 * (removal of single-post breadcrumb row & category badge + redesigned comments.php).
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
			$ps_author_url       = get_author_posts_url( $ps_author_id );
			$ps_author_bio       = trim( (string) get_the_author_meta( 'description', $ps_author_id ) );
			$ps_custom_photo     = trim( (string) get_user_meta( $ps_author_id, 'promptsyra_profile_photo_url', true ) );
			$ps_avatar_url       = ! empty( $ps_custom_photo ) ? $ps_custom_photo : get_avatar_url( $ps_author_id, array( 'size' => 160 ) );
			$ps_author_initials  = strtoupper( substr( preg_replace( '/[^a-zA-Z0-9]/', '', (string) $ps_author_name ), 0, 2 ) );
			$ps_author_initials  = '' !== $ps_author_initials ? $ps_author_initials : 'PS';
			$ps_read_time        = function_exists( 'promptsyra_get_reading_time' ) ? promptsyra_get_reading_time( $ps_post_id ) : __( '1 min read', 'promptsyra' );
			$ps_pub_timestamp    = (int) get_the_time( 'U' );
			$ps_mod_timestamp    = (int) get_the_modified_time( 'U' );
			$ps_show_updated     = ( $ps_mod_timestamp > ( $ps_pub_timestamp + DAY_IN_SECONDS ) );
			?>

			<div class="ps-single-breadcrumb-wrap">
				<nav class="ps-single-breadcrumbs" aria-label="<?php esc_attr_e( 'Breadcrumb', 'promptsyra' ); ?>">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>">PromptSyra.com</a>
					<?php if ( $ps_primary_cat instanceof WP_Term ) : ?>
						<span class="ps-bc-sep" aria-hidden="true">›</span>
						<a href="<?php echo esc_url( get_category_link( $ps_primary_cat->term_id ) ); ?>">
							<?php echo esc_html( $ps_primary_cat->name ); ?>
						</a>
					<?php endif; ?>
					<span class="ps-bc-sep" aria-hidden="true">›</span>
					<span class="ps-bc-current" aria-current="page"><?php the_title(); ?></span>
				</nav>
			</div>

			<div class="ps-single-layout">
				<div class="ps-single-main-col">
					<article id="post-<?php the_ID(); ?>" <?php post_class( 'ps-single-article-card' ); ?>>
						<header class="ps-single-article-header">
							<?php if ( ! empty( $ps_post_cats ) ) : ?>
								<div class="ps-entry-cat-wrap">
									<?php foreach ( array_slice( $ps_post_cats, 0, 2 ) as $ps_cat_item ) : ?>
										<a href="<?php echo esc_url( get_category_link( $ps_cat_item->term_id ) ); ?>" class="ps-entry-cat-badge">
											<?php echo esc_html( $ps_cat_item->name ); ?>
										</a>
									<?php endforeach; ?>
								</div>
							<?php endif; ?>
							<h1 class="ps-entry-title"><?php the_title(); ?></h1>
						</header>
						<div class="entry-content ps-single-entry"><?php the_content(); ?></div>
					</article>
				</div>
			</div>
		<?php endwhile; ?>
	</main>
	<?php get_template_part( 'template-parts/footer', 'promptsyra' ); ?>
</div>
<?php wp_footer(); ?>
</body>
</html>
