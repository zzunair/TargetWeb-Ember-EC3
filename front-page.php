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
		$forge_fn = 'forge_home_' . $forge_s;
		if ( function_exists( $forge_fn ) ) {
			$forge_fn();
		}
	}
	?>
</main>
<?php
get_footer();
