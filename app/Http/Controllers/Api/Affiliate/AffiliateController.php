<?php

namespace App\Http\Controllers\Api\Affiliate;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Affiliate\AffiliateResource;
use App\Traits\ApiResponseTrait;

class AffiliateController extends Controller
{
 use ApiResponseTrait;
 public function myAffiliate(Request $request)
 {
  $user = auth()->user();
  $user->affiliate_code;

  $users = User::where('referred_by', $user->affiliate_code)->get();
  $teamCount = $users->count();


  return $this->successResponse(
   [
    'user' => AffiliateResource::collection($users),
    'team_count' => $teamCount,
    'total_profit' => 0,
   ],
   'Affiliate data fetched successfully',
   200
  );
 }
}
