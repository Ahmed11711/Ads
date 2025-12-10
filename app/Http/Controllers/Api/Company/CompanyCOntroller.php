<?php

namespace App\Http\Controllers\Api\Company;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserAdsRequest;
use App\Models\Company;
use App\Models\setting;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class CompanyCOntroller extends Controller
{
 use ApiResponseTrait;
 public function index()
 {
  $company = Company::get();
  return $this->successResponse($company, 'All Companies', 200);
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

  return $this->successResponse($data, 'success to store ads');
 }
}
