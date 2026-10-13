/* ==========================================================================
   qb-main.js — behaviour shared by every page (ported from the static main.js):
   header (scrolled / hide-on-scroll / mobile menu), scroll reveal, word-by-word
   title reveal, card tilt, pausing of off-screen animations and the 1440px
   canvas scale (--hs).

   WordPress / Elementor changes:
   - QB.scan(root) can be called again for any DOM that is (re)rendered, which is
     what happens every time a widget is edited in the Elementor canvas.
   - In the Elementor editor the reveal "pre-hidden" state and the title word
     splitting are skipped, so every text stays visible and click-to-edit works.
   ========================================================================== */
(function () {
  'use strict';
  var root = document.documentElement;
  var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var finePointer = window.matchMedia('(hover: hover) and (pointer: fine)').matches;
  var editing = document.body.classList.contains('elementor-editor-active') || /[?&]elementor-preview=/.test(location.search);
  if (editing) { root.classList.add('qb-editing'); }

  /* ---- Canvas scale: designs are 1440 wide; scale down between 900 and 1440 ---- */
  var lastVw = 0;
  function setScale() {
    var vw = root.clientWidth;
    if (vw === lastVw) return;
    lastVw = vw;
    root.style.setProperty('--hs', (vw >= 900 ? Math.min(1, vw / 1440) : 1).toFixed(4));
  }
  setScale();
  window.addEventListener('resize', setScale, { passive: true });

  /* ---- Header: scrolled state, hide on scroll down / show on scroll up ---- */
  var lastY = window.scrollY, ticking = false;
  function onScroll() {
    if (ticking) return;
    ticking = true;
    requestAnimationFrame(function () {
      var header = document.getElementById('siteHeader');
      var y = window.scrollY;
      if (header) {
        header.classList.toggle('is-scrolled', y > 24);
        var goingDown = y > lastY + 4, goingUp = y < lastY - 4;
        if (header.classList.contains('is-open') || y < 240 || editing) header.classList.remove('is-hidden');
        else if (goingDown) header.classList.add('is-hidden');
        else if (goingUp) header.classList.remove('is-hidden');
      }
      lastY = y;
      ticking = false;
    });
  }
  onScroll();
  window.addEventListener('scroll', onScroll, { passive: true });

  function initHeader(scope) {
    var header = scope.querySelector ? scope.querySelector('#siteHeader') : null;
    if (!header || header.dataset.qbInit) return;
    header.dataset.qbInit = '1';
    var toggle = header.querySelector('#navToggle');
    if (toggle) {
      toggle.addEventListener('click', function () {
        var open = header.classList.toggle('is-open');
        toggle.setAttribute('aria-expanded', String(open));
      });
      header.querySelectorAll('.site-nav a').forEach(function (a) {
        a.addEventListener('click', function () {
          header.classList.remove('is-open');
          toggle.setAttribute('aria-expanded', 'false');
        });
      });
      window.addEventListener('resize', function () { if (root.clientWidth > 900) header.classList.remove('is-open'); }, { passive: true });
    }
    header.addEventListener('focusin', function () { header.classList.remove('is-hidden'); });
    onScroll();
  }

  /* ---- Scroll reveal ---- */
  var io = null;
  if ('IntersectionObserver' in window && !reduce && !editing) {
    io = new IntersectionObserver(function (entries) {
      entries.forEach(function (e) {
        if (e.isIntersecting) { e.target.classList.add('is-visible'); io.unobserve(e.target); }
      });
    }, { threshold: 0.12, rootMargin: '0px 0px -6% 0px' });
  }
  function reveal(scope) {
    scope.querySelectorAll('[data-reveal]:not(.is-visible)').forEach(function (el) {
      if (io) { if (!el.dataset.qbObs) { el.dataset.qbObs = '1'; io.observe(el); } } else el.classList.add('is-visible');
    });
    if (scope.matches && scope.matches('[data-reveal]') && !io) scope.classList.add('is-visible');
  }

  /* ---- Word-by-word title reveal (skips titles that already animate / while editing) ---- */
  function splitWords(el) {
    var n = 0;
    (function walk(node) {
      Array.prototype.slice.call(node.childNodes).forEach(function (child) {
        if (child.nodeType === 3) {
          var parts = child.nodeValue.split(/(\s+)/), frag = document.createDocumentFragment();
          parts.forEach(function (p) {
            if (!p) return;
            if (/^\s+$/.test(p)) { frag.appendChild(document.createTextNode(' ')); return; }
            var w = document.createElement('span'); w.className = 'w';
            var inner = document.createElement('span'); inner.textContent = p;
            inner.style.setProperty('--i', n++);
            w.appendChild(inner); frag.appendChild(w);
          });
          node.replaceChild(frag, child);
        } else if (child.nodeType === 1 && child.tagName !== 'BR') walk(child);
      });
    })(el);
    el.classList.add('split');
  }
  var titleIO = null;
  if ('IntersectionObserver' in window && !reduce && !editing) {
    titleIO = new IntersectionObserver(function (entries) {
      entries.forEach(function (e) { if (e.isIntersecting) { e.target.classList.add('is-in'); titleIO.unobserve(e.target); } });
    }, { threshold: 0.35 });
  }
  function titles(scope) {
    if (!titleIO) return;
    scope.querySelectorAll('.title').forEach(function (t) {
      if (t.dataset.qbSplit || t.closest('.rise-in') || t.classList.contains('rise-in') || t.querySelector('.w')) return;
      t.dataset.qbSplit = '1';
      splitWords(t);
      titleIO.observe(t);
    });
  }
  if (titleIO) {
    setTimeout(function () {       // safety net: a title already on screen must never stay hidden
      document.querySelectorAll('.split:not(.is-in)').forEach(function (t) {
        if (t.getBoundingClientRect().top < window.innerHeight) t.classList.add('is-in');
      });
    }, 3500);
  }

  /* ---- Pause infinite animations while their section is off-screen ---- */
  var pauseIO = null;
  if ('IntersectionObserver' in window) {
    pauseIO = new IntersectionObserver(function (entries) {
      entries.forEach(function (e) { e.target.classList.toggle('is-paused', !e.isIntersecting); });
    }, { rootMargin: '120px 0px' });
  }
  function pauses(scope) {
    if (!pauseIO) return;
    scope.querySelectorAll('.hero, .clients, .testi, .story, .proj-hero, .about-hero, .inner-glow, .services__panel, .page-head, .sd-hero, .svc-hero, .site-footer').forEach(function (s) {
      if (!s.dataset.qbPause) { s.dataset.qbPause = '1'; pauseIO.observe(s); }
    });
  }

  /* ---- Project-card tilt (mouse only) ---- */
  if (finePointer && !reduce) {
    var tiltEl = null;
    document.addEventListener('pointermove', function (e) {
      if (e.pointerType !== 'mouse') return;
      var shot = e.target.closest && e.target.closest('.pcard__shot');
      if (tiltEl && tiltEl !== shot) { tiltEl.style.setProperty('--rx', '0deg'); tiltEl.style.setProperty('--ry', '0deg'); }
      tiltEl = shot;
      if (!shot) return;
      var r = shot.getBoundingClientRect();
      var x = (e.clientX - r.left) / r.width - 0.5, y = (e.clientY - r.top) / r.height - 0.5;
      shot.style.setProperty('--ry', (x * 7).toFixed(2) + 'deg');
      shot.style.setProperty('--rx', (-y * 5).toFixed(2) + 'deg');
    }, { passive: true });
  }

  /* ---- Public API ---- */
  var QB = window.QB = window.QB || {};
  QB.components = QB.components || {};
  QB.reduce = reduce;
  QB.editing = editing;
  QB.scan = function (scope) {
    scope = scope || document;
    initHeader(scope.querySelector ? scope : document);
    reveal(scope);
    titles(scope);
    pauses(scope);
    // component initialisers live in qb-components.js, keyed by data-qb="name"
    var nodes = scope.querySelectorAll ? Array.prototype.slice.call(scope.querySelectorAll('[data-qb]')) : [];
    if (scope.matches && scope.matches('[data-qb]')) nodes.unshift(scope);
    nodes.forEach(function (node) {
      var name = node.getAttribute('data-qb');
      if (node.dataset.qbReady || !QB.components[name]) return;
      node.dataset.qbReady = '1';
      QB.components[name](node);
    });
  };
  // shared helpers (same names as the static site, so ported code keeps working)
  window.QT = {
    reduce: reduce,
    reveal: function (els) { Array.prototype.forEach.call(els, function (el) { if (io) { io.observe(el); } else el.classList.add('is-visible'); }); },
    whenVisible: function (el, onChange) {
      if (!('IntersectionObserver' in window)) { onChange(true); return; }
      new IntersectionObserver(function (es) { onChange(es[0].isIntersecting); }, { threshold: 0.25 }).observe(el);
    }
  };

  function boot() { QB.scan(document); }
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', boot); else boot();
  // components script loads after this one: run once more when everything is ready
  window.addEventListener('load', function () { QB.scan(document); });

  // Elementor: every time a widget is (re)rendered in the canvas, set it up again
  window.addEventListener('elementor/frontend/init', function () {
    if (window.elementorFrontend && window.elementorFrontend.hooks) {
      window.elementorFrontend.hooks.addAction('frontend/element_ready/global', function ($el) { QB.scan($el[0]); });
    }
  });
})();
