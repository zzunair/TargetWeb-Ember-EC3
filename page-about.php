<?php
/**
 * About page.
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
			<div class="frg-split">
				<div class="entry-content">
					<?php echo wp_kses_post( wpautop( forge_opt( 'about_body' ) ) ); ?>
					<?php the_content(); ?>
				</div>
				<?php forge_dealer_card(); ?>
			</div>
		</div>
	</main>
	<?php
}
get_footer();
