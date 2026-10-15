<?php
/**
 * Search results body.
 *
 * @package Forge
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $forge_search, $wp_query;
$forge_q    = isset( $forge_search ) && $forge_search ? $forge_search : get_search_query();
$forge_prod = 'product' === get_query_var( 'post_type' );
/* translators: %s: search term */
forge_page_hero( sprintf( __( 'Results for “%s”', 'forge' ), $forge_q ) );
?>
<main id="frg-main" class="frg-main">
	<div class="frg-wrap frg-inner">
		<div class="frg-searchbar"><?php forge_search_form( 'frg-s-page' ); ?></div>
		<?php if ( have_posts() ) : ?>
			<?php if ( $forge_prod ) : ?>
				<div class="frg-shopbar">
					<p class="woocommerce-result-count"><?php echo esc_html( sprintf( _n( '%s result', '%s results', (int) $wp_query->found_posts, 'forge' ), number_format_i18n( (int) $wp_query->found_posts ) ) ); ?></p>
					<?php forge_view_switch(); ?>
				</div>
			<?php endif; ?>
			<div class="<?php echo $forge_prod ? 'frg-grid' : 'frg-postgrid'; ?>"<?php echo $forge_prod ? ' data-frg-products data-frg-view="grid"' : ''; ?>>
				<?php
				while ( have_posts() ) {
					the_post();
					if ( 'product' === get_post_type() && forge_has_wc() ) {
						forge_product_card( get_the_ID() );
					} else {
						forge_post_card();
					}
				}
				?>
			</div>
			<div class="frg-pagination"><?php the_posts_pagination( array( 'prev_text' => '←', 'next_text' => '→' ) ); ?></div>
			<?php if ( $forge_prod ) : ?>
				<script>try{var v=localStorage.getItem("frgProductView");if(v==="list"||v==="grid"){var el=document.querySelector("[data-frg-products]");if(el)el.setAttribute("data-frg-view",v);}}catch(e){}</script>
			<?php endif; ?>
		<?php else : ?>
			<p class="frg-empty"><?php esc_html_e( 'No matches. Try a model number, brand or category.', 'forge' ); ?></p>
		<?php endif; ?>
	</div>
</main>
