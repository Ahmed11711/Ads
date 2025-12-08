<?php

namespace App\Http\Requests\Admin\Affiliate;

use App\Http\Requests\BaseRequest\BaseRequest;
use Illuminate\Foundation\Http\FormRequest;

class AllChildRequest extends BaseRequest
{
   
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'affiliate_code' => 'required|exists:users,affiliate_code',
        ];
    }
}
