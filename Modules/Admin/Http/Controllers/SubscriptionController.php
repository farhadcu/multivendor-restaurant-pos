<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Modules\Admin\Contracts\SubscriptionContract AS Subscription;

class SubscriptionController extends BaseController
{
    private $subscription;

    public function __construct(Subscription $subscription)
    {
        parent::__construct();
        $this->subscription = $subscription;
    }

    public function index()
    {
        if($this->helper->permission('subscription-manage')){
            $this->setPageData('Subscription','Manage Subscription','fab fa-speakap');
            return view('admin::subscription.subscription');
        }
    }

    public function getList(Request $request)
    {
        if($request->ajax()){
            if($this->helper->permission('subscription-manage')){
                $params              = $request->except('_token');
                $params['order']     = $request->input('order.0.column');
                $params['direction'] = $request->input('order.0.dir');
                $params['length']    = $request->input('length');
                $params['start']     = $request->input('start');
                $this->output        = $this->subscription->getList($params);
                echo json_encode($this->output);
            }
        }
    }
   
    public function store(Request $request){
        if($request->ajax()){
            if($this->helper->permission('subscription-add')){
                $params       = $request->except(['_token','method_id']);
                $this->output = $this->subscription->createSubscription($params);
            }else{
                $this->output = $this->access_blocked();
            }
            return response()->json($this->output);
        }
    }

    public function edit(Request $request){
        if($request->ajax()){
            if($this->helper->permission('subscription-edit')){
                $params       = $request->except('_token');
                $this->output = $this->subscription->editSubscription($params);
            }else{
                $this->output = $this->access_blocked();
            }
            return response()->json($this->output);
        }
        
    }
    public function update(Request $request){
        if($request->ajax()){
            if($this->helper->permission('subscription-edit')){
                $params       = $request->except('_token');
                $this->output = $this->subscription->updateSubscription($params);
            }else{
                $this->output = $this->access_blocked();
            }
            return response()->json($this->output);
        }
        
    }

    public function destroy(Request $request)
    {
        if($request->ajax()){
            if($this->helper->permission('subscription-delete')){
                $params       = $request->except('_token');
                $this->output = $this->subscription->deleteSubscription($params);
            }else{
                $this->output = $this->access_blocked();
            }
            return response()->json($this->output);
        }
    }

    public function bulk_action_delete(Request $request)
    {
        if($request->ajax()){
            if($this->helper->permission('subscription-bulk-action-delete')){
                $params       = $request->except('_token');
                $this->output = $this->subscription->bulk_action_delete($params);
            }else{
                $this->output = $this->access_blocked();
            }
            return response()->json($this->output);
        }
    }
}
