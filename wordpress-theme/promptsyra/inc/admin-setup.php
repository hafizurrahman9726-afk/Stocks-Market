<?php
/**
 * PromptSyra Non-Destructive Admin Setup, Author Profile Fields & Page Provisioner (v2.3.0)
 *
 * Provides:
 * 1. Editable Editorial Portrait URL and Editorial Role fields on Users -> Profile so
 *    Syra Ansari, Arjun Patel, and Neha Joshi can be managed natively in WordPress.
 * 2. Appearance -> PromptSyra Setup page to safely:
 *    - Provision/sync the 3 WordPress Author accounts (with their verified WebP portraits
 *      and roles, without reassigning any existing published posts)
 *    - Publish/update the 5 essential pages (About Us, Contact Us, Privacy Policy,
 *      Disclaimer, Terms & Conditions) in-place with automatic backup snapshots
 *    - Create the 3 approved primary categories if missing
 *
 * @package PromptSyra
 * @version 2.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render custom Editorial Portrait URL and Role Label fields on WordPress User Profile screen.
 *
 * @param WP_User $user User object being edited.
 */
function promptsyra_render_user_profile_fields( WP_User $user ) {
	if ( ! current_user_can( 'edit_user', $user->ID ) ) {
		return;
	}
	$photo_url = get_user_meta( $user->ID, 'promptsyra_profile_photo_url', true );
	$role_text = get_user_meta( $user->ID, 'promptsyra_editorial_role', true );
	wp_nonce_field( 'promptsyra_save_user_photo_' . $user->ID, 'promptsyra_user_photo_nonce' );
	?>
	<h2><?php esc_html_e( 'PromptSyra Editorial Author Profile', 'promptsyra' ); ?></h2>
	<table class="form-table" role="presentation">
		<tr>
			<th>
				<label for="promptsyra_editorial_role"><?php esc_html_e( 'Editorial Role / Title', 'promptsyra' ); ?></label>
			</th>
			<td>
				<input
					type="text"
					name="promptsyra_editorial_role"
					id="promptsyra_editorial_role"
					value="<?php echo esc_attr( $role_text ); ?>"
					class="regular-text"
					placeholder="e.g., Author and Editorial Contributor"
				/>
				<p class="description">
					<?php esc_html_e( 'Displayed alongside your name on the About Us page, Author Archive, and Single-Post author box.', 'promptsyra' ); ?>
				</p>
			</td>
		</tr>
		<tr>
			<th>
				<label for="promptsyra_profile_photo_url"><?php esc_html_e( 'Editorial Portrait Image URL', 'promptsyra' ); ?></label>
			</th>
			<td>
				<input
					type="url"
					name="promptsyra_profile_photo_url"
					id="promptsyra_profile_photo_url"
					value="<?php echo esc_attr( $photo_url ); ?>"
					class="regular-text code"
					placeholder="https://promptsyra.com/wp-content/uploads/2026/10/Syra-Ansari.webp"
				/>
				<p class="description">
					<?php esc_html_e( 'Paste a WebP/JPG/PNG image URL from your WordPress Media Library to display across PromptSyra author cards and archives without external Gravatar calls.', 'promptsyra' ); ?>
				</p>
			</td>
		</tr>
	</table>
	<?php
}
add_action( 'show_user_profile', 'promptsyra_render_user_profile_fields' );
add_action( 'edit_user_profile', 'promptsyra_render_user_profile_fields' );

/**
 * Save custom Profile Photo URL and Role securely with capability and nonce checks.
 *
 * @param int $user_id ID of user being saved.
 */
