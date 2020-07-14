<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Modules\Admin\Contracts\MethodContract AS Method;

class MethodController extends BaseController
{
    private $method;

    public function __construct(Method $method)
    {
        parent::__construct();
        $this->method = $method;
    }

    public function index()
    {
        if($this->helper->permission('method-manage')){
            $this->setPageData('Method','Manage Method','fas fa-list-ol');
            return view('admin::method.method');
        }
    }

    public function getList(Request $request)
    {
        if($request->ajax()){
            if($this->helper->permission('method-manage')){
                $params              = $request->except('_token');
                $params['order']     = $request->input('order.0.column');
                $params['direction'] = $request->input('order.0.dir');
                $params['length']    = $request->input('length');
                $params['start']     = $request->input('start');
                $this->output        = $this->method->getList($params);
                echo json_encode($this->output);
            }
        }
    }
   
    public function store(Request $request){
        if($request->ajax()){
            if($this->helper->permission('method-add')){
                $params       = $request->except(['_token','method_id']);
                $this->output = $this->method->createMethod($params);
            }else{
                $this->output = $this->access_blocked();
            }
            return response()->json($this->output);
        }
    }

    public function edit(Request $request){
        if($request->ajax()){
            if($this->helper->permission('method-edit')){
                $params       = $request->except('_token');
                $this->output = $this->method->editMethod($params);
            }else{
                $this->output = $this->access_blocked();
            }
            return response()->json($this->output);
        }
        
    }
    public function update(Request $request){
        if($request->ajax()){
            if($this->helper->permission('method-edit')){
                $params       = $request->except('_token');
                $this->output = $this->method->updateMethod($params);
            }else{
                $this->output = $this->access_blocked();
            }
            return response()->json($this->output);
        }
        
    }

    public function destroy(Request $request)
    {
        if($request->ajax()){
            if($this->helper->permission('method-delete')){
                $params       = $request->except('_token');
                $this->output = $this->method->deleteMethod($params);
            }else{
                $this->output = $this->access_blocked();
            }
            return response()->json($this->output);
        }
    }

    public function bulk_action_delete(Request $request)
    {
        if($request->ajax()){
            if($this->helper->permission('method-bulk-action-delete')){
                $params       = $request->except('_token');
                $this->output = $this->method->bulk_action_delete($params);
            }else{
                $this->output = $this->access_blocked();
            }
            return response()->json($this->output);
        }
    }
}
