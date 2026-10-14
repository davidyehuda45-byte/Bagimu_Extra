<!DOCTYPE html>
<html lang="id">
<head><meta charset="utf-8"><title>Undangan Akun Pembina & Pengelola</title></head>
<body style="font-family: sans-serif; color: #0f172a;">
    <p>Halo {{ $nama }},</p>
    <p>Anda telah ditambahkan sebagai <b>{{ $peran }}</b> di sistem ekstrakurikuler {{ config('app.name', 'Bagimu Extra') }}.</p>
    <p>
        Username: <b>{{ $username }}</b><br>
        Password: <b>{{ $password }}</b>
    </p>
    <p>Tautan masuk sistem:<br><a href="{{ $loginUrl }}">{{ $loginUrl }}</a></p>
    <p>Simpan kredensial ini dengan aman dan jangan dibagikan kepada pihak lain.</p>
</body>
</html>
