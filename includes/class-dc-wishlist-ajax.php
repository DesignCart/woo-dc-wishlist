<?php
/**
 * AJAX handlers.
 *
 * @package DesignCartWishlist
 * @author  Paweł Nosko
 * @company Design Cart
 */

defined( 'ABSPATH' ) || exit;

class DC_Wishlist_Ajax {

	public static function init(): void {
		add_action( 'wp_ajax_dc_wishlist_toggle', array( __CLASS__, 'toggle' ) );
		add_action( 'wp_ajax_nopriv_dc_wishlist_toggle', array( __CLASS__, 'toggle' ) );
	}

	public static function toggle(): void {
		check_ajax_referer( 'dc_wishlist', 'nonce' );

		if ( ! DC_Wishlist_Settings::is_enabled() ) {
			wp_send_json_error( array( 'message' => __( 'Lista życzeń jest wyłączona.', 'design-cart-wishlist' ) ), 403 );
		}

		$settings   = DC_Wishlist_Settings::get_all();
		$product_id = absint( $_POST['product_id'] ?? 0 );

		if ( ! $product_id ) {
			wp_send_json_error( array( 'message' => __( 'Nieprawidłowy produkt.', 'design-cart-wishlist' ) ), 400 );
		}

		if ( ! is_user_logged_in() && 'login_only' === $settings['general']['guest_mode'] ) {
			wp_send_json_error(
				array(
					'message'    => __( 'Zaloguj się, aby korzystać z listy życzeń.', 'design-cart-wishlist' ),
					'login_url'  => DC_Wishlist_Settings::login_url(),
					'needs_auth' => true,
				),
				401
			);
		}

		$result = DC_Wishlist_Storage::toggle( $product_id );

		wp_send_json_success(
			array(
				'product_id' => $product_id,
				'action'     => $result['action'],
				'ids'        => $result['ids'],
				'count'      => count( $result['ids'] ),
				'in_list'    => 'added' === $result['action'],
			)
		);
	}
}
