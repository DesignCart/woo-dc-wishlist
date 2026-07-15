<?php
/**
 * Admin settings screen.
 *
 * @package DesignCartWishlist
 * @author  Paweł Nosko
 * @company Design Cart
 */

defined( 'ABSPATH' ) || exit;

class DC_Wishlist_Admin {

	public static function init(): void {
		add_action( 'admin_menu', array( __CLASS__, 'register_menu' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );
		add_action( 'admin_post_dc_wishlist_save_settings', array( __CLASS__, 'save_settings' ) );
	}

	public static function register_menu(): void {
		add_menu_page(
			__( 'Design Cart Wishlist', 'design-cart-wishlist' ),
			__( 'Lista życzeń', 'design-cart-wishlist' ),
			'manage_woocommerce',
			'dc-wishlist',
			array( __CLASS__, 'render_page' ),
			'dashicons-heart',
			56
		);

		add_submenu_page(
			'dc-wishlist',
			__( 'Ustawienia listy życzeń', 'design-cart-wishlist' ),
			__( 'Ustawienia', 'design-cart-wishlist' ),
			'manage_woocommerce',
			'dc-wishlist',
			array( __CLASS__, 'render_page' )
		);
	}

	public static function enqueue_assets( string $hook ): void {
		if ( 'toplevel_page_dc-wishlist' !== $hook ) {
			return;
		}

		wp_enqueue_style(
			'dc-wishlist-font-awesome',
			DC_WISHLIST_URL . 'admin/assets/font-awesome/css/font-awesome.min.css',
			array(),
			'4.7.0'
		);

		wp_enqueue_style(
			'dc-wishlist-admin-interface',
			DC_WISHLIST_URL . 'admin/css/dc-interface.css',
			array( 'dc-wishlist-font-awesome' ),
			DC_WISHLIST_VERSION
		);

		wp_enqueue_style(
			'dc-wishlist-admin',
			DC_WISHLIST_URL . 'admin/css/admin-page.css',
			array( 'dc-wishlist-admin-interface' ),
			DC_WISHLIST_VERSION
		);

		wp_enqueue_script(
			'dc-colorpicker',
			DC_WISHLIST_URL . 'admin/js/dc-colorpicker.js',
			array(),
			DC_WISHLIST_VERSION,
			true
		);

		wp_enqueue_script(
			'dc-dimension',
			DC_WISHLIST_URL . 'admin/js/dc-dimension.js',
			array(),
			DC_WISHLIST_VERSION,
			true
		);

		wp_enqueue_script(
			'dc-interface',
			DC_WISHLIST_URL . 'admin/js/dc-interface.js',
			array( 'dc-colorpicker', 'dc-dimension' ),
			DC_WISHLIST_VERSION,
			true
		);

		wp_enqueue_script(
			'dc-wishlist-admin-rules',
			DC_WISHLIST_URL . 'admin/js/admin-rules.js',
			array( 'dc-interface' ),
			DC_WISHLIST_VERSION,
			true
		);

		wp_localize_script(
			'dc-wishlist-admin-rules',
			'dcWishlistAdmin',
			array(
				'ruleTemplate' => self::get_rule_row_template(),
				'i18n'         => array(
					'removeRule'  => __( 'Usuń regułę', 'design-cart-wishlist' ),
					'ruleLabel'   => __( 'Reguła', 'design-cart-wishlist' ),
					'confirmRemove'=> __( 'Usunąć tę regułę?', 'design-cart-wishlist' ),
				),
			)
		);
	}

	public static function save_settings(): void {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_die( esc_html__( 'Brak uprawnień.', 'design-cart-wishlist' ) );
		}

		check_admin_referer( 'dc_wishlist_save_settings' );

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Sanitized in DC_Wishlist_Settings::sanitize().
		$post_settings = isset( $_POST['dc_wishlist'] ) && is_array( $_POST['dc_wishlist'] ) ? wp_unslash( $_POST['dc_wishlist'] ) : array();
		$sanitized     = DC_Wishlist_Settings::sanitize( $post_settings );

		update_option( DC_WISHLIST_OPTION, $sanitized );

		wp_safe_redirect(
			add_query_arg(
				array(
					'page'    => 'dc-wishlist',
					'updated' => '1',
				),
				admin_url( 'admin.php' )
			)
		);
		exit;
	}

	public static function render_page(): void {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}

		$settings = DC_Wishlist_Settings::get_all();
		$pages    = get_pages( array( 'sort_column' => 'post_title' ) );

