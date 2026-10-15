<?php
/**
 * Financing page.
 *
 * @package Forge
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();
$forge_steps = array_filter( array_map( 'trim', explode( "\n", wp_strip_all_tags( forge_opt( 'financing_steps' ) ) ) ) );
while ( have_posts() ) {
	the_post();
	forge_page_hero( get_the_title(), forge_opt( 'financing_intro' ) );
	?>
	<main id="frg-main" class="frg-main">
		<div class="frg-wrap frg-inner">
			<?php if ( $forge_steps ) : ?>
				<ol class="frg-steps">
					<?php foreach ( $forge_steps as $forge_st ) : ?>
						<li><?php echo esc_html( $forge_st ); ?></li>
					<?php endforeach; ?>
				</ol>
			<?php endif; ?>
			<p style="margin-top:40px">
				<?php echo forge_btn( __( 'Apply for financing', 'forge' ), forge_url( 'financing_url', forge_page_url( 'contact' ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</p>
			<div class="entry-content" style="margin-top:40px"><?php the_content(); ?></div>
		</div>
	</main>
	<?php
}
get_footer();
