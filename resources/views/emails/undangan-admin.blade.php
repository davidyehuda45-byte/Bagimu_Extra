<!DOCTYPE html>
<html lang="id">
<head><meta charset="utf-8"><title>Undangan Akun Staf / Pembina</title></head>
<body style="font-family: sans-serif; color: #0f172a;">
    <p>Halo {{ $nama }},</p>
    <p>Anda telah ditambahkan sebagai <b>{{ $peran }}</b> di sistem ekstrakurikuler {{ config('app.name', 'Bagimu Extra') }}.</p>
    <p>
        Username: <b>{{ $username }}</b><br>
        Password: <b>{{ $password }}</b>
    </p>
    <p>Tautan masuk sistem (Staf / Pembina):<br><a href="{{ $loginUrl }}">{{ $loginUrl }}</a></p>
    <p>Simpan kredensial ini dengan aman dan jangan dibagikan kepada pihak lain. Segera ganti password setelah login pertama via menu pengaturan akun.</p>
</body>
</html>