		include DC_WISHLIST_PATH . 'admin/views/settings-page.php';
	}

	private static function get_rule_row_template(): string {
		ob_start();
		self::render_rule_row(
			array(
				'id'                  => '__INDEX__',
				'enabled'             => true,
				'label'               => '',
				'context'             => 'loop',
				'url_pattern'         => '',
				'selector'            => '',
				'match'               => 'all',
				'placement'           => 'append',
				'position'            => 'overlay-top-right',
				'position_custom'     => array(
					'mode'   => 'absolute',
					'top'    => '8px',
					'right'  => '8px',
					'bottom' => '',
					'left'   => '',
				),
				'product_id'          => 'auto',
				'product_id_selector' => '',
				'product_id_attr'     => 'data-product_id',
			),
			'__INDEX__',
			true
		);
		return (string) ob_get_clean();
	}

	public static function render_rule_row( array $rule, $index, bool $is_template = false ): void {
		$id = $rule['id'] ?? 'rule_' . $index;
		$prefix = 'dc_wishlist[rules][' . $index . ']';
		$custom = $rule['position_custom'] ?? array();
		$row_class = $is_template ? 'dc-wishlist-rule-row dc-wishlist-rule-row--template' : 'dc-wishlist-rule-row';
		?>
		<div class="<?php echo esc_attr( $row_class ); ?>" data-rule-index="<?php echo esc_attr( (string) $index ); ?>">
			<div class="dc-wishlist-rule-row__head">
				<strong><?php esc_html_e( 'Reguła placement', 'design-cart-wishlist' ); ?> #<span class="dc-wishlist-rule-num"><?php echo esc_html( is_numeric( $index ) ? (string) ( (int) $index + 1 ) : '1' ); ?></span></strong>
				<button type="button" class="dc-btn dc-btn--ghost dc-btn--sm dc-wishlist-rule-remove" title="<?php esc_attr_e( 'Usuń regułę', 'design-cart-wishlist' ); ?>">
					<i class="fa fa-trash"></i>
				</button>
			</div>

			<input type="hidden" name="<?php echo esc_attr( $prefix ); ?>[id]" value="<?php echo esc_attr( $id ); ?>">

			<div class="dc-row dc-row--2">
				<div class="dc-field">
					<label class="dc-label"><?php esc_html_e( 'Nazwa', 'design-cart-wishlist' ); ?></label>
					<input class="dc-input" type="text" name="<?php echo esc_attr( $prefix ); ?>[label]" value="<?php echo esc_attr( $rule['label'] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'np. Katalog — overlay', 'design-cart-wishlist' ); ?>">
				</div>
				<div class="dc-field dc-field--switch">
					<label class="dc-toggle">
						<input class="dc-toggle__input" type="checkbox" name="<?php echo esc_attr( $prefix ); ?>[enabled]" value="1" <?php checked( ! empty( $rule['enabled'] ) ); ?>>
						<span class="dc-toggle__track"></span>
						<span><?php esc_html_e( 'Włączona', 'design-cart-wishlist' ); ?></span>
					</label>
				</div>
			</div>

			<div class="dc-row dc-row--3">
				<div class="dc-field">
					<label class="dc-label"><?php esc_html_e( 'Kontekst', 'design-cart-wishlist' ); ?></label>
					<select class="dc-input dc-wishlist-rule-context" name="<?php echo esc_attr( $prefix ); ?>[context]">
						<?php foreach ( array( 'loop' => __( 'Lista produktów', 'design-cart-wishlist' ), 'single' => __( 'Karta produktu', 'design-cart-wishlist' ), 'everywhere' => __( 'Wszędzie', 'design-cart-wishlist' ), 'custom' => __( 'Własny URL', 'design-cart-wishlist' ) ) as $val => $label ) : ?>
							<option value="<?php echo esc_attr( $val ); ?>" <?php selected( $rule['context'] ?? 'loop', $val ); ?>><?php echo esc_html( $label ); ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="dc-field dc-wishlist-rule-url-wrap" <?php echo ( 'custom' === ( $rule['context'] ?? '' ) ) ? '' : 'hidden'; ?>>
					<label class="dc-label"><?php esc_html_e( 'Wzorzec URL', 'design-cart-wishlist' ); ?></label>
					<input class="dc-input" type="text" name="<?php echo esc_attr( $prefix ); ?>[url_pattern]" value="<?php echo esc_attr( $rule['url_pattern'] ?? '' ); ?>" placeholder="/sklep/">
				</div>
				<div class="dc-field">
					<label class="dc-label"><?php esc_html_e( 'Tryb dopasowania', 'design-cart-wishlist' ); ?></label>
					<select class="dc-input" name="<?php echo esc_attr( $prefix ); ?>[match]">
						<option value="all" <?php selected( $rule['match'] ?? 'all', 'all' ); ?>><?php esc_html_e( 'Każdy match', 'design-cart-wishlist' ); ?></option>
						<option value="first" <?php selected( $rule['match'] ?? 'all', 'first' ); ?>><?php esc_html_e( 'Pierwszy match', 'design-cart-wishlist' ); ?></option>
					</select>
				</div>
			</div>

			<div class="dc-row dc-row--2">
				<div class="dc-field">
					<label class="dc-label"><?php esc_html_e( 'Selektor kontenera', 'design-cart-wishlist' ); ?></label>
					<input class="dc-input" type="text" name="<?php echo esc_attr( $prefix ); ?>[selector]" value="<?php echo esc_attr( $rule['selector'] ?? '' ); ?>" placeholder=".product .product-image" required>
				</div>
				<div class="dc-field">
					<label class="dc-label"><?php esc_html_e( 'Placement', 'design-cart-wishlist' ); ?></label>
					<select class="dc-input" name="<?php echo esc_attr( $prefix ); ?>[placement]">
						<?php foreach ( array( 'prepend', 'append', 'before', 'after' ) as $placement ) : ?>
							<option value="<?php echo esc_attr( $placement ); ?>" <?php selected( $rule['placement'] ?? 'append', $placement ); ?>><?php echo esc_html( $placement ); ?></option>
						<?php endforeach; ?>
					</select>
				</div>
			</div>

			<div class="dc-row dc-row--2">
				<div class="dc-field">
					<label class="dc-label"><?php esc_html_e( 'Preset pozycji', 'design-cart-wishlist' ); ?></label>
					<select class="dc-input dc-wishlist-rule-position" name="<?php echo esc_attr( $prefix ); ?>[position]">
						<?php
						$positions = array(
							'inline'               => __( 'Inline', 'design-cart-wishlist' ),
							'overlay-top-right'    => __( 'Overlay — prawy górny', 'design-cart-wishlist' ),
							'overlay-top-left'     => __( 'Overlay — lewy górny', 'design-cart-wishlist' ),
							'overlay-bottom-right' => __( 'Overlay — prawy dolny', 'design-cart-wishlist' ),
							'overlay-bottom-left'  => __( 'Overlay — lewy dolny', 'design-cart-wishlist' ),
							'custom'               => __( 'Custom (advanced)', 'design-cart-wishlist' ),
						);
						foreach ( $positions as $val => $label ) :
							?>
							<option value="<?php echo esc_attr( $val ); ?>" <?php selected( $rule['position'] ?? 'inline', $val ); ?>><?php echo esc_html( $label ); ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="dc-field">
					<label class="dc-label"><?php esc_html_e( 'Product ID', 'design-cart-wishlist' ); ?></label>
					<select class="dc-input dc-wishlist-rule-product-id" name="<?php echo esc_attr( $prefix ); ?>[product_id]">
						<option value="auto" <?php selected( $rule['product_id'] ?? 'auto', 'auto' ); ?>><?php esc_html_e( 'Auto (domyślnie)', 'design-cart-wishlist' ); ?></option>
						<option value="selector" <?php selected( $rule['product_id'] ?? 'auto', 'selector' ); ?>><?php esc_html_e( 'Selektor', 'design-cart-wishlist' ); ?></option>
						<option value="attribute" <?php selected( $rule['product_id'] ?? 'auto', 'attribute' ); ?>><?php esc_html_e( 'Atrybut', 'design-cart-wishlist' ); ?></option>
					</select>
				</div>
			</div>

			<div class="dc-wishlist-rule-custom-position" <?php echo ( 'custom' === ( $rule['position'] ?? '' ) ) ? '' : 'hidden'; ?>>
				<div class="dc-row dc-row--5">
					<div class="dc-field">
						<label class="dc-label"><?php esc_html_e( 'Position', 'design-cart-wishlist' ); ?></label>
						<select class="dc-input" name="<?php echo esc_attr( $prefix ); ?>[position_custom][mode]">
							<option value="absolute" <?php selected( $custom['mode'] ?? 'absolute', 'absolute' ); ?>>absolute</option>
							<option value="relative" <?php selected( $custom['mode'] ?? 'absolute', 'relative' ); ?>>relative</option>
						</select>
					</div>
					<?php foreach ( array( 'top', 'right', 'bottom', 'left' ) as $side ) : ?>
						<div class="dc-field">
							<label class="dc-label"><?php echo esc_html( ucfirst( $side ) ); ?></label>
							<input class="dc-input" type="text" name="<?php echo esc_attr( $prefix ); ?>[position_custom][<?php echo esc_attr( $side ); ?>]" value="<?php echo esc_attr( $custom[ $side ] ?? '' ); ?>" placeholder="8px">
						</div>
					<?php endforeach; ?>
				</div>
			</div>

			<div class="dc-wishlist-rule-product-id-advanced" <?php echo ( 'auto' === ( $rule['product_id'] ?? 'auto' ) ) ? 'hidden' : ''; ?>>
				<div class="dc-row dc-row--2">
					<div class="dc-field">
						<label class="dc-label"><?php esc_html_e( 'Selektor ID produktu', 'design-cart-wishlist' ); ?></label>
						<input class="dc-input" type="text" name="<?php echo esc_attr( $prefix ); ?>[product_id_selector]" value="<?php echo esc_attr( $rule['product_id_selector'] ?? '' ); ?>" placeholder=".add_to_cart_button">
					</div>
					<div class="dc-field">
						<label class="dc-label"><?php esc_html_e( 'Atrybut ID', 'design-cart-wishlist' ); ?></label>
						<input class="dc-input" type="text" name="<?php echo esc_attr( $prefix ); ?>[product_id_attr]" value="<?php echo esc_attr( $rule['product_id_attr'] ?? 'data-product_id' ); ?>">
					</div>
				</div>
			</div>
		</div>
		<?php
	}
}
