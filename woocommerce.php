<?php
/**
 * WooCommerce shop, product and taxonomy archives.
 *
 * @package Forge
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();
if ( is_search() ) {
	get_template_part( 'template-parts/search' );
} else {
	?>
	<main id="frg-main" class="frg-main">
		<div class="frg-wrap">
			<?php woocommerce_content(); ?>
		</div>
	</main>
	<?php
}
get_footer();
