<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\userBalance;
use App\Models\UserWithAds;
use App\Models\Company;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TheoremReachController extends Controller
{
    public function debug(Request $request)
    // 
    {
        Log::info('========== CALLBACK START ==========');

        // Log::info('Incoming Request', [
        //     'method' => $request->method(),
        //     'ip'     => $request->ip(),
        //     'data'   => $request->all(),
        // ]);

        $data = $request->all();

        // debug mode
        if (($data['debug'] ?? 'false') === 'true') {
            Log::warning('DEBUG MODE ENABLED - SKIPPING DB CHANGES');
            return response()->json(['status' => 'debug']);
        }

        try {
            DB::beginTransaction();
            Log::info('DB TRANSACTION STARTED');

            // user_id parsing
            $rawUserId = $data['user_id'] ?? '0-0';
            Log::info('Raw user_id received', ['user_id' => $rawUserId]);

            [$userId, $companyId] = explode('-', $rawUserId);

            Log::info('Parsed IDs', [
                'user_id'    => $userId,
                'company_id' => $companyId
            ]);

            // duplicate tx check
            Log::info('Checking duplicate tx_id', [
                'tx_id' => $data['tx_id'] ?? null
            ]);

            if (UserWithAds::where('tx_id', $data['tx_id'] ?? null)->exists()) {
                Log::warning('DUPLICATE TX FOUND - EXITING');
                DB::rollBack();
                return response()->json(['status' => 'duplicate']);
            }

            // find pending ad
            Log::info('Searching for pending UserWithAds');

            $userAds = UserWithAds::where('user_id', $userId)
                ->where('company_id', $companyId)
                ->where('status', 'pending')
                ->latest()
                ->first();

            if (!$userAds) {
                Log::error('UserAds NOT FOUND', [
                    'user_id'    => $userId,
                    'company_id' => $companyId
                ]);
                DB::rollBack();
                return response()->json(['status' => 'not_found'], 404);
            }

            Log::info('UserAds FOUND', $userAds->toArray());

            // status check
            Log::info('Callback status received', [
                'status' => $data['status'] ?? null
            ]);

            if (($data['status'] ?? '0') != '1') {
                Log::warning('STATUS NOT SUCCESS - REJECTING AD');

                $userAds->update([
                    'status'    => 'rejected',
                    'is_active' => false,
                    'tx_id'     => $data['tx_id'] ?? null,
                ]);

                DB::commit();
                Log::info('Ad REJECTED SUCCESSFULLY');
                return response()->json(['status' => 'rejected']);
            }

            // reward
            $reward = round(floatval($data['reward'] ?? 0), 2);

            Log::info('Reward Calculated', [
                'reward' => $reward
            ]);

            // update ad
            $userAds->update([
                'status'    => 'complete',
                'is_active' => true,
                'tx_id'     => $data['tx_id'],
            ]);

            Log::info('UserAds UPDATED TO COMPLETE');

            // user balance
            Log::info('Updating USER balance');

            $userBalance = userBalance::firstOrCreate(
                ['user_id' => $userId],
                ['balance' => 0]
            );

            Log::info('User balance BEFORE', [
                'balance' => $userBalance->balance
            ]);

            $userBalance->increment('balance', $userAds->amount);

            Log::info('User balance AFTER', [
                'balance' => $userBalance->fresh()->balance
            ]);

            // company balance
            Log::info('Updating COMPANY balance');

            $company = Company::find($companyId);

            if ($company) {
                Log::info('Company balance BEFORE', [
                    'amount' => $company->amount
                ]);

                $company->increment('amount', $reward);

                Log::info('Company balance AFTER', [
                    'amount' => $company->fresh()->amount
                ]);
            } else {
                Log::error('Company NOT FOUND', ['company_id' => $companyId]);
            }

            // affiliate
            Log::info('Processing AFFILIATE money');
            $this->addAffiliateMoney($userId, $reward);

            DB::commit();
            Log::info('DB TRANSACTION COMMITTED');

            Log::info('========== CALLBACK SUCCESS ==========');

            return response()->json(['status' => 'completed']);
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::critical('CALLBACK FAILED', [
                'error' => $e->getMessage(),
                'line'  => $e->getLine(),
                'file'  => $e->getFile(),
            ]);

            return response()->json(['error' => 'server_error'], 500);
        }
    }

    /**
     * Affiliate logic with logs
     */
    private function addAffiliateMoney($userId, $reward)
    {
        Log::info('Affiliate START', compact('userId', 'reward'));

        $user = User::find($userId);
        if (!$user) {
            Log::warning('Affiliate: User NOT FOUND');
            return;
        }

        if (!$user->referred_by) {
            Log::info('Affiliate: No referred_by');
            return;
        }

        $father = User::where('affiliate_code', $user->referred_by)->first();
        if (!$father) {
            Log::warning('Affiliate: Father NOT FOUND');
            return;
        }

        $percent = floatval(
            Setting::where('key', 'affiliate_ads_percent')->value('value') ?? 5
        );

        Log::info('Affiliate percent', ['percent' => $percent]);

        $amount = round(($reward * $percent) / 100, 2);

        Log::info('Affiliate calculated amount', ['amount' => $amount]);

        if ($amount <= 0) {
            Log::warning('Affiliate amount ZERO');
            return;
        }

        $balance = userBalance::firstOrCreate(
            ['user_id' => $father->id],
            ['affiliate_balance' => 0]
        );

        Log::info('Affiliate balance BEFORE', [
            'balance' => $balance->affiliate_balance
        ]);

        $balance->increment('affiliate_balance', $amount);

        Log::info('Affiliate balance AFTER', [
            'balance' => $balance->fresh()->affiliate_balance
        ]);
    }
}
