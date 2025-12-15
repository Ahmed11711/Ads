<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\loginRequest;
use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Http\Requests\Api\Auth\resendOtpRequest;
use App\Http\Requests\Api\Auth\ScoialLoginRequest;
use App\Http\Requests\Api\Auth\UPdateProfileRequest;
use App\Http\Requests\Api\Auth\VerifyAffiliateRequest;
use App\Http\Requests\Api\Auth\VerifyEmailRequest;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Models\User;
use App\Models\userBalance;
use App\Models\withdraw;
use App\Traits\ApiResponseTrait;
use App\Traits\OTPTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Kreait\Firebase\Factory;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;


class AuthController extends Controller
{
 use ApiResponseTrait, OTPTrait;

 public function login(loginRequest $request)
 {
  $data = $request->validated();

  if (!empty($data['provider']) && !empty($data['uid'])) {
   // Social Login
   $user = User::where('email', $data['email'])->first();

   if (!$user) {
    $user = User::create([
     'name' => $data['name'] ?? 'User',
     'email' => $data['email'],
     'provider' => $data['provider'],
     'firebase_uid' => $data['uid'],
     'avatar' => $data['photo'] ?? null,
     'password' => bcrypt(Str::random(20)), // dummy password
     'email_verified_at' => now(),
     'last_login_at' => now(),

    ]);
   }
   $token = JWTAuth::fromUser($user);
  } else {
   // Normal login
   $credentials = $request->only('email', 'password');
   try {
    if (!$token = JWTAuth::attempt($credentials)) {
     return $this->errorResponse('Invalid credentials', 401);
    }
    $user = auth()->user();
   } catch (JWTException $e) {
    return $this->errorResponse('Could not create token', 500);
   }
  }

  if (!empty($data['fcm_token'])) {
   $user->fcm_token = $data['fcm_token'];
   $user->save();
  }

  $userBalance = UserBalance::where('user_id', $user->id)->first();
  $user->balance = $userBalance->balance ?? 0;
  $user->balance_affiliate = $userBalance->affiliate_balance ?? 0;
  $user->myLink = config('app.url') . '/' . $user->affiliate_code;
  $user->count = User::where('referred_by', $user->affiliate_code)->count() ?? 0;
  $user->count_withdraw_pending = withdraw::where('user_id', $user->id)->where('status', 'pending')->count() ?? 0;
  $user->token = $token;
  $user->otp = $user->otp ?? null;

  return $this->successResponse([
   'user' => $user,
  ], 'Login successful', 200);
 }


 public function register(RegisterRequest $request)
 {
  $data = $request->validated();

  // إذا social login
  if (!empty($data['provider']) && !empty($data['uid'])) {
   $user = User::updateOrCreate(
    ['email' => $data['email']],
    [
     'name' => $data['name'],
     'provider' => $data['provider'],
     'firebase_uid' => $data['uid'],
     'avatar' => $data['photo'] ?? null,
     'password' => bcrypt(Str::random(20)), // dummy password
     'email_verified_at' => now(),
     'last_login_at' => now(),
     'gender' => $data['gender']  ?? null,
     'age' => $data['age'] ?? null,
    ]
   );
  } else {
   //    
   $data['password'] = bcrypt($data['password']);
   $user = User::create($data);
  }

  $token = JWTAuth::fromUser($user);
  $user->otp = $user->otp ?? null;


  return $this->successResponse([
   'token' => $token,
   'user' => $user,
  ], 'User registered successfully', 201);
 }

 private function generateAffiliateCode()
 {
  do {
   $code = 'AFF-' . strtoupper(Str::random(6));
  } while (User::where('affiliate_code', $code)->exists());
  return $code;
 }

 public function logout(Request $request)
 {
  try {
   JWTAuth::invalidate(JWTAuth::getToken());
   return $this->successResponse([], 'User logged out successfully', 200);
  } catch (JWTException $e) {
   return $this->errorResponse('Could not invalidate token', 500);
  }
 }

 public function me(Request $request)
 {
  $user = auth()->user();
  $userBalance = UserBalance::where('user_id', $user->id)->first();
  $user->balance = $userBalance->balance ?? 0;
  $user->balance_affiliate = $userBalance->affiliate_balance ?? 0;
  $user->myLink = config('app.url') . '/' . $user->affiliate_code;
  $user->count_team = User::where('referred_by', $user->affiliate_code)->count() ?? 0;
  $user->count_withdraw_pending = withdraw::where('user_id', $user->id)->where('status', 'pending')->count() ?? 0;
  $token = JWTAuth::fromUser($user);
  $user->token = $token;
  $user->profile_image
   ? 'http://astar.zayamrock.com/storage/' . ltrim($user->profile_image, '/')
   : null;



  return $this->successResponse([
   'user' => $user,
  ], 'User details fetched successfully', 200);
 }
 public function verifyEmail(VerifyEmailRequest $request)
 {
  $validatedData = $request->validated();

  $email = $validatedData['email'];
  $otp   = $validatedData['otp'];

  $user = User::where('email', $email)->first();

  if (!$user || $user->otp !== $otp) {
   return $this->errorResponse('Invalid OTP', 400);
  }

  $user->update([
   'email_verified_at' => now(),
   'is_verified' => true,
   'otp' => null,
  ]);

  return $this->successResponse([], 'Email verified successfully', 200);
 }

