<?php
/**
 * PromptSyra Single-Post Helpers, Reusable AI Prompt Card Shortcode/Block, & Conditional TOC
 *
 * Provides:
 * 1. Dynamic reading-time calculation from real post content (`promptsyra_get_reading_time`).
 * 2. Reusable AI Prompt Box component (`[promptsyra_prompt]` shortcode + `promptsyra/ai-prompt-box` Gutenberg block)
 *    with View Prompt expand/collapse, clean prompt-only Copy Prompt button, and ChatGPT / Gemini / Grok launchers.
 * 3. Conditional Table of Contents injection on single posts ONLY when >= 3 headings exist AND no third-party
 *    TOC plugin (#toc_container, EasyTOC, RankMath TOC) is already rendering one.
 *
 * @package PromptSyra
 * @version 2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Compute estimated reading time for a post (200 words per minute, minimum 1 min read).
 *
 * @param int $post_id Optional post ID. Defaults to current post.
 * @return string
 */
function promptsyra_get_reading_time( $post_id = 0 ) {
	$post_id = $post_id ? absint( $post_id ) : get_the_ID();
	if ( ! $post_id ) {
		return __( '1 min read', 'promptsyra' );
	}

	$raw_content   = (string) get_post_field( 'post_content', $post_id );
	$clean_content = wp_strip_all_tags( strip_shortcodes( $raw_content ) );
	$words         = preg_split( '/\s+/u', trim( $clean_content ), -1, PREG_SPLIT_NO_EMPTY );
	$word_count    = is_array( $words ) ? count( $words ) : 0;
	$minutes       = max( 1, (int) ceil( $word_count / 200 ) );

	/* translators: %d: estimated reading minutes */
	return sprintf( _n( '%d min read', '%d min read', $minutes, 'promptsyra' ), $minutes );
}

/**
 * Render a reusable PromptSyra AI Prompt Card matching the reference interactive card structure.
 *
 * @param string $prompt_text Raw prompt text to display and copy.
 * @param string $label       Badge label above the prompt text.
 * @return string HTML markup.
 */
