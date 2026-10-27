<?php
/**
 * 404 Not Found Template — PromptSyra.com
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
<body <?php body_class( 'ps-404-body' ); ?>>
<?php wp_body_open(); ?>

<div class="ps-page-shell">
	<?php get_template_part( 'template-parts/header', 'promptsyra' ); ?>

	<main id="ps-main-content" class="ps-front-main">
		<section class="ps-hero-section">
			<div class="ps-container" style="max-width:680px;text-align:center;">
				<p class="ps-eyebrow"><?php esc_html_e( '404 · Page Not Found', 'promptsyra' ); ?></p>
				<h1 class="ps-section-title" style="margin-bottom:0.75rem;">
					<?php esc_html_e( 'We could not find that page', 'promptsyra' ); ?>
				</h1>
				<p class="ps-hero-desc" style="margin:0 auto 1.75rem;">
					<?php esc_html_e( 'Search our AI photo prompt library below or return to the PromptSyra.com homepage.', 'promptsyra' ); ?>
				</p>

				<form role="search" method="get" class="ps-hero-search" style="margin:0 auto 1.5rem;" action="<?php echo esc_url( home_url( '/' ) ); ?>">
					<label for="ps-404-s" class="ps-sr-only"><?php esc_html_e( 'Search AI photo prompts', 'promptsyra' ); ?></label>
					<input
						type="search"
						id="ps-404-s"
						name="s"
						placeholder="<?php esc_attr_e( 'Search photo prompts...', 'promptsyra' ); ?>"
						class="ps-hero-search-field"
					/>
					<button type="submit" class="ps-btn-primary"><?php esc_html_e( 'Search', 'promptsyra' ); ?></button>
				</form>

				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="ps-btn-secondary">
					<?php esc_html_e( 'Back to PromptSyra.com Home', 'promptsyra' ); ?>
				</a>
			</div>
		</section>
	</main>

	<?php get_template_part( 'template-parts/footer', 'promptsyra' ); ?>
</div>

<?php wp_footer(); ?>
</body>
</html>
