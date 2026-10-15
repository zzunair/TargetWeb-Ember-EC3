<?php
/**
 * Homepage section renderers.
 *
 * @package Forge
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function forge_home_sections() {
	$o = array();
	$i = 0;
	foreach ( forge_home_keys() as $k => $label ) {
		if ( forge_opt( "home_{$k}_on" ) ) {
			$o[ $k ] = array( (int) forge_opt( "home_{$k}_order" ), $i );
		}
		$i++;
	}
	uasort(
		$o,
		function ( $a, $b ) {
			return $a[0] === $b[0] ? $a[1] <=> $b[1] : $a[0] <=> $b[0];
		}
	);
	return array_keys( $o );
}

function forge_btn( $label, $url, $class = '' ) {
	if ( ! $label ) {
		return '';
	}
	return '<a class="frg-btn ' . esc_attr( $class ) . '" href="' . esc_url( $url ) . '">' . esc_html( $label ) . '</a>';
}

function forge_home_hero() {
	$slides = array();
	for ( $n = 1; $n <= 3; $n++ ) {
		if ( forge_opt( "hero_{$n}_title" ) !== '' ) {
			$slides[] = $n;
		}
	}
	if ( ! $slides ) {
		return;
	}
	echo '<section class="frg-hero" data-frg-hero aria-roledescription="carousel" aria-label="' . esc_attr__( 'Featured', 'forge' ) . '">';
	foreach ( $slides as $i => $n ) {
		$tag = 0 === $i ? 'h1' : 'h2';
		echo '<div class="frg-hero__slide' . ( 0 === $i ? ' is-active' : '' ) . '" role="group" aria-roledescription="slide">';
		$img = forge_img( forge_opt( "hero_{$n}_image" ), 'full', array( 'loading' => 0 === $i ? 'eager' : 'lazy' ) );
		if ( $img ) {
			echo '<div class="frg-hero__media">' . $img . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput
		}
		echo '<div class="frg-wrap"><div class="frg-hero__copy">';
		if ( forge_opt( "hero_{$n}_eyebrow" ) ) {
			echo '<span class="frg-eyebrow">' . esc_html( forge_opt( "hero_{$n}_eyebrow" ) ) . '</span>';
		}
		echo '<' . $tag . '>' . esc_html( forge_opt( "hero_{$n}_title" ) ) . '</' . $tag . '>'; // phpcs:ignore WordPress.Security.EscapeOutput
		if ( forge_opt( "hero_{$n}_text" ) ) {
			echo '<p>' . esc_html( forge_opt( "hero_{$n}_text" ) ) . '</p>';
		}
		echo '<div class="frg-hero__btns">';
		echo forge_btn( forge_opt( "hero_{$n}_b1_label" ), forge_url( "hero_{$n}_b1_url", forge_shop_url() ) ); // phpcs:ignore WordPress.Security.EscapeOutput
		echo forge_btn( forge_opt( "hero_{$n}_b2_label" ), forge_url( "hero_{$n}_b2_url", forge_page_url( 'service' ) ), 'frg-btn--ghost' ); // phpcs:ignore WordPress.Security.EscapeOutput
		echo '</div></div></div></div>';
	}
	if ( count( $slides ) > 1 ) {
		echo '<div class="frg-hero__nav"><div class="frg-wrap">';
		foreach ( $slides as $i => $n ) {
			/* translators: %d: slide number */
			echo '<button type="button" class="frg-dot' . ( 0 === $i ? ' is-active' : '' ) . '" data-frg-dot="' . esc_attr( $i ) . '" aria-label="' . esc_attr( sprintf( __( 'Slide %d', 'forge' ), $i + 1 ) ) . '"></button>';
		}
		echo '<div class="frg-hero__arrows"><button type="button" class="frg-iconlink" data-frg-prev aria-label="' . esc_attr__( 'Previous', 'forge' ) . '">' . forge_icon( 'left' ) . '</button><button type="button" class="frg-iconlink" data-frg-next aria-label="' . esc_attr__( 'Next', 'forge' ) . '">' . forge_icon( 'arrow' ) . '</button></div>'; // phpcs:ignore WordPress.Security.EscapeOutput
		echo '</div></div>';
	}
	echo '</section>';
}

function forge_home_trust() {
	$icons = array( 'truck', 'wrench', 'shield' );
	echo '<section class="frg-trust"><div class="frg-wrap frg-trust__grid">';
	for ( $n = 1; $n <= 3; $n++ ) {
		if ( ! forge_opt( "trust_{$n}_title" ) ) {
			continue;
		}
		echo '<div class="frg-trust__item">' . forge_icon( $icons[ $n - 1 ], 32 ) . '<div><b>' . esc_html( forge_opt( "trust_{$n}_title" ) ) . '</b><span>' . esc_html( forge_opt( "trust_{$n}_text" ) ) . '</span></div></div>'; // phpcs:ignore WordPress.Security.EscapeOutput
	}
	echo '</div></section>';
}

