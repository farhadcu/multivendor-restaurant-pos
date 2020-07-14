<?php

namespace Modules\Admin\Http\Controllers\Company;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Modules\Admin\Contracts\Company\CompanyContract AS Company;
use Modules\Admin\Contracts\SubscriptionContract AS Subscription;

class CompanyController extends BaseController
{
    private $company;
    private $subscription;

    public function __construct(Company $company, Subscription $subscription)
    {
        parent::__construct();
        $this->company = $company;
        $this->subscription = $subscription;
    }

    public function index()
    {
        if($this->helper->permission('company-manage')){
            $this->setPageData('Company','Company','fas fa-store');
            
            $data['subscriptions'] =  $this->subscription->index();
            return view('admin::company.company',compact('data'));
        }
    }

    public function getList(Request $request)
    {
        if($request->ajax()){
            if($this->helper->permission('company-manage')){
                $params              = $request->except('_token');
                $params['order']     = $request->input('order.0.column');
                $params['direction'] = $request->input('order.0.dir');
                $params['length']    = $request->input('length');
                $params['start']     = $request->input('start');
                $this->output        = $this->company->getList($params);
                echo json_encode($this->output);
            }
        }
    }
   
    public function store(Request $request){
        if($request->ajax()){
            if($this->helper->permission('company-add')){
                $params       = $request->except(['_token','update_id']);
                $this->output = $this->company->createCompany($params);
            }else{
                $this->output = $this->access_blocked();
            }
            return response()->json($this->output);
        }
    }

    public function edit(Request $request){
        if($request->ajax()){
            if($this->helper->permission('company-edit')){
                $params       = $request->except('_token');
                $this->output = $this->company->editCompany($params);
            }else{
                $this->output = $this->access_blocked();
            }
            return response()->json($this->output);
        }
        
    }
    public function update(Request $request){
        if($request->ajax()){
            if($this->helper->permission('company-edit')){
                $params       = $request->except('_token');
                $this->output = $this->company->updateCompany($params);
            }else{
                $this->output = $this->access_blocked();
            }
            return response()->json($this->output);
        }
        
    }

    public function destroy(Request $request)
    {
        if($request->ajax()){
            if($this->helper->permission('company-delete')){
                $params       = $request->except('_token');
                $this->output = $this->company->deleteCompany($params);
            }else{
                $this->output = $this->access_blocked();
            }
            return response()->json($this->output);
        }
    }

    public function bulk_action_delete(Request $request)
    {
        if($request->ajax()){
            if($this->helper->permission('company-bulk-action-delete')){
                $params       = $request->except('_token');
                $this->output = $this->company->bulk_action_delete($params);
            }else{
                $this->output = $this->access_blocked();
            }
            return response()->json($this->output);
        }
    }
}
