<?php

namespace Modules\Company\Http\Controllers\Accounts;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Modules\Company\Contracts\Accounts\TransactionCategoryContract AS Category;

class TransactionCategoryController extends BaseController
{
    private $category;
    public function __construct(Category $category)
    {
        parent::__construct();
        $this->category        = $category;

    }
    public function index(string $category)
    {
        if($this->helper->permission('income-category-list') || $this->helper->permission('expense-category-list')){
            $data['type'] = ($category == 'income') ? 1 : 2;
            $icon = ($category == 'income') ? 'fas fa-dollar' : '';
            $this->setPageData(ucwords($category).' Category',ucwords($category).' Category',$icon);
            return view('company::accounts.category',compact('data'));
        }
    }

    public function getList(Request $request)
    {
        if($request->ajax()){
            if($this->helper->permission('income-category-list') || $this->helper->permission('expense-category-list')){
                $params                 = $request->except('_token');
                $params['order']        = $request->input('order.0.column');
                $params['direction']    = $request->input('order.0.dir');
                $params['length']       = $request->input('length');
                $params['start']        = $request->input('start');
                $output                 = $this->category->getList($params);
                echo json_encode($output);
            }
        }
    }
   
    public function store(Request $request){
        if($request->ajax()){
            if($this->helper->permission('income-category-add') || $this->helper->permission('expense-category-add')){
                $params         = $request->except(['_token','update_id']);
                $this->output   = $this->category->createCategory($params);
            }else{
                $this->output   = $this->access_blocked();
            }
            return response()->json($this->output);
        }
    }

    public function edit(Request $request){
        if($request->ajax()){
            if($this->helper->permission('income-category-edit') || $this->helper->permission('expense-category-edit')){
                $params         = $request->except('_token');
                $this->output   = $this->category->editCategory($params);
            }else{
                $this->output   = $this->access_blocked();
            }
            return response()->json($this->output);
        }
        
    }

    public function update(Request $request){
        if($request->ajax()){
            if($this->helper->permission('income-category-edit') || $this->helper->permission('expense-category-edit')){
                $params         = $request->except('_token');
                $this->output   = $this->category->updateCategory($params);
            }else{
                $this->output   = $this->access_blocked();
            }
            return response()->json($this->output);
        }
    }

    public function destroy(Request $request)
    {
        if($request->ajax()){
            if($this->helper->permission('income-category-delete') || $this->helper->permission('expense-category-delete')){
                $params         = $request->except('_token');
                $this->output   = $this->category->deleteCategory($params);
            }else{
                $this->output   = $this->access_blocked();
            }
            return response()->json($this->output);
        }
    }

    public function bulk_action_delete(Request $request)
    {
        if($request->ajax()){
            if($this->helper->permission('income-category-bulk-action-delete') || $this->helper->permission('expense-category-bulk-action-delete')){
                $params         = $request->except('_token');
                $this->output   = $this->category->bulk_action_delete($params);
            }else{
                $this->output   = $this->access_blocked();
            }
            return response()->json($this->output);
        }
    }
}
