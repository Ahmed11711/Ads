<?php

namespace App\Http\Requests\Admin\Company;
use App\Http\Requests\BaseRequest\BaseRequest;
class CompanyUpdateRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'logo' => 'nullable|sometimes|string|max:255',
            'type' => 'sometimes|required|in:ads,tasks,survey,games,other',
            'status' => 'sometimes|required|in:active,inactive',
            'description' => 'nullable|sometimes|string',
            'amount' => 'sometimes|required|string|max:255',
            'url' => 'sometimes|required|string',
        ];
    }
}
