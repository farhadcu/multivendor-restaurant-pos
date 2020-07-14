<?php

namespace Modules\Admin\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Validation\ValidationException;
use DB;
use Session;
class LoginController extends Controller
{
    use ThrottlesLogins;

    /**
     * Where to redirect admins after login.
     *
     * @var string
     */
    protected $redirectTo = '/admin/dashboard';

    protected $route = 'admin.login';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest:admin')->except('logout');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('auth.login')->with('route',$this->route);
    }


    public function login(Request $request)
    {

        $this->validateLogin($request);
        if (method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            return $this->sendLoginResponse($request);
        }

        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }


    protected function validateLogin(Request $request)
    {
        $request->validate([
            $this->username() => 'required|string|email|max:30',
            'password' => 'required|string',
        ]);
    }

    protected function attemptLogin(Request $request)
    {
        return $this->guard()->attempt(
            $this->credentials($request),
            $request->filled('remember')
        );
    }

    protected function credentials(Request $request)
    {
        return $request->only($this->username(), 'password');
    }


    protected function sendLoginResponse(Request $request)
    {
        $request->session()->regenerate();

        $this->clearLoginAttempts($request);

        return $this->authenticated($request, $this->guard()->user())
            ? : redirect()->intended($this->redirectPath());
    }


    protected function authenticated(Request $request, $user)
    {

        if($user->status != 1){
            $this->guard()->logout();
            return back()->with('error', 'You account is temporarily disabled. Please contact with admin to enable account.');
        }
        if($user->role_id == 1){
            $user_permissions = DB::table('methods')
                                    ->select('method_slug')
                                    ->orderBy('id','asc')
                                    ->get();
        }else{
            $user_permissions = DB::table('admin_method_permissions as a')
                                    ->select('a.*','m.method_slug')
                                    ->leftjoin('methods as m','a.method_id','=','m.id')
                                    ->where('a.user_id', $user->id)
                                    ->orderBy('a.method_id','asc')
                                    ->get();
        }
        
        $permission_data = [];
        foreach ($user_permissions as $permission) {
            array_push($permission_data,$permission->method_slug);
        }
     session(['permission' => $permission_data]);

    }

    protected function sendFailedLoginResponse(Request $request)
    {
        throw ValidationException::withMessages([
            $this->username() => [trans('auth.failed')],
        ]);
    }


    public function username()
    {
        return 'email';
    }

    protected function guard()
    {
        return Auth::guard('admin');
    }


    public function logout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->invalidate();

        return $this->loggedOut($request) ? : redirect('/admin');
    }


    protected function loggedOut(Request $request)
    {

    }
    
    public function redirectPath()
    {
        if (method_exists($this, 'redirectTo')) {
            return $this->redirectTo();
        }

        return property_exists($this, 'redirectTo') ? $this->redirectTo : '/admin';
    }

}
