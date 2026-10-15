<?php
/**
 * Single post.
 *
 * @package Forge
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();
while ( have_posts() ) {
	the_post();
	?>
	<main id="frg-main" class="frg-main frg-single-post">
		<div class="frg-wrap frg-inner">
			<span class="frg-eyebrow"><?php echo esc_html( get_the_date() ); ?></span>
			<h1><?php echo esc_html( get_the_title() ); ?></h1>
			<?php if ( has_post_thumbnail() ) : ?>
				<div class="frg-single-post__img"><?php echo wp_kses_post( get_the_post_thumbnail( null, 'large' ) ); ?></div>
			<?php endif; ?>
			<div class="entry-content"><?php the_content(); ?></div>
			<hr>
			<div style="display:flex;justify-content:space-between;gap:20px">
				<span><?php previous_post_link( '%link', '← %title' ); ?></span>
				<span><?php next_post_link( '%link', '%title →' ); ?></span>
			</div>
		</div>
	</main>
	<?php
}
get_footer();
