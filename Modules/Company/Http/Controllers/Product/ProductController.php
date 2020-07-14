<?php

namespace Modules\Company\Http\Controllers\Product;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Modules\Company\Contracts\Product\ProductContract AS Product;
use Modules\Company\Contracts\SupplierContract AS Supplier;
use Modules\Admin\Contracts\UnitContract AS Unit;
use DNS1D;

class ProductController extends BaseController
{
    private $product;
    private $supplier;
    private $unit;
    public function __construct(Product $product, Supplier $supplier, Unit $unit)
    {
        parent::__construct();
        $this->product  = $product;
        $this->supplier = $supplier;
        $this->unit     = $unit;

    }
    public function index()
    {
        if($this->helper->permission('product-list')){
            $this->setPageData('Product','Product','');
            $data['supplier'] = $this->supplier->index();
            $data['units'] = $this->unit->index();
            return view('company::product.product',compact('data'));
        }
    }

    public function getList(Request $request)
    {
        if($request->ajax()){
            if($this->helper->permission('product-list')){
                $params              = $request->except('_token');
                $params['order']     = $request->input('order.0.column');
                $params['direction'] = $request->input('order.0.dir');
                $params['length']    = $request->input('length');
                $params['start']     = $request->input('start');
                $output = $this->product->getList($params);
                echo json_encode($output);
            }
        }
    }
   
    public function store(Request $request){
        if($request->ajax()){
            if($this->helper->permission('product-add')){
                $params = $request->except(['_token','update_id']);
                $this->output = $this->product->createProduct($params);
            }else{
                $this->output = $this->access_blocked();
            }
            return response()->json($this->output);
        }
    }

    public function show(Request $request){
        if($request->ajax()){
            if($this->helper->permission('product-view')){
                $params = $request->except('_token');
                $this->output = $this->product->showProduct($params);
            }else{
                $this->output = $this->access_blocked();
            }
            return response()->json($this->output);
        }
        
    }
    public function edit(Request $request){
        if($request->ajax()){
            if($this->helper->permission('product-edit')){
                $params = $request->except('_token');
                $this->output = $this->product->editProduct($params);
            }else{
                $this->output = $this->access_blocked();
            }
            return response()->json($this->output);
        }
        
    }

    public function update(Request $request){
        if($request->ajax()){
            if($this->helper->permission('product-add')){
                $params = $request->except('_token');
                $this->output = $this->product->updateProduct($params);
            }else{
                $this->output= $this->access_blocked();
            }
            return response()->json($this->output);
        }
    }

    public function change_status(Request $request)
    {
        if($request->ajax()){
            if($this->helper->permission('product-change-status'))
            {  
                $params       = $request->except('_token');
                $this->output = $this->product->change_status($params);
            }else{
                $this->output = $this->access_blocked();
            }
            return response()->json($this->output);
        }
    }

    public function destroy(Request $request)
    {
        if($request->ajax()){
            if($this->helper->permission('product-delete')){
                $params = $request->except('_token');
                $this->output = $this->product->deleteProduct($params);
            }else{
                $this->output = $this->access_blocked();
            }
            return response()->json($this->output);
        }
    }

    public function bulk_action_delete(Request $request)
    {
        if($request->ajax()){
            if($this->helper->permission('product-bulk-action-delete')){
                $params = $request->except('_token');
                $this->output = $this->product->bulk_action_delete($params);
            }else{
                $this->output = $this->access_blocked();
            }
            return response()->json($this->output);
        }
    }

    public function autocomplete_search_product(Request $request)
    {
        if($request->ajax()){
            $this->output = $this->product->autocomplete_search_product($request->term);
            return response()->json($this->output);
        }
    }

    public function variation_product(Request $request)
    {
        if($request->ajax()){
            $params = $request->except('_token');
            $this->output = $this->product->variation_product($params);
            return response()->json($this->output);
        }
    }

    public function generate_barcode(Request $request)
    {
        if($request->ajax()){
            $barcode = "data:image/png;base64," . DNS1D::getBarcodePNG($request->model, "EAN13");
            return response()->json($barcode);
        }
    }
}
