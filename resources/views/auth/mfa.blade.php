<x-guest-layout>
    <div class="mb-4 text-center">
        <h1 class="text-xl font-bold text-slate-900">Verifikasi MFA</h1>
        <p class="text-sm text-slate-500">Masukkan 6 digit kode dari aplikasi authenticator.</p>
    </div>

    <form method="POST" action="{{ route('admin.mfa') }}">
        @csrf

        <div>
            <x-input-label for="code" value="Kode MFA" />
            <x-text-input id="code" class="mt-1 block w-full text-center text-2xl tracking-[0.5em] font-mono"
                            type="text" inputmode="numeric" autocomplete="one-time-code"
                            name="code" required autofocus maxlength="6" />
            <x-input-error :messages="$errors->get('code')" class="mt-2" />
        </div>

        <x-primary-button class="mt-6 w-full justify-center">
            Verifikasi
        </x-primary-button>
    </form>
</x-guest-layout>
