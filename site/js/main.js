/* ==========================================================================
   main.js — behaviour shared by every page:
   header (scrolled / hide-on-scroll-down / mobile menu), scroll reveal,
   word-by-word title reveal, card tilt, pausing of off-screen animations,
   and the 1440px canvas scale (--hs).
   Everything animates transform / opacity only, and runs on passive listeners
   or IntersectionObserver so scrolling stays smooth.
   ========================================================================== */
(function () {
  'use strict';
  var root = document.documentElement;
  var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var finePointer = window.matchMedia('(hover: hover) and (pointer: fine)').matches;

  /* ---- Canvas scale: designs are 1440 wide; scale down between 900 and 1440 ---- */
  var lastVw = 0;
  function setScale() {
    var vw = root.clientWidth;
    if (vw === lastVw) return;                       // ignore height-only resizes (mobile address bar)
    lastVw = vw;
    root.style.setProperty('--hs', (vw >= 900 ? Math.min(1, vw / 1440) : 1).toFixed(4));
  }
  setScale();
  window.addEventListener('resize', setScale, { passive: true });

  /* ---- Header: scrolled state, hide on scroll down / show on scroll up, mobile menu ---- */
  var header = document.getElementById('siteHeader');
  var toggle = document.getElementById('navToggle');
  var lastY = window.scrollY, ticking = false;
  function onScroll() {
    if (ticking) return;
    ticking = true;
    requestAnimationFrame(function () {
      var y = window.scrollY;
      if (header) {
        header.classList.toggle('is-scrolled', y > 24);
        var goingDown = y > lastY + 4, goingUp = y < lastY - 4;
        if (header.classList.contains('is-open') || y < 240) header.classList.remove('is-hidden');
        else if (goingDown) header.classList.add('is-hidden');
        else if (goingUp) header.classList.remove('is-hidden');
      }
      lastY = y;
      ticking = false;
    });
  }
  onScroll();
  window.addEventListener('scroll', onScroll, { passive: true });

  if (header && toggle) {
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
    window.addEventListener('resize', function () {
      if (root.clientWidth > 900) header.classList.remove('is-open');
    }, { passive: true });
    // keyboard users: never leave the header hidden while it has focus
    header.addEventListener('focusin', function () { header.classList.remove('is-hidden'); });
  }

  /* ---- Scroll reveal ---- */
  var items = document.querySelectorAll('[data-reveal]');
  var io = null;
  if (!('IntersectionObserver' in window) || reduce) {
    items.forEach(function (el) { el.classList.add('is-visible'); });
  } else {
    io = new IntersectionObserver(function (entries) {
      entries.forEach(function (e) {
        if (e.isIntersecting) {
          e.target.classList.add('is-visible');
          io.unobserve(e.target);
        }
      });
    }, { threshold: 0.12, rootMargin: '0px 0px -6% 0px' });
    items.forEach(function (el) { io.observe(el); });
  }

  /* ---- Word-by-word title reveal (keeps <strong> etc., skips titles that already animate) ---- */
  function splitWords(el) {
    var n = 0;
    (function walk(node) {
      Array.prototype.slice.call(node.childNodes).forEach(function (child) {
        if (child.nodeType === 3) {
          var parts = child.nodeValue.split(/(\s+)/);
          var frag = document.createDocumentFragment();
          parts.forEach(function (p) {
            if (!p) return;
            if (/^\s+$/.test(p)) { frag.appendChild(document.createTextNode(' ')); return; }
            var w = document.createElement('span'); w.className = 'w';
            var inner = document.createElement('span'); inner.textContent = p;
            inner.style.setProperty('--i', n++);
            w.appendChild(inner); frag.appendChild(w);
          });
          node.replaceChild(frag, child);
        } else if (child.nodeType === 1 && child.tagName !== 'BR') {
          walk(child);
        }
      });
    })(el);
    el.classList.add('split');
  }
  if (!reduce && io) {
    var titles = document.querySelectorAll('.title');
    var titleIO = new IntersectionObserver(function (entries) {
      entries.forEach(function (e) {
        if (e.isIntersecting) { e.target.classList.add('is-in'); titleIO.unobserve(e.target); }
      });
    }, { threshold: 0.35 });
    titles.forEach(function (t) {
      if (t.closest('.rise-in') || t.classList.contains('rise-in') || t.querySelector('.w')) return;
      splitWords(t);
      titleIO.observe(t);
    });
    // safety net: a title that is already on screen must never stay hidden
    setTimeout(function () {
      document.querySelectorAll('.split:not(.is-in)').forEach(function (t) {
        if (t.getBoundingClientRect().top < window.innerHeight) t.classList.add('is-in');
      });
    }, 3500);
  }

  /* ---- Pause infinite animations while their section is off-screen (saves CPU/GPU) ---- */
  if ('IntersectionObserver' in window) {
    var pauseIO = new IntersectionObserver(function (entries) {
      entries.forEach(function (e) { e.target.classList.toggle('is-paused', !e.isIntersecting); });
    }, { rootMargin: '120px 0px' });
    document.querySelectorAll('.hero, .clients, .testi, .story, .proj-hero, .about-hero, .inner-glow, .services__panel, .page-head, .site-footer')
      .forEach(function (s) { pauseIO.observe(s); });
  }

  /* ---- Project-card tilt (mouse only; transform only) ---- */
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

  /* ---- Tiny shared helper for other scripts ---- */
  window.QT = {
    reduce: reduce,
    /** reveal elements that were added after page load (e.g. rendered cards) */
    reveal: function (els) {
      Array.prototype.forEach.call(els, function (el) {
        if (io) io.observe(el); else el.classList.add('is-visible');
      });
    },
    /** run fn only while el is on screen (used for autoplay) */
    whenVisible: function (el, onChange) {
      if (!('IntersectionObserver' in window)) { onChange(true); return; }
      new IntersectionObserver(function (es) { onChange(es[0].isIntersecting); }, { threshold: 0.25 }).observe(el);
    }
  };
})();
