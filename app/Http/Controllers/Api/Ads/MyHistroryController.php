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

  return $this->successResponsePaginate(
   myAdsResource::collection($userAds),
   "Ads history retrieved successfully"
  );
 }
}