function promptsyra_render_prompt_card_html( $prompt_text, $label = '' ) {
	$clean_prompt = trim( wp_strip_all_tags( (string) $prompt_text ) );
	if ( '' === $clean_prompt ) {
		return '';
	}

	$badge_label = '' !== trim( (string) $label ) ? trim( (string) $label ) : __( 'AI Photo Prompt', 'promptsyra' );
	$encoded_q   = rawurlencode( $clean_prompt );
	$chatgpt_url = 'https://chatgpt.com/?q=' . $encoded_q;
	$gemini_url  = 'https://gemini.google.com/?q=' . $encoded_q;
	$grok_url    = 'https://x.com/i/grok?q=' . $encoded_q;

	ob_start();
	?>
	<div class="ps-ai-prompt-box az-ai-prompt-box" data-ps-prompt-card>
		<div class="ps-prompt-badge az-prompt-badge">
			<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
				<path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/>
			</svg>
			<span><?php echo esc_html( $badge_label ); ?></span>
		</div>

		<div class="ps-prompt-content-wrapper az-prompt-content-wrapper" data-ps-prompt-wrapper>
			<p class="ps-prompt-text-preview az-prompt-text-preview" data-ps-prompt-text><?php echo nl2br( esc_html( $clean_prompt ) ); ?></p>
			<div class="ps-prompt-gradient-fade az-prompt-gradient-fade" aria-hidden="true"></div>
		</div>

		<div class="ps-prompt-button-bar az-prompt-button-bar">
			<button type="button" class="ps-btn-view-prompt az-btn-view-prompt" data-ps-toggle-prompt aria-expanded="false">
				<svg class="ps-icon-eye az-icon-eye" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
					<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
					<circle cx="12" cy="12" r="3"></circle>
				</svg>
				<svg class="ps-icon-eye-off az-icon-eye-off" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="display:none;" aria-hidden="true">
					<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
					<line x1="1" y1="1" x2="23" y2="23"></line>
				</svg>
				<span class="ps-view-text az-view-text"><?php esc_html_e( 'View Prompt', 'promptsyra' ); ?></span>
			</button>

			<button type="button" class="ps-btn-copy-prompt az-btn-copy-prompt" data-ps-copy-prompt data-prompt="<?php echo esc_attr( $clean_prompt ); ?>">
				<svg class="ps-icon-copy az-icon-copy" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
					<rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
					<path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
				</svg>
				<svg class="ps-icon-check az-icon-check" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="display:none;" aria-hidden="true">
					<polyline points="20 6 9 17 4 12"></polyline>
				</svg>
				<span class="ps-btn-text az-btn-text"><?php esc_html_e( 'Copy Prompt', 'promptsyra' ); ?></span>
			</button>

			<a href="<?php echo esc_url( $chatgpt_url ); ?>" target="_blank" rel="noopener noreferrer" class="ps-btn-ai-tool az-btn-ai-tool">
				<span>ChatGPT</span>
				<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
					<path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
					<polyline points="15 3 21 3 21 9"></polyline>
					<line x1="10" y1="14" x2="21" y2="3"></line>
				</svg>
			</a>

			<a href="<?php echo esc_url( $gemini_url ); ?>" target="_blank" rel="noopener noreferrer" class="ps-btn-ai-tool az-btn-ai-tool">
				<span>Gemini</span>
				<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
					<path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
					<polyline points="15 3 21 3 21 9"></polyline>
					<line x1="10" y1="14" x2="21" y2="3"></line>
				</svg>
			</a>

			<a href="<?php echo esc_url( $grok_url ); ?>" target="_blank" rel="noopener noreferrer" class="ps-btn-ai-tool az-btn-ai-tool">
				<span>Grok</span>
				<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
					<path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
					<polyline points="15 3 21 3 21 9"></polyline>
					<line x1="10" y1="14" x2="21" y2="3"></line>
				</svg>
			</a>
		</div>
	</div>
	<?php
	return (string) ob_get_clean();
}

/**
 * Shortcode handler: [promptsyra_prompt label="AI Photo Prompt"]Prompt text...[/promptsyra_prompt]
 * Also supports `prompt="..."` attribute for self-closing usage.
 *
 * @param array<string, mixed> $atts    Shortcode attributes.
 * @param string|null          $content Enclosed prompt text.
 * @return string
 */
function promptsyra_prompt_shortcode_handler( $atts, $content = null ) {
	$parsed = shortcode_atts(
		array(
			'label'  => __( 'AI Photo Prompt', 'promptsyra' ),
			'prompt' => '',
		),
		$atts,
		'promptsyra_prompt'
	);

	$text = ! empty( $content ) ? $content : $parsed['prompt'];
	return promptsyra_render_prompt_card_html( (string) $text, (string) $parsed['label'] );
}
add_shortcode( 'promptsyra_prompt', 'promptsyra_prompt_shortcode_handler' );
add_shortcode( 'ai_prompt_box', 'promptsyra_prompt_shortcode_handler' );

/**
 * Register native Gutenberg dynamic block `promptsyra/ai-prompt-box` so authors can add
 * AI Prompt Cards directly from the WordPress Block Editor without extra plugins.
 */
function promptsyra_register_prompt_box_block() {
	if ( ! function_exists( 'register_block_type' ) ) {
		return;
	}

	register_block_type(
		'promptsyra/ai-prompt-box',
		array(
			'attributes'      => array(
				'promptText' => array(
					'type'    => 'string',
					'default' => '',
				),
				'badgeLabel' => array(
					'type'    => 'string',
					'default' => 'AI Photo Prompt',
				),
			),
			'render_callback' => function ( $attributes, $content ) {
				$prompt_text = ! empty( $attributes['promptText'] ) ? (string) $attributes['promptText'] : (string) $content;
				$badge_label = ! empty( $attributes['badgeLabel'] ) ? (string) $attributes['badgeLabel'] : __( 'AI Photo Prompt', 'promptsyra' );
				return promptsyra_render_prompt_card_html( $prompt_text, $badge_label );
			},
		)
	);
}
add_action( 'init', 'promptsyra_register_prompt_box_block' );

