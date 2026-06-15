<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-[#F1F3F5] text-[#0F1419] font-['Geist',sans-serif] antialiased">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full">
            <!-- Logo -->
            <div class="text-center mb-8">
                <a href="{{ route('home') }}" class="inline-block">
                    <img src="{{ asset('images/logorm.webp') }}" alt="{{ config('app.name') }}" class="h-20 w-auto mx-auto">
                </a>
                <h2 class="mt-6 text-[2.25rem] font-semibold tracking-[-0.02em] text-[#0F1419]">
                    Daftar Akun Baru
                </h2>
                <p class="mt-2 text-[0.95rem] text-[#4A5568]">
                    Sudah punya akun?
                    <a href="{{ route('login') }}"
                        class="font-medium text-[#0F1419] underline decoration-[#4A5568]/30 hover:decoration-[#0F1419] transition-colors">
                        Masuk
                    </a>
                </p>
            </div>

            <!-- Registration Form -->
            <div class="bg-[#FFFFFF] p-6 sm:p-8 rounded-2xl shadow-sm border border-[#4A5568]/10">
                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <!-- Account Type Toggle -->
                    <div class="mb-6">
                        <label class="block text-[0.875rem] font-medium text-[#4A5568] mb-3">
                            Tipe Akun
                        </label>
                        <div class="flex gap-3">
                            <label class="flex-1 cursor-pointer">
                                <input type="radio" name="type" value="b2c"
                                    class="peer sr-only"
                                    {{ (old('type', request('type', 'b2c')) === 'b2c') ? 'checked' : '' }}>
                                <div class="text-center px-4 py-3 border border-[#4A5568]/20 rounded-[10px] text-[0.875rem] font-medium text-[#4A5568] peer-checked:border-[#2C5EF5] peer-checked:bg-[#2C5EF5]/5 peer-checked:text-[#2C5EF5] transition-colors">
                                    Perorangan
                                </div>
                            </label>
                            <label class="flex-1 cursor-pointer">
                                <input type="radio" name="type" value="b2b"
                                    class="peer sr-only b2b-type-radio"
                                    {{ (old('type', request('type')) === 'b2b') ? 'checked' : '' }}>
                                <div class="text-center px-4 py-3 border border-[#4A5568]/20 rounded-[10px] text-[0.875rem] font-medium text-[#4A5568] peer-checked:border-[#2C5EF5] peer-checked:bg-[#2C5EF5]/5 peer-checked:text-[#2C5EF5] transition-colors">
                                    Perusahaan
                                </div>
                            </label>
                        </div>
                        @error('type')
                            <p class="mt-2 text-[0.75rem] text-red-600 font-['Geist_Mono',monospace]">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- B2B Fields (hidden by default, shown when "Perusahaan" selected) -->
                    <div id="b2b-fields" class="{{ (old('type', request('type')) === 'b2b') ? '' : 'hidden' }}">
                        <!-- Company Name -->
                        <div class="mb-5">
                            <label for="company_name" class="block text-[0.875rem] font-medium text-[#4A5568] mb-2">
                                Nama Perusahaan <span class="text-red-500">*</span>
                            </label>
                            <input id="company_name" type="text" name="company_name" value="{{ old('company_name') }}"
                                class="w-full px-4 py-3 bg-[#F1F3F5]/50 border border-[#4A5568]/20 rounded-[10px] text-[0.95rem] focus:ring-1 focus:ring-[#0F1419] focus:border-[#0F1419] transition-colors outline-none">
                            @error('company_name')
                                <p class="mt-2 text-[0.75rem] text-red-600 font-['Geist_Mono',monospace]">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Company Registration Number (NPWP) -->
                        <div class="mb-5">
                            <label for="company_registration_number" class="block text-[0.875rem] font-medium text-[#4A5568] mb-2">
                                NPWP (Opsional)
                            </label>
                            <input id="company_registration_number" type="text" name="company_registration_number" value="{{ old('company_registration_number') }}"
                                placeholder="XX.XXX.XXX.X-XXX.XXX"
                                class="w-full px-4 py-3 bg-[#F1F3F5]/50 border border-[#4A5568]/20 rounded-[10px] text-[0.95rem] focus:ring-1 focus:ring-[#0F1419] focus:border-[#0F1419] transition-colors outline-none">
                            @error('company_registration_number')
                                <p class="mt-2 text-[0.75rem] text-red-600 font-['Geist_Mono',monospace]">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- B2B Info -->
                        <div class="mb-5 p-4 bg-blue-50 border border-blue-200 rounded-[10px]">
                            <p class="text-[0.85rem] text-blue-700">
                                Akun perusahaan akan ditinjau oleh admin terlebih dahulu.
                                Anda akan menerima notifikasi setelah akun diaktifkan.
                            </p>
                        </div>
                    </div>

                    <!-- Name -->
                    <div class="mb-5">
                        <label for="name" class="block text-[0.875rem] font-medium text-[#4A5568] mb-2">
                            Nama Lengkap <span class="text-red-500">*</span>
                        </label>
                        <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                            class="w-full px-4 py-3 bg-[#F1F3F5]/50 border border-[#4A5568]/20 rounded-[10px] text-[0.95rem] focus:ring-1 focus:ring-[#0F1419] focus:border-[#0F1419] transition-colors outline-none">
                        @error('name')
                            <p class="mt-2 text-[0.75rem] text-red-600 font-['Geist_Mono',monospace]">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="mb-5">
                        <label for="email" class="block text-[0.875rem] font-medium text-[#4A5568] mb-2">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required
                            class="w-full px-4 py-3 bg-[#F1F3F5]/50 border border-[#4A5568]/20 rounded-[10px] text-[0.95rem] focus:ring-1 focus:ring-[#0F1419] focus:border-[#0F1419] transition-colors outline-none">
                        @error('email')
                            <p class="mt-2 text-[0.75rem] text-red-600 font-['Geist_Mono',monospace]">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Phone -->
                    <div class="mb-5">
                        <label for="phone" class="block text-[0.875rem] font-medium text-[#4A5568] mb-2">
                            Nomor Telepon
                        </label>
                        <input id="phone" type="tel" name="phone" value="{{ old('phone') }}"
                            class="w-full px-4 py-3 bg-[#F1F3F5]/50 border border-[#4A5568]/20 rounded-[10px] text-[0.95rem] focus:ring-1 focus:ring-[#0F1419] focus:border-[#0F1419] transition-colors outline-none">
                        @error('phone')
                            <p class="mt-2 text-[0.75rem] text-red-600 font-['Geist_Mono',monospace]">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="mb-5">
                        <label for="password" class="block text-[0.875rem] font-medium text-[#4A5568] mb-2">
                            Password <span class="text-red-500">*</span>
                        </label>
                        <input id="password" type="password" name="password" required
                            class="w-full px-4 py-3 bg-[#F1F3F5]/50 border border-[#4A5568]/20 rounded-[10px] text-[0.95rem] focus:ring-1 focus:ring-[#0F1419] focus:border-[#0F1419] transition-colors outline-none">
                        @error('password')
                            <p class="mt-2 text-[0.75rem] text-red-600 font-['Geist_Mono',monospace]">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password Confirmation -->
                    <div class="mb-5">
                        <label for="password_confirmation" class="block text-[0.875rem] font-medium text-[#4A5568] mb-2">
                            Konfirmasi Password <span class="text-red-500">*</span>
                        </label>
                        <input id="password_confirmation" type="password" name="password_confirmation" required
                            class="w-full px-4 py-3 bg-[#F1F3F5]/50 border border-[#4A5568]/20 rounded-[10px] text-[0.95rem] focus:ring-1 focus:ring-[#0F1419] focus:border-[#0F1419] transition-colors outline-none">
                    </div>

                    <!-- Terms -->
                    <div class="mb-6">
                        <label class="flex items-start gap-2 cursor-pointer">
                            <input type="checkbox" required
                                class="w-4 h-4 text-[#0F1419] border-[#4A5568]/30 rounded-sm focus:ring-[#0F1419] mt-0.5">
                            <span class="text-[0.875rem] text-[#4A5568]">
                                Saya setuju dengan
                                <a href="#" class="text-[#0F1419] underline decoration-[#4A5568]/30 hover:decoration-[#0F1419]">Syarat dan Ketentuan</a>
                                dan
                                <a href="#" class="text-[#0F1419] underline decoration-[#4A5568]/30 hover:decoration-[#0F1419]">Kebijakan Privasi</a>
                            </span>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit"
                        class="w-full bg-[#2C5EF5] text-[#FFFFFF] py-3 px-5 rounded-[10px] font-medium text-[0.95rem] hover:opacity-90 transition-opacity">
                        Buat Akun
                    </button>
                </form>

                <!-- Back to Home -->
                <p class="mt-8 text-center text-[0.95rem]">
                    <a href="{{ route('home') }}"
                        class="text-[#4A5568] hover:text-[#0F1419] transition-colors flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Kembali ke Beranda
                    </a>
                </p>
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('.b2b-type-radio').forEach(radio => {
            radio.addEventListener('change', function() {
                const b2bFields = document.getElementById('b2b-fields');
                const companyNameInput = document.getElementById('company_name');
                if (this.checked) {
                    b2bFields.classList.remove('hidden');
                    companyNameInput.setAttribute('required', 'required');
                }
            });
        });

        document.querySelectorAll('input[name="type"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const b2bFields = document.getElementById('b2b-fields');
                const companyNameInput = document.getElementById('company_name');
                if (this.value === 'b2b') {
                    b2bFields.classList.remove('hidden');
                    companyNameInput.setAttribute('required', 'required');
                } else {
                    b2bFields.classList.add('hidden');
                    companyNameInput.removeAttribute('required');
                }
            });
        });
    </script>
</body>

</html>
