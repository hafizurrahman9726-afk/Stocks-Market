<?php
/**
 * PromptSyra Essential Pages, Dynamic Authors & Non-Destructive Provisioner (v2.3.0)
 *
 * Implements:
 * 1. Dynamic [promptsyra_meet_writers] shortcode rendering Syra Ansari, Arjun Patel,
 *    and Neha Joshi from real WordPress User profiles and verified Media Library WebP portraits.
 * 2. Original, accurate, production-ready page content definitions for:
 *    - About PromptSyra (/about-us/)
 *    - Contact PromptSyra (/contact-us/)
 *    - Privacy Policy (/privacy-policy/)
 *    - Disclaimer (/disclaimer/)
 *    - Terms & Conditions (/terms-and-conditions/)
 * 3. Non-destructive WP-Admin sync action (backed up to _promptsyra_backup_v220 post meta)
 *    that creates/updates the 3 WordPress author accounts (without reassigning existing posts)
 *    and updates legacy AIPromptzyra / WP boilerplate page stubs in-place without duplicates.
 * 4. Automatic 301 redirect bridging between /about/ <-> /about-us/ and /contact/ <-> /contact-us/
 *    so neither legacy bookmarks nor new canonical links ever return 404.
 *
 * @package PromptSyra
 * @version 2.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render the [promptsyra_meet_writers] shortcode for the About Us page.
 *
 * Pulls live WordPress user profile data (display_name, role, description,
 * portrait URL, and author archive permalink) for Syra Ansari, Arjun Patel,
 * and Neha Joshi without hardcoding uneditable biographies.
 *
 * @return string
 */
function promptsyra_render_meet_writers_shortcode() {
	$author_keys = array( 'syra', 'arjun', 'neha' );

	ob_start();
	?>
	<div class="ps-writers-grid" role="list">
		<?php foreach ( $author_keys as $key ) : ?>
			<?php
			$author     = promptsyra_resolve_designated_author( $key );
			$name       = ! empty( $author['display_name'] ) ? $author['display_name'] : '';
			$role       = ! empty( $author['role_label'] ) ? $author['role_label'] : __( 'Editorial Contributor', 'promptsyra' );
			$bio        = ! empty( $author['bio'] ) ? $author['bio'] : ( ! empty( $author['default_bio'] ) ? $author['default_bio'] : '' );
			$avatar_url = ! empty( $author['avatar_url'] ) ? $author['avatar_url'] : '';
			$archive    = ! empty( $author['archive_url'] ) ? $author['archive_url'] : '';
			$alt_text   = sprintf(
				/* translators: 1: Author display name, 2: Editorial role */
				__( 'Portrait of %1$s, %2$s at PromptSyra.com', 'promptsyra' ),
				$name,
				$role
			);
			$initials   = strtoupper( substr( preg_replace( '/[^a-zA-Z]/', '', $name ), 0, 2 ) );
			?>
			<article class="ps-writer-card" role="listitem">
				<div class="ps-writer-card-top">
					<div class="ps-writer-avatar-wrap">
						<?php if ( ! empty( $avatar_url ) ) : ?>
							<img
								src="<?php echo esc_url( $avatar_url ); ?>"
								alt="<?php echo esc_attr( $alt_text ); ?>"
								class="ps-writer-avatar"
								width="96"
								height="96"
								loading="lazy"
								decoding="async"
							/>
						<?php else : ?>
							<span class="ps-writer-avatar-fallback" aria-hidden="true"><?php echo esc_html( $initials ); ?></span>
						<?php endif; ?>
					</div>

					<div class="ps-writer-identity">
						<h3 class="ps-writer-name">
							<?php if ( ! empty( $archive ) ) : ?>
								<a href="<?php echo esc_url( $archive ); ?>"><?php echo esc_html( $name ); ?></a>
							<?php else : ?>
								<?php echo esc_html( $name ); ?>
							<?php endif; ?>
						</h3>
						<p class="ps-writer-role"><?php echo esc_html( $role ); ?></p>
					</div>
				</div>

				<?php if ( ! empty( $bio ) ) : ?>
					<p class="ps-writer-bio"><?php echo esc_html( $bio ); ?></p>
				<?php endif; ?>

				<div class="ps-writer-footer">
					<?php if ( ! empty( $archive ) ) : ?>
						<a href="<?php echo esc_url( $archive ); ?>" class="ps-writer-archive-link">
							<span>
								<?php
								printf(
									/* translators: %s: Author first name or display name */
									esc_html__( 'View %s’s Author Archive', 'promptsyra' ),
									esc_html( $name )
								);
								?>
							</span>
							<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
						</a>
					<?php elseif ( current_user_can( 'manage_options' ) ) : ?>
						<a href="<?php echo esc_url( admin_url( 'themes.php?page=promptsyra-setup#ps-sync-panel' ) ); ?>" class="ps-writer-archive-link">
							<span><?php esc_html_e( 'Activate Author Archive in Setup', 'promptsyra' ); ?></span>
							<span aria-hidden="true">→</span>
						</a>
					<?php endif; ?>
				</div>
			</article>
		<?php endforeach; ?>
	</div>
	<?php
	return (string) ob_get_clean();
}
add_shortcode( 'promptsyra_meet_writers', 'promptsyra_render_meet_writers_shortcode' );

/**
 * Return specifications and clean Gutenberg-compatible HTML content for the 5 essential pages.
 *
 * @return array<string, array<string, mixed>>
 */