function forge_home_tiles() {
	echo '<section class="frg-section"><div class="frg-wrap"><div class="frg-tiles">';
	for ( $n = 1; $n <= 4; $n++ ) {
		if ( ! forge_opt( "tile_{$n}_title" ) ) {
			continue;
		}
		echo '<a class="frg-tile" href="' . esc_url( forge_url( "tile_{$n}_url", forge_shop_url() ) ) . '">';
		echo forge_img( forge_opt( "tile_{$n}_image" ), 'forge_tile', array( 'loading' => 'lazy' ) ); // phpcs:ignore WordPress.Security.EscapeOutput
		echo '<div class="frg-tile__copy"><h3>' . esc_html( forge_opt( "tile_{$n}_title" ) ) . '</h3><span>' . esc_html( forge_opt( "tile_{$n}_label" ) ) . '</span></div></a>';
	}
	echo '</div></div></section>';
}

function forge_home_countdown() {
	if ( ! forge_opt( 'cd_title' ) ) {
		return;
	}
	$end = forge_end_ms( 'cd_end' );
	echo '<section class="frg-offer">';
	$img = forge_img( forge_opt( 'cd_image' ), 'full', array( 'class' => 'frg-bg', 'loading' => 'lazy' ) );
	echo $img; // phpcs:ignore WordPress.Security.EscapeOutput
	echo '<div class="frg-wrap frg-offer__inner"><div>';
	if ( forge_opt( 'cd_eyebrow' ) ) {
		echo '<span class="frg-eyebrow">' . esc_html( forge_opt( 'cd_eyebrow' ) ) . '</span>';
	}
	echo '<h2 class="frg-section-title">' . esc_html( forge_opt( 'cd_title' ) ) . '</h2><p>' . esc_html( forge_opt( 'cd_text' ) ) . '</p></div><div class="frg-offer__cta">';
	if ( $end ) {
		echo '<div class="frg-countdown" data-frg-countdown="' . esc_attr( $end ) . '"><div><b data-u="d">0</b><span>' . esc_html__( 'Days', 'forge' ) . '</span></div><div><b data-u="h">0</b><span>' . esc_html__( 'Hrs', 'forge' ) . '</span></div><div><b data-u="m">0</b><span>' . esc_html__( 'Min', 'forge' ) . '</span></div><div><b data-u="s">0</b><span>' . esc_html__( 'Sec', 'forge' ) . '</span></div></div>';
	}
	echo forge_btn( forge_opt( 'cd_btn' ), forge_url( 'cd_url', forge_deals_url() ) ); // phpcs:ignore WordPress.Security.EscapeOutput
	echo '</div></div></section>';
}

