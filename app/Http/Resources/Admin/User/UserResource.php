<?php

namespace App\Http\Resources\Admin\User;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
 public function toArray($request): array
 {
  return [
   'id' => $this->id,
   'name' => $this->name,
   'email' => $this->email,
   'email_verified_at' => $this->email_verified_at,
   'password' => $this->password,
   'affiliate_code' => $this->affiliate_code,
   'referred_by' => $this->referred_by,
   'otp' => $this->otp,
   'is_verified' => $this->is_verified,
   'remember_token' => $this->remember_token,
   'phone' => $this->phone,
   'address' => $this->address,
   'profile_image' => $this->profile_image,
   'role' => $this->role,
   'balance' => $this->balanceRecord?->balance ?? 0,
   'affiliate_balance' => $this->balanceRecord?->affiliate_balance ?? 0,

   'age' => $this->age ?? "",
   'gender' => $this->gender ?? "",
   'country' => $this->country ?? "",
   "bank_name" => $this->bank_name ?? "",
   "iban" => $this->iban ?? "",
   "wallet" => $this->wallet ?? "",
   'last_login_at' => $this->updated_at,
   'created_at' => $this->created_at,
   'updated_at' => $this->updated_at,
   'referrals_count' => $this->referrals_count ?? 0,

  ];
 }
}