function promptsyra_save_user_profile_fields( $user_id ) {
	if ( ! current_user_can( 'edit_user', $user_id ) ) {
		return;
	}
	if (
		! isset( $_POST['promptsyra_user_photo_nonce'] ) ||
		! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['promptsyra_user_photo_nonce'] ) ), 'promptsyra_save_user_photo_' . $user_id )
	) {
		return;
	}

	if ( isset( $_POST['promptsyra_editorial_role'] ) ) {
		$clean_role = sanitize_text_field( wp_unslash( $_POST['promptsyra_editorial_role'] ) );
		if ( ! empty( $clean_role ) ) {
			update_user_meta( $user_id, 'promptsyra_editorial_role', $clean_role );
		} else {
			delete_user_meta( $user_id, 'promptsyra_editorial_role' );
		}
	}

	if ( isset( $_POST['promptsyra_profile_photo_url'] ) ) {
		$clean_url = esc_url_raw( wp_unslash( $_POST['promptsyra_profile_photo_url'] ) );
		if ( ! empty( $clean_url ) ) {
			update_user_meta( $user_id, 'promptsyra_profile_photo_url', $clean_url );
		} else {
			delete_user_meta( $user_id, 'promptsyra_profile_photo_url' );
		}
	}
}
add_action( 'personal_options_update', 'promptsyra_save_user_profile_fields' );
add_action( 'edit_user_profile_update', 'promptsyra_save_user_profile_fields' );

/**
 * Register Appearance -> PromptSyra Setup submenu page.
 */
function promptsyra_register_setup_page() {
	add_theme_page(
		__( 'PromptSyra Setup & Audit', 'promptsyra' ),
		__( 'PromptSyra Setup', 'promptsyra' ),
		'manage_options',
		'promptsyra-setup',
		'promptsyra_render_setup_page'
	);
}
add_action( 'admin_menu', 'promptsyra_register_setup_page' );

/**
 * Handle non-destructive category creation and v2.3.0 Author + Essential Pages sync.
 */
function promptsyra_handle_setup_actions() {
	if ( isset( $_POST['promptsyra_run_v230_sync'] ) && function_exists( 'promptsyra_sync_authors_and_essential_pages' ) ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Insufficient permissions to synchronize authors and pages.', 'promptsyra' ) );
		}
		check_admin_referer( 'promptsyra_v230_sync_action', 'promptsyra_v230_sync_nonce' );

		$force_refresh = ! empty( $_POST['promptsyra_force_refresh_pages'] );
		$summary       = promptsyra_sync_authors_and_essential_pages( $force_refresh );

		wp_safe_redirect(
			add_query_arg(
				array(
					'page'            => 'promptsyra-setup',
					'v230_synced'     => 1,
					'authors_created' => (int) $summary['authors_created'],
					'authors_updated' => (int) $summary['authors_updated'],
					'pages_created'   => (int) $summary['pages_created'],
					'pages_updated'   => (int) $summary['pages_updated'],
				),
				admin_url( 'themes.php' )
			)
		);
		exit;
	}

	if ( ! isset( $_POST['promptsyra_create_categories'] ) ) {
		return;
	}
	if ( ! current_user_can( 'manage_categories' ) || ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Insufficient permissions to manage categories.', 'promptsyra' ) );
	}
	check_admin_referer( 'promptsyra_setup_categories_action', 'promptsyra_setup_nonce' );

	$created = array();
	foreach ( promptsyra_get_approved_categories() as $slug => $cat_spec ) {
		$existing = get_term_by( 'slug', $slug, 'category' );
		if ( ! $existing ) {
			$result = wp_insert_term(
				$cat_spec['name'],
				'category',
				array(
					'slug'        => $slug,
					'description' => $cat_spec['description'],
				)
			);
			if ( ! is_wp_error( $result ) ) {
				$created[] = $slug;
			}
		}
	}

	wp_safe_redirect(
		add_query_arg(
			array(
				'page'          => 'promptsyra-setup',
				'cats_verified' => count( $created ),
			),
			admin_url( 'themes.php' )
		)
	);
	exit;
}
add_action( 'admin_init', 'promptsyra_handle_setup_actions' );

/**
 * Render the Appearance -> PromptSyra Setup & Audit screen.
 */
