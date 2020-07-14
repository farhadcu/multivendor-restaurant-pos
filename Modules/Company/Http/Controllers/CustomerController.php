<?php

namespace Modules\Company\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Modules\Company\Contracts\CustomerContract AS Customer;

class CustomerController extends BaseController
{
    private $customer;

    public function __construct(Customer $customer)
    {
        parent::__construct();
        $this->customer = $customer;
    }
    public function index()
    {

        if($this->helper->permission('customer-list')){
            $this->setPageData('Customer','Customer','fas fa-user-alt');
            return view('company::customer.customer');
        }
    }

    public function getList(Request $request)
    {
        if($request->ajax()){
            if($this->helper->permission('customer-list')){
                $params              = $request->except('_token');
                $params['order']     = $request->input('order.0.column');
                $params['direction'] = $request->input('order.0.dir');
                $params['length']    = $request->input('length');
                $params['start']     = $request->input('start');
                $this->output        = $this->customer->getList($params);
                echo json_encode($this->output);
            }
        }
    }
   
    public function store(Request $request){
        if($request->ajax()){
            if($this->helper->permission('customer-add')){
                $params       = $request->except(['_token','update_id']);
                $this->output = $this->customer->createCustomer($params);
            }else{
                $this->output = $this->access_blocked();
            }
            return response()->json($this->output);
        }
    }

    public function show(Request $request){
        if($request->ajax()){
            if($this->helper->permission('customer-view')){
                $params       = $request->except('_token');
                $this->output = $this->customer->showCustomer($params);
            }else{
                $this->output = $this->access_blocked();
            }
            return response()->json($this->output);
        }
        
    }
    public function edit(Request $request){
        if($request->ajax()){
            if($this->helper->permission('customer-edit')){
                $params       = $request->except('_token');
                $this->output = $this->customer->editCustomer($params);
            }else{
                $this->output = $this->access_blocked();
            }
            return response()->json($this->output);
        }
        
    }
    public function update(Request $request){
        if($request->ajax()){
            if($this->helper->permission('customer-edit')){
                $params       = $request->except('_token');
                $this->output = $this->customer->updateCustomer($params);
            }else{
                $this->output = $this->access_blocked();
            }
            return response()->json($this->output);
        }
        
    }

    public function destroy(Request $request)
    {
        if($request->ajax()){
            if($this->helper->permission('customer-delete')){
                $params       = $request->except('_token');
                $this->output = $this->customer->deleteCustomer($params);
            }else{
                $this->output = $this->access_blocked();
            }
            return response()->json($this->output);
        }
    }

    public function bulk_action_delete(Request $request)
    {
        if($request->ajax()){
            if($this->helper->permission('customer-bulk-action-delete')){
                $params       = $request->except('_token');
                $this->output = $this->customer->bulk_action_delete($params);
            }else{
                $this->output = $this->access_blocked();
            }
            return response()->json($this->output);
        }
    }


    public function getListForPos(Request $request)
    {
        if($request->ajax()){
            if($this->helper->permission('customer-list')){
                $this->output = $this->customer->index();
                return $this->output;
            }
        }
    }
}
