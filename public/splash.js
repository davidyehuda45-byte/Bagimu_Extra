/* ===== Splash Screen – SMK Bagimu Negeriku ===== */
(function () {
  /* ---- Pengaturan ---- */
  var CONFIG = {
    durasi: 3000,              // lama minimum splash (milidetik)
    teks: 'Ekstrakurikuler'    // teks yang diketik otomatis
  };

  var splash = document.getElementById('splash');
  if (!splash) return;
  var num = document.getElementById('sp-num');
  var ekskul = document.getElementById('sp-ekskul');

  var t0 = Date.now(), cur = 1, done = false;
  var loaded = document.readyState === 'complete';
  window.addEventListener('load', function () { loaded = true; });

  function selesai() {
    splash.classList.add('shrink');                    // logo mengecil
    setTimeout(function () {
      document.dispatchEvent(new CustomEvent('splash:selesai')); // beri tahu halaman utama
      splash.classList.add('out');
      setTimeout(function () { splash.remove(); }, 500);
    }, 650);
  }

  function tick() {
    var t = Math.min(1, (Date.now() - t0) / CONFIG.durasi);
    var target = 1 + 99 * t;                           // naik 1 → 100
    if (!loaded) target = Math.min(target, 92);        // tunggu halaman benar-benar siap
    cur = Math.max(cur, Math.floor(target));
    num.textContent = cur;
    if (cur >= 100 && !done) { done = true; setTimeout(selesai, 350); return; }
    requestAnimationFrame(tick);
  }
  requestAnimationFrame(tick);

  /* teks diketik sendiri */
  var i = 0;
  function ketik() {
    if (i <= CONFIG.teks.length) {
      ekskul.textContent = CONFIG.teks.slice(0, i++);
      setTimeout(ketik, 95 + Math.random() * 70);
    }
  }
  setTimeout(ketik, 700);

  /* tombol "back" browser → splash tampil lagi */
  window.addEventListener('pageshow', function (e) { if (e.persisted) location.reload(); });
})();
