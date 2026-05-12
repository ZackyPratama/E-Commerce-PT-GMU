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
        Validator::make($input, [
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
        ])->validate();

        $customer = Customer::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
            'phone' => $input['phone'] ?? null,
            'is_active' => true, // set default active status
        ]);

        // kirim welcome email atau lakukan tindakan lain setelah pendaftaran jika diperlukan
        return $customer;
    }
}
