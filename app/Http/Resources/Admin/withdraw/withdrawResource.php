<?php

namespace App\Http\Resources\Admin\withdraw;

use Illuminate\Http\Resources\Json\JsonResource;

class withdrawResource extends JsonResource
{
 public function toArray($request): array
 {
  return [
   'id' => $this->id,
   'user_id' => $this->user_id,
   'username' => $this->user->name ?? null,
   'name' => $this->name,
   'phone' => $this->phone,
   'email' => $this->email,
   'country' => $this->country,
   'bank_name' => $this->bank_name,
   'iban' => $this->iban,
   'software' => $this->software,
   'status' => $this->status,
   'amount' => $this->amount,
   'method' => $this->method,
   'note' => $this->note,
   'type_withdraw' => $this->type_withdraw,
   'address' => $this->address,
   'transaction_id' => $this->transaction_id ?? null,
   'created_at' => $this->created_at,
   'updated_at' => $this->updated_at,
  ];
 }
}
