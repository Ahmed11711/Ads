<?php

namespace App\Http\Requests\Admin\notifications;

use App\Http\Requests\BaseRequest\BaseRequest;

class notificationsStoreRequest extends BaseRequest
{
 public function authorize(): bool
 {
  return true;
 }

 public function rules(): array
 {
  return [
   'user_id' => 'required|integer|exists:users,id',
   'title' => 'required|string|max:255',
   'message' => 'required|string',
   'emails' => 'sometimes|array',
   'emails.*' => 'email',
  ];
 }
}
