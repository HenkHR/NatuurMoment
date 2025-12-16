<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLocationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->is_admin ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:locations'],
            'description' => ['required', 'string'],
            'province' => ['required', 'string', 'max:255'],
            'distance' => ['required', 'numeric', 'min:0.1'],
            'image' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
            'game_modes' => ['required', 'array', 'min:1'],
            'game_modes.*' => ['string', 'in:bingo,vragen'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Naam is verplicht.',
            'name.max' => 'Naam mag maximaal 255 tekens zijn.',
            'name.unique' => 'Deze locatie naam bestaat al.',
            'image.required' => 'Afbeelding is verplicht.',
            'image.image' => 'Het bestand moet een afbeelding zijn.',
            'image.mimes' => 'Toegestane formaten: jpeg, png, jpg, gif, webp.',
            'image.max' => 'Afbeelding mag maximaal 2MB zijn.',
            'game_modes.required' => 'Selecteer minimaal één spelmodus.',
            'game_modes.min' => 'Selecteer minimaal één spelmodus.',
        ];
    }
}
