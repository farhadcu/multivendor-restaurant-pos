<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Modules\Admin\Contracts\ModuleContract AS Module;

class ModuleController extends BaseController
{
    private $module;
    public function __construct(Module $module)
    {
        parent::__construct();
        $this->module = $module;

    }
    public function index()
    {
        if($this->helper->permission('module-manage')){
            
            $this->setPageData('Module','Manage Module','fas fa-align-left');
            return view('admin::module.module');
        }
    }

    public function getList(Request $request)
    {
        if($request->ajax()){
            if($this->helper->permission('module-manage')){
                $params              = $request->except('_token');
                $params['order']     = $request->input('order.0.column');
                $params['direction'] = $request->input('order.0.dir');
                $params['length']    = $request->input('length');
                $params['start']     = $request->input('start');
                $output = $this->module->getList($params);
                echo json_encode($output);
            }
        }
    }
   
    public function store(Request $request){
        if($request->ajax()){
            if($this->helper->permission('module-add')){
                $params = $request->except(['_token','module_id']);
                $this->output = $this->module->createModule($params);
            }else{
                $this->output = $this->access_blocked();
            }
            return response()->json($this->output);
        }
    }

    public function edit(Request $request){
        if($request->ajax()){
            if($this->helper->permission('module-edit')){
                $params = $request->except('_token');
                $this->output = $this->module->editModule($params);
            }else{
                $this->output = $this->access_blocked();
            }
            return response()->json($this->output);
        }
        
    }

    public function update(Request $request){
        if($request->ajax()){
            if($this->helper->permission('module-add')){
                $params = $request->except('_token');
                $this->output = $this->module->updateModule($params);
            }else{
                $this->output= $this->access_blocked();
            }
            return response()->json($this->output);
        }
    }

    public function destroy(Request $request)
    {
        if($request->ajax()){
            if($this->helper->permission('module-delete')){
                $params = $request->except('_token');
                $this->output = $this->module->deleteModule($params);
            }else{
                $this->output = $this->access_blocked();
            }
            return response()->json($this->output);
        }
    }

    public function bulk_action_delete(Request $request)
    {
        if($request->ajax()){
            if($this->helper->permission('module-bulk-action-delete')){
                $params = $request->except('_token');
                $this->output = $this->module->bulk_action_delete($params);
            }else{
                $this->output = $this->access_blocked();
            }
            return response()->json($this->output);
        }
    }

    public function parent_module_list(Request $request){
        if($request->ajax()){
            return $this->module->parent_module_list();
        }
    }
}
