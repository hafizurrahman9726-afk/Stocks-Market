<?php
/**
 * PromptSyra Scoped Footer Partial (Updated for Community & Official Social Channels)
 *
 * Implements:
 * - PromptSyra.com branding & concise accurate description
 * - Official Social Channels: Instagram, Facebook, WhatsApp Channel (exact verified URLs)
 * - Dedicated Social Pages: /instagram/, /facebook/, /whatsapp-channel/
 * - Approved three primary categories (ai-photo-prompts, trending-prompts, social-media-prompts)
 * - About, Contact, Privacy Policy, Terms and Conditions, Disclaimer
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

// Official verified social channels
$ps_social_channels = function_exists( 'promptsyra_get_official_social_links' )
	? promptsyra_get_official_social_links()
	: array(
		'instagram' => array(
			'name'         => 'Instagram',
			'url'          => 'https://www.instagram.com/promptsyra',
			'internal_url' => home_url( '/instagram/' ),
		),
		'facebook'  => array(
			'name'         => 'Facebook',
			'url'          => 'https://www.facebook.com/people/PromptSyra/61595584906422/',
			'internal_url' => home_url( '/facebook/' ),
		),
		'whatsapp'  => array(
			'name'         => 'WhatsApp Channel',
			'url'          => 'https://whatsapp.com/channel/0029VbDkVtP7T8bZzLpWMq2d',
			'internal_url' => home_url( '/whatsapp-channel/' ),
		),
	);
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

			<!-- Official Social Channels with Genuine SVGs -->
			<div class="ps-footer-socials" aria-label="<?php esc_attr_e( 'Official PromptSyra Social Channels', 'promptsyra' ); ?>">
				<a href="https://www.instagram.com/promptsyra" class="ps-footer-social-link ps-footer-social-link--instagram" target="_blank" rel="noopener noreferrer" title="<?php esc_attr_e( 'Follow PromptSyra on Instagram', 'promptsyra' ); ?>">
					<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect width="20" height="20" x="2" y="2" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" x2="17.51" y1="6.5" y2="6.5"/></svg>
					<span>Instagram</span>
				</a>
				<a href="https://www.facebook.com/people/PromptSyra/61595584906422/" class="ps-footer-social-link ps-footer-social-link--facebook" target="_blank" rel="noopener noreferrer" title="<?php esc_attr_e( 'Follow PromptSyra on Facebook', 'promptsyra' ); ?>">
					<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
					<span>Facebook</span>
				</a>
				<a href="https://whatsapp.com/channel/0029VbDkVtP7T8bZzLpWMq2d" class="ps-footer-social-link ps-footer-social-link--whatsapp" target="_blank" rel="noopener noreferrer" title="<?php esc_attr_e( 'Join PromptSyra WhatsApp Channel', 'promptsyra' ); ?>">
					<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 21l1.65-3.8a9 9 0 1 1 3.4 2.9L3 21"/><path d="M9 10a.5.5 0 0 0 1 0V9a.5.5 0 0 0-1 0v1a5 5 0 0 0 5 5h1a.5.5 0 0 0 0-1h-1a.5.5 0 0 0 0 1"/></svg>
					<span>WhatsApp</span>
				</a>
			</div>
		</div>

		<div class="ps-footer-col">
			<h2 class="ps-footer-heading"><?php esc_html_e( 'Community & Pages', 'promptsyra' ); ?></h2>
			<ul class="ps-footer-links">
				<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'promptsyra' ); ?></a></li>
				<li><a href="<?php echo esc_url( home_url( '/instagram/' ) ); ?>"><?php esc_html_e( 'Instagram Community', 'promptsyra' ); ?></a></li>
				<li><a href="<?php echo esc_url( home_url( '/facebook/' ) ); ?>"><?php esc_html_e( 'Facebook Page', 'promptsyra' ); ?></a></li>
				<li><a href="<?php echo esc_url( home_url( '/whatsapp-channel/' ) ); ?>"><?php esc_html_e( 'WhatsApp Channel', 'promptsyra' ); ?></a></li>
				<?php if ( ! empty( $ps_about_url ) ) : ?>
					<li><a href="<?php echo esc_url( $ps_about_url ); ?>"><?php esc_html_e( 'About Us', 'promptsyra' ); ?></a></li>
				<?php endif; ?>
				<?php if ( ! empty( $ps_contact_url ) ) : ?>
					<li><a href="<?php echo esc_url( $ps_contact_url ); ?>"><?php esc_html_e( 'Contact Us', 'promptsyra' ); ?></a></li>
				<?php endif; ?>
			</ul>
		</div>

		<div class="ps-footer-col">
			<h2 class="ps-footer-heading"><?php esc_html_e( 'Main Collections', 'promptsyra' ); ?></h2>
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
			</ul>
		</div>
	</div>

	<div class="ps-footer-bottom">
		<div class="ps-container ps-footer-bottom-inner">
			<p class="ps-copyright">
				&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> PromptSyra.com. <?php esc_html_e( 'All rights reserved.', 'promptsyra' ); ?>
			</p>
			<div class="ps-footer-bottom-links">
				<a href="<?php echo esc_url( home_url( '/instagram/' ) ); ?>">Instagram</a>
				<a href="<?php echo esc_url( home_url( '/facebook/' ) ); ?>">Facebook</a>
				<a href="<?php echo esc_url( home_url( '/whatsapp-channel/' ) ); ?>">WhatsApp</a>
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