function promptsyra_get_essential_pages_spec() {
	$contact_url = promptsyra_get_verified_page_url( array( 'contact-us', 'contact' ) );
	if ( empty( $contact_url ) ) {
		$contact_url = home_url( '/contact-us/' );
	}

	$privacy_url = get_privacy_policy_url();
	if ( empty( $privacy_url ) ) {
		$privacy_url = promptsyra_get_verified_page_url( array( 'privacy-policy', 'privacy' ) );
	}
	if ( empty( $privacy_url ) ) {
		$privacy_url = home_url( '/privacy-policy/' );
	}

	$disclaimer_url = promptsyra_get_verified_page_url( array( 'disclaimer' ) );
	if ( empty( $disclaimer_url ) ) {
		$disclaimer_url = home_url( '/disclaimer/' );
	}

	$updated_date = 'October 2026';

	// 1. ABOUT PROMPTSYRA (/about-us/)
	$about_html = <<<HTML
<div class="ps-editorial-intro-block">
<p class="ps-page-lead">PromptSyra.com is an independent editorial library dedicated to practical AI photo prompts, trending visual concepts, social media image ideas, and straightforward customization notes for everyday creators.</p>
<p>Generating believable portraits or stylized scenes with modern AI image tools often involves trial and error when camera angles, lighting descriptions, or background cues are left vague. PromptSyra.com organizes copy-ready prompt examples alongside clear guidance on how to adapt each detail to your own creative project.</p>
</div>

<div class="ps-about-pillars">
<div class="ps-about-pillar">
<h2>What PromptSyra Publishes</h2>
<p>Our catalog focuses on structured text prompts and practical reference guides across key creative areas:</p>
<ul>
<li><strong>AI Photo Prompts:</strong> Portrait, studio lighting, couple composition, candid street style, and editorial photography prompt examples.</li>
<li><strong>Trending Prompts:</strong> Seasonal themes, festive attire concepts, poster-style compositions, and popular visual aesthetics.</li>
<li><strong>Social Media Prompts:</strong> Aspect-ratio-aware image prompt ideas suited for profile visuals, story frames, thumbnails, and creator feeds.</li>
<li><strong>Customization Guides:</strong> Plain-language notes explaining how changing wardrobe, lighting direction, color palette, lens feel, or background setting alters a prompt.</li>
</ul>
</div>

<div class="ps-about-pillar">
<h2>Who PromptSyra Is Built For</h2>
<p>PromptSyra.com is written for everyday readers as well as regular digital creators. You do not need technical prompt-engineering jargon to use the library.</p>
<ul>
<li><strong>Everyday Users &amp; Hobbyists</strong> exploring creative portrait or themed photo ideas for personal projects.</li>
<li><strong>Content Creators &amp; Social Media Publishers</strong> looking for consistent visual themes, story backgrounds, and thumbnail concepts.</li>
<li><strong>Designers, Bloggers &amp; Marketers</strong> drafting moodboards, concept illustrations, and editorial visual references.</li>
</ul>
</div>
</div>

<h2>How Readers Can Use Prompts Effectively</h2>
<p>Every prompt published on PromptSyra.com is meant to work as an adaptable starting point rather than a rigid script. When copying a prompt from an article, you will usually get better alignment with your idea by tailoring a few specific elements:</p>
<ul>
<li><strong>Subject &amp; Wardrobe Details:</strong> Adjust clothing styles, fabrics, eyewear, expressions, or cultural attire to match the subject you have in mind.</li>
<li><strong>Lighting &amp; Time of Day:</strong> Swap phrases like soft window daylight, golden-hour rim light, or overcast studio diffusion to shift the mood.</li>
<li><strong>Framing, Camera Feel &amp; Aspect Ratio:</strong> Choose close-up portrait framing, medium waist-up composition, or wide environmental context depending on where the image will appear.</li>
<li><strong>Color Palette &amp; Setting:</strong> Replace background locations and dominant color tones so the scene feels cohesive rather than overcrowded with conflicting adjectives.</li>
</ul>

<h2>Our Editorial Approach &amp; How Content Is Prepared</h2>
<p>We prioritize clarity, scannable formatting, and honest presentation across every page on PromptSyra.com:</p>
<ul>
<li><strong>Readable Prompt Structure:</strong> Prompts are separated clearly from surrounding commentary with one-click copy blocks so visitors never accidentally copy page headings or UI labels into an AI generator.</li>
<li><strong>Organized Categories:</strong> Articles are grouped into focused primary categories so readers can browse portraits, trending styles, and social media visuals without navigating cluttered tag lists.</li>
<li><strong>Practical Preparation:</strong> Prompts and companion notes are structured around clear photographic descriptors—subject, pose, environment, lighting, and composition—so readers can immediately see which words control each part of the scene and edit them cleanly.</li>
<li><strong>No Misleading Promises:</strong> We avoid exaggerated claims, invented benchmarks, or promises of guaranteed results.</li>
</ul>

<div class="ps-editorial-callout">
<h2>Understanding the Limitations of AI Image Tools</h2>
<p>AI image generators interpret text probabilistically rather than following deterministic rules. Even when using the exact same prompt text, generated images will vary depending on the AI platform you choose (such as ChatGPT, Google Gemini, Midjourney, Grok, or other tools), the active model version, safety filters, aspect-ratio settings, and whether you attach a reference photo.</p>
<p>Details such as hands, small background text, intricate jewelry, and consistent facial likeness across multiple generations frequently require wording adjustments or multiple attempts. Our guides highlight flexible phrasing rather than promising identical one-click reproductions.</p>
</div>

<section class="ps-writers-section" aria-labelledby="ps-meet-writers-heading">
<div class="ps-writers-section-head">
<p class="ps-section-kicker">Editorial Contributors</p>
<h2 id="ps-meet-writers-heading">Meet Our Writers</h2>
<p>Articles and prompt collections on PromptSyra.com are prepared and maintained by our editorial contributors. Select any author card below to browse articles attributed to their profile.</p>
</div>
[promptsyra_meet_writers]
</section>

<h2>Accuracy, Updates &amp; Corrections</h2>
<p>AI image tools evolve quickly, and prompt syntax or platform features can change as providers update their models. We review and update articles periodically to improve readability, fix broken page elements, and clarify prompt instructions. If you spot an outdated note, a formatting issue, or an error on any PromptSyra.com page, please let us know so we can review and correct it.</p>

<h2>Contact &amp; Reader Feedback</h2>
<p>We welcome prompt topic suggestions, editorial feedback, correction requests, and general questions from readers. You can reach us anytime through our <a href="{$contact_url}">Contact Us page</a> or by emailing <a href="mailto:contact@promptsyra.com"><strong>contact@promptsyra.com</strong></a>.</p>
HTML;

	// 2. CONTACT PROMPTSYRA (/contact-us/)
	$contact_html = <<<HTML
<div class="ps-contact-page-grid">
<div class="ps-contact-info-col">
<p class="ps-page-lead">Have a question about an article, a suggestion for a new prompt category, or a correction to report? Reach out to the PromptSyra.com editorial inbox using the form on this page or by direct email.</p>

<div class="ps-contact-direct-box">
<h2>Official Email Address</h2>
<p>For direct email inquiries, write to us at:</p>
<p class="ps-contact-email-line"><a href="mailto:contact@promptsyra.com">contact@promptsyra.com</a></p>
</div>

<h2>Topics You Can Contact Us About</h2>
<ul class="ps-contact-topics-list">
<li><strong>General Website Questions:</strong> Questions about navigating PromptSyra.com or understanding how our prompt guides are organized.</li>
<li><strong>Prompt &amp; Category Suggestions:</strong> Ideas for portrait themes, creative photography styles, or social media prompt collections you would like to see covered.</li>
<li><strong>Corrections &amp; Technical Issues:</strong> Reporting broken links, copy-button issues, display glitches on mobile or desktop, or outdated article details (please include the page URL).</li>
<li><strong>Copyright &amp; Content Concerns:</strong> Inquiries regarding attribution, intellectual property, or content removal requests.</li>
<li><strong>Advertising &amp; Collaboration Inquiries:</strong> Relevant partnership, sponsorship, or display advertising questions related to PromptSyra.com.</li>
</ul>

<p class="ps-contact-helpful-note">When reporting an issue on a specific post, including the exact article link helps us review the page accurately. For details on how inquiry messages are handled, please see our <a href="{$privacy_url}">Privacy Policy</a>.</p>
</div>

<div class="ps-contact-form-col">
[promptsyra_contact_form]
</div>
</div>
HTML;

	// 3. PRIVACY POLICY (/privacy-policy/)
	$privacy_html = <<<HTML
<p class="ps-legal-meta"><strong>Website:</strong> PromptSyra.com (https://promptsyra.com) &nbsp;·&nbsp; <strong>Last Updated:</strong> {$updated_date}</p>

<p>This Privacy Policy explains what information PromptSyra.com collects when you visit <strong>https://promptsyra.com</strong>, submit a message through our contact page, or leave a public comment on an article, how that information is used, and how you can contact us with privacy requests.</p>

<h2>1. Information You Voluntarily Provide</h2>
<p>You can browse articles and copy AI photo prompts on PromptSyra.com without creating an account. We collect personal information directly from you only when you choose to submit it:</p>
<ul>
<li><strong>Contact Form &amp; Direct Email Inquiries:</strong> When you use our Contact Us form or email <a href="mailto:contact@promptsyra.com">contact@promptsyra.com</a>, we receive your name, email address, subject line, message content, and submission timestamp so we can read and reply to your inquiry. To protect the inbox against automated spam bursts, our contact form temporarily checks a cryptographic hash of the submitting IP address for up to 15 minutes without storing a permanent visitor IP log in the WordPress database.</li>
<li><strong>Public Article Comments:</strong> When visitors leave comments on posts where WordPress comments are open, WordPress collects the name, email address, optional website URL, and comment text entered in the comment form, along with the visitor’s IP address and browser user-agent string to assist with standard WordPress spam detection and moderation. Your email address is never displayed publicly alongside your comment.</li>
</ul>

<h2>2. Technical &amp; Server Log Data Collected Automatically</h2>
<p>Like standard WordPress websites, our hosting environment and content delivery network automatically process basic technical request data needed to deliver web pages and defend against malicious traffic:</p>
<ul>
<li><strong>Server &amp; Network Delivery Logs:</strong> IP address, browser type and version, operating system, requested page URL, referring page, date and time of access, and HTTP status codes.</li>
<li><strong>Cloudflare Security &amp; Performance Delivery:</strong> PromptSyra.com uses Cloudflare infrastructure to route traffic securely, mitigate automated attacks, and measure aggregate page-load performance (including Cloudflare’s browser performance beacon). Cloudflare processes technical request metadata in accordance with its own network privacy practices.</li>
</ul>

<h2>3. Cookies &amp; Browser Storage</h2>
<p>Cookies are small text files stored by your browser. PromptSyra.com uses limited functional cookies and does not require invasive tracking to read articles:</p>
<ul>
<li><strong>WordPress Comment Convenience Cookies:</strong> If you leave a comment on an article and explicitly check the optional save-details box, WordPress sets convenience cookies (<code>comment_author_*</code>) storing your name, email, and website so you do not have to re-type them on your next comment. These cookies last for up to one year and can be cleared in your browser at any time.</li>
<li><strong>WordPress Editorial Session Cookies:</strong> Standard WordPress authentication and preferences cookies (<code>wordpress_logged_in_*</code>, <code>wp-settings-*</code>) apply only to authorized website administrators and authors signing into the backend editor.</li>
<li><strong>Analytics &amp; Advertising Status:</strong> PromptSyra.com does not currently run third-party behavioral advertising networks or cross-site user profiling scripts. If display advertising (such as Google AdSense) or additional analytics tools are enabled on PromptSyra.com in the future, this Privacy Policy—and any required cookie disclosures or consent controls—will be updated to describe how those services operate and how visitors can manage their preferences.</li>
</ul>

<h2>4. Third-Party Fonts, Avatars &amp; Embedded Services</h2>
<p>Certain standard visual components on PromptSyra.com load resources from established third-party providers, which receive your browser’s technical request (including IP address) to deliver the requested asset:</p>
<ul>
<li><strong>Google Fonts:</strong> Pages load web font stylesheets and font files from Google servers (<code>fonts.googleapis.com</code> and <code>fonts.gstatic.com</code>) so headings and body text render consistently across devices.</li>
<li><strong>Gravatar (Automattic):</strong> On pages displaying WordPress comment avatars, an anonymized hash created from a commenter’s email address may be checked against the Gravatar service (<code>secure.gravatar.com</code>) to see if you use a public profile avatar.</li>
</ul>

<h2>5. External Links &amp; Third-Party AI Image Platforms</h2>
<p>PromptSyra.com publishes text prompts and creative guides that readers may copy and use on external AI image generation platforms (such as ChatGPT, Google Gemini, Midjourney, Grok, or other third-party services) and may link to external websites or social channels for reference.</p>
<p><strong>PromptSyra.com does not operate those external AI tools and does not receive the photos or prompts you submit to them.</strong> Whenever you paste a prompt, upload a personal reference photo, or create an account on a third-party platform, your data is governed strictly by that third party’s own privacy policy and terms of service.</p>

<h2>6. How We Use Information</h2>
<p>We use the limited information described above solely to:</p>
<ul>
<li>Deliver, maintain, and troubleshoot PromptSyra.com pages and interactive features;</li>
<li>Review and respond to reader questions, correction reports, and inquiries sent to <a href="mailto:contact@promptsyra.com">contact@promptsyra.com</a>;</li>
<li>Moderate, display, and protect public article comment sections against automated spam and abuse; and</li>
<li>Maintain website security, prevent abuse, and honor legal or intellectual property requests.</li>
</ul>
<p>We do not sell or rent visitor email addresses or contact form messages to data brokers or third-party marketers.</p>

<h2>7. Data Retention</h2>
<ul>
<li><strong>Comments:</strong> If you leave a comment, the comment and its metadata are retained indefinitely in WordPress so follow-up replies can be recognized and displayed unless you request removal or an administrator deletes the comment.</li>
<li><strong>Contact Inquiries:</strong> Emails sent via our Contact Us form or directly to <a href="mailto:contact@promptsyra.com">contact@promptsyra.com</a> are retained in our editorial email inbox only as long as reasonably needed to address your inquiry, maintain correspondence records, or resolve reported issues.</li>
<li><strong>Server Logs:</strong> Technical server and security logs are rotated automatically according to standard hosting and CDN retention schedules.</li>
</ul>

<h2>8. Data Security</h2>
<p>PromptSyra.com uses HTTPS encryption in transit, WordPress security nonces, input sanitization, and access controls to help safeguard submitted information. However, no method of transmission over the internet or electronic storage is completely immune from risk, and we cannot guarantee absolute security.</p>

<h2>9. Children’s Privacy</h2>
<p>PromptSyra.com is a general-audience creative reference publication and is not directed to children under the age of 13 (or the minimum age required by applicable local law). We do not knowingly solicit personal information from children. If you believe a child has submitted personal details through a comment or contact message on PromptSyra.com, please email <a href="mailto:contact@promptsyra.com">contact@promptsyra.com</a> so we can remove the information promptly.</p>

<h2>10. Your Privacy Choices &amp; Deletion Requests</h2>
<p>Depending on where you live, you may have rights to request access to personal information we hold about you, ask us to correct inaccurate details, or request deletion of your published comments or past email inquiries. You can also block or delete cookies at any time through your web browser settings.</p>
<p>To request removal of a public comment you posted or deletion of an email inquiry you sent to us, please contact <a href="mailto:contact@promptsyra.com">contact@promptsyra.com</a> from the email address associated with that submission and include the relevant article URL or message details.</p>

<h2>11. Updates to This Privacy Policy &amp; Contact Information</h2>
<p>We may revise this Privacy Policy periodically when website features, plugins, or services change. Any updates will be published on this page with an updated revision date at the top. For any questions about this Privacy Policy, email us at <a href="mailto:contact@promptsyra.com"><strong>contact@promptsyra.com</strong></a>.</p>
HTML;

	// 4. DISCLAIMER (/disclaimer/)
	$disclaimer_html = <<<HTML
<p class="ps-legal-meta"><strong>Website:</strong> PromptSyra.com (https://promptsyra.com) &nbsp;·&nbsp; <strong>Last Updated:</strong> {$updated_date}</p>

<p>Please read this Disclaimer carefully before using the AI photo prompts, creative examples, and editorial guides published on <strong>PromptSyra.com</strong>.</p>

<h2>1. Informational &amp; Creative Reference Purpose</h2>
<p>All content published on PromptSyra.com—including AI photo prompts, portrait composition notes, trending visual concepts, social media prompt collections, and customization tips—is provided strictly for general informational, educational, and creative inspiration purposes. Nothing on this website constitutes legal, financial, business, technical certification, or other regulated professional advice.</p>

<h2>2. AI Image Output Variability &amp; No Guaranteed Results</h2>
<p>Generative AI image tools produce different results every time a prompt is run. Output quality, subject likeness, hand and facial geometry, lighting accuracy, and rendered text vary widely depending on:</p>
<ul>
<li>Which AI image generator or platform you use (for example, ChatGPT, Google Gemini, Midjourney, Grok, or other tools);</li>
<li>Model updates, safety filters, regional availability, and account-tier capabilities;</li>
<li>Whether you attach a reference photo, and the lighting, resolution, and angle of that photo; and</li>
<li>Aspect ratio settings, random generation seeds, and edits you make to the prompt wording.</li>
</ul>
<p>Because PromptSyra.com does not control how external AI models interpret text, we make no representation or guarantee that copying a prompt from this website will recreate a sample illustration, preserve exact facial likeness, or achieve a specific creative or commercial outcome.</p>

<h2>3. Prompts Are Adaptable Examples</h2>
<p>Prompts shared on PromptSyra.com are written as customizable starting examples. Readers are encouraged to modify clothing, backgrounds, lighting descriptions, colors, and framing cues so the prompt fits their intended subject and tool. Sample preview images on PromptSyra.com illustrate the general theme or mood of an article rather than promising a fixed output across every AI generator.</p>

<h2>4. Independence from Third-Party AI Platforms &amp; Trademarks</h2>
<p>PromptSyra.com is an independent publication. References on this website to third-party products, company names, AI models, or social media platforms—such as ChatGPT, OpenAI, Google Gemini, Midjourney, Grok, Instagram, Facebook, YouTube, Telegram, WhatsApp, or X—are used solely for descriptive identification to explain how text prompts may be used. PromptSyra.com is not owned by, affiliated with, endorsed by, or sponsored by any of those third-party providers.</p>

<h2>5. Responsible Use &amp; Respect for Third-Party Rights</h2>
<p>Visitors are solely responsible for how they adapt prompts and how they use any images generated on external AI platforms. When creating or sharing AI-generated visuals, please follow the acceptable-use rules of your chosen AI tool and respect applicable laws, portrait and publicity rights, privacy expectations, and third-party copyrights or trademarks.</p>

<h2>6. External Links, Sponsorships &amp; Monetization Transparency</h2>
<p>Articles on PromptSyra.com may reference or link to external websites, documentation, or creative tools for reader convenience. We do not control and are not responsible for the availability, accuracy, policies, or content of external websites.</p>
<p>If PromptSyra.com includes display advertisements, sponsored placements, or affiliate links on any page now or in the future, sponsored or affiliate relationships will be disclosed clearly so readers can distinguish editorial prompt content from commercial links.</p>

<h2>7. Accuracy, Updates &amp; Reporting Errors</h2>
<p>While we aim to keep prompt examples well-formatted and practical, AI tools change frequently and occasional typographical errors or outdated tool notes may occur. To the extent permitted by applicable law, PromptSyra.com provides its free editorial content on an "as-is" informational basis without warranties of completeness or fitness for a particular purpose. If you notice an error, broken element, or concern on any page, please contact us at <a href="mailto:contact@promptsyra.com"><strong>contact@promptsyra.com</strong></a> so we can review it.</p>
HTML;

	// 5. TERMS & CONDITIONS (/terms-and-conditions/)
	$terms_html = <<<HTML
<p class="ps-legal-meta"><strong>Website:</strong> PromptSyra.com (https://promptsyra.com) &nbsp;·&nbsp; <strong>Last Updated:</strong> {$updated_date}</p>

<p>Welcome to <strong>PromptSyra.com</strong>. These Terms &amp; Conditions describe the practical rules for accessing our website, copying and adapting published AI photo prompts, and interacting with site features such as comments and contact forms. By browsing or using PromptSyra.com, you agree to follow these Terms &amp; Conditions alongside our <a href="{$privacy_url}">Privacy Policy</a> and <a href="{$disclaimer_url}">Disclaimer</a>.</p>

<h2>1. Permitted Use of Prompts &amp; Guides</h2>
<p>PromptSyra.com publishes AI photo prompts, creative composition ideas, and customization guides for readers to explore and adapt:</p>
<ul>
<li><strong>Copying &amp; Customizing Prompts:</strong> You are welcome to copy individual text prompts from PromptSyra.com, edit or combine their wording, and paste them into compatible third-party AI image tools for your personal, creative, social media, or project workflows (always subject to the license and acceptable-use policies of the AI tool you use).</li>
<li><strong>Sharing Links:</strong> You may share direct links to PromptSyra.com articles on social media, blogs, or messaging channels with accurate attribution.</li>
</ul>

<h2>2. Intellectual Property &amp; Restrictions on Bulk Republishing</h2>
<p>While individual prompt strings are shared for practical use inside AI image generators, the editorial compilation, article prose, customization guides, custom page layouts, visual assets, and <strong>PromptSyra.com</strong> name and branding are original works protected by applicable copyright and intellectual property laws.</p>
<p>Without prior written permission from PromptSyra.com, you agree <strong>not</strong> to:</p>
<ul>
<li>Systematically scrape, crawl, or harvest our article library to build a competing prompt directory, database, mobile app, or commercial prompt pack;</li>
<li>Copy and republish full PromptSyra.com articles, guides, or curated collections verbatim (or with minimal automated spinning) on another website; or</li>
<li>Misrepresent yourself as affiliated with PromptSyra.com or its editorial contributors.</li>
</ul>

<h2>3. User Responsibilities, Comments &amp; Acceptable Conduct</h2>
<p>When leaving a comment on an article, using our Contact Us form, or interacting with PromptSyra.com, you agree not to:</p>
<ul>
<li>Submit automated spam, unsolicited promotional links, phishing URLs, or malicious code;</li>
<li>Post unlawful, defamatory, harassing, hateful, or deceptive material, or impersonate another person;</li>
<li>Upload or post content that infringes another party’s copyright, trademark, or privacy rights; or</li>
<li>Attempt to probe, disrupt, overload, or gain unauthorized access to WordPress accounts, server infrastructure, or security controls.</li>
</ul>
<p>We reserve the right to moderate, edit for formatting, decline, or remove comments or submissions that violate these standards.</p>

<h2>4. Responsible Use of AI Prompts on External Platforms</h2>
<p>PromptSyra.com provides text-based creative prompts only; we do not generate images on our servers or control how visitors use external AI tools. You are responsible for ensuring that your prompts, any reference images you upload to third-party generators, and any resulting AI-generated visuals comply with applicable laws, third-party likeness and intellectual property rights, and the terms of service of the AI platform you use.</p>

<h2>5. Third-Party Services &amp; External Websites</h2>
<p>PromptSyra.com may reference external AI tools or include links to third-party websites. Those services operate independently under their own terms, pricing, and privacy policies. PromptSyra.com is not responsible for the availability, output behavior, account policies, or content of any third-party platform.</p>

<h2>6. Website Availability, Accuracy &amp; Feature Changes</h2>
<p>We work to keep PromptSyra.com accessible, fast, and helpful, but the website and its content are provided on an "as available" basis. Because AI image tools and creative workflows change over time, we may update, reorganize, edit, or remove articles, prompt collections, categories, or site features at any time without prior notice. To the fullest extent permitted by applicable law, PromptSyra.com is not liable for indirect or incidental losses arising from temporary downtime, outdated tool behavior, or reliance on informational prompt examples.</p>

<h2>7. Copyright &amp; Content Inquiries</h2>
<p>PromptSyra.com respects the intellectual property rights of creators and publishers. If you believe any text or media published on PromptSyra.com infringes your copyright or trademark rights, please send a clear notice to <a href="mailto:contact@promptsyra.com">contact@promptsyra.com</a> including the exact PromptSyra.com URL, a description of the work in question, and your contact details so we can review and address the matter promptly.</p>

<h2>8. Changes to These Terms &amp; Contact Information</h2>
<p>We may update these Terms &amp; Conditions from time to time to reflect new website features or editorial updates. Revised terms take effect when published on this page with an updated "Last Updated" date. If you have questions regarding these Terms &amp; Conditions, please contact us via our <a href="{$contact_url}">Contact Us page</a> or email <a href="mailto:contact@promptsyra.com"><strong>contact@promptsyra.com</strong></a>.</p>
HTML;

	return array(
		'about-us'             => array(
			'title'           => 'About PromptSyra',
			'preferred_slug'  => 'about-us',
			'candidate_slugs' => array( 'about-us', 'about' ),
			'legacy_slug'     => 'about',
			'layout_mode'     => 'editorial',
			'subtitle'        => __( 'Practical AI photo prompts, clear customization notes, and the editorial contributors behind PromptSyra.com.', 'promptsyra' ),
			'content'         => $about_html,
		),
		'contact-us'           => array(
			'title'           => 'Contact PromptSyra',
			'preferred_slug'  => 'contact-us',
			'candidate_slugs' => array( 'contact-us', 'contact' ),
			'legacy_slug'     => 'contact',
			'layout_mode'     => 'contact',
			'subtitle'        => __( 'Questions, prompt suggestions, content corrections, or partnership inquiries — get in touch with PromptSyra.com.', 'promptsyra' ),
			'content'         => $contact_html,
		),
		'privacy-policy'       => array(
			'title'           => 'Privacy Policy',
			'preferred_slug'  => 'privacy-policy',
			'candidate_slugs' => array( 'privacy-policy', 'privacy' ),
			'legacy_slug'     => '',
			'layout_mode'     => 'legal',
			'subtitle'        => __( 'How PromptSyra.com handles visitor messages, comments, cookies, and third-party resources transparently.', 'promptsyra' ),
			'content'         => $privacy_html,
		),
		'disclaimer'           => array(
			'title'           => 'Disclaimer',
			'preferred_slug'  => 'disclaimer',
			'candidate_slugs' => array( 'disclaimer' ),
			'legacy_slug'     => '',
			'layout_mode'     => 'legal',
			'subtitle'        => __( 'Important context on AI image output variability, customizable prompt examples, and platform independence.', 'promptsyra' ),
			'content'         => $disclaimer_html,
		),
		'terms-and-conditions' => array(
			'title'           => 'Terms & Conditions',
			'preferred_slug'  => 'terms-and-conditions',
			'candidate_slugs' => array( 'terms-and-conditions', 'terms-of-service', 'terms' ),
			'legacy_slug'     => '',
			'layout_mode'     => 'legal',
			'subtitle'        => __( 'Practical guidelines for browsing PromptSyra.com, copying and adapting prompts, and respecting site content.', 'promptsyra' ),
			'content'         => $terms_html,
		),
	);
}

/**
 * Match a WP_Post page object to one of the 5 essential PromptSyra page specifications.
 *
 * @param WP_Post $post Page post object.
 * @return array<string, mixed>|null
 */
function promptsyra_match_essential_page_spec( WP_Post $post ) {
	if ( 'page' !== $post->post_type ) {
		return null;
	}
	$slug  = sanitize_title( $post->post_name );
	$specs = promptsyra_get_essential_pages_spec();

	foreach ( $specs as $key => $spec ) {
		if ( in_array( $slug, $spec['candidate_slugs'], true ) ) {
			$spec['key'] = $key;
			return $spec;
		}
	}
	return null;
}

/**
 * Determine whether an existing WordPress page still holds an unedited legacy
 * AIPromptZyra 1-line stub or raw WordPress "Suggested text:" boilerplate.
 *
 * Once synced or manually edited by the site owner, this returns false so
 * page.php renders standard editable WordPress database content via the_content().
 *
 * @param WP_Post $post Page post object.
 * @return bool
 */
function promptsyra_is_legacy_stub_page( WP_Post $post ) {
	if ( get_post_meta( $post->ID, '_promptsyra_v230_synced', true ) ) {
		return false;
	}

	$raw_title   = (string) $post->post_title;
	$raw_content = trim( wp_strip_all_tags( (string) $post->post_content ) );

	// Legacy AIPromptZyra brand leftover in title or body.
	if ( false !== stripos( $raw_title, 'aipromptzyra' ) || false !== stripos( $raw_content, 'aipromptzyra' ) ) {
		return true;
	}

	// Default WordPress Privacy Policy unedited generator template ("Who we are" + "Suggested text:").
	if ( false !== stripos( $raw_content, 'Suggested text:' ) ) {
		return true;
	}

	// Empty or 1-sentence placeholder stub (< 220 characters) on one of the 5 essential slugs.
	if ( mb_strlen( $raw_content ) < 220 && false === strpos( (string) $post->post_content, '[promptsyra_' ) ) {
		return true;
	}

	return false;
}

/**
 * Locate an existing page (published or draft) across candidate slugs without creating duplicates.
 *
 * @param string[] $candidate_slugs Ordered list of slugs to inspect.
 * @param string   $page_key        Essential page key ('privacy-policy', etc.).
 * @return WP_Post|null
 */
function promptsyra_find_existing_page_by_slugs( array $candidate_slugs, $page_key = '' ) {
	if ( 'privacy-policy' === $page_key ) {
		$policy_id = (int) get_option( 'wp_page_for_privacy_policy' );
		if ( $policy_id > 0 ) {
			$policy_post = get_post( $policy_id );
			if ( $policy_post instanceof WP_Post && 'page' === $policy_post->post_type && 'trash' !== $policy_post->post_status ) {
				return $policy_post;
			}
		}
	}

	foreach ( $candidate_slugs as $slug ) {
		$found = get_posts(
			array(
				'name'                   => sanitize_title( $slug ),
				'post_type'              => 'page',
				'post_status'            => array( 'publish', 'draft', 'private', 'pending' ),
				'posts_per_page'         => 1,
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
			)
		);
		if ( ! empty( $found ) && $found[0] instanceof WP_Post ) {
			return $found[0];
		}
	}

	return null;
}

/**
 * Synchronize the 3 designated WordPress author accounts and 5 essential pages safely.
 *
 * Non-destructive guarantees:
 * - Never deletes or reassigns existing published posts (e.g., /hello-world/).
 * - Never overwrites existing custom author bios or portraits unless empty or $force_refresh = true.
 * - Backs up original page title/slug/content into _promptsyra_backup_v220 post meta before updating.
 * - Updates existing legacy pages (/about/, /contact/, etc.) in-place to avoid duplicate pages.
 *
 * @param bool $force_refresh Whether to refresh pages even if already marked synced.
 * @return array<string, int> Summary counts.
 */
function promptsyra_sync_authors_and_essential_pages( $force_refresh = false ) {
	$summary = array(
		'authors_created' => 0,
		'authors_updated' => 0,
		'pages_created'   => 0,
		'pages_updated'   => 0,
	);

	// 1. Provision or update the 3 designated WordPress Author accounts.
	foreach ( promptsyra_get_designated_authors_spec() as $author_key => $spec ) {
		$existing_user = get_user_by( 'slug', $spec['slug_hint'] );
		if ( ! $existing_user ) {
			$existing_user = get_user_by( 'login', $spec['slug_hint'] );
		}

		$user_id      = 0;
		$was_inserted = false;

		if ( $existing_user instanceof WP_User ) {
			$user_id = (int) $existing_user->ID;
		} else {
			$internal_email = 'author-' . $spec['slug_hint'] . '@promptsyra.invalid';
			if ( email_exists( $internal_email ) ) {
				$internal_email = 'author-' . $spec['slug_hint'] . '-' . wp_rand( 100, 999 ) . '@promptsyra.invalid';
			}

			$inserted = wp_insert_user(
				array(
					'user_login'    => $spec['slug_hint'],
					'user_nicename' => $spec['slug_hint'],
					'display_name'  => $spec['display_name'],
					'nickname'      => $spec['display_name'],
					'first_name'    => $spec['first_name'],
					'last_name'     => $spec['last_name'],
					'user_email'    => $internal_email,
					'user_pass'     => wp_generate_password( 28, true, true ),
					'role'          => 'author',
					'description'   => $spec['default_bio'],
				)
			);

			if ( ! is_wp_error( $inserted ) && $inserted > 0 ) {
				$user_id      = (int) $inserted;
				$was_inserted = true;
				++$summary['authors_created'];
			}
		}

		if ( $user_id > 0 ) {
			set_theme_mod( $spec['mod_key'], $user_id );

			$current_photo = trim( (string) get_user_meta( $user_id, 'promptsyra_profile_photo_url', true ) );
			if ( empty( $current_photo ) || $force_refresh ) {
				update_user_meta( $user_id, 'promptsyra_profile_photo_url', esc_url_raw( $spec['photo_url'] ) );
			}

			$current_role = trim( (string) get_user_meta( $user_id, 'promptsyra_editorial_role', true ) );
			if ( empty( $current_role ) || $force_refresh ) {
				update_user_meta( $user_id, 'promptsyra_editorial_role', sanitize_text_field( $spec['role_label'] ) );
			}

			$current_bio = trim( (string) get_user_meta( $user_id, 'description', true ) );
			if ( empty( $current_bio ) || $force_refresh ) {
				wp_update_user(
					array(
						'ID'           => $user_id,
						'display_name' => $spec['display_name'],
						'first_name'   => $spec['first_name'],
						'last_name'    => $spec['last_name'],
						'description'  => $spec['default_bio'],
					)
				);
			}

			if ( ! $was_inserted ) {
				++$summary['authors_updated'];
			}
		}
	}

	// 2. Provision or update the 5 essential WordPress Pages in-place without duplicates.
	$page_specs = promptsyra_get_essential_pages_spec();
	foreach ( $page_specs as $page_key => $p_spec ) {
		$existing_page = promptsyra_find_existing_page_by_slugs( $p_spec['candidate_slugs'], $page_key );

		if ( $existing_page instanceof WP_Post ) {
			// Save a one-time recoverable snapshot before modifying any legacy page.
			if ( ! get_post_meta( $existing_page->ID, '_promptsyra_backup_v220', true ) ) {
				update_post_meta(
					$existing_page->ID,
					'_promptsyra_backup_v220',
					array(
						'post_title'   => $existing_page->post_title,
						'post_name'    => $existing_page->post_name,
						'post_content' => $existing_page->post_content,
						'post_status'  => $existing_page->post_status,
						'backed_up_at' => gmdate( 'c' ),
					)
				);
			}

			$should_update_body = $force_refresh || promptsyra_is_legacy_stub_page( $existing_page ) || 'publish' !== $existing_page->post_status;

			if ( $should_update_body ) {
				$updated_id = wp_update_post(
					array(
						'ID'             => $existing_page->ID,
						'post_title'     => $p_spec['title'],
						'post_name'      => $p_spec['preferred_slug'],
						'post_content'   => $p_spec['content'],
						'post_status'    => 'publish',
						'comment_status' => 'closed',
						'ping_status'    => 'closed',
					),
					true
				);

				if ( ! is_wp_error( $updated_id ) ) {
					update_post_meta( $existing_page->ID, '_promptsyra_v230_synced', '2.3.0' );
					++$summary['pages_updated'];
				}
			} elseif ( $existing_page->post_name !== $p_spec['preferred_slug'] && in_array( $existing_page->post_name, array( 'about', 'contact' ), true ) ) {
				// Standardize legacy /about/ -> /about-us/ and /contact/ -> /contact-us/ slug while keeping custom body intact.
				wp_update_post(
					array(
						'ID'        => $existing_page->ID,
						'post_name' => $p_spec['preferred_slug'],
					)
				);
				++$summary['pages_updated'];
			}

			if ( 'privacy-policy' === $page_key ) {
				update_option( 'wp_page_for_privacy_policy', (int) $existing_page->ID );
			}
		} else {
			$new_page_id = wp_insert_post(
				array(
					'post_type'      => 'page',
					'post_title'     => $p_spec['title'],
					'post_name'      => $p_spec['preferred_slug'],
					'post_content'   => $p_spec['content'],
					'post_status'    => 'publish',
					'comment_status' => 'closed',
					'ping_status'    => 'closed',
				),
				true
			);

			if ( ! is_wp_error( $new_page_id ) && $new_page_id > 0 ) {
				update_post_meta( $new_page_id, '_promptsyra_v230_synced', '2.3.0' );
				if ( 'privacy-policy' === $page_key ) {
					update_option( 'wp_page_for_privacy_policy', (int) $new_page_id );
				}
				++$summary['pages_created'];
			}
		}
	}

	update_option( 'promptsyra_v230_sync_completed', current_time( 'mysql' ) );
	flush_rewrite_rules( false );

	return $summary;
}

/**
 * Automatically run the non-destructive author & essential page sync upon theme activation.
 */
function promptsyra_auto_sync_on_theme_switch() {
	if ( ! get_option( 'promptsyra_v230_sync_completed' ) ) {
		promptsyra_sync_authors_and_essential_pages( false );
	}
}
add_action( 'after_switch_theme', 'promptsyra_auto_sync_on_theme_switch' );

/**
 * Bridge /about/ <-> /about-us/ and /contact/ <-> /contact-us/ cleanly with 301 redirects
 * so neither legacy bookmarks nor new canonical URLs ever hit a 404 before or after sync.
 */
function promptsyra_bridge_legacy_and_preferred_page_slugs() {
	if ( ! is_404() ) {
		return;
	}

	$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
	$path        = trim( (string) wp_parse_url( $request_uri, PHP_URL_PATH ), '/' );

	$pairs = array(
		'about-us'   => 'about',
		'about'      => 'about-us',
		'contact-us' => 'contact',
		'contact'    => 'contact-us',
	);

	if ( isset( $pairs[ $path ] ) ) {
		$counterpart = get_page_by_path( $pairs[ $path ] );
		if ( $counterpart instanceof WP_Post && 'publish' === $counterpart->post_status ) {
			$target = get_permalink( $counterpart->ID );
			if ( $target ) {
				wp_safe_redirect( $target, 301 );
				exit;
			}
		}
	}

	$category_alias_pairs = array(
		'category/social-media-prompts'    => 'ai-social-media-prompts',
		'category/ai-social-media-prompts' => 'social-media-prompts',
		'social-media-prompts'             => 'ai-social-media-prompts',
		'ai-social-media-prompts'          => 'social-media-prompts',
	);

	if ( isset( $category_alias_pairs[ $path ] ) ) {
		$cat_term = get_term_by( 'slug', $category_alias_pairs[ $path ], 'category' );
		if ( $cat_term && ! is_wp_error( $cat_term ) ) {
			$cat_link = get_term_link( $cat_term );
			if ( ! is_wp_error( $cat_link ) && ! empty( $cat_link ) ) {
				wp_safe_redirect( $cat_link, 301 );
				exit;
			}
		}
	}
}
add_action( 'template_redirect', 'promptsyra_bridge_legacy_and_preferred_page_slugs', 5 );

/**
 * Display a clear, actionable WP-Admin banner when the v2.3.0 Author & Essential Pages
 * database sync has not been triggered yet (for example, when updating an already-active
 * theme via Upload Theme -> Replace active with uploaded).
 */
function promptsyra_render_v230_sync_admin_notice() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	if ( get_option( 'promptsyra_v230_sync_completed' ) ) {
		return;
	}

	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	if ( $screen && 'appearance_page_promptsyra-setup' === $screen->id ) {
		return;
	}
	?>
	<div class="notice notice-info" style="border-left-color:#7c3aed;padding:12px 16px;">
		<p style="margin:0 0 8px;font-size:14px;">
			<strong><?php esc_html_e( 'PromptSyra v2.3.0 Ready:', 'promptsyra' ); ?></strong>
			<?php esc_html_e( 'Click below to create the 3 designated WordPress author profiles (Syra Ansari, Arjun Patel, Neha Joshi) with their Media Library WebP portraits and publish the updated About Us, Contact Us, Privacy Policy, Disclaimer, and Terms & Conditions pages in WordPress (original page stubs are backed up automatically; existing posts are never reassigned).', 'promptsyra' ); ?>
		</p>
		<form method="post" action="<?php echo esc_url( admin_url( 'themes.php?page=promptsyra-setup' ) ); ?>" style="margin:0;">
			<?php wp_nonce_field( 'promptsyra_v230_sync_action', 'promptsyra_v230_sync_nonce' ); ?>
			<button type="submit" name="promptsyra_run_v230_sync" value="1" class="button button-primary">
				<?php esc_html_e( 'Sync 3 Authors & 5 Essential Pages Now', 'promptsyra' ); ?>
			</button>
			<a href="<?php echo esc_url( admin_url( 'themes.php?page=promptsyra-setup' ) ); ?>" class="button" style="margin-left:6px;">
				<?php esc_html_e( 'Review Setup Audit First', 'promptsyra' ); ?>
			</a>
		</form>
	</div>
	<?php
}
add_action( 'admin_notices', 'promptsyra_render_v230_sync_admin_notice' );
