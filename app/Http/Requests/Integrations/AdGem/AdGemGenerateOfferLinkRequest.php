<?php

namespace App\Http\Requests\Integrations\AdGem;

use Illuminate\Foundation\Http\FormRequest;

class AdGemGenerateOfferLinkRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => 'required|string',
            'offer_id' => 'required|string',
        ];
    }
}
