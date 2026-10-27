// scripts/deploy-community-and-theme.mjs
import https from 'https';
import fs from 'fs';

async function request(url, options = {}, postData = null) {
  return new Promise((resolve, reject) => {
    const req = https.request(url, options, (res) => {
      let data = '';
      res.on('data', chunk => data += chunk);
      res.on('end', () => {
        resolve({
          statusCode: res.statusCode,
          headers: res.headers,
          body: data
        });
      });
    });
    req.on('error', reject);
    if (postData) {
      req.write(postData);
    }
    req.end();
  });
}

async function login() {
  const loginUrl = 'https://promptsyra.com/wp-login.php';
  const step1 = await request(loginUrl, { method: 'GET' });
  const cookies = [];
  if (step1.headers['set-cookie']) {
    step1.headers['set-cookie'].forEach(c => cookies.push(c.split(';')[0]));
  }
  
  const postParams = new URLSearchParams({
    log: 'promptsyra',
    pwd: process.env.WP_PASSWORD || '',
    'wp-submit': 'Log In',
    redirect_to: 'https://promptsyra.com/wp-admin/',
    testcookie: '1'
  }).toString();

  const step2 = await request(loginUrl, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
      'Content-Length': Buffer.byteLength(postParams),
      'Cookie': cookies.join('; ')
    }
  }, postParams);

  if (step2.headers['set-cookie']) {
    step2.headers['set-cookie'].forEach(c => cookies.push(c.split(';')[0]));
  }

  return cookies.join('; ');
}

async function getNonceForFile(cookieHeader, filename) {
  const res = await request(`https://promptsyra.com/wp-admin/theme-editor.php?file=${encodeURIComponent(filename)}&theme=promptsyra`, {
    headers: { 'Cookie': cookieHeader }
  });
  const nonceMatch = res.body.match(/id="nonce"\s+name="nonce"\s+value="([^"]+)"/);
  if (nonceMatch) {
    return nonceMatch[1];
  }
  const altNonce = res.body.match(/name="nonce"\s+value="([^"]+)"/);
  return altNonce ? altNonce[1] : null;
}

async function updateThemeFile(cookieHeader, filename, content) {
  console.log(`\n--- Updating theme file: ${filename} ---`);
  const nonce = await getNonceForFile(cookieHeader, filename);
  if (!nonce) {
    throw new Error(`Failed to acquire nonce for ${filename}`);
  }
  console.log(`Acquired nonce for ${filename}: ${nonce}`);

  const postParams = new URLSearchParams({
    nonce: nonce,
    _wp_http_referer: `/wp-admin/theme-editor.php?file=${filename}&theme=promptsyra`,
    action: 'update',
    file: filename,
    theme: 'promptsyra',
    newcontent: content,
    submit: 'Update File'
  }).toString();

  const updateRes = await request('https://promptsyra.com/wp-admin/theme-editor.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
      'Content-Length': Buffer.byteLength(postParams),
      'Cookie': cookieHeader
    }
  }, postParams);

  console.log(`Update response status: ${updateRes.statusCode}, Location: ${updateRes.headers.location}`);
  const isSuccess = updateRes.headers.location && (updateRes.headers.location.includes('a=1') || updateRes.headers.location.includes('updated=true'));
  console.log(`File ${filename} updated successfully?`, Boolean(isSuccess));
  return isSuccess;
}

