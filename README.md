<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Design Cart Wishlist for WooCommerce — Documentation</title>
<meta name="description" content="Documentation for Design Cart Wishlist for WooCommerce: installation, settings, header placement, CSS selector rules, shortcodes, guest wishlists, and theme integration without hooks.">
</head>
<body>

<article>

<p><strong>Repository description (GitHub About):</strong> A flexible WooCommerce wishlist plugin with configurable heart buttons in the header, product loops, and single product pages — theme-agnostic placement via CSS selectors, WooCommerce hooks, and shortcodes.</p>

<h1>Design Cart Wishlist for WooCommerce</h1>

<p><em>Author: Paweł Nosko · Design Cart · License: GPL v2 or later · Version: 1.0.0</em></p>

<p>Design Cart Wishlist for WooCommerce adds a full wishlist experience to WordPress shops: save products with a heart icon, view a dedicated wishlist page, and inject buttons almost anywhere in your theme — with or without PHP hooks.</p>

<h2>Requirements</h2>
<ul>
<li>WordPress 6.0+</li>
<li>PHP 7.4+</li>
<li>WooCommerce (active)</li>
<li>Tested up to WordPress 7.0</li>
</ul>

<h2>Features</h2>
<ul>
<li>Heart link in the site header (wishlist page or login)</li>
<li>Heart toggle buttons on product archives, single product pages, and WooCommerce blocks</li>
<li>Three placement layers: WooCommerce hooks, CSS selector rules, shortcodes</li>
<li>Separate appearance settings for header vs product buttons</li>
<li>Guest wishlists (cookie) with optional merge after login</li>
<li>Auto product ID detection in custom HTML / page builders</li>
<li>Debug placement mode for administrators</li>
<li>Admin UI built with DC Interface (Design Cart design system)</li>
</ul>

<h2>Installation</h2>
<ol>
<li>Download or clone this repository.</li>
<li>Upload the <code>design-cart-wishlist</code> folder to <code>/wp-content/plugins/</code>.</li>
<li>Activate the plugin in <strong>Plugins</strong>.</li>
<li>Ensure WooCommerce is installed and active.</li>
<li>Open <strong>Wishlist</strong> in the admin sidebar (heart icon) → <strong>Settings</strong>.</li>
</ol>

<p>On activation, a wishlist page is created automatically with the <code>[dc_wishlist]</code> shortcode.</p>

<h3>Manual install (ZIP)</h3>
<ol>
<li>Download the release ZIP.</li>
<li>Go to <strong>Plugins → Add New → Upload Plugin</strong>.</li>
<li>Activate and configure settings.</li>
</ol>

<h2>Quick start</h2>
<ol>
<li>Enable the plugin under <strong>General</strong>.</li>
<li>Select the wishlist page (created on activation).</li>
<li>Keep WooCommerce hooks enabled under <strong>Products</strong> — hearts appear on standard shop layouts immediately.</li>
<li>Configure <strong>Header</strong> with your theme container selector (e.g. <code>.site-header__actions</code>).</li>
<li>Add placement rules only for custom modules, sliders, or page builders.</li>
</ol>

<h2>Architecture — three placement layers</h2>

<h3>Layer 1 — WooCommerce hooks (default)</h3>
<p>Works out of the box with zero selector configuration:</p>
<ul>
<li><code>woocommerce_after_shop_loop_item</code> — product archives</li>
<li><code>woocommerce_single_product_summary</code> — single product</li>
<li>WooCommerce Blocks product grid filter</li>
</ul>
<p>Buttons are rendered server-side (PHP) with the correct active state.</p>

<h3>Layer 2 — Placement rules (CSS selectors)</h3>
<p>For custom HTML, Elementor sections, sliders, or non-standard product markup. Define a container selector, placement mode, position preset, and optional product ID source.</p>

<h3>Layer 3 — Shortcodes</h3>
<p>Manual insertion anywhere in content, widgets, or builders.</p>

<h2>Admin settings reference</h2>

<p>Path: <strong>Wishlist → Settings</strong> (admin sidebar, heart icon).</p>

