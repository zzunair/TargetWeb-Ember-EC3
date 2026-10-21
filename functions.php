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
define( 'FORGE_DIR', get_template_directory() );
define( 'FORGE_URI', get_template_directory_uri() );

foreach ( array( 'fields', 'helpers', 'customizer', 'assets', 'setup', 'woocommerce', 'home', 'ajax', 'seo', 'maintenance' ) as $forge_inc ) {
	require_once FORGE_DIR . '/inc/' . $forge_inc . '.php';
}
unset( $forge_inc );
