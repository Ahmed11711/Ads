<?php

namespace App\Http\Requests\Admin\User;

use App\Http\Requests\BaseRequest\BaseRequest;

class UserUpdateRequest extends BaseRequest
{
 public function authorize(): bool
 {
  return true;
 }

 public function rules(): array
 {
  return [
   'name' => 'sometimes|required|string|max:255',
   'email' => 'sometimes|required|string|max:255|unique:users,email,' . $this->route('user') . ',id',
   'email_verified_at' => 'nullable|sometimes|date',
   'password' => 'sometimes|required|string|max:255',
   'affiliate_code' => 'sometimes|required|string|max:255|unique:users,affiliate_code,' . $this->route('user') . ',id',
   'otp' => 'nullable|sometimes|string|max:6',
   'is_verified' => 'sometimes|required|integer',
   'remember_token' => 'nullable|sometimes|string|max:100',
   'balance' => 'nullable|sometimes',
   'affiliate_balance' => 'nullable|sometimes',
   'bank_name' => 'nullable|string|max:250',
   'iban' => 'nullable|string',
   'wallet' => 'nullable|string',
   'phone' => 'nullable|string',
   'gender' => 'nullable|string',
   'age' => 'nullable|string',
   'country' => 'nullable|string'
  ];
 }
}
