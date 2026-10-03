/* ==========================================================================
   home.js — Home page interactions:
   hero parallax + text toolbar, client marquee, scroll-driven services wheel,
   work filter, testimonials.
   ========================================================================== */
(function () {
  'use strict';
  var QT = window.QT || { reduce: false, whenVisible: function (el, cb) { cb(true); } };

  /* ---- Hero: card fan follows the pointer ---- */
  (function heroParallax() {
    var hero = document.getElementById('hero');
    var fan = document.getElementById('heroFan');
    if (!hero || !fan || QT.reduce) return;
    hero.addEventListener('pointermove', function (e) {
      if (e.pointerType !== 'mouse') return;
      var r = hero.getBoundingClientRect();
      fan.style.setProperty('--mx', (((e.clientX - r.left) / r.width) * 2 - 1).toFixed(3));
      fan.style.setProperty('--my', (((e.clientY - r.top) / r.height) * 2 - 1).toFixed(3));
    });
    hero.addEventListener('pointerleave', function () {
      fan.style.setProperty('--mx', 0);
      fan.style.setProperty('--my', 0);
    });
  })();

  /* ---- Hero: text formatting toolbar (size, bold / italic / underline, colour) ---- */
  (function heroToolbar() {
    var bar = document.getElementById('ftool');
    var title = document.getElementById('heroTitle');
    if (!bar || !title) return;

    var headBtn = document.getElementById('ftoolHeading');
    var headLabel = document.getElementById('ftoolHeadingLabel');
    var sizes = document.getElementById('ftoolSizes');
    var colorBtn = document.getElementById('ftoolColor');
    var swatches = document.getElementById('ftoolSwatches');
    var options = Array.prototype.slice.call(sizes.querySelectorAll('[role=option]'));
    var swatchBtns = Array.prototype.slice.call(swatches.querySelectorAll('button'));
    var swatchColor = { default: 'transparent', navy: '#00247e', blue: '#0c5adb', cyan: '#00a9b5', gradient: '#0c5adb' };

    function closeMenus(except) {
      [[headBtn, sizes], [colorBtn, swatches]].forEach(function (p) {
        if (p[1] === except) return;
        p[1].hidden = true;
        p[0].setAttribute('aria-expanded', 'false');
      });
    }
    function toggleMenu(btn, menu) {
      var open = menu.hidden;
      closeMenus(open ? menu : null);
      menu.hidden = !open;
      btn.setAttribute('aria-expanded', String(open));
      return open;
    }
    function setSize(size) {
      title.dataset.size = size;
      options.forEach(function (o) {
        var on = o.dataset.size === size;
        o.setAttribute('aria-selected', String(on));
        if (on) headLabel.textContent = o.textContent;
      });
    }
    function setColor(c) {
      if (c === 'default') title.removeAttribute('data-color'); else title.dataset.color = c;
      swatchBtns.forEach(function (b) { b.classList.toggle('is-active', b.dataset.color === c); });
      bar.style.setProperty('--ftool-color', swatchColor[c]);
    }
    function reset() {
      setSize('h1'); setColor('default');
      ['bold', 'italic', 'underline'].forEach(function (s) {
        title.classList.remove('is-' + s);
        bar.querySelector('[data-style=' + s + ']').setAttribute('aria-pressed', 'false');
      });
    }

    headBtn.addEventListener('click', function () {
      if (toggleMenu(headBtn, sizes)) {
        var sel = options.filter(function (o) { return o.getAttribute('aria-selected') === 'true'; })[0] || options[0];
        sel.focus();
      }
    });
    headBtn.addEventListener('dblclick', reset);
    headBtn.addEventListener('keydown', function (e) {
      if (e.key === 'ArrowDown') { e.preventDefault(); if (sizes.hidden) toggleMenu(headBtn, sizes); options[0].focus(); }
    });
    options.forEach(function (o, i) {
      o.addEventListener('click', function () { setSize(o.dataset.size); closeMenus(); headBtn.focus(); });
      o.addEventListener('keydown', function (e) {
        if (e.key === 'ArrowDown') { e.preventDefault(); options[(i + 1) % options.length].focus(); }
        else if (e.key === 'ArrowUp') { e.preventDefault(); options[(i - 1 + options.length) % options.length].focus(); }
        else if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); o.click(); }
      });
    });

    bar.querySelectorAll('.ftool__icon').forEach(function (b) {
      b.addEventListener('click', function () {
        var on = b.getAttribute('aria-pressed') !== 'true';
        b.setAttribute('aria-pressed', String(on));
        title.classList.toggle('is-' + b.dataset.style, on);
      });
    });

    colorBtn.addEventListener('click', function () { toggleMenu(colorBtn, swatches); });
    swatchBtns.forEach(function (b) {
      b.addEventListener('click', function () { setColor(b.dataset.color); closeMenus(); colorBtn.focus(); });
    });

    document.addEventListener('pointerdown', function (e) { if (!bar.contains(e.target)) closeMenus(); });
    bar.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') { closeMenus(); (e.target.closest('.ftool__wrap') || bar).querySelector('button').focus(); }
    });
  })();

  /* ---- Client logos: duplicate each track so the loop is seamless ---- */
  document.querySelectorAll('.marquee__track').forEach(function (track) {
    Array.prototype.slice.call(track.children).forEach(function (li) {
      var clone = li.cloneNode(true);
      clone.setAttribute('aria-hidden', 'true');
      track.appendChild(clone);
    });
  });

  /* ---- Services: pinned section, wheel + slides driven by scroll ---- */
  (function services() {
    var track = document.getElementById('servicesTrack');
    var panel = document.getElementById('servicesPanel');
    if (!track || !panel) return;
    var wheel = panel.querySelector('.wheel');
    var items = Array.prototype.slice.call(panel.querySelectorAll('.wheel__item'));
    var slides = Array.prototype.slice.call(panel.querySelectorAll('.sslide'));
    var N = items.length;
    var STEP = 30;                       // degrees between numbers
    var mq = window.matchMedia('(max-width: 899px)');
    var cur = 0, target = 0, raf = 0, active = -1;

    items.forEach(function (el, i) {     // accessible names for the wheel buttons
      var h = slides[i] && slides[i].querySelector('h3');
      el.querySelector('button').setAttribute('aria-label', 'Service ' + (i + 1) + (h ? ': ' + h.textContent : ''));
    });

    function unit() { return window.innerHeight * 0.75; }          // scroll distance per service
    function trackTop() { return track.getBoundingClientRect().top + window.scrollY; }

    function draw(pos) {
      items.forEach(function (el, i) {
        // keep each number between -90deg and +90deg; it wraps while off-screen on the left
        var d = ((i - pos + N + 3) % N) - 3;
        el.style.setProperty('--a', (d * STEP).toFixed(2) + 'deg');
      });
      panel.style.setProperty('--n', pos.toFixed(3));
      var idx = Math.max(0, Math.min(N - 1, Math.round(pos)));
      if (idx !== active) {
        active = idx;
        items.forEach(function (el, i) { el.classList.toggle('is-active', i === idx); });
        slides.forEach(function (el, i) { el.classList.toggle('is-active', i === idx); });
        panel.dataset.active = idx;
      }
    }

    function scrollPos() {                                          // 0 .. N-1 from the page scroll
      var p = (-track.getBoundingClientRect().top) / unit();
      return Math.max(0, Math.min(N - 1, p));
    }

    function loop() {
      var diff = target - cur;
      if (QT.reduce || Math.abs(diff) < 0.002) { cur = target; raf = 0; } else { cur += diff * 0.14; raf = requestAnimationFrame(loop); }
      draw(cur);
    }
    function onScroll() {
      if (mq.matches) return;
      target = scrollPos();
      if (!raf) raf = requestAnimationFrame(loop);
    }

    function goTo(i) {
      i = Math.max(0, Math.min(N - 1, i));
      if (mq.matches) {                                             // mobile: no pinning, just switch
        cur = target = i; draw(i); return;
      }
      window.scrollTo({ top: trackTop() + i * unit() + 2, behavior: QT.reduce ? 'auto' : 'smooth' });
    }

    function applyMode() {
      wheel.classList.toggle('is-scroll', !mq.matches);
      panel.classList.toggle('is-discrete', mq.matches);
      cur = target = mq.matches ? Math.max(0, active) : scrollPos();
      draw(cur);
    }

    items.forEach(function (el, i) { el.querySelector('button').addEventListener('click', function () { goTo(i); }); });
    panel.addEventListener('keydown', function (e) {
      if (e.key === 'ArrowRight' || e.key === 'ArrowDown') { e.preventDefault(); goTo(Math.round(cur) + 1); }
      if (e.key === 'ArrowLeft' || e.key === 'ArrowUp') { e.preventDefault(); goTo(Math.round(cur) - 1); }
    });
    window.addEventListener('scroll', onScroll, { passive: true });
    window.addEventListener('resize', onScroll, { passive: true });
    (mq.addEventListener ? mq.addEventListener.bind(mq, 'change') : mq.addListener.bind(mq))(applyMode);
    applyMode();
  })();

  /* ---- Work: filter chips ---- */
  (function work() {
    var tags = document.getElementById('workTags');
    var grid = document.getElementById('workGrid');
    if (!tags || !grid) return;
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
  })();

  /* ---- Testimonials: vertical slider ---- */
  (function testimonials() {
    var stage = document.getElementById('testiStage');
    if (!stage) return;
    var slides = Array.prototype.slice.call(stage.querySelectorAll('.tslide'));
    var dots = Array.prototype.slice.call(document.querySelectorAll('#testiDots button'));
    var N = slides.length, active = 0, timer = null, paused = false, visible = true;

    function render(next) {
      slides.forEach(function (el, i) {
        el.classList.toggle('is-active', i === next);
        el.classList.toggle('is-before', i < next);
      });
      dots.forEach(function (d, i) {
        d.classList.toggle('is-active', i === next);
        d.setAttribute('aria-selected', String(i === next));
      });
      active = next;
    }
    function go(i) { render(((i % N) + N) % N); restart(); }
    function restart() {
      clearInterval(timer);
      if (QT.reduce) return;
      timer = setInterval(function () { if (!paused && visible) render((active + 1) % N); }, 7000);
    }
    document.getElementById('testiPrev').addEventListener('click', function () { go(active - 1); });
    document.getElementById('testiNext').addEventListener('click', function () { go(active + 1); });
    dots.forEach(function (d, i) { d.addEventListener('click', function () { go(i); }); });
    stage.addEventListener('pointerenter', function () { paused = true; });
    stage.addEventListener('pointerleave', function () { paused = false; });
    QT.whenVisible(stage, function (v) { visible = v; });
    render(0);
    restart();
  })();
})();
