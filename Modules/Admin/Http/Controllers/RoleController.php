<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Modules\Admin\Contracts\RoleContract AS Role;

class RoleController extends BaseController
{

    private $role;

    public function __construct(Role $role)
    {
        parent::__construct();   
        $this->role = $role;
    }

    public function index()
    {
        if($this->helper->permission('role-manage')){
            $this->setPageData('Role','Manage Role','fas fa-user-cog');
            return view('admin::role.role');
        }
    }

    public function getList(Request $request)
    {
        if($request->ajax()){
            if($this->helper->permission('role-manage')){
                $params              = $request->except('_token');
                $params['order']     = $request->input('order.0.column');
                $params['direction'] = $request->input('order.0.dir');
                $params['length']    = $request->input('length');
                $params['start']     = $request->input('start');
                $this->output        = $this->role->getList($params);
                echo json_encode($this->output);
            }
        }
    }

    public function store(Request $request){
        if($request->ajax()){
            if($this->helper->permission('role-add')){
                $params       = $request->except(['_token','role_id']);
                $this->output = $this->role->createRole($params);
            }else{
                $this->output = $this->access_blocked();
            }   
            return response()->json($this->output);
        }
    }

    public function edit(Request $request){
        if($request->ajax()){
            if($this->helper->permission('role-edit')){
                $params         = $request->except('_token');
                $this->output   = $this->role->editRole($params);
            }else{
                $this->output   = $this->access_blocked();
            }
            return response()->json($this->output);
        }
    }

    public function update(Request $request){
        if($request->ajax()){
            if($this->helper->permission('role-edit')){
                $params         = $request->except('_token');
                $this->output   = $this->role->updateRole($params);
            }else{
                $this->output   = $this->access_blocked();
            }
            return response()->json($this->output);
        }
    }

    public function destroy(Request $request)
    {
        if($request->ajax()){
            if($this->helper->permission('role-delete')){
                $params         = $request->except('_token');
                $this->output   = $this->role->deleteRole($params);
            }else{
                $this->output   = $this->access_blocked();
            }
            return response()->json($this->output);
        }
    }

    public function bulk_action_delete(Request $request)
    {
        if($request->ajax()){
            if($this->helper->permission('role-bulk-action-delete')){
                $params       = $request->except('_token');
                $this->output = $this->role->bulk_action_delete($params);
            }else{
                $this->output = $this->access_blocked();
            }
            return response()->json($this->output);
        }
    }
}