function promptsyra_render_setup_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$approved_cats  = promptsyra_get_approved_categories();
	$all_cats       = get_categories( array( 'hide_empty' => false ) );
	$essential_spec = function_exists( 'promptsyra_get_essential_pages_spec' ) ? promptsyra_get_essential_pages_spec() : array();
	$last_sync      = get_option( 'promptsyra_v230_sync_completed', '' );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'PromptSyra.com v2.3.0 — Non-Destructive Setup & Audit', 'promptsyra' ); ?></h1>
		<p>
			<?php esc_html_e( 'Manage the three designated editorial author profiles, five essential legal & editorial pages, and three approved primary categories. Existing published posts and post authorship records are never reassigned automatically.', 'promptsyra' ); ?>
		</p>

		<?php if ( isset( $_GET['v230_synced'] ) ) : ?>
			<div class="notice notice-success is-dismissible">
				<p>
					<?php
					printf(
						/* translators: 1: authors created, 2: authors updated, 3: pages created, 4: pages updated */
						esc_html__( 'PromptSyra v2.3.0 sync complete: %1$d author(s) created, %2$d author(s) verified, %3$d page(s) created, and %4$d page(s) updated in-place (legacy snapshots backed up safely).', 'promptsyra' ),
						absint( $_GET['authors_created'] ?? 0 ),
						absint( $_GET['authors_updated'] ?? 0 ),
						absint( $_GET['pages_created'] ?? 0 ),
						absint( $_GET['pages_updated'] ?? 0 )
					);
					?>
				</p>
			</div>
		<?php endif; ?>

		<?php if ( isset( $_GET['cats_verified'] ) ) : ?>
			<div class="notice notice-success is-dismissible">
				<p>
					<?php
					printf(
						/* translators: %d: number of newly created categories */
						esc_html__( 'Category verification complete. %d missing approved category record(s) created safely without duplicating slugs.', 'promptsyra' ),
						absint( $_GET['cats_verified'] )
					);
					?>
				</p>
			</div>
		<?php endif; ?>

		<hr />
		<div id="ps-sync-panel" style="background:#fff;border:1px solid #cbd5e1;border-radius:8px;padding:18px 20px;max-width:960px;margin:16px 0 24px;">
			<h2 style="margin-top:0;"><?php esc_html_e( '1-Click Author Profiles & Essential Pages Provisioner (v2.3.0)', 'promptsyra' ); ?></h2>
			<p style="margin:0 0 12px;">
				<?php esc_html_e( 'Creates or updates WordPress Author accounts for Syra Ansari, Arjun Patel, and Neha Joshi using their verified Media Library WebP portraits, and updates About Us (/about-us/), Contact Us (/contact-us/), Privacy Policy (/privacy-policy/), Disclaimer (/disclaimer/), and Terms & Conditions (/terms-and-conditions/) in-place without creating duplicate pages.', 'promptsyra' ); ?>
			</p>
			<?php if ( ! empty( $last_sync ) ) : ?>
				<p style="margin:0 0 12px;color:#15803d;font-weight:600;">
					<?php
					printf(
						/* translators: %s: timestamp */
						esc_html__( 'Last synchronized: %s', 'promptsyra' ),
						esc_html( (string) $last_sync )
					);
					?>
				</p>
			<?php endif; ?>
			<form method="post">
				<?php wp_nonce_field( 'promptsyra_v230_sync_action', 'promptsyra_v230_sync_nonce' ); ?>
				<p style="margin:0 0 12px;">
					<label>
						<input type="checkbox" name="promptsyra_force_refresh_pages" value="1" />
						<?php esc_html_e( 'Also refresh already-synced essential pages to the latest default PromptSyra v2.3.0 text (original pre-sync content remains backed up in post meta).', 'promptsyra' ); ?>
					</label>
				</p>
				<button type="submit" name="promptsyra_run_v230_sync" value="1" class="button button-primary button-hero">
					<?php esc_html_e( 'Sync 3 Authors & 5 Essential Pages in WordPress', 'promptsyra' ); ?>
				</button>
			</form>
		</div>

		<h2><?php esc_html_e( '1. Essential Website & Legal Pages Audit', 'promptsyra' ); ?></h2>
		<table class="widefat striped" style="max-width:960px;">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Required Page', 'promptsyra' ); ?></th>
					<th><?php esc_html_e( 'Preferred Slug', 'promptsyra' ); ?></th>
					<th><?php esc_html_e( 'Database Status', 'promptsyra' ); ?></th>
					<th><?php esc_html_e( 'Permalink', 'promptsyra' ); ?></th>
					<th><?php esc_html_e( 'Editor', 'promptsyra' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $essential_spec as $p_key => $p_spec ) : ?>
					<?php
					$found_page = function_exists( 'promptsyra_find_existing_page_by_slugs' ) ? promptsyra_find_existing_page_by_slugs( $p_spec['candidate_slugs'], $p_key ) : null;
					$is_stub    = ( $found_page instanceof WP_Post && function_exists( 'promptsyra_is_legacy_stub_page' ) ) ? promptsyra_is_legacy_stub_page( $found_page ) : false;
					?>
					<tr>
						<td><strong><?php echo esc_html( $p_spec['title'] ); ?></strong></td>
						<td><code>/<?php echo esc_html( $p_spec['preferred_slug'] ); ?>/</code></td>
						<td>
							<?php if ( $found_page instanceof WP_Post && 'publish' === $found_page->post_status && ! $is_stub ) : ?>
								<span style="color:#15803d;font-weight:600;"><?php esc_html_e( 'Published & Synced', 'promptsyra' ); ?></span>
							<?php elseif ( $found_page instanceof WP_Post && 'publish' === $found_page->post_status && $is_stub ) : ?>
								<span style="color:#b45309;font-weight:600;"><?php esc_html_e( 'Legacy Stub in DB (Live Fallback Active)', 'promptsyra' ); ?></span>
							<?php elseif ( $found_page instanceof WP_Post ) : ?>
								<span style="color:#b45309;font-weight:600;"><?php echo esc_html( ucfirst( $found_page->post_status ) . ' — Click Sync Above to Publish' ); ?></span>
							<?php else : ?>
								<span style="color:#b45309;font-weight:600;"><?php esc_html_e( 'Missing — Click Sync Above to Create', 'promptsyra' ); ?></span>
							<?php endif; ?>
						</td>
						<td>
							<?php if ( $found_page instanceof WP_Post && 'publish' === $found_page->post_status ) : ?>
								<a href="<?php echo esc_url( get_permalink( $found_page->ID ) ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'View Page', 'promptsyra' ); ?></a>
							<?php else : ?>
								—
							<?php endif; ?>
						</td>
						<td>
							<?php if ( $found_page instanceof WP_Post && current_user_can( 'edit_post', $found_page->ID ) ) : ?>
								<a href="<?php echo esc_url( get_edit_post_link( $found_page->ID ) ); ?>" class="button button-small"><?php esc_html_e( 'Edit Page', 'promptsyra' ); ?></a>
							<?php else : ?>
								—
							<?php endif; ?>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>

		<hr style="margin-top:28px;" />
		<h2><?php esc_html_e( '2. Three Designated Author Profiles Audit', 'promptsyra' ); ?></h2>
		<table class="widefat striped" style="max-width:960px;">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Designated Author', 'promptsyra' ); ?></th>
					<th><?php esc_html_e( 'Editorial Role', 'promptsyra' ); ?></th>
					<th><?php esc_html_e( 'WP User Account', 'promptsyra' ); ?></th>
					<th><?php esc_html_e( 'WebP Portrait', 'promptsyra' ); ?></th>
					<th><?php esc_html_e( 'Action', 'promptsyra' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( array( 'syra', 'arjun', 'neha' ) as $author_key ) : ?>
					<?php $a = promptsyra_resolve_designated_author( $author_key ); ?>
					<tr>
						<td><strong><?php echo esc_html( $a['display_name'] ); ?></strong></td>
						<td><?php echo esc_html( $a['role_label'] ); ?></td>
						<td>
							<?php if ( $a['found'] ) : ?>
								<span style="color:#15803d;font-weight:600;">
									<?php echo esc_html( sprintf( 'Active (User #%d)', $a['user_id'] ) ); ?>
								</span>
							<?php else : ?>
								<span style="color:#b45309;font-weight:600;"><?php esc_html_e( 'Click Sync Above to Create WP User', 'promptsyra' ); ?></span>
							<?php endif; ?>
						</td>
						<td>
							<?php if ( ! empty( $a['avatar_url'] ) ) : ?>
								<a href="<?php echo esc_url( $a['avatar_url'] ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Verified WebP Linked', 'promptsyra' ); ?></a>
							<?php else : ?>
								—
							<?php endif; ?>
						</td>
						<td>
							<?php if ( $a['found'] && ! empty( $a['archive_url'] ) ) : ?>
								<a href="<?php echo esc_url( $a['archive_url'] ); ?>" class="button button-small" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Archive', 'promptsyra' ); ?></a>
							<?php endif; ?>
							<?php if ( ! empty( $a['edit_url'] ) ) : ?>
								<a href="<?php echo esc_url( $a['edit_url'] ); ?>" class="button button-small"><?php echo $a['found'] ? esc_html__( 'Edit Profile', 'promptsyra' ) : esc_html__( 'Sync User', 'promptsyra' ); ?></a>
							<?php endif; ?>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>

		<hr style="margin-top:28px;" />
		<h2><?php esc_html_e( '3. Three Approved Primary Categories', 'promptsyra' ); ?></h2>
		<table class="widefat striped" style="max-width:960px;">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Category Name', 'promptsyra' ); ?></th>
					<th><?php esc_html_e( 'Required Slug', 'promptsyra' ); ?></th>
					<th><?php esc_html_e( 'Status', 'promptsyra' ); ?></th>
					<th><?php esc_html_e( 'Published Posts', 'promptsyra' ); ?></th>
					<th><?php esc_html_e( 'Archive Link', 'promptsyra' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $approved_cats as $slug => $spec ) : ?>
					<?php $resolved = promptsyra_resolve_category_data( $slug ); ?>
					<tr>
						<td><strong><?php echo esc_html( $resolved['name'] ); ?></strong></td>
						<td><code><?php echo esc_html( $slug ); ?></code></td>
						<td>
							<?php if ( $resolved['exists'] ) : ?>
								<span style="color:#15803d;font-weight:600;"><?php esc_html_e( 'Configured', 'promptsyra' ); ?></span>
							<?php else : ?>
								<span style="color:#b45309;font-weight:600;"><?php esc_html_e( 'Not Created Yet', 'promptsyra' ); ?></span>
							<?php endif; ?>
						</td>
						<td><?php echo esc_html( (string) $resolved['count'] ); ?></td>
						<td>
							<?php if ( $resolved['exists'] && ! empty( $resolved['url'] ) ) : ?>
								<a href="<?php echo esc_url( $resolved['url'] ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'View Archive', 'promptsyra' ); ?></a>
							<?php else : ?>
								—
							<?php endif; ?>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>

		<form method="post" style="margin-top:14px;">
			<?php wp_nonce_field( 'promptsyra_setup_categories_action', 'promptsyra_setup_nonce' ); ?>
			<button type="submit" name="promptsyra_create_categories" value="1" class="button button-primary">
				<?php esc_html_e( 'Create Any Missing Approved Categories Safely', 'promptsyra' ); ?>
			</button>
		</form>

		<h3 style="margin-top:24px;"><?php esc_html_e( 'Legacy Categories Flagged for Manual Editorial Review (Preserved, Never Auto-Deleted)', 'promptsyra' ); ?></h3>
		<p class="description">
			<?php esc_html_e( 'Obsolete demo categories are hidden from the PromptSyra header, homepage, and footer. Published articles inside legacy categories remain untouched below.', 'promptsyra' ); ?>
		</p>
		<ul style="list-style:disc;padding-left:20px;">
			<?php
			$legacy_found = false;
			foreach ( $all_cats as $cat_obj ) {
				if ( isset( $approved_cats[ $cat_obj->slug ] ) ) {
					continue;
				}
				$legacy_found = true;
				?>
				<li>
					<strong><?php echo esc_html( $cat_obj->name ); ?></strong>
					(<code><?php echo esc_html( $cat_obj->slug ); ?></code>) —
					<?php
					printf(
						/* translators: %d: post count */
						esc_html__( '%d post(s) preserved.', 'promptsyra' ),
						(int) $cat_obj->count
					);
					?>
					<a href="<?php echo esc_url( admin_url( 'edit.php?category_name=' . rawurlencode( $cat_obj->slug ) ) ); ?>">
						<?php esc_html_e( 'Review Posts', 'promptsyra' ); ?>
					</a>
				</li>
				<?php
			}
			if ( ! $legacy_found ) {
				echo '<li>' . esc_html__( 'No legacy categories detected.', 'promptsyra' ) . '</li>';
			}
			?>
		</ul>
	</div>
	<?php
}