function forge_home_tabs() {
	if ( ! forge_has_wc() ) {
		return;
	}
	$tabs = array();
	for ( $n = 1; $n <= 3; $n++ ) {
		if ( forge_opt( "tab_{$n}_label" ) ) {
			$tabs[ $n ] = forge_query_products( forge_opt( "tab_{$n}_source" ), forge_opt( "tab_{$n}_slug" ), 4 );
		}
	}
	if ( ! $tabs ) {
		return;
	}
	echo '<section class="frg-section"><div class="frg-wrap frg-tabs" data-frg-tabs><div class="frg-head"><h2>' . esc_html( forge_opt( 'tabs_title' ) ) . '</h2><div class="frg-tabs__nav" role="tablist">';
	$first = true;
	foreach ( $tabs as $n => $ids ) {
		echo '<button type="button" role="tab" class="frg-tab' . ( $first ? ' is-active' : '' ) . '" id="frg-tab-' . esc_attr( $n ) . '" aria-controls="frg-panel-' . esc_attr( $n ) . '" aria-selected="' . ( $first ? 'true' : 'false' ) . '">' . esc_html( forge_opt( "tab_{$n}_label" ) ) . '</button>';
		$first = false;
	}
	echo '</div></div>';
	$first = true;
	foreach ( $tabs as $n => $ids ) {
		echo '<div class="frg-panel" role="tabpanel" id="frg-panel-' . esc_attr( $n ) . '" aria-labelledby="frg-tab-' . esc_attr( $n ) . '"' . ( $first ? '' : ' hidden' ) . '>';
		if ( $ids ) {
			echo '<div class="frg-grid">' . forge_cards_html( $ids ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput
		} else {
			echo '<p class="frg-empty">' . esc_html__( 'No products to show yet.', 'forge' ) . '</p>';
		}
		echo '</div>';
		$first = false;
	}
	echo '<p style="margin:40px 0 0"><a class="frg-link" href="' . esc_url( forge_shop_url() ) . '">' . esc_html__( 'View all equipment', 'forge' ) . ' ' . forge_icon( 'arrow', 16 ) . '</a></p></div></section>'; // phpcs:ignore WordPress.Security.EscapeOutput
}

function forge_home_categories() {
	$cards = array();
	if ( 'manual' === forge_opt( 'cats_mode' ) ) {
		for ( $n = 1; $n <= 8; $n++ ) {
			if ( forge_opt( "cat_{$n}_title" ) ) {
				$cards[] = array( 'title' => forge_opt( "cat_{$n}_title" ), 'img' => forge_img( forge_opt( "cat_{$n}_image" ), 'large', array( 'loading' => 'lazy' ) ), 'url' => forge_url( "cat_{$n}_url", forge_shop_url() ) );
			}
		}
	} elseif ( forge_has_wc() ) {
		$terms = get_terms( array( 'taxonomy' => 'product_cat', 'parent' => 0, 'hide_empty' => true, 'number' => 8, 'exclude' => array( (int) get_option( 'default_product_cat' ) ) ) );
		if ( ! is_wp_error( $terms ) ) {
			foreach ( $terms as $t ) {
				$cards[] = array( 'title' => $t->name, 'img' => forge_img( get_term_meta( $t->term_id, 'thumbnail_id', true ), 'large', array( 'loading' => 'lazy' ) ), 'url' => get_term_link( $t ) );
			}
		}
	}
	if ( ! $cards ) {
		return;
	}
	echo '<section class="frg-section"><div class="frg-wrap"><div class="frg-head"><h2>' . esc_html( forge_opt( 'cats_title' ) ) . '</h2></div><div class="frg-catgrid">';
	foreach ( $cards as $c ) {
		echo '<a class="frg-catcard" href="' . esc_url( $c['url'] ) . '">' . $c['img'] . '<div class="frg-catcard__copy"><h3>' . esc_html( $c['title'] ) . '</h3><span>' . esc_html__( 'Explore', 'forge' ) . '</span></div></a>'; // phpcs:ignore WordPress.Security.EscapeOutput
	}
	echo '</div></div></section>';
}

function forge_home_story() {
	if ( ! forge_opt( 'story_title' ) ) {
		return;
	}
	echo '<section class="frg-section"><div class="frg-wrap"><div class="frg-story"><div class="frg-story__media">';
	echo forge_img( forge_opt( 'story_image' ), 'large', array( 'class' => 'frg-bg', 'loading' => 'lazy' ) ); // phpcs:ignore WordPress.Security.EscapeOutput
	echo '</div><div class="frg-story__copy">';
	if ( forge_opt( 'story_eyebrow' ) ) {
		echo '<span class="frg-eyebrow">' . esc_html( forge_opt( 'story_eyebrow' ) ) . '</span>';
	}
	echo '<h2>' . esc_html( forge_opt( 'story_title' ) ) . '</h2><p>' . esc_html( wp_strip_all_tags( forge_opt( 'story_text' ) ) ) . '</p>';
	echo forge_btn( forge_opt( 'story_btn' ), forge_url( 'story_url', forge_page_url( 'about' ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput
	echo '</div></div></div></section>';
}

function forge_home_journal() {
	$q = new WP_Query( array( 'post_type' => 'post', 'posts_per_page' => (int) forge_opt( 'journal_count' ), 'ignore_sticky_posts' => true, 'no_found_rows' => true ) );
	if ( ! $q->have_posts() ) {
		return;
	}
	$blog = get_option( 'page_for_posts' ) ? get_permalink( (int) get_option( 'page_for_posts' ) ) : home_url( '/blog/' );
	echo '<section class="frg-section"><div class="frg-wrap"><div class="frg-head"><h2>' . esc_html( forge_opt( 'journal_title' ) ) . '</h2><a class="frg-link" href="' . esc_url( $blog ) . '">' . esc_html__( 'All articles', 'forge' ) . ' ' . forge_icon( 'arrow', 16 ) . '</a></div><div class="frg-postgrid">'; // phpcs:ignore WordPress.Security.EscapeOutput
	while ( $q->have_posts() ) {
		$q->the_post();
		forge_post_card();
	}
	wp_reset_postdata();
	echo '</div></div></section>';
}

function forge_home_newsletter() {
	forge_newsletter_section();
}