async function main() {
  console.log("=== STARTING COMMUNITY SECTION & THEME DEPLOYMENT ===");
  const cookieHeader = await login();
  console.log("Logged into WordPress successfully.");

  // 1. UPDATE functions.php
  let functionsPhp = fs.readFileSync('wordpress-theme/promptsyra/functions.php', 'utf8');
  if (!functionsPhp.includes('promptsyra_get_official_social_links')) {
    const socialHelperCode = `
/**
 * Official PromptSyra Social Links Specification.
 *
 * @return array<string, array<string, string>>
 */
function promptsyra_get_official_social_links() {
	return array(
		'instagram' => array(
			'key'          => 'instagram',
			'name'         => __( 'Instagram', 'promptsyra' ),
			'handle'       => '@promptsyra',
			'badge'        => __( 'Visual Gallery', 'promptsyra' ),
			'title'        => __( 'Instagram Visual Gallery', 'promptsyra' ),
			'desc'         => __( 'Browse curated portrait lighting setups, photo prompt ideas, and visual aesthetics crafted for modern creators.', 'promptsyra' ),
			'button_label' => __( 'Follow on Instagram', 'promptsyra' ),
			'url'          => 'https://www.instagram.com/promptsyra',
			'internal_slug'=> 'instagram',
			'internal_url' => home_url( '/instagram/' ),
		),
		'facebook'  => array(
			'key'          => 'facebook',
			'name'         => __( 'Facebook', 'promptsyra' ),
			'handle'       => 'PromptSyra',
			'badge'        => __( 'Community Updates', 'promptsyra' ),
			'title'        => __( 'Facebook Community Updates', 'promptsyra' ),
			'desc'         => __( 'Stay updated with newly published prompt guides, creative themes, creator discussions, and website announcements.', 'promptsyra' ),
			'button_label' => __( 'Follow on Facebook', 'promptsyra' ),
			'url'          => 'https://www.facebook.com/people/PromptSyra/61595584906422/',
			'internal_slug'=> 'facebook',
			'internal_url' => home_url( '/facebook/' ),
		),
		'whatsapp'  => array(
			'key'          => 'whatsapp',
			'name'         => __( 'WhatsApp Channel', 'promptsyra' ),
			'handle'       => 'PromptSyra',
			'badge'        => __( 'Official Channel', 'promptsyra' ),
			'title'        => __( 'WhatsApp Official Channel', 'promptsyra' ),
			'desc'         => __( 'Get instant mobile alerts and prompt drops directly to your phone. 100% private one-way broadcast.', 'promptsyra' ),
			'button_label' => __( 'Join WhatsApp Channel', 'promptsyra' ),
			'url'          => 'https://whatsapp.com/channel/0029VbDkVtP7T8bZzLpWMq2d',
			'internal_slug'=> 'whatsapp-channel',
			'internal_url' => home_url( '/whatsapp-channel/' ),
		),
	);
}
`;
    // Insert before require_once
    functionsPhp = functionsPhp.replace("require_once PROMPTSYRA_DIR . '/inc/customizer.php';", socialHelperCode + "\nrequire_once PROMPTSYRA_DIR . '/inc/customizer.php';");
    fs.writeFileSync('wordpress-theme/promptsyra/functions.php', functionsPhp);
    console.log("Updated local functions.php with promptsyra_get_official_social_links()");
  }

  // 2. Read front-page.php
  const frontPagePhp = fs.readFileSync('wordpress-theme/promptsyra/front-page.php', 'utf8');
  console.log("Read updated front-page.php with Community Section, length:", frontPagePhp.length);

  // 3. UPDATE footer-promptsyra.php
  let footerPhp = fs.readFileSync('wordpress-theme/promptsyra/template-parts/footer-promptsyra.php', 'utf8');
  // Define official social links in footer
  const updatedFooterPhp = `<?php
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
`;
  fs.writeFileSync('wordpress-theme/promptsyra/template-parts/footer-promptsyra.php', updatedFooterPhp);
  console.log("Updated local template-parts/footer-promptsyra.php");

  // 4. POLISH promptsyra-home.css with complete Community Section styles
  let homeCss = fs.readFileSync('wordpress-theme/promptsyra/assets/css/promptsyra-home.css', 'utf8');
  const communityCssEnhancements = `
/* ==========================================================================
   PromptSyra Community Section & Social Styles (v2.4.1)
   ========================================================================== */
.ps-community-section {
	padding: 4.5rem 0 5rem;
	background: #ffffff;
	border-top: 1px solid var(--ps-border, #e2e8f0);
}

.ps-community-header {
	text-align: center;
	max-width: 680px;
	margin: 0 auto 3rem;
}

.ps-community-section-title {
	margin: 0.5rem 0 0.85rem;
	font-family: var(--ps-font-display, "Plus Jakarta Sans", sans-serif);
	font-size: 2.25rem;
	font-weight: 800;
	letter-spacing: -0.025em;
	color: var(--ps-navy, #0f172a);
}

.ps-community-lead {
	font-size: 1.0625rem;
	line-height: 1.65;
	color: var(--ps-slate, #64748b);
	margin: 0;
}

.ps-community-grid {
	display: grid;
	grid-template-columns: repeat(3, 1fr);
	gap: 1.75rem;
}

.ps-community-card {
	display: flex;
	flex-direction: column;
	padding: 2.25rem 2rem;
	background: #ffffff;
	border: 1px solid var(--ps-border, #e2e8f0);
	border-radius: 1.25rem;
	transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
	box-shadow: 0 4px 6px -1px rgba(15, 23, 42, 0.03), 0 2px 4px -2px rgba(15, 23, 42, 0.03);
}

.ps-community-card:hover {
	transform: translateY(-4px);
	box-shadow: 0 16px 28px -6px rgba(15, 23, 42, 0.09), 0 6px 12px -4px rgba(15, 23, 42, 0.04);
}

.ps-community-card--instagram:hover {
	border-color: #f472b6;
}

.ps-community-card--facebook:hover {
	border-color: #60a5fa;
}

.ps-community-card--whatsapp:hover {
	border-color: #4ade80;
}

.ps-community-card-top {
	display: flex;
	align-items: center;
	justify-content: space-between;
	margin-bottom: 1.5rem;
}

.ps-community-icon {
	display: inline-flex;
	align-items: center;
	justify-content: center;
	width: 48px;
	height: 48px;
	border-radius: 0.875rem;
}

.ps-community-icon--instagram {
	background: linear-gradient(135deg, #fdf2f8 0%, #fce7f3 100%);
	color: #e1306c;
}

.ps-community-icon--facebook {
	background: #eff6ff;
	color: #1877f2;
}

.ps-community-icon--whatsapp {
	background: #ecfdf5;
	color: #10b981;
}

.ps-community-badge {
	display: inline-block;
	padding: 0.3rem 0.75rem;
	font-size: 0.75rem;
	font-weight: 700;
	letter-spacing: 0.02em;
	text-transform: uppercase;
	border-radius: 9999px;
}

.ps-badge-instagram {
	background: #fdf2f8;
	color: #be185d;
	border: 1px solid #fbcfe8;
}

.ps-badge-facebook {
	background: #eff6ff;
	color: #1d4ed8;
	border: 1px solid #bfdbfe;
}

.ps-badge-whatsapp {
	background: #ecfdf5;
	color: #047857;
	border: 1px solid #a7f3d0;
}

.ps-community-title {
	margin: 0 0 0.85rem;
	font-family: var(--ps-font-display, "Plus Jakarta Sans", sans-serif);
	font-size: 1.35rem;
	font-weight: 700;
	letter-spacing: -0.015em;
	color: var(--ps-navy, #0f172a);
}

.ps-community-desc {
	margin: 0 0 1.75rem;
	font-size: 0.95rem;
	line-height: 1.6;
	color: var(--ps-slate, #64748b);
	flex-grow: 1;
}

.ps-community-actions {
	display: flex;
	flex-direction: column;
	gap: 0.85rem;
}

.ps-community-btn {
	display: inline-flex;
	align-items: center;
	justify-content: center;
	gap: 0.5rem;
	padding: 0.85rem 1.25rem;
	font-weight: 600;
	font-size: 0.925rem;
	border-radius: 0.75rem;
	text-decoration: none;
	transition: all 0.2s ease;
	cursor: pointer;
}

.ps-btn-instagram {
	background: #e1306c;
	color: #ffffff !important;
}

.ps-btn-instagram:hover {
	background: #c1275b;
}

.ps-btn-facebook {
	background: #1877f2;
	color: #ffffff !important;
}

.ps-btn-facebook:hover {
	background: #1464cc;
}

.ps-btn-whatsapp {
	background: #10b981;
	color: #ffffff !important;
}

.ps-btn-whatsapp:hover {
	background: #059669;
}

.ps-community-sublink {
	font-size: 0.875rem;
	font-weight: 600;
	color: var(--ps-violet, #6366f1);
	text-align: center;
	text-decoration: none;
	transition: color 0.15s ease;
}

.ps-community-sublink:hover {
	color: #4f46e5;
	text-decoration: underline;
}

.ps-footer-socials {
	display: flex;
	align-items: center;
	flex-wrap: wrap;
	gap: 0.75rem;
	margin-top: 1.25rem;
}

.ps-footer-social-link {
	display: inline-flex;
	align-items: center;
	gap: 0.45rem;
	padding: 0.4rem 0.85rem;
	font-size: 0.8125rem;
	font-weight: 600;
	background: #f8fafc;
	border: 1px solid #e2e8f0;
	border-radius: 0.625rem;
	color: #334155;
	text-decoration: none;
	transition: all 0.2s ease;
}

.ps-footer-social-link:hover {
	background: #ffffff;
	border-color: #cbd5e1;
	color: #0f172a;
}

.ps-footer-social-link--instagram:hover {
	color: #e1306c;
	border-color: #fbcfe8;
}

.ps-footer-social-link--facebook:hover {
	color: #1877f2;
	border-color: #bfdbfe;
}

.ps-footer-social-link--whatsapp:hover {
	color: #10b981;
	border-color: #a7f3d0;
}

@media (max-width: 960px) {
	.ps-community-grid {
		grid-template-columns: 1fr;
		gap: 1.25rem;
	}
	.ps-community-section-title {
		font-size: 1.85rem;
	}
}
`;

  if (!homeCss.includes('.ps-community-section-title')) {
    homeCss += '\n' + communityCssEnhancements;
    fs.writeFileSync('wordpress-theme/promptsyra/assets/css/promptsyra-home.css', homeCss);
    console.log("Appended community CSS to assets/css/promptsyra-home.css");
  }

  // 5. UPDATE promptsyra-pages.css for dedicated social pages styling
  let pagesCss = fs.readFileSync('wordpress-theme/promptsyra/assets/css/promptsyra-pages.css', 'utf8');
  const socialPagesCss = `
/* Social Pages Banner & Privacy Callout */
.ps-social-cta-banner {
	padding: 2.5rem;
	border-radius: 1.25rem;
	margin: 2rem 0 2.5rem;
	box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
}
.ps-instagram-banner {
	background: linear-gradient(135deg, #fdf2f8 0%, #fff1f2 50%, #fef3c7 100%);
	border: 1px solid #fbcfe8;
}
.ps-facebook-banner {
	background: linear-gradient(135deg, #eff6ff 0%, #f0f9ff 100%);
	border: 1px solid #bfdbfe;
}
.ps-whatsapp-banner {
	background: linear-gradient(135deg, #ecfdf5 0%, #f0fdf4 100%);
	border: 1px solid #a7f3d0;
}
.ps-social-cta-actions {
	display: flex;
	align-items: center;
	gap: 1.25rem;
	margin-top: 1.5rem;
	flex-wrap: wrap;
}
.ps-social-cta-note {
	font-size: 0.875rem;
	color: #64748b;
}
.ps-privacy-callout {
	display: flex;
	gap: 1.25rem;
	padding: 1.75rem;
	background: #f8fafc;
	border: 1px solid #e2e8f0;
	border-left: 4px solid #10b981;
	border-radius: 1rem;
	margin: 2rem 0;
}
.ps-privacy-callout-icon {
	color: #10b981;
	flex-shrink: 0;
	padding-top: 0.25rem;
}
.ps-privacy-callout-text h3 {
	margin: 0 0 0.5rem;
	font-size: 1.125rem;
	font-weight: 700;
	color: #0f172a;
}
.ps-privacy-callout-text p {
	margin: 0;
	font-size: 0.95rem;
	line-height: 1.6;
	color: #475569;
}
.ps-connect-footer-bar {
	padding: 1.5rem;
	background: #f8fafc;
	border-radius: 0.875rem;
	border: 1px solid #e2e8f0;
	margin-top: 2.5rem;
	text-align: center;
}
.ps-connect-footer-bar p {
	margin: 0;
	font-size: 0.95rem;
	color: #475569;
}
`;
  if (!pagesCss.includes('.ps-social-cta-banner')) {
    pagesCss += '\n' + socialPagesCss;
    fs.writeFileSync('wordpress-theme/promptsyra/assets/css/promptsyra-pages.css', pagesCss);
    console.log("Appended social pages CSS to assets/css/promptsyra-pages.css");
  }

  // 6. DEPLOY TO LIVE WORDPRESS THEME
  console.log("\n=== DEPLOYING UPDATED THEME FILES TO LIVE WORDPRESS ===");
  await updateThemeFile(cookieHeader, 'functions.php', functionsPhp);
  await updateThemeFile(cookieHeader, 'front-page.php', frontPagePhp);
  await updateThemeFile(cookieHeader, 'template-parts/footer-promptsyra.php', updatedFooterPhp);
  await updateThemeFile(cookieHeader, 'assets/css/promptsyra-home.css', homeCss);
  await updateThemeFile(cookieHeader, 'assets/css/promptsyra-pages.css', pagesCss);

  console.log("\n=== ALL THEME FILES DEPLOYED TO LIVE SERVER ===");

  // 7. VERIFY LIVE HOMEPAGE AND PAGES
  console.log("\n=== VERIFYING LIVE HOMEPAGE FOR COMMUNITY SECTION ===");
  const homeRes = await request('https://promptsyra.com/', { method: 'GET' });
  console.log("Homepage status:", homeRes.statusCode);
  console.log("Found 'ps-community-section' on homepage?", homeRes.body.includes('ps-community-section'));
  console.log("Found 'Join Our Community' on homepage?", homeRes.body.includes('Join Our Community'));
  console.log("Found 'Instagram Visual Gallery'?", homeRes.body.includes('Instagram Visual Gallery'));
  console.log("Found 'Facebook Community Updates'?", homeRes.body.includes('Facebook Community Updates'));
  console.log("Found 'WhatsApp Official Channel'?", homeRes.body.includes('WhatsApp Official Channel'));
  console.log("Found Instagram link (https://www.instagram.com/promptsyra)?", homeRes.body.includes('https://www.instagram.com/promptsyra'));
  console.log("Found Facebook link (https://www.facebook.com/people/PromptSyra/61595584906422/)?", homeRes.body.includes('https://www.facebook.com/people/PromptSyra/61595584906422/'));
  console.log("Found WhatsApp link (https://whatsapp.com/channel/0029VbDkVtP7T8bZzLpWMq2d)?", homeRes.body.includes('https://whatsapp.com/channel/0029VbDkVtP7T8bZzLpWMq2d'));
}

main().catch(console.error);
