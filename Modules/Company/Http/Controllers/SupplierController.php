<?php

namespace Modules\Company\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Modules\Company\Contracts\SupplierContract AS Supplier;

class SupplierController extends BaseController
{
    private $supplier;

    public function __construct(Supplier $supplier)
    {
        parent::__construct();
        $this->supplier = $supplier;
    }
    public function index()
    {

        if($this->helper->permission('supplier-list')){
            $this->setPageData('Supplier','Supplier','fas fa-user-secret');
            return view('company::supplier.supplier');
        }
    }

    public function getList(Request $request)
    {
        if($request->ajax()){
            if($this->helper->permission('supplier-list')){
                $params              = $request->except('_token');
                $params['order']     = $request->input('order.0.column');
                $params['direction'] = $request->input('order.0.dir');
                $params['length']    = $request->input('length');
                $params['start']     = $request->input('start');
                $this->output        = $this->supplier->getList($params);
                echo json_encode($this->output);
            }
        }
    }
   
    public function store(Request $request){
        if($request->ajax()){
            if($this->helper->permission('supplier-add')){
                $params       = $request->except(['_token','update_id']);
                $this->output = $this->supplier->createSupplier($params);
            }else{
                $this->output = $this->access_blocked();
            }
            return response()->json($this->output);
        }
    }

    public function show(Request $request){
        if($request->ajax()){
            if($this->helper->permission('supplier-view')){
                $params       = $request->except('_token');
                $this->output = $this->supplier->showSupplier($params);
            }else{
                $this->output = $this->access_blocked();
            }
            return response()->json($this->output);
        }
        
    }
    public function edit(Request $request){
        if($request->ajax()){
            if($this->helper->permission('supplier-edit')){
                $params       = $request->except('_token');
                $this->output = $this->supplier->editSupplier($params);
            }else{
                $this->output = $this->access_blocked();
            }
            return response()->json($this->output);
        }
        
    }
    public function update(Request $request){
        if($request->ajax()){
            if($this->helper->permission('supplier-edit')){
                $params       = $request->except('_token');
                $this->output = $this->supplier->updateSupplier($params);
            }else{
                $this->output = $this->access_blocked();
            }
            return response()->json($this->output);
        }
        
    }

    public function destroy(Request $request)
    {
        if($request->ajax()){
            if($this->helper->permission('supplier-delete')){
                $params       = $request->except('_token');
                $this->output = $this->supplier->deleteSupplier($params);
            }else{
                $this->output = $this->access_blocked();
            }
            return response()->json($this->output);
        }
    }

    public function bulk_action_delete(Request $request)
    {
        if($request->ajax()){
            if($this->helper->permission('supplier-bulk-action-delete')){
                $params       = $request->except('_token');
                $this->output = $this->supplier->bulk_action_delete($params);
            }else{
                $this->output = $this->access_blocked();
            }
            return response()->json($this->output);
        }
    }

    
}
