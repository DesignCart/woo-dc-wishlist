<?php
/**
 * Wishlist button / link markup.
 *
 * @package DesignCartWishlist
 * @author  Paweł Nosko
 * @company Design Cart
 */

defined( 'ABSPATH' ) || exit;

class DC_Wishlist_Button {

	/**
	 * Product toggle button (loop / single / rules).
	 */
	public static function render_product_button( int $product_id, array $args = array() ): string {
		$args = wp_parse_args(
			$args,
			array(
				'context'  => 'product',
				'source'   => 'hook',
				'rule_id'  => '',
				'position' => 'inline',
				'classes'  => array(),
			)
		);

		$product_id = absint( $product_id );
		if ( ! $product_id ) {
			return '';
		}

		$in_wishlist = DC_Wishlist_Storage::has( $product_id );
		$classes     = array_merge(
			array(
				'dc-wishlist-btn',
				'dc-wishlist-btn--product',
				'dc-wishlist-btn--' . sanitize_html_class( $args['position'] ),
			),
			array_map( 'sanitize_html_class', (array) $args['classes'] )
		);

		if ( $in_wishlist ) {
			$classes[] = 'is-active';
		}

		$attrs = array(
			'type'                 => 'button',
			'class'                => implode( ' ', $classes ),
			'data-product-id'      => (string) $product_id,
			'data-dc-wishlist'     => 'toggle',
			'data-dc-wishlist-src' => sanitize_key( $args['source'] ),
			'aria-pressed'         => $in_wishlist ? 'true' : 'false',
			'aria-label'           => $in_wishlist
				? __( 'Usuń z listy życzeń', 'design-cart-wishlist' )
				: __( 'Dodaj do listy życzeń', 'design-cart-wishlist' ),
		);

		if ( ! empty( $args['rule_id'] ) ) {
			$attrs['data-dc-wishlist-rule'] = sanitize_key( $args['rule_id'] );
		}

		return self::wrap_if_overlay(
			self::build_element( 'button', $attrs, self::heart_icon( $in_wishlist ) ),
			$args['position']
		);
	}

	/**
	 * Header link to wishlist or login.
	 */
	public static function render_header_link(): string {
		$settings = DC_Wishlist_Settings::get_all();
		$url      = DC_Wishlist_Settings::wishlist_page_url();
		$count    = count( DC_Wishlist_Storage::get_ids() );

		if ( ! is_user_logged_in() ) {
			if ( 'login' === $settings['header']['guest_behavior'] ) {
				$url = DC_Wishlist_Settings::login_url();
			} elseif ( 'login_only' === $settings['general']['guest_mode'] ) {
				$url = DC_Wishlist_Settings::login_url();
			}
		}

		$attrs = array(
			'href'               => esc_url( $url ),
			'class'              => 'dc-wishlist-btn dc-wishlist-btn--header',
			'data-dc-wishlist'   => 'header-link',
			'aria-label'         => __( 'Lista życzeń', 'design-cart-wishlist' ),
		);

		$inner = self::heart_icon( $count > 0, true );
		if ( $count > 0 ) {
			$inner .= '<span class="dc-wishlist-btn__count">' . esc_html( (string) $count ) . '</span>';
		}

		return self::build_element( 'a', $attrs, $inner );
	}

	/**
	 * Inline SVG heart.
	 */
	public static function heart_icon( bool $filled = false, bool $header = false ): string {
		$class = 'dc-wishlist-btn__icon';
		if ( $filled ) {
			$class .= ' is-filled';
		}
		if ( $header ) {
			$class .= ' dc-wishlist-btn__icon--header';
		}

		if ( $filled ) {
			return '<svg class="' . esc_attr( $class ) . '" viewBox="0 0 24 24" width="1em" height="1em" aria-hidden="true"><path fill="currentColor" d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>';
		}

		return '<svg class="' . esc_attr( $class ) . '" viewBox="0 0 24 24" width="1em" height="1em" aria-hidden="true"><path fill="none" stroke="currentColor" stroke-width="2" d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>';
	}

	private static function wrap_if_overlay( string $html, string $position ): string {
		if ( 'inline' === $position ) {
			return $html;
		}

		return '<span class="dc-wishlist-wrap dc-wishlist-wrap--' . esc_attr( sanitize_html_class( $position ) ) . '">' . $html . '</span>';
	}

	private static function build_element( string $tag, array $attrs, string $inner ): string {
		$parts = array();
		foreach ( $attrs as $key => $value ) {
			if ( 'href' === $key ) {
				$parts[] = sprintf( '%s="%s"', esc_attr( $key ), esc_url( $value ) );
			} else {
				$parts[] = sprintf( '%s="%s"', esc_attr( $key ), esc_attr( (string) $value ) );
			}
		}

		return sprintf( '<%1$s %2$s>%3$s</%1$s>', tag_escape( $tag ), implode( ' ', $parts ), $inner );
	}
}
