/* ==========================================================
   STEMPEL NOTIFIKASI - file animasi terpisah
   Pasang bersama css/stempel.css

   Cara pakai:
     showToast("Sandi salah");                 // stempel biru (salah)
     showToast("Wajib diisi");                 // stempel biru
     showToast("Berhasil", "success");         // stempel hijau
     showToast("Sandi salah", "error", 5000);  // tampil 5 detik

   Container stempel dibuat otomatis di dalam elemen .card
   ========================================================== */

function showToast(message, type, duration){
  type = type || "error"; duration = duration || 3800;
  var box = document.getElementById("toastContainer");
  if (!box) {
    box = document.createElement("div");
    box.id = "toastContainer";
    box.className = "toast-container";
    (document.querySelector(".card") || document.body).appendChild(box);
  }
  box.innerHTML = "";
  var t = document.createElement("div");
  t.className = "toast" + (type === "success" ? " success" : "");
  t.innerHTML = '<svg viewBox="0 0 140 140" aria-hidden="true"><defs><filter id="rough" x="-20%" y="-20%" width="140%" height="140%"><feTurbulence type="fractalNoise" baseFrequency="0.045" numOctaves="3" seed="3" result="n"/><feDisplacementMap in="SourceGraphic" in2="n" scale="10" xChannelSelector="R" yChannelSelector="G"/></filter></defs><circle class="ink" cx="70" cy="70" r="4" style="--dx:75px;--dy:13px;animation-delay:.52s"/><circle class="ink" cx="70" cy="70" r="3" style="--dx:56px;--dy:62px;animation-delay:.54s"/><circle class="ink" cx="70" cy="70" r="5" style="--dx:12px;--dy:71px;animation-delay:.53s"/><circle class="ink" cx="70" cy="70" r="3.5" style="--dx:-41px;--dy:78px;animation-delay:.55s"/><circle class="ink" cx="70" cy="70" r="4.5" style="--dx:-71px;--dy:32px;animation-delay:.52s"/><circle class="ink" cx="70" cy="70" r="3" style="--dx:-81px;--dy:-14px;animation-delay:.56s"/><circle class="ink" cx="70" cy="70" r="5" style="--dx:-50px;--dy:-55px;animation-delay:.53s"/><circle class="ink" cx="70" cy="70" r="3.5" style="--dx:-12px;--dy:-85px;animation-delay:.55s"/><circle class="ink" cx="70" cy="70" r="4" style="--dx:40px;--dy:-69px;animation-delay:.52s"/><circle class="ink" cx="70" cy="70" r="3" style="--dx:70px;--dy:-31px;animation-delay:.54s"/><circle class="shock" cx="70" cy="70" r="60"/><circle class="shock s2" cx="70" cy="70" r="60"/><g filter="url(#rough)"><circle class="disc" cx="70" cy="70" r="60"/><circle class="ring" cx="70" cy="70" r="58" stroke-width="6" stroke-dasharray="90 5 60 8 110 4 45 6 30 6"/><circle class="ring" cx="70" cy="70" r="48" stroke-width="2.5" stroke-dasharray="70 4 50 7 90 5 40 6 20 9"/></g></svg><span></span>';
  var sp = t.querySelector("span");
  message.split(" ").forEach(function(w, i){
    var b = document.createElement("b");
    b.style.setProperty("--i", i);
    b.textContent = w;
    sp.appendChild(b);
  });
  var c = document.querySelector(".card");
  if (type !== "success" && c){ c.classList.remove("hit"); void c.offsetWidth; c.classList.add("hit"); }
  box.appendChild(t);
  t.classList.add("show");
  setTimeout(function(){
    t.classList.add("hide");
    setTimeout(function(){ t.remove(); }, 400);
  }, duration);
}
