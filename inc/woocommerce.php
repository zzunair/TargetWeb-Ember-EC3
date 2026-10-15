<?php
/**
 * WooCommerce integration.
 *
 * @package Forge
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );
add_filter( 'loop_shop_columns', function () { return 4; } );
add_filter( 'loop_shop_per_page', function () { return 12; } );
add_filter( 'woocommerce_output_related_products_args', function ( $a ) { $a['posts_per_page'] = 4; $a['columns'] = 4; return $a; } );
add_filter( 'woocommerce_upsell_display_args', function ( $a ) { $a['posts_per_page'] = 4; $a['columns'] = 4; return $a; } );

add_filter(
	'woocommerce_loop_add_to_cart_args',
	function ( $a ) {
		$a['class'] = ( isset( $a['class'] ) ? $a['class'] : '' ) . ' frg-btn';
		return $a;
	}
);

// Layout wrappers are in woocommerce.php.
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );

/* ---------- Call for Price ---------- */
add_filter(
	'woocommerce_get_price_html',
	function ( $html, $product ) {
		return forge_is_hidden_price( $product ) ? forge_price_html( $product ) : $html;
	},
	20,
	2
);
add_filter(
	'woocommerce_is_purchasable',
	function ( $ok, $product ) {
		return forge_is_hidden_price( $product ) ? false : $ok;
	},
	20,
	2
);

/* ---------- Single product ---------- */
add_action(
	'woocommerce_single_product_summary',
	function () {
		global $product;
		$b = $product ? forge_brand( $product->get_id() ) : null;
		if ( $b ) {
			echo '<span class="frg-brand-line">' . esc_html( $b['name'] ) . '</span>';
		}
	},
	4
);
add_action(
	'woocommerce_single_product_summary',
	function () {
		global $product;
		if ( ! $product ) {
			return;
		}
		echo '<p>' . wp_kses_post( forge_stock_html( $product ) ) . '</p>';
		if ( forge_is_hidden_price( $product ) ) {
			$tel = forge_phone_href();
			echo '<p><a class="frg-btn frg-btn--block" href="' . esc_url( $tel ? $tel : '#' ) . '">' . esc_html( forge_opt( 'cfp_label' ) ) . '</a></p>';
		}
	},
	11
);
add_action(
	'woocommerce_single_product_summary',
	function () {
		global $product;
		if ( ! $product ) {
			return;
		}
		$id = $product->get_id();
		echo '<div class="frg-single-tools" data-frg-product="' . esc_attr( $id ) . '">';
		echo '<button type="button" class="frg-btn frg-btn--ghost" data-frg-wish="' . esc_attr( $id ) . '" aria-pressed="false">' . forge_icon( 'heart', 18 ) . esc_html__( 'Save', 'forge' ) . '</button>'; // phpcs:ignore WordPress.Security.EscapeOutput
		echo '<button type="button" class="frg-btn frg-btn--ghost" data-frg-compare="' . esc_attr( $id ) . '" aria-pressed="false">' . forge_icon( 'compare', 18 ) . esc_html__( 'Compare', 'forge' ) . '</button>'; // phpcs:ignore WordPress.Security.EscapeOutput
		forge_request_info( $product );
		echo '</div>';
		if ( forge_opt( 'stock_widget' ) ) {
			echo '<div id="product-stock-div" class="frg-stockwidget"></div>';
			wp_print_inline_script_tag( 'window.targetWebShopifyProductId = ' . (int) $id . ';' );
		}
	},
	35
);

/**
 * TargetWeb CRM lead form is enabled and its button should show.
 */
function forge_crm_enabled() {
	if ( ! class_exists( 'TW_CRM_Settings' ) ) {
		return false;
	}
	$settings = TW_CRM_Settings::get_settings();
	return ! empty( $settings['enabled'] ) && ! empty( $settings['cta_show'] );
}

/**
 * CTA label from Products → TargetWeb CRM. The plugin is not edited.
 */
function forge_crm_cta_text() {
	if ( ! class_exists( 'TW_CRM_Settings' ) ) {
		return __( 'Request information', 'forge' );
	}
	$settings = TW_CRM_Settings::get_settings();
	$text     = isset( $settings['cta_text'] ) ? sanitize_text_field( $settings['cta_text'] ) : '';
	return $text ? $text : __( 'Request information', 'forge' );
}

/**
 * Request Information button. The plugin script binds #tw-request-info-btn
 * and fills #leads-form-div inside #tw-crm-popup when modal source is theme.
 *
 * @param WC_Product $product Product.
 */
function forge_request_info( $product ) {
	if ( forge_crm_enabled() ) {
		echo '<button type="button" id="tw-request-info-btn" class="frg-btn frg-btn--ghost" data-product="' . esc_attr( $product->get_name() ) . '" data-product-id="' . (int) $product->get_id() . '">' . esc_html( forge_crm_cta_text() ) . '</button>';
		return;
	}
	echo '<a class="frg-btn frg-btn--ghost" href="' . esc_url( add_query_arg( 'product', rawurlencode( $product->get_name() ), forge_page_url( 'contact' ) ) ) . '">' . esc_html__( 'Request information', 'forge' ) . '</a>';
}

