/**
 * Design Cart Wishlist — admin rules repeater.
 *
 * @author  Paweł Nosko
 * @company Design Cart
 */
(function () {
  'use strict';

  var list = document.getElementById('dcWishlistRulesList');
  var addBtn = document.getElementById('dcWishlistAddRule');
  if (!list || !addBtn || typeof dcWishlistAdmin === 'undefined') {
    return;
  }

  var ruleIndex = list.querySelectorAll('.dc-wishlist-rule-row:not(.dc-wishlist-rule-row--template)').length;

  function bindRow(row) {
    var contextSelect = row.querySelector('.dc-wishlist-rule-context');
    var urlWrap = row.querySelector('.dc-wishlist-rule-url-wrap');
    var positionSelect = row.querySelector('.dc-wishlist-rule-position');
    var customWrap = row.querySelector('.dc-wishlist-rule-custom-position');
    var productIdSelect = row.querySelector('.dc-wishlist-rule-product-id');
    var productIdAdvanced = row.querySelector('.dc-wishlist-rule-product-id-advanced');
    var removeBtn = row.querySelector('.dc-wishlist-rule-remove');

    if (contextSelect && urlWrap) {
      contextSelect.addEventListener('change', function () {
        urlWrap.hidden = contextSelect.value !== 'custom';
      });
    }

    if (positionSelect && customWrap) {
      positionSelect.addEventListener('change', function () {
        customWrap.hidden = positionSelect.value !== 'custom';
      });
    }

    if (productIdSelect && productIdAdvanced) {
      productIdSelect.addEventListener('change', function () {
        productIdAdvanced.hidden = productIdSelect.value === 'auto';
      });
    }

    if (removeBtn) {
      removeBtn.addEventListener('click', function () {
        if (window.confirm(dcWishlistAdmin.i18n.confirmRemove)) {
          row.remove();
          renumber();
        }
      });
    }
  }

  function renumber() {
    list.querySelectorAll('.dc-wishlist-rule-row:not(.dc-wishlist-rule-row--template)').forEach(function (row, i) {
      var num = row.querySelector('.dc-wishlist-rule-num');
      if (num) {
        num.textContent = String(i + 1);
      }
    });
  }

  function addRule() {
    var html = dcWishlistAdmin.ruleTemplate.replace(/__INDEX__/g, String(ruleIndex));
    var wrap = document.createElement('div');
    wrap.innerHTML = html.trim();
    var row = wrap.firstElementChild;
    if (!row) {
      return;
    }
    row.classList.remove('dc-wishlist-rule-row--template');
    row.querySelector('input[name*="[id]"]').value = 'rule_' + Date.now();
    list.appendChild(row);
    bindRow(row);
    ruleIndex += 1;
    renumber();
  }

  list.querySelectorAll('.dc-wishlist-rule-row:not(.dc-wishlist-rule-row--template)').forEach(bindRow);
  addBtn.addEventListener('click', addRule);
})();
