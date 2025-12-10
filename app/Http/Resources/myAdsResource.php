<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class myAdsResource extends JsonResource
{
 /**
  * Transform the resource into an array.
  *
  * @return array<string, mixed>
  */
 public function toArray(Request $request): array
 {
  return [
   'id' => $this->id,
   'user_name' => $this->user->name ?? null,
   'ads_id' => $this->ads_id ?? null,
   'company_name' => $this->company->name ?? null,
   'amount' => $this->amount,
   'status' => $this->status,
   'type' => $this->type ?? null,
  ];
 }
}
