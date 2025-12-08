<?php

namespace App\Http\Requests\Admin\Company;
use App\Http\Requests\BaseRequest\BaseRequest;
class CompanyStoreRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'logo' => 'nullable|string|max:255',
            'type' => 'required|in:ads,tasks,survey,games,other',
            'status' => 'required|in:active,inactive',
            'description' => 'nullable|string',
            'amount' => 'required|string|max:255',
            'url' => 'required|string',
        ];
    }
}
