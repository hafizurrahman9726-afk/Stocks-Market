<?php
/**
 * PromptSyra Theme Functions and Definitions
 *
 * Versioning Scheme:
 * - Legacy Theme: AIPromptZyra 1.1.4
 * - Rebranded Release: PromptSyra 2.0.0 (MAJOR increment for complete brand & homepage overhaul)
 * - Single-Post Article Rebuild: PromptSyra 2.1.0 (MINOR increment adding 2-column reference single-post layout, interactive AI Prompt Cards, conditional TOC, and sticky sidebar)
 * - Single-Post Final UI Fix: PromptSyra 2.2.0 (Removed single-post breadcrumb row & category pill; added native comments.php "Join the Conversation" redesign)
 * - Authors, About, Contact & Legal Pages: PromptSyra 2.3.0 (Dynamic 3-author profiles with verified WebP portraits, About Us, functional Contact Us form to contact@promptsyra.com, Privacy Policy, Disclaimer, and Terms & Conditions)
 * - Master Homepage Four-Section UI Upgrade: PromptSyra 2.4.0 (Hero supporting CTA & benefits bar, tailored 3-category discovery triptych with live WP slug alias support, connected 3-step prompt workflow, and interactive Why Choose PromptSyra? specimen & customization ledger)
 *
 * @package PromptSyra
 * @version 2.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'PROMPTSYRA_VERSION', '2.4.1' );
define( 'PROMPTSYRA_DIR', get_stylesheet_directory() );
define( 'PROMPTSYRA_URI', get_stylesheet_directory_uri() );

/**
 * Approved Primary Categories Specification.
 *
 * @return array<string, array<string, string>>
 */
function promptsyra_get_approved_categories() {
	return array(
		'ai-photo-prompts'     => array(
			'name'        => __( 'AI Photo Prompts', 'promptsyra' ),
			'slug'        => 'ai-photo-prompts',
			'aliases'     => array( 'ai-photo-prompts' ),
			'description' => __( 'Portrait framing, studio and natural lighting setups, and realistic creative photography prompts.', 'promptsyra' ),
		),
		'trending-prompts'     => array(
			'name'        => __( 'Trending Prompts', 'promptsyra' ),
			'slug'        => 'trending-prompts',
			'aliases'     => array( 'trending-prompts' ),
			'description' => __( 'Seasonal photography concepts, emerging visual styles, and timely creative scene ideas.', 'promptsyra' ),
		),
		'social-media-prompts' => array(
			'name'        => __( 'Social Media Prompts', 'promptsyra' ),
			'slug'        => 'social-media-prompts',
			'aliases'     => array( 'social-media-prompts', 'ai-social-media-prompts' ),
			'description' => __( 'Vertical 9:16 stories, reel covers, creator thumbnails, and feed-ready visual compositions.', 'promptsyra' ),
		),
	);
}

/**
 * Designated Editorial Author Profiles Specification.
 *
 * @return array<string, array<string, string>>
 */
