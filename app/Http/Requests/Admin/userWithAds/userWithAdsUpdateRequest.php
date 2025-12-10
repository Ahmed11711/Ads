<?php

namespace App\Http\Requests\Admin\userWithAds;
use App\Http\Requests\BaseRequest\BaseRequest;
class userWithAdsUpdateRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => 'sometimes|required|integer|exists:users,id',
            'company_id' => 'sometimes|required|integer|exists:companies,id',
            'amount' => 'sometimes|required|numeric',
            'is_active' => 'sometimes|required|integer',
        ];
    }
}
