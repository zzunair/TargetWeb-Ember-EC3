<?php
/**
 * Service page.
 *
 * @package Forge
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();
while ( have_posts() ) {
	the_post();
	forge_page_hero( get_the_title(), forge_opt( 'service_intro' ) );
	?>
	<main id="frg-main" class="frg-main">
		<div class="frg-wrap frg-inner">
			<div class="frg-split">
				<?php forge_dealer_card(); ?>
				<div><?php forge_form( 'service' ); ?></div>
			</div>
			<div class="entry-content" style="margin-top:56px"><?php the_content(); ?></div>
		</div>
	</main>
	<?php
}
get_footer();
