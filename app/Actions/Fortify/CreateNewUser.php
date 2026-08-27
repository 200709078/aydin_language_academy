<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['required', 'string', 'regex:/^\+[1-9][0-9]{7,14}$/'],
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ], [
            'phone.required' => __('dictt.required_item', ['name' => __('dictt.phone')]),
            'phone.regex' => __('dictt.phone_international_format'),
        ])->validate();

        return User::create([
            'name' => $this->capitalizeNameWords($input['name']),
            'email' => $input['email'],
            'phone' => $input['phone'],
            'password' => Hash::make($input['password']),
        ]);
    }

    private function capitalizeNameWords(string $name): string
    {
        return preg_replace_callback(
            '/(^|\s)(\p{L})/u',
            static fn (array $matches): string => $matches[1]
                . ($matches[2] === 'i' ? 'İ' : mb_strtoupper($matches[2], 'UTF-8')),
            $name,
        ) ?? $name;
    }
}
