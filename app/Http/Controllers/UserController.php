<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\MobileNumberHelper;
use App\Jobs\SendSMSJob;
use App\Models\PackageName;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use App\Rules\MobileNumber;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Http;
class UserController extends Controller
{
    public function login(Request $request)
    {
        $data = $request->validate([
            'auth' => 'string|required',
            'password' => 'string|required'
        ]);

        if (filter_var($data['auth'], FILTER_VALIDATE_EMAIL)) {
            $authType = 'email';
        } elseif (MobileNumberHelper::checkMobileNumber($data['auth'])) {
            $authType = 'mobile';
            $data['auth'] = MobileNumberHelper::formatMobile($data['auth']);
        } else {
            return response()->json([
                'message' => 'wrong credentials'
            ], 404);
        }

        if (!$user = User::where($authType, $data['auth'])->first()) {
            return response()->json([
                'message' => 'wrong credentials'
            ], 404);
        }
        
        if (!Hash::check($data['password'], $user->password)) {
            return response()->json([
                'message' => 'wrong credentials'
            ], 404);
        }

        $token = '';
        if ($user->is_admin) {
            $token = $user->createToken('token', ['admin'])->plainTextToken;
        } else {
            $token = $user->createToken('token', ['user'])->plainTextToken;
        }

        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'first_name' => 'string',
            'last_name' => 'string',
            'email' => 'email|unique:users|required_without:mobile',
            'password' => 'string|nullable|required_with:email',
            'mobile' => ['string', new MobileNumber, 'nullable', 'unique:users', 'required_without:email'], 
        ]);

            if(isset($data['mobile'])){
                $data['mobile'] = MobileNumberHelper::formatMobile($data['mobile']);
            }

        if (!isset($data['password'])) {
            $data['password'] = Hash::make(config('app.default_password')); // Hash default password
        } else {
            $data['password'] = Hash::make($data['password']); // Hash provided password
        }
        // return response()->json([
        //     "salam"
        // ]);
        $user = User::create($data);

        $token = $user->createToken('token', ['user'])->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token
        ]);
    }

    public function loginOTP(Request $request)
    {
        $data = $request->validate([
            'mobile' => ['string', new MobileNumber, 'required'],
            'package_name' => 'string|required',
             'fcm_token' => 'string',
        ]);

        if (!$packageName = PackageName::where('name', $data['package_name'])->first()) {
            return response()->json([
                'message' => 'package name not found'
            ], 404);
        }

        if (!$user = User::where('mobile', $data['mobile'])->first()) {
            $user = User::create([
                'mobile' => MobileNumberHelper::formatMobile($data['mobile']),
                'password' => config('app.default_password'),
            ]);
        }
        if ($data['mobile'] != config('approo.admin_mobile')) {
            $otpToken = rand(pow(10, 4), pow(10, 5) - 1);
            $user->otpTokens()->create([
                'token' => $otpToken
            ]);

            $verify = Str::random(8);
//             SendSMSJob::dispatch([
//                 'mobile' => $data['mobile'],
//                 'message' => "کد ورود به برنامه: $otpToken
// @{$packageName->web_app_url} #$otpToken
// لغو 11"
//             ]);
            SendSMSJob::dispatch([
                'mobile' => $data['mobile'],
                'token' => "$otpToken"
            ]);
        }


        if (!$user->packageNames()->where('package_names.id', $packageName->id)->exists()) {
            $user->packageNames()->attach($packageName->id, ['tries' => $packageName->tries,
        
                'fcm_token' => $data['fcm_token'] ?? null,]);
        } else {
            $user->packageNames()->updateExistingPivot($packageName->id,[
                'fcm_token' => $data['fcm_token'] ?? null,]);
        }

        return response()->json([
            'message' => 'otp sent',
            'verify_code' => $verify ?? null
        ]);
    }

    public function checkOTP(Request $request)
    {
        $data = $request->validate([
            'mobile' => ['string', new MobileNumber, 'required'],
            'token' => 'string|required',
        ]);

        if (!$user = User::where('mobile', $data['mobile'])->first()) {
            return response()->json([
                'message' => 'user not found'
            ], 404);
        }

        $otpToken = $user->otpTokens()->where('token', $data['token'])->where('created_at', '>', now()->subMinutes(15))->first();
        if (!$otpToken && !($data['mobile'] == config('approo.admin_mobile') && $data['token'] == config('approo.admin_otp'))) {
            return response()->json([
                'message' => 'token not valid'
            ], 400);
        }
        $otpToken?->delete();

        $token = '';
        if ($user->is_admin) {
            $token = $user->createToken('token', ['admin'])->plainTextToken;
        } else {
            $token = $user->createToken('token', ['user'])->plainTextToken;
        }

        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }
    public function updateProfile(Request $request)
    {
        $data = $request->validate([
            'first_name' => 'string|nullable',
            'last_name' => 'string|nullable',
            'birthday' => 'date|nullable',
            'gender' => ['integer', 'nullable', Rule::in(User::GENDERS)],
            'email' => 'email|nullable',
            'mobile' => [new MobileNumber, 'string'],
            'avatar' => 'image'
        ]);

        if (isset($data['email'])) {
            $user = User::where('email', $data['email'])->first();
            if ($user && $data['email'] != auth()->user()->email) {
                return response()->json([
                    'message' => 'user with given email exists'
                ], 400);
            }
        }

        if (isset($data['mobile'])) {
            $user = User::where('mobile', $data['mobile'])->first();
            if ($user && $data['mobile'] != auth()->user()->mobile) {
                return response()->json([
                    'message' => 'user with given email exists'
                ], 400);
            }
        }

        $user = auth()->user();
        if (isset($data['avatar'])) {
            Storage::disk('public')->put("avatars/$user->uuid.png", file_get_contents($data['avatar']->path()));
            $data['avatar'] = "storage/avatars/$user->uuid.png";
        }

        $user->update($data);

        return response()->json([
            'user' => $user,
            'message' => 'user updated'
        ]);
    }
    public function status(Request $request)
    {
        $data = $request->validate([
            'package_name' => 'string|required'
        ]);

        if (!$packageName = PackageName::with('cafeConfig')->where('name', $data['package_name'])->first()) {
            return response()->json([
                'message' => 'package name not found'
            ], 404);
        }

        $user = auth()->user();
        $user->load(['response', 'plans']);

        if ($user->mobile == config('approo.admin_mobile')) {
            $user['products'] = Product::where('package_name_id', $packageName->id)->get();
        } else {
            // if ($packageName->cafeConfig) {
            //     $user['products'] = $user->products()->where('package_name_id', $packageName->id)->get();
            //     foreach ($user['products'] as $key => $product) {
            //         $service = new CafeService($packageName->cafeConfig);
            //         if ($product->pivot->gateway == Transaction::GATEWAYS['cafe'] && $product->pivot->purchase_token && $product->type != Product::TYPES['permanent']) {
            //             $activeSubscriptions = $service->checkSubscription($product, $product->pivot->purchase_token);
            //             if ($activeSubscriptions) {
            //                 $expire = Carbon::createFromTimestamp($activeSubscriptions[0]['validUntilTimestampMsec'] / 1000);
            //                 $user->products()->updateExistingPivot($product, ['expire_at' => $expire->format('Y-m-d H:i:s')]);
            //             } else {
            //                 $user->products()->updateExistingPivot($product, ['expire_at' => now()->format('Y-m-d H:i:s')]);
            //             }
            //         }
            //     }
            // }
            $user['products'] = $user->products()
                ->where('package_name_id', $packageName->id)
                ->where(fn ($q) => $q->where('user_product.expire_at', '>', now()->addHour())->orWhere('user_product.expire_at', null))
                ->get();

            $user['package_name'] = $user->packageNames()
                ->where('package_names.id', $packageName->id)
                ->first();
        }

        return $user;
    }
    public function try(Request $request, $name) {
        $user = auth()->user();

        if (!$packageName = $user->packageNames()->where('name', $name)->first()) {
            return response()->json([
                'message' => 'package name not found'
            ], 404);
        }

        if ($packageName->pivot->tries <= 0) {
            return response()->json([
                'message' => 'no more tries'
            ], 400);
        }

        $packageName->pivot->tries->decrement();

        return response()->json([
            'message' => 'success'
        ]);
    }

    public function oauthCallback(Request $request)
    {
        return dd(Socialite::driver('google')->stateless()->user());
    } 
    
       public function googleLogin(Request $request)
    {
        $data = $request->validate([
            'access_token' => 'string|required',
            'package_name' => 'string|required',
        ]);

        if (!$packageName = PackageName::where('name', $data['package_name'])->first()) {
            return response()->json([
                'message' => 'package name not found'
            ], 404);
        }

        $response = Http::get("https://www.googleapis.com/oauth2/v3/userinfo", [
            'access_token' => $data['access_token']
        ])->json();

        if (!isset($response['email'])) {
            return response()->json([
                'message' => 'invalid token'
            ], 400);
        }
        if (!$user = User::where('email', $response['email'])->first()) {
            $user = User::create([
                'email' => $response['email'],
                'password' => config('app.default_password'),
            ]);

            if (!$user->packageNames()->where('package_names.id', $packageName->id)->exists()) {
                $user->packageNames()->attach($packageName->id, [
                    'tries' => $packageName->tries,
                    'source' => $data['source'] ?? 0,
                ]);
            }
        }

        $token = $user->createToken('token', ['user'])->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }
}
