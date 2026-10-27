<?php
/**
 * PromptSyra Scoped Footer Partial
 *
 * Implements:
 * - PromptSyra.com branding & concise accurate description
 * - Approved three primary categories (ai-photo-prompts, trending-prompts, social-media-prompts)
 * - About, Contact, Privacy Policy, Terms and Conditions, Disclaimer (verified existing URLs only)
 * - Configurable official social links (only shown when genuine URLs are entered in Customizer)
 * - Dynamic copyright
 *
 * @package PromptSyra
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$ps_footer_desc = get_theme_mod(
	'promptsyra_footer_description',
	__( 'PromptSyra.com curates copy-ready AI photo prompts, portrait lighting setups, and practical customization notes for visual creators.', 'promptsyra' )
);

$ps_categories = array(
	promptsyra_resolve_category_data( 'ai-photo-prompts' ),
	promptsyra_resolve_category_data( 'trending-prompts' ),
	promptsyra_resolve_category_data( 'social-media-prompts' ),
);

$ps_about_url      = promptsyra_get_verified_page_url( array( 'about-us', 'about' ) );
$ps_contact_url    = promptsyra_get_verified_page_url( array( 'contact-us', 'contact' ) );
$ps_privacy_url    = get_privacy_policy_url();
if ( empty( $ps_privacy_url ) ) {
	$ps_privacy_url = promptsyra_get_verified_page_url( array( 'privacy-policy', 'privacy' ) );
}
$ps_terms_url      = promptsyra_get_verified_page_url( array( 'terms-and-conditions', 'terms-of-service', 'terms' ) );
$ps_disclaimer_url = promptsyra_get_verified_page_url( array( 'disclaimer' ) );
$ps_contact_email  = function_exists( 'promptsyra_get_official_contact_email' ) ? promptsyra_get_official_contact_email() : 'contact@promptsyra.com';

$ps_social_links = array();
$ps_social_labels = array(
	'instagram' => 'Instagram',
	'facebook'  => 'Facebook',
	'youtube'   => 'YouTube',
	'telegram'  => 'Telegram',
	'whatsapp'  => 'WhatsApp',
	'x'         => 'X',
);
foreach ( $ps_social_labels as $s_key => $s_name ) {
	$s_url = trim( (string) get_theme_mod( 'promptsyra_social_' . $s_key, '' ) );
	if ( ! empty( $s_url ) ) {
		$ps_social_links[ $s_key ] = array(
			'label' => $s_name,
			'url'   => $s_url,
		);
	}
}
?>
<footer class="ps-site-footer">
	<div class="ps-container ps-footer-grid">
		<div class="ps-footer-brand-col">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="ps-footer-brand-link">
				<span class="ps-brand-mark" aria-hidden="true">P</span>
				<span class="ps-footer-brand-text">Prompt<span class="ps-footer-brand-accent">Syra</span>.com</span>
			</a>
			<p class="ps-footer-desc"><?php echo esc_html( $ps_footer_desc ); ?></p>
			<p class="ps-footer-desc" style="margin-top:0.625rem;">
				<a href="mailto:<?php echo esc_attr( $ps_contact_email ); ?>" class="ps-footer-social-link"><?php echo esc_html( $ps_contact_email ); ?></a>
			</p>

			<?php if ( ! empty( $ps_social_links ) ) : ?>
				<div class="ps-footer-socials" aria-label="<?php esc_attr_e( 'Official Social Channels', 'promptsyra' ); ?>">
					<?php foreach ( $ps_social_links as $s_item ) : ?>
						<a href="<?php echo esc_url( $s_item['url'] ); ?>" class="ps-footer-social-link" target="_blank" rel="noopener noreferrer">
							<?php echo esc_html( $s_item['label'] ); ?>
						</a>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>

		<div class="ps-footer-col">
			<h2 class="ps-footer-heading"><?php esc_html_e( 'Navigation', 'promptsyra' ); ?></h2>
			<?php if ( has_nav_menu( 'promptsyra_footer_quick' ) ) : ?>
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'promptsyra_footer_quick',
						'container'      => false,
						'menu_class'     => 'ps-footer-links',
						'depth'          => 1,
					)
				);
				?>
			<?php else : ?>
				<ul class="ps-footer-links">
					<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'promptsyra' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/#featured-prompts' ) ); ?>"><?php esc_html_e( 'Featured AI Prompts', 'promptsyra' ); ?></a></li>
					<?php if ( ! empty( $ps_about_url ) ) : ?>
						<li><a href="<?php echo esc_url( $ps_about_url ); ?>"><?php esc_html_e( 'About Us', 'promptsyra' ); ?></a></li>
					<?php endif; ?>
					<?php if ( ! empty( $ps_contact_url ) ) : ?>
						<li><a href="<?php echo esc_url( $ps_contact_url ); ?>"><?php esc_html_e( 'Contact Us', 'promptsyra' ); ?></a></li>
					<?php endif; ?>
				</ul>
			<?php endif; ?>
		</div>

		<div class="ps-footer-col">
			<h2 class="ps-footer-heading"><?php esc_html_e( 'Main Categories', 'promptsyra' ); ?></h2>
			<ul class="ps-footer-links">
				<?php foreach ( $ps_categories as $ps_cat ) : ?>
					<li>
						<?php if ( $ps_cat['exists'] && ! empty( $ps_cat['url'] ) ) : ?>
							<a href="<?php echo esc_url( $ps_cat['url'] ); ?>"><?php echo esc_html( $ps_cat['name'] ); ?></a>
						<?php else : ?>
							<span class="ps-footer-unlinked"><?php echo esc_html( $ps_cat['name'] ); ?></span>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>

		<div class="ps-footer-col">
			<h2 class="ps-footer-heading"><?php esc_html_e( 'Legal & Policies', 'promptsyra' ); ?></h2>
			<?php if ( has_nav_menu( 'promptsyra_footer_legal' ) ) : ?>
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'promptsyra_footer_legal',
						'container'      => false,
						'menu_class'     => 'ps-footer-links',
						'depth'          => 1,
					)
				);
				?>
			<?php else : ?>
				<ul class="ps-footer-links">
					<?php if ( ! empty( $ps_privacy_url ) ) : ?>
						<li><a href="<?php echo esc_url( $ps_privacy_url ); ?>"><?php esc_html_e( 'Privacy Policy', 'promptsyra' ); ?></a></li>
					<?php endif; ?>
					<?php if ( ! empty( $ps_disclaimer_url ) ) : ?>
						<li><a href="<?php echo esc_url( $ps_disclaimer_url ); ?>"><?php esc_html_e( 'Disclaimer', 'promptsyra' ); ?></a></li>
					<?php endif; ?>
					<?php if ( ! empty( $ps_terms_url ) ) : ?>
						<li><a href="<?php echo esc_url( $ps_terms_url ); ?>"><?php esc_html_e( 'Terms & Conditions', 'promptsyra' ); ?></a></li>
					<?php endif; ?>
					<?php if ( empty( $ps_privacy_url ) && empty( $ps_terms_url ) && empty( $ps_disclaimer_url ) && current_user_can( 'edit_pages' ) ) : ?>
						<li>
							<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=page' ) ); ?>">
								<?php esc_html_e( 'Configure Legal Pages in WP Admin', 'promptsyra' ); ?>
							</a>
						</li>
					<?php endif; ?>
				</ul>
			<?php endif; ?>
		</div>
	</div>

	<div class="ps-footer-bottom">
		<div class="ps-container ps-footer-bottom-inner">
			<p class="ps-copyright">
				&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> PromptSyra.com. <?php esc_html_e( 'All rights reserved.', 'promptsyra' ); ?>
			</p>
			<div class="ps-footer-bottom-links">
				<?php if ( ! empty( $ps_about_url ) ) : ?>
					<a href="<?php echo esc_url( $ps_about_url ); ?>"><?php esc_html_e( 'About Us', 'promptsyra' ); ?></a>
				<?php endif; ?>
				<?php if ( ! empty( $ps_contact_url ) ) : ?>
					<a href="<?php echo esc_url( $ps_contact_url ); ?>"><?php esc_html_e( 'Contact Us', 'promptsyra' ); ?></a>
				<?php endif; ?>
				<?php if ( ! empty( $ps_privacy_url ) ) : ?>
					<a href="<?php echo esc_url( $ps_privacy_url ); ?>"><?php esc_html_e( 'Privacy Policy', 'promptsyra' ); ?></a>
				<?php endif; ?>
				<?php if ( ! empty( $ps_disclaimer_url ) ) : ?>
					<a href="<?php echo esc_url( $ps_disclaimer_url ); ?>"><?php esc_html_e( 'Disclaimer', 'promptsyra' ); ?></a>
				<?php endif; ?>
				<?php if ( ! empty( $ps_terms_url ) ) : ?>
					<a href="<?php echo esc_url( $ps_terms_url ); ?>"><?php esc_html_e( 'Terms & Conditions', 'promptsyra' ); ?></a>
				<?php endif; ?>
			</div>
		</div>
	</div>
</footer>
