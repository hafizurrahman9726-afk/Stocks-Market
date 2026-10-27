<?php
/**
 * PromptSyra WordPress Customizer Controls
 *
 * Makes all homepage sections, hero copy, CTA destinations, social channels,
 * FAQ items, designated author mappings, and single-post safety settings
 * editable natively through Appearance -> Customize.
 *
 * @package PromptSyra
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Sanitize checkbox values.
 *
 * @param mixed $checked Raw input.
 * @return bool
 */
function promptsyra_sanitize_checkbox( $checked ) {
	return ( isset( $checked ) && ( true === $checked || '1' === $checked || 1 === $checked ) );
}

/**
 * Default accurate FAQ entries (editable in Customizer).
 *
 * @return array<int, array<string, string>>
 */
function promptsyra_get_default_faqs() {
	return array(
		1 => array(
			'q' => __( 'What is PromptSyra.com?', 'promptsyra' ),
			'a' => __( 'PromptSyra.com is an editorial library of structured AI photo prompts covering portraits, creative studio photography, and social media visuals, paired with practical customization notes.', 'promptsyra' ),
		),
		2 => array(
			'q' => __( 'How do I copy and use prompts from PromptSyra?', 'promptsyra' ),
			'a' => __( 'Open any prompt article, review the framing and lighting notes, use the copy button (or select the prompt text directly), and paste it into your preferred image-generation tool.', 'promptsyra' ),
		),
		3 => array(
			'q' => __( 'Which image-generation tools work with these photo prompts?', 'promptsyra' ),
			'a' => __( 'Our prompts are written in clear natural language suitable for modern text-to-image tools that accept descriptive photography prompts. Because each platform interprets lighting, aspect ratios, and styles differently, results will vary by tool and model version.', 'promptsyra' ),
		),
		4 => array(
			'q' => __( 'How can I customize a photo prompt for my own concept?', 'promptsyra' ),
			'a' => __( 'Start with the base prompt structure and replace specific details such as outfit colors, camera focal length (for example 35mm vs. 85mm), background setting, or time-of-day lighting while keeping the composition clear.', 'promptsyra' ),
		),
		5 => array(
			'q' => __( 'Are the prompts on PromptSyra free to browse and copy?', 'promptsyra' ),
			'a' => __( 'Yes. Visitors can browse our published categories, read the customization guides, and copy prompt text directly from articles without registering an account.', 'promptsyra' ),
		),
		6 => array(
			'q' => __( 'Tips for getting clearer, more natural-looking AI portrait results?', 'promptsyra' ),
			'a' => __( 'Keep subject descriptions concise, specify a realistic light source (such as soft window light or golden-hour rim light), avoid conflicting camera angles in the same prompt, and adjust aspect ratios inside your generator settings.', 'promptsyra' ),
		),
	);
}

/**
 * Register Customizer panels, sections, settings, and controls.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager instance.
 */
