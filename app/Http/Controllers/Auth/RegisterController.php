<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\User;
use Faker\Factory;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

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
    protected $redirectTo = RouteServiceProvider::HOME;

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
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone_number' => ['required', 'string', 'min:17', 'max:17', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        if (0) {
            $faker = Factory::create('ru_RU');
            for ($i = 0; $i < 10; $i++) {
                User::create([
                    'name' => $faker->firstName('male'),
                    's_name' => $faker->lastName,
                    't_name' => $faker->firstName('male') . 'ович',
                    'email' => $faker->email,
                    'phone_number' => $faker->phoneNumber,
                    'location' => $faker->address,
                    'description' => $faker->realText($maxNbChars = 200, $indexSize = 1),
                    'password' => Hash::make('123123123'),
                ]);
            }
        }

        return User::create([
            'name' => $data['name'],
            's_name' => $data['s_name'],
            't_name' => $data['t_name'],
            'email' => $data['email'],
            'phone_number' => $data['phone_number'],
            'location' => $data['location'],
            'description' => $data['description'],
            'password' => Hash::make($data['password']),
        ]);


    }
}
