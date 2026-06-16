<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Catering Al-Bahjah') }}</title>
    <meta name="theme-color" content="#0f4c35">
    <link rel="manifest" href="/manifest.json">
    <link rel="apple-touch-icon" href="/icons/icon-192.png">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased">
    {{-- Background gradien islami --}}
    <div class="min-h-screen flex" style="background: linear-gradient(135deg, #0a3526 0%, #0f4c35 40%, #166644 100%);">

        {{-- Panel kiri — branding (hanya tampil di layar lg+) --}}
        <div class="hidden lg:flex flex-col items-center justify-center flex-1 px-12 text-center">
            {{-- Ornamen lingkaran --}}
            <div class="relative">
                <div class="absolute -inset-8 rounded-full bg-white/5 animate-pulse"></div>
                <div class="relative w-28 h-28 rounded-full bg-white/10 border-2 border-white/20
                            flex items-center justify-center shadow-2xl">
                    <svg class="w-14 h-14 text-green-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
            </div>

            <h1 class="mt-8 text-4xl font-bold text-white">Catering Al-Bahjah</h1>
            <p class="mt-3 text-green-200 text-lg leading-relaxed max-w-sm">
                Sistem Informasi Pengadaan Bahan Makanan
            </p>
            <p class="mt-2 text-green-300 text-sm">Pondok Pesantren Al-Bahjah</p>

            {{-- Fitur highlights --}}
            <div class="mt-10 grid grid-cols-2 gap-4 max-w-md w-full text-left">
                @foreach([
                    ['🧾', 'Catat Nota', 'Digitalisasi nota pembelian harian'],
                    ['📊', 'Laporan', 'Export Excel & PDF otomatis'],
                    ['📱', 'Installable', 'Tersedia sebagai aplikasi Android'],
                    ['🔒', 'Aman', 'Akses berbasis peran pengguna'],
                ] as $f)
                <div class="bg-white/10 rounded-xl p-4 border border-white/10">
                    <div class="text-2xl mb-1">{{ $f[0] }}</div>
                    <div class="text-white font-semibold text-sm">{{ $f[1] }}</div>
                    <div class="text-green-300 text-xs mt-0.5">{{ $f[2] }}</div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Panel kanan — form login --}}
        <div class="flex flex-col items-center justify-center w-full lg:w-auto lg:min-w-[420px]
                    bg-white lg:rounded-l-3xl px-8 py-12 lg:px-12 shadow-2xl">

            {{-- Mobile header --}}
            <div class="lg:hidden mb-8 text-center">
                <div class="w-16 h-16 rounded-2xl bg-primary-600 flex items-center justify-center mx-auto mb-4 shadow-lg">
                    <svg class="w-9 h-9 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-gray-800">Catering Al-Bahjah</h2>
            </div>

            <div class="w-full max-w-sm">
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900">Selamat Datang</h2>
                    <p class="mt-1 text-gray-500 text-sm">Masukkan kredensial Anda untuk melanjutkan</p>
                </div>

                {{ $slot }}

                <p class="mt-8 text-center text-xs text-gray-400">
                    &copy; {{ date('Y') }} Pondok Pesantren Al-Bahjah
                </p>
            </div>
        </div>
    </div>

    @livewireScripts
</body>
</html>