function promptsyra_customize_register( WP_Customize_Manager $wp_customize ) {
	$wp_customize->add_panel(
		'promptsyra_panel',
		array(
			'title'       => __( 'PromptSyra Brand & Homepage', 'promptsyra' ),
			'description' => __( 'Configure the PromptSyra.com homepage redesign, social links, FAQs, and designated author profiles.', 'promptsyra' ),
			'priority'    => 25,
		)
	);

	// 1. Single-Post Compatibility & Section Visibility.
	$wp_customize->add_section(
		'promptsyra_visibility_section',
		array(
			'title' => __( 'Section Visibility & Single-Post Guard', 'promptsyra' ),
			'panel' => 'promptsyra_panel',
		)
	);

	$wp_customize->add_setting(
		'promptsyra_preserve_legacy_single_chrome',
		array(
			'default'           => true,
			'sanitize_callback' => 'promptsyra_sanitize_checkbox',
		)
	);
	$wp_customize->add_control(
		'promptsyra_preserve_legacy_single_chrome',
		array(
			'type'        => 'checkbox',
			'section'     => 'promptsyra_visibility_section',
			'label'       => __( 'Keep original parent header/footer on Single Posts (single.php)', 'promptsyra' ),
			'description' => __( 'Recommended: Guarantees zero layout or CSS changes on existing article pages.', 'promptsyra' ),
		)
	);

	$visibility_toggles = array(
		'promptsyra_show_categories' => __( 'Show Main 3-Category Bar', 'promptsyra' ),
		'promptsyra_show_featured'   => __( 'Show Featured AI Prompts Section', 'promptsyra' ),
		'promptsyra_show_howto'      => __( 'Show 3-Step How to Use Guide', 'promptsyra' ),
		'promptsyra_show_editorial'  => __( 'Show Why Choose PromptSyra Editorial Section', 'promptsyra' ),
		'promptsyra_show_community'  => __( 'Show Community Section (when social URLs exist)', 'promptsyra' ),
		'promptsyra_show_faq'        => __( 'Show Frequently Asked Questions Section', 'promptsyra' ),
		'promptsyra_show_author'     => __( 'Show Syra Ansari & Editorial Team Feature', 'promptsyra' ),
	);

	foreach ( $visibility_toggles as $setting_id => $label ) {
		$wp_customize->add_setting(
			$setting_id,
			array(
				'default'           => true,
				'sanitize_callback' => 'promptsyra_sanitize_checkbox',
			)
		);
		$wp_customize->add_control(
			$setting_id,
			array(
				'type'    => 'checkbox',
				'section' => 'promptsyra_visibility_section',
				'label'   => $label,
			)
		);
	}

	// 2. Hero Section Controls.
	$wp_customize->add_section(
		'promptsyra_hero_section',
		array(
			'title' => __( 'Hero Section & CTA', 'promptsyra' ),
			'panel' => 'promptsyra_panel',
		)
	);

	$hero_text_fields = array(
		'promptsyra_hero_eyebrow'            => array(
			'label'   => __( 'Hero Eyebrow', 'promptsyra' ),
			'default' => __( 'AI Photo Prompts & Creative Ideas', 'promptsyra' ),
		),
		'promptsyra_hero_heading'            => array(
			'label'   => __( 'Hero Main Heading', 'promptsyra' ),
			'default' => __( 'Turn Your Ideas Into Stunning AI Creations', 'promptsyra' ),
		),
		'promptsyra_hero_search_placeholder' => array(
			'label'   => __( 'Search Input Placeholder', 'promptsyra' ),
			'default' => __( 'Search photo prompts (portraits, studio lighting, social media...)', 'promptsyra' ),
		),
		'promptsyra_hero_cta_label'          => array(
			'label'   => __( 'Primary CTA Label', 'promptsyra' ),
			'default' => __( 'Explore Prompts', 'promptsyra' ),
		),
		'promptsyra_utility_point_1'         => array(
			'label'   => __( 'Utility Point 1 (Accurate only)', 'promptsyra' ),
			'default' => __( 'Structured, Copy-Ready Text', 'promptsyra' ),
		),
		'promptsyra_utility_point_2'         => array(
			'label'   => __( 'Utility Point 2 (Accurate only)', 'promptsyra' ),
			'default' => __( 'Portrait, Creative & Social Styles', 'promptsyra' ),
		),
		'promptsyra_utility_point_3'         => array(
			'label'   => __( 'Utility Point 3 (Accurate only)', 'promptsyra' ),
			'default' => __( 'Practical Customization Notes', 'promptsyra' ),
		),
	);

	foreach ( $hero_text_fields as $id => $cfg ) {
		$wp_customize->add_setting(
			$id,
			array(
				'default'           => $cfg['default'],
				'sanitize_callback' => 'sanitize_text_field',
			)
		);
		$wp_customize->add_control(
			$id,
			array(
				'type'    => 'text',
				'section' => 'promptsyra_hero_section',
				'label'   => $cfg['label'],
			)
		);
	}

	$wp_customize->add_setting(
		'promptsyra_hero_description',
		array(
			'default'           => __( 'Discover useful AI photo prompts for portraits, creative photography, social media visuals, and more.', 'promptsyra' ),
			'sanitize_callback' => 'sanitize_textarea_field',
		)
	);
	$wp_customize->add_control(
		'promptsyra_hero_description',
		array(
			'type'    => 'textarea',
			'section' => 'promptsyra_hero_section',
			'label'   => __( 'Hero Description', 'promptsyra' ),
		)
	);

	$wp_customize->add_setting(
		'promptsyra_hero_cta_url',
		array(
			'default'           => '#featured-prompts',
			'sanitize_callback' => 'esc_url_raw',
		)
	);
	$wp_customize->add_control(
		'promptsyra_hero_cta_url',
		array(
			'type'        => 'url',
			'section'     => 'promptsyra_hero_section',
			'label'       => __( 'Explore Prompts CTA Destination URL', 'promptsyra' ),
			'description' => __( 'Leave blank or use #featured-prompts to jump to Featured AI Prompts.', 'promptsyra' ),
		)
	);

	for ( $img_idx = 1; $img_idx <= 3; $img_idx++ ) {
		$setting_key = 'promptsyra_hero_card_image_' . $img_idx;
		$wp_customize->add_setting(
			$setting_key,
			array(
				'default'           => 0,
				'sanitize_callback' => 'absint',
			)
		);
		$wp_customize->add_control(
			new WP_Customize_Media_Control(
				$wp_customize,
				$setting_key,
				array(
					'label'     => sprintf( __( 'Optional Hero Showcase Image %d', 'promptsyra' ), $img_idx ),
					'section'   => 'promptsyra_hero_section',
					'mime_type' => 'image',
				)
			)
		);
	}

	// 3. Featured Prompts Count.
	$wp_customize->add_section(
		'promptsyra_featured_section',
		array(
			'title' => __( 'Featured AI Prompts Query', 'promptsyra' ),
			'panel' => 'promptsyra_panel',
		)
	);

	$wp_customize->add_setting(
		'promptsyra_featured_posts_count',
		array(
			'default'           => 6,
			'sanitize_callback' => 'absint',
		)
	);
	$wp_customize->add_control(
		'promptsyra_featured_posts_count',
		array(
			'type'        => 'number',
			'section'     => 'promptsyra_featured_section',
			'label'       => __( 'Number of Published Posts per View', 'promptsyra' ),
			'input_attrs' => array(
				'min'  => 3,
				'max'  => 12,
				'step' => 1,
			),
		)
	);

	// 4. Official Social & Community Links (Empty by default — never invented).
	$wp_customize->add_section(
		'promptsyra_social_section',
		array(
			'title'       => __( 'Community & Official Social URLs', 'promptsyra' ),
			'description' => __( 'Only channels with a genuine URL entered below will appear on the homepage and footer. Leave empty to hide.', 'promptsyra' ),
			'panel'       => 'promptsyra_panel',
		)
	);

	$social_networks = array(
		'instagram' => __( 'Instagram Official Profile URL', 'promptsyra' ),
		'facebook'  => __( 'Facebook Official Page URL', 'promptsyra' ),
		'youtube'   => __( 'YouTube Official Channel URL', 'promptsyra' ),
		'telegram'  => __( 'Telegram Official Channel URL', 'promptsyra' ),
		'whatsapp'  => __( 'WhatsApp Official Channel URL', 'promptsyra' ),
		'x'         => __( 'X (Twitter) Official Profile URL', 'promptsyra' ),
	);

	foreach ( $social_networks as $net_key => $net_label ) {
		$wp_customize->add_setting(
			'promptsyra_social_' . $net_key,
			array(
				'default'           => '',
				'sanitize_callback' => 'esc_url_raw',
			)
		);
		$wp_customize->add_control(
			'promptsyra_social_' . $net_key,
			array(
				'type'    => 'url',
				'section' => 'promptsyra_social_section',
				'label'   => $net_label,
			)
		);
	}

	// 5. Designated Authors Mapping (Syra Ansari, Arjun Patel, Neha Joshi).
	$wp_customize->add_section(
		'promptsyra_authors_section',
		array(
			'title'       => __( 'Designated Authors (3 Profiles)', 'promptsyra' ),
			'description' => __( 'Map Syra Ansari, Arjun Patel, and Neha Joshi to their actual WordPress User accounts. Biographies and photos are read directly from Users -> Profile.', 'promptsyra' ),
			'panel'       => 'promptsyra_panel',
		)
	);

	$user_choices = array( 0 => __( '— Auto-detect by Name / Slug —', 'promptsyra' ) );
	$wp_users     = get_users(
		array(
			'fields'  => array( 'ID', 'display_name', 'user_login' ),
			'orderby' => 'display_name',
			'number'  => 200,
		)
	);
	foreach ( $wp_users as $u ) {
		$user_choices[ (int) $u->ID ] = sprintf( '%s (%s)', $u->display_name, $u->user_login );
	}

	foreach ( promptsyra_get_designated_authors_spec() as $author_spec ) {
		$wp_customize->add_setting(
			$author_spec['mod_key'],
			array(
				'default'           => 0,
				'sanitize_callback' => 'absint',
			)
		);
		$wp_customize->add_control(
			$author_spec['mod_key'],
			array(
				'type'    => 'select',
				'section' => 'promptsyra_authors_section',
				'label'   => sprintf( __( 'WordPress User for %s', 'promptsyra' ), $author_spec['display_name'] ),
				'choices' => $user_choices,
			)
		);
	}

	// 6. FAQ Accordion & Structured Data.
	$wp_customize->add_section(
		'promptsyra_faq_section',
		array(
			'title' => __( 'Homepage FAQ & Structured Data', 'promptsyra' ),
			'panel' => 'promptsyra_panel',
		)
	);

	$wp_customize->add_setting(
		'promptsyra_enable_faq_schema',
		array(
			'default'           => true,
			'sanitize_callback' => 'promptsyra_sanitize_checkbox',
		)
	);
	$wp_customize->add_control(
		'promptsyra_enable_faq_schema',
		array(
			'type'        => 'checkbox',
			'section'     => 'promptsyra_faq_section',
			'label'       => __( 'Output FAQPage JSON-LD when FAQ is visible', 'promptsyra' ),
			'description' => __( 'Disable only if your SEO plugin already injects FAQPage schema on the front page.', 'promptsyra' ),
		)
	);

	foreach ( promptsyra_get_default_faqs() as $idx => $faq_pair ) {
		$q_key = 'promptsyra_faq_q_' . $idx;
		$a_key = 'promptsyra_faq_a_' . $idx;

		$wp_customize->add_setting(
			$q_key,
			array(
				'default'           => $faq_pair['q'],
				'sanitize_callback' => 'sanitize_text_field',
			)
		);
		$wp_customize->add_control(
			$q_key,
			array(
				'type'    => 'text',
				'section' => 'promptsyra_faq_section',
				'label'   => sprintf( __( 'FAQ %d Question', 'promptsyra' ), $idx ),
			)
		);

		$wp_customize->add_setting(
			$a_key,
			array(
				'default'           => $faq_pair['a'],
				'sanitize_callback' => 'sanitize_textarea_field',
			)
		);
		$wp_customize->add_control(
			$a_key,
			array(
				'type'    => 'textarea',
				'section' => 'promptsyra_faq_section',
				'label'   => sprintf( __( 'FAQ %d Answer', 'promptsyra' ), $idx ),
			)
		);
	}

	// 7. Footer Description.
	$wp_customize->add_section(
		'promptsyra_footer_section',
		array(
			'title' => __( 'Footer Description', 'promptsyra' ),
			'panel' => 'promptsyra_panel',
		)
	);

	$wp_customize->add_setting(
		'promptsyra_footer_description',
		array(
			'default'           => __( 'PromptSyra.com curates copy-ready AI photo prompts, portrait lighting setups, and practical customization notes for visual creators.', 'promptsyra' ),
			'sanitize_callback' => 'sanitize_textarea_field',
		)
	);
	$wp_customize->add_control(
		'promptsyra_footer_description',
		array(
			'type'    => 'textarea',
			'section' => 'promptsyra_footer_section',
			'label'   => __( 'Footer Brand Summary', 'promptsyra' ),
		)
	);

	// 8. Single-Post Comment Form Copy ("Join the Conversation").
	$wp_customize->add_section(
		'promptsyra_comments_section',
		array(
			'title'       => __( 'Single-Post Comment Section', 'promptsyra' ),
			'description' => __( 'Customize the heading and supporting community message shown above the single-post comment form.', 'promptsyra' ),
			'panel'       => 'promptsyra_panel',
		)
	);

	$wp_customize->add_setting(
		'promptsyra_comment_heading',
		array(
			'default'           => __( 'Join the Conversation', 'promptsyra' ),
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		'promptsyra_comment_heading',
		array(
			'type'    => 'text',
			'section' => 'promptsyra_comments_section',
			'label'   => __( 'Comment Form Heading', 'promptsyra' ),
		)
	);

	$wp_customize->add_setting(
		'promptsyra_comment_subtext',
		array(
			'default'           => __( 'Have a question or suggestion? Share your thoughts with the PromptSyra community.', 'promptsyra' ),
			'sanitize_callback' => 'sanitize_textarea_field',
		)
	);
	$wp_customize->add_control(
		'promptsyra_comment_subtext',
		array(
			'type'    => 'textarea',
			'section' => 'promptsyra_comments_section',
			'label'   => __( 'Comment Form Supporting Text', 'promptsyra' ),
		)
	);

	// 9. Contact Us Form Integration.
	$wp_customize->add_section(
		'promptsyra_contact_section',
		array(
			'title'       => __( 'Contact Us Form Integration', 'promptsyra' ),
			'description' => __( 'By default, [promptsyra_contact_form] uses PromptSyra’s built-in WordPress contact form delivering messages to contact@promptsyra.com. If you prefer Contact Form 7, WPForms, or Fluent Forms later, paste its shortcode below.', 'promptsyra' ),
			'panel'       => 'promptsyra_panel',
		)
	);

	$wp_customize->add_setting(
		'promptsyra_custom_contact_shortcode',
		array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		'promptsyra_custom_contact_shortcode',
		array(
			'type'        => 'text',
			'section'     => 'promptsyra_contact_section',
			'label'       => __( 'Optional Form Plugin Shortcode Override', 'promptsyra' ),
			'description' => __( 'Leave blank to use the built-in WordPress form delivering to contact@promptsyra.com.', 'promptsyra' ),
		)
	);
}
add_action( 'customize_register', 'promptsyra_customize_register' );
