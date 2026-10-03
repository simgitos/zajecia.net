<?php

declare(strict_types=1);

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class EnrollChildRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'child_id' => ['required', 'exists:children,id'],
            'course_id' => ['required', 'exists:courses,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'child_id.required' => 'Wybór dziecka jest wymagany.',
            'child_id.exists' => 'Wybrane dziecko nie istnieje.',
            'course_id.required' => 'Wybór zajęć jest wymagany.',
            'course_id.exists' => 'Wybrane zajęcia nie istnieją.',
        ];
    }
}
