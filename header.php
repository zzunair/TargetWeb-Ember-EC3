<?php
/**
 * Header.
 *
 * @package Forge
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$forge_mega = forge_mega_terms();
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="frg-skip" href="#frg-main"><?php esc_html_e( 'Skip to content', 'forge' ); ?></a>

<?php if ( forge_opt( 'announce_on' ) && forge_opt( 'announce_text' ) ) : ?>
	<div class="frg-announce">
		<?php if ( forge_opt( 'announce_url' ) ) : ?>
			<a href="<?php echo esc_url( forge_opt( 'announce_url' ) ); ?>"><?php echo esc_html( forge_opt( 'announce_text' ) ); ?></a>
		<?php else : ?>
			<?php echo esc_html( forge_opt( 'announce_text' ) ); ?>
		<?php endif; ?>
	</div>
<?php endif; ?>

<header class="frg-header">
	<div class="frg-wrap frg-header__row">
		<button type="button" class="frg-iconlink frg-burger" data-frg-open="mobile" aria-label="<?php esc_attr_e( 'Open menu', 'forge' ); ?>" aria-controls="frg-mobile" aria-expanded="false"><?php echo forge_icon( 'menu', 24 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
		<?php forge_logo(); ?>

		<nav class="frg-nav" aria-label="<?php esc_attr_e( 'Primary', 'forge' ); ?>">
			<div class="frg-nav__item">
				<button type="button" class="frg-nav__btn" aria-expanded="false" aria-controls="frg-mega" data-frg-mega><?php esc_html_e( 'Departments', 'forge' ); ?> <?php echo forge_icon( 'chev', 14 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
				<div class="frg-mega" id="frg-mega" hidden>
					<div class="frg-wrap">
						<?php if ( $forge_mega ) : ?>
							<div class="frg-mega__grid">
								<?php foreach ( $forge_mega as $col ) : ?>
									<div>
										<h3><a href="<?php echo esc_url( get_term_link( $col['term'] ) ); ?>"><?php echo esc_html( $col['term']->name ); ?></a></h3>
										<ul>
											<?php foreach ( $col['kids'] as $kid ) : ?>
												<li><a href="<?php echo esc_url( get_term_link( $kid ) ); ?>"><?php echo esc_html( $kid->name ); ?></a></li>
											<?php endforeach; ?>
										</ul>
									</div>
								<?php endforeach; ?>
							</div>
						<?php else : ?>
							<a class="frg-link" href="<?php echo esc_url( forge_shop_url() ); ?>"><?php esc_html_e( 'Browse all equipment', 'forge' ); ?></a>
						<?php endif; ?>
					</div>
				</div>
			</div>
			<div class="frg-nav__item frg-nav__item--deals">
				<?php if ( has_nav_menu( 'forge_deals' ) ) : ?>
					<button type="button" class="frg-nav__btn frg-nav__link" aria-expanded="false" aria-controls="frg-deals" data-frg-drop><?php echo esc_html( forge_opt( 'deals_label' ) ); ?> <?php echo forge_icon( 'chev', 14 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
					<div class="frg-drop" id="frg-deals" hidden>
						<?php wp_nav_menu( array( 'theme_location' => 'forge_deals', 'container' => false, 'depth' => 1, 'fallback_cb' => false ) ); ?>
					</div>
				<?php else : ?>
					<a class="frg-nav__link" href="<?php echo esc_url( forge_deals_url() ); ?>"><?php echo esc_html( forge_opt( 'deals_label' ) ); ?></a>
				<?php endif; ?>
			</div>
		</nav>

		<?php forge_search_form( 'frg-s-desktop' ); ?>

		<div class="frg-actions">
			<a class="frg-iconlink frg-hide-m" href="<?php echo esc_url( forge_has_wc() ? wc_get_page_permalink( 'myaccount' ) : wp_login_url() ); ?>" aria-label="<?php esc_attr_e( 'Account', 'forge' ); ?>"><?php echo forge_icon( 'user', 22 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
			<button type="button" class="frg-iconlink frg-hide-m" data-frg-open="wish" aria-label="<?php esc_attr_e( 'Saved items', 'forge' ); ?>"><?php echo forge_icon( 'heart', 22 ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span class="frg-count" data-frg-wish-count data-zero="1">0</span></button>
			<?php if ( forge_has_wc() ) : $forge_n = WC()->cart ? WC()->cart->get_cart_contents_count() : 0; ?>
				<a class="frg-iconlink" href="<?php echo esc_url( wc_get_cart_url() ); ?>" data-frg-open="cart" aria-label="<?php esc_attr_e( 'Cart', 'forge' ); ?>"><?php echo forge_icon( 'cart', 22 ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span class="frg-count frg-count--cart" data-zero="<?php echo $forge_n ? '0' : '1'; ?>"><?php echo esc_html( $forge_n ); ?></span></a>
			<?php endif; ?>
		</div>
	</div>

	<?php if ( forge_opt( 'secondary_on' ) ) : ?>
		<div class="frg-secondary">
			<nav class="frg-wrap" aria-label="<?php esc_attr_e( 'Secondary', 'forge' ); ?>">
				<?php if ( has_nav_menu( 'forge_secondary' ) ) : ?>
					<?php wp_nav_menu( array( 'theme_location' => 'forge_secondary', 'container' => false, 'depth' => 1, 'fallback_cb' => false ) ); ?>
				<?php else : ?>
					<ul>
						<?php foreach ( forge_secondary_links() as $forge_l => $forge_u ) : ?>
							<li><a href="<?php echo esc_url( $forge_u ); ?>"><?php echo esc_html( $forge_l ); ?></a></li>
						<?php endforeach; ?>
						<?php if ( forge_opt( 'dealer_phone' ) ) : ?>
							<li class="frg-secondary__phone"><a href="<?php echo esc_url( forge_phone_href() ); ?>"><?php echo esc_html( forge_opt( 'dealer_phone' ) ); ?></a></li>
						<?php endif; ?>
					</ul>
				<?php endif; ?>
			</nav>
		</div>
	<?php endif; ?>
</header>

<div class="frg-overlay" data-frg-close hidden></div>

<aside class="frg-drawer frg-drawer--left frg-mobile" id="frg-mobile" aria-label="<?php esc_attr_e( 'Menu', 'forge' ); ?>" hidden>
	<div class="frg-drawer__head"><?php forge_logo(); ?><button type="button" class="frg-iconlink" data-frg-close aria-label="<?php esc_attr_e( 'Close menu', 'forge' ); ?>"><?php echo forge_icon( 'close', 22 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button></div>
	<div class="frg-drawer__body">
		<div class="frg-mobile__search"><?php forge_search_form( 'frg-s-mobile' ); ?></div>
		<ul class="frg-mobile__links">
			<?php if ( $forge_mega ) : ?>
				<li>
					<details class="frg-mdept">
						<summary><?php esc_html_e( 'Departments', 'forge' ); ?> <?php echo forge_icon( 'chev', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></summary>
						<ul class="frg-mdept__list">
							<?php foreach ( $forge_mega as $col ) : ?>
								<li>
									<?php if ( $col['kids'] ) : ?>
										<details class="frg-mdept__sub">
											<summary><?php echo esc_html( $col['term']->name ); ?> <?php echo forge_icon( 'chev', 14 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></summary>
											<ul>
												<li><a href="<?php echo esc_url( get_term_link( $col['term'] ) ); ?>"><?php esc_html_e( 'View all', 'forge' ); ?></a></li>
												<?php foreach ( $col['kids'] as $kid ) : ?>
													<li><a href="<?php echo esc_url( get_term_link( $kid ) ); ?>"><?php echo esc_html( $kid->name ); ?></a></li>
												<?php endforeach; ?>
											</ul>
										</details>
									<?php else : ?>
										<a href="<?php echo esc_url( get_term_link( $col['term'] ) ); ?>"><?php echo esc_html( $col['term']->name ); ?></a>
									<?php endif; ?>
								</li>
							<?php endforeach; ?>
						</ul>
					</details>
				</li>
			<?php endif; ?>
			<li><a href="<?php echo esc_url( forge_deals_url() ); ?>" style="color:var(--frg-accent)"><?php echo esc_html( forge_opt( 'deals_label' ) ); ?></a></li>
			<?php foreach ( forge_secondary_links() as $forge_l => $forge_u ) : ?>
				<li><a href="<?php echo esc_url( $forge_u ); ?>"><?php echo esc_html( $forge_l ); ?></a></li>
			<?php endforeach; ?>
			<li><button type="button" class="frg-nav__link" data-frg-open="wish"><?php esc_html_e( 'Saved items', 'forge' ); ?></button></li>
		</ul>
	</div>
</aside>

<nav class="frg-mbar" aria-label="<?php esc_attr_e( 'Quick links', 'forge' ); ?>">
	<button type="button" data-frg-open="mobile-search"><?php echo forge_icon( 'search', 22 ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php esc_html_e( 'Search', 'forge' ); ?></button>
	<a href="<?php echo esc_url( forge_shop_url() ); ?>"><?php echo forge_icon( 'shop', 22 ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php esc_html_e( 'Shop', 'forge' ); ?></a>
	<a href="<?php echo esc_url( forge_has_wc() ? wc_get_page_permalink( 'myaccount' ) : wp_login_url() ); ?>"><?php echo forge_icon( 'user', 22 ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php esc_html_e( 'Account', 'forge' ); ?></a>
	<?php if ( forge_has_wc() ) : ?>
		<a href="<?php echo esc_url( wc_get_cart_url() ); ?>" data-frg-open="cart"><?php echo forge_icon( 'cart', 22 ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php esc_html_e( 'Cart', 'forge' ); ?><span class="frg-count frg-count--cart" data-zero="<?php echo $forge_n ? '0' : '1'; ?>"><?php echo esc_html( $forge_n ); ?></span></a>
	<?php endif; ?>
</nav>
