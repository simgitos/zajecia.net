<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendNotificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'target_type' => ['required', 'string', 'in:group,single_user,instructors,all_parents'],
            'course_id'   => ['nullable', 'required_if:target_type,group', 'exists:courses,id'],
            'user_id'     => ['nullable', 'required_if:target_type,single_user', 'exists:users,id'],
            'title'       => ['required', 'string', 'max:255'],
            'message'     => ['required', 'string', 'max:5000'],
            'type'        => ['required', 'string', 'in:info,warning,success,danger'],
            'url'         => ['nullable', 'string', 'max:255'],
            'action_text' => ['nullable', 'string', 'max:100'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'target_type.required'     => 'Wybierz grupę odbiorców powiadomienia.',
            'target_type.in'           => 'Niepoprawny typ odbiorców.',
            'course_id.required_if'    => 'Wybierz grupę zajęciową.',
            'course_id.exists'         => 'Wybrana grupa zajęciowa nie istnieje.',
            'user_id.required_if'      => 'Wybierz użytkownika, do którego chcesz wysłać powiadomienie.',
            'user_id.exists'           => 'Wybrany użytkownik nie istnieje.',
            'title.required'           => 'Temat powiadomienia jest wymagany.',
            'title.max'                => 'Temat może mieć maksymalnie 255 znaków.',
            'message.required'         => 'Treść powiadomienia jest wymagana.',
            'message.max'              => 'Treść może mieć maksymalnie 5000 znaków.',
            'type.required'            => 'Wybierz typ / priorytet powiadomienia.',
            'type.in'                  => 'Niepoprawny typ powiadomienia.',
        ];
    }
}
