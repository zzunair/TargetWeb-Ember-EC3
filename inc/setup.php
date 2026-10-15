<?php
/**
 * Theme setup, menus, activation pages, body classes.
 *
 * @package Forge
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action(
	'after_setup_theme',
	function () {
		load_theme_textdomain( 'forge', FORGE_DIR . '/languages' );
		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
		add_theme_support( 'custom-logo', array( 'height' => 80, 'width' => 280, 'flex-width' => true, 'flex-height' => true ) );
		add_theme_support( 'responsive-embeds' );
		add_theme_support( 'woocommerce', array( 'thumbnail_image_width' => 640, 'single_image_width' => 960, 'product_grid' => array( 'default_columns' => 4, 'min_columns' => 2, 'max_columns' => 4 ) ) );
		add_theme_support( 'wc-product-gallery-zoom' );
		add_theme_support( 'wc-product-gallery-lightbox' );
		add_theme_support( 'wc-product-gallery-slider' );
		register_nav_menus(
			array(
				'forge_secondary' => __( 'Secondary bar', 'forge' ),
				'forge_deals'     => __( 'Deals dropdown', 'forge' ),
				'forge_footer'    => __( 'Footer links', 'forge' ),
			)
		);
		add_image_size( 'forge_tile', 900, 1200, true );
	}
);

function forge_create_pages() {
	if ( get_option( 'forge_pages_created' ) ) {
		return;
	}
	foreach ( array( 'about' => 'About', 'contact' => 'Contact', 'service' => 'Service', 'financing' => 'Financing' ) as $slug => $title ) {
		if ( get_page_by_path( $slug ) ) {
			continue;
		}
		wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => $title, 'post_name' => $slug, 'post_content' => '' ) );
	}
	update_option( 'forge_pages_created', 1 );
}
add_action( 'after_switch_theme', 'forge_create_pages' );
add_action( 'init', 'forge_create_pages' );

add_filter(
	'body_class',
	function ( $c ) {
		if ( forge_opt( 'announce_on' ) && forge_opt( 'announce_text' ) ) {
			$c[] = 'frg-has-announce';
		}
		return $c;
	}
);

add_filter( 'excerpt_length', function () { return 28; } );
add_filter( 'excerpt_more', function () { return '…'; } );
add_filter( 'use_default_gallery_style', '__return_false' );
