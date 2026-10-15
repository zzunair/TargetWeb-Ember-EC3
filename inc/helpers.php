<?php
/**
 * Helpers: options, icons, products, cards, forms.
 *
 * @package Forge
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function forge_opt( $key ) {
	$idx     = forge_field_index();
	$default = isset( $idx[ $key ] ) ? $idx[ $key ]['default'] : '';
	return get_theme_mod( $key, $default );
}

function forge_has_wc() {
	return class_exists( 'WooCommerce' );
}

function forge_shop_url() {
	return forge_has_wc() ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
}

function forge_url( $key, $fallback ) {
	$u = forge_opt( $key );
	return $u ? $u : $fallback;
}

function forge_page_url( $slug ) {
	$p = get_page_by_path( $slug );
	return $p ? get_permalink( $p ) : home_url( '/' . $slug . '/' );
}

function forge_phone_href() {
	$p = preg_replace( '/[^0-9+]/', '', (string) forge_opt( 'dealer_phone' ) );
	return $p ? 'tel:' . $p : '';
}

function forge_form_recipient() {
	foreach ( array( forge_opt( 'form_email' ), forge_opt( 'dealer_email' ), get_option( 'admin_email' ) ) as $e ) {
		if ( is_email( $e ) ) {
			return $e;
		}
	}
	return get_option( 'admin_email' );
}

/** Local timestamp (ms) from a Customizer datetime string, or 0. */
function forge_end_ms( $key ) {
	$v = forge_opt( $key );
	if ( ! $v ) {
		return 0;
	}
	try {
		$d = new DateTime( $v, wp_timezone() );
	} catch ( Exception $e ) {
		return 0;
	}
	return $d->getTimestamp() * 1000;
}

function forge_icon( $name, $size = 20 ) {
	$p = array(
		'search'  => '<circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>',
		'user'    => '<circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/>',
		'cart'    => '<path d="M3 4h3l2.5 11h10L21 7H7"/><circle cx="10" cy="19.5" r="1.3"/><circle cx="18" cy="19.5" r="1.3"/>',
		'heart'   => '<path d="M12 20s-7.5-4.6-7.5-10A4.5 4.5 0 0 1 12 7a4.5 4.5 0 0 1 7.5 3c0 5.4-7.5 10-7.5 10z"/>',
		'compare' => '<path d="M7 4v16M3 8l4-4 4 4M17 20V4M13 16l4 4 4-4"/>',
		'menu'    => '<path d="M4 7h16M4 12h16M4 17h16"/>',
		'close'   => '<path d="M6 6l12 12M18 6 6 18"/>',
		'arrow'   => '<path d="M5 12h14M13 6l6 6-6 6"/>',
		'left'    => '<path d="M19 12H5M11 6l-6 6 6 6"/>',
		'chev'    => '<path d="m6 9 6 6 6-6"/>',
		'shop'    => '<rect x="4" y="4" width="7" height="7"/><rect x="13" y="4" width="7" height="7"/><rect x="4" y="13" width="7" height="7"/><rect x="13" y="13" width="7" height="7"/>',
		'truck'   => '<path d="M3 6h11v10H3zM14 10h4l3 3v3h-7"/><circle cx="7" cy="18" r="1.6"/><circle cx="17" cy="18" r="1.6"/>',
		'shield'  => '<path d="M12 3l8 3v6c0 5-3.5 8-8 9-4.5-1-8-4-8-9V6z"/>',
		'wrench'  => '<path d="M14 6a4 4 0 0 0 5 5l-9 9a2.1 2.1 0 0 1-3-3l9-9z"/>',
		'eye'     => '<path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/>',
		'grid'    => '<rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/>',
		'list'    => '<path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/>',
	);
	if ( ! isset( $p[ $name ] ) ) {
		return '';
	}
	$size = (int) $size;
	$svg  = '<svg width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' . $p[ $name ] . '</svg>';
	return wp_kses(
		$svg,
		array(
			'svg'    => array( 'width' => true, 'height' => true, 'viewbox' => true, 'fill' => true, 'stroke' => true, 'stroke-width' => true, 'stroke-linecap' => true, 'stroke-linejoin' => true, 'aria-hidden' => true, 'focusable' => true ),
			'path'   => array( 'd' => true ),
			'circle' => array( 'cx' => true, 'cy' => true, 'r' => true ),
			'rect'   => array( 'x' => true, 'y' => true, 'width' => true, 'height' => true ),
		)
	);
}

