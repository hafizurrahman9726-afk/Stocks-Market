<?php
/**
 * PromptSyra Native Contact Form Module
 *
 * Provides the [promptsyra_contact_form] shortcode and secure WordPress
 * admin-post submission handlers delivering inquiries to contact@promptsyra.com.
 *
 * Security & Honesty Guarantees:
 * - WordPress Nonce verification
 * - Honeypot field + signed render-timestamp check against automated bots
 * - Hashed IP rate-limiting transient (max 3 submissions per 15 minutes)
 * - Strict input sanitization and CR/LF header-injection prevention
 * - Honest wp_mail() return verification: never claims delivery succeeded if
 *   the hosting server's mail transport fails; preserves user input via a
 *   short-lived token and offers direct mailto:contact@promptsyra.com fallback.
 *
 * @package PromptSyra
 * @version 2.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return the official PromptSyra contact email address.
 *
 * @return string
 */
function promptsyra_get_official_contact_email() {
	return 'contact@promptsyra.com';
}

/**
 * Strip carriage returns and line feeds to prevent email header injection.
 *
 * @param string $value Raw header token.
 * @return string
 */
function promptsyra_strip_header_breaks( $value ) {
	return trim( str_replace( array( "\r", "\n", '%0a', '%0d', '%0A', '%0D' ), ' ', (string) $value ) );
}

/**
 * Compute a privacy-preserving hashed client fingerprint for anti-spam rate limiting.
 *
 * @return string
 */
function promptsyra_get_rate_limit_key() {
	$remote_addr = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : 'unknown';
	return 'ps_cf_rl_' . substr( hash_hmac( 'sha256', $remote_addr, wp_salt( 'nonce' ) ), 0, 24 );
}

/**
 * Create a signed form timestamp token to detect instant zero-ms bot submissions.
 *
 * @return string
 */
function promptsyra_create_form_timestamp_token() {
	$now = (string) time();
	$sig = hash_hmac( 'sha256', $now, wp_salt( 'auth' ) );
	return $now . '.' . substr( $sig, 0, 20 );
}

/**
 * Verify that the signed form timestamp token is valid and at least 2 seconds old.
 *
 * @param string $token Submitted token.
 * @return bool
 */
function promptsyra_verify_form_timestamp_token( $token ) {
	$parts = explode( '.', (string) $token );
	if ( 2 !== count( $parts ) || ! ctype_digit( $parts[0] ) ) {
		return false;
	}
	$ts       = (int) $parts[0];
	$expected = substr( hash_hmac( 'sha256', $parts[0], wp_salt( 'auth' ) ), 0, 20 );
	if ( ! hash_equals( $expected, $parts[1] ) ) {
		return false;
	}
	$age = time() - $ts;
	return ( $age >= 2 && $age <= DAY_IN_SECONDS );
}

/**
 * Redirect back to the Contact page with a short-lived status token.
 *
 * @param string               $return_url Safe redirect URL.
 * @param string               $type       'success' | 'error'.
 * @param string               $message    Human-readable status message.
 * @param array<string,string> $old_fields Submitted field values to repopulate on error.
 */
function promptsyra_contact_redirect_with_state( $return_url, $type, $message, array $old_fields = array() ) {
	$state_token = wp_generate_password( 20, false, false );
	set_transient(
		'ps_cf_state_' . $state_token,
		array(
			'type'    => $type,
			'message' => $message,
			'fields'  => $old_fields,
		),
		5 * MINUTE_IN_SECONDS
	);

	$target = add_query_arg( 'ps_contact_state', rawurlencode( $state_token ), $return_url ) . '#ps-contact-status';
	wp_safe_redirect( $target );
	exit;
}

/**
 * Process Contact Form POST submissions via admin-post.php.
 */
