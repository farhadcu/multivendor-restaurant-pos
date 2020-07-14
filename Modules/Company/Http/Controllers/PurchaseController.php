<?php

namespace Modules\Company\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Modules\Company\Contracts\PurchaseContract AS Purchase;
use Modules\Company\Contracts\SupplierContract AS Supplier;
use Cart;
class PurchaseController extends BaseController
{
    private $purchase;
    private $supplier;

    public function __construct(Purchase $purchase,Supplier $supplier)
    {
        parent::__construct();
        $this->purchase = $purchase;
        $this->supplier = $supplier;
    }

    public function index()                                                 
    {

        if($this->helper->permission('purchase-list')){
            Cart::instance('purchase')->destroy();                                            
            $this->setPageData('Purchase','Purchase','fas fa-shopping-basket');
            $data['suppliers'] = $this->supplier->index();
            return view('company::purchase.index',compact('data'));
        }
    }

    public function getList(Request $request)
    {
        if($request->ajax()){
            if($this->helper->permission('purchase-list')){
                $params              = $request->except('_token');
                $params['order']     = $request->input('order.0.column');
                $params['direction'] = $request->input('order.0.dir');
                $params['length']    = $request->input('length');
                $params['start']     = $request->input('start');
                $this->output        = $this->purchase->getList($params);
                echo json_encode($this->output);
            }
        }
    }

    public function create()
    {

        if($this->helper->permission('purchase-add')){
            $this->setPageData('Purchase Add','Purchase Add','fas fa-plus-square');
            $data['suppliers'] = $this->supplier->index();
            return view('company::purchase.add',compact('data'));
        }
    }
   
    public function store(Request $request){
        if($request->ajax()){
            if($this->helper->permission('purchase-add')){
                $params       = $request->except('_token');
                $this->output = $this->purchase->createPurchase($params);
            }else{
                $this->output = $this->access_blocked();
            }
            return response()->json($this->output);
        }
    }

    public function show($id){
        if($this->helper->permission('purchase-edit')){
            $data = $this->purchase->showPurchase($id);
            $this->setPageData('Purchase Details','Purchase Details','fas fa-shopping-basket');
            return view('company::purchase.view',compact('data'));
        }
        
    }


    public function edit($id){

        if($this->helper->permission('purchase-edit')){
            $data['purchase'] = $this->purchase->editPurchase($id);
            $this->setPageData('Purchase Edit','Purchase Edit','fas fa-edit');
            $data['suppliers'] = $this->supplier->index();
            return view('company::purchase.edit',compact('data'));
        }
        
    }
    public function update(Request $request){
        if($request->ajax()){
            if($this->helper->permission('purchase-edit')){
                $params       = $request->except('_token');
                $this->output = $this->purchase->updatePurchase($params);
            }else{
                $this->output = $this->access_blocked();
            }
            return response()->json($this->output);
        }
        
    }

    public function destroy(Request $request)
    {
        if($request->ajax()){
            if($this->helper->permission('purchase-delete')){
                $params       = $request->except('_token');
                $this->output = $this->purchase->deletePurchase($params);
            }else{
                $this->output = $this->access_blocked();
            }
            return response()->json($this->output);
        }
    }

    public function bulk_action_delete(Request $request)
    {
        if($request->ajax()){
            if($this->helper->permission('purchase-bulk-action-delete')){
                $params       = $request->except('_token');
                $this->output = $this->purchase->bulk_action_delete($params);
            }else{
                $this->output = $this->access_blocked();
            }
            return response()->json($this->output);
        }
    }

    public function payment_list(Request $request)
    {
        if($request->ajax()){
            if($this->helper->permission('purchase-list')){
                $params       = $request->except('_token');
                $this->output = $this->purchase->payment_list($params);
            }else{
                $this->output = $this->access_blocked();
            }
        
            return response()->json($this->output);
        }
    }

    public function add_payment(Request $request)
    {
        if($request->ajax()){
            if($this->helper->permission('purchase-add')){
                $params       = $request->except('_token');
                $this->output = $this->purchase->add_payment($params);
            }else{
                $this->output = $this->access_blocked();
            }
            return response()->json($this->output);
        }
    }

    public function edit_payment(Request $request)
    {
        if($request->ajax()){
            if($this->helper->permission('purchase-edit')){
                $params       = $request->except('_token');
                $this->output = $this->purchase->edit_payment($params);
            }else{
                $this->output = $this->access_blocked();
            }
            return response()->json($this->output);
        }
    }

    public function update_payment(Request $request)
    {
        if($request->ajax()){
            if($this->helper->permission('purchase-edit')){
                $params       = $request->except('_token');
                $this->output = $this->purchase->update_payment($params);
            }else{
                $this->output = $this->access_blocked();
            }
            return response()->json($this->output);
        }
    }
    
    public function delete_payment(Request $request)
    {
        if($request->ajax()){
            if($this->helper->permission('purchase-delete')){
                $params       = $request->except('_token');
                $this->output = $this->purchase->delete_payment($params);
            }else{
                $this->output = $this->access_blocked();
            }
            return response()->json($this->output);
        }
    }
}
