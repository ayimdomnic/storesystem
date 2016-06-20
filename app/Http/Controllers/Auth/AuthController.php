<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Validator;
use Auth;
use Input;
use App\Mailers\AppMailers;
use App\Category;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware($this->guestMiddleware(), ['except' => 'logout']);
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
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }

    public function getRegister()
    {
        $query = Input::get('search');

        $search = \DB::table('products')->where('product_name', 'LIKE', '%'. $query . '%')->paginate(10);

        return view('auth.register', compact('query', 'search'));
    }

    public function postRegister(RegistrationRequest $request, AppMailers $mailer) {
        // Create the user in the DB.
        $user = User::create([
            'email' => $request->input('email'),
            'username' => $request->input('username'),
            'password' => bcrypt($request->input('password')),
            'verified' => 0,
        ]);
        /**
         * send email conformation to user that just registered.
         * -- sendEmailConfirmationTo is in Mailers/AppMailers.php --
         */
        $mailer->sendEmailConfirmationTo($user);
        // Flash a info message saying you need to confirm your email.
        flash()->overlay('Info', 'Please confirm your email address in your inbox.');
        return redirect()->back();
    }

    public function getLogin(Request $request)
    {
        $query = Input::get('search');

        $search = \DB::table('products')->where('product_name', 'LIKE', '%' . $query . '%')->paginate(10);
        return view('auth.login', compact('query', 'search'));
    }

     public function postLogin(Request $request) {
        // Validate email and password.
        $this->validate($request, [
            'email'    => 'required|email',
            'password' => 'required|'
        ]);
        // login in user if successful
        if ($this->signIn($request)) {
            //flash()->success('Success', 'You have successfully signed in.');
            return redirect('/');
        }
        // Else, show error message, and redirect them back to login.php.
        flash()->customErrorOverlay('Error', 'Could not sign you in with those credentials');
        return redirect('login');
    }

    protected function signIn(Request $request) {
        return Auth::attempt($this->getCredentials($request), $request->has('remember'));
    }

     protected function getCredentials(Request $request) {
        return [
            'email'    => $request->input('email'),
            'password' => $request->input('password'),
            'verified' => true
        ];
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }
}
