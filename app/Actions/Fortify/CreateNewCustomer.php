<?php

namespace App\Actions\Fortify;

use App\Models\Customer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewCustomer implements CreatesNewUsers
{
    use PasswordValidationRules;

    public function create(array $input)
    {
        $isB2B = ($input['type'] ?? 'b2c') === 'b2b';

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(Customer::class),
            ],
            'password' => $this->passwordRules(),
            'phone' => ['nullable', 'string', 'max:20'],
            'type' => ['required', 'in:b2c,b2b'],
        ];

        if ($isB2B) {
            $rules['company_name'] = ['required', 'string', 'max:255'];
            $rules['company_registration_number'] = ['nullable', 'string', 'max:50'];
        }

        Validator::make($input, $rules, [
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.max' => 'Nama lengkap maksimal 255 karakter.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar. Silakan gunakan email lain.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'type.required' => 'Jenis akun wajib dipilih.',
            'type.in' => 'Jenis akun tidak valid.',
            'phone.max' => 'Nomor telepon maksimal 20 karakter.',
            'company_name.required' => 'Nama perusahaan wajib diisi untuk akun B2B.',
            'company_name.max' => 'Nama perusahaan maksimal 255 karakter.',
            'company_registration_number.max' => 'Nomor registrasi perusahaan maksimal 50 karakter.',
        ])->validate();

        $data = [
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
            'phone' => $input['phone'] ?? null,
            'type' => $isB2B ? 'b2b' : 'b2c',
            'is_active' => true,
        ];

        if ($isB2B) {
            $data['company_name'] = $input['company_name'];
            $data['company_registration_number'] = $input['company_registration_number'] ?? null;
            $data['b2b_status'] = 'pending';
        }

        $customer = Customer::create($data);

        return $customer;
    }
}
