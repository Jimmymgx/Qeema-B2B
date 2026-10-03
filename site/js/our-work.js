/* ==========================================================================
   our-work.js — project archive: 12 cards per page, filter chips, pagination.
   Project data below is placeholder (same text as the Figma). Replace with real
   projects (or WordPress query results) later.
   ========================================================================== */
(function () {
  'use strict';
  var PER_PAGE = 12;
  var TOTAL = 60;                       // placeholder: 5 pages of 12
  var WINDOW = 4;                       // numbered buttons shown at once
  var IMAGES = ['assets/img/project-screenshot.webp', 'assets/img/project-screenshot1.webp', 'assets/img/project-screenshot2.webp'];
  var CATS = [['corporate', 'travel'], ['mobile', 'custom'], ['ecommerce', 'corporate'], ['education', 'travel'], ['custom', 'education'], ['mobile', 'ecommerce']];
  var ARROW = 'assets/svg/arrow-right.svg';

  var items = [];
  for (var i = 0; i < TOTAL; i++) {
    items.push({
      title: 'Rivermark',
      text: 'High-Performance Websites And Platforms Built To Deliver Seamless Digital Experiences.',
      img: IMAGES[(i + Math.floor(i / PER_PAGE)) % IMAGES.length],   // shifts per page so each page looks different
      cats: CATS[i % CATS.length],
      href: i % 2 ? 'project.html?device=laptop' : 'project.html'   // demo: alternate the two project layouts
    });
  }

  var grid = document.getElementById('workGrid');
  var empty = document.getElementById('workEmpty');
  var tags = document.getElementById('workTags');
  var pager = document.getElementById('pager');
  var list = document.getElementById('pgList');
  var prev = document.getElementById('pgPrev');
  var next = document.getElementById('pgNext');
  var filter = null, page = 1, swapTimer = null;

  function filtered() {
    return filter ? items.filter(function (p) { return p.cats.indexOf(filter) > -1; }) : items;
  }
  function pageCount() { return Math.max(1, Math.ceil(filtered().length / PER_PAGE)); }

  function cardHTML(p, n) {
    return '<article class="pcard" data-reveal style="--d:' + ((n % 3) * 0.1).toFixed(1) + 's">' +
      '<a class="pcard__shot" href="' + p.href + '"><img src="' + p.img + '" alt="" width="960" height="701" decoding="async" ' + (n < 3 ? 'fetchpriority="high"' : 'loading="lazy"') + '></a>' +
      '<h3>' + p.title + '</h3><p>' + p.text + '</p>' +
      '<a class="pcard__link" href="' + p.href + '"><span>View Nominees</span><img src="' + ARROW + '" alt=""></a>' +
      '</article>';
  }

  function renderCards() {
    var all = filtered();
    var slice = all.slice((page - 1) * PER_PAGE, page * PER_PAGE);
    grid.innerHTML = slice.map(cardHTML).join('');
    empty.hidden = slice.length > 0;
    if (window.QT) window.QT.reveal(grid.children);
  }

  function renderPager() {
    var P = pageCount();
    pager.hidden = P <= 1;
    var start = Math.min(Math.max(page - 1, 1), Math.max(1, P - WINDOW + 1));
    var end = Math.min(P, start + WINDOW - 1);
    var html = '';
    for (var n = start; n <= end; n++) {
      html += '<li><button type="button" data-page="' + n + '"' + (n === page ? ' aria-current="page"' : '') + ' aria-label="Page ' + n + '">' + n + '</button></li>';
    }
    if (end < P) html += '<li><button type="button" data-page="' + (end + 1) + '" aria-label="More pages">…</button></li>';
    list.innerHTML = html;
    prev.disabled = page <= 1;
    next.disabled = page >= P;
  }

  function go(n, scroll) {
    var P = pageCount();
    n = Math.max(1, Math.min(P, n));
    if (n === page && grid.children.length) return;
    clearTimeout(swapTimer);
    grid.classList.add('is-swapping');
    swapTimer = setTimeout(function () {
      page = n;
      renderCards();
      renderPager();
      grid.classList.remove('is-swapping');
    }, grid.children.length ? 220 : 0);
    if (scroll) {
      var top = grid.getBoundingClientRect().top + window.scrollY - 140;
      if (window.scrollY > top) window.scrollTo({ top: top, behavior: 'smooth' });
    }
  }

  list.addEventListener('click', function (e) {
    var b = e.target.closest('button[data-page]');
    if (b) go(parseInt(b.dataset.page, 10), true);
  });
  prev.addEventListener('click', function () { go(page - 1, true); });
  next.addEventListener('click', function () { go(page + 1, true); });

  tags.addEventListener('click', function (e) {
    var btn = e.target.closest('button[data-filter]');
    if (!btn) return;
    var f = btn.dataset.filter;
    filter = filter === f ? null : f;
    tags.querySelectorAll('button').forEach(function (b) { b.classList.toggle('is-active', b.dataset.filter === filter); });
    page = 0;                             // force a re-render on page 1
    go(1, false);
  });

  page = 0;
  go(1, false);
})();
