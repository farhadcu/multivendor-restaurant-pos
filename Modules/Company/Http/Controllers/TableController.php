<?php

namespace Modules\Company\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Modules\Company\Contracts\TableContract AS Table;

class TableController extends BaseController
{
    private $table;

    public function __construct(Table $table)
    {
        parent::__construct();
        $this->table = $table;
    }
    public function index()
    {

        if($this->helper->permission('table-list')){
            $this->setPageData('Table','Table','fas fa-table');
            return view('company::setting.table');
        }
    }

    public function getList(Request $request)
    {
        if($request->ajax()){
            if($this->helper->permission('table-list')){
                $params              = $request->except('_token');
                $params['order']     = $request->input('order.0.column');
                $params['direction'] = $request->input('order.0.dir');
                $params['length']    = $request->input('length');
                $params['start']     = $request->input('start');
                $this->output        = $this->table->getList($params);
                echo json_encode($this->output);
            }
        }
    }
   
    public function store(Request $request){
        if($request->ajax()){
            if($this->helper->permission('table-add')){
                $params       = $request->except(['_token','update_id']);
                $this->output = $this->table->createTable($params);
            }else{
                $this->output = $this->access_blocked();
            }
            return response()->json($this->output);
        }
    }


    public function edit(Request $request){
        if($request->ajax()){
            if($this->helper->permission('table-edit')){
                $params       = $request->except('_token');
                $this->output = $this->table->editTable($params);
            }else{
                $this->output = $this->access_blocked();
            }
            return response()->json($this->output);
        }
        
    }
    public function update(Request $request){
        if($request->ajax()){
            if($this->helper->permission('table-edit')){
                $params       = $request->except('_token');
                $this->output = $this->table->updateTable($params);
            }else{
                $this->output = $this->access_blocked();
            }
            return response()->json($this->output);
        }
        
    }

    public function destroy(Request $request)
    {
        if($request->ajax()){
            if($this->helper->permission('table-delete')){
                $params       = $request->except('_token');
                $this->output = $this->table->deleteTable($params);
            }else{
                $this->output = $this->access_blocked();
            }
            return response()->json($this->output);
        }
    }

    public function bulk_action_delete(Request $request)
    {
        if($request->ajax()){
            if($this->helper->permission('table-bulk-action-delete')){
                $params       = $request->except('_token');
                $this->output = $this->table->bulk_action_delete($params);
            }else{
                $this->output = $this->access_blocked();
            }
            return response()->json($this->output);
        }
    }
}
