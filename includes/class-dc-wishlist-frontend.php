<?php
/**
 * Frontend hooks, assets, shortcodes.
 *
 * @package DesignCartWishlist
 * @author  Paweł Nosko
 * @company Design Cart
 */

defined( 'ABSPATH' ) || exit;

class DC_Wishlist_Frontend {

	public static function init(): void {
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );
		add_action( 'woocommerce_after_shop_loop_item', array( __CLASS__, 'render_loop_button' ), 15 );
		add_action( 'woocommerce_single_product_summary', array( __CLASS__, 'render_single_button' ), 35 );
		add_filter( 'woocommerce_blocks_product_grid_item_html', array( __CLASS__, 'inject_block_grid_button' ), 10, 3 );

		add_shortcode( 'dc_wishlist', array( __CLASS__, 'shortcode_wishlist_page' ) );
		add_shortcode( 'dc_wishlist_button', array( __CLASS__, 'shortcode_button' ) );
	}

	public static function enqueue_assets(): void {
		if ( ! DC_Wishlist_Settings::is_enabled() ) {
			return;
		}

		wp_enqueue_style(
			'dc-wishlist-front',
			DC_WISHLIST_URL . 'assets/css/wishlist-front.css',
			array(),
			DC_WISHLIST_VERSION
		);

		wp_add_inline_style( 'dc-wishlist-front', self::build_appearance_css() );

		wp_enqueue_script(
			'dc-wishlist-front',
			DC_WISHLIST_URL . 'assets/js/wishlist-front.js',
			array(),
			DC_WISHLIST_VERSION,
			true
		);

		$settings = DC_Wishlist_Settings::get_all();

		wp_localize_script(
			'dc-wishlist-front',
			'dcWishlist',
			array(
				'ajaxUrl'      => admin_url( 'admin-ajax.php' ),
				'nonce'        => wp_create_nonce( 'dc_wishlist' ),
				'wishlistUrl'  => DC_Wishlist_Settings::wishlist_page_url(),
				'loginUrl'     => DC_Wishlist_Settings::login_url(),
				'isLoggedIn'   => is_user_logged_in(),
				'guestMode'    => $settings['general']['guest_mode'],
				'productIds'   => DC_Wishlist_Storage::get_ids(),
				'header'       => $settings['header'],
				'rules'        => array_values(
					array_filter(
						$settings['rules'],
						static function ( $rule ) {
							return ! empty( $rule['enabled'] ) && ! empty( $rule['selector'] );
						}
					)
				),
				'debug'        => ! empty( $settings['advanced']['debug_mode'] ) && current_user_can( 'manage_options' ),
				'context'      => self::get_page_context(),
				'currentUrl'   => self::current_url(),
				'i18n'         => array(
					'added'       => __( 'Dodano do listy życzeń', 'design-cart-wishlist' ),
					'removed'     => __( 'Usunięto z listy życzeń', 'design-cart-wishlist' ),
					'loginNeeded' => __( 'Zaloguj się, aby dodać produkt do listy życzeń.', 'design-cart-wishlist' ),
					'error'       => __( 'Wystąpił błąd. Spróbuj ponownie.', 'design-cart-wishlist' ),
				),
				'heartSvg'     => array(
					'outline' => DC_Wishlist_Button::heart_icon( false ),
					'filled'  => DC_Wishlist_Button::heart_icon( true ),
				),
			)
		);
	}

	public static function build_appearance_css(): string {
		if ( ! DC_Wishlist_Settings::is_enabled() ) {
			return '';
		}

		$appearance = DC_Wishlist_Settings::get( 'appearance' );
		$header     = $appearance['header'] ?? array();
		$product    = $appearance['product'] ?? array();

		return '.dc-wishlist-btn--header{' .
			'--dc-wl-bg:' . ( $header['bg'] ?? 'transparent' ) . ';' .
			'--dc-wl-color:' . ( $header['color'] ?? '#262c38' ) . ';' .
			'--dc-wl-size:' . ( $header['font_size'] ?? '20px' ) . ';' .
			'--dc-wl-hover-bg:' . ( $header['hover_bg'] ?? 'transparent' ) . ';' .
			'--dc-wl-hover-color:' . ( $header['hover_color'] ?? '#1fa28c' ) . ';' .
			'}' .
			'.dc-wishlist-btn--product{' .
			'--dc-wl-bg:' . ( $product['bg'] ?? '#fff' ) . ';' .
			'--dc-wl-color:' . ( $product['color'] ?? '#262c38' ) . ';' .
			'--dc-wl-size:' . ( $product['font_size'] ?? '18px' ) . ';' .
			'--dc-wl-hover-bg:' . ( $product['hover_bg'] ?? '#fff' ) . ';' .
			'--dc-wl-hover-color:' . ( $product['hover_color'] ?? '#e74c3c' ) . ';' .
			'--dc-wl-active-color:' . ( $product['active_color'] ?? '#e74c3c' ) . ';' .
			'--dc-wl-active-bg:' . ( $product['active_bg'] ?? '#fff' ) . ';' .
			'}';
	}

	public static function render_loop_button(): void {
		if ( ! self::hooks_enabled( 'loop' ) ) {
			return;
		}

		global $product;
		if ( ! $product instanceof WC_Product ) {
			return;
		}

		echo self::get_product_button_html( $product->get_id(), 'hook', 'overlay-top-right' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	public static function render_single_button(): void {
		if ( ! self::hooks_enabled( 'single' ) ) {
			return;
		}

		global $product;
		if ( ! $product instanceof WC_Product ) {
			return;
		}

		echo self::get_product_button_html( $product->get_id(), 'hook', 'inline' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	public static function inject_block_grid_button( string $html, $data, $product ): string {
		if ( ! self::hooks_enabled( 'blocks' ) || ! $product instanceof WC_Product ) {
			return $html;
		}

		$button = self::get_product_button_html( $product->get_id(), 'hook', 'overlay-top-right' );
		if ( ! $button ) {
			return $html;
		}

		return $html . $button;
	}

	public static function shortcode_button( $atts ): string {
		$atts = shortcode_atts(
			array(
				'product_id' => 0,
				'position'   => 'inline',
			),
			$atts,
			'dc_wishlist_button'
		);

		$product_id = absint( $atts['product_id'] );
		if ( ! $product_id && is_singular( 'product' ) ) {
			$product_id = get_the_ID();
		}

		if ( ! $product_id ) {
			return '';
		}

		return self::get_product_button_html( $product_id, 'shortcode', sanitize_key( $atts['position'] ) );
	}

	public static function shortcode_wishlist_page(): string {
		if ( ! DC_Wishlist_Settings::is_enabled() ) {
			return '';
		}

		$settings = DC_Wishlist_Settings::get_all();

		if ( ! is_user_logged_in() && 'login_only' === $settings['general']['guest_mode'] ) {
			ob_start();
			?>
			<div class="dc-wishlist-page dc-wishlist-page--guest">
				<p><?php esc_html_e( 'Zaloguj się, aby zobaczyć swoją listę życzeń.', 'design-cart-wishlist' ); ?></p>
				<a class="button wc-forward" href="<?php echo esc_url( DC_Wishlist_Settings::login_url() ); ?>">
					<?php esc_html_e( 'Zaloguj się', 'design-cart-wishlist' ); ?>
				</a>
			</div>
			<?php
			return (string) ob_get_clean();
		}

		$products = DC_Wishlist_Storage::get_products();

		ob_start();
		include DC_WISHLIST_PATH . 'templates/wishlist-page.php';
		return (string) ob_get_clean();
	}

	public static function get_product_button_html( int $product_id, string $source = 'hook', string $position = 'inline' ): string {
		if ( ! DC_Wishlist_Settings::is_enabled() || ! $product_id ) {
			return '';
		}

		return DC_Wishlist_Button::render_product_button(
			$product_id,
			array(
				'source'   => $source,
				'position' => $position,
			)
		);
	}

	private static function hooks_enabled( string $key ): bool {
		if ( ! DC_Wishlist_Settings::is_enabled() ) {
			return false;
		}

		$hooks = DC_Wishlist_Settings::get( 'hooks' );
		return ! empty( $hooks[ $key ] );
	}

	private static function get_page_context(): string {
		if ( is_product() ) {
			return 'single';
		}

		if ( is_shop() || is_product_category() || is_product_tag() || is_product_taxonomy() ) {
			return 'loop';
		}

		return 'other';
	}

	private static function current_url(): string {
		global $wp;
		return home_url( add_query_arg( array(), $wp->request ?? '' ) );
	}
}