function forge_logo() {
	$id = (int) forge_opt( 'dealer_logo' );
	if ( ! $id && has_custom_logo() ) {
		$id = (int) get_theme_mod( 'custom_logo' );
	}
	echo '<a class="frg-logo" href="' . esc_url( home_url( '/' ) ) . '" rel="home">';
	if ( $id ) {
		echo wp_kses_post( wp_get_attachment_image( $id, 'medium', false, array( 'alt' => get_bloginfo( 'name' ) ) ) );
	} else {
		$n     = forge_opt( 'dealer_name' );
		$n     = $n ? $n : get_bloginfo( 'name' );
		$parts = preg_split( '/\s+/', trim( (string) $n ) );
		if ( is_array( $parts ) && count( $parts ) > 1 ) {
			$last = array_pop( $parts );
			echo esc_html( implode( ' ', $parts ) ) . ' <span>' . esc_html( $last ) . '</span>';
		} else {
			echo esc_html( $n );
		}
	}
	echo '</a>';
}

function forge_img( $id, $size = 'large', $attr = array() ) {
	$id = (int) $id;
	return $id ? wp_kses_post( wp_get_attachment_image( $id, $size, false, $attr ) ) : '';
}

/* ---------- Products ---------- */

function forge_is_hidden_price( $product ) {
	if ( ! forge_opt( 'cfp_on' ) || ! $product ) {
		return false;
	}
	$v = get_post_meta( $product->get_parent_id() ? $product->get_parent_id() : $product->get_id(), '_tw_hide_price', true );
	return in_array( $v, array( 'yes', '1', 1, true, 'on', 'true' ), true );
}

function forge_brand_tax() {
	$t = forge_opt( 'brand_taxonomy' );
	if ( $t && taxonomy_exists( $t ) ) {
		return $t;
	}
	foreach ( array( 'product_brand', 'pa_brand', 'brand', 'pwb-brand' ) as $c ) {
		if ( taxonomy_exists( $c ) ) {
			return $c;
		}
	}
	return '';
}

function forge_brand( $product_id ) {
	$tax = forge_brand_tax();
	if ( ! $tax ) {
		return null;
	}
	$terms = get_the_terms( $product_id, $tax );
	if ( ! $terms || is_wp_error( $terms ) ) {
		return null;
	}
	$t = reset( $terms );
	return array( 'name' => $t->name, 'slug' => $t->slug );
}

function forge_stock_html( $product ) {
	if ( $product->is_on_backorder() ) {
		$c = 'frg-stock--back';
		$l = __( 'On backorder', 'forge' );
	} elseif ( $product->is_in_stock() ) {
		$c = 'frg-stock--in';
		$l = __( 'In stock', 'forge' );
	} else {
		$c = 'frg-stock--out';
		$l = __( 'Out of stock', 'forge' );
	}
	return '<span class="frg-stock ' . esc_attr( $c ) . '">' . esc_html( $l ) . '</span>';
}

function forge_price_html( $product ) {
	if ( forge_is_hidden_price( $product ) ) {
		return '<span class="frg-cfp">' . esc_html( forge_opt( 'cfp_label' ) ) . '</span>';
	}
	return $product->get_price_html();
}

