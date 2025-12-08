<?php

namespace App\Http\Requests\Integrations;

use Illuminate\Foundation\Http\FormRequest;

class CpxTriggerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function prepareForValidation()
    {
        $this->merge([
            'ext_user_id' => $this->ext_user_id ?? $this->user()->id,
            'ip_user' => $this->ip_user ?? $this->ip(),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'ext_user_id' => 'required|string',
            'ip_user' => 'required|ip',
        ];
    }
}
