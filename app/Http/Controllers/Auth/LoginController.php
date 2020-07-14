<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use DB;
class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';
    protected $route = 'login';
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLoginForm()
    {
        return view('auth.login')->with('route',$this->route);
    }

    protected function authenticated(Request $request, $user)
    {

        if($user->company->status != 1){
            $this->guard()->logout();
            return back()->with('error', 'You company account is disabled. Please contact with admin to enable account.');
        }
        if($user->status != 1){
            $this->guard()->logout();
            return back()->with('error', 'You account is temporarily disabled. Please contact with admin to enable account.');
        }
        $user_permissions = DB::table('user_method_permissions as u')
                                ->select('u.*','m.method_slug')
                                ->leftjoin('company_methods as m','u.method_id','=','m.id')
                                ->where(['u.company_id'=>$user->company_id,'u.user_id'=> $user->id])
                                ->orderBy('u.method_id','asc')
                                ->get();
        $permission_data = [];
        foreach ($user_permissions as $permission) {
            array_push($permission_data,$permission->method_slug);
        }
        session(['permission' => $permission_data]);

        if(empty($user->branch_id)){
            $this->redirectTo = '/select-branch';
        }else{
            session(['branch'=>$user->branch_id]);
            $this->redirectTo = '/dashboard';
        }

    }
}
