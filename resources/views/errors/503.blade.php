@php
    $pesan = !empty($exception?->getMessage()) ? $exception->getMessage() : \App\Models\AppSetting::get('app.maintenance_msg', 'Sistem sedang dalam proses pemeliharaan dan optimalisasi data.');
    $eta = \App\Models\AppSetting::get('app.maintenance_eta');
@endphp
@include('errors.maintenance', ['pesan' => $pesan, 'eta' => $eta])

