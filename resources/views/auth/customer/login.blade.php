<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-[#F1F3F5] text-[#0F1419] font-['Geist',sans-serif] antialiased">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <!-- Header -->
            <div class="text-center">
                <a href="{{ route('home') }}" class="inline-block">
                    <img src="{{ asset('images/logorm.webp') }}" alt="{{ config('app.name') }}"
                        class="h-20 w-auto mx-auto">
                </a>
                <h2 class="mt-8 text-[2.25rem] font-semibold tracking-[-0.02em] text-[#0F1419]">
                    Selamat Datang Kembali!
                </h2>
                <p class="mt-2 text-[0.95rem] text-[#4A5568]">
                    Belum punya akun?
                    <a href="{{ route('register') }}"
                        class="font-medium text-[#0F1419] underline decoration-[#4A5568]/30 hover:decoration-[#0F1419] transition-colors">
                        Daftar
                    </a>
                </p>
            </div>

            <!-- Login Form -->
            <div class="bg-[#FFFFFF] p-[24px] sm:p-[32px] rounded-[16px] shadow-sm border border-[#4A5568]/10">
                @if (session('status'))
                    <div
                        class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-[10px] text-[0.95rem]">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- Email -->
                    <div class="mb-5">
                        <label for="email" class="block text-[0.875rem] font-medium text-[#4A5568] mb-2">
                            Email
                        </label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                            class="w-full px-4 py-3 bg-[#F1F3F5]/50 border border-[#4A5568]/20 rounded-[10px] text-[0.95rem] focus:ring-1 focus:ring-[#0F1419] focus:border-[#0F1419] transition-colors outline-none">
                        @error('email')
                            <p class="mt-2 text-[0.75rem] text-red-600 font-['Geist_Mono',monospace]">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="mb-6">
                        <label for="password" class="block text-[0.875rem] font-medium text-[#4A5568] mb-2">
                            Password
                        </label>
                        <input id="password" type="password" name="password" required
                            class="w-full px-4 py-3 bg-[#F1F3F5]/50 border border-[#4A5568]/20 rounded-[10px] text-[0.95rem] focus:ring-1 focus:ring-[#0F1419] focus:border-[#0F1419] transition-colors outline-none">
                        @error('password')
                            <p class="mt-2 text-[0.75rem] text-red-600 font-['Geist_Mono',monospace]">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center justify-between mb-8">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="remember"
                                class="w-4 h-4 text-[#0F1419] border-[#4A5568]/30 rounded-[4px] focus:ring-[#0F1419]">
                            <span class="text-[0.95rem] text-[#4A5568]">Ingat saya</span>
                        </label>

                        <a href="{{ route('password.request') }}"
                            class="text-[0.95rem] text-[#4A5568] hover:text-[#0F1419] transition-colors">
                            Lupa password?
                        </a>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit"
                        class="w-full bg-[#2C5EF5] text-[#FFFFFF] py-[12px] px-[20px] rounded-[10px] font-medium text-[0.95rem] hover:opacity-90 transition-opacity">
                        Masuk
                    </button>
                </form>

                <!-- Back to Home -->
                <p class="mt-8 text-center text-[0.95rem]">
                    <a href="{{ route('home') }}"
                        class="text-[#4A5568] hover:text-[#0F1419] transition-colors flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Kembali ke Beranda
                    </a>
                </p>
            </div>
        </div>
</body>