<?php

namespace App\Http\Requests\Api\Withdraw;

use App\Http\Requests\BaseRequest\BaseRequest;
use Illuminate\Container\Attributes\Log;

class WithdrawRequest extends BaseRequest
{
 public function rules(): array
 {
  $rules = [
   'amount' => ['required', 'numeric', 'min:1'],
   'type_withdraw' => ['required', 'in:affiliate,profit_ads'],
   'method' => ['required', 'in:bank,bank_dollar,wallet,games'],
  ];

  $method = $this->input('method'); // <--- هنا

  if ($method === 'bank' || $method === 'bank_dollar') {
   $rules = array_merge($rules, [
    'name' => ['required', 'string'],
    'phone' => ['required', 'string'],
    'email' => ['required', 'email'],
    'country' => ['required', 'string'],
    'bank_name' => ['required', 'string'],
    'iban' => ['required', 'string'],
    'software' => ['required', 'string'],
   ]);
  }

  if ($method === 'wallet') {
   $rules = array_merge($rules, [
    'address' => ['required', 'string'],
   ]);
  }

  return $rules;
 }


 public function messages(): array
 {
  $typeWithdrawValues = implode(', ', ['affiliate', 'profit_ads']);
  $methodValues = implode(', ', ['bank', 'bank_dollar', 'wallet']);

  return [
   'amount.required' => 'Please enter the amount.',
   'amount.numeric' => 'The amount must be a number.',
   'amount.min' => 'The amount must be greater than 0.',

   'type_withdraw.required' => 'Please select a withdrawal type.',
   'type_withdraw.in' => "The selected withdrawal type is invalid. Allowed values: $typeWithdrawValues.",

   'method.required' => 'Please select a withdrawal method.',
   'method.in' => "The selected withdrawal method is invalid. Allowed values: $methodValues.",

   'name.required' => 'Please enter your name.',
   'phone.required' => 'Please enter your phone number.',
   'email.required' => 'Please enter your email address.',
   'email.email' => 'The email address is not valid.',
   'country.required' => 'Please enter your country.',
   'bank_name.required' => 'Please enter the bank name.',
   'iban.required' => 'Please enter the IBAN number.',
   'software.required' => 'Please enter the software name.',

   'address.required' => 'Please enter the address.',
  ];
 }
}
