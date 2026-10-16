<?php

namespace App\Http\Controllers;

use Inertia\Response;

class InertiaTestController extends Controller
{
    public function __invoke(): Response
    {
        return inertia('Test', [
            'message' => 'Setup Inertia + React di Bagimu Extra sudah aktif dan siap digunakan.',
        ]);
    }
}
