<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRouteStopRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'question_text' => ['required', 'string'],
            'option_a' => ['required', 'string', 'max:255'],
            'option_b' => ['required', 'string', 'max:255'],
            'option_c' => ['nullable', 'string', 'max:255'],
            'option_d' => ['nullable', 'string', 'max:255'],
            'correct_option' => [
                'required',
                'in:A,B,C,D',
                function ($attribute, $value, $fail) {
                    if ($value === 'C' && empty($this->option_c)) {
                        $fail('Je kunt C niet als correct antwoord selecteren als optie C niet ingevuld is.');
                    }
                    if ($value === 'D' && empty($this->option_d)) {
                        $fail('Je kunt D niet als correct antwoord selecteren als optie D niet ingevuld is.');
                    }
                },
            ],
            'points' => ['required', 'integer', 'min:1'],
            'sequence' => ['required', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Naam is verplicht.',
            'question_text.required' => 'Vraagtekst is verplicht.',
            'option_a.required' => 'Antwoord A is verplicht.',
            'option_b.required' => 'Antwoord B is verplicht.',
            'correct_option.required' => 'Correct antwoord is verplicht.',
            'correct_option.in' => 'Correct antwoord moet A, B, C of D zijn.',
            'points.required' => 'Punten is verplicht.',
            'points.min' => 'Punten moet minimaal 1 zijn.',
            'sequence.required' => 'Volgorde is verplicht.',
            'sequence.min' => 'Volgorde moet minimaal 0 zijn.',
        ];
    }
}
