<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class withdraw extends Model
{
 protected $guarded = [];

 public function user()
 {
  return $this->belongsTo(User::class);
 }

 protected static function boot()
 {
  parent::boot();

  static::creating(function ($withdraw) {
   if (empty($withdraw->transaction_id)) {
    $withdraw->transaction_id = 'TXN-' . strtoupper(uniqid());
   }
  });
 }
}
