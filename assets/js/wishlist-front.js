/**
 * Design Cart Wishlist — frontend scripts.
 *
 * @author  Paweł Nosko
 * @company Design Cart
 */
(function () {
  'use strict';

  if (typeof dcWishlist === 'undefined') {
    return;
  }

  var cfg = dcWishlist;
  var wishlistIds = (cfg.productIds || []).map(Number);

  function $(sel, root) {
    return (root || document).querySelector(sel);
  }

  function $$(sel, root) {
    return Array.prototype.slice.call((root || document).querySelectorAll(sel));
  }

  function uniqueId() {
    return 'rule_' + Date.now() + '_' + Math.floor(Math.random() * 1000);
  }

  function inWishlist(id) {
    return wishlistIds.indexOf(Number(id)) !== -1;
  }

  function setWishlistIds(ids) {
    wishlistIds = (ids || []).map(Number);
    syncAllButtons();
    updateHeaderCount();
  }

  function syncButton(btn, productId) {
    var active = inWishlist(productId);
    btn.classList.toggle('is-active', active);
    btn.setAttribute('aria-pressed', active ? 'true' : 'false');
    btn.setAttribute('aria-label', active ? 'Usuń z listy życzeń' : 'Dodaj do listy życzeń');

    var icon = btn.querySelector('.dc-wishlist-btn__icon');
    if (icon && cfg.heartSvg) {
      icon.outerHTML = active ? cfg.heartSvg.filled : cfg.heartSvg.outline;
    }
  }

  function syncAllButtons() {
    $$('[data-dc-wishlist="toggle"]').forEach(function (btn) {
      var id = Number(btn.getAttribute('data-product-id'));
      if (id) {
        syncButton(btn, id);
      }
    });
  }

  function updateHeaderCount() {
    var link = $('[data-dc-wishlist="header-link"]');
    if (!link) {
      return;
    }
    var countEl = link.querySelector('.dc-wishlist-btn__count');
    var count = wishlistIds.length;
    if (count > 0) {
      if (!countEl) {
        countEl = document.createElement('span');
        countEl.className = 'dc-wishlist-btn__count';
        link.appendChild(countEl);
      }
      countEl.textContent = String(count);
    } else if (countEl) {
      countEl.remove();
    }
    var icon = link.querySelector('.dc-wishlist-btn__icon');
    if (icon) {
      icon.classList.toggle('is-filled', count > 0);
    }
  }

  function buildHeaderLink() {
    var count = wishlistIds.length;
    var url = cfg.wishlistUrl;
    if (!cfg.isLoggedIn && cfg.header && cfg.header.guest_behavior === 'login') {
      url = cfg.loginUrl;
    }
    if (!cfg.isLoggedIn && cfg.guestMode === 'login_only') {
      url = cfg.loginUrl;
    }

    var a = document.createElement('a');
    a.href = url;
    a.className = 'dc-wishlist-btn dc-wishlist-btn--header';
    a.setAttribute('data-dc-wishlist', 'header-link');
    a.setAttribute('data-dc-wishlist-injected', 'header');
    a.setAttribute('aria-label', 'Lista życzeń');
    a.innerHTML = (count > 0 ? cfg.heartSvg.filled : cfg.heartSvg.outline);
    if (count > 0) {
      a.innerHTML += '<span class="dc-wishlist-btn__count">' + count + '</span>';
    }
    return a;
  }

  function placeElement(parent, node, placement) {
    if (!parent || !node) {
      return false;
    }
    switch (placement) {
      case 'prepend':
        parent.insertBefore(node, parent.firstChild);
        break;
      case 'append':
        parent.appendChild(node);
        break;
      case 'before':
        parent.parentNode.insertBefore(node, parent);
        break;
      case 'after':
        if (parent.nextSibling) {
          parent.parentNode.insertBefore(node, parent.nextSibling);
        } else {
          parent.parentNode.appendChild(node);
        }
        break;
      default:
        parent.appendChild(node);
    }
    return true;
  }

  function resolveProductId(anchor, rule) {
    var mode = rule.product_id || 'auto';
    var id = 0;

    if (mode === 'selector' && rule.product_id_selector) {
      var el = anchor.querySelector(rule.product_id_selector) || $(rule.product_id_selector, anchor.closest('.product, article, li'));
      if (el) {
        id = extractIdFromElement(el, rule.product_id_attr || 'data-product_id');
      }
    } else if (mode === 'attribute' && rule.product_id_selector) {
      id = extractIdFromElement(anchor.querySelector(rule.product_id_selector) || anchor, rule.product_id_attr || 'data-product_id');
    } else {
      id = autoDetectProductId(anchor);
    }

    return id > 0 ? id : 0;
  }

  function extractIdFromElement(el, attr) {
    if (!el) {
      return 0;
    }
    if (attr && el.getAttribute(attr)) {
      return Number(el.getAttribute(attr));
    }
    if (el.dataset && el.dataset.productId) {
      return Number(el.dataset.productId);
    }
    if (el.dataset && el.dataset.product_id) {
      return Number(el.dataset.product_id);
    }
    return 0;
  }

  function autoDetectProductId(anchor) {
    var root = anchor.closest('.product, .wc-block-grid__product, li.product, article[id*="product"]') || anchor;
    var selectors = [
      '[data-product_id]',
      '[data-product-id]',
      '.add_to_cart_button',
      'a[href*="add-to-cart="]',
      'input[name="product_id"]',
      'input[name="add-to-cart"]'
    ];

    var el;
    var i;
    for (i = 0; i < selectors.length; i++) {
      el = root.querySelector(selectors[i]);
      if (el) {
        var id = extractIdFromElement(el, 'data-product_id') || extractIdFromElement(el, 'data-product-id');
        if (!id && el.name === 'product_id') {
          id = Number(el.value);
        }
        if (!id && el.name === 'add-to-cart') {
          id = Number(el.value);
        }
        if (!id && el.href) {
          var m = el.href.match(/add-to-cart=(\d+)/);
          if (m) {
            id = Number(m[1]);
          }
        }
        if (id) {
          return id;
        }
      }
    }

    var classMatch = (root.className || '').match(/post-(\d+)/);
    if (classMatch) {
      return Number(classMatch[1]);
    }

    if (root.id) {
      var idMatch = root.id.match(/product-(\d+)/);
      if (idMatch) {
        return Number(idMatch[1]);
      }
    }

    return 0;
  }

  function ruleMatchesContext(rule) {
    var ctx = rule.context || 'loop';
    if (ctx === 'everywhere') {
      return true;
    }
    if (ctx === 'custom') {
      if (!rule.url_pattern) {
        return false;
      }
      return (cfg.currentUrl || window.location.pathname).indexOf(rule.url_pattern) !== -1;
    }
    return cfg.context === ctx;
  }

  function hasHookButtonIn(anchor) {
    var root = anchor.closest('.product, li.product') || anchor;
    return !!root.querySelector('[data-dc-wishlist-src="hook"]');
  }

  function applyPositionWrap(btn, position, custom) {
    if (position === 'inline') {
      return btn;
    }

    var wrap = document.createElement('span');
    wrap.className = 'dc-wishlist-wrap dc-wishlist-wrap--' + position;

    if (position === 'custom' && custom) {
      wrap.className = 'dc-wishlist-wrap dc-wishlist-wrap--custom';
      wrap.style.position = custom.mode === 'relative' ? 'relative' : 'absolute';
      ['top', 'right', 'bottom', 'left'].forEach(function (side) {
        if (custom[side]) {
          wrap.style[side] = custom[side];
        }
      });
      var host = btn.parentElement;
      if (host && custom.mode === 'absolute') {
        var pos = window.getComputedStyle(host).position;
        if (pos === 'static') {
          host.style.position = 'relative';
        }
      }
    }

    wrap.appendChild(btn);
    return wrap;
  }

  function buildProductButton(productId, rule) {
    var btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'dc-wishlist-btn dc-wishlist-btn--product dc-wishlist-btn--' + (rule.position || 'inline');
    btn.setAttribute('data-product-id', String(productId));
    btn.setAttribute('data-dc-wishlist', 'toggle');
    btn.setAttribute('data-dc-wishlist-src', 'rule');
    btn.setAttribute('data-dc-wishlist-rule', rule.id || '');
    btn.innerHTML = inWishlist(productId) ? cfg.heartSvg.filled : cfg.heartSvg.outline;
    if (inWishlist(productId)) {
      btn.classList.add('is-active');
      btn.setAttribute('aria-pressed', 'true');
    } else {
      btn.setAttribute('aria-pressed', 'false');
    }
    btn.setAttribute('aria-label', inWishlist(productId) ? 'Usuń z listy życzeń' : 'Dodaj do listy życzeń');
    return applyPositionWrap(btn, rule.position || 'inline', rule.position_custom);
  }

  function injectHeader() {
    var header = cfg.header || {};
    if (!header.enabled || !header.host_selector) {
      return;
    }
    if ($('[data-dc-wishlist-injected="header"]')) {
      return;
    }

    var host = $(header.host_selector);
    if (!host) {
      return;
    }

    var target = host;
    if (header.reference_selector) {
      var ref = $(header.reference_selector);
      if (ref) {
        target = ref;
      }
    }

    var link = buildHeaderLink();
    placeElement(target, link, header.placement || 'append');
  }

  function injectRules() {
    (cfg.rules || []).forEach(function (rule) {
      if (!rule.enabled || !rule.selector || !ruleMatchesContext(rule)) {
        return;
      }

      var nodes = $$(rule.selector);
      if (rule.match === 'first' && nodes.length) {
        nodes = [nodes[0]];
      }

      nodes.forEach(function (anchor) {
        var flag = 'rule-' + (rule.id || rule.selector);
        if (anchor.getAttribute('data-dc-wishlist-injected') === flag) {
          return;
        }
        if (anchor.querySelector('[data-dc-wishlist-injected="' + flag + '"]')) {
          return;
        }
        if (hasHookButtonIn(anchor)) {
          return;
        }

        var productId = resolveProductId(anchor, rule);
        if (!productId) {
          if (cfg.debug) {
            anchor.classList.add('dc-wishlist-debug-miss');
          }
          return;
        }

        if (cfg.debug) {
          anchor.classList.add('dc-wishlist-debug-hit');
          anchor.setAttribute('data-dc-wishlist-debug-id', String(productId));
        }

        var node = buildProductButton(productId, rule);
        node.setAttribute('data-dc-wishlist-injected', flag);
        placeElement(anchor, node, rule.placement || 'append');
        anchor.setAttribute('data-dc-wishlist-injected', flag);
      });
    });
  }

  function showToast(message) {
    var toast = document.createElement('div');
    toast.className = 'dc-wishlist-toast';
    toast.textContent = message;
    document.body.appendChild(toast);
    requestAnimationFrame(function () {
      toast.classList.add('is-visible');
    });
    setTimeout(function () {
      toast.classList.remove('is-visible');
      setTimeout(function () {
        toast.remove();
      }, 300);
    }, 2500);
  }

  function toggleProduct(productId, btn) {
    if (!cfg.isLoggedIn && cfg.guestMode === 'login_only') {
      showToast(cfg.i18n.loginNeeded);
      window.location.href = cfg.loginUrl;
      return;
    }

    btn.disabled = true;
    var body = new FormData();
    body.append('action', 'dc_wishlist_toggle');
    body.append('nonce', cfg.nonce);
    body.append('product_id', String(productId));

    fetch(cfg.ajaxUrl, {
      method: 'POST',
      credentials: 'same-origin',
      body: body
    })
      .then(function (r) {
        return r.json();
      })
      .then(function (res) {
        btn.disabled = false;
        if (!res.success) {
          if (res.data && res.data.needs_auth) {
            showToast(cfg.i18n.loginNeeded);
            window.location.href = res.data.login_url || cfg.loginUrl;
            return;
          }
          showToast((res.data && res.data.message) || cfg.i18n.error);
          return;
        }
        setWishlistIds(res.data.ids);
        showToast(res.data.in_list ? cfg.i18n.added : cfg.i18n.removed);
      })
      .catch(function () {
        btn.disabled = false;
        showToast(cfg.i18n.error);
      });
  }

  function onClick(e) {
    var btn = e.target.closest('[data-dc-wishlist="toggle"]');
    if (!btn) {
      return;
    }
    e.preventDefault();
    e.stopPropagation();
    var productId = Number(btn.getAttribute('data-product-id'));
    if (productId) {
      toggleProduct(productId, btn);
    }
  }

  function initObserver() {
    if (!window.MutationObserver) {
      return;
    }
    var timer;
    var observer = new MutationObserver(function () {
      clearTimeout(timer);
      timer = setTimeout(function () {
        injectHeader();
        injectRules();
      }, 120);
    });
    observer.observe(document.body, { childList: true, subtree: true });
  }

  function init() {
    injectHeader();
    injectRules();
    document.addEventListener('click', onClick);
    initObserver();
    updateHeaderCount();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
