<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Modules\Admin\Contracts\UnitContract AS Unit;

class UnitController extends BaseController
{
    private $unit;

    public function __construct(Unit $unit)
    {
        $this->unit = $unit;
        parent::__construct();
    }

    public function index()
    {
        if($this->helper->permission('unit-manage')){
            $this->setPageData('Unit','Manage Unit','fas fa-balance-scale');
            return view('admin::setting.unit');
        }
    }

    public function getList(Request $request)
    {
        if($request->ajax()){
            if($this->helper->permission('unit-manage')){
                $params              = $request->except('_token');
                $params['order']     = $request->input('order.0.column');
                $params['direction'] = $request->input('order.0.dir');
                $params['length']    = $request->input('length');
                $params['start']     = $request->input('start');
                $this->output        = $this->unit->getList($params);
                echo json_encode($this->output);
            }
        }
    }

    public function store(Request $request){
        if($request->ajax()){
            if($this->helper->permission('unit-add')){
                $params       = $request->except(['_token','unit_id']);
                $this->output = $this->unit->createUnit($params);
            }else{
                $this->output = $this->access_blocked();
            }
            return response()->json($this->output);
        }
    }

    public function edit(Request $request){
        if($request->ajax()){
            if($this->helper->permission('unit-edit')){
                $params       = $request->except('_token');
                $this->output = $this->unit->editUnit($params);
            }else{
                $this->output = $this->access_blocked();
            }
            return response()->json($this->output);
        }
        
    }

    public function update(Request $request){
        if($request->ajax()){
            if($this->helper->permission('unit-edit')){
                $params       = $request->except('_token');
                $this->output = $this->unit->updateUnit($params);
            }else{
                $this->output = $this->access_blocked();
            }
            return response()->json($this->output);
        }
    }

    public function destroy(Request $request)
    {
        if($request->ajax()){
            if($this->helper->permission('unit-delete')){
                $params       = $request->except('_token');
                $this->output = $this->unit->deleteUnit($params);
            }else{
                $this->output = $this->access_blocked();
            }
            return response()->json($this->output);
        }
    }

    public function bulk_action_delete(Request $request)
    {
        if($request->ajax()){
            if($this->helper->permission('unit-bulk-action-delete')){
                $params       = $request->except('_token');
                $this->output = $this->unit->bulk_action_delete($params);
            }else{
                $this->output = $this->access_blocked();
            }
            return response()->json($this->output);
        }
    }


}