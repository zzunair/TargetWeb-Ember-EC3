<?php
/**
 * 404.
 *
 * @package Forge
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();
?>
<main id="frg-main" class="frg-main">
	<div class="frg-wrap frg-404">
		<span class="frg-eyebrow">404</span>
		<h1><?php esc_html_e( 'Page not found', 'forge' ); ?></h1>
		<p class="frg-muted"><?php esc_html_e( 'That page has moved or no longer exists. Try a search or head back to the shop.', 'forge' ); ?></p>
		<?php forge_search_form( 'frg-s-404' ); ?>
		<a class="frg-btn" href="<?php echo esc_url( forge_shop_url() ); ?>"><?php esc_html_e( 'Shop equipment', 'forge' ); ?></a>
	</div>
</main>
<?php
get_footer();