function promptsyra_handle_contact_submission() {
	$default_return = promptsyra_get_verified_page_url( array( 'contact-us', 'contact' ) );
	if ( empty( $default_return ) ) {
		$default_return = home_url( '/contact-us/' );
	}

	$raw_return = isset( $_POST['ps_return_url'] ) ? esc_url_raw( wp_unslash( $_POST['ps_return_url'] ) ) : '';
	$return_url = wp_validate_redirect( $raw_return, $default_return );
	$return_url = remove_query_arg( 'ps_contact_state', $return_url );

	// 1. Verify WordPress security nonce.
	if (
		! isset( $_POST['promptsyra_contact_nonce'] ) ||
		! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['promptsyra_contact_nonce'] ) ), 'promptsyra_contact_submit' )
	) {
		promptsyra_contact_redirect_with_state(
			$return_url,
			'error',
			__( 'Your form session expired before submission. Please review your message and submit again.', 'promptsyra' )
		);
	}

	// Sanitize submitted fields upfront so legitimate visitors never lose their text on validation errors.
	$sender_name  = isset( $_POST['ps_contact_name'] ) ? promptsyra_strip_header_breaks( sanitize_text_field( wp_unslash( $_POST['ps_contact_name'] ) ) ) : '';
	$sender_email = isset( $_POST['ps_contact_email'] ) ? promptsyra_strip_header_breaks( sanitize_email( wp_unslash( $_POST['ps_contact_email'] ) ) ) : '';
	$subject      = isset( $_POST['ps_contact_subject'] ) ? promptsyra_strip_header_breaks( sanitize_text_field( wp_unslash( $_POST['ps_contact_subject'] ) ) ) : '';
	$message      = isset( $_POST['ps_contact_message'] ) ? trim( sanitize_textarea_field( wp_unslash( $_POST['ps_contact_message'] ) ) ) : '';

	$old_fields = array(
		'name'    => $sender_name,
		'email'   => $sender_email,
		'subject' => $subject,
		'message' => $message,
	);

	// 2. Honeypot check (should always remain empty for human visitors).
	$honeypot = isset( $_POST['ps_website_confirm'] ) ? trim( sanitize_text_field( wp_unslash( $_POST['ps_website_confirm'] ) ) ) : '';
	if ( '' !== $honeypot ) {
		promptsyra_contact_redirect_with_state(
			$return_url,
			'error',
			__( 'Spam protection blocked this submission. Please clear any autofilled hidden fields or email contact@promptsyra.com directly.', 'promptsyra' )
		);
	}

	// 3. Timestamp check against automated zero-latency bot scripts.
	$ts_token = isset( $_POST['ps_form_ts'] ) ? sanitize_text_field( wp_unslash( $_POST['ps_form_ts'] ) ) : '';
	if ( ! promptsyra_verify_form_timestamp_token( $ts_token ) ) {
		promptsyra_contact_redirect_with_state(
			$return_url,
			'error',
			__( 'Please wait a moment after loading the form before submitting your message.', 'promptsyra' ),
			$old_fields
		);
	}

	// 4. Validate required fields and email format.
	if ( '' === $sender_name || mb_strlen( $sender_name ) < 2 ) {
		promptsyra_contact_redirect_with_state(
			$return_url,
			'error',
			__( 'Please enter your name so we know how to address our reply.', 'promptsyra' ),
			$old_fields
		);
	}

	if ( '' === $sender_email || ! is_email( $sender_email ) ) {
		promptsyra_contact_redirect_with_state(
			$return_url,
			'error',
			__( 'Please enter a valid email address so we can respond to your inquiry.', 'promptsyra' ),
			$old_fields
		);
	}

	if ( '' === $subject || mb_strlen( $subject ) < 3 ) {
		promptsyra_contact_redirect_with_state(
			$return_url,
			'error',
			__( 'Please enter a brief subject describing your question or feedback.', 'promptsyra' ),
			$old_fields
		);
	}

	if ( '' === $message || mb_strlen( $message ) < 10 ) {
		promptsyra_contact_redirect_with_state(
			$return_url,
			'error',
			__( 'Please include a clear message (at least 10 characters) with the details of your inquiry.', 'promptsyra' ),
			$old_fields
		);
	}

	if ( mb_strlen( $message ) > 6000 ) {
		promptsyra_contact_redirect_with_state(
			$return_url,
			'error',
			__( 'Your message exceeds the 6,000-character limit. Please shorten it or email contact@promptsyra.com directly.', 'promptsyra' ),
			$old_fields
		);
	}

	// 5. Rate limit check (max 3 submissions per 15 minutes per hashed IP).
	$rl_key   = promptsyra_get_rate_limit_key();
	$attempts = (int) get_transient( $rl_key );
	if ( $attempts >= 3 ) {
		promptsyra_contact_redirect_with_state(
			$return_url,
			'error',
			__( 'Too many messages were submitted recently from this connection. Please wait 15 minutes or email contact@promptsyra.com directly.', 'promptsyra' ),
			$old_fields
		);
	}

	// 6. Compose and send email to contact@promptsyra.com via wp_mail().
	$recipient    = promptsyra_get_official_contact_email();
	$mail_subject = sprintf( '[PromptSyra.com Contact] %s', $subject );

	$mail_lines = array(
		'New inquiry submitted via the PromptSyra.com Contact Form:',
		'------------------------------------------------------------',
		'Name: ' . $sender_name,
		'Email: ' . $sender_email,
		'Subject: ' . $subject,
		'Submitted (UTC): ' . gmdate( 'Y-m-d H:i:s' ) . ' UTC',
		'Page URL: ' . $return_url,
		'------------------------------------------------------------',
		'',
		$message,
		'',
		'------------------------------------------------------------',
		'Reply directly to this email to respond to ' . $sender_name . ' <' . $sender_email . '>.',
	);
	$mail_body = implode( "\n", $mail_lines );

	$headers = array(
		'Content-Type: text/plain; charset=UTF-8',
		sprintf( 'Reply-To: %s <%s>', str_replace( array( '"', '<', '>' ), '', $sender_name ), $sender_email ),
	);

	$sent = wp_mail( $recipient, $mail_subject, $mail_body, $headers );

	if ( ! $sent ) {
		promptsyra_contact_redirect_with_state(
			$return_url,
			'error',
			__( 'Our server could not dispatch the email right now. Your message has been kept in the form below—please send it directly to contact@promptsyra.com using your email client.', 'promptsyra' ),
			$old_fields
		);
	}

	set_transient( $rl_key, $attempts + 1, 15 * MINUTE_IN_SECONDS );

	promptsyra_contact_redirect_with_state(
		$return_url,
		'success',
		__( 'Thank you for contacting PromptSyra.com. Your message has been sent to contact@promptsyra.com.', 'promptsyra' )
	);
}
add_action( 'admin_post_nopriv_promptsyra_send_contact', 'promptsyra_handle_contact_submission' );
add_action( 'admin_post_promptsyra_send_contact', 'promptsyra_handle_contact_submission' );

