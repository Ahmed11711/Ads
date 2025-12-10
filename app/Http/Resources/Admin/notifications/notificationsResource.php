<?php

namespace App\Http\Resources\Admin\notifications;

use Illuminate\Http\Resources\Json\JsonResource;

class notificationsResource extends JsonResource
{
 public function toArray($request): array
 {
  return [
   'id' => $this->id,
   'user_id' => $this->user_id,
   'username' => $this->user->name ?? null,

   'title' => $this->title,
   'message' => $this->message,
   'is_read' => $this->is_read,
   'created_at' => $this->created_at,
   'updated_at' => $this->updated_at,
  ];
 }
}
