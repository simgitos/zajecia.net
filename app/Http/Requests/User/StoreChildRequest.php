<?php

declare(strict_types=1);

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class StoreChildRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'birth_date' => ['required', 'date', 'before_or_equal:today'],
            'pesel' => ['nullable', 'string', 'digits:11'],
            'parent_comment' => ['nullable', 'string', 'max:1000'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'consents' => ['nullable', 'array'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Imię i nazwisko dziecka jest wymagane.',
            'birth_date.required' => 'Data urodzenia jest wymagana.',
            'birth_date.date' => 'Wprowadź poprawną datę.',
            'birth_date.before_or_equal' => 'Data urodzenia nie może być z przyszłości.',
            'pesel.digits' => 'Numer PESEL musi składać się z dokładnie 11 cyfr.',
        ];
    }
}
