<?php

namespace App\Http\Requests\Api\Auth;

use App\Http\Requests\BaseRequest\BaseRequest;

class loginRequest extends BaseRequest
{
 public function rules(): array
 {
  return [
   'email' => 'required|email|exists:users,email',
   'password' => 'required_without:provider|string|min:6', // مطلوب لو مش Social Login
   'provider' => 'nullable|string|in:google,facebook,apple',
   'uid' => 'nullable|string', // موجود لو Social Login
   'name' => 'nullable|string|max:255', // لو Social Login وجاي الاسم من الفرونت
   'photo' => 'nullable|string', // رابط الصورة لو موجود
   'fcm_token' => 'nullable|string',
  ];
 }
}
