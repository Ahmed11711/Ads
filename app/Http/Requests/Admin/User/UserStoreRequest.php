<?php

namespace App\Http\Requests\Admin\User;

use App\Http\Requests\BaseRequest\BaseRequest;

class UserStoreRequest extends BaseRequest
{
 public function authorize(): bool
 {
  return true;
 }

 public function rules(): array
 {
  return [
   'name' => 'required|string|max:255',
   'email' => 'required|string|max:255|unique:users,email',
   'email_verified_at' => 'nullable|date',
   'password' => 'required|string|max:255',
   'affiliate_code' => 'required|string|max:255|unique:users,affiliate_code',
   'otp' => 'nullable|string|max:6',
   'is_verified' => 'nullable|integer',
   'remember_token' => 'nullable|string|max:100',
   'bank_name' => 'nullable|string|max:250',
   'iban' => 'nullable|string',
   'wallet' => 'nullable|string',
   'phone' => 'nullable|string',

  ];
 }
}
