(function () {
  'use strict';
  var QB = window.QB = window.QB || {};
  QB.components = QB.components || {};

  QB.components.sculpt = function (host) {
  if (!window.THREE || QB.editing) return;              // the editor canvas keeps the static image
  var THREE = window.THREE;
  var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  var renderer;
  try {
    renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true, powerPreference: 'high-performance' });
  } catch (e) { return; }
  renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, 1.75));
  renderer.outputEncoding = THREE.sRGBEncoding;
  renderer.toneMapping = THREE.ACESFilmicToneMapping;
  renderer.toneMappingExposure = 1.0;
  var canvas = renderer.domElement;
  canvas.setAttribute('aria-hidden', 'true');
  host.appendChild(canvas);

  var scene = new THREE.Scene();
  var camera = new THREE.PerspectiveCamera(30, 1, 0.1, 60);
  var BASE_Z = 11.2;
  camera.position.set(0, 0.05, BASE_Z);

  /* ---- procedural studio environment: sky gradient, soft boxes and dark flags, for rich reflections ---- */
  (function () {
    var c = document.createElement('canvas'); c.width = 1024; c.height = 512;
    var g = c.getContext('2d');
    var grd = g.createLinearGradient(0, 0, 0, 512);
    grd.addColorStop(0, '#7f93b8'); grd.addColorStop(0.38, '#dbe5f7'); grd.addColorStop(0.5, '#7d8cab'); grd.addColorStop(0.78, '#303a57'); grd.addColorStop(1, '#161c2e');
    g.fillStyle = grd; g.fillRect(0, 0, 1024, 512);
    function box(x, y, w, h, a, rgb) {
      var gg = g.createLinearGradient(x, y, x, y + h);
      gg.addColorStop(0, 'rgba(' + rgb + ',' + a + ')'); gg.addColorStop(1, 'rgba(' + rgb + ',' + a * 0.55 + ')');
      g.fillStyle = gg; g.fillRect(x, y, w, h);
    }
    box(70, 70, 150, 120, 1, '255,255,255');
    box(560, 40, 260, 70, 1, '255,255,255');
    box(880, 140, 70, 190, 0.95, '235,243,255');
    box(330, 190, 90, 40, 0.8, '255,255,255');
    g.fillStyle = 'rgba(8,12,26,0.85)';
    g.fillRect(250, 110, 120, 110); g.fillRect(470, 220, 200, 60); g.fillRect(740, 90, 90, 140); g.fillRect(0, 230, 1024, 14);
    var tex = new THREE.CanvasTexture(c);
    tex.mapping = THREE.EquirectangularReflectionMapping;
    tex.encoding = THREE.sRGBEncoding;
    var pm = new THREE.PMREMGenerator(renderer);
    scene.environment = pm.fromEquirectangular(tex).texture;
    tex.dispose(); pm.dispose();
  })();

  var key = new THREE.DirectionalLight(0xffffff, 0.9); key.position.set(3.5, 5, 6); scene.add(key);
  var rim = new THREE.DirectionalLight(0x9bb6ff, 0.9); rim.position.set(-5, 1.5, -4); scene.add(rim);
  scene.add(new THREE.AmbientLight(0xffffff, 0.18));

  /* ---- brushed-metal streak bump: smooth 1-D noise across the tube, constant along the ring ---- */
  function brushedTexture() {
    var H = 512, c = document.createElement('canvas'); c.width = 4; c.height = H;
    var g = c.getContext('2d'), img = g.createImageData(4, H), d = img.data, y, x, i, v, rows = [], sm = [];
    for (y = 0; y < H; y++) rows.push(Math.random());
    for (y = 0; y < H; y++) sm.push((rows[y] + rows[(y + 1) % H] + rows[(y + 2) % H] + rows[(y + H - 1) % H]) / 4);
    for (y = 0; y < H; y++) {
      v = 128 + (sm[y] - 0.5) * 70;
      for (x = 0; x < 4; x++) { i = (y * 4 + x) * 4; d[i] = d[i + 1] = d[i + 2] = v; d[i + 3] = 255; }
    }
    g.putImageData(img, 0, 0);
    var t = new THREE.CanvasTexture(c);
    t.wrapS = t.wrapT = THREE.RepeatWrapping;
    t.anisotropy = Math.min(8, renderer.capabilities.getMaxAnisotropy());
    return t;
  }
  var brushed = brushedTexture();

  var silver = new THREE.MeshStandardMaterial({
    color: 0xffffff, vertexColors: true, metalness: 1, roughness: 0.3, envMapIntensity: 1.35,
    bumpMap: brushed, bumpScale: 0.05
  });
  var pearl = new THREE.MeshPhysicalMaterial({
    color: 0xf4f4ef, metalness: 0, roughness: 0.52, clearcoat: 0.45, clearcoatRoughness: 0.35, envMapIntensity: 0.6
  });

  /* torus with a blue glaze on its inner face (vertex colours blend silver -> blue) */
  function ringGeo(R, r, blueAmount) {
    var geo = new THREE.TorusGeometry(R, r, 72, 160);
    var pos = geo.attributes.position, n = pos.count, col = new Float32Array(n * 3), i, x, y, rad, cx, cy, dx, dy, inner, t;
    var blue = new THREE.Color(0x1237ff), white = new THREE.Color(0xdadade), tmp = new THREE.Color();
    for (i = 0; i < n; i++) {
      x = pos.getX(i); y = pos.getY(i);
      rad = Math.sqrt(x * x + y * y); cx = x / rad * R; cy = y / rad * R;
      dx = x - cx; dy = y - cy;
      inner = -(dx * x + dy * y) / (rad * r);              // 1 on the inside face, -1 on the outside
      t = Math.max(0, Math.min(1, (inner - (1 - blueAmount * 1.4)) / 0.35));
      t = t * t * (3 - 2 * t);
      tmp.copy(white).lerp(blue, blueAmount > 0 ? t : 0);
      col[i * 3] = tmp.r; col[i * 3 + 1] = tmp.g; col[i * 3 + 2] = tmp.b;
    }
    geo.setAttribute('color', new THREE.BufferAttribute(col, 3));
    return geo;
  }

  /* ---- three interlocked rings: position + Euler rotation (order ZYX) ---- */
  var R = 1, r = 0.3;
  var SPEC = [
    { p: [0, 0, 0], e: [0, 0, 0], mat: silver, blue: 0.5, from: [-3.2, -1.2, 0.5], spin: 0.9 },
    { p: [-0.663, 0.028, -0.004], e: [3.172, 2.345, 1.62], mat: silver, blue: 0.3, from: [-3.6, 1.4, -1.5], spin: -1.1 },
    { p: [0.375, 0.584, 0.168], e: [-0.122, 1.226, 2.27], mat: pearl, blue: 0, from: [3.4, 2.2, 1], spin: 1.0 }
  ];
  var cen = new THREE.Vector3();
  SPEC.forEach(function (s) { cen.add(new THREE.Vector3().fromArray(s.p)); });
  cen.multiplyScalar(1 / SPEC.length);

  var knot = new THREE.Group();
  scene.add(knot);
  var rings = SPEC.map(function (s) {
    var m = new THREE.Mesh(ringGeo(R, r, s.blue), s.mat);
    m.rotation.order = 'ZYX';
    m.rotation.set(s.e[0], s.e[1], s.e[2]);
    var pivot = new THREE.Group();
    pivot.add(m);
    knot.add(pivot);
    return { pivot: pivot, mesh: m, home: new THREE.Vector3().fromArray(s.p).sub(cen), from: new THREE.Vector3().fromArray(s.from), spin: s.spin, euler: s.e };
  });
  var SCALE = 1.28;
  knot.scale.setScalar(SCALE);

  /* ---- sizing ---- */
  function resize() {
    var w = host.clientWidth || 600, h = host.clientHeight || 510;
    renderer.setSize(w, h, false);
    camera.aspect = w / h;
    camera.position.z = BASE_Z * Math.max(1, (560 / Math.max(w, 1)) * 0.6 + 0.4);
    camera.updateProjectionMatrix();
  }
  resize();
  if ('ResizeObserver' in window) new ResizeObserver(resize).observe(host); else window.addEventListener('resize', resize, { passive: true });

  /* ---- input: spring-damped pointer + scroll parallax ---- */
  var tx = 0, ty = 0, px = 0, py = 0, vx = 0, vy = 0, scrollBias = 0;
  if (window.matchMedia('(hover: hover) and (pointer: fine)').matches) {
    window.addEventListener('pointermove', function (e) {
      tx = (e.clientX / window.innerWidth - 0.5) * 2;
      ty = (e.clientY / window.innerHeight - 0.5) * 2;
    }, { passive: true });
  }
  /* drag to rotate: pointer capture, inertia on release, then eases back to the idle sway */
  var dragYaw = 0, dragPitch = 0, velYaw = 0, velPitch = 0, dragging = false, lastX = 0, lastY = 0, lastMove = 0, releasedAt = 0, hinted = false;
  var hint = document.createElement('span');
  hint.className = 'sculpt-hint'; hint.setAttribute('aria-hidden', 'true'); hint.textContent = host.getAttribute('data-hint') || 'Drag to rotate';
  host.appendChild(hint);
  canvas.style.touchAction = 'pan-y';            // vertical swipes still scroll the page on touch screens
  canvas.style.cursor = 'grab';
  function wrap(a) { var T = Math.PI * 2; a = a % T; if (a > Math.PI) a -= T; if (a < -Math.PI) a += T; return a; }
  canvas.addEventListener('pointerdown', function (e) {
    dragging = true; lastX = e.clientX; lastY = e.clientY; lastMove = performance.now(); velYaw = velPitch = 0;
    try { canvas.setPointerCapture(e.pointerId); } catch (err) {}
    canvas.style.cursor = 'grabbing';
    if (!hinted) { hinted = true; hint.classList.add('is-gone'); }
  });
  canvas.addEventListener('pointermove', function (e) {
    if (!dragging) return;
    var now = performance.now(), dt = Math.max((now - lastMove) / 1000, 0.001);
    var dyaw = (e.clientX - lastX) * 0.009, dpitch = (e.clientY - lastY) * 0.007;
    dragYaw += dyaw; dragPitch = Math.max(-1.1, Math.min(1.1, dragPitch + dpitch));
    velYaw = dyaw / dt; velPitch = dpitch / dt;
    lastX = e.clientX; lastY = e.clientY; lastMove = now;
  });
  function endDrag() {
    if (!dragging) return;
    dragging = false; canvas.style.cursor = 'grab'; releasedAt = performance.now();
    if (performance.now() - lastMove > 90) velYaw = velPitch = 0;   // paused before release: no fling
    velYaw = Math.max(-9, Math.min(9, velYaw)); velPitch = Math.max(-5, Math.min(5, velPitch));
    dragYaw = wrap(dragYaw);
  }
  canvas.addEventListener('pointerup', endDrag);
  canvas.addEventListener('pointercancel', endDrag);
  // keyboard / assistive tech: nothing to operate, the canvas is decorative

  window.addEventListener('scroll', function () { scrollBias = Math.min(window.scrollY, 900) * 0.0018; }, { passive: true });

  function easeOutExpo(x) { return x >= 1 ? 1 : 1 - Math.pow(2, -10 * x); }
  function easeOutBack(x) { var c1 = 1.4, c3 = c1 + 1; return 1 + c3 * Math.pow(x - 1, 3) + c1 * Math.pow(x - 1, 2); }

  var INTRO = 2.4;
  function pose(t, dt) {
    // critically-damped-ish spring toward the pointer
    var k = 30, c = 9;
    vx += ((tx - px) * k - vx * c) * dt; px += vx * dt;
    vy += ((ty - py) * k - vy * c) * dt; py += vy * dt;

    if (!dragging) {
      // inertia after release, then (after a short rest) ease the offset back toward the idle sway
      dragYaw += velYaw * dt; dragPitch = Math.max(-1.1, Math.min(1.1, dragPitch + velPitch * dt));
      var damp = Math.exp(-3.2 * dt); velYaw *= damp; velPitch *= damp;
      if ((performance.now() - releasedAt) > 1400) {
        var back = 1 - Math.exp(-1.1 * dt);
        dragYaw -= dragYaw * back; dragPitch -= dragPitch * back;
      }
    }
    var idleT = Math.max(t - 0.4, 0);
    // breathing yaw/pitch (oscillates so the best angle stays in view) + pointer + scroll
    knot.rotation.y = -0.55 + Math.sin(idleT * 0.32) * 0.62 + idleT * 0.03 + px * 0.45 * (dragging ? 0 : 1) + scrollBias + dragYaw;
    knot.rotation.x = 0.18 + Math.sin(idleT * 0.23 + 1.2) * 0.16 + py * 0.28 * (dragging ? 0 : 1) + dragPitch;
    knot.rotation.z = Math.sin(idleT * 0.17) * 0.05;
    knot.scale.setScalar(SCALE * (0.82 + 0.18 * easeOutBack(Math.min(t / 1.6, 1))));
    knot.position.y = Math.sin(idleT * 0.55) * 0.07;

    rings.forEach(function (rg, i) {
      var local = Math.min(Math.max((t - 0.12 * i) / (INTRO - 0.3), 0), 1);
      var e = easeOutExpo(local), inv = 1 - e;
      rg.pivot.position.copy(rg.home).addScaledVector(rg.from, inv);
      // tiny per-ring drift once assembled (well inside the 0.05 clearance)
      if (e > 0.98) {
        rg.pivot.position.x += Math.sin(idleT * 0.6 + i * 2.1) * 0.018;
        rg.pivot.position.y += Math.sin(idleT * 0.5 + i * 1.7) * 0.018;
      }
      rg.mesh.rotation.set(rg.euler[0], rg.euler[1], rg.euler[2] + rg.spin * inv * 2.2);
    });
  }

  /* ---- render loop ---- */
  var running = false, visible = true, last = 0, t = reduce ? INTRO + 1 : 0;
  function frame(now) {
    if (!running) return;
    var dt = Math.min((now - last) / 1000, 0.1); last = now; t += dt;
    pose(t, dt);
    renderer.render(scene, camera);
    requestAnimationFrame(frame);
  }
  function start() {
    if (running || reduce || !visible || document.hidden) return;
    running = true; last = performance.now(); requestAnimationFrame(frame);
  }
  function stop() { running = false; }

  // first frame, then cross-fade the canvas in over the static fallback image
  pose(reduce ? t : 0.0001, 0.016);
  renderer.render(scene, camera);
  requestAnimationFrame(function () { host.classList.add('is-3d'); });

  if ('IntersectionObserver' in window) {
    new IntersectionObserver(function (es) { visible = es[0].isIntersecting; if (visible) start(); else stop(); }, { rootMargin: '80px' }).observe(host);
  }
  document.addEventListener('visibilitychange', function () { if (document.hidden) stop(); else start(); });
  start();
  };
})();
