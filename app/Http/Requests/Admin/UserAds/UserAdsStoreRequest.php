<?php

namespace App\Http\Requests\Admin\UserAds;
use App\Http\Requests\BaseRequest\BaseRequest;
class UserAdsStoreRequest extends BaseRequest
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