function promptsyra_get_designated_authors_spec() {
	return array(
		'syra'  => array(
			'display_name' => 'Syra Ansari',
			'first_name'   => 'Syra',
			'last_name'    => 'Ansari',
			'slug_hint'    => 'syra-ansari',
			'mod_key'      => 'promptsyra_author_syra_id',
			'role_label'   => __( 'Author and Editorial Contributor', 'promptsyra' ),
			'photo_url'    => 'https://promptsyra.com/wp-content/uploads/2026/10/Syra-Ansari.webp',
			'default_bio'  => __( 'Syra Ansari contributes AI photo prompt collections, portrait composition examples, and editorial customization guides published on PromptSyra.com, focusing on clear structure and practical adaptability across modern image tools.', 'promptsyra' ),
		),
		'arjun' => array(
			'display_name' => 'Arjun Patel',
			'first_name'   => 'Arjun',
			'last_name'    => 'Patel',
			'slug_hint'    => 'arjun-patel',
			'mod_key'      => 'promptsyra_author_arjun_id',
			'role_label'   => __( 'AI Photo Prompt Contributor', 'promptsyra' ),
			'photo_url'    => 'https://promptsyra.com/wp-content/uploads/2026/10/Arjun-Patel.webp',
			'default_bio'  => __( 'Arjun Patel prepares structured AI photo prompt ideas, lighting and framing variations, and visual style notes for PromptSyra.com readers looking to customize portrait and creative scene concepts.', 'promptsyra' ),
		),
		'neha'  => array(
			'display_name' => 'Neha Joshi',
			'first_name'   => 'Neha',
			'last_name'    => 'Joshi',
			'slug_hint'    => 'neha-joshi',
			'mod_key'      => 'promptsyra_author_neha_id',
			'role_label'   => __( 'Creative Content Contributor', 'promptsyra' ),
			'photo_url'    => 'https://promptsyra.com/wp-content/uploads/2026/10/Neha-Joshi.webp',
			'default_bio'  => __( 'Neha Joshi contributes trending visual prompt topics, social media image concepts, and step-by-step adaptation tips on PromptSyra.com to help creators tailor prompt wording to their own projects.', 'promptsyra' ),
		),
	);
}

/**
 * Theme Setup: menus, theme support, and brand capabilities.
 */
function promptsyra_setup() {
	load_theme_textdomain( 'promptsyra', PROMPTSYRA_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 72,
			'width'       => 260,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);

	register_nav_menus(
		array(
			'promptsyra_primary'      => __( 'PromptSyra Primary Header Navigation', 'promptsyra' ),
			'promptsyra_tools'        => __( 'PromptSyra Header Tools Dropdown (Optional — Shown Only When Assigned)', 'promptsyra' ),
			'promptsyra_footer_quick' => __( 'PromptSyra Footer Quick Links', 'promptsyra' ),
			'promptsyra_footer_legal' => __( 'PromptSyra Footer Legal Links', 'promptsyra' ),
		)
	);
}
add_action( 'after_setup_theme', 'promptsyra_setup', 20 );

/**
 * Enqueue parent stylesheet safely (when running as a child theme) and
 * load strictly-scoped PromptSyra assets without altering single-post articles.
 */
function promptsyra_enqueue_assets() {
	// If running as a child theme, preserve parent stylesheet load order when parent doesn't auto-enqueue.
	if ( is_child_theme() ) {
		$parent_style_path = get_template_directory() . '/style.css';
		if ( file_exists( $parent_style_path ) && ! wp_style_is( get_template() . '-style', 'enqueued' ) ) {
			wp_enqueue_style(
				'promptsyra-parent-style',
				get_template_directory_uri() . '/style.css',
				array(),
				wp_get_theme( get_template() )->get( 'Version' )
			);
		}
	}

	// In Child Theme mode, if "Preserve Legacy Chrome on Single Posts" is enabled, skip new CSS/JS so parent single.php renders identically.
	$preserve_legacy_single = (bool) get_theme_mod( 'promptsyra_preserve_legacy_single_chrome', true );
	if ( is_child_theme() && is_singular( 'post' ) && $preserve_legacy_single ) {
		return;
	}

	// Load Google Fonts with sensible display=swap fallback.
	wp_enqueue_style(
		'promptsyra-fonts',
		'https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap',
		array(),
		null
	);

	// Strictly scoped stylesheet (.ps-* classes only; zero bare HTML tag selectors).
	wp_enqueue_style(
		'promptsyra-scoped-ui',
		PROMPTSYRA_URI . '/assets/css/promptsyra-home.css',
		array(),
		PROMPTSYRA_VERSION
	);

	wp_enqueue_script(
		'promptsyra-scoped-ui',
		PROMPTSYRA_URI . '/assets/js/promptsyra-home.js',
		array(),
		PROMPTSYRA_VERSION,
		true
	);

	// Load single-post layout, prompt-card, TOC, and sticky-sidebar assets strictly on single posts (never on front-page.php).
	if ( is_singular( 'post' ) ) {
		wp_enqueue_style(
			'promptsyra-single-post',
			PROMPTSYRA_URI . '/assets/css/promptsyra-single.css',
			array( 'promptsyra-scoped-ui' ),
			PROMPTSYRA_VERSION
		);

		wp_enqueue_script(
			'promptsyra-single-post',
			PROMPTSYRA_URI . '/assets/js/promptsyra-single.js',
			array(),
			PROMPTSYRA_VERSION,
			true
		);

		if ( comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}
	}

	// Load editorial, contact, legal, and author archive typography & layout styles on pages and author archives.
	if ( is_page() || is_author() || is_404() ) {
		wp_enqueue_style(
			'promptsyra-pages',
			PROMPTSYRA_URI . '/assets/css/promptsyra-pages.css',
			array( 'promptsyra-scoped-ui' ),
			PROMPTSYRA_VERSION
		);
	}
}
add_action( 'wp_enqueue_scripts', 'promptsyra_enqueue_assets', 30 );

