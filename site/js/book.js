/* ==========================================================================
   book.js — Book a call form: validation, auto-growing textarea, success state.
   Front-end only. Replace the fake send below with a real request / WordPress
   form plugin during conversion.
   ========================================================================== */
(function () {
  'use strict';
  var form = document.getElementById('bookForm');
  var success = document.getElementById('bookSuccess');
  var pill = document.getElementById('touchTitle');
  if (!form) return;

  var rules = {
    firstName: function (v) { return v.trim() ? '' : 'Please enter your first name.'; },
    lastName: function (v) { return v.trim() ? '' : 'Please enter your last name.'; },
    email: function (v) {
      if (!v.trim()) return 'Please enter your email address.';
      return /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(v.trim()) ? '' : 'That email address does not look right.';
    },
    phone: function (v) {
      if (!v.trim()) return '';                                   // optional
      return /^[+\d][\d\s()\-]{6,}$/.test(v.trim()) ? '' : 'Use digits only, for example +20 101 280 4721.';
    }
  };

  function check(input, showEmpty) {
    var rule = rules[input.name];
    if (!rule) return true;
    var msg = rule(input.value);
    var box = input.closest('.field');
    var err = document.getElementById(input.id + '-err');
    var bad = !!msg && (showEmpty || input.value.trim() !== '');
    box.classList.toggle('is-invalid', bad);
    input.setAttribute('aria-invalid', String(bad));
    if (err) err.textContent = bad ? msg : '';
    return !msg;
  }

  form.querySelectorAll('input').forEach(function (input) {
    input.addEventListener('blur', function () { check(input, input.required); });
    input.addEventListener('input', function () {
      if (input.closest('.field').classList.contains('is-invalid')) check(input, true);
    });
  });

  // textarea grows with its content
  var ta = document.getElementById('message');
  function grow() { ta.style.height = 'auto'; ta.style.height = Math.max(64, ta.scrollHeight) + 'px'; }
  ta.addEventListener('input', grow);

  form.addEventListener('submit', function (e) {
    e.preventDefault();
    var firstBad = null;
    form.querySelectorAll('input').forEach(function (input) {
      if (!check(input, true) && !firstBad) firstBad = input;
    });
    if (firstBad) { firstBad.focus(); return; }

    form.classList.add('is-sending');
    setTimeout(function () {                                      // fake network delay
      form.classList.remove('is-sending');
      form.hidden = true;
      if (pill) pill.hidden = true;
      success.hidden = false;
      success.focus({ preventScroll: true });
      success.scrollIntoView({ behavior: window.QT && window.QT.reduce ? 'auto' : 'smooth', block: 'center' });
    }, 900);
  });
})();