/**
 * Theme modal the CRM plugin fills. Output only on a product page.
 */
function forge_crm_modal() {
	if ( ! function_exists( 'is_product' ) || ! is_product() || ! forge_crm_enabled() ) {
		return;
	}
	global $product;
	if ( ! $product ) {
		return;
	}
	?>
	<div id="tw-crm-popup" class="tw-crm-popup frg-crm" role="dialog" aria-modal="true" aria-labelledby="frg-crm-title" aria-hidden="true">
		<div class="tw-crm-popup__overlay" data-tw-crm-close></div>
		<div class="tw-crm-popup__panel" role="document">
			<div class="frg-crm__head">
				<h2 id="frg-crm-title"><?php echo esc_html( forge_crm_cta_text() ); ?></h2>
				<button type="button" class="frg-iconlink" data-tw-crm-close aria-label="<?php esc_attr_e( 'Close', 'forge' ); ?>"><?php echo forge_icon( 'close', 22 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
			</div>
			<p class="frg-crm__about"><?php echo esc_html( $product->get_name() ); ?><?php echo $product->get_sku() ? ' · ' . esc_html( $product->get_sku() ) : ''; ?></p>
			<div id="leads-form-div"></div>
		</div>
	</div>
	<?php
}

add_action(
	'woocommerce_after_single_product_summary',
	function () {
		echo '<section class="frg-recent" data-frg-recent hidden><h2>' . esc_html__( 'Recently viewed', 'forge' ) . '</h2><div class="frg-grid"></div></section>';
	},
	25
);
add_action(
	'woocommerce_after_cart',
	function () {
		echo '<section class="frg-recent" data-frg-recent hidden><h2>' . esc_html__( 'Recently viewed', 'forge' ) . '</h2><div class="frg-grid"></div></section>';
	}
);

/* ---------- Fragments (cart drawer) ---------- */
add_filter(
	'woocommerce_add_to_cart_fragments',
	function ( $f ) {
		ob_start();
		forge_ship_progress();
		$f['div.frg-ship'] = ob_get_clean();
		$n                 = WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
		$f['span.frg-count--cart'] = '<span class="frg-count frg-count--cart" data-zero="' . ( $n ? '0' : '1' ) . '">' . esc_html( $n ) . '</span>';
		return $f;
	}
);

/* ---------- Equipment finder ---------- */
function forge_finder() {
	if ( ! forge_opt( 'finder_on' ) ) {
		return;
	}
	$cats   = get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => true ) );
	$bt     = forge_brand_tax();
	$brands = $bt ? get_terms( array( 'taxonomy' => $bt, 'hide_empty' => true ) ) : array();
	$cur_c  = isset( $_GET['frg_cat'] ) ? sanitize_title( wp_unslash( $_GET['frg_cat'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
	$cur_b  = isset( $_GET['frg_brand'] ) ? sanitize_title( wp_unslash( $_GET['frg_brand'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
	$cur_s  = ! empty( $_GET['frg_stock'] ); // phpcs:ignore WordPress.Security.NonceVerification
	?>
	<form class="frg-finder" method="get" action="<?php echo esc_url( forge_shop_url() ); ?>" aria-label="<?php esc_attr_e( 'Equipment finder', 'forge' ); ?>">
		<div>
			<label for="frg-f-cat"><?php esc_html_e( 'Category', 'forge' ); ?></label>
			<select id="frg-f-cat" name="frg_cat">
				<option value=""><?php esc_html_e( 'All categories', 'forge' ); ?></option>
				<?php if ( ! is_wp_error( $cats ) ) : foreach ( $cats as $t ) : ?>
					<option value="<?php echo esc_attr( $t->slug ); ?>" <?php selected( $cur_c, $t->slug ); ?>><?php echo esc_html( $t->name ); ?></option>
				<?php endforeach; endif; ?>
			</select>
		</div>
		<div>
			<label for="frg-f-brand"><?php esc_html_e( 'Brand', 'forge' ); ?></label>
			<select id="frg-f-brand" name="frg_brand">
				<option value=""><?php esc_html_e( 'All brands', 'forge' ); ?></option>
				<?php if ( $brands && ! is_wp_error( $brands ) ) : foreach ( $brands as $t ) : ?>
					<option value="<?php echo esc_attr( $t->slug ); ?>" <?php selected( $cur_b, $t->slug ); ?>><?php echo esc_html( $t->name ); ?></option>
				<?php endforeach; endif; ?>
			</select>
		</div>
		<label class="frg-finder__check"><input type="checkbox" name="frg_stock" value="1" <?php checked( $cur_s ); ?>><?php esc_html_e( 'In stock', 'forge' ); ?></label>
		<button type="submit" class="frg-btn"><?php esc_html_e( 'Find equipment', 'forge' ); ?></button>
		<a class="frg-btn frg-btn--ghost" href="<?php echo esc_url( forge_shop_url() ); ?>"><?php esc_html_e( 'Reset', 'forge' ); ?></a>
	</form>
	<?php
}
add_action(
	'woocommerce_before_shop_loop',
	function () {
		if ( is_shop() || is_product_taxonomy() ) {
			forge_finder();
		}
	},
	5
);

/**
 * Grid / list switch for the shop and category archives.
 */
function forge_view_switch() {
	?>
	<div class="frg-view" role="group" aria-label="<?php esc_attr_e( 'Product layout', 'forge' ); ?>">
		<button type="button" data-frg-view="grid" aria-pressed="true"><?php echo forge_icon( 'grid', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php esc_html_e( 'Grid', 'forge' ); ?></span></button>
		<button type="button" data-frg-view="list" aria-pressed="false"><?php echo forge_icon( 'list', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php esc_html_e( 'List', 'forge' ); ?></span></button>
	</div>
	<?php
}

/**
 * Result count, sort, and the layout switch on one bar.
 */
function forge_shop_toolbar() {
	echo '<div class="frg-shopbar">';
	woocommerce_result_count();
	echo '<div class="frg-shopbar__tools">';
	woocommerce_catalog_ordering();
	forge_view_switch();
	echo '</div></div>';
}
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );
add_action( 'woocommerce_before_shop_loop', 'forge_shop_toolbar', 20 );

add_filter(
	'woocommerce_product_loop_start',
	function ( $html ) {
		if ( ! ( is_shop() || is_product_taxonomy() ) || false !== strpos( $html, 'data-frg-products' ) ) {
			return $html;
		}
		return preg_replace( '/<ul\b/', '<ul data-frg-products data-frg-view="grid"', $html, 1 );
	}
);

add_action(
	'woocommerce_after_shop_loop',
	function () {
		if ( is_shop() || is_product_taxonomy() ) {
			echo '<script>try{var v=localStorage.getItem("frgProductView");if(v==="list"||v==="grid"){var el=document.querySelector("[data-frg-products]");if(el)el.setAttribute("data-frg-view",v);}}catch(e){}</script>';
		}
	},
	1
);

add_action(
	'pre_get_posts',
	function ( $q ) {
		if ( is_admin() || ! $q->is_main_query() || ! forge_has_wc() ) {
			return;
		}
		// Product search: name, SKU, brand, category, attributes.
		if ( $q->is_search() && 'product' === $q->get( 'post_type' ) ) {
			$s                       = $q->get( 's' );
			$GLOBALS['forge_search'] = $s;
			$ids                     = forge_search_ids( $s, 60 );
			$q->set( 'post__in', $ids ? $ids : array( 0 ) );
			$q->set( 's', '' );
			add_filter(
				'document_title_parts',
				function ( $parts ) {
					if ( ! empty( $GLOBALS['forge_search'] ) ) {
						$parts['title'] = sprintf(
							/* translators: %s: search query */
							__( 'Search results for “%s”', 'forge' ),
							$GLOBALS['forge_search']
						);
					}
					return $parts;
				}
			);
			if ( empty( $_GET['orderby'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
				$q->set( 'orderby', 'post__in' );
			}
			return;
		}
		if ( ! ( $q->is_post_type_archive( 'product' ) || $q->is_tax( array( 'product_cat', 'product_tag' ) ) ) ) {
			return;
		}
		$tax   = (array) $q->get( 'tax_query' );
		$meta  = (array) $q->get( 'meta_query' );
		$cat   = isset( $_GET['frg_cat'] ) ? sanitize_title( wp_unslash( $_GET['frg_cat'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
		$brand = isset( $_GET['frg_brand'] ) ? sanitize_title( wp_unslash( $_GET['frg_brand'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
		if ( $cat ) {
			$tax[] = array( 'taxonomy' => 'product_cat', 'field' => 'slug', 'terms' => array( $cat ) );
		}
		if ( $brand && forge_brand_tax() ) {
			$tax[] = array( 'taxonomy' => forge_brand_tax(), 'field' => 'slug', 'terms' => array( $brand ) );
		}
		if ( ! empty( $_GET['frg_stock'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			$meta[] = array( 'key' => '_stock_status', 'value' => 'instock' );
		}
		if ( ! empty( $_GET['frg_deals'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			$q->set( 'post__in', array_merge( array( 0 ), wc_get_product_ids_on_sale() ) );
		}
		if ( $tax ) {
			$q->set( 'tax_query', $tax ); // phpcs:ignore WordPress.DB.SlowDBQuery
		}
		if ( $meta ) {
			$q->set( 'meta_query', $meta ); // phpcs:ignore WordPress.DB.SlowDBQuery
		}
	},
	20
);
