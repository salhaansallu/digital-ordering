<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
    //protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    // public function __construct()
    // {
    //     $this->middleware('guest');
    // }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    // protected function validator(array $data)
    // {
    //     return Validator::make($data, [
    //         'name' => ['required', 'string', 'max:255'],
    //         'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
    //         'password' => ['required', 'string', 'min:8', 'confirmed'],
    //     ]);
    // }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    // protected function create(array $data)
    // {

    //     return User::create([
    //         'fname' => $data['name'],
    //         'email' => $data['email'],
    //         'password' => Hash::make($data['password']),
    //     ]);
    // }

    public function register(Request $request)
    {
        $name = sanitize($request->input('name'));
        $email = sanitize($request->input('email'));
        $password = sanitize($request->input('password'));
        $error = array();

        if (!empty($name) && !empty($email) & !empty($password)) {

            $captcha = captchaVerify($request->input('cf-turnstile-response'));
            if (1!=1) {
                $error = array(
                    "error" => 1,
                    "msg" => $captcha->msg
                );
            } else {
                $validate_email = User::where('email', '=', $email)->get();

                if ($validate_email->count() > 0) {
                    $error = array(
                        "error" => 1,
                        "msg" => "Email " . $email . " already used"
                    );
                } else {
                    $user = new User();
                    $user->fname = $name;
                    $user->email = $email;
                    $user->password = Hash::make($password);

                    if ($user->save()) {
                        if (Auth::attempt(["email" => $email, "password" => $password], false)) {
                            $request->session()->regenerate();
                            return redirect('/dashboard');
                        } else {
                            $error = array(
                                "error" => 1,
                                "msg" => "Couldn't login, please click login now and login"
                            );
                        }
                    } else {
                        $error = array(
                            "error" => 1,
                            "msg" => "Something went wrong"
                        );
                    }

                    return view('auth.register')->with(['email' => $email, 'name' => $name, 'error' => $error]);
                }
            }
        } else {
            $error = array(
                "error" => 1,
                "msg" => "All fields are required"
            );
        }

        return view('auth.register')->with(['email' => $email, 'name' => $name, 'error' => $error]);
    }
}
