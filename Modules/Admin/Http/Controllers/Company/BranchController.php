<?php

namespace Modules\Admin\Http\Controllers\Company;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Modules\Admin\Contracts\Company\BranchContract AS Branch;
use Modules\Admin\Contracts\Company\CompanyContract AS Company;

class BranchController extends BaseController
{
    private $branch;
    private $company;
    public function __construct(Branch $branch,Company $company)
    {
        parent::__construct();
        $this->branch   = $branch;
        $this->company  = $company;

    }
    public function index()
    {
        if($this->helper->permission('branch-manage')){
            $this->setPageData('Company Branch','Company Branch','fas fa-code-branch');
            $data['companies'] = $this->company->index();
            return view('admin::company.branch',compact('data'));
        }
    }

    public function getList(Request $request)
    {
        if($request->ajax()){
            if($this->helper->permission('branch-manage')){
                $params              = $request->except('_token');
                $params['order']     = $request->input('order.0.column');
                $params['direction'] = $request->input('order.0.dir');
                $params['length']    = $request->input('length');
                $params['start']     = $request->input('start');
                $output = $this->branch->getList($params);
                echo json_encode($output);
            }
        }
    }
   
    public function store(Request $request){
        if($request->ajax()){
            if($this->helper->permission('branch-add')){
                $params = $request->except(['_token','module_id']);
                $this->output = $this->branch->createBranch($params);
            }else{
                $this->output = $this->access_blocked();
            }
            return response()->json($this->output);
        }
    }

    public function edit(Request $request){
        if($request->ajax()){
            if($this->helper->permission('branch-edit')){
                $params = $request->except('_token');
                $this->output = $this->branch->editBranch($params);
            }else{
                $this->output = $this->access_blocked();
            }
            return response()->json($this->output);
        }
        
    }

    public function update(Request $request){
        if($request->ajax()){
            if($this->helper->permission('branch-add')){
                $params = $request->except('_token');
                $this->output = $this->branch->updateBranch($params);
            }else{
                $this->output= $this->access_blocked();
            }
            return response()->json($this->output);
        }
    }

    public function change_status(Request $request)
    {
        if($request->ajax()){
            if($this->helper->permission('branch-change-status'))
            {  
                $params       = $request->except('_token');
                $this->output = $this->branch->change_status($params);
            }else{
                $this->output = $this->access_blocked();
            }
            return response()->json($this->output);
        }
    }

    public function destroy(Request $request)
    {
        if($request->ajax()){
            if($this->helper->permission('branch-delete')){
                $params = $request->except('_token');
                $this->output = $this->branch->deleteBranch($params);
            }else{
                $this->output = $this->access_blocked();
            }
            return response()->json($this->output);
        }
    }

    public function bulk_action_delete(Request $request)
    {
        if($request->ajax()){
            if($this->helper->permission('branch-bulk-action-delete')){
                $params = $request->except('_token');
                $this->output = $this->branch->bulk_action_delete($params);
            }else{
                $this->output = $this->access_blocked();
            }
            return response()->json($this->output);
        }
    }


}
