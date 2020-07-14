<?php
namespace Modules\Admin\Http\Controllers\Company;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Modules\Admin\Contracts\Company\CompanyContract AS Company;
use Modules\Admin\Contracts\Company\BranchContract AS Branch;
use Modules\Admin\Contracts\Company\RoleContract AS Role;
use Modules\Admin\Contracts\Company\UserContract AS User;

class UserController extends BaseController
{

    private $user;
    private $company;
    private $branch;
    private $role;

    public function __construct(User $user,Company $company,Branch $branch,Role $role)
    {
        $this->user       = $user;
        $this->company    = $company;
        $this->branch     = $branch;
        $this->role       = $role;
        parent::__construct();
    }

    public function index()
    {
        if($this->helper->permission('company-user-manage')){
            $this->setPageData('Company User','Company User','fas fa-users');
            $data['companies'] = $this->company->index();
            return view('admin::company.user.index',compact('data'));
        }
    }

    //user list show method
    public function getList(Request $request){
        if($request->ajax()){
            if($this->helper->permission('company-user-manage')){
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
        if($this->helper->permission('company-user-add')){
            $this->setPageData('Company User Add','Company User Add','fas fa-plus-square');
            $data['companies'] = $this->company->index();
            $data['permission'] = $this->user->get_permission($user_id=null);
            return view('admin::company.user.add',compact('data'));
        }
    }

    public function store(Request $request)
    {
        if($request->ajax())
        {
            if($this->helper->permission('company-user-add'))
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
        if($this->helper->permission('company-user-view')){
            $params  = $request->except('_token');
            $data    = $this->user->showUser($params);
            $permitted_role = $this->permitted_role;
            return view('user.user-view',compact('data','permitted_role'))->render(); //rendering the user view with user data
        }
    }


    public function edit($id)
    {
        if($this->helper->permission('company-user-edit')){
            $this->setPageData('Company User Edit','Company User Edit','fas fa-edit');
            $data['companies'] = $this->company->index();
            $data['permission'] = $this->user->get_permission($id);
            $data['user'] = $this->user->showUser($id);
            $data['roles']   = $this->role->index($data['user']['company_id']);
            $data['branchws'] = $this->branch->index($data['user']['company_id']);

            return view('admin::company.user.edit',compact('data'));
        }
    }


    public function update(Request $request)
    {
        if($request->ajax())
        {
            if($this->helper->permission('company-user-edit'))
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
            if($this->helper->permission('company-user-change-status')){
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
            if($this->helper->permission('company-user-password-change'))
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

    public function get_role_branch(Request $request)
    {
        if($request->ajax())
        {
            $company_id = $request->company_id;
            $roles = $this->role->index($company_id);
            $branches = $this->branch->index($company_id);

            $role_output = '';
            if(!empty($roles)){
                $role_output .= '<option value="">Select Please</option>';
                foreach ($roles  as $key => $value) {
                    $role_output .= '<option value="'.$value->id.'">'.$value->role.'</option>';
                }
            }
            $branch_output = '';
            if(!empty($roles)){
                $branch_output .= '<option value="">Select Please</option>';
                foreach ($branches  as $key => $value) {
                    $branch_output .= '<option value="'.$value->id.'">'.$value->branch_name.'</option>';
                }
            }

            $this->output = [
                'role' => $role_output,
                'branch' => $branch_output,
            ];
                
            return response()->json($this->output);
        }
    }
}