/**
 * Render the [promptsyra_contact_form] shortcode.
 *
 * If an administrator configures an external form plugin shortcode in Customizer
 * (e.g. Contact Form 7, WPForms, Fluent Forms), it renders that shortcode cleanly;
 * otherwise it renders PromptSyra's built-in accessible WordPress contact form.
 *
 * @return string
 */
function promptsyra_render_contact_form_shortcode() {
	$custom_shortcode = trim( (string) get_theme_mod( 'promptsyra_custom_contact_shortcode', '' ) );
	if ( ! empty( $custom_shortcode ) && '[' === substr( $custom_shortcode, 0, 1 ) && 'promptsyra_contact_form' !== trim( $custom_shortcode, '[] ' ) ) {
		return '<div class="ps-contact-form-card">' . do_shortcode( $custom_shortcode ) . '</div>';
	}

	$state_type    = '';
	$state_message = '';
	$old_values    = array(
		'name'    => '',
		'email'   => '',
		'subject' => '',
		'message' => '',
	);

	if ( isset( $_GET['ps_contact_state'] ) ) {
		$token = preg_replace( '/[^a-zA-Z0-9]/', '', sanitize_text_field( wp_unslash( $_GET['ps_contact_state'] ) ) );
		if ( ! empty( $token ) ) {
			$saved = get_transient( 'ps_cf_state_' . $token );
			if ( is_array( $saved ) ) {
				$state_type    = isset( $saved['type'] ) ? (string) $saved['type'] : '';
				$state_message = isset( $saved['message'] ) ? (string) $saved['message'] : '';
				if ( isset( $saved['fields'] ) && is_array( $saved['fields'] ) ) {
					$old_values = wp_parse_args( $saved['fields'], $old_values );
				}
				delete_transient( 'ps_cf_state_' . $token );
			}
		}
	}

	$privacy_url = get_privacy_policy_url();
	if ( empty( $privacy_url ) ) {
		$privacy_url = promptsyra_get_verified_page_url( array( 'privacy-policy', 'privacy' ) );
	}
	if ( empty( $privacy_url ) ) {
		$privacy_url = home_url( '/privacy-policy/' );
	}

	$current_url    = get_permalink();
	$official_email = promptsyra_get_official_contact_email();

	ob_start();
	?>
	<div id="ps-contact-status" class="ps-contact-form-card">
		<div class="ps-contact-form-header">
			<h2 class="ps-contact-form-title"><?php esc_html_e( 'Send Us a Message', 'promptsyra' ); ?></h2>
			<p class="ps-contact-form-subtitle">
				<?php esc_html_e( 'Complete all required fields marked with an asterisk (*). Messages are delivered directly to our editorial inbox.', 'promptsyra' ); ?>
			</p>
		</div>

		<?php if ( 'success' === $state_type && ! empty( $state_message ) ) : ?>
			<div class="ps-contact-alert is-success" role="status" aria-live="polite">
				<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true">
					<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
					<polyline points="22 4 12 14.01 9 11.01"></polyline>
				</svg>
				<div>
					<strong><?php esc_html_e( 'Message Sent', 'promptsyra' ); ?></strong>
					<p><?php echo esc_html( $state_message ); ?></p>
				</div>
			</div>
		<?php elseif ( 'error' === $state_type && ! empty( $state_message ) ) : ?>
			<div class="ps-contact-alert is-error" role="alert">
				<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true">
					<circle cx="12" cy="12" r="10"></circle>
					<line x1="12" y1="8" x2="12" y2="12"></line>
					<line x1="12" y1="16" x2="12.01" y2="16"></line>
				</svg>
				<div>
					<strong><?php esc_html_e( 'Unable to Send Message', 'promptsyra' ); ?></strong>
					<p>
						<?php echo esc_html( $state_message ); ?>
						<a href="mailto:<?php echo esc_attr( $official_email ); ?>"><?php echo esc_html( $official_email ); ?></a>
					</p>
				</div>
			</div>
		<?php endif; ?>

		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="ps-contact-form" novalidate>
			<input type="hidden" name="action" value="promptsyra_send_contact" />
			<input type="hidden" name="ps_return_url" value="<?php echo esc_url( $current_url ? $current_url : home_url( '/contact-us/' ) ); ?>" />
			<input type="hidden" name="ps_form_ts" value="<?php echo esc_attr( promptsyra_create_form_timestamp_token() ); ?>" />
			<?php wp_nonce_field( 'promptsyra_contact_submit', 'promptsyra_contact_nonce' ); ?>

			<div class="ps-cf-hp" aria-hidden="true">
				<label for="ps-website-confirm"><?php esc_html_e( 'Leave this field blank', 'promptsyra' ); ?></label>
				<input type="text" id="ps-website-confirm" name="ps_website_confirm" value="" tabindex="-1" autocomplete="off" />
			</div>

			<div class="ps-contact-row-2col">
				<div class="ps-contact-field">
					<label for="ps-contact-name" class="ps-contact-label">
						<?php esc_html_e( 'Name', 'promptsyra' ); ?> <span class="ps-req-mark" aria-hidden="true">*</span>
					</label>
					<input
						type="text"
						id="ps-contact-name"
						name="ps_contact_name"
						class="ps-contact-input"
						value="<?php echo esc_attr( $old_values['name'] ); ?>"
						placeholder="<?php esc_attr_e( 'Your full name', 'promptsyra' ); ?>"
						autocomplete="name"
						maxlength="120"
						required
						aria-required="true"
					/>
				</div>

				<div class="ps-contact-field">
					<label for="ps-contact-email" class="ps-contact-label">
						<?php esc_html_e( 'Email Address', 'promptsyra' ); ?> <span class="ps-req-mark" aria-hidden="true">*</span>
					</label>
					<input
						type="email"
						id="ps-contact-email"
						name="ps_contact_email"
						class="ps-contact-input"
						value="<?php echo esc_attr( $old_values['email'] ); ?>"
						placeholder="<?php esc_attr_e( 'you@example.com', 'promptsyra' ); ?>"
						autocomplete="email"
						maxlength="190"
						required
						aria-required="true"
					/>
				</div>
			</div>

			<div class="ps-contact-field">
				<label for="ps-contact-subject" class="ps-contact-label">
					<?php esc_html_e( 'Subject', 'promptsyra' ); ?> <span class="ps-req-mark" aria-hidden="true">*</span>
				</label>
				<input
					type="text"
					id="ps-contact-subject"
					name="ps_contact_subject"
					class="ps-contact-input"
					value="<?php echo esc_attr( $old_values['subject'] ); ?>"
					placeholder="<?php esc_attr_e( 'e.g., Prompt suggestion, content correction, or collaboration inquiry', 'promptsyra' ); ?>"
					maxlength="180"
					required
					aria-required="true"
				/>
			</div>

			<div class="ps-contact-field">
				<label for="ps-contact-message" class="ps-contact-label">
					<?php esc_html_e( 'Message', 'promptsyra' ); ?> <span class="ps-req-mark" aria-hidden="true">*</span>
				</label>
				<textarea
					id="ps-contact-message"
					name="ps_contact_message"
					class="ps-contact-textarea"
					rows="6"
					placeholder="<?php esc_attr_e( 'Share the page URL, prompt details, or question so we can review it accurately...', 'promptsyra' ); ?>"
					maxlength="6000"
					required
					aria-required="true"
				><?php echo esc_textarea( $old_values['message'] ); ?></textarea>
			</div>

			<p class="ps-contact-privacy-note" id="ps-contact-privacy-desc">
				<?php
				printf(
					/* translators: %s: Privacy Policy URL */
					wp_kses(
						__( 'By submitting this form, you agree that PromptSyra.com may use your message and contact details to review and respond to your inquiry, as described in our <a href="%s">Privacy Policy</a>.', 'promptsyra' ),
						array(
							'a' => array(
								'href' => array(),
							),
						)
					),
					esc_url( $privacy_url )
				);
				?>
			</p>

			<div class="ps-contact-submit-row">
				<button type="submit" class="ps-btn-primary ps-contact-submit-btn" aria-describedby="ps-contact-privacy-desc">
					<span><?php esc_html_e( 'Send Message', 'promptsyra' ); ?></span>
					<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true">
						<line x1="22" y1="2" x2="11" y2="13"></line>
						<polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
					</svg>
				</button>
			</div>
		</form>
	</div>
	<?php
	return (string) ob_get_clean();
}
add_shortcode( 'promptsyra_contact_form', 'promptsyra_render_contact_form_shortcode' );