function forge_query_products( $source, $slug = '', $n = 4 ) {
	$args = array(
		'post_type'           => 'product',
		'post_status'         => 'publish',
		'posts_per_page'      => (int) $n,
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
		'fields'              => 'ids',
		'tax_query'           => array(
			array( 'taxonomy' => 'product_visibility', 'field' => 'name', 'terms' => array( 'exclude-from-catalog' ), 'operator' => 'NOT IN' ),
		),
	);
	switch ( $source ) {
		case 'sale':
			$args['post__in'] = array_merge( array( 0 ), wc_get_product_ids_on_sale() );
			break;
		case 'featured':
			$args['tax_query'][] = array( 'taxonomy' => 'product_visibility', 'field' => 'name', 'terms' => array( 'featured' ) );
			break;
		case 'bestselling':
			$args['meta_key'] = 'total_sales'; // phpcs:ignore WordPress.DB.SlowDBQuery
			$args['orderby']  = 'meta_value_num';
			$args['order']    = 'DESC';
			break;
		case 'category':
			if ( ! $slug ) {
				return array();
			}
			$args['tax_query'][] = array( 'taxonomy' => 'product_cat', 'field' => 'slug', 'terms' => array( $slug ) );
			break;
		default:
			$args['orderby'] = 'date';
			$args['order']   = 'DESC';
	}
	$q = new WP_Query( $args );
	return array_map( 'intval', $q->posts );
}

