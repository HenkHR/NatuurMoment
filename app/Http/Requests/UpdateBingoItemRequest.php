<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class UpdateBingoItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->is_admin ?? false;
    }

    public function rules(): array
    {
        return [
            'label' => ['required', 'string', 'max:255'],
            'points' => ['required', 'integer', 'min:1'],
            'fact' => ['nullable', 'string'],
            'icon' => ['nullable', File::image()->max(2 * 1024)],
            'remove_icon' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'label.required' => 'Label is verplicht.',
            'label.max' => 'Label mag maximaal 255 tekens zijn.',
            'points.required' => 'Punten is verplicht.',
            'points.integer' => 'Punten moet een geheel getal zijn.',
            'points.min' => 'Punten moet minimaal 1 zijn.',
            'icon.image' => 'Icon moet een afbeelding zijn.',
            'icon.mimes' => 'Icon moet een jpeg, png, jpg, gif, svg of webp bestand zijn.',
            'icon.max' => 'Icon mag maximaal 2MB zijn.',
        ];
    }
}