<h3>Tab: General</h3>
<dl>
<dt>Enable plugin</dt>
<dd>Global on/off switch for frontend rendering and AJAX.</dd>
<dt>Guest mode</dt>
<dd><strong>Guests can add (cookie)</strong> — wishlist stored in browser cookie. <strong>Logged-in only</strong> — guests are redirected to login when toggling a product.</dd>
<dt>Wishlist page</dt>
<dd>WordPress page that displays the full wishlist (uses <code>[dc_wishlist]</code>).</dd>
<dt>Merge guest list on login</dt>
<dd>Combines cookie wishlist items into the user account after login.</dd>
</dl>

<h3>Tab: Header</h3>
<p>Header heart is a <strong>link</strong> to the wishlist or login — not a product toggle.</p>
<dl>
<dt>Enable in header</dt>
<dd>Inserts the heart link via JavaScript into the configured container.</dd>
<dt>Container selector</dt>
<dd>CSS selector of the header element (e.g. <code>.header-interface</code>, <code>.site-header__actions</code>).</dd>
<dt>Reference selector (optional)</dt>
<dd>Insert relative to a specific element, e.g. <code>.cart-contents</code> next to the cart icon.</dd>
<dt>Placement</dt>
<dd><code>prepend</code> · <code>append</code> · <code>before</code> · <code>after</code></dd>
<dt>Guest behavior</dt>
<dd><strong>Redirect to login</strong> or <strong>Wishlist page</strong> when a guest clicks the header heart.</dd>
</dl>

<h3>Tab: Products</h3>

<h4>WooCommerce hooks</h4>
<ul>
<li><strong>Product loop</strong> — archives, categories, shop</li>
<li><strong>Single product</strong> — product page summary</li>
<li><strong>WooCommerce blocks</strong> — block-based product grids</li>
</ul>

<h4>Placement rules (repeater)</h4>
<p>Click <strong>Add rule</strong> for each custom injection point.</p>
<dl>
<dt>Name</dt>
<dd>Internal label (e.g. "Homepage slider").</dd>
<dt>Enabled</dt>
<dd>Toggle rule without deleting configuration.</dd>
<dt>Context</dt>
<dd>Product loop · Single product · Everywhere · Custom URL</dd>
<dt>URL pattern</dt>
<dd>Visible when context is Custom URL (e.g. <code>/sale/</code>).</dd>
<dt>Match mode</dt>
<dd><strong>Every match</strong> — all matching elements. <strong>First match</strong> — first element only.</dd>
<dt>Container selector</dt>
<dd>CSS selector where the heart button is inserted.</dd>
<dt>Placement</dt>
<dd><code>prepend</code> · <code>append</code> · <code>before</code> · <code>after</code></dd>
<dt>Position preset</dt>
<dd>Inline · Overlay top-right/left · Overlay bottom-right/left · Custom (advanced)</dd>
<dt>Product ID</dt>
<dd>Auto (default) · Selector · Attribute</dd>
<dt>Custom position fields</dt>
<dd><code>position</code>, <code>top</code>, <code>right</code>, <code>bottom</code>, <code>left</code> — when preset is Custom.</dd>
<dt>Product ID selector / attribute</dt>
<dd>Advanced fields when Product ID is not Auto (e.g. <code>.add_to_cart_button</code>, <code>data-product_id</code>).</dd>
</dl>

<h3>Tab: Appearance</h3>
<p>Two independent style sets:</p>
<ul>
<li><strong>Header</strong> — heart link in the site header</li>
<li><strong>Product lists &amp; single product</strong> — toggle buttons on products</li>
</ul>
<p>Each set includes: background, icon color, size, hover background, hover color. Product context also has active (filled heart) color and background.</p>

<h3>Tab: Advanced</h3>
<dl>
<dt>Debug placement mode</dt>
<dd>Highlights rule anchors on the frontend for administrators. Green outline = product ID detected. Red outline = selector matched but ID missing.</dd>
</dl>

<h3>Tab: Documentation</h3>
<p>In-panel quick reference (shortcodes, header setup, guest modes, debug).</p>

