<?php
/**
 * Forge theme bootstrap.
 *
 * @package Forge
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'FORGE_VERSION', '1.0.6' );

if ( ! function_exists( 'tw_main_menu_args' ) ) {
	function tw_main_menu_args( $args = array() ) {
		$menu = wp_get_nav_menu_object( 'Main Menu' );
		if ( ! $menu ) {
			$menu = wp_get_nav_menu_object( 'main-menu' );
		}
		if ( $menu ) {
			$args['menu'] = (int) $menu->term_id;
		}
		return $args;
	}
}
define( 'FORGE_DIR', get_template_directory() );
define( 'FORGE_URI', get_template_directory_uri() );

foreach ( array( 'fields', 'helpers', 'customizer', 'assets', 'setup', 'woocommerce', 'home', 'ajax', 'seo', 'maintenance' ) as $forge_inc ) {
	require_once FORGE_DIR . '/inc/' . $forge_inc . '.php';
}
unset( $forge_inc );