 public function verifyAffiliate(VerifyAffiliateRequest $request)
 {
  $validatedData = $request->validated();
  $code = $validatedData['affiliate_code'];

  $affiliateUser = User::where('affiliate_code', $code)->first();


  if (!$affiliateUser) {
   return $this->errorResponse('Affiliate code is invalid', 404);
  }
  if ($code == $affiliateUser->affiliate_code) {
   return $this->errorResponse('Affiliate code is invalid', 404);
  }


  $balance = UserBalance::firstOrCreate(
   ['user_id' => $affiliateUser->id],
   ['balance' => 0,]
  );

  $balance->increment('balance', 10);
  $user = User::where('email', $validatedData['email'])->first();
  $user->referred_by = $affiliateUser->affiliate_code;
  $user->save();

  return $this->successResponse([], 'Affiliate code is valid, balance updated', 200);
 }



 public function resendOtp(resendOtpRequest $request)
 {
  $user = User::where('email', $request->email)->first();

  $otp = $this->generateOtp(6);
  $user->update(['otp' => $otp]);

  // Here you would typically send the OTP via email or SMS

  return $this->successResponse([$otp], 'OTP resent successfully', 200);
 }

 public function socailLogin(ScoialLoginRequest $request)
 {
  $data = $request->validated();

  // Firebase verify
  $factory = (new Factory)
   ->withServiceAccount(storage_path('app/firebase/firebase_credentials.json'));

  return  $auth = $factory->createAuth();



  try {
   $verifiedToken = $auth->verifyIdToken($request->id_token);
  } catch (\Exception $e) {
   return response()->json([
    'message' => 'Invalid Firebase token',
   ], 401);
  }

  $firebaseUser = $verifiedToken->claims()->all();

  return $firebaseUser;
  // Extract data
  $email = $firebaseUser['email'] ?? null;
  $name  = $firebaseUser['name'] ?? null;
  $uid   = $firebaseUser['sub']; // Firebase UID
  $photo = $firebaseUser['picture'] ?? null;

  if (!$email) {
   return response()->json(['message' => 'Email not found'], 400);
  }

  // Create or update user
  $user = User::updateOrCreate(
   ['email' => $email],
   [
    'name' => $name,
    'firebase_uid' => $uid,
    'avatar' => $photo,
    'password' => bcrypt(Str::random(20)), // dummy password
    'email_verified_at' => now(),
   ]
  );

  // Create API token (Sanctum)
  $token = $user->createToken('mobile')->plainTextToken;

  return response()->json([
   'user' => $user,
   'token' => $token,
   'token_type' => 'Bearer',
  ]);
 }
 public function forgotPassword(ForgotPasswordRequest $request)
 {
  $user = User::where('email', $request->email)->first();

  $otp = rand(100000, 999999);
  $user->otp = $otp;
  $user->save();

  return $this->successResponse($otp, 'OTP sent successfully');
 }

 public function resetPassword(ResetPasswordRequest $request)
 {
  $user = User::where('email', $request->email)->first();

  if ($user->otp !== $request->otp) {
   return $this->successResponse("Invalid OTP");
  }



  $user->password = Hash::make($request->password);
  $user->otp = null;
  $user->save();

  return $this->successResponse("Password reset successfully");
 }

 public function getBalance(Request $request)
 {
  $user = auth()->user();

  $balance = userBalance::where('user_id', $user->id)->first();

  $netBalance = $balance?->balance ?? 0;              // لو مش موجود نرجع صفر
  $affiliateBalance = $balance?->affiliate_balance ?? 0;
  $totalWithdrawn = withdraw::where('user_id', $user->id)
   ->where('status', 'confirmed')
   ->sum('amount');
  return $this->successResponse([
   'balance' => $netBalance,
   'affiliate_balance' => $affiliateBalance,
   'total_withdraw' => $totalWithdrawn,
  ], 'User balance retrieved successfully');
 }

 public function updateProfile(UpdateProfileRequest $request)
 {
  $user = auth()->user();
  $validatedData = $request->validated();

  $validatedData = $this->uploadProfileImage($request, $validatedData, $user);

   if (isset($validatedData['password'])) {
   $validatedData['password'] = bcrypt($validatedData['password']);
  }

  $user->update($validatedData);

  return $this->successResponse([
   'user' => $user,
  ], 'Profile updated successfully', 200);
 }


 protected function uploadProfileImage(Request $request, array $validated, $user = null): array
 {
  if (!$request->hasFile('profile_image')) {
   return $validated;
  }

  $file = $request->file('profile_image');
  $filename = time() . '_' . $file->getClientOriginalName();

  // رفع مباشر داخل public/uploads/profile_images
  $destinationPath = public_path('uploads/profile_images');
  $file->move($destinationPath, $filename);

  // حذف الصورة القديمة لو موجودة
  if ($user && $user->profile_image) {
   $oldPath = public_path('uploads/profile_images/' . basename($user->profile_image));
   if (file_exists($oldPath)) {
    unlink($oldPath);
   }
  }

  // 👇 تحديد الدومين حسب البيئة
  if (app()->environment('local')) {
   $domain = 'http://localhost:8000';
  } else {
   $domain = 'https://ahmed.api.regtai.com';
  }
//   $domain = 'https://api.regtai.com';
  $domain = 'https://api.regtai.com/ahmed/public';

  // تخزين Full URL
  $validated['profile_image'] = $domain . '/uploads/profile_images/' . $filename;

  return $validated;
 }
}