/**
 * Resolve an approved category object safely without creating broken links.
 *
 * @param string $slug Approved category slug.
 * @return array<string, mixed>
 */
function promptsyra_resolve_category_data( $slug ) {
	$approved = promptsyra_get_approved_categories();
	$fallback = isset( $approved[ $slug ] ) ? $approved[ $slug ] : array(
		'name'        => $slug,
		'slug'        => $slug,
		'aliases'     => array( $slug ),
		'description' => '',
	);

	$candidate_slugs = ! empty( $fallback['aliases'] ) && is_array( $fallback['aliases'] )
		? $fallback['aliases']
		: array( $slug );

	foreach ( $candidate_slugs as $candidate_slug ) {
		$term = get_term_by( 'slug', $candidate_slug, 'category' );
		if ( $term && ! is_wp_error( $term ) ) {
			$link = get_term_link( $term );
			return array(
				'exists'        => true,
				'term_id'       => (int) $term->term_id,
				'name'          => $fallback['name'],
				'wp_term_name'  => $term->name,
				'slug'          => $term->slug,
				'canonical_key' => $slug,
				'description'   => ! empty( $term->description ) ? $term->description : $fallback['description'],
				'count'         => (int) $term->count,
				'url'           => ! is_wp_error( $link ) ? $link : home_url( '/' ),
			);
		}
	}

	return array(
		'exists'        => false,
		'term_id'       => 0,
		'name'          => $fallback['name'],
		'wp_term_name'  => $fallback['name'],
		'slug'          => $fallback['slug'],
		'canonical_key' => $slug,
		'description'   => $fallback['description'],
		'count'         => 0,
		'url'           => '',
	);
}

/**
 * Resolve a published WordPress page URL by checking candidate slugs.
 * Never returns a broken 404 URL; returns empty string if not published.
 *
 * @param string[] $candidate_slugs Possible page slugs.
 * @return string
 */
function promptsyra_get_verified_page_url( array $candidate_slugs ) {
	foreach ( $candidate_slugs as $slug ) {
		$page = get_page_by_path( sanitize_title( $slug ) );
		if ( $page instanceof WP_Post && 'publish' === $page->post_status ) {
			$permalink = get_permalink( $page->ID );
			if ( $permalink ) {
				return $permalink;
			}
		}
	}
	return '';
}

/**
 * Resolve real WordPress User data for one of the 3 designated editorial profiles.
 * Never fabricates biographies, credentials, or photos.
 *
 * @param string $author_key 'syra' | 'arjun' | 'neha'.
 * @return array<string, mixed>
 */
