<?php
/**
 * Plugin Name:       Design Cart Wishlist for WooCommerce
 * Plugin URI:        https://designcart.pl
 * Description:       Lista życzeń WooCommerce z konfigurowalnym placement serduszka w headerze, na listach produktów i karcie produktu.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Paweł Nosko
 * Author URI:        https://designcart.pl
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       design-cart-wishlist
 * Domain Path:       /languages
 *
 * @package DesignCartWishlist
 */

defined( 'ABSPATH' ) || exit;

define( 'DC_WISHLIST_VERSION', '1.0.0' );
define( 'DC_WISHLIST_FILE', __FILE__ );
define( 'DC_WISHLIST_PATH', plugin_dir_path( __FILE__ ) );
define( 'DC_WISHLIST_URL', plugin_dir_url( __FILE__ ) );
define( 'DC_WISHLIST_OPTION', 'dc_wishlist_settings' );
define( 'DC_WISHLIST_COOKIE', 'dc_wishlist' );

require_once DC_WISHLIST_PATH . 'includes/class-dc-wishlist-settings.php';
require_once DC_WISHLIST_PATH . 'includes/class-dc-wishlist-storage.php';
require_once DC_WISHLIST_PATH . 'includes/class-dc-wishlist-button.php';
require_once DC_WISHLIST_PATH . 'includes/class-dc-wishlist-ajax.php';
require_once DC_WISHLIST_PATH . 'includes/class-dc-wishlist-frontend.php';
require_once DC_WISHLIST_PATH . 'includes/class-dc-wishlist-admin.php';

/**
 * Bootstrap plugin.
 */
function dc_wishlist_init(): void {
	if ( ! class_exists( 'WooCommerce' ) ) {
		add_action(
			'admin_notices',
			static function (): void {
				printf(
					'<div class="notice notice-error"><p>%s</p></div>',
					esc_html__( 'Design Cart Wishlist wymaga aktywnego WooCommerce.', 'design-cart-wishlist' )
				);
			}
		);
		return;
	}

	DC_Wishlist_Ajax::init();
	DC_Wishlist_Frontend::init();

	if ( is_admin() ) {
		DC_Wishlist_Admin::init();
	}
}
add_action( 'plugins_loaded', 'dc_wishlist_init' );

/**
 * Activation: defaults + wishlist page.
 */
function dc_wishlist_activate(): void {
	DC_Wishlist_Settings::ensure_defaults();

	$settings = DC_Wishlist_Settings::get_all();

	if ( empty( $settings['general']['wishlist_page_id'] ) ) {
		$page_id = wp_insert_post(
			array(
				'post_title'   => __( 'Lista życzeń', 'design-cart-wishlist' ),
				'post_content' => '[dc_wishlist]',
				'post_status'  => 'publish',
				'post_type'    => 'page',
			),
			true
		);

		if ( ! is_wp_error( $page_id ) ) {
			$settings['general']['wishlist_page_id'] = (int) $page_id;
			update_option( DC_WISHLIST_OPTION, $settings );
		}
	}
}
register_activation_hook( __FILE__, 'dc_wishlist_activate' );

/**
 * Merge guest wishlist after login.
 */
function dc_wishlist_on_login( string $user_login, WP_User $user ): void {
	$settings = DC_Wishlist_Settings::get_all();

	if ( empty( $settings['general']['merge_on_login'] ) ) {
		return;
	}

	DC_Wishlist_Storage::merge_guest_into_user( $user->ID );
}
add_action( 'wp_login', 'dc_wishlist_on_login', 10, 2 );
