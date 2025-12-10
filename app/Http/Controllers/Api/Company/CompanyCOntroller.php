<?php

namespace App\Http\Controllers\Api\Company;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserAdsRequest;
use App\Http\Resources\myAdsResource;
use App\Models\Company;
use App\Models\setting;
use App\Models\userWithAds;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class CompanyCOntroller extends Controller
{
 use ApiResponseTrait;
 public function index(Request $request)
 {
  $query = Company::where('status', 'active');

  if ($type = $request->input('type')) {
   $query->where('type', $type);
  }

  $companies = $query->get();

  return $this->successResponse($companies, 'Companies retrieved successfully', 200);
 }


 public function setting()
 {
  $settings = setting::get();
  $settingsObject = $settings->pluck('value', 'key')->toArray();

  return $this->successResponse($settingsObject, 'All settings', 200);
 }
 public function userAds(UserAdsRequest $request)
 {
  $user = auth()->user();
  $data = $request->all();
  $userAds = userWithAds::create([
   'user_id' => $user->id,
   'company_id' => $data['company_id'],
   'amount' => 0.1,
   // 'status' => 'pending',
   'is_active' => '1'

  ]);

  return $this->successResponse(myAdsResource::collection($userAds));

  return $this->successResponse($userAds, 'success to store ads');
 }
}