function forge_product_card( $product ) {
	if ( ! $product instanceof WC_Product ) {
		$product = wc_get_product( $product );
	}
	if ( ! $product || ! $product->is_visible() ) {
		return;
	}
	$id    = $product->get_id();
	$brand = forge_brand( $id );
	$hide  = forge_is_hidden_price( $product );
	$sku   = $product->get_sku();
	$link  = get_permalink( $id );
	?>
	<article class="frg-card" data-id="<?php echo esc_attr( $id ); ?>">
		<div class="frg-card__media">
			<?php if ( $product->is_on_sale() && ! $hide ) : ?>
				<span class="frg-badge"><?php esc_html_e( 'Sale', 'forge' ); ?></span>
			<?php endif; ?>
			<a href="<?php echo esc_url( $link ); ?>" tabindex="-1" aria-hidden="true"><?php echo wp_kses_post( $product->get_image( 'woocommerce_thumbnail' ) ); ?></a>
		</div>
		<div class="frg-card__body">
			<div class="frg-card__copy">
				<?php if ( $brand ) : ?>
					<span class="frg-card__brand"><?php echo esc_html( $brand['name'] ); ?></span>
				<?php endif; ?>
				<h3 class="frg-card__title"><a href="<?php echo esc_url( $link ); ?>"><?php echo esc_html( $product->get_name() ); ?></a></h3>
				<?php if ( $sku ) : ?>
					<span class="frg-card__sku"><?php echo esc_html__( 'SKU', 'forge' ) . ' ' . esc_html( $sku ); ?></span>
				<?php endif; ?>
				<?php
				$summary = $product->get_short_description() ? $product->get_short_description() : $product->get_description();
				$summary = trim( wp_strip_all_tags( (string) $summary ) );
				if ( $summary ) :
					?>
					<p class="frg-card__summary"><?php echo esc_html( wp_trim_words( $summary, 26 ) ); ?></p>
				<?php endif; ?>
				<div class="frg-card__tools">
					<button type="button" class="frg-iconlink" data-frg-wish="<?php echo esc_attr( $id ); ?>" aria-pressed="false" aria-label="<?php esc_attr_e( 'Save to wishlist', 'forge' ); ?>"><?php echo forge_icon( 'heart', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php esc_html_e( 'Save', 'forge' ); ?></span></button>
					<button type="button" class="frg-iconlink" data-frg-compare="<?php echo esc_attr( $id ); ?>" aria-pressed="false" aria-label="<?php esc_attr_e( 'Compare', 'forge' ); ?>"><?php echo forge_icon( 'compare', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php esc_html_e( 'Compare', 'forge' ); ?></span></button>
					<button type="button" class="frg-iconlink" data-frg-quickview="<?php echo esc_attr( $id ); ?>" aria-label="<?php esc_attr_e( 'Quick view', 'forge' ); ?>"><?php echo forge_icon( 'eye', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php esc_html_e( 'Quick view', 'forge' ); ?></span></button>
				</div>
			</div>
			<div class="frg-card__buy">
			<div class="frg-card__price price"><?php echo wp_kses_post( forge_price_html( $product ) ); ?></div>
			<?php echo wp_kses_post( forge_stock_html( $product ) ); ?>
			<div class="frg-card__foot">
				<?php
				if ( $hide ) {
					$tel = forge_phone_href();
					echo '<a class="frg-btn frg-btn--ghost frg-btn--sm" href="' . esc_url( $tel ? $tel : $link ) . '">' . esc_html( forge_opt( 'cfp_label' ) ) . '</a>';
				} else {
					$GLOBALS['product'] = $product; // phpcs:ignore WordPress.WP.GlobalVariablesOverride
					woocommerce_template_loop_add_to_cart();
				}
				?>
			</div>
			</div>
		</div>
	</article>
	<?php
}

function forge_cards_html( $ids ) {
	ob_start();
	foreach ( $ids as $id ) {
		forge_product_card( (int) $id );
	}
	return ob_get_clean();
}

/** Brand / category / attribute-aware search → product IDs. */
function forge_search_ids( $q, $limit = 8 ) {
	$q = trim( $q );
	if ( strlen( $q ) < 2 || ! forge_has_wc() ) {
		return array();
	}
	$base = array( 'post_type' => 'product', 'post_status' => 'publish', 'fields' => 'ids', 'posts_per_page' => $limit * 2, 'no_found_rows' => true );
	$ids  = ( new WP_Query( $base + array( 's' => $q ) ) )->posts;
	$ids  = array_merge( $ids, ( new WP_Query( $base + array( 'meta_query' => array( array( 'key' => '_sku', 'value' => $q, 'compare' => 'LIKE' ) ) ) ) )->posts ); // phpcs:ignore WordPress.DB.SlowDBQuery

	$taxes = array_merge( array( 'product_cat', forge_brand_tax() ), wc_get_attribute_taxonomy_names() );
	$taxes = array_values( array_filter( array_unique( $taxes ), 'taxonomy_exists' ) );
	if ( $taxes ) {
		$terms = get_terms( array( 'taxonomy' => $taxes, 'name__like' => $q, 'hide_empty' => true, 'number' => 20 ) );
		if ( ! is_wp_error( $terms ) && $terms ) {
			$tq = array( 'relation' => 'OR' );
			foreach ( $terms as $t ) {
				$tq[] = array( 'taxonomy' => $t->taxonomy, 'field' => 'term_id', 'terms' => array( $t->term_id ) );
			}
			$ids = array_merge( $ids, ( new WP_Query( $base + array( 'tax_query' => $tq ) ) )->posts ); // phpcs:ignore WordPress.DB.SlowDBQuery
		}
	}
	return array_slice( array_values( array_unique( array_map( 'intval', $ids ) ) ), 0, $limit );
}

function forge_search_form( $id = 'frg-s' ) {
	?>
	<div class="frg-search" data-frg-search>
		<form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
			<label class="screen-reader-text" for="<?php echo esc_attr( $id ); ?>"><?php esc_html_e( 'Search', 'forge' ); ?></label>
			<input type="search" id="<?php echo esc_attr( $id ); ?>" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php echo esc_attr( forge_opt( 'search_hint' ) ); ?>" autocomplete="off">
			<input type="hidden" name="post_type" value="product">
			<button type="submit" aria-label="<?php esc_attr_e( 'Search', 'forge' ); ?>"><?php echo forge_icon( 'search', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
		</form>
		<div class="frg-search__results" hidden></div>
	</div>
	<?php
}

function forge_mega_terms() {
	if ( ! forge_has_wc() ) {
		return array();
	}
	$top = get_terms( array( 'taxonomy' => 'product_cat', 'parent' => 0, 'hide_empty' => true, 'number' => 8, 'exclude' => array( (int) get_option( 'default_product_cat' ) ) ) );
	if ( is_wp_error( $top ) ) {
		return array();
	}
	$out = array();
	foreach ( $top as $t ) {
		$kids = get_terms( array( 'taxonomy' => 'product_cat', 'parent' => $t->term_id, 'hide_empty' => true, 'number' => 6 ) );
		$out[] = array( 'term' => $t, 'kids' => is_wp_error( $kids ) ? array() : $kids );
	}
	return $out;
}

function forge_secondary_links() {
	$parts = forge_url( 'parts_url', home_url( '/parts/' ) );
	return array(
		__( 'Shop', 'forge' )      => forge_shop_url(),
		__( 'Service', 'forge' )   => forge_page_url( 'service' ),
		__( 'Financing', 'forge' ) => forge_page_url( 'financing' ),
		__( 'Parts', 'forge' )     => $parts,
		__( 'Support', 'forge' )   => forge_url( 'support_url', forge_page_url( 'contact' ) ),
	);
}

function forge_deals_url() {
	$u = forge_opt( 'deals_url' );
	return $u ? $u : add_query_arg( 'frg_deals', '1', forge_shop_url() );
}

/* ---------- Forms ---------- */

function forge_form_fields( $type ) {
	$sets = array(
		'contact'    => array(
			'name'    => array( 'Name', 'text', true ),
			'email'   => array( 'Email', 'email', true ),
			'phone'   => array( 'Phone', 'tel', false ),
			'message' => array( 'How can we help?', 'textarea', true ),
		),
		'service'    => array(
			'name'         => array( 'Name', 'text', true ),
			'email'        => array( 'Email', 'email', true ),
			'phone'        => array( 'Phone', 'tel', true ),
			'equipment'    => array( 'Equipment make & model', 'text', true ),
			'service_type' => array( 'Service needed', 'select', true, array( 'Repair', 'Annual tune-up', 'Warranty claim', 'Pickup & delivery', 'Other' ) ),
			'date'         => array( 'Preferred date', 'date', false ),
			'message'      => array( 'What is going on?', 'textarea', false ),
		),
		'newsletter' => array(
			'email' => array( 'Email', 'email', true ),
		),
	);
	return isset( $sets[ $type ] ) ? $sets[ $type ] : array();
}

function forge_form( $type ) {
	$fields = forge_form_fields( $type );
	if ( ! $fields ) {
		return;
	}
	$news = 'newsletter' === $type;
	echo '<form class="frg-form" data-frg-form="' . esc_attr( $type ) . '" novalidate>';
	echo '<div class="frg-form__hp" aria-hidden="true"><label>Website<input type="text" name="frg_website" tabindex="-1" autocomplete="off"></label></div>';
	$pair = array();
	foreach ( $fields as $name => $f ) {
		$id    = 'frg-' . $type . '-' . $name;
		$req   = $f[2] ? ' required' : '';
		$label = $f[0] . ( $f[2] ? ' *' : '' );
		echo '<div>';
		if ( $news ) {
			echo '<label class="screen-reader-text" for="' . esc_attr( $id ) . '">' . esc_html( $label ) . '</label>';
		} else {
			echo '<label for="' . esc_attr( $id ) . '">' . esc_html( $label ) . '</label>';
		}
		if ( 'textarea' === $f[1] ) {
			echo '<textarea id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '"' . esc_attr( $req ) . '></textarea>';
		} elseif ( 'select' === $f[1] ) {
			echo '<select id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '"' . esc_attr( $req ) . '>';
			foreach ( $f[3] as $o ) {
				echo '<option value="' . esc_attr( $o ) . '">' . esc_html( $o ) . '</option>';
			}
			echo '</select>';
		} else {
			$ph = $news ? ' placeholder="' . esc_attr__( 'Your email', 'forge' ) . '"' : '';
			echo '<input type="' . esc_attr( $f[1] ) . '" id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '"' . $ph . esc_attr( $req ) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput
		}
		echo '</div>';
	}
	unset( $pair );
	echo '<button type="submit" class="frg-btn">' . esc_html( $news ? __( 'Subscribe', 'forge' ) : __( 'Send message', 'forge' ) ) . '</button>';
	echo '<p class="frg-form__status" role="status" aria-live="polite"></p>';
	echo '</form>';
}

function forge_dealer_card() {
	$addr  = array_filter( array( forge_opt( 'dealer_address' ), trim( forge_opt( 'dealer_city' ) . ' ' . forge_opt( 'dealer_state' ) . ' ' . forge_opt( 'dealer_zip' ) ) ) );
	$hours = array_filter( array_map( 'trim', explode( "\n", wp_strip_all_tags( (string) forge_opt( 'dealer_hours' ) ) ) ) );
	echo '<div class="frg-card-info"><h3>' . esc_html( forge_opt( 'dealer_name' ) ) . '</h3><dl>';
	if ( $addr ) {
		echo '<div><dt>' . esc_html__( 'Visit', 'forge' ) . '</dt><dd>' . wp_kses_post( implode( '<br>', array_map( 'esc_html', $addr ) ) ) . '</dd></div>';
	}
	if ( forge_opt( 'dealer_phone' ) ) {
		echo '<div><dt>' . esc_html__( 'Call', 'forge' ) . '</dt><dd><a href="' . esc_url( forge_phone_href() ) . '">' . esc_html( forge_opt( 'dealer_phone' ) ) . '</a></dd></div>';
	}
	if ( forge_opt( 'dealer_email' ) ) {
		echo '<div><dt>' . esc_html__( 'Email', 'forge' ) . '</dt><dd><a href="' . esc_url( 'mailto:' . forge_opt( 'dealer_email' ) ) . '">' . esc_html( forge_opt( 'dealer_email' ) ) . '</a></dd></div>';
	}
	if ( $hours ) {
		echo '<div><dt>' . esc_html__( 'Hours', 'forge' ) . '</dt><dd>' . wp_kses_post( implode( '<br>', array_map( 'esc_html', $hours ) ) ) . '</dd></div>';
	}
	echo '</dl></div>';
}

function forge_page_hero( $title, $intro = '' ) {
	echo '<section class="frg-pagehero"><div class="frg-wrap"><h1>' . esc_html( $title ) . '</h1>';
	if ( $intro ) {
		echo '<p>' . esc_html( $intro ) . '</p>';
	}
	echo '</div></section>';
}

function forge_post_card() {
	?>
	<a class="frg-post" href="<?php the_permalink(); ?>">
		<div class="frg-post__img"><?php echo has_post_thumbnail() ? wp_kses_post( get_the_post_thumbnail( null, 'large' ) ) : ''; ?></div>
		<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
		<h3><?php echo esc_html( get_the_title() ); ?></h3>
		<p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 22 ) ); ?></p>
	</a>
	<?php
}

function forge_ship_progress() {
	echo '<div class="frg-ship">';
	$t = (float) forge_opt( 'ship_threshold' );
	if ( $t > 0 && forge_has_wc() && WC()->cart ) {
		$sub = (float) WC()->cart->get_displayed_subtotal();
		$pct = min( 100, max( 0, round( $sub / $t * 100 ) ) );
		if ( $sub >= $t ) {
			echo '<p>' . esc_html__( 'You qualify for free shipping.', 'forge' ) . '</p>';
		} else {
			/* translators: %s: amount remaining */
			echo '<p>' . wp_kses_post( sprintf( esc_html__( 'Add %s more for free shipping.', 'forge' ), wc_price( $t - $sub ) ) ) . '</p>';
		}
		echo '<div class="frg-ship__bar"><i style="width:' . esc_attr( $pct ) . '%"></i></div>';
	}
	echo '</div>';
}

function forge_newsletter_section() {
	?>
	<section class="frg-newsletter" aria-labelledby="frg-nl-h">
		<div class="frg-wrap frg-newsletter__inner">
			<div>
				<h2 id="frg-nl-h" class="frg-section-title"><?php echo esc_html( forge_opt( 'nl_title' ) ); ?></h2>
				<p><?php echo esc_html( forge_opt( 'nl_text' ) ); ?></p>
			</div>
			<?php forge_form( 'newsletter' ); ?>
		</div>
	</section>
	<?php
}
