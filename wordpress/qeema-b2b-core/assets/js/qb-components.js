/* ==========================================================================
   qb-components.js — one initialiser per widget, keyed by data-qb="name".
   Ported from the static home.js / our-work.js / project.js / book.js /
   service-detail.js; every id lookup is now scoped to the widget root, so a
   widget can be re-rendered (Elementor canvas) or used more than once.
   ========================================================================== */
(function () {
  'use strict';
  var QB = window.QB = window.QB || {};
  QB.components = QB.components || {};
  var C = QB.components;
  var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var editing = document.documentElement.classList.contains('qb-editing');
  var data = window.qbData || {};

  /* ---------------------------------------------------------------- hero */
  C.hero = function (hero) {
    var fan = hero.querySelector('.fan');
    var title = hero.querySelector('.hero__title');

    // card fan follows the pointer
    if (fan && !reduce) {
      hero.addEventListener('pointermove', function (e) {
        if (e.pointerType !== 'mouse') return;
        var r = hero.getBoundingClientRect();
        fan.style.setProperty('--mx', (((e.clientX - r.left) / r.width) * 2 - 1).toFixed(3));
        fan.style.setProperty('--my', (((e.clientY - r.top) / r.height) * 2 - 1).toFixed(3));
      });
      hero.addEventListener('pointerleave', function () { fan.style.setProperty('--mx', 0); fan.style.setProperty('--my', 0); });
    }

    // text formatting toolbar
    var bar = hero.querySelector('.ftool');
    if (!bar || !title) return;
    var headBtn = bar.querySelector('.ftool__btn--heading');
    var headLabel = headBtn.querySelector('span');
    var sizes = bar.querySelector('.ftool__menu');
    var colorBtn = bar.querySelector('.ftool__btn--color');
    var swatches = bar.querySelector('.ftool__swatches');
    var options = Array.prototype.slice.call(sizes.querySelectorAll('[role=option]'));
    var swatchBtns = Array.prototype.slice.call(swatches.querySelectorAll('button'));
    var swatchColor = { default: 'transparent', navy: '#00247e', blue: '#0c5adb', cyan: '#00a9b5', gradient: '#0c5adb' };

    function closeMenus(except) {
      [[headBtn, sizes], [colorBtn, swatches]].forEach(function (p) {
        if (p[1] === except) return;
        p[1].hidden = true; p[0].setAttribute('aria-expanded', 'false');
      });
    }
    function toggleMenu(btn, menu) {
      var open = menu.hidden;
      closeMenus(open ? menu : null);
      menu.hidden = !open; btn.setAttribute('aria-expanded', String(open));
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
        var b = bar.querySelector('[data-style=' + s + ']'); if (b) b.setAttribute('aria-pressed', 'false');
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
    swatchBtns.forEach(function (b) { b.addEventListener('click', function () { setColor(b.dataset.color); closeMenus(); colorBtn.focus(); }); });
    document.addEventListener('pointerdown', function (e) { if (!bar.contains(e.target)) closeMenus(); });
    bar.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') { closeMenus(); (e.target.closest('.ftool__wrap') || bar).querySelector('button').focus(); }
    });
  };

  /* ------------------------------------------------------------- marquee */
  C.marquee = function (root) {
    root.querySelectorAll('.marquee__track').forEach(function (track) {
      if (track.dataset.cloned) return;
      track.dataset.cloned = '1';
      Array.prototype.slice.call(track.children).forEach(function (li) {
        var clone = li.cloneNode(true);
        clone.setAttribute('aria-hidden', 'true');
        track.appendChild(clone);
      });
    });
  };

  /* ---------------------------------------------------- services wheel */
  C.wheel = function (section) {
    var track = section.querySelector('.services__track');
    var panel = section.querySelector('.services__panel');
    if (!track || !panel) return;
    var wheel = panel.querySelector('.wheel');
    var items = Array.prototype.slice.call(panel.querySelectorAll('.wheel__item'));
    var slides = Array.prototype.slice.call(panel.querySelectorAll('.sslide'));
    var N = items.length;
    if (!N) return;
    var artShared = panel.querySelector('.services__art-shared');
    var artPer = Array.prototype.slice.call(panel.querySelectorAll('.services__art-i'));
    function swapArt(idx) {
      var own = artPer.filter(function (im) { return parseInt(im.getAttribute('data-i'), 10) === idx; });
      artPer.forEach(function (im) { im.classList.toggle('is-off', own.indexOf(im) < 0); });
      if (artShared) artShared.classList.toggle('is-off', own.length > 0);
    }
    var STEP = 30;
    var mq = window.matchMedia('(max-width: 899px)');
    var cur = 0, target = 0, raf = 0, active = -1;
    var pinned = !editing;          // in the Elementor canvas the section stays a normal block (easier to edit)

    items.forEach(function (el, i) {
      var h = slides[i] && slides[i].querySelector('h3');
      el.querySelector('button').setAttribute('aria-label', 'Service ' + (i + 1) + (h ? ': ' + h.textContent : ''));
    });
    // runway height depends on how many services there are
    track.style.setProperty('--n', N);

    function unit() { return window.innerHeight * 0.75; }
    function trackTop() { return track.getBoundingClientRect().top + window.scrollY; }
    function draw(pos) {
      items.forEach(function (el, i) {
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
        if (artPer.length) swapArt(idx);
      }
    }
    function scrollPos() { return Math.max(0, Math.min(N - 1, (-track.getBoundingClientRect().top) / unit())); }
    function loop() {
      var diff = target - cur;
      if (reduce || Math.abs(diff) < 0.002) { cur = target; raf = 0; } else { cur += diff * 0.14; raf = requestAnimationFrame(loop); }
      draw(cur);
    }
    function onScroll() {
      if (mq.matches || !pinned) return;
      target = scrollPos();
      if (!raf) raf = requestAnimationFrame(loop);
    }
    function goTo(i) {
      i = Math.max(0, Math.min(N - 1, i));
      if (mq.matches || !pinned) { cur = target = i; draw(i); return; }
      window.scrollTo({ top: trackTop() + i * unit() + 2, behavior: reduce ? 'auto' : 'smooth' });
    }
    function applyMode() {
      var discrete = mq.matches || !pinned;
      wheel.classList.toggle('is-scroll', !discrete);
      panel.classList.toggle('is-discrete', discrete);
      section.classList.toggle('services--flat', !pinned);
      cur = target = discrete ? Math.max(0, active) : scrollPos();
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
  };

  /* ---------------------------------------------- work (filter chips) */
  C.work = function (section) {
    var tags = section.querySelector('.work__tags');
    var grid = section.querySelector('.work__grid');
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
        var show = !current || (c.dataset.cat || '').split(' ').indexOf(current) > -1;
        c.hidden = !show;
        c.classList.add('is-visible');
        c.classList.remove('is-shown');
        if (show) { void c.offsetWidth; c.classList.add('is-shown'); }
      });
    });
  };

  /* ----------------------------------------------------- testimonials */
  C.testi = function (section) {
    var stage = section.querySelector('.testi__stage');
    if (!stage) return;
    var slides = Array.prototype.slice.call(stage.querySelectorAll('.tslide'));
    var dotsWrap = stage.querySelector('.testi__dots');
    var dots = dotsWrap ? Array.prototype.slice.call(dotsWrap.querySelectorAll('button')) : [];
    var N = slides.length, active = 0, timer = null, paused = false, visible = true;
    if (!N) return;
    function render(next) {
      slides.forEach(function (el, i) { el.classList.toggle('is-active', i === next); el.classList.toggle('is-before', i < next); });
      dots.forEach(function (d, i) { d.classList.toggle('is-active', i === next); d.setAttribute('aria-selected', String(i === next)); });
      active = next;
    }
    function go(i) { render(((i % N) + N) % N); restart(); }
    function restart() {
      clearInterval(timer);
      if (reduce || editing) return;
      timer = setInterval(function () { if (!paused && visible) render((active + 1) % N); }, 7000);
    }
    var up = stage.querySelector('.testi__arrow--up'), down = stage.querySelector('.testi__arrow--down');
    if (up) up.addEventListener('click', function () { go(active - 1); });
    if (down) down.addEventListener('click', function () { go(active + 1); });
    dots.forEach(function (d, i) { d.addEventListener('click', function () { go(i); }); });
    stage.addEventListener('pointerenter', function () { paused = true; });
    stage.addEventListener('pointerleave', function () { paused = false; });
    window.QT.whenVisible(stage, function (v) { visible = v; });
    render(0); restart();
  };

  /* ----------------------------------------- work archive (AJAX paging) */
  C.archive = function (root) {
    var grid = root.querySelector('.archive__grid');
    var pagerWrap = root.querySelector('.archive__pager');
    var empty = root.querySelector('.archive__empty');
    var tagsWrap = document.querySelector('[data-qb="archive-filter"]') || root.querySelector('.work__tags');
    if (!grid) return;
    var state = { cat: '', page: parseInt(root.dataset.page || '1', 10) || 1 };
    var timer = null, busy = false;

    function load(cat, page, scroll) {
      if (busy || !data.ajaxUrl) return;
      busy = true;
      grid.classList.add('is-swapping');
      var body = new URLSearchParams({ action: 'qb_archive', nonce: data.nonce || '', cat: cat, page: page, per: root.dataset.per || '12', link: root.dataset.link || 'View Project', fallback: root.dataset.fallback || '', arrow: root.dataset.arrow || '', empty: root.dataset.empty || '' });
      fetch(data.ajaxUrl, { method: 'POST', body: body, credentials: 'same-origin' })
        .then(function (r) { return r.json(); })
        .then(function (res) {
          if (!res || !res.success) throw new Error('bad response');
          clearTimeout(timer);
          timer = setTimeout(function () {
            grid.innerHTML = res.data.cards;
            if (pagerWrap) pagerWrap.innerHTML = res.data.pager;
            if (empty) empty.hidden = !!res.data.count;
            state.cat = cat; state.page = res.data.page;
            grid.classList.remove('is-swapping');
            window.QB.scan(grid);
            busy = false;
          }, 200);
          if (scroll) {
            var top = grid.getBoundingClientRect().top + window.scrollY - 140;
            if (window.scrollY > top) window.scrollTo({ top: top, behavior: reduce ? 'auto' : 'smooth' });
          }
        })
        .catch(function () { grid.classList.remove('is-swapping'); busy = false; });
    }

    if (pagerWrap) {
      pagerWrap.addEventListener('click', function (e) {
        var b = e.target.closest('button[data-page]');
        if (!b || b.disabled) return;
        e.preventDefault();
        load(state.cat, parseInt(b.dataset.page, 10), true);
      });
    }
    if (tagsWrap) {
      tagsWrap.addEventListener('click', function (e) {
        var btn = e.target.closest('button[data-filter]');
        if (!btn) return;
        var f = btn.dataset.filter;
        var next = state.cat === f ? '' : f;
        tagsWrap.querySelectorAll('button').forEach(function (b) { b.classList.toggle('is-active', b.dataset.filter === next && next !== ''); });
        load(next, 1, false);
      });
    }
  };

  /* ------------------------------------------------ project hero tilt */
  C.project = function (hero) {
    if (reduce) return;
    hero.addEventListener('pointermove', function (e) {
      if (e.pointerType !== 'mouse') return;
      var r = hero.getBoundingClientRect();
      var x = ((e.clientX - r.left) / r.width) * 2 - 1, y = ((e.clientY - r.top) / r.height) * 2 - 1;
      hero.querySelectorAll('.device').forEach(function (d) { d.style.setProperty('--tx', x.toFixed(3)); d.style.setProperty('--ty', y.toFixed(3)); });
    });
    hero.addEventListener('pointerleave', function () {
      hero.querySelectorAll('.device').forEach(function (d) { d.style.setProperty('--tx', 0); d.style.setProperty('--ty', 0); });
    });
  };

  /* ------------------------------------------- service rows (hover img) */
  C.rows = function (list) {
    var rows = Array.prototype.slice.call(list.querySelectorAll('.sd-row'));
    function activate(row) { rows.forEach(function (r) { r.classList.toggle('is-active', r === row); }); }
    rows.forEach(function (row) {
      row.setAttribute('tabindex', '0');
      row.addEventListener('pointerenter', function (e) { if (e.pointerType === 'mouse') activate(row); });
      row.addEventListener('focus', function () { activate(row); });
      row.addEventListener('click', function () { activate(row); });
    });
  };

  /* ----------------------------------------------------- book a call */
  C.book = function (section) {
    var form = section.querySelector('.book-form');
    var success = section.querySelector('.book-success');
    var pill = section.querySelector('.book-pill');
    if (!form) return;
    var msgs = {
      first: form.dataset.msgFirst || 'Please enter your first name.',
      last: form.dataset.msgLast || 'Please enter your last name.',
      email: form.dataset.msgEmail || 'Please enter your email address.',
      emailBad: form.dataset.msgEmailBad || 'That email address does not look right.',
      phone: form.dataset.msgPhone || 'Use digits only, for example +20 101 280 4721.',
      fail: form.dataset.msgFail || 'Sorry, something went wrong. Please try again.'
    };
    var rules = {
      firstName: function (v) { return v.trim() ? '' : msgs.first; },
      lastName: function (v) { return v.trim() ? '' : msgs.last; },
      email: function (v) {
        if (!v.trim()) return msgs.email;
        return /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(v.trim()) ? '' : msgs.emailBad;
      },
      phone: function (v) { if (!v.trim()) return ''; return /^[+\d][\d\s()\-]{6,}$/.test(v.trim()) ? '' : msgs.phone; }
    };
    function check(input, showEmpty) {
      var rule = rules[input.name];
      if (!rule) return true;
      var msg = rule(input.value);
      var box = input.closest('.field');
      var err = box.querySelector('.field__error');
      var bad = !!msg && (showEmpty || input.value.trim() !== '');
      box.classList.toggle('is-invalid', bad);
      input.setAttribute('aria-invalid', String(bad));
      if (err) err.textContent = bad ? msg : '';
      return !msg;
    }
    form.querySelectorAll('input').forEach(function (input) {
      input.addEventListener('blur', function () { check(input, input.required); });
      input.addEventListener('input', function () { if (input.closest('.field').classList.contains('is-invalid')) check(input, true); });
    });
    var ta = form.querySelector('textarea');
    function grow() { ta.style.height = 'auto'; ta.style.height = Math.max(64, ta.scrollHeight) + 'px'; }
    if (ta) ta.addEventListener('input', grow);

    function showSuccess() {
      form.classList.remove('is-sending');
      form.hidden = true;
      if (pill) pill.hidden = true;
      success.hidden = false;
      success.focus({ preventScroll: true });
      success.scrollIntoView({ behavior: reduce ? 'auto' : 'smooth', block: 'center' });
    }
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      var firstBad = null;
      form.querySelectorAll('input').forEach(function (input) { if (!check(input, true) && !firstBad) firstBad = input; });
      if (firstBad) { firstBad.focus(); return; }
      form.classList.add('is-sending');
      var fd = new FormData(form);
      fd.append('action', 'qb_book_call');
      fd.append('nonce', data.nonce || '');
      fetch(data.ajaxUrl, { method: 'POST', body: fd, credentials: 'same-origin' })
        .then(function (r) { return r.json(); })
        .then(function (res) {
          if (res && res.success) { showSuccess(); } else { throw new Error((res && res.data && res.data.message) || msgs.fail); }
        })
        .catch(function (err) {
          form.classList.remove('is-sending');
          var box = form.querySelector('.book-form__error');
          if (box) { box.textContent = err.message || msgs.fail; box.hidden = false; }
        });
    });
  };
})();
