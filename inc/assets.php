<?php
/**
 * Assets: fonts, CSS variables, scripts, analytics, Constellation.
 *
 * @package Forge
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function forge_fonts_url() {
	$h   = array_key_exists( forge_opt( 'font_heading' ), forge_heading_fonts() ) ? forge_opt( 'font_heading' ) : 'Barlow Condensed';
	$b   = array_key_exists( forge_opt( 'font_body' ), forge_body_fonts() ) ? forge_opt( 'font_body' ) : 'Source Sans 3';
	$fam = array();
	foreach ( array_unique( array( $h, $b ) ) as $n ) {
		$fam[] = 'family=' . str_replace( ' ', '+', $n ) . ':wght@400;500;600;700';
	}
	return 'https://fonts.googleapis.com/css2?' . implode( '&', $fam ) . '&display=swap';
}

function forge_css_vars() {
	$h = array_key_exists( forge_opt( 'font_heading' ), forge_heading_fonts() ) ? forge_opt( 'font_heading' ) : 'Barlow Condensed';
	$b = array_key_exists( forge_opt( 'font_body' ), forge_body_fonts() ) ? forge_opt( 'font_body' ) : 'Source Sans 3';
	$v = array(
		'--frg-font-heading' => "'{$h}','Arial Narrow',sans-serif",
		'--frg-font-body'    => "'{$b}',system-ui,sans-serif",
		'--frg-fs-base'      => absint( forge_opt( 'fs_base' ) ) . 'px',
		'--frg-fs-h1'        => absint( forge_opt( 'fs_h1' ) ) . 'px',
		'--frg-fs-h2'        => absint( forge_opt( 'fs_h2' ) ) . 'px',
		'--frg-fs-h3'        => absint( forge_opt( 'fs_h3' ) ) . 'px',
		'--frg-fs-h4'        => absint( forge_opt( 'fs_h4' ) ) . 'px',
		'--frg-fs-section'   => absint( forge_opt( 'fs_section' ) ) . 'px',
		'--frg-accent'       => sanitize_hex_color( forge_opt( 'color_accent' ) ),
		'--frg-bg'           => sanitize_hex_color( forge_opt( 'color_bg' ) ),
		'--frg-surface'      => sanitize_hex_color( forge_opt( 'color_surface' ) ),
		'--frg-text'         => sanitize_hex_color( forge_opt( 'color_text' ) ),
		'--frg-muted'        => sanitize_hex_color( forge_opt( 'color_muted' ) ),
	);
	$css = '';
	foreach ( $v as $k => $val ) {
		if ( $val ) {
			$css .= $k . ':' . $val . ';';
		}
	}
	return ':root{' . $css . '}';
}

add_action(
	'wp_enqueue_scripts',
	function () {
		wp_enqueue_style( 'forge-fonts', forge_fonts_url(), array(), null ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters
		wp_enqueue_style( 'forge', get_stylesheet_uri(), array(), FORGE_VERSION );
		wp_add_inline_style( 'forge', forge_css_vars() );

		wp_enqueue_script( 'forge', FORGE_URI . '/assets/js/forge.js', array( 'jquery' ), FORGE_VERSION, array( 'in_footer' => true, 'strategy' => 'defer' ) );
		wp_localize_script(
			'forge',
			'forgeData',
			array(
				'ajax'  => admin_url( 'admin-ajax.php' ),
				'nonce' => wp_create_nonce( 'forge_form' ),
				'i18n'  => array(
					'compareMax' => __( 'You can compare up to 4 products.', 'forge' ),
					'compare'    => __( 'Compare products', 'forge' ),
					'saved'      => __( 'Saved items', 'forge' ),
					'noSaved'    => __( 'Nothing saved yet. Tap the heart on any product.', 'forge' ),
					'recent'     => __( 'Recently viewed', 'forge' ),
					'error'      => __( 'Something went wrong. Please try again.', 'forge' ),
					'sending'    => __( 'Sending…', 'forge' ),
					'required'   => __( 'Please fill in the required fields.', 'forge' ),
					'compareN'   => __( 'products selected', 'forge' ),
				),
			)
		);

	}
);

/**
 * Optional Constellation dealer widget. Same tag the first ecommerce theme prints.
 * Lead-form API settings stay in the TargetWeb CRM plugin.
 */
function forge_constellation_widget() {
	$client_id = preg_replace( '/[^A-Za-z0-9_-]/', '', (string) forge_opt( 'constellation_id' ) );
	if ( ! $client_id ) {
		return;
	}
	printf(
		'<script src="https://app.constellationdealer.com/widget.js" id="target-crm-widget" data-client-id="%s" defer></script>' . "\n",
		esc_attr( $client_id )
	);
}
add_action( 'wp_head', 'forge_constellation_widget', 20 );

// GA4 + GTM.
add_action(
	'wp_head',
	function () {
		$gtm = preg_replace( '/[^A-Za-z0-9\-]/', '', (string) forge_opt( 'gtm' ) );
		$ga  = preg_replace( '/[^A-Za-z0-9\-]/', '', (string) forge_opt( 'ga4' ) );
		if ( $gtm ) {
			wp_print_inline_script_tag( "(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer'," . wp_json_encode( $gtm ) . ');' );
		}
		if ( $ga ) {
			echo '<script async src="' . esc_url( 'https://www.googletagmanager.com/gtag/js?id=' . $ga ) . '"></script>' . "\n"; // phpcs:ignore WordPress.WP.EnqueuedResources
			wp_print_inline_script_tag( 'window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag("js",new Date());gtag("config",' . wp_json_encode( $ga ) . ');' );
		}
	},
	3
);

add_action(
	'wp_body_open',
	function () {
		$gtm = preg_replace( '/[^A-Za-z0-9\-]/', '', (string) forge_opt( 'gtm' ) );
		if ( $gtm ) {
			echo '<noscript><iframe src="' . esc_url( 'https://www.googletagmanager.com/ns.html?id=' . $gtm ) . '" height="0" width="0" style="display:none;visibility:hidden" title="gtm"></iframe></noscript>';
		}
	}
);
