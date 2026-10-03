/* ==========================================================================
   components.js — one template for the site header and footer.
   Put <div data-include="header"></div> / <div data-include="footer"></div>
   in a page and set <body data-page="home|about|work|book"> so the right
   nav link is highlighted. Plain JS strings (no fetch), so pages still work
   when opened by double-click (file://). Must load BEFORE main.js.
   ========================================================================== */
(function () {
  'use strict';
  var page = document.body.getAttribute('data-page') || '';
  function cur(p) { return page === p ? ' aria-current="page"' : ''; }

  var header = [
    '<header class="site-header" id="siteHeader">',
    '  <div class="site-header__inner">',
    '    <a class="site-logo" href="index.html" aria-label="Qeematech home"><span class="site-logo__mark"></span></a>',
    '    <nav class="site-nav" id="siteNav" aria-label="Main">',
    '      <a href="index.html"' + cur('home') + '>Home</a>',
    '      <a href="about.html"' + cur('about') + '>About Us</a>',
    '      <a href="index.html#services">Services</a>',
    '      <a href="our-work.html"' + cur('work') + '>Our Work</a>',
    '    </nav>',
    '    <a class="btn site-header__cta" href="book-a-call.html">Book a call</a>',
    '    <button class="site-header__toggle" id="navToggle" aria-label="Toggle menu" aria-expanded="false" aria-controls="siteNav"><span></span></button>',
    '  </div>',
    '</header>',
    '<div class="scroll-progress" aria-hidden="true"></div>'
  ].join('\n');

  var footer = [
    '<footer class="site-footer">',
    '  <img class="site-footer__glow" src="assets/svg/ellipse.svg" alt="" aria-hidden="true">',
    '  <div class="site-footer__inner">',
    '    <div class="site-footer__top">',
    '      <p class="site-footer__headline" data-reveal><span>Let’s Work</span><span>Together</span></p>',
    '      <div class="site-footer__intro" data-reveal style="--d:.1s">',
    '        <p>Building digital products, platforms, and experiences that help businesses move forward.<br>Our company is one of the leading firms in website development and design in Egypt and the Arab world.</p>',
    '        <a class="btn btn--light" href="book-a-call.html">Book a call</a>',
    '      </div>',
    '    </div>',
    '    <div class="site-footer__cols">',
    '      <div class="site-footer__brand">',
    '        <div class="site-footer__logo" role="img" aria-label="Qeematech"></div>',
    '        <div class="site-footer__social">',
    '          <a href="#" aria-label="Facebook"><img src="assets/svg/facebook.svg" alt=""></a>',
    '          <a href="#" aria-label="Instagram"><img src="assets/svg/instagram.svg" alt=""></a>',
    '          <a href="#" aria-label="Behance"><img src="assets/svg/behance.svg" alt=""></a>',
    '          <a href="#" aria-label="WhatsApp"><img src="assets/svg/whatsapp.svg" alt=""></a>',
    '        </div>',
    '      </div>',
    '      <div class="site-footer__col">',
    '        <h3>Important Links</h3>',
    '        <ul><li><a href="index.html">Home</a></li><li><a href="about.html">About Us</a></li><li><a href="index.html#services">Our Services</a></li><li><a href="book-a-call.html">Contact Us</a></li></ul>',
    '      </div>',
    '      <div class="site-footer__col site-footer__col--addr">',
    '        <h3>Address</h3>',
    '        <ul><li>3 Makram Ebeid, Nasr City, Cairo</li><li>59 Iran Street, off Mohi El Din Abu El Ezz, Dokki, Giza</li><li>51 Al Nahda Street, Maadi, Cairo</li></ul>',
    '      </div>',
    '      <div class="site-footer__col">',
    '        <h3>Call Us</h3>',
    '        <ul><li><a href="tel:01012804721">01012804721</a></li><li><a href="mailto:Sales@qeematech.net">Sales@qeematech.net</a></li><li><a href="mailto:Support@qeematech.net">Support@qeematech.net</a></li></ul>',
    '      </div>',
    '    </div>',
    '  </div>',
    '</footer>'
  ].join('\n');

  var templates = { header: header, footer: footer };
  document.querySelectorAll('[data-include]').forEach(function (el) {
    var html = templates[el.getAttribute('data-include')];
    if (html) el.outerHTML = html;
  });
})();
