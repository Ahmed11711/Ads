<?php

namespace App\Http\Controllers\Api\Ads;

use App\Http\Controllers\Controller;
use App\Http\Resources\myAdsResource;
use App\Models\userWithAds;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class MyHistroryController extends Controller
{
 use ApiResponseTrait;

 public function index(Request $request)
 {
  $user = auth()->user();

  $userAds = userWithAds::where('user_id', $user->id)
   ->when($request->type, function ($query) use ($request) {
    $query->where('type', $request->type);
   }, function ($query) {
    $query->whereIn('type', ['ads', 'survey']);
   })
   ->paginate(10);

  // حساب الـ summary
  $summary = [
   'daily_income' => $userAds->sum('amount'), // مجموع المبالغ المعروضة في الصفحة الحالية أو حسب حاجتك
   'total_watch_today' => $userAds->count(), // عدد الإعلانات في الصفحة الحالية أو حسب حاجتك
  ];

  return response()->json([
   'status' => true,
   'message' => 'Ads history retrieved successfully',
   'data' => myAdsResource::collection($userAds),
   'meta' => [
    'current_page' => $userAds->currentPage(),
    'last_page' => $userAds->lastPage(),
    'per_page' => $userAds->perPage(),
    'total' => $userAds->total(),
   ],
   'summary' => $summary,
  ]);
 }
}
