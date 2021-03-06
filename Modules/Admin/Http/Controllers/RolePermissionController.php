<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Modules\Admin\Contracts\RolePermissionContract AS Permission;

class RolePermissionController extends BaseController
{

    private $permission;

    public function __construct(Permission $permission){
        parent::__construct();   
        $this->permission = $permission;
    }
    public function index()
    {
        if($this->helper->permission('role-permission')){
            $this->setPageData('Role Permission','Role Permission','fas fa-align-left');
            $roles  = $this->permission->index(); //get role list data to show in blade file dropdown list
            return view('admin::permission.role-permission',compact('roles'));
        }
    }

    //Store role permission method
    public function store(Request $request)
    {
        if($request->ajax()){
            if($this->helper->permission('role-permission')){
                $params         = $request->except('_token');
                $this->output   = $this->permission->store($params);
                return response()->json($this->output);
            }
        }
    }

    //Get role permission list method
    public function get_role_permission(Request $request)
    {
        if($request->ajax()){
            if($this->helper->permission('role-permission')){
                return $this->permission->get_role_permission((int) $request->role_id);
            }
        }
    }
}
