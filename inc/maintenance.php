<?php
/**
 * Maintenance mode.
 *
 * @package Forge
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action(
	'template_redirect',
	function () {
		if ( ! forge_opt( 'mnt_on' ) || is_customize_preview() || current_user_can( 'manage_options' ) ) {
			return;
		}
		status_header( 503 );
		header( 'Retry-After: 3600' );
		nocache_headers();
		add_filter( 'wp_robots', 'wp_robots_no_robots' );
		include FORGE_DIR . '/maintenance.php';
		exit;
	},
	1
);
