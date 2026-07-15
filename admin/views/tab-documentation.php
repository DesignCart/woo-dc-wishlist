<?php
/**
 * Documentation tab content.
 *
 * @package DesignCartWishlist
 * @author  Paweł Nosko
 * @company Design Cart
 */

defined( 'ABSPATH' ) || exit;

$dc_wishlist_wishlist_page_id = (int) ( $settings['general']['wishlist_page_id'] ?? 0 );
$dc_wishlist_wishlist_url     = $dc_wishlist_wishlist_page_id ? get_permalink( $dc_wishlist_wishlist_page_id ) : '';
?>
<div id="tab-documentation" class="dc-tab-panel" role="tabpanel">
	<div class="dc-section-card dc-section dc-docs">
		<div class="dc-section-card__head">
			<span class="dc-section-card__icon"><i class="fa fa-book"></i></span>
			<div>
				<h3 class="dc-section-card__title"><?php esc_html_e( 'Szybki start', 'design-cart-wishlist' ); ?></h3>
				<p class="dc-section-card__sub"><?php esc_html_e( 'Design Cart Wishlist — lista życzeń dla WooCommerce', 'design-cart-wishlist' ); ?></p>
			</div>
		</div>

		<ol class="dc-docs__steps">
			<li><?php esc_html_e( 'Aktywuj plugin i upewnij się, że WooCommerce jest włączone.', 'design-cart-wishlist' ); ?></li>
			<li><?php esc_html_e( 'W zakładce Ogólne wybierz stronę listy życzeń (tworzona automatycznie przy aktywacji).', 'design-cart-wishlist' ); ?></li>
			<li><?php esc_html_e( 'W zakładce Header ustaw selektor kontenera w nagłówku sklepu.', 'design-cart-wishlist' ); ?></li>
			<li><?php esc_html_e( 'Hooki WooCommerce w zakładce Produkty działają od razu — reguły placement dodaj tylko dla niestandardowego HTML.', 'design-cart-wishlist' ); ?></li>
		</ol>

		<?php if ( $dc_wishlist_wishlist_url ) : ?>
			<p class="dc-docs__link">
				<a class="dc-btn dc-btn--ghost dc-btn--sm" href="<?php echo esc_url( $dc_wishlist_wishlist_url ); ?>" target="_blank" rel="noopener">
					<i class="fa fa-external-link"></i> <?php esc_html_e( 'Podgląd strony listy życzeń', 'design-cart-wishlist' ); ?>
				</a>
			</p>
		<?php endif; ?>
	</div>

	<div class="dc-section-card dc-section dc-docs">
		<div class="dc-section-card__head">
			<span class="dc-section-card__icon"><i class="fa fa-header"></i></span>
			<div>
				<h3 class="dc-section-card__title"><?php esc_html_e( 'Header — serduszko obok koszyka', 'design-cart-wishlist' ); ?></h3>
			</div>
		</div>

		<p><?php esc_html_e( 'Serduszko w headerze to link do listy życzeń (zalogowany) lub logowania (gość). Nie wymaga product ID.', 'design-cart-wishlist' ); ?></p>

		<table class="dc-docs__table">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Pole', 'design-cart-wishlist' ); ?></th>
					<th><?php esc_html_e( 'Opis', 'design-cart-wishlist' ); ?></th>
					<th><?php esc_html_e( 'Przykład', 'design-cart-wishlist' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><?php esc_html_e( 'Selektor kontenera', 'design-cart-wishlist' ); ?></td>
					<td><?php esc_html_e( 'Element, w którym wstawiamy serduszko.', 'design-cart-wishlist' ); ?></td>
					<td><code>.header-interface</code></td>
				</tr>
				<tr>
					<td><?php esc_html_e( 'Selektor odniesienia', 'design-cart-wishlist' ); ?></td>
					<td><?php esc_html_e( 'Opcjonalnie — wstaw względem koszyka, nie całego kontenera.', 'design-cart-wishlist' ); ?></td>
					<td><code>.header-cart</code></td>
				</tr>
				<tr>
					<td><?php esc_html_e( 'Placement', 'design-cart-wishlist' ); ?></td>
					<td><?php esc_html_e( 'Gdzie względem elementu docelowego.', 'design-cart-wishlist' ); ?></td>
					<td><code>before</code>, <code>after</code>, <code>prepend</code>, <code>append</code></td>
				</tr>
			</tbody>
		</table>

		<div class="dc-docs__example">
			<p class="dc-docs__example-title"><?php esc_html_e( 'Typowa konfiguracja', 'design-cart-wishlist' ); ?></p>
			<ul>
				<li><?php esc_html_e( 'Kontener:', 'design-cart-wishlist' ); ?> <code>.site-header__actions</code></li>
				<li><?php esc_html_e( 'Odniesienie:', 'design-cart-wishlist' ); ?> <code>.cart-contents</code></li>
				<li><?php esc_html_e( 'Placement:', 'design-cart-wishlist' ); ?> <code>before</code></li>
			</ul>
		</div>
	</div>

	<div class="dc-section-card dc-section dc-docs">
		<div class="dc-section-card__head">
			<span class="dc-section-card__icon"><i class="fa fa-th-large"></i></span>
			<div>
				<h3 class="dc-section-card__title"><?php esc_html_e( 'Produkty — trzy warstwy placement', 'design-cart-wishlist' ); ?></h3>
			</div>
		</div>

		<div class="dc-docs__layers">
			<div class="dc-docs__layer">
				<h4>1. <?php esc_html_e( 'Hooki WooCommerce (domyślnie)', 'design-cart-wishlist' ); ?></h4>
				<p><?php esc_html_e( 'Działają bez konfiguracji na standardowych listach, karcie produktu i blokach WC. Serduszko renderuje PHP od razu ze stanem listy życzeń.', 'design-cart-wishlist' ); ?></p>
			</div>
			<div class="dc-docs__layer">
				<h4>2. <?php esc_html_e( 'Reguły placement', 'design-cart-wishlist' ); ?></h4>
				<p><?php esc_html_e( 'Dla modułów, sliderów i page builderów z niestandardowym HTML. Definiujesz selektor, placement, preset overlay i auto product ID.', 'design-cart-wishlist' ); ?></p>
			</div>
			<div class="dc-docs__layer">
				<h4>3. <?php esc_html_e( 'Shortcode / ręczne wstawienie', 'design-cart-wishlist' ); ?></h4>
				<p><?php esc_html_e( 'Gdy potrzebujesz serduszka w dowolnym miejscu strony.', 'design-cart-wishlist' ); ?></p>
			</div>
		</div>

		<h4 class="dc-docs__subtitle"><?php esc_html_e( 'Auto-detekcja product ID', 'design-cart-wishlist' ); ?></h4>
		<p><?php esc_html_e( 'Domyślnie skrypt szuka ID produktu w górę DOM od selektora:', 'design-cart-wishlist' ); ?></p>
		<ul class="dc-docs__list">
			<li><code>data-product_id</code> / <code>data-product-id</code></li>
			<li><code>.add_to_cart_button</code></li>
			<li><?php esc_html_e( 'Klasa', 'design-cart-wishlist' ); ?> <code>post-{id}</code></li>
			<li><?php esc_html_e( 'Link', 'design-cart-wishlist' ); ?> <code>?add-to-cart=123</code></li>
		</ul>
		<p class="dc-hint"><?php esc_html_e( 'Gdy auto-detekcja zawiedzie — ustaw tryb Product ID na Selektor lub Atrybut w regule.', 'design-cart-wishlist' ); ?></p>
	</div>

	<div class="dc-section-card dc-section dc-docs">
		<div class="dc-section-card__head">
			<span class="dc-section-card__icon"><i class="fa fa-code"></i></span>
			<div>
				<h3 class="dc-section-card__title"><?php esc_html_e( 'Shortcodes', 'design-cart-wishlist' ); ?></h3>
			</div>
		</div>

		<table class="dc-docs__table">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Shortcode', 'design-cart-wishlist' ); ?></th>
					<th><?php esc_html_e( 'Opis', 'design-cart-wishlist' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><code>[dc_wishlist]</code></td>
					<td><?php esc_html_e( 'Pełna strona listy życzeń — lista produktów z miniaturą, usuwanie, dodaj do koszyka.', 'design-cart-wishlist' ); ?></td>
				</tr>
				<tr>
					<td><code>[dc_wishlist_button]</code></td>
					<td><?php esc_html_e( 'Serduszko na karcie produktu — bierze ID bieżącego produktu.', 'design-cart-wishlist' ); ?></td>
				</tr>
				<tr>
					<td><code>[dc_wishlist_button product_id="123"]</code></td>
					<td><?php esc_html_e( 'Serduszko dla konkretnego produktu.', 'design-cart-wishlist' ); ?></td>
				</tr>
				<tr>
					<td><code>[dc_wishlist_button product_id="123" position="overlay-top-right"]</code></td>
					<td><?php esc_html_e( 'Z presetem pozycji (inline, overlay-top-right, overlay-top-left itd.).', 'design-cart-wishlist' ); ?></td>
				</tr>
			</tbody>
		</table>
	</div>

	<div class="dc-section-card dc-section dc-docs">
		<div class="dc-section-card__head">
			<span class="dc-section-card__icon"><i class="fa fa-users"></i></span>
			<div>
				<h3 class="dc-section-card__title"><?php esc_html_e( 'Goście i konta użytkowników', 'design-cart-wishlist' ); ?></h3>
			</div>
		</div>

		<table class="dc-docs__table">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Ustawienie', 'design-cart-wishlist' ); ?></th>
					<th><?php esc_html_e( 'Zachowanie', 'design-cart-wishlist' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><?php esc_html_e( 'Goście mogą dodawać (cookie)', 'design-cart-wishlist' ); ?></td>
					<td><?php esc_html_e( 'Lista zapisywana w ciasteczku przeglądarki.', 'design-cart-wishlist' ); ?></td>
				</tr>
				<tr>
					<td><?php esc_html_e( 'Tylko zalogowani', 'design-cart-wishlist' ); ?></td>
					<td><?php esc_html_e( 'Kliknięcie serduszka przekierowuje do logowania.', 'design-cart-wishlist' ); ?></td>
				</tr>
				<tr>
					<td><?php esc_html_e( 'Połącz listę po logowaniu', 'design-cart-wishlist' ); ?></td>
					<td><?php esc_html_e( 'Produkty z cookie gościa trafiają na konto użytkownika.', 'design-cart-wishlist' ); ?></td>
				</tr>
			</tbody>
		</table>
	</div>

	<div class="dc-section-card dc-section dc-docs">
		<div class="dc-section-card__head">
			<span class="dc-section-card__icon"><i class="fa fa-paint-brush"></i></span>
			<div>
				<h3 class="dc-section-card__title"><?php esc_html_e( 'Wygląd serduszka', 'design-cart-wishlist' ); ?></h3>
			</div>
		</div>

		<p><?php esc_html_e( 'Style są rozdzielone na dwa konteksty:', 'design-cart-wishlist' ); ?></p>
		<ul class="dc-docs__list">
			<li><strong><?php esc_html_e( 'Header', 'design-cart-wishlist' ); ?></strong> — <?php esc_html_e( 'link w nagłówku sklepu', 'design-cart-wishlist' ); ?></li>
			<li><strong><?php esc_html_e( 'Listy i karta produktu', 'design-cart-wishlist' ); ?></strong> — <?php esc_html_e( 'przyciski toggle na produktach', 'design-cart-wishlist' ); ?></li>
		</ul>
		<p><?php esc_html_e( 'Dla każdego kontekstu ustawiasz: tło, kolor ikony, rozmiar, hover oraz (produkty) kolor/tło aktywnego — wypełnionego serduszka.', 'design-cart-wishlist' ); ?></p>
	</div>

	<div class="dc-section-card dc-section dc-docs">
		<div class="dc-section-card__head">
			<span class="dc-section-card__icon"><i class="fa fa-bug"></i></span>
			<div>
				<h3 class="dc-section-card__title"><?php esc_html_e( 'Debug placement', 'design-cart-wishlist' ); ?></h3>
			</div>
		</div>

		<p><?php esc_html_e( 'W zakładce Zaawansowane włącz tryb debug, a następnie odwiedź front sklepu jako administrator.', 'design-cart-wishlist' ); ?></p>
		<ul class="dc-docs__list">
			<li><span class="dc-docs__badge dc-docs__badge--ok"></span> <?php esc_html_e( 'Zielona ramka — anchor znaleziony, product ID wykryty', 'design-cart-wishlist' ); ?></li>
			<li><span class="dc-docs__badge dc-docs__badge--err"></span> <?php esc_html_e( 'Czerwona ramka — anchor OK, brak product ID', 'design-cart-wishlist' ); ?></li>
		</ul>
	</div>

	<div class="dc-section-card dc-section dc-docs dc-docs--meta">
		<div class="dc-section-card__head">
			<span class="dc-section-card__icon"><i class="fa fa-info-circle"></i></span>
			<div>
				<h3 class="dc-section-card__title"><?php esc_html_e( 'Informacje o module', 'design-cart-wishlist' ); ?></h3>
			</div>
		</div>
		<dl class="dc-docs__meta">
			<div><dt><?php esc_html_e( 'Nazwa', 'design-cart-wishlist' ); ?></dt><dd>Design Cart Wishlist</dd></div>
			<div><dt><?php esc_html_e( 'Wersja', 'design-cart-wishlist' ); ?></dt><dd><?php echo esc_html( DC_WISHLIST_VERSION ); ?></dd></div>
			<div><dt><?php esc_html_e( 'Autor', 'design-cart-wishlist' ); ?></dt><dd>Paweł Nosko</dd></div>
			<div><dt><?php esc_html_e( 'Firma', 'design-cart-wishlist' ); ?></dt><dd>Design Cart</dd></div>
		</dl>
	</div>
</div>
