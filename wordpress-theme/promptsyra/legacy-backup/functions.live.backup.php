&lt;?php
/**
 * PromptSyra Theme Functions and Definitions
 *
 * Versioning Scheme:
 * - Legacy Theme: AIPromptZyra 1.1.4
 * - Rebranded Release: PromptSyra 2.0.0 (MAJOR increment for complete brand &amp; homepage overhaul)
 * - Single-Post Article Rebuild: PromptSyra 2.1.0 (MINOR increment adding 2-column reference single-post layout, interactive AI Prompt Cards, conditional TOC, and sticky sidebar)
 * - Single-Post Final UI Fix: PromptSyra 2.2.0 (Removed single-post breadcrumb row &amp; category pill; added native comments.php &quot;Join the Conversation&quot; redesign)
 * - Authors, About, Contact &amp; Legal Pages: PromptSyra 2.3.0 (Dynamic 3-author profiles with verified WebP portraits, About Us, functional Contact Us form to contact@promptsyra.com, Privacy Policy, Disclaimer, and Terms &amp; Conditions)
 * - Master Homepage Four-Section UI Upgrade: PromptSyra 2.4.0 (Hero supporting CTA &amp; benefits bar, tailored 3-category discovery triptych with live WP slug alias support, connected 3-step prompt workflow, and interactive Why Choose PromptSyra? specimen &amp; customization ledger)
 *
 * @package PromptSyra
 * @version 2.4.0
 */

