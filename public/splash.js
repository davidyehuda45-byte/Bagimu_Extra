/* ===== Splash Screen – Ide 1: The Micro-Brand Reveal ===== */
(function () {
  'use strict';

  var splash = document.getElementById('splash');
  if (!splash) return;

  // Cek apakah sudah pernah tampil di sesi browser ini
  try {
    if (sessionStorage.getItem('bagimu_splash_shown') === '1') {
      if (splash.parentNode) {
        splash.parentNode.removeChild(splash);
      }
      var appInit = document.getElementById('app-content');
      if (appInit) {
        appInit.classList.add('show');
      }
      document.dispatchEvent(new CustomEvent('splash:selesai'));
      return;
    }
  } catch (e) {
    // Abaikan jika storage dinonaktifkan di browser
  }

  var DURATION = 1100; // milidetik (~1.1 detik)
  var bar = splash.querySelector('.sp-bar');
  var isFinished = false;

  function selesai() {
    if (isFinished) return;
    isFinished = true;

    try {
      sessionStorage.setItem('bagimu_splash_shown', '1');
    } catch (e) {}

    // Penuhkan progress bar
    if (bar) {
      bar.style.width = '100%';
    }

    // Beri sinyal ke konten halaman utama agar mulai fade-in
    document.dispatchEvent(new CustomEvent('splash:selesai'));

    // Transisi keluar halus (dissolve blur + soft zoom)
    splash.classList.add('out');

    setTimeout(function () {
      if (splash.parentNode) {
        splash.parentNode.removeChild(splash);
      }
    }, 420);
  }

  // Mulai animasi progress bar
  requestAnimationFrame(function () {
    if (bar) {
      bar.style.transition = 'width ' + DURATION + 'ms cubic-bezier(0.2, 0.8, 0.4, 1)';
      bar.style.width = '100%';
    }
  });

  // Otomatis selesai setelah DURATION
  var timer = setTimeout(selesai, DURATION);

  // Akselerasi klik atau sentuhan di mana saja untuk langsung lewati
  splash.addEventListener('click', function () {
    clearTimeout(timer);
    selesai();
  }, { once: true });

  // Dukungan keyboard (Esc, Spasi, Enter) untuk aksesibilitas
  splash.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' || e.key === ' ' || e.key === 'Enter') {
      clearTimeout(timer);
      selesai();
    }
  });
})();
