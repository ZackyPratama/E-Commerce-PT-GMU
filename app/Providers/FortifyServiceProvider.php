<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewCustomer;
use App\Actions\Fortify\ResetUserPassword;
use App\Models\Customer;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\RegisterResponse;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureActions();
        $this->configureViews();
        $this->configureRateLimiting();
        $this->configureRegisterResponse();

        // configure authentication to use customer guard
        Fortify::authenticateUsing(function(Request $request){
            $customer = Customer::where('email', $request->email)->first();

            if (!$customer || !Hash::check($request->password, $customer->password)) {
                throw ValidationException::withMessages([
                    'email' => 'Email atau password salah.',
                ]);
            }

            // Cek status B2B
            if ($customer->isB2BPending()) {
                throw ValidationException::withMessages([
                    'email' => 'Akun perusahaan Anda masih menunggu verifikasi admin. Silakan coba lagi setelah akun diaktifkan.',
                ]);
            }

            if ($customer->isB2BRejected()) {
                $reason = $customer->rejection_reason ? ' Alasan: ' . $customer->rejection_reason : '';
                throw ValidationException::withMessages([
                    'email' => 'Akun perusahaan Anda ditolak.' . $reason,
                ]);
            }

            return $customer;
        });
    }

    /**
     * Configure Fortify actions.
     */
    private function configureActions(): void
    {
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);
        Fortify::createUsersUsing(CreateNewCustomer::class);
    }

    /**
     * Configure Fortify views.
     */
    private function configureViews(): void
    {
        Fortify::loginView(fn() => view('auth.customer.login'));
        Fortify::verifyEmailView(fn() => view('livewire.auth.verify-email'));
        Fortify::twoFactorChallengeView(fn() => view('livewire.auth.two-factor-challenge'));
        Fortify::confirmPasswordView(fn() => view('livewire.auth.confirm-password'));
        Fortify::registerView(fn() => view('auth.customer.register'));
        Fortify::resetPasswordView(fn() => view('livewire.auth.reset-password'));
        Fortify::requestPasswordResetLinkView(fn() => view('livewire.auth.forgot-password'));
    }

    /**
     * Configure rate limiting.
     */
    private function configureRateLimiting(): void
    {
        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)
                ->by($request->session()->get('login.id'))
                ->response(fn (Request $request, array $headers) => throw ValidationException::withMessages([
                    'email' => 'Terlalu banyak percobaan login. Silakan coba lagi dalam beberapa menit.',
                ]));
        });

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())) . '|' . $request->ip());

            return Limit::perMinute(5)
                ->by($throttleKey)
                ->response(fn (Request $request, array $headers) => throw ValidationException::withMessages([
                    'email' => 'Terlalu banyak percobaan login. Silakan coba lagi dalam beberapa menit.',
                ]));
        });
    }

    /**
     * Configure custom register response.
     */
    private function configureRegisterResponse(): void
    {
        $this->app->singleton(RegisterResponse::class, function () {
            return new class implements RegisterResponse {
                public function toResponse($request)
                {
                    $customer = auth()->guard('customer')->user();

                    if ($customer && $customer->isB2BPending()) {
                        auth()->guard('customer')->logout();
                        $request->session()->invalidate();
                        $request->session()->regenerateToken();

                        return redirect()->route('login')->with('status', 'Pendaftaran berhasil! Akun perusahaan Anda sedang menunggu verifikasi admin. Kami akan memberitahu Anda setelah akun diaktifkan.');
                    }

                    return redirect()->intended(config('fortify.home'));
                }
            };
        });
    }
}
