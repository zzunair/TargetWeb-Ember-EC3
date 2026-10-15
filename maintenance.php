<?php
/**
 * Maintenance page (served with 503).
 *
 * @package Forge
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$forge_end = forge_end_ms( 'mnt_end' );
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo esc_html( forge_opt( 'mnt_title' ) . ' – ' . get_bloginfo( 'name' ) ); ?></title>
<?php wp_head(); ?>
</head>
<body class="frg-maintenance">
<main class="frg-maint">
	<div class="frg-maint__box">
		<?php forge_logo(); ?>
		<h1><?php echo esc_html( forge_opt( 'mnt_title' ) ); ?></h1>
		<p><?php echo esc_html( wp_strip_all_tags( forge_opt( 'mnt_text' ) ) ); ?></p>
		<?php if ( forge_opt( 'mnt_phone' ) ) : ?>
			<a class="frg-maint__phone" href="<?php echo esc_url( 'tel:' . preg_replace( '/[^0-9+]/', '', forge_opt( 'mnt_phone' ) ) ); ?>"><?php echo esc_html( forge_opt( 'mnt_phone' ) ); ?></a>
		<?php endif; ?>
		<?php if ( $forge_end && $forge_end > time() * 1000 ) : ?>
			<div class="frg-countdown" data-frg-countdown="<?php echo esc_attr( $forge_end ); ?>">
				<div><b data-u="d">0</b><span><?php esc_html_e( 'Days', 'forge' ); ?></span></div>
				<div><b data-u="h">0</b><span><?php esc_html_e( 'Hrs', 'forge' ); ?></span></div>
				<div><b data-u="m">0</b><span><?php esc_html_e( 'Min', 'forge' ); ?></span></div>
				<div><b data-u="s">0</b><span><?php esc_html_e( 'Sec', 'forge' ); ?></span></div>
			</div>
		<?php endif; ?>
	</div>
</main>
<?php wp_footer(); ?>
</body>
</html>