if ( ! defined( &#039;ABSPATH&#039; ) ) {
	exit;
}

define( &#039;PROMPTSYRA_VERSION&#039;, &#039;2.4.1&#039; );
define( &#039;PROMPTSYRA_DIR&#039;, get_stylesheet_directory() );
define( &#039;PROMPTSYRA_URI&#039;, get_stylesheet_directory_uri() );

/**
 * Approved Primary Categories Specification.
 *
 * @return array&lt;string, array&lt;string, string&gt;&gt;
 */
function promptsyra_get_approved_categories() {
	return array(
		&#039;ai-photo-prompts&#039;     =&gt; array(
			&#039;name&#039;        =&gt; __( &#039;AI Photo Prompts&#039;, &#039;promptsyra&#039; ),
			&#039;slug&#039;        =&gt; &#039;ai-photo-prompts&#039;,
			&#039;aliases&#039;     =&gt; array( &#039;ai-photo-prompts&#039; ),
			&#039;description&#039; =&gt; __( &#039;Portrait framing, studio and natural lighting setups, and realistic creative photography prompts.&#039;, &#039;promptsyra&#039; ),
		),
		&#039;trending-prompts&#039;     =&gt; array(
			&#039;name&#039;        =&gt; __( &#039;Trending Prompts&#039;, &#039;promptsyra&#039; ),
			&#039;slug&#039;        =&gt; &#039;trending-prompts&#039;,
			&#039;aliases&#039;     =&gt; array( &#039;trending-prompts&#039; ),
			&#039;description&#039; =&gt; __( &#039;Seasonal photography concepts, emerging visual styles, and timely creative scene ideas.&#039;, &#039;promptsyra&#039; ),
		),
		&#039;social-media-prompts&#039; =&gt; array(
			&#039;name&#039;        =&gt; __( &#039;Social Media Prompts&#039;, &#039;promptsyra&#039; ),
			&#039;slug&#039;        =&gt; &#039;social-media-prompts&#039;,
			&#039;aliases&#039;     =&gt; array( &#039;social-media-prompts&#039;, &#039;ai-social-media-prompts&#039; ),
			&#039;description&#039; =&gt; __( &#039;Vertical 9:16 stories, reel covers, creator thumbnails, and feed-ready visual compositions.&#039;, &#039;promptsyra&#039; ),
		),
	);
}

/**
 * Designated Editorial Author Profiles Specification.
 *
 * @return array&lt;string, array&lt;string, string&gt;&gt;
 */
function promptsyra_get_designated_authors_spec() {
	return array(
		&#039;syra&#039;  =&gt; array(
			&#039;display_name&#039; =&gt; &#039;Syra Ansari&#039;,
			&#039;first_name&#039;   =&gt; &#039;Syra&#039;,
			&#039;last_name&#039;    =&gt; &#039;Ansari&#039;,
			&#039;slug_hint&#039;    =&gt; &#039;syra-ansari&#039;,
			&#039;mod_key&#039;      =&gt; &#039;promptsyra_author_syra_id&#039;,
			&#039;role_label&#039;   =&gt; __( &#039;Author and Editorial Contributor&#039;, &#039;promptsyra&#039; ),
			&#039;photo_url&#039;    =&gt; &#039;https://promptsyra.com/wp-content/uploads/2026/10/Syra-Ansari.webp&#039;,
			&#039;default_bio&#039;  =&gt; __( &#039;Syra Ansari contributes AI photo prompt collections, portrait composition examples, and editorial customization guides published on PromptSyra.com, focusing on clear structure and practical adaptability across modern image tools.&#039;, &#039;promptsyra&#039; ),
		),
		&#039;arjun&#039; =&gt; array(
			&#039;display_name&#039; =&gt; &#039;Arjun Patel&#039;,
			&#039;first_name&#039;   =&gt; &#039;Arjun&#039;,
			&#039;last_name&#039;    =&gt; &#039;Patel&#039;,
			&#039;slug_hint&#039;    =&gt; &#039;arjun-patel&#039;,
			&#039;mod_key&#039;      =&gt; &#039;promptsyra_author_arjun_id&#039;,
			&#039;role_label&#039;   =&gt; __( &#039;AI Photo Prompt Contributor&#039;, &#039;promptsyra&#039; ),
			&#039;photo_url&#039;    =&gt; &#039;https://promptsyra.com/wp-content/uploads/2026/10/Arjun-Patel.webp&#039;,
			&#039;default_bio&#039;  =&gt; __( &#039;Arjun Patel prepares structured AI photo prompt ideas, lighting and framing variations, and visual style notes for PromptSyra.com readers looking to customize portrait and creative scene concepts.&#039;, &#039;promptsyra&#039; ),
		),
		&#039;neha&#039;  =&gt; array(
			&#039;display_name&#039; =&gt; &#039;Neha Joshi&#039;,
			&#039;first_name&#039;   =&gt; &#039;Neha&#039;,
			&#039;last_name&#039;    =&gt; &#039;Joshi&#039;,
			&#039;slug_hint&#039;    =&gt; &#039;neha-joshi&#039;,
			&#039;mod_key&#039;      =&gt; &#039;promptsyra_author_neha_id&#039;,
			&#039;role_label&#039;   =&gt; __( &#039;Creative Content Contributor&#039;, &#039;promptsyra&#039; ),
			&#039;photo_url&#039;    =&gt; &#039;https://promptsyra.com/wp-content/uploads/2026/10/Neha-Joshi.webp&#039;,
			&#039;default_bio&#039;  =&gt; __( &#039;Neha Joshi contributes trending visual prompt topics, social media image concepts, and step-by-step adaptation tips on PromptSyra.com to help creators tailor prompt wording to their own projects.&#039;, &#039;promptsyra&#039; ),
		),
	);
}

/**
 * Theme Setup: menus, theme support, and brand capabilities.
 */
