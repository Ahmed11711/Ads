<?php

namespace App\Http\Controllers\Api\Ads;

use App\Http\Controllers\Controller;
use App\Models\userWithAds;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class MyHistroryController extends Controller
{
 use ApiResponseTrait;

 public function index()
 {
  $user = auth()->user();
  $userads = userWithAds::where('user_id', $user->id)->get();
  $data = [
   [
    'id' => 1,
    'user_name' => "ahmed",
    'created_at' => now()->toDateTimeString(),
    'ads_id' => 101,
    'company_name' => 'Company A',
    'amount' => 150.50,
    'desc' => 'First ad description',
    'status' => 'pending',
   ],
   [
    'id' => 2,
    'user_name' => "awad",

    'created_at' => now()->subDay()->toDateTimeString(),
    'ads_id' => 102,
    'company_name' => 'Company B',
    'amount' => 200.00,
    'desc' => 'Second ad description',
    'status' => 'reject',
   ],
   [
    'id' => 3,
    'user_name' => "ahmed",

    'created_at' => now()->subDays(2)->toDateTimeString(),
    'ads_id' => 103,
    'company_name' => 'Company C',
    'amount' => 300.75,
    'desc' => 'Third ad description',
    'status' => 'complete',
   ],
   [
    'id' => 4,
    'user_name' => "ss",

    'created_at' => now()->subDays(3)->toDateTimeString(),
    'ads_id' => 104,
    'company_name' => 'Company D',
    'amount' => 450.00,
    'desc' => 'Fourth ad description',
    'status' => 'pending',
   ],
  ];

  return $this->successResponse($data, "Ads history retrieved successfully");
 }
}
