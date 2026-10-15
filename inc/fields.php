<?php
/**
 * Customizer field definitions. Single source of truth for defaults, types, sanitizing.
 *
 * @package Forge
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function forge_f( $label, $type = 'text', $default = '', $extra = array() ) {
	return array_merge( array( 'label' => $label, 'type' => $type, 'default' => $default ), $extra );
}

function forge_heading_fonts() {
	return array(
		'Barlow Condensed' => 'Barlow Condensed',
		'Oswald'           => 'Oswald',
		'Saira Condensed'  => 'Saira Condensed',
		'Archivo Narrow'   => 'Archivo Narrow',
	);
}

function forge_body_fonts() {
	return array(
		'Source Sans 3' => 'Source Sans 3',
		'DM Sans'       => 'DM Sans',
		'Work Sans'     => 'Work Sans',
		'IBM Plex Sans' => 'IBM Plex Sans',
	);
}

function forge_home_keys() {
	return array(
		'hero'       => 'Hero slider',
		'trust'      => 'Trust strip',
		'tiles'      => 'Promo tiles',
		'countdown'  => 'Countdown offer',
		'tabs'       => 'Product tabs',
		'categories' => 'Category grid',
		'story'      => 'Editorial story',
		'journal'    => 'Journal row',
		'newsletter' => 'Newsletter',
	);
}

function forge_source_choices() {
	return array(
		'newest'      => 'Newest',
		'featured'    => 'WooCommerce featured (star)',
		'sale'        => 'On sale',
		'bestselling' => 'Best selling',
		'category'    => 'One category (slug below)',
	);
}

function forge_fields() {
	static $f = null;
	if ( null !== $f ) {
		return $f;
	}
	$f = array();

	$f['dealer'] = array(
		'title'  => 'Dealer details',
		'fields' => array(
			'dealer_name'      => forge_f( 'Dealer name', 'text', 'Ember Outdoor Power' ),
			'dealer_logo'      => forge_f( 'Logo (overrides site logo)', 'image', 0 ),
			'dealer_phone'     => forge_f( 'Phone', 'text', '' ),
			'dealer_email'     => forge_f( 'Email', 'email', '' ),
			'dealer_address'   => forge_f( 'Street address', 'text', '' ),
			'dealer_city'      => forge_f( 'City', 'text', '' ),
			'dealer_state'     => forge_f( 'State / region', 'text', '' ),
			'dealer_zip'       => forge_f( 'Postal code', 'text', '' ),
			'dealer_hours'     => forge_f( 'Hours (one line each)', 'textarea', "Mon–Fri 8am–5:30pm\nSat 8am–3pm\nSun Closed" ),
			'dealer_facebook'  => forge_f( 'Facebook URL', 'url', '' ),
			'dealer_instagram' => forge_f( 'Instagram URL', 'url', '' ),
			'dealer_youtube'   => forge_f( 'YouTube URL', 'url', '' ),
			'form_email'       => forge_f( 'Form recipient email (defaults to dealer/admin email)', 'email', '' ),
		),
	);

	$f['header'] = array(
		'title'  => 'Announcement & header',
		'fields' => array(
			'announce_on'   => forge_f( 'Show announcement bar', 'checkbox', 1 ),
			'announce_text' => forge_f( 'Announcement text', 'text', 'Free shipping on orders over $500.' ),
			'announce_url'  => forge_f( 'Announcement link', 'url', '' ),
			'secondary_on'  => forge_f( 'Show secondary bar', 'checkbox', 1 ),
			'deals_label'   => forge_f( 'Deals menu label', 'text', 'Deals' ),
			'deals_url'     => forge_f( 'Deals link (blank = on-sale products)', 'url', '' ),
			'parts_url'     => forge_f( 'Parts link (blank = /parts/)', 'url', '' ),
			'support_url'   => forge_f( 'Support link (blank = Contact page)', 'url', '' ),
			'search_hint'   => forge_f( 'Search placeholder', 'text', 'Search by name, SKU, brand or category' ),
		),
	);

	$f['footer'] = array(
		'title'  => 'Footer',
		'fields' => array(
			'footer_about'      => forge_f( 'Footer blurb', 'textarea', 'Sales, parts and factory-trained service for outdoor power equipment.' ),
			'footer_copy'       => forge_f( 'Copyright line', 'text', '' ),
			'newsletter_inner'  => forge_f( 'Show newsletter above footer on inner pages', 'checkbox', 1 ),
			'nl_title'          => forge_f( 'Newsletter heading', 'text', 'Stay in the loop' ),
			'nl_text'           => forge_f( 'Newsletter text', 'text', 'Offers, new arrivals and seasonal service reminders. No clutter.' ),
		),
	);

	$f['colors'] = array(
		'title'  => 'Colors',
		'fields' => array(
			'color_accent'  => forge_f( 'Accent (buttons, sale prices, active states)', 'color', '#e5231f' ),
			'color_bg'      => forge_f( 'Page background', 'color', '#121212' ),
			'color_surface' => forge_f( 'Card surface', 'color', '#1b1b1c' ),
			'color_text'    => forge_f( 'Primary text', 'color', '#f2f0eb' ),
			'color_muted'   => forge_f( 'Secondary text', 'color', '#a3a19b' ),
		),
	);

	$f['type'] = array(
		'title'  => 'Typography',
		'fields' => array(
			'font_heading' => forge_f( 'Heading font', 'select', 'Barlow Condensed', array( 'choices' => forge_heading_fonts() ) ),
			'font_body'    => forge_f( 'Body font', 'select', 'Source Sans 3', array( 'choices' => forge_body_fonts() ) ),
			'fs_base'      => forge_f( 'Base size (px)', 'number', 16, array( 'min' => 14, 'max' => 20 ) ),
			'fs_h1'        => forge_f( 'H1 size (px)', 'number', 72, array( 'min' => 32, 'max' => 120 ) ),
			'fs_h2'        => forge_f( 'H2 size (px)', 'number', 48, array( 'min' => 24, 'max' => 90 ) ),
			'fs_h3'        => forge_f( 'H3 size (px)', 'number', 30, array( 'min' => 18, 'max' => 56 ) ),
			'fs_h4'        => forge_f( 'H4 size (px)', 'number', 22, array( 'min' => 14, 'max' => 36 ) ),
			'fs_section'   => forge_f( 'Section title size (px)', 'number', 48, array( 'min' => 24, 'max' => 90 ) ),
		),
	);

	$home = array();
	$i    = 1;
	foreach ( forge_home_keys() as $k => $label ) {
		$home[ "home_{$k}_on" ]    = forge_f( "Show: {$label}", 'checkbox', 1 );
		$home[ "home_{$k}_order" ] = forge_f( "Order: {$label} (lowest first)", 'number', $i * 10, array( 'min' => 0, 'max' => 999 ) );
		$i++;
	}
	$f['home'] = array( 'title' => 'Homepage order', 'fields' => $home );

	$hero = array();
	for ( $n = 1; $n <= 3; $n++ ) {
		$hero[ "hero_{$n}_image" ]   = forge_f( "Slide {$n}: image", 'image', 0 );
		$hero[ "hero_{$n}_eyebrow" ] = forge_f( "Slide {$n}: small label", 'text', 1 === $n ? 'Spring season' : '' );
		$hero[ "hero_{$n}_title" ]   = forge_f( "Slide {$n}: heading (blank hides slide)", 'text', 1 === $n ? 'Power that works as hard as you do' : '' );
		$hero[ "hero_{$n}_text" ]    = forge_f( "Slide {$n}: text", 'text', 1 === $n ? 'Mowers, saws, generators and more, backed by our service team.' : '' );
		$hero[ "hero_{$n}_b1_label" ] = forge_f( "Slide {$n}: button 1 label", 'text', 1 === $n ? 'Shop equipment' : '' );
		$hero[ "hero_{$n}_b1_url" ]   = forge_f( "Slide {$n}: button 1 link", 'url', '' );
		$hero[ "hero_{$n}_b2_label" ] = forge_f( "Slide {$n}: button 2 label", 'text', 1 === $n ? 'Book service' : '' );
		$hero[ "hero_{$n}_b2_url" ]   = forge_f( "Slide {$n}: button 2 link", 'url', '' );
	}
	$f['hero'] = array( 'title' => 'Hero slider', 'fields' => $hero );

	$trust    = array();
	$trust_d  = array(
		array( 'Free shipping over $500', 'Delivered to your door' ),
		array( 'Factory-trained service', 'Certified technicians on site' ),
		array( 'Financing available', 'Flexible plans, quick approval' ),
	);
	for ( $n = 1; $n <= 3; $n++ ) {
		$trust[ "trust_{$n}_title" ] = forge_f( "Item {$n}: title", 'text', $trust_d[ $n - 1 ][0] );
		$trust[ "trust_{$n}_text" ]  = forge_f( "Item {$n}: text", 'text', $trust_d[ $n - 1 ][1] );
	}
	$f['trust'] = array( 'title' => 'Trust strip', 'fields' => $trust );

	$tiles   = array();
	$tiles_d = array( 'Mowers', 'Chainsaws', 'Generators', 'Parts & accessories' );
	for ( $n = 1; $n <= 4; $n++ ) {
		$tiles[ "tile_{$n}_image" ] = forge_f( "Tile {$n}: image", 'image', 0 );
		$tiles[ "tile_{$n}_title" ] = forge_f( "Tile {$n}: title (blank hides)", 'text', $tiles_d[ $n - 1 ] );
		$tiles[ "tile_{$n}_label" ] = forge_f( "Tile {$n}: link label", 'text', 'Shop now' );
		$tiles[ "tile_{$n}_url" ]   = forge_f( "Tile {$n}: link", 'url', '' );
	}
	$f['tiles'] = array( 'title' => 'Promo tiles', 'fields' => $tiles );

	$f['countdown'] = array(
		'title'  => 'Countdown offer',
		'fields' => array(
			'cd_image'   => forge_f( 'Background image', 'image', 0 ),
			'cd_eyebrow' => forge_f( 'Small label', 'text', 'Limited time' ),
			'cd_title'   => forge_f( 'Heading', 'text', 'Season opener sale' ),
			'cd_text'    => forge_f( 'Text', 'text', 'Save on select equipment while the offer lasts.' ),
			'cd_end'     => forge_f( 'Ends (YYYY-MM-DD HH:MM, site time; blank = no timer)', 'datetime', '' ),
			'cd_btn'     => forge_f( 'Button label', 'text', 'Shop the sale' ),
			'cd_url'     => forge_f( 'Button link', 'url', '' ),
		),
	);

	$tabs   = array( 'tabs_title' => forge_f( 'Section title', 'text', 'Shop equipment' ) );
	$tabs_d = array( array( 'Newest', 'newest' ), array( 'On sale', 'sale' ), array( 'Featured', 'featured' ) );
	for ( $n = 1; $n <= 3; $n++ ) {
		$tabs[ "tab_{$n}_label" ]  = forge_f( "Tab {$n}: label", 'text', $tabs_d[ $n - 1 ][0] );
		$tabs[ "tab_{$n}_source" ] = forge_f( "Tab {$n}: products", 'select', $tabs_d[ $n - 1 ][1], array( 'choices' => forge_source_choices() ) );
		$tabs[ "tab_{$n}_slug" ]   = forge_f( "Tab {$n}: category slug", 'slug', '' );
	}
	$f['tabs'] = array( 'title' => 'Product tabs', 'fields' => $tabs );

	$cats = array(
		'cats_title' => forge_f( 'Section title', 'text', 'Shop by category' ),
		'cats_mode'  => forge_f( 'Source', 'select', 'auto', array( 'choices' => array( 'auto' => 'Automatic WooCommerce categories', 'manual' => 'Manual cards' ) ) ),
	);
	for ( $n = 1; $n <= 8; $n++ ) {
		$cats[ "cat_{$n}_title" ] = forge_f( "Card {$n}: title (blank hides)", 'text', '' );
		$cats[ "cat_{$n}_image" ] = forge_f( "Card {$n}: image", 'image', 0 );
		$cats[ "cat_{$n}_url" ]   = forge_f( "Card {$n}: link", 'url', '' );
	}
	$f['cats'] = array( 'title' => 'Category grid', 'fields' => $cats );

	$f['story'] = array(
		'title'  => 'Editorial story',
		'fields' => array(
			'story_image'   => forge_f( 'Image', 'image', 0 ),
			'story_eyebrow' => forge_f( 'Small label', 'text', 'Our story' ),
			'story_title'   => forge_f( 'Heading', 'text', 'Local dealer. Real expertise.' ),
			'story_text'    => forge_f( 'Text', 'textarea', 'We sell what we service and service what we sell. Talk to people who use this equipment every day.' ),
			'story_btn'     => forge_f( 'Button label', 'text', 'About us' ),
			'story_url'     => forge_f( 'Button link (blank = About page)', 'url', '' ),
		),
	);

	$f['journal'] = array(
		'title'  => 'Journal row',
		'fields' => array(
			'journal_title' => forge_f( 'Section title', 'text', 'From the journal' ),
			'journal_count' => forge_f( 'Posts', 'number', 3, array( 'min' => 1, 'max' => 6 ) ),
		),
	);

	$f['pages'] = array(
		'title'  => 'Page content',
		'fields' => array(
			'about_body'      => forge_f( 'About: body', 'textarea', 'We are a locally owned outdoor power equipment dealer. Sales, parts and factory-trained service under one roof.' ),
			'contact_intro'   => forge_f( 'Contact: intro', 'text', 'Questions about equipment, parts or availability? Send a note and we will reply quickly.' ),
			'service_intro'   => forge_f( 'Service: intro', 'text', 'Tell us what needs attention and we will schedule your repair or tune-up.' ),
			'financing_intro' => forge_f( 'Financing: intro', 'text', 'Spread the cost of the equipment you need with flexible financing.' ),
			'financing_steps' => forge_f( 'Financing: steps (one per line)', 'textarea', "Choose your equipment\nApply online in minutes\nPick it up or have it delivered" ),
			'financing_url'   => forge_f( 'Financing: application link', 'url', '' ),
		),
	);

	$f['shop'] = array(
		'title'  => 'Shop & pricing',
		'fields' => array(
			'cfp_on'         => forge_f( 'Enable Call for Price (reads _tw_hide_price)', 'checkbox', 1 ),
			'cfp_label'      => forge_f( 'Call for Price label', 'text', 'Call for price' ),
			'brand_taxonomy' => forge_f( 'Brand taxonomy slug', 'slug', 'product_brand' ),
			'finder_on'      => forge_f( 'Show equipment finder on shop', 'checkbox', 1 ),
			'ship_threshold' => forge_f( 'Free-shipping threshold (0 = off)', 'number', 500, array( 'min' => 0, 'max' => 100000 ) ),
		),
	);

	$f['integrations'] = array(
		'title'  => 'Integrations',
		'fields' => array(
			'ga4'              => forge_f( 'GA4 measurement ID (G-XXXXXXX)', 'idstr', '' ),
			'gtm'              => forge_f( 'Google Tag Manager ID (GTM-XXXXXXX)', 'idstr', '' ),
			'constellation_id' => forge_f( 'Constellation widget client ID', 'idstr', '' ),
			'stock_widget'     => forge_f( 'Show the TargetWeb stock-by-location area (#product-stock-div)', 'checkbox', 1 ),
		),
	);

	$f['maintenance'] = array(
		'title'  => 'Maintenance mode',
		'fields' => array(
			'mnt_on'    => forge_f( 'Enable maintenance mode (admins still see the site)', 'checkbox', 0 ),
			'mnt_title' => forge_f( 'Headline', 'text', 'We will be right back' ),
			'mnt_text'  => forge_f( 'Message', 'textarea', 'The site is getting an upgrade. Call us if you need anything in the meantime.' ),
			'mnt_phone' => forge_f( 'Phone number', 'text', '' ),
			'mnt_end'   => forge_f( 'Countdown ends (YYYY-MM-DD HH:MM, optional)', 'datetime', '' ),
		),
	);

	$f['seo'] = array(
		'title'  => 'SEO',
		'fields' => array(
			'seo_on'    => forge_f( 'Enable built-in SEO (auto-off when an SEO plugin is active)', 'checkbox', 1 ),
			'seo_desc'  => forge_f( 'Default meta description', 'textarea', '' ),
			'seo_image' => forge_f( 'Default social image', 'image', 0 ),
		),
	);

	return $f;
}

function forge_field_index() {
	static $idx = null;
	if ( null === $idx ) {
		$idx = array();
		foreach ( forge_fields() as $s ) {
			$idx = array_merge( $idx, $s['fields'] );
		}
	}
	return $idx;
}

function forge_sanitize_field( $v, $d ) {
	switch ( $d['type'] ) {
		case 'checkbox':
			return ( true === $v || 1 === $v || '1' === $v || 'on' === $v || 'true' === $v ) ? 1 : 0;
		case 'select':
			$v = is_scalar( $v ) ? (string) $v : '';
			return array_key_exists( $v, $d['choices'] ) ? $v : $d['default'];
		case 'number':
			$n = absint( $v );
			if ( isset( $d['min'] ) ) {
				$n = max( $d['min'], $n );
			}
			if ( isset( $d['max'] ) ) {
				$n = min( $d['max'], $n );
			}
			return $n;
		case 'image':
			return absint( $v );
		case 'color':
			$c = sanitize_hex_color( is_scalar( $v ) ? (string) $v : '' );
			return $c ? $c : $d['default'];
		case 'url':
			return esc_url_raw( is_scalar( $v ) ? (string) $v : '' );
		case 'email':
			return sanitize_email( is_scalar( $v ) ? (string) $v : '' );
		case 'textarea':
			return wp_kses_post( is_scalar( $v ) ? (string) $v : '' );
		case 'slug':
			return sanitize_title( is_scalar( $v ) ? (string) $v : '' );
		case 'idstr':
			return substr( preg_replace( '/[^A-Za-z0-9_\-]/', '', is_scalar( $v ) ? (string) $v : '' ), 0, 40 );
		case 'datetime':
			$v = trim( is_scalar( $v ) ? (string) $v : '' );
			return preg_match( '/^\d{4}-\d{2}-\d{2}( \d{2}:\d{2})?$/', $v ) ? $v : '';
		default:
			return sanitize_text_field( is_scalar( $v ) ? (string) $v : '' );
	}
}