/**
 * Enqueue lightweight Block Editor UI for `promptsyra/ai-prompt-box` (zero external build step needed).
 */
function promptsyra_enqueue_prompt_block_editor_script() {
	wp_register_script(
		'promptsyra-prompt-block-editor',
		false,
		array( 'wp-blocks', 'wp-element', 'wp-components', 'wp-block-editor' ),
		PROMPTSYRA_VERSION,
		true
	);
	wp_enqueue_script( 'promptsyra-prompt-block-editor' );

	$inline_js = <<<'JS'
( function ( wp ) {
	if ( ! wp || ! wp.blocks || ! wp.element || ! wp.components ) {
		return;
	}
	var el = wp.element.createElement;
	var TextareaControl = wp.components.TextareaControl;
	var TextControl = wp.components.TextControl;
	var useBlockProps = wp.blockEditor ? wp.blockEditor.useBlockProps : function () { return {}; };

	wp.blocks.registerBlockType( 'promptsyra/ai-prompt-box', {
		title: 'PromptSyra AI Prompt Box',
		description: 'Interactive AI prompt card with View Prompt toggle, Copy Prompt button, and ChatGPT / Gemini / Grok quick-launch links.',
		icon: 'superhero-alt',
		category: 'widgets',
		attributes: {
			promptText: { type: 'string', default: '' },
			badgeLabel: { type: 'string', default: 'AI Photo Prompt' }
		},
		edit: function ( props ) {
			var blockProps = useBlockProps( {
				style: {
					border: '1px solid #e2e8f0',
					borderRadius: '14px',
					padding: '18px',
					background: '#ffffff'
				}
			} );
			return el(
				'div',
				blockProps,
				el( TextControl, {
					label: 'Prompt Badge Label',
					value: props.attributes.badgeLabel,
					onChange: function ( val ) { props.setAttributes( { badgeLabel: val } ); }
				} ),
				el( TextareaControl, {
					label: 'Prompt Text (Copied cleanly when readers click Copy Prompt)',
					rows: 5,
					placeholder: 'Paste or write the complete AI generation prompt here...',
					value: props.attributes.promptText,
					onChange: function ( val ) { props.setAttributes( { promptText: val } ); }
				} )
			);
		},
		save: function () {
			return null;
		}
	} );
} )( window.wp );
JS;

	wp_add_inline_script( 'promptsyra-prompt-block-editor', $inline_js );
}
add_action( 'enqueue_block_editor_assets', 'promptsyra_enqueue_prompt_block_editor_script' );

/**
 * Inject a clean, collapsible Table of Contents before the first <h2> on single posts ONLY when:
 * - Viewing a single post in the main query (`is_singular('post') && in_the_loop() && is_main_query()`)
 * - No third-party TOC container (`id="toc_container"`, `ez-toc-container`, `rank-math-toc`) is already in `$content`
 * - At least 3 `<h2>` / `<h3>` headings exist in the post body (so short posts like /hello-world/ never show an empty TOC).
 *
 * @param string $content Rendered post HTML.
 * @return string
 */
