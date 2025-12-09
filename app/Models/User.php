<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;


class User extends Authenticatable implements JWTSubject
{
 /** @use HasFactory<\Database\Factories\UserFactory> */
 use HasFactory, Notifiable;

 /**
  * The attributes that are mass assignable.
  *
  * @var list<string>
  */
 protected $fillable = [
  'name',
  'email',
  'password',
  'affiliate_code',
  'otp',
  'is_verified',
  'phone',
  'profile_image',
  'address',
  'role',
  'fcm_token',
  'referred_by',

 ];
 /**
  * The attributes that should be hidden for serialization.
  *
  * @var list<string>
  */
 protected $hidden = [
  'password',
  'remember_token',
 ];

 /**
  * Get the attributes that should be cast.
  *
  * @return array<string, string>
  */
 protected function casts(): array
 {
  return [
   'email_verified_at' => 'datetime',
   'password' => 'hashed',
  ];
 }

 public function getJWTIdentifier()
 {
  return $this->getKey();
 }

 public function getJWTCustomClaims()
 {
  return [];
 }

 public function balance()
 {
  return $this->hasOne(UserBalance::class, 'user_id');
 }
 protected static function boot()
 {
  parent::boot();

  static::creating(function ($user) {
   // لو مش موجود affiliate_code، اعمل واحد جديد
   if (empty($user->affiliate_code)) {
    $user->affiliate_code = self::generateAffiliateCode();
   }

   // لو مش موجود otp، اعمل واحد جديد
   if (empty($user->otp)) {
    $user->otp = self::generateOtp(6);
   }
  });
 }

 // دالة لتوليد OTP
 private static function generateOtp($length = 6)
 {
  $otp = '';
  for ($i = 0; $i < $length; $i++) {
   $otp .= mt_rand(0, 9);
  }
  return $otp;
 }
}
