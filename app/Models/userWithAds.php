<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class userWithAds extends Model
{
 protected $table = 'user_ads';

 public function user()
 {
  return $this->belongsTo(User::class, 'user_id');
 }


 public function company()
 {
  return $this->belongsTo(Company::class, 'company_id');
 }
}
