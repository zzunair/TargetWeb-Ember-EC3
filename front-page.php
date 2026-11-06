<?php
/**
 * Homepage.
 *
 * @package Forge
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();
?>
<main id="frg-main" class="frg-home">
	<?php
	foreach ( forge_home_sections() as $forge_s ) {
		// A child can replace one section with template-parts/home/{section}.php.
		$forge_part = locate_template( 'template-parts/home/' . $forge_s . '.php' );
		if ( $forge_part ) {
			include $forge_part;
			continue;
		}
		$forge_fn = 'forge_home_' . $forge_s;
		if ( function_exists( $forge_fn ) ) {
			$forge_fn();
		}
	}
	?>
</main>
<?php
get_footer();
