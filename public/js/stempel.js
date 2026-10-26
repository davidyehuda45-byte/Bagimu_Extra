/* ==========================================================
   STEMPEL NOTIFIKASI - Bagimu Extra
   Aturan:
   - Jika error: tampilkan getar + stempel sejenak, lalu
     kembalikan lagi seperti semula (hilangkan stempel & status err).
   - Jika berhasil: stempel hijau permanen, jangan dikembalikan.
   ========================================================== */

var _stempelTimer = null;
var _stempelRemoveTimer = null;

function showToast(message, type, duration) {
  type = type || "error";
  duration = duration || 2500;

  if (_stempelTimer) {
    clearTimeout(_stempelTimer);
    _stempelTimer = null;
  }
  if (_stempelRemoveTimer) {
    clearTimeout(_stempelRemoveTimer);
    _stempelRemoveTimer = null;
  }

  var card = document.querySelector(".card");
  var box = document.getElementById("toastContainer");
  if (!box) {
    box = document.createElement("div");
    box.id = "toastContainer";
    box.className = "toast-container";
    (card || document.body).appendChild(box);
  }
  box.innerHTML = "";

  var t = document.createElement("div");
  t.className = "toast" + (type === "success" ? " success" : "");
  t.innerHTML = '<svg viewBox="0 0 140 140" aria-hidden="true"><defs><filter id="rough" x="-20%" y="-20%" width="140%" height="140%"><feTurbulence type="fractalNoise" baseFrequency="0.045" numOctaves="3" seed="3" result="n"/><feDisplacementMap in="SourceGraphic" in2="n" scale="10" xChannelSelector="R" yChannelSelector="G"/></filter></defs><circle class="shock" cx="70" cy="70" r="60"/><circle class="shock s2" cx="70" cy="70" r="60"/><g filter="url(#rough)"><circle class="disc" cx="70" cy="70" r="60"/><circle class="ring" cx="70" cy="70" r="58" stroke-width="6" stroke-dasharray="90 5 60 8 110 4 45 6 30 6"/><circle class="ring" cx="70" cy="70" r="48" stroke-width="2.5" stroke-dasharray="70 4 50 7 90 5 40 6 20 9"/></g></svg><span></span>';

  var sp = t.querySelector("span");
  message.split(" ").forEach(function(w, i) {
    var b = document.createElement("b");
    b.style.setProperty("--i", i);
    b.textContent = w;
    sp.appendChild(b);
  });

  if (card) {
    card.classList.remove("hit");
    void card.offsetWidth;
    card.classList.add("hit");
  }

  box.appendChild(t);
  t.classList.add("show");

  // Jika error: kembalikan lagi seperti semula setelah durasi selesai
  if (type !== "success") {
    _stempelTimer = setTimeout(function() {
      t.classList.add("hide");
      _stempelRemoveTimer = setTimeout(function() {
        t.remove();
        // Kembalikan form seperti semula: bersihkan indikator err
        document.querySelectorAll("input.err").forEach(function(el) {
          el.classList.remove("err");
        });
      }, 450);
    }, duration);
  }
  // Jika berhasil: stempel tetap terpasang, jangan dikembalikan
}

