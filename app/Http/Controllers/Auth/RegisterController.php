<?php

namespace App\Http\Controllers\Auth;

use App\AntiPhishingCode;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use App\Coin;
use Illuminate\Support\Facades\Cache;
use App\Wallet;
use App\UserIp;
use Illuminate\Http\Request;
class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/emailConfirm';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|string|max:30',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'captcha'=>'required|captcha',
            'terms_of_service'=>'accepted',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  Request $request
     * @return \App\User
     */
    protected function create(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $data = $request->all();
            $ip = ip2long($request->server("REMOTE_ADDR"));
            $tz = "UTC";
            if (isset($data['tz']))
            {
                if (in_array($data['tz'], timezone_identifiers_list()))
                {
                    $tz = $data['tz'];
                }
            }
            $user=User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'timezone' => $tz
            ]);
            $coins = Cache::remember('Coins',60, function () {
                return Coin::all();
            });
            foreach ($coins as $coin) {
                Wallet::create(["coin_id" => $coin->id, "user_id" => $user->id]);
            }
            UserIp::create(['user_id'=>$user->id,'ip'=>$ip]);
            AntiPhishingCode::create(['user_id'=>$user->id,'code'=>rand(1,100000)]);
            return $user;
        },1);

    }
}
