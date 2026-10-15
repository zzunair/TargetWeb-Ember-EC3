<?php
/**
 * Built-in SEO. Steps aside for SEO plugins; faceted noindex always applies.
 *
 * @package Forge
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter(
	'option_blogname',
	function ( $value ) {
		if ( '' !== trim( (string) $value ) ) {
			return $value;
		}
		$name = forge_opt( 'dealer_name' );
		return $name ? $name : 'Ember Outdoor Power';
	}
);

function forge_seo_plugin_active() {
	return defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'AIOSEO_VERSION' ) || defined( 'SEOPRESS_VERSION' ) || defined( 'THE_SEO_FRAMEWORK_VERSION' ) || defined( 'SLIM_SEO_VER' );
}

function forge_seo_enabled() {
	return forge_opt( 'seo_on' ) && ! forge_seo_plugin_active();
}

/* Faceted / utility URLs: noindex,follow — applies even with an SEO plugin. */
add_filter(
	'wp_robots',
	function ( $r ) {
		$facets = array( 'frg_cat', 'frg_brand', 'frg_stock', 'frg_deals', 'orderby', 'min_price', 'max_price', 'rating_filter', 'product_cat', 'product_tag' );
		foreach ( array_keys( $_GET ) as $k ) { // phpcs:ignore WordPress.Security.NonceVerification
			$k = sanitize_key( $k );
			if ( in_array( $k, $facets, true ) || 0 === strpos( $k, 'filter_' ) || 0 === strpos( $k, 'query_type_' ) ) {
				$r['noindex'] = true;
				$r['follow']  = true;
				unset( $r['index'] );
				return $r;
			}
		}
		if ( is_search() || is_404() ) {
			$r['noindex'] = true;
			$r['follow']  = true;
		}
		return $r;
	}
);

function forge_seo_description() {
	$d = '';
	if ( is_singular() ) {
		$d = has_excerpt() ? get_the_excerpt() : wp_strip_all_tags( strip_shortcodes( get_post_field( 'post_content' ) ) );
	} elseif ( is_tax() || is_category() || is_tag() ) {
		$d = wp_strip_all_tags( term_description() );
	}
	if ( ! $d ) {
		$d = forge_opt( 'seo_desc' );
	}
	if ( ! $d ) {
		$d = get_bloginfo( 'description' );
	}
	if ( ! $d ) {
		$name = forge_opt( 'dealer_name' ) ? forge_opt( 'dealer_name' ) : 'Ember Outdoor Power';
		$d    = $name . ' sells and services outdoor power equipment, including mowers, saws, generators, parts, and financing.';
	}
	return wp_trim_words( trim( preg_replace( '/\s+/', ' ', (string) $d ) ), 32, '…' );
}

function forge_seo_canonical() {
	if ( is_front_page() ) {
		return home_url( '/' );
	}
	if ( is_singular() ) {
		return get_permalink();
	}
	if ( is_tax() || is_category() || is_tag() ) {
		$l = get_term_link( get_queried_object() );
		return is_wp_error( $l ) ? '' : $l;
	}
	if ( forge_has_wc() && is_shop() ) {
		return wc_get_page_permalink( 'shop' );
	}
	if ( is_home() && get_option( 'page_for_posts' ) ) {
		return get_permalink( (int) get_option( 'page_for_posts' ) );
	}
	if ( is_search() || is_404() ) {
		return '';
	}
	return home_url( '/' );
}

function forge_seo_image() {
	if ( is_singular() && has_post_thumbnail() ) {
		return get_the_post_thumbnail_url( null, 'large' );
	}
	$id = (int) forge_opt( 'seo_image' );
	return $id ? wp_get_attachment_image_url( $id, 'large' ) : '';
}

function forge_seo_json( $data ) {
	wp_print_inline_script_tag( wp_json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP ), array( 'type' => 'application/ld+json' ) );
}

