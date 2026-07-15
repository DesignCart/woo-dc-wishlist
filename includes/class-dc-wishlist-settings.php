<?php
/**
 * Plugin settings.
 *
 * @package DesignCartWishlist
 * @author  Paweł Nosko
 * @company Design Cart
 */

defined( 'ABSPATH' ) || exit;

class DC_Wishlist_Settings {

	/**
	 * Default settings structure.
	 */
	public static function defaults(): array {
		return array(
			'general'    => array(
				'enabled'          => true,
				'guest_mode'       => 'allow',
				'merge_on_login'   => true,
				'wishlist_page_id' => 0,
			),
			'header'     => array(
				'enabled'            => true,
				'host_selector'      => '',
				'reference_selector' => '',
				'placement'          => 'append',
				'guest_behavior'     => 'login',
			),
			'hooks'      => array(
				'loop'   => true,
				'single' => true,
				'blocks' => true,
			),
			'rules'      => array(),
			'appearance' => array(
				'header'  => array(
					'bg'         => 'transparent',
					'color'      => '#262c38',
					'font_size'  => '20px',
					'hover_bg'   => 'transparent',
					'hover_color'=> '#1fa28c',
				),
				'product' => array(
					'bg'           => '#ffffff',
					'color'        => '#262c38',
					'font_size'    => '18px',
					'hover_bg'     => '#ffffff',
					'hover_color'  => '#e74c3c',
					'active_color' => '#e74c3c',
					'active_bg'    => '#ffffff',
				),
			),
			'advanced'   => array(
				'debug_mode' => false,
			),
		);
	}

	public static function ensure_defaults(): void {
		$current = get_option( DC_WISHLIST_OPTION, null );

		if ( ! is_array( $current ) ) {
			update_option( DC_WISHLIST_OPTION, self::defaults() );
			return;
		}

		update_option( DC_WISHLIST_OPTION, self::merge_recursive( self::defaults(), $current ) );
	}

	public static function get_all(): array {
		self::ensure_defaults();
		$stored = get_option( DC_WISHLIST_OPTION, array() );

		return self::merge_recursive( self::defaults(), is_array( $stored ) ? $stored : array() );
	}

	public static function get( string $section, ?string $key = null ) {
		$all = self::get_all();

		if ( ! isset( $all[ $section ] ) ) {
			return null;
		}

		if ( null === $key ) {
			return $all[ $section ];
		}

		return $all[ $section ][ $key ] ?? null;
	}

	public static function is_enabled(): bool {
		return (bool) self::get( 'general', 'enabled' );
	}

	public static function wishlist_page_url(): string {
		$page_id = (int) self::get( 'general', 'wishlist_page_id' );

		if ( $page_id > 0 ) {
			$url = get_permalink( $page_id );
			if ( $url ) {
				return $url;
			}
		}

		return home_url( '/lista-zyczen/' );
	}

	public static function login_url(): string {
		return wp_login_url( self::wishlist_page_url() );
	}

	/**
	 * Sanitize posted settings.
	 */
	public static function sanitize( array $input ): array {
		$defaults = self::defaults();
		$output   = self::merge_recursive( $defaults, array() );

		$output['general']['enabled']          = ! empty( $input['general']['enabled'] );
		$output['general']['guest_mode']       = in_array( $input['general']['guest_mode'] ?? '', array( 'allow', 'login_only' ), true )
			? $input['general']['guest_mode']
			: 'allow';
		$output['general']['merge_on_login']   = ! empty( $input['general']['merge_on_login'] );
		$output['general']['wishlist_page_id'] = absint( $input['general']['wishlist_page_id'] ?? 0 );

		$output['header']['enabled']            = ! empty( $input['header']['enabled'] );
		$output['header']['host_selector']      = sanitize_text_field( $input['header']['host_selector'] ?? '' );
		$output['header']['reference_selector'] = sanitize_text_field( $input['header']['reference_selector'] ?? '' );
		$output['header']['placement']          = self::sanitize_placement( $input['header']['placement'] ?? 'append' );
		$output['header']['guest_behavior']     = in_array( $input['header']['guest_behavior'] ?? '', array( 'login', 'page' ), true )
			? $input['header']['guest_behavior']
			: 'login';

		$output['hooks']['loop']   = ! empty( $input['hooks']['loop'] );
		$output['hooks']['single'] = ! empty( $input['hooks']['single'] );
		$output['hooks']['blocks'] = ! empty( $input['hooks']['blocks'] );

		$output['rules'] = self::sanitize_rules( $input['rules'] ?? array() );

		foreach ( array( 'header', 'product' ) as $ctx ) {
			foreach ( $defaults['appearance'][ $ctx ] as $key => $default_val ) {
				$raw = $input['appearance'][ $ctx ][ $key ] ?? $default_val;
				$output['appearance'][ $ctx ][ $key ] = self::sanitize_css_value( (string) $raw, $default_val );
			}
		}

		$output['advanced']['debug_mode'] = ! empty( $input['advanced']['debug_mode'] );

		return $output;
	}

