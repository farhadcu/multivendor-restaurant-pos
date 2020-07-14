<?php

namespace Modules\Admin\Http\Controllers\Company;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Modules\Admin\Contracts\Company\RoleContract AS Role;
use Modules\Admin\Contracts\Company\CompanyContract AS Company;

class RoleController extends BaseController
{

    private $role;
    private $company;

    public function __construct(Role $role,Company $company)
    {
        parent::__construct();   
        $this->role       = $role;
        $this->company    = $company;
    }

    public function index()
    {
        if($this->helper->permission('company-role-manage')){
            $this->setPageData('Company Role','Company Role','fas fa-user-cog');
            $data['companies'] = $this->company->index();
            return view('admin::company.role',compact('data'));
        }
    }

    public function getList(Request $request)
    {
        if($request->ajax()){
            if($this->helper->permission('company-role-manage')){
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
            if($this->helper->permission('company-role-add')){
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
            if($this->helper->permission('company-role-edit')){
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
            if($this->helper->permission('company-role-edit')){
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
            if($this->helper->permission('company-role-delete')){
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
            if($this->helper->permission('company-role-bulk-action-delete')){
                $params       = $request->except('_token');
                $this->output = $this->role->bulk_action_delete($params);
            }else{
                $this->output = $this->access_blocked();
            }
            return response()->json($this->output);
        }
    }
}
