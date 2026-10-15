<?php
/**
 * Blog and product archives.
 *
 * @package Forge
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();
$forge_title = is_home() && ! is_front_page() ? get_the_title( (int) get_option( 'page_for_posts' ) ) : ( is_archive() ? get_the_archive_title() : __( 'Journal', 'forge' ) );
forge_page_hero( $forge_title ? $forge_title : __( 'Journal', 'forge' ), is_archive() ? wp_strip_all_tags( get_the_archive_description() ) : '' );
?>
<main id="frg-main" class="frg-main">
	<div class="frg-wrap frg-inner">
		<?php if ( have_posts() ) : ?>
			<div class="frg-postgrid">
				<?php
				while ( have_posts() ) {
					the_post();
					forge_post_card();
				}
				?>
			</div>
			<div class="frg-pagination"><?php the_posts_pagination( array( 'prev_text' => '←', 'next_text' => '→' ) ); ?></div>
		<?php else : ?>
			<p class="frg-empty"><?php esc_html_e( 'Nothing here yet.', 'forge' ); ?></p>
		<?php endif; ?>
	</div>
</main>
<?php
get_footer();
