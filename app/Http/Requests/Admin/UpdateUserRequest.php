<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        $user = $this->route('user');
        $userId = is_object($user) ? $user->id : $user;

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['required', Rule::enum(UserRole::class)],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Pole imię i nazwisko jest wymagane.',
            'email.required' => 'Pole adres e-mail jest wymagane.',
            'email.email' => 'Wprowadź poprawny adres e-mail.',
            'email.unique' => 'Podany adres e-mail jest już zajęty.',
            'roles.required' => 'Wybierz przynajmniej jedną rolę dla użytkownika.',
            'roles.min' => 'Wybierz przynajmniej jedną rolę dla użytkownika.',
            'roles.*.enum' => 'Wybrana rola jest nieprawidłowa.',
        ];
    }
}
