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

        Validator::make($input, $rules)->validate();

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
