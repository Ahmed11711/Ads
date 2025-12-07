<?php

namespace App\Http\Controllers\Api\Affiliate;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Affiliate\AffiliateResource;
use App\Models\User;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

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
            ],
            'Affiliate data fetched successfully',
            200
        );
    }
}
