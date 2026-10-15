<?php
/**
 * Footer, cart drawer, modal, compare bar.
 *
 * @package Forge
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( forge_opt( 'newsletter_inner' ) && ! is_front_page() ) {
	forge_newsletter_section();
}
$forge_copy = forge_opt( 'footer_copy' );
?>
<footer class="frg-footer">
	<div class="frg-wrap frg-foot">
		<div>
			<?php forge_logo(); ?>
			<p><?php echo esc_html( wp_strip_all_tags( forge_opt( 'footer_about' ) ) ); ?></p>
			<div class="frg-foot__social">
				<?php foreach ( array( 'dealer_facebook' => 'Facebook', 'dealer_instagram' => 'Instagram', 'dealer_youtube' => 'YouTube' ) as $forge_k => $forge_n2 ) : if ( forge_opt( $forge_k ) ) : ?>
					<a href="<?php echo esc_url( forge_opt( $forge_k ) ); ?>" rel="noopener" target="_blank"><?php echo esc_html( $forge_n2 ); ?></a>
				<?php endif; endforeach; ?>
			</div>
		</div>
		<div>
			<h4><?php esc_html_e( 'Shop', 'forge' ); ?></h4>
			<ul>
				<?php foreach ( array_slice( forge_mega_terms(), 0, 6 ) as $col ) : ?>
					<li><a href="<?php echo esc_url( get_term_link( $col['term'] ) ); ?>"><?php echo esc_html( $col['term']->name ); ?></a></li>
				<?php endforeach; ?>
				<li><a href="<?php echo esc_url( forge_deals_url() ); ?>"><?php echo esc_html( forge_opt( 'deals_label' ) ); ?></a></li>
			</ul>
		</div>
		<div>
			<h4><?php esc_html_e( 'Company', 'forge' ); ?></h4>
			<?php if ( has_nav_menu( 'forge_footer' ) ) : ?>
				<?php wp_nav_menu( array( 'theme_location' => 'forge_footer', 'container' => false, 'depth' => 1 ) ); ?>
			<?php else : ?>
				<ul>
					<li><a href="<?php echo esc_url( forge_page_url( 'about' ) ); ?>"><?php esc_html_e( 'About', 'forge' ); ?></a></li>
					<li><a href="<?php echo esc_url( forge_page_url( 'service' ) ); ?>"><?php esc_html_e( 'Service', 'forge' ); ?></a></li>
					<li><a href="<?php echo esc_url( forge_page_url( 'financing' ) ); ?>"><?php esc_html_e( 'Financing', 'forge' ); ?></a></li>
					<li><a href="<?php echo esc_url( forge_page_url( 'contact' ) ); ?>"><?php esc_html_e( 'Contact', 'forge' ); ?></a></li>
				</ul>
			<?php endif; ?>
		</div>
		<div>
			<h4><?php esc_html_e( 'Visit', 'forge' ); ?></h4>
			<address>
				<?php
				$forge_a = array_filter( array( forge_opt( 'dealer_address' ), trim( forge_opt( 'dealer_city' ) . ' ' . forge_opt( 'dealer_state' ) . ' ' . forge_opt( 'dealer_zip' ) ) ) );
				echo wp_kses_post( implode( '<br>', array_map( 'esc_html', $forge_a ) ) );
				?>
			</address>
			<?php if ( forge_opt( 'dealer_phone' ) ) : ?>
				<p><a href="<?php echo esc_url( forge_phone_href() ); ?>"><?php echo esc_html( forge_opt( 'dealer_phone' ) ); ?></a></p>
			<?php endif; ?>
			<p><?php echo wp_kses_post( nl2br( esc_html( wp_strip_all_tags( forge_opt( 'dealer_hours' ) ) ) ) ); ?></p>
		</div>
	</div>
	<div class="frg-wrap frg-foot__bottom">
		<span><?php echo esc_html( $forge_copy ? $forge_copy : '© ' . gmdate( 'Y' ) . ' ' . ( forge_opt( 'dealer_name' ) ? forge_opt( 'dealer_name' ) : get_bloginfo( 'name' ) ) ); ?></span>
		<span><?php esc_html_e( 'All rights reserved.', 'forge' ); ?></span>
	</div>
</footer>

<?php if ( forge_has_wc() ) : ?>
<aside class="frg-drawer frg-drawer--right" id="frg-cart" aria-label="<?php esc_attr_e( 'Cart', 'forge' ); ?>" hidden>
	<div class="frg-drawer__head"><h2><?php esc_html_e( 'Your cart', 'forge' ); ?></h2><button type="button" class="frg-iconlink" data-frg-close aria-label="<?php esc_attr_e( 'Close cart', 'forge' ); ?>"><?php echo forge_icon( 'close', 22 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button></div>
	<?php forge_ship_progress(); ?>
	<div class="frg-drawer__body"><div class="widget_shopping_cart_content"><?php woocommerce_mini_cart(); ?></div></div>
</aside>
<?php endif; ?>

<div class="frg-modal" id="frg-modal" role="dialog" aria-modal="true" aria-labelledby="frg-modal-title" hidden>
	<div class="frg-modal__panel">
		<button type="button" class="frg-iconlink frg-modal__close" data-frg-close-modal aria-label="<?php esc_attr_e( 'Close', 'forge' ); ?>"><?php echo forge_icon( 'close', 22 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
		<h2 class="frg-modal__title" id="frg-modal-title"></h2>
		<div class="frg-modal__body"></div>
	</div>
</div>

<div class="frg-comparebar" hidden>
	<span><b data-frg-compare-n>0</b> <?php esc_html_e( 'of 4 selected', 'forge' ); ?></span>
	<button type="button" class="frg-btn frg-btn--sm" data-frg-compare-open><?php esc_html_e( 'Compare', 'forge' ); ?></button>
	<button type="button" class="frg-link" data-frg-compare-clear><?php esc_html_e( 'Clear', 'forge' ); ?></button>
</div>

<?php forge_crm_modal(); ?>
<?php wp_footer(); ?>
</body>
</html>
