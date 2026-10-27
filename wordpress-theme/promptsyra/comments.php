<?php
/**
 * PromptSyra Comments & "Join the Conversation" Form Template (comments.php)
 *
 * Uses WordPress's native comment APIs (`wp_list_comments`, `the_comments_navigation`,
 * `comment_form`) to preserve full WordPress comment submission, moderation, threaded
 * replies, logged-in / logged-out states, required validation, and comment-cookie
 * consent while delivering a clean, modern PromptSyra.com visual design.
 *
 * @package PromptSyra
 * @version 2.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( post_password_required() ) {
	return;
}

$ps_comment_count   = (int) get_comments_number();
$ps_comment_heading = get_theme_mod(
	'promptsyra_comment_heading',
	__( 'Join the Conversation', 'promptsyra' )
);
$ps_comment_subtext = get_theme_mod(
	'promptsyra_comment_subtext',
	__( 'Have a question or suggestion? Share your thoughts with the PromptSyra community.', 'promptsyra' )
);

$ps_commenter = wp_get_current_commenter();
$ps_req       = (bool) get_option( 'require_name_email' );
$ps_html_req  = $ps_req ? ' required="required" aria-required="true"' : '';

$ps_fields = array(
	'author' => sprintf(
		'<div class="comment-form-author ps-comment-field-group">' .
		'<label for="author" class="ps-comment-label">%1$s%2$s</label>' .
		'<input id="author" name="author" type="text" class="ps-comment-input" value="%3$s" maxlength="245" autocomplete="name" placeholder="%4$s"%5$s />' .
		'</div>',
		esc_html__( 'Name', 'promptsyra' ),
		$ps_req ? ' <span class="required ps-required" aria-hidden="true">*</span>' : '',
		esc_attr( $ps_commenter['comment_author'] ),
		esc_attr__( 'Enter your name', 'promptsyra' ),
		$ps_html_req
	),
	'email'  => sprintf(
		'<div class="comment-form-email ps-comment-field-group">' .
		'<label for="email" class="ps-comment-label">%1$s%2$s</label>' .
		'<input id="email" name="email" type="email" class="ps-comment-input" value="%3$s" maxlength="100" aria-describedby="email-notes" autocomplete="email" placeholder="%4$s"%5$s />' .
		'</div>',
		esc_html__( 'Email Address', 'promptsyra' ),
		$ps_req ? ' <span class="required ps-required" aria-hidden="true">*</span>' : '',
		esc_attr( $ps_commenter['comment_author_email'] ),
		esc_attr__( 'name@example.com', 'promptsyra' ),
		$ps_html_req
	),
	'url'    => sprintf(
		'<div class="comment-form-url ps-comment-field-group">' .
		'<label for="url" class="ps-comment-label">%1$s <span class="ps-optional-tag">%2$s</span></label>' .
		'<input id="url" name="url" type="url" class="ps-comment-input" value="%3$s" maxlength="200" autocomplete="url" placeholder="%4$s" />' .
		'</div>',
		esc_html__( 'Website', 'promptsyra' ),
		esc_html__( '(Optional)', 'promptsyra' ),
		esc_attr( $ps_commenter['comment_author_url'] ),
		esc_attr__( 'https://yourwebsite.com', 'promptsyra' )
	),
);

// Preserve WordPress's native comment-cookie consent setting and behavior without altering privacy rules.
if ( has_action( 'set_comment_cookies', 'wp_set_comment_cookies' ) && get_option( 'show_comments_cookies_opt_in' ) ) {
	$ps_consent           = empty( $ps_commenter['comment_author_email'] ) ? '' : ' checked="checked"';
	$ps_fields['cookies'] = sprintf(
		'<div class="comment-form-cookies-consent ps-comment-cookies-row">' .
		'<input id="wp-comment-cookies-consent" name="wp-comment-cookies-consent" type="checkbox" class="ps-comment-checkbox" value="yes"%1$s />' .
		'<label for="wp-comment-cookies-consent" class="ps-comment-cookies-label">%2$s</label>' .
		'</div>',
		$ps_consent,
		esc_html__( 'Save my name and email in this browser for the next time I comment.', 'promptsyra' )
	);
}
?>

<section id="comments" class="comments-area ps-comments-section" aria-label="<?php esc_attr_e( 'Article Comments', 'promptsyra' ); ?>">
	<?php if ( have_comments() ) : ?>
		<div class="ps-existing-comments-block">
			<h2 class="comments-title ps-comments-count-heading">
				<?php
				printf(
					/* translators: 1: number of comments, 2: post title */
					esc_html( _nx( '%1$s Comment on &ldquo;%2$s&rdquo;', '%1$s Comments on &ldquo;%2$s&rdquo;', $ps_comment_count, 'comments title', 'promptsyra' ) ),
					esc_html( number_format_i18n( $ps_comment_count ) ),
					esc_html( get_the_title() )
				);
				?>
			</h2>

			<ol class="comment-list ps-comment-list">
				<?php
				wp_list_comments(
					array(
						'style'       => 'ol',
						'short_ping'  => true,
						'avatar_size' => 44,
					)
				);
				?>
			</ol>

			<?php
			the_comments_navigation(
				array(
					'prev_text' => esc_html__( '← Older Comments', 'promptsyra' ),
					'next_text' => esc_html__( 'Newer Comments →', 'promptsyra' ),
				)
			);
			?>
		</div>
	<?php endif; ?>

	<?php if ( ! comments_open() && $ps_comment_count > 0 && post_type_supports( get_post_type(), 'comments' ) ) : ?>
		<p class="no-comments ps-comments-closed-note">
			<?php esc_html_e( 'Comments are closed for this article.', 'promptsyra' ); ?>
		</p>
	<?php endif; ?>

	<?php
	if ( comments_open() ) {
		comment_form(
			array(
				'class_container'      => 'comment-respond ps-comment-respond-card',
				'class_form'           => 'comment-form ps-comment-form',
				'title_reply_before'   => '<div class="ps-comment-reply-header"><div class="ps-comment-title-row"><h2 id="reply-title" class="comment-reply-title ps-comment-reply-title">',
				'title_reply'          => esc_html( $ps_comment_heading ),
				'title_reply_to'       => esc_html__( 'Reply to %s', 'promptsyra' ),
				'cancel_reply_before'  => '<span class="ps-cancel-reply-wrap">',
				'cancel_reply_after'   => '</span>',
				'cancel_reply_link'    => esc_html__( 'Cancel reply', 'promptsyra' ),
				'title_reply_after'    => '</h2></div><p class="ps-comment-reply-subtext">' . esc_html( $ps_comment_subtext ) . '</p></div>',
				'comment_notes_before' => sprintf(
					'<p class="comment-notes ps-comment-notes"><span id="email-notes">%1$s</span> <span class="required-field-message">%2$s <span class="required ps-required" aria-hidden="true">*</span></span></p>',
					esc_html__( 'Your email address will not be published.', 'promptsyra' ),
					esc_html__( 'Required fields are marked', 'promptsyra' )
				),
				'comment_field'        => sprintf(
					'<div class="comment-form-comment ps-comment-field-group">' .
					'<label for="comment" class="ps-comment-label">%1$s <span class="required ps-required" aria-hidden="true">*</span></label>' .
					'<textarea id="comment" name="comment" class="ps-comment-textarea" cols="45" rows="5" maxlength="65525" placeholder="%2$s" required="required" aria-required="true"></textarea>' .
					'</div>',
					esc_html__( 'Your Comment', 'promptsyra' ),
					esc_attr__( 'Write your comment here...', 'promptsyra' )
				),
				'fields'               => $ps_fields,
				'label_submit'         => esc_html__( 'Post Comment', 'promptsyra' ),
				'name_submit'          => 'submit',
				'id_submit'            => 'submit',
				'class_submit'         => 'submit ps-comment-submit-btn',
				'submit_button'        => '<button name="%1$s" type="submit" id="%2$s" class="%3$s" value="%4$s"><span class="ps-submit-btn-label">%4$s</span></button>',
				'submit_field'         => '<div class="form-submit ps-comment-submit-row">%1$s %2$s</div>',
			)
		);
	}
	?>
</section>
