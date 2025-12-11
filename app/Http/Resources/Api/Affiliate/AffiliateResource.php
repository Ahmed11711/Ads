<?php

namespace App\Http\Resources\Api\Affiliate;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AffiliateResource extends JsonResource
{

 public function toArray(Request $request): array
 {
  return [
   'id' => $this->id,
   'name' => $this->name,
   'email' => $this->email,
   'active' => $this->active ?? true,
   'affiliate_code' => $this->affiliate_code,
   'created_at' => $this->created_at,
   'updated_at' => $this->updated_at,
   'last_login_at' => $this->last_login_at ??  $this->updated_at,
  ];
 }
}
