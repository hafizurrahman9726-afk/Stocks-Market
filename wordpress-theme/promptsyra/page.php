<?php
/**
 * Standard & Essential Pages Template — PromptSyra.com (v2.3.0)
 *
 * Renders WordPress pages with clean editorial typography:
 * - About Us (/about-us/) & Contact Us (/contact-us/): 1080px balanced editorial/form grid
 * - Privacy Policy, Disclaimer, Terms & Conditions: 760px unboxed long-form legal prose
 * - Standard custom pages (/guides/, /categories/, /prompt-builder/): 820px clean prose
 *
 * Includes smart legacy-stub protection: if one of the 5 essential pages still
 * contains an unedited 1-sentence AIPromptZyra or WordPress "Suggested text:" stub
 * prior to running the 1-click WP-Admin sync, it renders the complete PromptSyra v2.3.0
 * content dynamically while switching seamlessly to editable the_content() once synced.
 *
 * @package PromptSyra
 * @version 2.3.0
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
<body <?php body_class( 'ps-page-body' ); ?>>
<?php wp_body_open(); ?>

<div class="ps-page-shell">
	<?php get_template_part( 'template-parts/header', 'promptsyra' ); ?>

	<main id="ps-main-content" class="ps-front-main">
		<?php
		while ( have_posts() ) :
			the_post();

			$ps_current_page  = get_post();
			$ps_matched_spec  = ( $ps_current_page instanceof WP_Post && function_exists( 'promptsyra_match_essential_page_spec' ) )
				? promptsyra_match_essential_page_spec( $ps_current_page )
				: null;
			$ps_use_fallback  = (
				null !== $ps_matched_spec &&
				$ps_current_page instanceof WP_Post &&
				function_exists( 'promptsyra_is_legacy_stub_page' ) &&
				promptsyra_is_legacy_stub_page( $ps_current_page )
			);

			$ps_layout_mode   = ( null !== $ps_matched_spec && ! empty( $ps_matched_spec['layout_mode'] ) ) ? $ps_matched_spec['layout_mode'] : 'standard';
			$ps_is_legal      = ( 'legal' === $ps_layout_mode );
			$ps_display_title = $ps_use_fallback ? $ps_matched_spec['title'] : get_the_title();
			$ps_subtitle      = ( null !== $ps_matched_spec && ! empty( $ps_matched_spec['subtitle'] ) ) ? $ps_matched_spec['subtitle'] : '';

			$ps_container_mod = 'ps-essential-container--standard';
			if ( $ps_is_legal ) {
				$ps_container_mod = 'ps-essential-container--legal';
			} elseif ( in_array( $ps_layout_mode, array( 'editorial', 'contact' ), true ) ) {
				$ps_container_mod = '';
			}
			?>
			<header class="ps-essential-hero <?php echo $ps_is_legal ? 'ps-essential-hero--legal' : ''; ?>">
				<div class="ps-essential-hero-inner">
					<?php if ( $ps_is_legal ) : ?>
						<p class="ps-essential-kicker"><?php esc_html_e( 'Website Policy & Legal Information', 'promptsyra' ); ?></p>
					<?php elseif ( 'editorial' === $ps_layout_mode ) : ?>
						<p class="ps-essential-kicker"><?php esc_html_e( 'About PromptSyra.com', 'promptsyra' ); ?></p>
					<?php elseif ( 'contact' === $ps_layout_mode ) : ?>
						<p class="ps-essential-kicker"><?php esc_html_e( 'Reader Support & Editorial Inbox', 'promptsyra' ); ?></p>
					<?php endif; ?>

					<h1 class="ps-essential-title"><?php echo esc_html( $ps_display_title ); ?></h1>

					<?php if ( ! empty( $ps_subtitle ) ) : ?>
						<p class="ps-essential-subtitle"><?php echo esc_html( $ps_subtitle ); ?></p>
					<?php elseif ( has_excerpt() ) : ?>
						<p class="ps-essential-subtitle"><?php echo esc_html( get_the_excerpt() ); ?></p>
					<?php endif; ?>
				</div>
			</header>

			<section class="ps-essential-body-section">
				<div class="ps-essential-container <?php echo esc_attr( $ps_container_mod ); ?>">
					<?php if ( $ps_use_fallback && current_user_can( 'manage_options' ) ) : ?>
						<div class="ps-page-admin-sync-bar" role="region" aria-label="<?php esc_attr_e( 'Administrator Page Sync Notice', 'promptsyra' ); ?>">
							<span>
								<?php esc_html_e( 'Admin Preview: Showing PromptSyra v2.3.0 page content over legacy stub. Sync once in PromptSyra Setup to edit this text directly in the WordPress Page Editor.', 'promptsyra' ); ?>
							</span>
							<a href="<?php echo esc_url( admin_url( 'themes.php?page=promptsyra-setup#ps-sync-panel' ) ); ?>">
								<?php esc_html_e( 'Open PromptSyra Setup →', 'promptsyra' ); ?>
							</a>
						</div>
					<?php endif; ?>

					<article id="post-<?php the_ID(); ?>" <?php post_class( 'ps-essential-prose' ); ?>>
						<?php
						if ( $ps_use_fallback ) {
							// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Curated theme HTML processed via standard the_content filters & shortcodes.
							echo apply_filters( 'the_content', $ps_matched_spec['content'] );
						} else {
							the_content();

							wp_link_pages(
								array(
									'before' => '<div class="ps-page-links">' . esc_html__( 'Pages:', 'promptsyra' ),
									'after'  => '</div>',
								)
							);
						}
						?>
					</article>

					<?php if ( null === $ps_matched_spec && ( comments_open() || get_comments_number() ) ) : ?>
						<div class="ps-single-comments-wrap">
							<?php comments_template(); ?>
						</div>
					<?php endif; ?>
				</div>
			</section>
		<?php endwhile; ?>
	</main>

	<?php get_template_part( 'template-parts/footer', 'promptsyra' ); ?>
</div>

<?php wp_footer(); ?>
</body>
</html>
