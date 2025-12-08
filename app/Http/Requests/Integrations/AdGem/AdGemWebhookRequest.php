<?php

namespace App\Http\Requests\Integrations\AdGem;

use Illuminate\Foundation\Http\FormRequest;

class AdGemWebhookRequest extends FormRequest
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
            'app_id' => 'required|string',
            'user_id' => 'required|string',
            'transaction_id' => 'required|string',
            'goal_name' => 'required|string',
            'offer_id' => 'required|string',
        ];
    }
}
