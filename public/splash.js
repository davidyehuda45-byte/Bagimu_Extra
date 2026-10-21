/* ===== Splash Screen – Ide 1: The Micro-Brand Reveal ===== */
(function () {
  'use strict';

  var splash = document.getElementById('splash');
  if (!splash) return;

  // Cek apakah sudah pernah tampil di sesi browser ini (hindari spam saat navigasi internal)
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

  var DURATION = 2000; // Tepat 2 detik sesuai permintaan pengguna
  var bar = splash.querySelector('.sp-bar');
  var isFinished = false;

  function selesai() {
    if (isFinished) return;
    isFinished = true;

    try {
      sessionStorage.setItem('bagimu_splash_shown', '1');
    } catch (e) {}

    // Penuhkan progress bar seketika saat keluar
    if (bar) {
      bar.style.width = '100%';
    }

    // Beri sinyal ke konten halaman utama agar mulai transisi fade-in
    document.dispatchEvent(new CustomEvent('splash:selesai'));

    // Transisi keluar halus (dissolve blur + soft zoom)
    splash.classList.add('out');

    setTimeout(function () {
      if (splash.parentNode) {
        splash.parentNode.removeChild(splash);
      }
    }, 450);
  }

  // Animasi progress bar berjalan serasi selama 2 detik
  requestAnimationFrame(function () {
    if (bar) {
      bar.style.transition = 'width ' + DURATION + 'ms cubic-bezier(0.25, 0.8, 0.4, 1)';
      bar.style.width = '100%';
    }
  });

  // Otomatis langsung masuk ke beranda setelah 2 detik
  var timer = setTimeout(selesai, DURATION);

  // Akselerasi klik atau sentuhan di mana saja untuk langsung lewati
  splash.addEventListener('click', function () {
    clearTimeout(timer);
    selesai();
  }, { once: true });

  // Aksesibilitas keyboard (Esc, Space, Enter)
  splash.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' || e.key === ' ' || e.key === 'Enter') {
      clearTimeout(timer);
      selesai();
    }
  });
})();
