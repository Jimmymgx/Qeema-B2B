/* ==========================================================================
   service-detail.js — the "what's included" list: the row under the pointer
   (or keyboard focus) becomes active and shows its thumbnail. Row 1 is active
   by default, as in the design. Class toggles only (no layout work).
   ========================================================================== */
(function () {
  'use strict';
  var list = document.getElementById('sdRows');
  if (!list) return;
  var rows = Array.prototype.slice.call(list.querySelectorAll('.sd-row'));
  function activate(row) {
    rows.forEach(function (r) { r.classList.toggle('is-active', r === row); });
  }
  rows.forEach(function (row) {
    row.setAttribute('tabindex', '0');
    row.addEventListener('pointerenter', function (e) { if (e.pointerType === 'mouse') activate(row); });
    row.addEventListener('focus', function () { activate(row); });
    // touch: tap a row to open its thumbnail
    row.addEventListener('click', function () { activate(row); });
  });
})();
