<?php

namespace Modules\Company\Http\Controllers\Product;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Modules\Company\Contracts\Product\CategoryContract AS Category;

class CategoryController extends BaseController
{
    private $category;
    public function __construct(Category $category)
    {
        parent::__construct();
        $this->category = $category;

    }
    public function index()
    {
        if($this->helper->permission('category-list')){
            $this->setPageData('Category','Category','');
            return view('company::product.category');
        }
    }

    public function getList(Request $request)
    {
        if($request->ajax()){
            if($this->helper->permission('category-list')){
                $params              = $request->except('_token');
                $params['order']     = $request->input('order.0.column');
                $params['direction'] = $request->input('order.0.dir');
                $params['length']    = $request->input('length');
                $params['start']     = $request->input('start');
                $output = $this->category->getList($params);
                echo json_encode($output);
            }
        }
    }
   
    public function store(Request $request){
        if($request->ajax()){
            if($this->helper->permission('category-add')){
                $params = $request->except(['_token','module_id']);
                $this->output = $this->category->createCategory($params);
            }else{
                $this->output = $this->access_blocked();
            }
            return response()->json($this->output);
        }
    }

    public function edit(Request $request){
        if($request->ajax()){
            if($this->helper->permission('category-edit')){
                $params = $request->except('_token');
                $this->output = $this->category->editCategory($params);
            }else{
                $this->output = $this->access_blocked();
            }
            return response()->json($this->output);
        }
        
    }

    public function update(Request $request){
        if($request->ajax()){
            if($this->helper->permission('category-add')){
                $params = $request->except('_token');
                $this->output = $this->category->updateCategory($params);
            }else{
                $this->output= $this->access_blocked();
            }
            return response()->json($this->output);
        }
    }

    public function change_status(Request $request)
    {
        if($request->ajax()){
            if($this->helper->permission('category-change-status'))
            {  
                $params       = $request->except('_token');
                $this->output = $this->category->change_status($params);
            }else{
                $this->output = $this->access_blocked();
            }
            return response()->json($this->output);
        }
    }

    public function destroy(Request $request)
    {
        if($request->ajax()){
            if($this->helper->permission('category-delete')){
                $params = $request->except('_token');
                $this->output = $this->category->deleteCategory($params);
            }else{
                $this->output = $this->access_blocked();
            }
            return response()->json($this->output);
        }
    }

    public function bulk_action_delete(Request $request)
    {
        if($request->ajax()){
            if($this->helper->permission('category-bulk-action-delete')){
                $params = $request->except('_token');
                $this->output = $this->category->bulk_action_delete($params);
            }else{
                $this->output = $this->access_blocked();
            }
            return response()->json($this->output);
        }
    }

    public function category_list(Request $request){
        if($request->ajax()){
            return $this->category->category_list();
        }
    }
}
