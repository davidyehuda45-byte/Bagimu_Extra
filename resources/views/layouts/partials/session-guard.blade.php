@auth
@php
    $bagimuAuthToken = session('auth_session_id');
    $bagimuIsFresh = session()->pull('fresh_login', false);
@endphp
@if($bagimuAuthToken)
<script>
(function() {
    try {
        const serverToken = @json($bagimuAuthToken);
        const isFresh = @json($bagimuIsFresh);
        const channelName = 'bagimu_auth_sync_' + serverToken.substring(0, 12);
        const exitUrl = "{{ route('auth.session.exit') }}";

        let bc;
        try {
            if ('BroadcastChannel' in window) {
                bc = new BroadcastChannel(channelName);
                bc.onmessage = function(ev) {
                    if (!ev || !ev.data) return;
                    if (ev.data.type === 'PING_SESSION') {
                        const cur = sessionStorage.getItem('bagimu_auth_session');
                        if (cur === serverToken) {
                            bc.postMessage({ type: 'PONG_SESSION', token: cur });
                        }
                    } else if (ev.data.type === 'LOGOUT') {
                        sessionStorage.removeItem('bagimu_auth_session');
                    }
                };
            }
        } catch (err) {}

        if (isFresh) {
            sessionStorage.setItem('bagimu_auth_session', serverToken);
        } else {
            const clientToken = sessionStorage.getItem('bagimu_auth_session');
            if (clientToken === serverToken) {
                // Token cocok pada tab aktif ini
            } else if (bc) {
                // Token belum ada pada tab ini (misal buka tab baru dari menu klik kanan)
                // Tanyakan ke tab terbuka lainnya apakah ada sesi aktif yang sah
                let resolved = false;
                const timer = setTimeout(function() {
                    if (!resolved) {
                        resolved = true;
                        window.location.replace(exitUrl);
                    }
                }, 180);

                const origHandler = bc.onmessage;
                bc.onmessage = function(ev) {
                    if (ev && ev.data && ev.data.type === 'PONG_SESSION' && ev.data.token === serverToken) {
                        resolved = true;
                        clearTimeout(timer);
                        sessionStorage.setItem('bagimu_auth_session', serverToken);
                        if (origHandler) bc.onmessage = origHandler;
                    } else if (origHandler) {
                        origHandler(ev);
                    }
                };

                bc.postMessage({ type: 'PING_SESSION' });
            } else {
                // Tidak ada BroadcastChannel dan token tab kosong -> keluar ke login
                window.location.replace(exitUrl);
            }
        }

        // Hapus token session storage saat tombol logout ditekan
        window.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('form[action*="logout"]').forEach(function(form) {
                form.addEventListener('submit', function() {
                    try {
                        sessionStorage.removeItem('bagimu_auth_session');
                        if (bc) bc.postMessage({ type: 'LOGOUT' });
                    } catch (e) {}
                });
            });
        });
    } catch (e) {}
})();
</script>
@endif
@endauth