function promptsyra_setup() {
	load_theme_textdomain( &#039;promptsyra&#039;, PROMPTSYRA_DIR . &#039;/languages&#039; );

	add_theme_support( &#039;title-tag&#039; );
	add_theme_support( &#039;post-thumbnails&#039; );
	add_theme_support(
		&#039;custom-logo&#039;,
		array(
			&#039;height&#039;      =&gt; 72,
			&#039;width&#039;       =&gt; 260,
			&#039;flex-height&#039; =&gt; true,
			&#039;flex-width&#039;  =&gt; true,
		)
	);
	add_theme_support(
		&#039;html5&#039;,
		array(
			&#039;search-form&#039;,
			&#039;comment-form&#039;,
			&#039;comment-list&#039;,
			&#039;gallery&#039;,
			&#039;caption&#039;,
			&#039;style&#039;,
			&#039;script&#039;,
		)
	);

	register_nav_menus(
		array(
			&#039;promptsyra_primary&#039;      =&gt; __( &#039;PromptSyra Primary Header Navigation&#039;, &#039;promptsyra&#039; ),
			&#039;promptsyra_tools&#039;        =&gt; __( &#039;PromptSyra Header Tools Dropdown (Optional — Shown Only When Assigned)&#039;, &#039;promptsyra&#039; ),
			&#039;promptsyra_footer_quick&#039; =&gt; __( &#039;PromptSyra Footer Quick Links&#039;, &#039;promptsyra&#039; ),
			&#039;promptsyra_footer_legal&#039; =&gt; __( &#039;PromptSyra Footer Legal Links&#039;, &#039;promptsyra&#039; ),
		)
	);
}
add_action( &#039;after_setup_theme&#039;, &#039;promptsyra_setup&#039;, 20 );

/**
 * Enqueue parent stylesheet safely (when running as a child theme) and
 * load strictly-scoped PromptSyra assets without altering single-post articles.
 */
function promptsyra_enqueue_assets() {
	// If running as a child theme, preserve parent stylesheet load order when parent doesn&#039;t auto-enqueue.
	if ( is_child_theme() ) {
		$parent_style_path = get_template_directory() . &#039;/style.css&#039;;
		if ( file_exists( $parent_style_path ) &amp;&amp; ! wp_style_is( get_template() . &#039;-style&#039;, &#039;enqueued&#039; ) ) {
			wp_enqueue_style(
				&#039;promptsyra-parent-style&#039;,
				get_template_directory_uri() . &#039;/style.css&#039;,
				array(),
				wp_get_theme( get_template() )-&gt;get( &#039;Version&#039; )
			);
		}
	}

	// In Child Theme mode, if &quot;Preserve Legacy Chrome on Single Posts&quot; is enabled, skip new CSS/JS so parent single.php renders identically.
	$preserve_legacy_single = (bool) get_theme_mod( &#039;promptsyra_preserve_legacy_single_chrome&#039;, true );
	if ( is_child_theme() &amp;&amp; is_singular( &#039;post&#039; ) &amp;&amp; $preserve_legacy_single ) {
		return;
	}

	// Load Google Fonts with sensible display=swap fallback.
	wp_enqueue_style(
		&#039;promptsyra-fonts&#039;,
		&#039;https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&amp;family=Plus+Jakarta+Sans:wght@600;700;800&amp;display=swap&#039;,
		array(),
		null
	);

	// Strictly scoped stylesheet (.ps-* classes only; zero bare HTML tag selectors).
	wp_enqueue_style(
		&#039;promptsyra-scoped-ui&#039;,
		PROMPTSYRA_URI . &#039;/assets/css/promptsyra-home.css&#039;,
		array(),
		PROMPTSYRA_VERSION
	);

	wp_enqueue_script(
		&#039;promptsyra-scoped-ui&#039;,
		PROMPTSYRA_URI . &#039;/assets/js/promptsyra-home.js&#039;,
		array(),
		PROMPTSYRA_VERSION,
		true
	);

	// Load single-post layout, prompt-card, TOC, and sticky-sidebar assets strictly on single posts (never on front-page.php).
	if ( is_singular( &#039;post&#039; ) ) {
		wp_enqueue_style(
			&#039;promptsyra-single-post&#039;,
			PROMPTSYRA_URI . &#039;/assets/css/promptsyra-single.css&#039;,
			array( &#039;promptsyra-scoped-ui&#039; ),
			PROMPTSYRA_VERSION
		);

		wp_enqueue_script(
			&#039;promptsyra-single-post&#039;,
			PROMPTSYRA_URI . &#039;/assets/js/promptsyra-single.js&#039;,
			array(),
			PROMPTSYRA_VERSION,
			true
		);

		if ( comments_open() &amp;&amp; get_option( &#039;thread_comments&#039; ) ) {
			wp_enqueue_script( &#039;comment-reply&#039; );
		}
	}

	// Load editorial, contact, legal, and author archive typography &amp; layout styles on pages and author archives.
	if ( is_page() || is_author() || is_404() ) {
		wp_enqueue_style(
			&#039;promptsyra-pages&#039;,
			PROMPTSYRA_URI . &#039;/assets/css/promptsyra-pages.css&#039;,
			array( &#039;promptsyra-scoped-ui&#039; ),
			PROMPTSYRA_VERSION
		);
	}
}
add_action( &#039;wp_enqueue_scripts&#039;, &#039;promptsyra_enqueue_assets&#039;, 30 );

/**
 * Resolve an approved category object safely without creating broken links.
 *
 * @param string $slug Approved category slug.
 * @return array&lt;string, mixed&gt;
 */
function promptsyra_resolve_category_data( $slug ) {
	$approved = promptsyra_get_approved_categories();
	$fallback = isset( $approved[ $slug ] ) ? $approved[ $slug ] : array(
		&#039;name&#039;        =&gt; $slug,
		&#039;slug&#039;        =&gt; $slug,
		&#039;aliases&#039;     =&gt; array( $slug ),
		&#039;description&#039; =&gt; &#039;&#039;,
	);

	$candidate_slugs = ! empty( $fallback[&#039;aliases&#039;] ) &amp;&amp; is_array( $fallback[&#039;aliases&#039;] )
		? $fallback[&#039;aliases&#039;]
		: array( $slug );

	foreach ( $candidate_slugs as $candidate_slug ) {
		$term = get_term_by( &#039;slug&#039;, $candidate_slug, &#039;category&#039; );
		if ( $term &amp;&amp; ! is_wp_error( $term ) ) {
			$link = get_term_link( $term );
			return array(
				&#039;exists&#039;        =&gt; true,
				&#039;term_id&#039;       =&gt; (int) $term-&gt;term_id,
				&#039;name&#039;          =&gt; $fallback[&#039;name&#039;],
				&#039;wp_term_name&#039;  =&gt; $term-&gt;name,
				&#039;slug&#039;          =&gt; $term-&gt;slug,
				&#039;canonical_key&#039; =&gt; $slug,
				&#039;description&#039;   =&gt; ! empty( $term-&gt;description ) ? $term-&gt;description : $fallback[&#039;description&#039;],
				&#039;count&#039;         =&gt; (int) $term-&gt;count,
				&#039;url&#039;           =&gt; ! is_wp_error( $link ) ? $link : home_url( &#039;/&#039; ),
			);
		}
	}

	return array(
		&#039;exists&#039;        =&gt; false,
		&#039;term_id&#039;       =&gt; 0,
		&#039;name&#039;          =&gt; $fallback[&#039;name&#039;],
		&#039;wp_term_name&#039;  =&gt; $fallback[&#039;name&#039;],
		&#039;slug&#039;          =&gt; $fallback[&#039;slug&#039;],
		&#039;canonical_key&#039; =&gt; $slug,
		&#039;description&#039;   =&gt; $fallback[&#039;description&#039;],
		&#039;count&#039;         =&gt; 0,
		&#039;url&#039;           =&gt; &#039;&#039;,
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
		if ( $page instanceof WP_Post &amp;&amp; &#039;publish&#039; === $page-&gt;post_status ) {
			$permalink = get_permalink( $page-&gt;ID );
			if ( $permalink ) {
				return $permalink;
			}
		}
	}
	return &#039;&#039;;
}

/**
 * Resolve real WordPress User data for one of the 3 designated editorial profiles.
 * Never fabricates biographies, credentials, or photos.
 *
 * @param string $author_key &#039;syra&#039; | &#039;arjun&#039; | &#039;neha&#039;.
 * @return array&lt;string, mixed&gt;
 */
function promptsyra_resolve_designated_author( $author_key ) {
	$specs = promptsyra_get_designated_authors_spec();
	if ( ! isset( $specs[ $author_key ] ) ) {
		return array( &#039;found&#039; =&gt; false );
	}

	$spec    = $specs[ $author_key ];
	$user    = false;
	$mod_uid = absint( get_theme_mod( $spec[&#039;mod_key&#039;], 0 ) );

	if ( $mod_uid &gt; 0 ) {
		$user = get_userdata( $mod_uid );
	}

	if ( ! $user ) {
		$user = get_user_by( &#039;slug&#039;, $spec[&#039;slug_hint&#039;] );
	}

	if ( ! $user ) {
		$matching_users = get_users(
			array(
				&#039;search&#039;         =&gt; $spec[&#039;display_name&#039;],
				&#039;search_columns&#039; =&gt; array( &#039;display_name&#039;, &#039;user_login&#039;, &#039;user_nicename&#039; ),
				&#039;number&#039;         =&gt; 1,
			)
		);
		if ( ! empty( $matching_users ) ) {
			$user = $matching_users[0];
		}
	}

	if ( ! ( $user instanceof WP_User ) ) {
		return array(
			&#039;found&#039;            =&gt; false,
			&#039;user_id&#039;          =&gt; 0,
			&#039;display_name&#039;     =&gt; $spec[&#039;display_name&#039;],
			&#039;role_label&#039;       =&gt; $spec[&#039;role_label&#039;],
			&#039;bio&#039;              =&gt; $spec[&#039;default_bio&#039;],
			&#039;default_bio&#039;      =&gt; $spec[&#039;default_bio&#039;],
			&#039;has_custom_photo&#039; =&gt; ! empty( $spec[&#039;photo_url&#039;] ),
			&#039;avatar_url&#039;       =&gt; $spec[&#039;photo_url&#039;],
			&#039;archive_url&#039;      =&gt; &#039;&#039;,
			&#039;edit_url&#039;         =&gt; current_user_can( &#039;create_users&#039; ) ? admin_url( &#039;themes.php?page=promptsyra-setup#ps-sync-panel&#039; ) : &#039;&#039;,
		);
	}

	$bio              = trim( (string) get_the_author_meta( &#039;description&#039;, $user-&gt;ID ) );
	$custom_role      = trim( (string) get_user_meta( $user-&gt;ID, &#039;promptsyra_editorial_role&#039;, true ) );
	$custom_photo_url = trim( (string) get_user_meta( $user-&gt;ID, &#039;promptsyra_profile_photo_url&#039;, true ) );
	if ( empty( $custom_photo_url ) &amp;&amp; ! empty( $spec[&#039;photo_url&#039;] ) ) {
		$custom_photo_url = $spec[&#039;photo_url&#039;];
	}
	$avatar_url  = ! empty( $custom_photo_url ) ? $custom_photo_url : get_avatar_url( $user-&gt;ID, array( &#039;size&#039; =&gt; 192 ) );
	$archive_url = get_author_posts_url( $user-&gt;ID );
	$edit_url    = current_user_can( &#039;edit_user&#039;, $user-&gt;ID ) ? get_edit_user_link( $user-&gt;ID ) : &#039;&#039;;

	return array(
		&#039;found&#039;            =&gt; true,
		&#039;user_id&#039;          =&gt; (int) $user-&gt;ID,
		&#039;display_name&#039;     =&gt; $user-&gt;display_name ? $user-&gt;display_name : $spec[&#039;display_name&#039;],
		&#039;role_label&#039;       =&gt; ! empty( $custom_role ) ? $custom_role : $spec[&#039;role_label&#039;],
		&#039;bio&#039;              =&gt; ! empty( $bio ) ? $bio : $spec[&#039;default_bio&#039;],
		&#039;default_bio&#039;      =&gt; $spec[&#039;default_bio&#039;],
		&#039;has_custom_photo&#039; =&gt; ! empty( $custom_photo_url ),
		&#039;avatar_url&#039;       =&gt; $avatar_url ? $avatar_url : &#039;&#039;,
		&#039;archive_url&#039;      =&gt; $archive_url ? $archive_url : &#039;&#039;,
		&#039;edit_url&#039;         =&gt; $edit_url,
	);
}

/**
 * Resolve any WordPress user&#039;s editorial role label cleanly.
 *
 * @param int $user_id WordPress User ID.
 * @return string
 */
function promptsyra_get_user_editorial_role( $user_id ) {
	$user_id = absint( $user_id );
	if ( $user_id &lt;= 0 ) {
		return __( &#039;Author&#039;, &#039;promptsyra&#039; );
	}

	$saved_role = trim( (string) get_user_meta( $user_id, &#039;promptsyra_editorial_role&#039;, true ) );
	if ( ! empty( $saved_role ) ) {
		return $saved_role;
	}

	$user = get_userdata( $user_id );
	if ( $user instanceof WP_User ) {
		foreach ( promptsyra_get_designated_authors_spec() as $spec ) {
			if ( $user-&gt;user_nicename === $spec[&#039;slug_hint&#039;] || $user-&gt;user_login === $spec[&#039;slug_hint&#039;] ) {
				return $spec[&#039;role_label&#039;];
			}
		}
	}

	return __( &#039;Author&#039;, &#039;promptsyra&#039; );
}

/**
 * Filter WordPress get_avatar_data so any author with a configured Media Library
 * portrait URL (promptsyra_profile_photo_url) uses their local WebP portrait everywhere
 * without hitting external Gravatar requests.
 *
 * @param array&lt;string, mixed&gt; $args        Avatar data arguments.
 * @param mixed                $id_or_email User ID, WP_User, WP_Post, WP_Comment, or email.
 * @return array&lt;string, mixed&gt;
 */
function promptsyra_filter_user_avatar_data( $args, $id_or_email ) {
	$user_id = 0;

	if ( is_numeric( $id_or_email ) ) {
		$user_id = (int) $id_or_email;
	} elseif ( $id_or_email instanceof WP_User ) {
		$user_id = (int) $id_or_email-&gt;ID;
	} elseif ( $id_or_email instanceof WP_Post ) {
		$user_id = (int) $id_or_email-&gt;post_author;
	} elseif ( $id_or_email instanceof WP_Comment &amp;&amp; ! empty( $id_or_email-&gt;user_id ) ) {
		$user_id = (int) $id_or_email-&gt;user_id;
	}

	if ( $user_id &gt; 0 ) {
		$photo_url = trim( (string) get_user_meta( $user_id, &#039;promptsyra_profile_photo_url&#039;, true ) );
		if ( empty( $photo_url ) ) {
			$u = get_userdata( $user_id );
			if ( $u instanceof WP_User ) {
				foreach ( promptsyra_get_designated_authors_spec() as $spec ) {
					if ( $u-&gt;user_nicename === $spec[&#039;slug_hint&#039;] || $u-&gt;user_login === $spec[&#039;slug_hint&#039;] ) {
						$photo_url = $spec[&#039;photo_url&#039;];
						break;
					}
				}
			}
		}
		if ( ! empty( $photo_url ) ) {
			$args[&#039;url&#039;]          = esc_url_raw( $photo_url );
			$args[&#039;found_avatar&#039;] = true;
		}
	}

	return $args;
}
add_filter( &#039;pre_get_avatar_data&#039;, &#039;promptsyra_filter_user_avatar_data&#039;, 10, 2 );

/**
 * Output safe social sharing fallback meta on the front page ONLY if no SEO plugin is active,
 * preventing duplicate og:title / meta description / canonical conflicts.
 */
function promptsyra_has_seo_plugin() {
	return (
		defined( &#039;WPSEO_VERSION&#039; ) ||
		defined( &#039;RANK_MATH_VERSION&#039; ) ||
		defined( &#039;AIOSEO_VERSION&#039; ) ||
		defined( &#039;SEOPRESS_VERSION&#039; ) ||
		defined( &#039;THE_SEO_FRAMEWORK_PRESENT&#039; )
	);
}

function promptsyra_front_social_meta_fallback() {
	if ( ! is_front_page() || promptsyra_has_seo_plugin() ) {
		return;
	}

	$site_name = get_bloginfo( &#039;name&#039; );
	if ( empty( $site_name ) || false !== stripos( $site_name, &#039;aipromptzyra&#039; ) ) {
		$site_name = &#039;PromptSyra.com&#039;;
	}

	$desc = get_theme_mod(
		&#039;promptsyra_hero_description&#039;,
		__( &#039;Discover useful AI photo prompts for portraits, creative photography, social media visuals, and more.&#039;, &#039;promptsyra&#039; )
	);
	?&gt;
	&lt;meta name=&quot;description&quot; content=&quot;&lt;?php echo esc_attr( wp_strip_all_tags( $desc ) ); ?&gt;&quot; /&gt;
	&lt;meta property=&quot;og:site_name&quot; content=&quot;&lt;?php echo esc_attr( $site_name ); ?&gt;&quot; /&gt;
	&lt;meta property=&quot;og:title&quot; content=&quot;&lt;?php echo esc_attr( $site_name . &#039; — AI Photo Prompts &amp; Creative Ideas&#039; ); ?&gt;&quot; /&gt;
	&lt;meta property=&quot;og:description&quot; content=&quot;&lt;?php echo esc_attr( wp_strip_all_tags( $desc ) ); ?&gt;&quot; /&gt;
	&lt;meta property=&quot;og:type&quot; content=&quot;website&quot; /&gt;
	&lt;meta property=&quot;og:url&quot; content=&quot;&lt;?php echo esc_url( home_url( &#039;/&#039; ) ); ?&gt;&quot; /&gt;
	&lt;?php
}
add_action( &#039;wp_head&#039;, &#039;promptsyra_front_social_meta_fallback&#039;, 5 );


/**
 * Official PromptSyra Social Links Specification.
 *
 * @return array&lt;string, array&lt;string, string&gt;&gt;
 */
function promptsyra_get_official_social_links() {
	return array(
		&#039;instagram&#039; =&gt; array(
			&#039;key&#039;          =&gt; &#039;instagram&#039;,
			&#039;name&#039;         =&gt; __( &#039;Instagram&#039;, &#039;promptsyra&#039; ),
			&#039;handle&#039;       =&gt; &#039;@promptsyra&#039;,
			&#039;badge&#039;        =&gt; __( &#039;Visual Gallery&#039;, &#039;promptsyra&#039; ),
			&#039;title&#039;        =&gt; __( &#039;Instagram Visual Gallery&#039;, &#039;promptsyra&#039; ),
			&#039;desc&#039;         =&gt; __( &#039;Browse curated portrait lighting setups, photo prompt ideas, and visual aesthetics crafted for modern creators.&#039;, &#039;promptsyra&#039; ),
			&#039;button_label&#039; =&gt; __( &#039;Follow on Instagram&#039;, &#039;promptsyra&#039; ),
			&#039;url&#039;          =&gt; &#039;https://www.instagram.com/promptsyra&#039;,
			&#039;internal_slug&#039;=&gt; &#039;instagram&#039;,
			&#039;internal_url&#039; =&gt; home_url( &#039;/instagram/&#039; ),
		),
		&#039;facebook&#039;  =&gt; array(
			&#039;key&#039;          =&gt; &#039;facebook&#039;,
			&#039;name&#039;         =&gt; __( &#039;Facebook&#039;, &#039;promptsyra&#039; ),
			&#039;handle&#039;       =&gt; &#039;PromptSyra&#039;,
			&#039;badge&#039;        =&gt; __( &#039;Community Updates&#039;, &#039;promptsyra&#039; ),
			&#039;title&#039;        =&gt; __( &#039;Facebook Community Updates&#039;, &#039;promptsyra&#039; ),
			&#039;desc&#039;         =&gt; __( &#039;Stay updated with newly published prompt guides, creative themes, creator discussions, and website announcements.&#039;, &#039;promptsyra&#039; ),
			&#039;button_label&#039; =&gt; __( &#039;Follow on Facebook&#039;, &#039;promptsyra&#039; ),
			&#039;url&#039;          =&gt; &#039;https://www.facebook.com/people/PromptSyra/61595584906422/&#039;,
			&#039;internal_slug&#039;=&gt; &#039;facebook&#039;,
			&#039;internal_url&#039; =&gt; home_url( &#039;/facebook/&#039; ),
		),
		&#039;whatsapp&#039;  =&gt; array(
			&#039;key&#039;          =&gt; &#039;whatsapp&#039;,
			&#039;name&#039;         =&gt; __( &#039;WhatsApp Channel&#039;, &#039;promptsyra&#039; ),
			&#039;handle&#039;       =&gt; &#039;PromptSyra&#039;,
			&#039;badge&#039;        =&gt; __( &#039;Official Channel&#039;, &#039;promptsyra&#039; ),
			&#039;title&#039;        =&gt; __( &#039;WhatsApp Official Channel&#039;, &#039;promptsyra&#039; ),
			&#039;desc&#039;         =&gt; __( &#039;Get instant mobile alerts and prompt drops directly to your phone. 100% private one-way broadcast.&#039;, &#039;promptsyra&#039; ),
			&#039;button_label&#039; =&gt; __( &#039;Join WhatsApp Channel&#039;, &#039;promptsyra&#039; ),
			&#039;url&#039;          =&gt; &#039;https://whatsapp.com/channel/0029VbDkVtP7T8bZzLpWMq2d&#039;,
			&#039;internal_slug&#039;=&gt; &#039;whatsapp-channel&#039;,
			&#039;internal_url&#039; =&gt; home_url( &#039;/whatsapp-channel/&#039; ),
		),
	);
}

require_once PROMPTSYRA_DIR . &#039;/inc/customizer.php&#039;;
require_once PROMPTSYRA_DIR . &#039;/inc/contact-form.php&#039;;
require_once PROMPTSYRA_DIR . &#039;/inc/essential-pages-and-authors.php&#039;;
require_once PROMPTSYRA_DIR . &#039;/inc/admin-setup.php&#039;;
require_once PROMPTSYRA_DIR . &#039;/inc/single-post-features.php&#039;;
