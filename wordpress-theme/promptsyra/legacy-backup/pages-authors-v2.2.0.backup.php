<?php
/**
 * Backup of PromptSyra v2.2.0 page.php and author.php prior to v2.3.0
 * Author Integration, Premium About Us, Contact Form, and Essential Legal Pages update.
 *
 * @package PromptSyra
 * @version 2.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
--- ORIGINAL v2.2.0 page.php ---
<main id="ps-main-content" class="ps-front-main">
	<?php while ( have_posts() ) : the_post(); ?>
		<section class="ps-archive-banner">
			<div class="ps-container">
				<h1 class="ps-section-title"><?php the_title(); ?></h1>
			</div>
		</section>
		<section class="ps-featured-section">
			<div class="ps-container" style="max-width:860px;">
				<article <?php post_class(); ?>>
					<?php the_content(); ?>
				</article>
			</div>
		</section>
	<?php endwhile; ?>
</main>
*/