<h2>Shortcodes</h2>

<h3>Full wishlist page</h3>
<pre>[dc_wishlist]</pre>
<p>Renders the wishlist product list: thumbnail, title, price, add to cart, remove heart.</p>

<h3>Single heart button</h3>
<pre>[dc_wishlist_button]</pre>
<p>On a single product page — uses the current product ID.</p>

<pre>[dc_wishlist_button product_id="123"]</pre>
<p>Heart for a specific product.</p>

<pre>[dc_wishlist_button product_id="123" position="overlay-top-right"]</pre>
<p>With position preset: <code>inline</code>, <code>overlay-top-right</code>, <code>overlay-top-left</code>, <code>overlay-bottom-right</code>, <code>overlay-bottom-left</code>.</p>

<h2>Theme integration (PHP)</h2>

<pre>&lt;?php
if ( class_exists( 'DC_Wishlist_Button' ) ) {
    echo DC_Wishlist_Button::render_product_button(
        get_the_ID(),
        array(
            'source'   =&gt; 'theme',
            'position' =&gt; 'overlay-top-right',
        )
    );
}
?&gt;</pre>

<h2>Auto product ID detection</h2>
<p>When Product ID is set to Auto, the script walks up the DOM from the container and looks for:</p>
<ul>
<li><code>[data-product_id]</code> / <code>[data-product-id]</code></li>
<li><code>.add_to_cart_button</code></li>
<li>Class <code>post-{id}</code></li>
<li>Link with <code>?add-to-cart=</code></li>
<li>Form inputs <code>product_id</code> / variation fields</li>
</ul>

<h2>Guest wishlist &amp; storage</h2>
<ul>
<li><strong>Logged-in users</strong> — wishlist stored in user meta.</li>
<li><strong>Guests</strong> — wishlist stored in a secure HTTP-only cookie (JSON array of product IDs).</li>
<li><strong>Merge on login</strong> — optional merge of guest cookie into user account.</li>
</ul>

<h2>AJAX</h2>
<p>Action: <code>dc_wishlist_toggle</code> · Nonce: <code>dc_wishlist</code> · Adds or removes a product and returns updated IDs and count.</p>

<h2>File structure</h2>
<pre>
design-cart-wishlist/
├── design-cart-wishlist.php    Main plugin file
├── readme.txt                  WordPress.org readme
├── includes/                   PHP classes (settings, storage, frontend, admin, ajax)
├── admin/                      Settings UI (DC Interface), views, assets
├── assets/                     Frontend CSS &amp; JS
├── templates/                  Wishlist page template
├── languages/                  Translation files
└── docs/                       Documentation
</pre>

<h2>Development</h2>
<ul>
<li>Text domain: <code>design-cart-wishlist</code></li>
<li>Function prefix: <code>dc_wishlist_</code></li>
<li>Class prefix: <code>DC_Wishlist_</code></li>
<li>Option key: <code>dc_wishlist_settings</code></li>
<li>Shortcodes: <code>dc_wishlist</code>, <code>dc_wishlist_button</code></li>
</ul>

<h2>Links</h2>
<ul>
<li>Website: <a href="https://designcart.pl">designcart.pl</a></li>
<li>WordPress.org: <a href="https://wordpress.org/plugins/design-cart-wishlist/">design-cart-wishlist</a> (when published)</li>
<li>Issues &amp; contributions: GitHub repository</li>
</ul>

<h2>Changelog</h2>
<h3>1.0.0</h3>
<ul>
<li>Initial release</li>
<li>WooCommerce hooks, header injection, placement rules, shortcodes</li>
<li>Guest cookie wishlist with merge on login</li>
<li>DC Interface admin panel</li>
<li>Plugin Check / WordPress.org ready</li>
</ul>

<h2>Support</h2>
<p>For bugs and feature requests, please use the GitHub Issues tab. Include WordPress version, WooCommerce version, active theme, and steps to reproduce.</p>

<p><em>© Design Cart · Paweł Nosko</em></p>

</article>

</body>
</html>