function promptsyra_conditional_auto_toc( $content ) {
	if ( ! is_singular( 'post' ) || ! in_the_loop() || ! is_main_query() ) {
		return $content;
	}

	if (
		false !== strpos( $content, 'id="toc_container"' ) ||
		false !== strpos( $content, 'ez-toc-container' ) ||
		false !== strpos( $content, 'rank-math-toc' ) ||
		false !== strpos( $content, 'ps-toc-container' )
	) {
		return $content;
	}

	if ( ! preg_match_all( '/<h([23])([^>]*)>(.*?)<\/h\1>/is', $content, $matches, PREG_SET_ORDER ) ) {
		return $content;
	}

	if ( count( $matches ) < 3 ) {
		return $content;
	}

	$used_ids  = array();
	$toc_items = array();
	$h2_index  = 0;
	$h3_index  = 0;

	foreach ( $matches as $match ) {
		$level      = (int) $match[1];
		$attrs      = $match[2];
		$inner_html = $match[3];
		$plain_text = trim( wp_strip_all_tags( html_entity_decode( $inner_html, ENT_QUOTES, 'UTF-8' ) ) );

		if ( '' === $plain_text ) {
			continue;
		}

		if ( preg_match( '/\bid=["\']([^"\']+)["\']/i', $attrs, $id_match ) ) {
			$anchor_id = $id_match[1];
		} else {
			$base_id   = sanitize_title( $plain_text );
			$base_id   = '' !== $base_id ? $base_id : 'section';
			$anchor_id = $base_id;
			$suffix    = 2;
			while ( in_array( $anchor_id, $used_ids, true ) ) {
				$anchor_id = $base_id . '-' . $suffix;
				++$suffix;
			}

			$new_heading = sprintf( '<h%1$d%2$s id="%3$s">%4$s</h%1$d>', $level, $attrs, esc_attr( $anchor_id ), $inner_html );
			$pos         = strpos( $content, $match[0] );
			if ( false !== $pos ) {
				$content = substr_replace( $content, $new_heading, $pos, strlen( $match[0] ) );
			}
		}

		$used_ids[] = $anchor_id;

		if ( 2 === $level ) {
			++$h2_index;
			$h3_index = 0;
			$number   = (string) $h2_index;
		} else {
			if ( 0 === $h2_index ) {
				$h2_index = 1;
			}
			++$h3_index;
			$number = $h2_index . '.' . $h3_index;
		}

		$toc_items[] = array(
			'level'  => $level,
			'id'     => $anchor_id,
			'number' => $number,
			'title'  => $plain_text,
		);
	}

	if ( count( $toc_items ) < 3 ) {
		return $content;
	}

	ob_start();
	?>
	<nav class="ps-toc-container" aria-label="<?php esc_attr_e( 'Table of Contents', 'promptsyra' ); ?>" data-ps-toc>
		<div class="ps-toc-header">
			<span class="ps-toc-title">
				<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
					<line x1="8" y1="6" x2="21" y2="6"></line>
					<line x1="8" y1="12" x2="21" y2="12"></line>
					<line x1="8" y1="18" x2="21" y2="18"></line>
					<line x1="3" y1="6" x2="3.01" y2="6"></line>
					<line x1="3" y1="12" x2="3.01" y2="12"></line>
					<line x1="3" y1="18" x2="3.01" y2="18"></line>
				</svg>
				<span><?php esc_html_e( 'Table of Contents', 'promptsyra' ); ?></span>
			</span>
			<button type="button" class="ps-toc-toggle" data-ps-toc-toggle aria-expanded="true">
				<span data-ps-toc-toggle-label><?php esc_html_e( 'Hide', 'promptsyra' ); ?></span>
			</button>
		</div>
		<ul class="ps-toc-list" data-ps-toc-list>
			<?php foreach ( $toc_items as $item ) : ?>
				<li class="ps-toc-item ps-toc-level-<?php echo (int) $item['level']; ?>">
					<a href="#<?php echo esc_attr( $item['id'] ); ?>">
						<span class="ps-toc-num"><?php echo esc_html( $item['number'] ); ?></span>
						<span><?php echo esc_html( $item['title'] ); ?></span>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</nav>
	<?php
	$toc_html = (string) ob_get_clean();

	$first_h2_pos = stripos( $content, '<h2' );
	if ( false !== $first_h2_pos ) {
		return substr_replace( $content, $toc_html, $first_h2_pos, 0 );
	}

	return $toc_html . $content;
}
add_filter( 'the_content', 'promptsyra_conditional_auto_toc', 12 );
