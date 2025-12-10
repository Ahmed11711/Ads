<?php

namespace App\Http\Requests\Admin\userWithAds;
use App\Http\Requests\BaseRequest\BaseRequest;
class userWithAdsStoreRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => 'required|integer|exists:users,id',
            'company_id' => 'required|integer|exists:companies,id',
            'amount' => 'required|numeric',
            'is_active' => 'required|integer',
        ];
    }
}
