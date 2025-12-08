<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLocationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->is_admin ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('locations')->ignore($this->route('location')),
            ],
            'description' => ['nullable', 'string'],
            'province' => ['required', 'string', 'max:255'],
            'duration' => ['required', 'integer', 'min:1'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
            'remove_image' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Naam is verplicht.',
            'name.max' => 'Naam mag maximaal 255 tekens zijn.',
            'name.unique' => 'Deze locatie naam bestaat al.',
        ];
    }
}
