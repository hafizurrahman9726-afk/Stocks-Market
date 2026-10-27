<?php
/**
 * Plugin Name: PromptSyra Homepage & Brand Redesign (Single-Post Safe)
 * Plugin URI: https://promptsyra.com/
 * Description: Non-destructive PromptSyra.com homepage redesign, Customizer controls, 3 approved primary categories (ai-photo-prompts, trending-prompts, social-media-prompts), and 3 designated editorial author profiles (Syra Ansari, Arjun Patel, Neha Joshi). Works alongside AIPromptZyra v1.1.4 without overwriting theme files and leaves existing single-post pages (single.php) 100% untouched.
 * Version: 2.4.0
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * Author: PromptSyra Editorial Team
 * License: GPL v2 or later
 * Text Domain: promptsyra
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'PROMPTSYRA_VERSION' ) ) {
	define( 'PROMPTSYRA_VERSION', '2.4.0' );
}
if ( ! defined( 'PROMPTSYRA_DIR' ) ) {
	define( 'PROMPTSYRA_DIR', plugin_dir_path( __FILE__ ) );
}
if ( ! defined( 'PROMPTSYRA_URI' ) ) {
	define( 'PROMPTSYRA_URI', rtrim( plugin_dir_url( __FILE__ ), '/' ) );
}

if ( ! function_exists( 'promptsyra_get_approved_categories' ) ) {
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
				'slug_hint'    => 'syra-ansari',
				'mod_key'      => 'promptsyra_author_syra_id',
				'role_label'   => __( 'Lead Prompt Editor · PromptSyra.com', 'promptsyra' ),
			),
			'arjun' => array(
				'display_name' => 'Arjun Patel',
				'slug_hint'    => 'arjun-patel',
				'mod_key'      => 'promptsyra_author_arjun_id',
				'role_label'   => __( 'Editorial Contributor', 'promptsyra' ),
			),
			'neha'  => array(
				'display_name' => 'Neha Joshi',
				'slug_hint'    => 'neha-joshi',
				'mod_key'      => 'promptsyra_author_neha_id',
				'role_label'   => __( 'Editorial Contributor', 'promptsyra' ),
			),
		);
	}

	/**
	 * Register PromptSyra navigation menu locations without removing existing theme menus.
	 */
	function promptsyra_plugin_register_menus() {
		register_nav_menus(
			array(
				'promptsyra_primary'      => __( 'PromptSyra Primary Header Navigation', 'promptsyra' ),
				'promptsyra_tools'        => __( 'PromptSyra Header Tools Dropdown (Optional — Shown Only When Assigned)', 'promptsyra' ),
				'promptsyra_footer_quick' => __( 'PromptSyra Footer Quick Links', 'promptsyra' ),
				'promptsyra_footer_legal' => __( 'PromptSyra Footer Legal Links', 'promptsyra' ),
			)
		);
	}
	add_action( 'after_setup_theme', 'promptsyra_plugin_register_menus', 25 );

	/**
	 * Enqueue strictly-scoped PromptSyra assets ONLY on front page and category/author archives.
	 * Existing single-post pages (is_singular('post')) NEVER load new CSS/JS, guaranteeing 0 regressions.
	 */
	function promptsyra_plugin_enqueue_assets() {
		if ( is_singular( 'post' ) ) {
			return;
		}
		if ( ! is_front_page() && ! is_category() && ! is_author() ) {
			return;
		}

		wp_enqueue_style(
			'promptsyra-fonts',
			'https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap',
			array(),
			null
		);

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
	}
	add_action( 'wp_enqueue_scripts', 'promptsyra_plugin_enqueue_assets', 30 );

	/**
	 * Helper replacement for get_template_part() inside plugin templates.
	 *
	 * @param string $slug Template slug.
	 * @param string $name Optional template variation name.
	 */
	function promptsyra_load_part( $slug, $name = '' ) {
		$candidate = ! empty( $name ) ? PROMPTSYRA_DIR . "{$slug}-{$name}.php" : PROMPTSYRA_DIR . "{$slug}.php";
		if ( file_exists( $candidate ) ) {
			include $candidate;
		}
	}

	/**
	 * Route ONLY front-page, category archives, and author archives to PromptSyra templates.
	 * Single-post pages (single.php) ALWAYS pass straight through to AIPromptZyra untouched!
	 *
	 * @param string $template Resolved WordPress template path.
	 * @return string
	 */
	function promptsyra_plugin_template_router( $template ) {
		if ( is_singular( 'post' ) ) {
			return $template;
		}
		if ( is_front_page() && file_exists( PROMPTSYRA_DIR . 'front-page.php' ) ) {
			return PROMPTSYRA_DIR . 'front-page.php';
		}
		if ( is_category() && file_exists( PROMPTSYRA_DIR . 'category.php' ) ) {
			return PROMPTSYRA_DIR . 'category.php';
		}
		if ( is_author() && file_exists( PROMPTSYRA_DIR . 'author.php' ) ) {
			return PROMPTSYRA_DIR . 'author.php';
		}
		return $template;
	}
	add_filter( 'template_include', 'promptsyra_plugin_template_router', 99 );

	/**
	 * Resolve approved category metadata safely.
	 *
	 * @param string $slug Category slug.
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
	 * Resolve verified published WordPress page URL without creating 404 links.
	 *
	 * @param string[] $candidate_slugs Slugs to inspect.
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
	 * Resolve real WordPress User profile for Syra Ansari, Arjun Patel, or Neha Joshi.
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
				'found'        => false,
				'user_id'      => 0,
				'display_name' => $spec['display_name'],
				'role_label'   => $spec['role_label'],
				'bio'          => '',
				'avatar_url'   => '',
				'archive_url'  => '',
				'edit_url'     => current_user_can( 'create_users' ) ? admin_url( 'user-new.php' ) : '',
			);
		}

		$bio              = trim( (string) get_the_author_meta( 'description', $user->ID ) );
		$custom_photo_url = trim( (string) get_user_meta( $user->ID, 'promptsyra_profile_photo_url', true ) );
		$avatar_url       = ! empty( $custom_photo_url ) ? $custom_photo_url : get_avatar_url( $user->ID, array( 'size' => 192 ) );
		$archive_url      = get_author_posts_url( $user->ID );
		$edit_url         = current_user_can( 'edit_user', $user->ID ) ? get_edit_user_link( $user->ID ) : '';

		return array(
			'found'            => true,
			'user_id'          => (int) $user->ID,
			'display_name'     => $user->display_name ? $user->display_name : $spec['display_name'],
			'role_label'       => $spec['role_label'],
			'bio'              => $bio,
			'has_custom_photo' => ! empty( $custom_photo_url ),
			'avatar_url'       => $avatar_url ? $avatar_url : '',
			'archive_url'      => $archive_url ? $archive_url : '',
			'edit_url'         => $edit_url,
		);
	}

	require_once PROMPTSYRA_DIR . 'inc/customizer.php';
	require_once PROMPTSYRA_DIR . 'inc/admin-setup.php';
}
