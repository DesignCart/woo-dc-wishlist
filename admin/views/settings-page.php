<?php
/**
 * Admin settings view.
 *
 * @var array $settings
 * @var WP_Post[] $pages
 *
 * @package DesignCartWishlist
 * @author  Paweł Nosko
 * @company Design Cart
 */

defined( 'ABSPATH' ) || exit;

$dc_wishlist_appearance = $settings['appearance'];
?>
<div class="wrap dc-wishlist-admin-wrap">
	<div class="dc-page">
		<div class="dc-hero">
			<div class="dc-hero__mesh"></div>
			<div class="dc-hero__orb dc-hero__orb--1"></div>
			<div class="dc-hero__orb dc-hero__orb--2"></div>
			<div class="dc-hero__inner">
				<div class="dc-hero__row">
					<div class="dc-hero__brand">
						<span class="dc-hero__icon"><i class="fa fa-heart"></i></span>
						<div>
							<p class="dc-hero__eyebrow">Design Cart</p>
							<h1 class="dc-hero__title"><?php esc_html_e( 'Design Cart Wishlist', 'design-cart-wishlist' ); ?></h1>
						</div>
					</div>
					<div class="dc-hero__actions">
						<button type="submit" form="dcWishlistSettingsForm" class="dc-btn dc-btn--light">
							<i class="fa fa-save"></i> <?php esc_html_e( 'Zapisz', 'design-cart-wishlist' ); ?>
						</button>
					</div>
				</div>
				<ul class="dc-hero__bc">
					<li><a href="<?php echo esc_url( admin_url( 'admin.php?page=dc-wishlist' ) ); ?>"><?php esc_html_e( 'Lista życzeń', 'design-cart-wishlist' ); ?></a></li>
					<li><?php esc_html_e( 'Ustawienia', 'design-cart-wishlist' ); ?></li>
				</ul>
			</div>
		</div>

		<?php if ( ! empty( $_GET['updated'] ) ) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>
			<div class="notice notice-success is-dismissible dc-wishlist-notice">
				<p><?php esc_html_e( 'Ustawienia zostały zapisane.', 'design-cart-wishlist' ); ?></p>
			</div>
		<?php endif; ?>

		<div class="dc-page__wrap">
			<div class="dc-form-card">
				<div class="dc-interface" id="dcWishlistInterface">
					<form id="dcWishlistSettingsForm" class="dc-form dc-form--full" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
						<input type="hidden" name="action" value="dc_wishlist_save_settings">
						<?php wp_nonce_field( 'dc_wishlist_save_settings' ); ?>

						<nav class="dc-nav dc-tabs" role="tablist">
							<button type="button" class="dc-nav__btn dc-tabs__btn dc-active" data-dc-tab="tab-general" role="tab"><i class="fa fa-cog"></i> <?php esc_html_e( 'Ogólne', 'design-cart-wishlist' ); ?></button>
							<button type="button" class="dc-nav__btn dc-tabs__btn" data-dc-tab="tab-header" role="tab"><i class="fa fa-header"></i> <?php esc_html_e( 'Header', 'design-cart-wishlist' ); ?></button>
							<button type="button" class="dc-nav__btn dc-tabs__btn" data-dc-tab="tab-products" role="tab"><i class="fa fa-th-large"></i> <?php esc_html_e( 'Produkty', 'design-cart-wishlist' ); ?></button>
							<button type="button" class="dc-nav__btn dc-tabs__btn" data-dc-tab="tab-appearance" role="tab"><i class="fa fa-paint-brush"></i> <?php esc_html_e( 'Wygląd', 'design-cart-wishlist' ); ?></button>
							<button type="button" class="dc-nav__btn dc-tabs__btn" data-dc-tab="tab-advanced" role="tab"><i class="fa fa-code"></i> <?php esc_html_e( 'Zaawansowane', 'design-cart-wishlist' ); ?></button>
							<button type="button" class="dc-nav__btn dc-tabs__btn" data-dc-tab="tab-documentation" role="tab"><i class="fa fa-book"></i> <?php esc_html_e( 'Dokumentacja', 'design-cart-wishlist' ); ?></button>
						</nav>

						<div class="dc-form-card__body">

							<div id="tab-general" class="dc-tab-panel dc-active" role="tabpanel">
								<div class="dc-section-card dc-section">
									<div class="dc-section-card__head">
										<span class="dc-section-card__icon"><i class="fa fa-cog"></i></span>
										<div>
											<h3 class="dc-section-card__title"><?php esc_html_e( 'Ustawienia ogólne', 'design-cart-wishlist' ); ?></h3>
											<p class="dc-section-card__sub"><?php esc_html_e( 'Podstawowa konfiguracja listy życzeń', 'design-cart-wishlist' ); ?></p>
										</div>
									</div>

									<label class="dc-toggle">
										<input class="dc-toggle__input" type="checkbox" name="dc_wishlist[general][enabled]" value="1" <?php checked( ! empty( $settings['general']['enabled'] ) ); ?>>
										<span class="dc-toggle__track"></span>
										<span><?php esc_html_e( 'Włącz plugin', 'design-cart-wishlist' ); ?></span>
									</label>

									<div class="dc-row dc-row--2" style="margin-top:1rem;">
										<div class="dc-field">
											<label class="dc-label"><?php esc_html_e( 'Tryb gości', 'design-cart-wishlist' ); ?></label>
											<select class="dc-input" name="dc_wishlist[general][guest_mode]">
												<option value="allow" <?php selected( $settings['general']['guest_mode'], 'allow' ); ?>><?php esc_html_e( 'Goście mogą dodawać (cookie)', 'design-cart-wishlist' ); ?></option>
												<option value="login_only" <?php selected( $settings['general']['guest_mode'], 'login_only' ); ?>><?php esc_html_e( 'Tylko zalogowani', 'design-cart-wishlist' ); ?></option>
											</select>
										</div>
										<div class="dc-field">
											<label class="dc-label"><?php esc_html_e( 'Strona listy życzeń', 'design-cart-wishlist' ); ?></label>
											<select class="dc-input" name="dc_wishlist[general][wishlist_page_id]">
												<option value="0"><?php esc_html_e( '— wybierz —', 'design-cart-wishlist' ); ?></option>
												<?php foreach ( $pages as $page ) : ?>
													<option value="<?php echo esc_attr( (string) $page->ID ); ?>" <?php selected( (int) $settings['general']['wishlist_page_id'], (int) $page->ID ); ?>>
														<?php echo esc_html( $page->post_title ); ?>
													</option>
												<?php endforeach; ?>
											</select>
										</div>
									</div>

									<label class="dc-toggle" style="margin-top:1rem;">
										<input class="dc-toggle__input" type="checkbox" name="dc_wishlist[general][merge_on_login]" value="1" <?php checked( ! empty( $settings['general']['merge_on_login'] ) ); ?>>
										<span class="dc-toggle__track"></span>
										<span><?php esc_html_e( 'Połącz listę gościa z kontem po logowaniu', 'design-cart-wishlist' ); ?></span>
									</label>
								</div>
							</div>

							<div id="tab-header" class="dc-tab-panel" role="tabpanel">
								<div class="dc-section-card dc-section">
									<div class="dc-section-card__head">
										<span class="dc-section-card__icon"><i class="fa fa-header"></i></span>
										<div>
											<h3 class="dc-section-card__title"><?php esc_html_e( 'Serduszko w headerze', 'design-cart-wishlist' ); ?></h3>
											<p class="dc-section-card__sub"><?php esc_html_e( 'Link do listy życzeń lub logowania obok koszyka', 'design-cart-wishlist' ); ?></p>
										</div>
									</div>

									<label class="dc-toggle">
										<input class="dc-toggle__input" type="checkbox" name="dc_wishlist[header][enabled]" value="1" <?php checked( ! empty( $settings['header']['enabled'] ) ); ?>>
										<span class="dc-toggle__track"></span>
										<span><?php esc_html_e( 'Włącz w headerze', 'design-cart-wishlist' ); ?></span>
									</label>

									<div class="dc-row dc-row--2" style="margin-top:1rem;">
										<div class="dc-field">
											<label class="dc-label"><?php esc_html_e( 'Selektor kontenera', 'design-cart-wishlist' ); ?></label>
											<input class="dc-input" type="text" name="dc_wishlist[header][host_selector]" value="<?php echo esc_attr( $settings['header']['host_selector'] ); ?>" placeholder=".site-header__actions, .header-interface">
											<p class="dc-hint"><?php esc_html_e( 'Element, w którym zostanie wstawione serduszko.', 'design-cart-wishlist' ); ?></p>
										</div>
										<div class="dc-field">
											<label class="dc-label"><?php esc_html_e( 'Selektor odniesienia (opcjonalnie)', 'design-cart-wishlist' ); ?></label>
											<input class="dc-input" type="text" name="dc_wishlist[header][reference_selector]" value="<?php echo esc_attr( $settings['header']['reference_selector'] ); ?>" placeholder=".header-cart, .cart-contents">
											<p class="dc-hint"><?php esc_html_e( 'Wstaw względem koszyka zamiast końca kontenera.', 'design-cart-wishlist' ); ?></p>
										</div>
									</div>

									<div class="dc-row dc-row--2">
										<div class="dc-field">
											<label class="dc-label"><?php esc_html_e( 'Placement', 'design-cart-wishlist' ); ?></label>
											<select class="dc-input" name="dc_wishlist[header][placement]">
												<?php foreach ( array( 'prepend', 'append', 'before', 'after' ) as $dc_wishlist_placement ) : ?>
													<option value="<?php echo esc_attr( $dc_wishlist_placement ); ?>" <?php selected( $settings['header']['placement'], $dc_wishlist_placement ); ?>><?php echo esc_html( $dc_wishlist_placement ); ?></option>
												<?php endforeach; ?>
											</select>
										</div>
										<div class="dc-field">
											<label class="dc-label"><?php esc_html_e( 'Zachowanie gościa', 'design-cart-wishlist' ); ?></label>
											<select class="dc-input" name="dc_wishlist[header][guest_behavior]">
												<option value="login" <?php selected( $settings['header']['guest_behavior'], 'login' ); ?>><?php esc_html_e( 'Przekieruj do logowania', 'design-cart-wishlist' ); ?></option>
												<option value="page" <?php selected( $settings['header']['guest_behavior'], 'page' ); ?>><?php esc_html_e( 'Strona listy życzeń', 'design-cart-wishlist' ); ?></option>
											</select>
										</div>
									</div>
								</div>
							</div>

							<div id="tab-products" class="dc-tab-panel" role="tabpanel">
								<div class="dc-section-card dc-section">
									<div class="dc-section-card__head">
										<span class="dc-section-card__icon"><i class="fa fa-plug"></i></span>
										<div>
											<h3 class="dc-section-card__title"><?php esc_html_e( 'Domyślne hooki WooCommerce', 'design-cart-wishlist' ); ?></h3>
											<p class="dc-section-card__sub"><?php esc_html_e( 'Działają od razu bez konfiguracji selektorów', 'design-cart-wishlist' ); ?></p>
										</div>
									</div>
									<div class="dc-switch-group">
										<label class="dc-switch-btn">
											<input class="dc-switch-btn__input" type="checkbox" name="dc_wishlist[hooks][loop]" value="1" <?php checked( ! empty( $settings['hooks']['loop'] ) ); ?>>
											<span class="dc-switch-btn__label"><?php esc_html_e( 'Lista produktów', 'design-cart-wishlist' ); ?></span>
										</label>
										<label class="dc-switch-btn">
											<input class="dc-switch-btn__input" type="checkbox" name="dc_wishlist[hooks][single]" value="1" <?php checked( ! empty( $settings['hooks']['single'] ) ); ?>>
											<span class="dc-switch-btn__label"><?php esc_html_e( 'Karta produktu', 'design-cart-wishlist' ); ?></span>
										</label>
										<label class="dc-switch-btn">
											<input class="dc-switch-btn__input" type="checkbox" name="dc_wishlist[hooks][blocks]" value="1" <?php checked( ! empty( $settings['hooks']['blocks'] ) ); ?>>
											<span class="dc-switch-btn__label"><?php esc_html_e( 'Bloki WooCommerce', 'design-cart-wishlist' ); ?></span>
										</label>
									</div>
								</div>

								<div class="dc-section-card dc-section">
									<div class="dc-section-card__head">
										<span class="dc-section-card__icon"><i class="fa fa-crosshairs"></i></span>
										<div>
											<h3 class="dc-section-card__title"><?php esc_html_e( 'Reguły placement', 'design-cart-wishlist' ); ?></h3>
											<p class="dc-section-card__sub"><?php esc_html_e( 'Dla modułów, builderów i niestandardowego HTML', 'design-cart-wishlist' ); ?></p>
										</div>
										<button type="button" class="dc-btn dc-btn--primary dc-btn--sm" id="dcWishlistAddRule">
											<i class="fa fa-plus"></i> <?php esc_html_e( 'Dodaj regułę', 'design-cart-wishlist' ); ?>
										</button>
									</div>

									<div id="dcWishlistRulesList" class="dc-wishlist-rules-list">
										<?php
										$dc_wishlist_rules = $settings['rules'];
										if ( empty( $dc_wishlist_rules ) ) {
											$dc_wishlist_rules = array();
										}
										foreach ( $dc_wishlist_rules as $dc_wishlist_index => $dc_wishlist_rule ) {
											DC_Wishlist_Admin::render_rule_row( $dc_wishlist_rule, $dc_wishlist_index );
										}
										?>
									</div>

									<p class="dc-hint"><?php esc_html_e( 'Shortcode: [dc_wishlist_button product_id="123"] lub [dc_wishlist] na stronie listy.', 'design-cart-wishlist' ); ?></p>
								</div>
							</div>

							<div id="tab-appearance" class="dc-tab-panel" role="tabpanel">
								<?php
								foreach (
									array(
										'header'  => __( 'Header', 'design-cart-wishlist' ),
										'product' => __( 'Listy i karta produktu', 'design-cart-wishlist' ),
									) as $dc_wishlist_ctx_key => $dc_wishlist_ctx_label
								) :
									$dc_wishlist_ctx = $dc_wishlist_appearance[ $dc_wishlist_ctx_key ];
									?>
									<div class="dc-section-card dc-section">
										<div class="dc-section-card__head">
											<span class="dc-section-card__icon"><i class="fa fa-heart"></i></span>
											<div>
												<h3 class="dc-section-card__title"><?php echo esc_html( $dc_wishlist_ctx_label ); ?></h3>
											</div>
										</div>
										<div class="dc-row dc-row--2">
											<div class="dc-field">
												<label class="dc-label"><?php esc_html_e( 'Tło', 'design-cart-wishlist' ); ?></label>
												<div class="dc-colorpicker" data-dc-colorpicker data-name="dc_wishlist[appearance][<?php echo esc_attr( $dc_wishlist_ctx_key ); ?>][bg]" data-value="<?php echo esc_attr( $dc_wishlist_ctx['bg'] ); ?>" data-label="<?php esc_attr_e( 'Tło', 'design-cart-wishlist' ); ?>"></div>
											</div>
											<div class="dc-field">
												<label class="dc-label"><?php esc_html_e( 'Kolor ikony', 'design-cart-wishlist' ); ?></label>
												<div class="dc-colorpicker" data-dc-colorpicker data-name="dc_wishlist[appearance][<?php echo esc_attr( $dc_wishlist_ctx_key ); ?>][color]" data-value="<?php echo esc_attr( $dc_wishlist_ctx['color'] ); ?>" data-label="<?php esc_attr_e( 'Kolor', 'design-cart-wishlist' ); ?>"></div>
											</div>
										</div>
										<div class="dc-row dc-row--2" style="margin-top:1rem;">
											<div class="dc-field">
												<label class="dc-label"><?php esc_html_e( 'Rozmiar', 'design-cart-wishlist' ); ?></label>
												<div class="dc-dimension" data-dc-dimension data-name="dc_wishlist[appearance][<?php echo esc_attr( $dc_wishlist_ctx_key ); ?>][font_size]" data-value="<?php echo esc_attr( preg_replace( '/[^0-9.]/', '', $dc_wishlist_ctx['font_size'] ) ); ?>" data-fixed-unit="px" data-min="10" data-max-px="48" data-label="<?php esc_attr_e( 'Rozmiar', 'design-cart-wishlist' ); ?>"></div>
											</div>
											<div class="dc-field">
												<label class="dc-label"><?php esc_html_e( 'Hover — tło', 'design-cart-wishlist' ); ?></label>
												<div class="dc-colorpicker" data-dc-colorpicker data-name="dc_wishlist[appearance][<?php echo esc_attr( $dc_wishlist_ctx_key ); ?>][hover_bg]" data-value="<?php echo esc_attr( $dc_wishlist_ctx['hover_bg'] ); ?>"></div>
											</div>
										</div>
										<div class="dc-row dc-row--2" style="margin-top:1rem;">
											<div class="dc-field">
												<label class="dc-label"><?php esc_html_e( 'Hover — kolor', 'design-cart-wishlist' ); ?></label>
												<div class="dc-colorpicker" data-dc-colorpicker data-name="dc_wishlist[appearance][<?php echo esc_attr( $dc_wishlist_ctx_key ); ?>][hover_color]" data-value="<?php echo esc_attr( $dc_wishlist_ctx['hover_color'] ); ?>"></div>
											</div>
											<?php if ( 'product' === $dc_wishlist_ctx_key ) : ?>
												<div class="dc-field">
													<label class="dc-label"><?php esc_html_e( 'Aktywne — kolor', 'design-cart-wishlist' ); ?></label>
													<div class="dc-colorpicker" data-dc-colorpicker data-name="dc_wishlist[appearance][product][active_color]" data-value="<?php echo esc_attr( $dc_wishlist_ctx['active_color'] ); ?>"></div>
												</div>
											<?php endif; ?>
										</div>
										<?php if ( 'product' === $dc_wishlist_ctx_key ) : ?>
											<div class="dc-field" style="margin-top:1rem;">
												<label class="dc-label"><?php esc_html_e( 'Aktywne — tło', 'design-cart-wishlist' ); ?></label>
												<div class="dc-colorpicker" data-dc-colorpicker data-name="dc_wishlist[appearance][product][active_bg]" data-value="<?php echo esc_attr( $dc_wishlist_ctx['active_bg'] ); ?>"></div>
											</div>
										<?php endif; ?>
									</div>
								<?php endforeach; ?>
							</div>

							<div id="tab-advanced" class="dc-tab-panel" role="tabpanel">
								<div class="dc-section-card dc-section">
									<div class="dc-section-card__head">
										<span class="dc-section-card__icon"><i class="fa fa-bug"></i></span>
										<div>
											<h3 class="dc-section-card__title"><?php esc_html_e( 'Debug', 'design-cart-wishlist' ); ?></h3>
											<p class="dc-section-card__sub"><?php esc_html_e( 'Podświetla anchory reguł na froncie (tylko admin)', 'design-cart-wishlist' ); ?></p>
										</div>
									</div>
									<label class="dc-toggle">
										<input class="dc-toggle__input" type="checkbox" name="dc_wishlist[advanced][debug_mode]" value="1" <?php checked( ! empty( $settings['advanced']['debug_mode'] ) ); ?>>
										<span class="dc-toggle__track"></span>
										<span><?php esc_html_e( 'Tryb debug placement', 'design-cart-wishlist' ); ?></span>
									</label>
									<p class="dc-hint" style="margin-top:1rem;"><?php esc_html_e( 'Szczegóły w zakładce Dokumentacja → Debug placement.', 'design-cart-wishlist' ); ?></p>
								</div>
							</div>

							<?php include DC_WISHLIST_PATH . 'admin/views/tab-documentation.php'; ?>

							<div class="dc-actions">
								<button type="submit" class="dc-btn dc-btn--primary"><i class="fa fa-save"></i> <?php esc_html_e( 'Zapisz ustawienia', 'design-cart-wishlist' ); ?></button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
