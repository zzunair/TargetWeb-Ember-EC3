<?php
/**
 * Generic page (also cart, checkout, account).
 *
 * @package Forge
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();
while ( have_posts() ) {
	the_post();
	forge_page_hero( get_the_title() );
	?>
	<main id="frg-main" class="frg-main">
		<div class="frg-wrap frg-inner">
			<div class="entry-content"><?php the_content(); ?></div>
		</div>
	</main>
	<?php
}
get_footer();
