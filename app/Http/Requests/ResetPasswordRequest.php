<?php

namespace App\Http\Requests;

use App\Http\Requests\BaseRequest\BaseRequest;
use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends BaseRequest
{


 /**
  * Get the validation rules that apply to the request.
  *
  * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
  */
 public function rules(): array
 {
  return [
   'email' => 'required|email|exists:users,email',
   'otp' => 'required|string',
   'password' => 'required|string|min:6|confirmed',
  ];
 }
}
