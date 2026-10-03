/* ==========================================================================
   project.js — project details: phone / laptop hero variant, pointer tilt,
   and the "more work" filter chips.
   ========================================================================== */
(function () {
  'use strict';
  var QT = window.QT || { reduce: false };

  /* ---- choose the hero mockup: project.html (phone) or project.html?device=laptop ---- */
  var phone = document.getElementById('devicePhone');
  var laptop = document.getElementById('deviceLaptop');
  var wantLaptop = /[?&]device=laptop\b/.test(location.search) || location.hash === '#laptop';
  if (phone && laptop) {
    phone.hidden = wantLaptop;
    laptop.hidden = !wantLaptop;
  }

  /* ---- gentle tilt that follows the pointer ---- */
  var hero = document.querySelector('.proj-hero');
  if (hero && !QT.reduce) {
    hero.addEventListener('pointermove', function (e) {
      if (e.pointerType !== 'mouse') return;
      var r = hero.getBoundingClientRect();
      var x = ((e.clientX - r.left) / r.width) * 2 - 1;
      var y = ((e.clientY - r.top) / r.height) * 2 - 1;
      document.querySelectorAll('.device').forEach(function (d) {
        d.style.setProperty('--tx', x.toFixed(3));
        d.style.setProperty('--ty', y.toFixed(3));
      });
    });
    hero.addEventListener('pointerleave', function () {
      document.querySelectorAll('.device').forEach(function (d) {
        d.style.setProperty('--tx', 0);
        d.style.setProperty('--ty', 0);
      });
    });
  }

  /* ---- more work: filter chips ---- */
  var tags = document.getElementById('workTags');
  var grid = document.getElementById('workGrid');
  if (tags && grid) {
    var cards = Array.prototype.slice.call(grid.children);
    var current = null;
    tags.addEventListener('click', function (e) {
      var btn = e.target.closest('button[data-filter]');
      if (!btn) return;
      var f = btn.dataset.filter;
      current = current === f ? null : f;
      tags.querySelectorAll('button').forEach(function (b) { b.classList.toggle('is-active', b.dataset.filter === current); });
      cards.forEach(function (c) {
        var show = !current || c.dataset.cat.split(' ').indexOf(current) > -1;
        c.hidden = !show;
        c.classList.add('is-visible');
        c.classList.remove('is-shown');
        if (show) { void c.offsetWidth; c.classList.add('is-shown'); }
      });
    });
  }
})();