	private static function sanitize_placement( string $placement ): string {
		return in_array( $placement, array( 'prepend', 'append', 'before', 'after' ), true ) ? $placement : 'append';
	}

	private static function sanitize_css_value( string $value, string $fallback ): string {
		$value = trim( wp_strip_all_tags( $value ) );

		if ( '' === $value ) {
			return $fallback;
		}

		if ( preg_match( '/^#[0-9a-fA-F]{3,8}$/', $value ) ) {
			return $value;
		}

		if ( preg_match( '/^(transparent|inherit|currentColor)$/i', $value ) ) {
			return $value;
		}

		if ( preg_match( '/^\d+(\.\d+)?(px|em|rem|%)$/', $value ) ) {
			return $value;
		}

		if ( preg_match( '/^\d+(\.\d+)?$/', $value ) ) {
			return $value . 'px';
		}

		return $fallback;
	}

	private static function sanitize_rules( array $rules ): array {
		$clean = array();

		foreach ( $rules as $index => $rule ) {
			if ( ! is_array( $rule ) ) {
				continue;
			}

			$id = sanitize_key( $rule['id'] ?? 'rule_' . $index );
			if ( '' === $id ) {
				$id = 'rule_' . $index;
			}

			$context = $rule['context'] ?? 'loop';
			if ( ! in_array( $context, array( 'loop', 'single', 'everywhere', 'custom' ), true ) ) {
				$context = 'loop';
			}

			$position = $rule['position'] ?? 'inline';
			$allowed_positions = array(
				'inline',
				'overlay-top-right',
				'overlay-top-left',
				'overlay-bottom-right',
				'overlay-bottom-left',
				'custom',
			);
			if ( ! in_array( $position, $allowed_positions, true ) ) {
				$position = 'inline';
			}

			$product_id_mode = $rule['product_id'] ?? 'auto';
			if ( ! in_array( $product_id_mode, array( 'auto', 'selector', 'attribute' ), true ) ) {
				$product_id_mode = 'auto';
			}

			$match = $rule['match'] ?? 'all';
			if ( ! in_array( $match, array( 'all', 'first' ), true ) ) {
				$match = 'all';
			}

			$clean[] = array(
				'id'                    => $id,
				'enabled'               => ! empty( $rule['enabled'] ),
				'label'                 => sanitize_text_field( $rule['label'] ?? '' ),
				'context'               => $context,
				'url_pattern'           => sanitize_text_field( $rule['url_pattern'] ?? '' ),
				'selector'              => sanitize_text_field( $rule['selector'] ?? '' ),
				'match'                 => $match,
				'placement'             => self::sanitize_placement( $rule['placement'] ?? 'append' ),
				'position'              => $position,
				'position_custom'       => array(
					'mode'   => in_array( $rule['position_custom']['mode'] ?? 'absolute', array( 'absolute', 'relative' ), true )
						? $rule['position_custom']['mode']
						: 'absolute',
					'top'    => sanitize_text_field( $rule['position_custom']['top'] ?? '' ),
					'right'  => sanitize_text_field( $rule['position_custom']['right'] ?? '' ),
					'bottom' => sanitize_text_field( $rule['position_custom']['bottom'] ?? '' ),
					'left'   => sanitize_text_field( $rule['position_custom']['left'] ?? '' ),
				),
				'product_id'            => $product_id_mode,
				'product_id_selector'   => sanitize_text_field( $rule['product_id_selector'] ?? '' ),
				'product_id_attr'       => sanitize_text_field( $rule['product_id_attr'] ?? 'data-product_id' ),
			);
		}

		return $clean;
	}

	private static function merge_recursive( array $defaults, array $custom ): array {
		foreach ( $custom as $key => $value ) {
			if ( is_array( $value ) && isset( $defaults[ $key ] ) && is_array( $defaults[ $key ] ) ) {
				$defaults[ $key ] = self::merge_recursive( $defaults[ $key ], $value );
			} else {
				$defaults[ $key ] = $value;
			}
		}

		return $defaults;
	}
}