add_action(
	'wp_head',
	function () {
		if ( ! forge_seo_enabled() || is_customize_preview() ) {
			return;
		}
		remove_action( 'wp_head', 'rel_canonical' );
		$desc  = forge_seo_description();
		$canon = forge_seo_canonical();
		$title = wp_get_document_title();
		$img   = forge_seo_image();
		$type  = is_singular( 'product' ) ? 'product' : ( is_singular( 'post' ) ? 'article' : 'website' );
		if ( $canon ) {
			$paged = (int) get_query_var( 'paged' );
			if ( $paged > 1 && ! is_singular() ) {
				$canon = strtok( get_pagenum_link( $paged ), '?' );
			}
			echo '<link rel="canonical" href="' . esc_url( $canon ) . '">' . "\n";
		}
		if ( $desc ) {
			echo '<meta name="description" content="' . esc_attr( $desc ) . '">' . "\n";
		}
		$og = array( 'og:locale' => get_locale(), 'og:type' => $type, 'og:title' => $title, 'og:description' => $desc, 'og:url' => $canon, 'og:site_name' => get_bloginfo( 'name' ), 'og:image' => $img );
		foreach ( $og as $k => $v ) {
			if ( $v ) {
				echo '<meta property="' . esc_attr( $k ) . '" content="' . esc_attr( $v ) . '">' . "\n";
			}
		}
		$tw = array( 'twitter:card' => $img ? 'summary_large_image' : 'summary', 'twitter:title' => $title, 'twitter:description' => $desc, 'twitter:image' => $img );
		foreach ( $tw as $k => $v ) {
			if ( $v ) {
				echo '<meta name="' . esc_attr( $k ) . '" content="' . esc_attr( $v ) . '">' . "\n";
			}
		}

		if ( is_front_page() || is_page( 'contact' ) || is_page( 'about' ) ) {
			$same = array_values( array_filter( array( forge_opt( 'dealer_facebook' ), forge_opt( 'dealer_instagram' ), forge_opt( 'dealer_youtube' ) ) ) );
			$biz  = array_filter(
				array(
					'@context'  => 'https://schema.org',
					'@type'     => 'LocalBusiness',
					'@id'       => home_url( '/#business' ),
					'name'      => forge_opt( 'dealer_name' ) ? forge_opt( 'dealer_name' ) : get_bloginfo( 'name' ),
					'url'       => home_url( '/' ),
					'telephone' => forge_opt( 'dealer_phone' ),
					'email'     => forge_opt( 'dealer_email' ),
					'image'     => $img,
					'address'   => array_filter(
						array(
							'@type'           => 'PostalAddress',
							'streetAddress'   => forge_opt( 'dealer_address' ),
							'addressLocality' => forge_opt( 'dealer_city' ),
							'addressRegion'   => forge_opt( 'dealer_state' ),
							'postalCode'      => forge_opt( 'dealer_zip' ),
						)
					),
					'sameAs'    => $same,
				)
			);
			if ( count( $biz['address'] ) < 2 ) {
				unset( $biz['address'] );
			}
			forge_seo_json( $biz );
		}
		if ( is_front_page() ) {
			forge_seo_json(
				array(
					'@context'        => 'https://schema.org',
					'@type'           => 'WebSite',
					'name'            => get_bloginfo( 'name' ),
					'url'             => home_url( '/' ),
					'potentialAction' => array(
						'@type'       => 'SearchAction',
						'target'      => home_url( '/?s={search_term_string}&post_type=product' ),
						'query-input' => 'required name=search_term_string',
					),
				)
			);
		}
		if ( forge_has_wc() && is_singular( 'product' ) ) {
			$p = wc_get_product( get_queried_object_id() );
			if ( $p ) {
				$b   = forge_brand( $p->get_id() );
				$sch = array(
					'@context'    => 'https://schema.org',
					'@type'       => 'Product',
					'name'        => $p->get_name(),
					'url'         => get_permalink( $p->get_id() ),
					'sku'         => $p->get_sku(),
					'image'       => $img,
					'description' => wp_strip_all_tags( $p->get_short_description() ? $p->get_short_description() : $desc ),
					'brand'       => $b ? array( '@type' => 'Brand', 'name' => $b['name'] ) : null,
				);
				if ( ! forge_is_hidden_price( $p ) && '' !== $p->get_price() ) {
					$sch['offers'] = array(
						'@type'         => 'Offer',
						'url'           => get_permalink( $p->get_id() ),
						'priceCurrency' => get_woocommerce_currency(),
						'price'         => wc_format_decimal( $p->get_price(), wc_get_price_decimals() ),
						'availability'  => $p->is_in_stock() ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
					);
				}
				forge_seo_json( array_filter( $sch ) );
			}
		}
	},
	1
);

// Avoid duplicate Product schema from WooCommerce when ours is active.
add_filter(
	'woocommerce_structured_data_product',
	function ( $markup ) {
		return forge_seo_enabled() ? array() : $markup;
	}
);