function promptsyra_resolve_designated_author( $author_key ) {
	$specs = promptsyra_get_designated_authors_spec();
	if ( ! isset( $specs[ $author_key ] ) ) {
		return array( 'found' => false );
	}

	$spec    = $specs[ $author_key ];
	$user    = false;
	$mod_uid = absint( get_theme_mod( $spec['mod_key'], 0 ) );

	if ( $mod_uid > 0 ) {
		$user = get_userdata( $mod_uid );
	}

	if ( ! $user ) {
		$user = get_user_by( 'slug', $spec['slug_hint'] );
	}

	if ( ! $user ) {
		$matching_users = get_users(
			array(
				'search'         => $spec['display_name'],
				'search_columns' => array( 'display_name', 'user_login', 'user_nicename' ),
				'number'         => 1,
			)
		);
		if ( ! empty( $matching_users ) ) {
			$user = $matching_users[0];
		}
	}

	if ( ! ( $user instanceof WP_User ) ) {
		return array(
			'found'            => false,
			'user_id'          => 0,
			'display_name'     => $spec['display_name'],
			'role_label'       => $spec['role_label'],
			'bio'              => $spec['default_bio'],
			'default_bio'      => $spec['default_bio'],
			'has_custom_photo' => ! empty( $spec['photo_url'] ),
			'avatar_url'       => $spec['photo_url'],
			'archive_url'      => '',
			'edit_url'         => current_user_can( 'create_users' ) ? admin_url( 'themes.php?page=promptsyra-setup#ps-sync-panel' ) : '',
		);
	}

	$bio              = trim( (string) get_the_author_meta( 'description', $user->ID ) );
	$custom_role      = trim( (string) get_user_meta( $user->ID, 'promptsyra_editorial_role', true ) );
	$custom_photo_url = trim( (string) get_user_meta( $user->ID, 'promptsyra_profile_photo_url', true ) );
	if ( empty( $custom_photo_url ) && ! empty( $spec['photo_url'] ) ) {
		$custom_photo_url = $spec['photo_url'];
	}
	$avatar_url  = ! empty( $custom_photo_url ) ? $custom_photo_url : get_avatar_url( $user->ID, array( 'size' => 192 ) );
	$archive_url = get_author_posts_url( $user->ID );
	$edit_url    = current_user_can( 'edit_user', $user->ID ) ? get_edit_user_link( $user->ID ) : '';

	return array(
		'found'            => true,
		'user_id'          => (int) $user->ID,
		'display_name'     => $user->display_name ? $user->display_name : $spec['display_name'],
		'role_label'       => ! empty( $custom_role ) ? $custom_role : $spec['role_label'],
		'bio'              => ! empty( $bio ) ? $bio : $spec['default_bio'],
		'default_bio'      => $spec['default_bio'],
		'has_custom_photo' => ! empty( $custom_photo_url ),
		'avatar_url'       => $avatar_url ? $avatar_url : '',
		'archive_url'      => $archive_url ? $archive_url : '',
		'edit_url'         => $edit_url,
	);
}

/**
 * Resolve any WordPress user's editorial role label cleanly.
 *
 * @param int $user_id WordPress User ID.
 * @return string
 */
function promptsyra_get_user_editorial_role( $user_id ) {
	$user_id = absint( $user_id );
	if ( $user_id <= 0 ) {
		return __( 'Author', 'promptsyra' );
	}

	$saved_role = trim( (string) get_user_meta( $user_id, 'promptsyra_editorial_role', true ) );
	if ( ! empty( $saved_role ) ) {
		return $saved_role;
	}

	$user = get_userdata( $user_id );
	if ( $user instanceof WP_User ) {
		foreach ( promptsyra_get_designated_authors_spec() as $spec ) {
			if ( $user->user_nicename === $spec['slug_hint'] || $user->user_login === $spec['slug_hint'] ) {
				return $spec['role_label'];
			}
		}
	}

	return __( 'Author', 'promptsyra' );
}

