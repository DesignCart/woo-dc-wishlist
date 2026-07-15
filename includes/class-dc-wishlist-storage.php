<?php
/**
 * Wishlist persistence (user meta + guest cookie).
 *
 * @package DesignCartWishlist
 * @author  Paweł Nosko
 * @company Design Cart
 */

defined( 'ABSPATH' ) || exit;

class DC_Wishlist_Storage {

	private const USER_META_KEY = '_dc_wishlist';

	public static function get_ids(): array {
		if ( is_user_logged_in() ) {
			return self::get_user_ids( get_current_user_id() );
		}

		return self::get_guest_ids();
	}

	public static function has( int $product_id ): bool {
		return in_array( $product_id, self::get_ids(), true );
	}

	public static function add( int $product_id ): array {
		$product_id = self::validate_product( $product_id );
		if ( ! $product_id ) {
			return self::get_ids();
		}

		if ( is_user_logged_in() ) {
			$ids = self::get_user_ids( get_current_user_id() );
			if ( ! in_array( $product_id, $ids, true ) ) {
				$ids[] = $product_id;
				update_user_meta( get_current_user_id(), self::USER_META_KEY, array_values( array_unique( $ids ) ) );
			}
			return self::get_user_ids( get_current_user_id() );
		}

		$ids = self::get_guest_ids();
		if ( ! in_array( $product_id, $ids, true ) ) {
			$ids[] = $product_id;
			self::set_guest_ids( $ids );
		}

		return self::get_guest_ids();
	}

	public static function remove( int $product_id ): array {
		$product_id = absint( $product_id );
		if ( ! $product_id ) {
			return self::get_ids();
		}

		if ( is_user_logged_in() ) {
			$ids = array_values(
				array_filter(
					self::get_user_ids( get_current_user_id() ),
					static function ( $id ) use ( $product_id ) {
						return (int) $id !== $product_id;
					}
				)
			);
			update_user_meta( get_current_user_id(), self::USER_META_KEY, $ids );
			return $ids;
		}

		$ids = array_values(
			array_filter(
				self::get_guest_ids(),
				static function ( $id ) use ( $product_id ) {
					return (int) $id !== $product_id;
				}
			)
		);
		self::set_guest_ids( $ids );

		return $ids;
	}

	public static function toggle( int $product_id ): array {
		if ( self::has( $product_id ) ) {
			self::remove( $product_id );
			return array(
				'ids'    => self::get_ids(),
				'action' => 'removed',
			);
		}

		self::add( $product_id );

		return array(
			'ids'    => self::get_ids(),
			'action' => 'added',
		);
	}

	public static function merge_guest_into_user( int $user_id ): void {
		$guest_ids = self::get_guest_ids();
		if ( empty( $guest_ids ) ) {
			return;
		}

		$user_ids = self::get_user_ids( $user_id );
		$merged   = array_values( array_unique( array_merge( $user_ids, $guest_ids ) ) );

		update_user_meta( $user_id, self::USER_META_KEY, $merged );
		self::clear_guest_cookie();
	}

	public static function get_products(): array {
		$products = array();

		foreach ( self::get_ids() as $product_id ) {
			$product = wc_get_product( $product_id );
			if ( $product && $product->is_visible() ) {
				$products[] = $product;
			}
		}

		return $products;
	}

	private static function get_user_ids( int $user_id ): array {
		$stored = get_user_meta( $user_id, self::USER_META_KEY, true );

		if ( ! is_array( $stored ) ) {
			return array();
		}

		return array_values(
			array_filter(
				array_map( 'absint', $stored )
			)
		);
	}

	private static function get_guest_ids(): array {
		if ( empty( $_COOKIE[ DC_WISHLIST_COOKIE ] ) ) {
			return array();
		}

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Validated as JSON array of integers below.
		$raw     = wp_unslash( $_COOKIE[ DC_WISHLIST_COOKIE ] );
		$decoded = json_decode( is_string( $raw ) ? $raw : '', true );

		if ( ! is_array( $decoded ) ) {
			return array();
		}

		return array_values(
			array_filter(
				array_map( 'absint', $decoded )
			)
		);
	}

	private static function set_guest_ids( array $ids ): void {
		$ids = array_values(
			array_filter(
				array_map( 'absint', $ids )
			)
		);

		$json = wp_json_encode( $ids );
		if ( ! $json ) {
			return;
		}

		setcookie(
			DC_WISHLIST_COOKIE,
			$json,
			time() + YEAR_IN_SECONDS,
			COOKIEPATH ? COOKIEPATH : '/',
			COOKIE_DOMAIN,
			is_ssl(),
			true
		);

		$_COOKIE[ DC_WISHLIST_COOKIE ] = $json;
	}

	private static function clear_guest_cookie(): void {
		setcookie(
			DC_WISHLIST_COOKIE,
			'',
			time() - HOUR_IN_SECONDS,
			COOKIEPATH ? COOKIEPATH : '/',
			COOKIE_DOMAIN,
			is_ssl(),
			true
		);
		unset( $_COOKIE[ DC_WISHLIST_COOKIE ] );
	}

	private static function validate_product( int $product_id ): int {
		$product_id = absint( $product_id );
		if ( ! $product_id ) {
			return 0;
		}

		$product = wc_get_product( $product_id );
		if ( ! $product || ! $product->exists() ) {
			return 0;
		}

		return $product_id;
	}
}
