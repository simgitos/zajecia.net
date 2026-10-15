<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSeasonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nazwa sezonu/semestru jest wymagana.',
            'start_date.required' => 'Data rozpoczęcia jest wymagana.',
            'start_date.date' => 'Podaj prawidłową datę rozpoczęcia.',
            'end_date.required' => 'Data zakończenia jest wymagana.',
            'end_date.date' => 'Podaj prawidłową datę zakończenia.',
            'end_date.after_or_equal' => 'Data zakończenia nie może być wcześniejsza niż data rozpoczęcia.',
        ];
    }
}