/**
 * Filter WordPress get_avatar_data so any author with a configured Media Library
 * portrait URL (promptsyra_profile_photo_url) uses their local WebP portrait everywhere
 * without hitting external Gravatar requests.
 *
 * @param array<string, mixed> $args        Avatar data arguments.
 * @param mixed                $id_or_email User ID, WP_User, WP_Post, WP_Comment, or email.
 * @return array<string, mixed>
 */
function promptsyra_filter_user_avatar_data( $args, $id_or_email ) {
	$user_id = 0;

	if ( is_numeric( $id_or_email ) ) {
		$user_id = (int) $id_or_email;
	} elseif ( $id_or_email instanceof WP_User ) {
		$user_id = (int) $id_or_email->ID;
	} elseif ( $id_or_email instanceof WP_Post ) {
		$user_id = (int) $id_or_email->post_author;
	} elseif ( $id_or_email instanceof WP_Comment && ! empty( $id_or_email->user_id ) ) {
		$user_id = (int) $id_or_email->user_id;
	}

	if ( $user_id > 0 ) {
		$photo_url = trim( (string) get_user_meta( $user_id, 'promptsyra_profile_photo_url', true ) );
		if ( empty( $photo_url ) ) {
			$u = get_userdata( $user_id );
			if ( $u instanceof WP_User ) {
				foreach ( promptsyra_get_designated_authors_spec() as $spec ) {
					if ( $u->user_nicename === $spec['slug_hint'] || $u->user_login === $spec['slug_hint'] ) {
						$photo_url = $spec['photo_url'];
						break;
					}
				}
			}
		}
		if ( ! empty( $photo_url ) ) {
			$args['url']          = esc_url_raw( $photo_url );
			$args['found_avatar'] = true;
		}
	}

	return $args;
}
add_filter( 'pre_get_avatar_data', 'promptsyra_filter_user_avatar_data', 10, 2 );

/**
 * Output safe social sharing fallback meta on the front page ONLY if no SEO plugin is active,
 * preventing duplicate og:title / meta description / canonical conflicts.
 */
function promptsyra_has_seo_plugin() {
	return (
		defined( 'WPSEO_VERSION' ) ||
		defined( 'RANK_MATH_VERSION' ) ||
		defined( 'AIOSEO_VERSION' ) ||
		defined( 'SEOPRESS_VERSION' ) ||
		defined( 'THE_SEO_FRAMEWORK_PRESENT' )
	);
}

function promptsyra_front_social_meta_fallback() {
	if ( ! is_front_page() || promptsyra_has_seo_plugin() ) {
		return;
	}

	$site_name = get_bloginfo( 'name' );
	if ( empty( $site_name ) || false !== stripos( $site_name, 'aipromptzyra' ) ) {
		$site_name = 'PromptSyra.com';
	}

	$desc = get_theme_mod(
		'promptsyra_hero_description',
		__( 'Discover useful AI photo prompts for portraits, creative photography, social media visuals, and more.', 'promptsyra' )
	);
	?>
	<meta name="description" content="<?php echo esc_attr( wp_strip_all_tags( $desc ) ); ?>" />
	<meta property="og:site_name" content="<?php echo esc_attr( $site_name ); ?>" />
	<meta property="og:title" content="<?php echo esc_attr( $site_name . ' — AI Photo Prompts & Creative Ideas' ); ?>" />
	<meta property="og:description" content="<?php echo esc_attr( wp_strip_all_tags( $desc ) ); ?>" />
	<meta property="og:type" content="website" />
	<meta property="og:url" content="<?php echo esc_url( home_url( '/' ) ); ?>" />
	<?php
}
add_action( 'wp_head', 'promptsyra_front_social_meta_fallback', 5 );


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

require_once PROMPTSYRA_DIR . '/inc/customizer.php';
require_once PROMPTSYRA_DIR . '/inc/contact-form.php';
require_once PROMPTSYRA_DIR . '/inc/essential-pages-and-authors.php';
require_once PROMPTSYRA_DIR . '/inc/admin-setup.php';
require_once PROMPTSYRA_DIR . '/inc/single-post-features.php';
