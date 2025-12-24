<?php

namespace App\Http\Controllers\Api\Company;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserAdsRequest;
use App\Http\Resources\myAdsResource;
use App\Models\Company;
use App\Models\setting;
use App\Models\userBalance;
use App\Models\userWithAds;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompanyCOntroller extends Controller
{
    use ApiResponseTrait;
    public function index(Request $request)
    {
        $query = Company::where('status', 'active');

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        } else {
            $query->where('type', '!=', 'tasks');
        }

        $companies = $query->get();

        return $this->successResponse(
            $companies,
            'Companies retrieved successfully',
            200
        );
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
        $data = $request->validated();

        $company = Company::findOrFail($data['company_id']);

        $priceAds = setting::where('key', 'price_ads')->value('value');

        $userAds = UserWithAds::create([
            'user_id'    => $user->id,
            'company_id' => $company->id,
            'amount'     => $priceAds,
            'status'     => 'pending',
            'is_active'  => true,
            'type'       => $company->type,
        ]);

        if (!empty($data['company_profit'])) {
            // تحديث رصيد الشركة
            $company->amount += $data['company_profit'];
            $company->save();

            // تحديث حالة الإعلان
            $userAds->status = "complete";
            $userAds->save();


            // updateOrCreate على user balance
            userBalance::updateOrCreate(
                ['user_id' => $user->id],
                ['balance' => DB::raw("balance + $priceAds")]
            );
        }

        return $this->successResponse(
            new MyAdsResource($userAds),
            'Ad created successfully',
            201
        );
    }
}
