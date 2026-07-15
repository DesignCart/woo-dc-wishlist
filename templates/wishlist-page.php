<?php
/**
 * Wishlist page template.
 *
 * @var WC_Product[] $products
 *
 * @package DesignCartWishlist
 * @author  Paweł Nosko
 * @company Design Cart
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="dc-wishlist-page">
	<h2><?php esc_html_e( 'Twoja lista życzeń', 'design-cart-wishlist' ); ?></h2>

	<?php if ( empty( $products ) ) : ?>
		<div class="dc-wishlist-empty">
			<p><?php esc_html_e( 'Twoja lista życzeń jest pusta.', 'design-cart-wishlist' ); ?></p>
			<a class="button wc-forward" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>">
				<?php esc_html_e( 'Przeglądaj produkty', 'design-cart-wishlist' ); ?>
			</a>
		</div>
	<?php else : ?>
		<ul class="dc-wishlist-list">
			<?php foreach ( $products as $dc_wishlist_product ) : ?>
				<li class="dc-wishlist-item product">
					<a class="dc-wishlist-item__image" href="<?php echo esc_url( $dc_wishlist_product->get_permalink() ); ?>">
						<?php echo wp_kses_post( $dc_wishlist_product->get_image( 'woocommerce_gallery_thumbnail' ) ); ?>
					</a>
					<div class="dc-wishlist-item__body">
						<h3 class="dc-wishlist-item__title">
							<a href="<?php echo esc_url( $dc_wishlist_product->get_permalink() ); ?>">
								<?php echo esc_html( $dc_wishlist_product->get_name() ); ?>
							</a>
						</h3>
						<div class="dc-wishlist-item__price">
							<?php echo wp_kses_post( $dc_wishlist_product->get_price_html() ); ?>
						</div>
					</div>
					<div class="dc-wishlist-item__actions">
						<a href="<?php echo esc_url( $dc_wishlist_product->add_to_cart_url() ); ?>"
							class="button add_to_cart_button"
							data-product_id="<?php echo esc_attr( (string) $dc_wishlist_product->get_id() ); ?>"
							aria-label="<?php echo esc_attr( $dc_wishlist_product->add_to_cart_text() ); ?>">
							<?php echo esc_html( $dc_wishlist_product->add_to_cart_text() ); ?>
						</a>
						<?php
						echo DC_Wishlist_Button::render_product_button( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							$dc_wishlist_product->get_id(),
							array(
								'source'   => 'page',
								'position' => 'inline',
							)
						);
						?>
					</div>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>
</div>
