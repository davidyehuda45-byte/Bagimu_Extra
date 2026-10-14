@props(['size' => 40])
{{--
  Logo sekolah: pakai /logo.png bila ada, fallback ikon toga.
  Taruh file logo di public/logo.png untuk mengganti ikon.
--}}
<span class="relative block shrink-0 overflow-hidden bg-white rounded-full ring-2 ring-slate-200" style="width: {{ $size }}px; height: {{ $size }}px;">
    <img src="/logo.png" alt="Logo sekolah" class="h-full w-full object-cover" style="display: none;"
        onload="this.style.display='block';this.parentElement.querySelector('[data-fallback]').style.display='none';"
        onerror="this.style.display='none';">
    <span data-fallback class="flex h-full w-full items-center justify-center bg-blue-800 text-white rounded-xl">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 60%; height: 60%;" aria-hidden="true"><path d="M21.42 10.922a1 1 0 0 0-.019-1.838L12.83 5.18a2 2 0 0 0-1.66 0L2.6 9.08a1 1 0 0 0 0 1.832l8.57 3.908a2 2 0 0 0 1.66 0z"/><path d="M22 10v6"/><path d="M6 12.5V16a6 3 0 0 0 12 0v-3.5"/></svg>
    </span>
</span>
