<?php

namespace App\Http\Requests\Integrations\AyetStudio;

use Illuminate\Foundation\Http\FormRequest;

class AyetGenerateOfferLinkRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
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
