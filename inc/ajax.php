<?php
/**
 * AJAX: live search, cards, compare, quick view, forms.
 *
 * @package Forge
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function forge_ajax_ids() {
	$raw = isset( $_REQUEST['ids'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['ids'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
	return array_slice( array_filter( array_map( 'absint', explode( ',', $raw ) ) ), 0, 24 );
}

add_action( 'wp_ajax_forge_live_search', 'forge_ajax_live_search' );
add_action( 'wp_ajax_nopriv_forge_live_search', 'forge_ajax_live_search' );
function forge_ajax_live_search() {
	$q   = isset( $_GET['q'] ) ? sanitize_text_field( wp_unslash( $_GET['q'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
	$ids = forge_search_ids( $q, 6 );
	ob_start();
	foreach ( $ids as $id ) {
		$p = wc_get_product( $id );
		if ( ! $p || ! $p->is_visible() ) {
			continue;
		}
		$b   = forge_brand( $id );
		$sku = $p->get_sku();
		$meta = trim( ( $b ? $b['name'] : '' ) . ( $sku ? ' · ' . $sku : '' ), ' ·' );
		echo '<a class="frg-sr" href="' . esc_url( get_permalink( $id ) ) . '">';
		echo wp_kses_post( $p->get_image( 'thumbnail' ) );
		echo '<span><b>' . esc_html( $p->get_name() ) . '</b><small>' . esc_html( $meta ) . '</small></span>';
		echo '<span class="price">' . wp_kses_post( forge_price_html( $p ) ) . '</span></a>';
	}
	$items = ob_get_clean();
	if ( ! $items ) {
		$items = '<span class="frg-sr__none">' . esc_html__( 'No matching equipment found.', 'forge' ) . '</span>';
	} else {
		$items .= '<a class="frg-sr__all" href="' . esc_url( add_query_arg( array( 's' => $q, 'post_type' => 'product' ), home_url( '/' ) ) ) . '">' . esc_html__( 'See all results', 'forge' ) . '</a>';
	}
	wp_send_json_success( array( 'html' => $items ) );
}

add_action( 'wp_ajax_forge_cards', 'forge_ajax_cards' );
add_action( 'wp_ajax_nopriv_forge_cards', 'forge_ajax_cards' );
function forge_ajax_cards() {
	$ids = forge_ajax_ids();
	// Keep requested order.
	wp_send_json_success( array( 'html' => forge_cards_html( $ids ) ) );
}

add_action( 'wp_ajax_forge_compare', 'forge_ajax_compare' );
add_action( 'wp_ajax_nopriv_forge_compare', 'forge_ajax_compare' );
function forge_ajax_compare() {
	$products = array();
	foreach ( array_slice( forge_ajax_ids(), 0, 4 ) as $id ) {
		$p = wc_get_product( $id );
		if ( $p && $p->is_visible() ) {
			$products[] = $p;
		}
	}
	if ( ! $products ) {
		wp_send_json_success( array( 'html' => '<p class="frg-empty">' . esc_html__( 'Nothing to compare yet.', 'forge' ) . '</p>' ) );
	}
	$attr_labels = array();
	foreach ( $products as $p ) {
		foreach ( $p->get_attributes() as $key => $a ) {
			$attr_labels[ $key ] = wc_attribute_label( $a->get_name(), $p );
		}
	}
	ob_start();
	echo '<div class="frg-cmp"><table><tbody>';
	echo '<tr><th></th>';
	foreach ( $products as $p ) {
		echo '<td><a href="' . esc_url( get_permalink( $p->get_id() ) ) . '">' . wp_kses_post( $p->get_image( 'woocommerce_thumbnail' ) ) . '<b>' . esc_html( $p->get_name() ) . '</b></a></td>';
	}
	echo '</tr>';
	$rows = array(
		__( 'Brand', 'forge' )  => function ( $p ) { $b = forge_brand( $p->get_id() ); return esc_html( $b ? $b['name'] : '—' ); },
		__( 'SKU', 'forge' )    => function ( $p ) { return esc_html( $p->get_sku() ? $p->get_sku() : '—' ); },
		__( 'Price', 'forge' )  => function ( $p ) { return wp_kses_post( forge_price_html( $p ) ); },
		__( 'Stock', 'forge' )  => function ( $p ) { return wp_kses_post( forge_stock_html( $p ) ); },
	);
	foreach ( $rows as $label => $cb ) {
		echo '<tr><th>' . esc_html( $label ) . '</th>';
		foreach ( $products as $p ) {
			echo '<td>' . $cb( $p ) . '</td>'; // phpcs:ignore WordPress.Security.EscapeOutput -- callbacks escape.
		}
		echo '</tr>';
	}
	foreach ( $attr_labels as $key => $label ) {
		echo '<tr><th>' . esc_html( $label ) . '</th>';
		foreach ( $products as $p ) {
			$v = $p->get_attribute( $key );
			echo '<td>' . esc_html( $v ? $v : '—' ) . '</td>';
		}
		echo '</tr>';
	}
	echo '</tbody></table></div>';
	wp_send_json_success( array( 'html' => ob_get_clean() ) );
}

add_action( 'wp_ajax_forge_quickview', 'forge_ajax_quickview' );
add_action( 'wp_ajax_nopriv_forge_quickview', 'forge_ajax_quickview' );
function forge_ajax_quickview() {
	$ids = forge_ajax_ids();
	$p   = $ids ? wc_get_product( $ids[0] ) : null;
	if ( ! $p || ! $p->is_visible() ) {
		wp_send_json_error();
	}
	$b    = forge_brand( $p->get_id() );
	$hide = forge_is_hidden_price( $p );
	ob_start();
	echo '<div class="frg-qv"><div>' . wp_kses_post( $p->get_image( 'large' ) ) . '</div><div>';
	if ( $b ) {
		echo '<span class="frg-eyebrow">' . esc_html( $b['name'] ) . '</span>';
	}
	echo '<h3>' . esc_html( $p->get_name() ) . '</h3>';
	if ( $p->get_sku() ) {
		echo '<p class="frg-muted">' . esc_html__( 'SKU', 'forge' ) . ' ' . esc_html( $p->get_sku() ) . '</p>';
	}
	echo '<p class="price">' . wp_kses_post( forge_price_html( $p ) ) . '</p>';
	echo '<p>' . wp_kses_post( forge_stock_html( $p ) ) . '</p>';
	echo '<div class="frg-muted">' . wp_kses_post( wpautop( $p->get_short_description() ) ) . '</div>';
	echo '<div class="frg-qv__actions">';
	if ( $hide ) {
		$tel = forge_phone_href();
		echo '<a class="frg-btn" href="' . esc_url( $tel ? $tel : get_permalink( $p->get_id() ) ) . '">' . esc_html( forge_opt( 'cfp_label' ) ) . '</a>';
	} else {
		$GLOBALS['product'] = $p; // phpcs:ignore WordPress.WP.GlobalVariablesOverride
		woocommerce_template_loop_add_to_cart();
	}
	echo '<a class="frg-btn frg-btn--ghost" href="' . esc_url( get_permalink( $p->get_id() ) ) . '">' . esc_html__( 'Full details', 'forge' ) . '</a>';
	echo '</div></div></div>';
	wp_send_json_success( array( 'html' => ob_get_clean(), 'title' => $p->get_name() ) );
}

/* ---------- Forms ---------- */
add_action( 'wp_ajax_forge_form', 'forge_ajax_form' );
add_action( 'wp_ajax_nopriv_forge_form', 'forge_ajax_form' );
function forge_ajax_form() {
	if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'forge_form' ) ) {
		wp_send_json_error( array( 'message' => __( 'Session expired. Please reload the page.', 'forge' ) ), 403 );
	}
	if ( ! empty( $_POST['frg_website'] ) ) { // Honeypot.
		wp_send_json_success( array( 'message' => __( 'Thank you.', 'forge' ) ) );
	}
	$type   = isset( $_POST['type'] ) ? sanitize_key( wp_unslash( $_POST['type'] ) ) : '';
	$fields = forge_form_fields( $type );
	if ( ! $fields ) {
		wp_send_json_error( array( 'message' => __( 'Unknown form.', 'forge' ) ) );
	}
	$ip  = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	$key = 'forge_rl_' . md5( $ip . $type );
	$hit = (int) get_transient( $key );
	if ( $hit >= 5 ) {
		wp_send_json_error( array( 'message' => __( 'Too many submissions. Please try again later.', 'forge' ) ), 429 );
	}
	set_transient( $key, $hit + 1, 10 * MINUTE_IN_SECONDS );

	$data = array();
	foreach ( $fields as $name => $f ) {
		$raw = isset( $_POST[ $name ] ) ? wp_unslash( $_POST[ $name ] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
		if ( 'email' === $f[1] ) {
			$val = sanitize_email( $raw );
			if ( '' !== trim( (string) $raw ) && ( '' === $val || ! is_email( $val ) ) ) {
				wp_send_json_error( array( 'message' => __( 'Please enter a valid email address.', 'forge' ) ) );
			}
		} elseif ( 'textarea' === $f[1] ) {
			$val = sanitize_textarea_field( $raw );
		} else {
			$val = sanitize_text_field( $raw );
		}
		if ( $f[2] && '' === $val ) {
			wp_send_json_error( array( 'message' => __( 'Please fill in the required fields.', 'forge' ) ) );
		}
		if ( 'email' === $f[1] && '' !== $val && ! is_email( $val ) ) {
			wp_send_json_error( array( 'message' => __( 'Please enter a valid email address.', 'forge' ) ) );
		}
		$data[ $name ] = $val;
	}

	if ( 'newsletter' === $type ) {
		$subs = (array) get_option( 'forge_subscribers', array() );
		if ( ! in_array( $data['email'], $subs, true ) && count( $subs ) < 5000 ) {
			$subs[] = $data['email'];
			update_option( 'forge_subscribers', $subs, false );
		}
		do_action( 'forge_newsletter_subscribed', $data['email'] );
		$subject = __( 'New newsletter signup', 'forge' );
	} else {
		/* translators: %s: form type */
		$subject = sprintf( __( 'New %s request', 'forge' ), $type );
	}
	$body = '';
	foreach ( $fields as $name => $f ) {
		$body .= $f[0] . ': ' . $data[ $name ] . "\n";
	}
	$headers = array();
	if ( ! empty( $data['email'] ) ) {
		$headers[] = 'Reply-To: ' . $data['email'];
	}
	$sent = wp_mail( forge_form_recipient(), '[' . wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ) . '] ' . $subject, $body, $headers );
	if ( ! $sent && 'newsletter' !== $type ) {
		wp_send_json_error( array( 'message' => __( 'We could not send your message. Please call us instead.', 'forge' ) ) );
	}
	wp_send_json_success( array( 'message' => 'newsletter' === $type ? __( 'You are subscribed.', 'forge' ) : __( 'Thank you. We will be in touch shortly.', 'forge' ) ) );
}
