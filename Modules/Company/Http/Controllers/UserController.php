<?php
namespace Modules\Company\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Modules\Company\Contracts\RoleContract AS Role;
use Modules\Company\Contracts\UserContract AS User;

class UserController extends BaseController
{

    private $user;
    private $role;

    public function __construct(User $user,Role $role)
    {
        $this->user       = $user;
        $this->role       = $role;
        parent::__construct();
    }

    public function index()
    {
        if($this->helper->permission('user-list')){
            $this->setPageData('User','User','fas fa-users');
            $data['roles'] = $this->role->index(auth()->user()->company_id);
            return view('company::user.index',compact('data'));
        }
    }

    //user list show method
    public function getList(Request $request){
        if($request->ajax()){
            if($this->helper->permission('user-list')){
                $params              = $request->except('_token');
                $params['order']     = $request->input('order.0.column');
                $params['direction'] = $request->input('order.0.dir');
                $params['length']    = $request->input('length');
                $params['start']     = $request->input('start');
                $this->output        = $this->user->getList($params);
                echo json_encode($this->output);
            }
        }
    }

    public function create()
    {
        if($this->helper->permission('user-add')){
            $this->setPageData('User Add','User Add','fas fa-plus-square');
            $data['roles'] = $this->role->index(auth()->user()->company_id);
            $data['permission'] = $this->user->get_permission($user_id=null);
            return view('company::user.add',compact('data'));
        }
    }

    public function store(Request $request)
    {
        if($request->ajax())
        {
            if($this->helper->permission('user-add'))
            {
                $params       = $request->except(['_token']);
                $this->output = $this->user->createUser($params);
            }else{
                $this->output = $this->access_blocked();
            }   
            return response()->json($this->output);
        }
    }


    public function show(Request $request)
    {
        if($this->helper->permission('user-view')){
            $params  = $request->except('_token');
            $data    = $this->user->showUser($params);
            return view('user.user-view',compact('data','permitted_role'))->render(); //rendering the user view with user data
        }
    }


    public function edit($id)
    {
        if($this->helper->permission('user-edit')){
            $this->setPageData('User Edit','User Edit','fas fa-edit');
            $data['permission'] = $this->user->get_permission($id);
            $data['user'] = $this->user->showUser($id);
            $data['roles'] = $this->role->index(auth()->user()->company_id);

            return view('company::user.edit',compact('data'));
        }
    }


    public function update(Request $request)
    {
        if($request->ajax())
        {
            if($this->helper->permission('user-edit'))
            {
                $params       = $request->except('_token');
                $this->output = $this->user->updateUser($params);
            }else{
                $this->output = $this->access_blocked();
            }   
            return response()->json($this->output);
        }
    }

    public function change_status(Request $request)
    {
        if($request->ajax()){
            if($this->helper->permission('user-change-status')){
                $params = $request->except('_token');
                $this->output = $this->user->change_status($params);
            }else{
                $this->output = $this->access_blocked();
            }
            return response()->json($this->output);
        }
    }

    public function destroy(Request $request)
    {
        //
    }

    public function change_password(Request $request)
    {
        if($request->ajax())
        {
            if($this->helper->permission('user-password-change'))
            {
                $params       = $request->except('_token');
                $this->output = $this->user->change_password($params);
            }else{
                $this->output = $this->access_blocked();
            }   
            return response()->json($this->output);
        }
    }

    public function my_profile()
    {
        $this->setPageData('My Profile','My Profile','fas fa-users');
        return view('user.my-profile');
    }
}