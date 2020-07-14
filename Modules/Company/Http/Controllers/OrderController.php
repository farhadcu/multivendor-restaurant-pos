<?php

namespace Modules\Company\Http\Controllers;
use Cart;
use Modules\Company\Entities\OrderTable;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Modules\Admin\Entities\Branch AS BranchModel;
use Modules\Company\Contracts\OrderContract AS Order;
use Modules\Company\Contracts\Product\ProductContract AS Product;

class OrderController extends BaseController
{
    private $order;

    public function __construct(Order $order,Product $product)
    {
        parent::__construct();
        $this->order = $order;
        $this->product = $product;
    }

    public function index()
    {
        if($this->helper->permission('purchase-list')){
            $this->setPageData('Sale','Sale','fab fa-opencart');
            $result = $this->order->index();
            $customers = $result['customer'];
            $tables = $result['tables'];
            return view('company::order.index',compact('customers','tables'));
        }
    }


    public function productList(Request $request){
        if($request->ajax()){

            $params['limit'] = $request->input('limit');
            $params['start'] = $request->input('start');
            $params['category'] = $request->input('category');
            $params['name'] = $request->input('name');
            $this->output = $this->product->index($params);

            $productDiv = '';
            foreach ($this->output as $value) {
                $dicount = 0;
                if($value->discount){
                    $todayDate = date("Y-m-d");
                    $startDate = $value->discount->start_date;
                    $endDate = $value->discount->end_date;
                    if($todayDate > $startDate && $todayDate < $endDate){
                        $dicount = $value->discount->discount_amount;
                    }
                }

                $productDiv .=   '<div class="col-md-3">
                                    <div class="product text-center p-2">
                                        <div class="product-header">';
                                        if($value->image != NULL){
                $productDiv .=               '<img class="lazyloaded"  src="'.asset(FOLDER_PATH.PRODUCT_IMAGE.$value->image).'" alt="'.$value->name.'">';
                                                if($dicount != 0){
                $productDiv .=                 '<span class="badge badge-pill badge-danger">'.$dicount.'% off</span>';
                                                }
                                        }else{
                $productDiv .=               '<img class="lazyloaded"  src="./public/img/no-image.png" alt="No Imsge">';
                                                if($dicount != 0){
                $productDiv .=                 '<span class="badge badge-pill badge-danger">'.$dicount.'% off</span>';
                                                }
                                        }
                $productDiv .=          '</div>
                                        <div class="product-body" >
                                            <p class="product-title">'.$value->name.'</p>
                                        </div>
                                        <div class="product-footer">
                                            <div class="price">
                                            <p class="product-price"> BDT '.sprintf('%0.2f',$value->selling_price).'</p>
                                            </div>';
                                            if(count($value->variation) > 1){
                $productDiv .=                      '<div class="btn-group">'.
                                                        '<button type="button" class="btn btn-label-brand btn-pill dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">'.
                                                            '<i class="fab fa-opencart"></i>  Add To  Cart</button>'.
                                                        '<div class="dropdown-menu" x-placement="top-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 0px, 0px);">';
                                                        foreach($value->variation as $variation){
                                                            $product_price = $value->selling_price;
                                                            $stock_qty = $value->subtract_stock == 1?$variation->variation_qty:-1;
                                                            $variation_price = '';
                                                            $variation_weight = $variation->variation_weight != Null?$variation->variation_weight:0;
                                                            if($variation->price_prefix == '+'){
                                                                $product_price = $value->selling_price + $variation->variation_price;
                                                                $variation_price = '(+'.$variation->variation_price.')';
                                                            }elseif($variation->price_prefix == '-'){
                                                                $product_price = $value->selling_price - $variation->variation_price;
                                                                $variation_price = '(-'.$variation->variation_price.')';
                                                            }
                                                            $product_name = strlen($variation->variation_name) > 10?$this->readMore($variation->variation_name,10):$variation->variation_name;
                $productDiv .=                              '<a class="dropdown-item" onclick="add_to_cart(\''.$value->name.'('.$variation->variation_name.')\','.$value->id.','.$variation->id.','.$product_price.','.$dicount.','.$stock_qty.',\''.$variation_weight.'\')" href="javascript:void(0);">'.$product_name.' - BDT '.sprintf('%0.2f',$product_price).$variation_price.'</a>';
                                                        }
                $productDiv .=                          '</div>'.
                                                    '</div>';
                                            }else{
                                                $variation_weight = $value->variation[0]->variation_weight != Null?$value->variation[0]->variation_weight:0;
                                                $stock_qty = $value->subtract_stock == 1?$value->variation[0]->variation_qty:-1;
                $productDiv .=                  '<button onclick="add_to_cart(\''.$value->variation[0]->variation_name.'\','.$value->id.','.$value->variation[0]->id.','.$value->selling_price.','.$dicount.','.$stock_qty.',\''.$variation_weight.'\')" type="button"class="btn btn-label-brand btn-pill">'.
                                                '<i class="fab fa-opencart"></i> Add To  Cart</button>';
                                            }
                $productDiv .=          '</div>
                                    </div>
                                </div>';
            }
            return $productDiv;
        }
        
    }

    public static function readMore($text, $limit = 400){
        $text = $text." ";
        $text = substr($text, 0, $limit);
        $text = $text."...";
        return $text;
    }

    public function getList(Request $request)
    {
        if($request->ajax()){
                $params                = $request->except('_token');
                $params['order']       = $request->input('order.0.column');
                $params['direction']   = $request->input('order.0.dir');
                $params['length']      = $request->input('length');
                $params['start']       = $request->input('start');
                $params['order_type']  = $request->input('order_type');
                $params['from_date']   = $request->input('from_date');
                $params['to_date']     = $request->input('to_date');
                $params['status']      = $request->input('status');
                $params['table_no']    = $request->input('table_no');
                $params['customer_id'] = $request->input('customer_id');
                $this->output         = $this->order->getList($params);
                echo json_encode($this->output);
        }
    }

    public function create()
    {
        if($this->helper->permission('sale-add')){
            $this->setPageData('POS','POS','');
            $vat = auth()->user()->company['vat'];
            $order_table = OrderTable::select('id','table_no')->where(['company_id' => auth()->user()->company_id,'branch_id' => session()->get('branch')])->get();
            $cartData = Cart::instance('shopping')->content();
            $subTotal = floatval(str_replace(',', '', Cart::instance('shopping')->subtotal()));
            return view('company::order.add')->with('cartData', $cartData)
            ->with('subTotal', $subTotal)
            ->with('vat', $vat)
            ->with('order_table', $order_table);
        }
    }
   

    public function store(Request $request){
        if($request->ajax()){
            $this->output = $this->order->createOrder($request->details);
            return response()->json($this->output);
        }
    }

    public function invoice($type,$id)
    {
        $params['id'] = $id;
        $this->setPageData('Sale Invoice','Sale Invoice','fas fa-user-alt');
        $branch = BranchModel::find(session()->get('branch'));
        $invoiceData = $this->order->invoice($params);
        $view = $type == 'invoice' ? 'invoice' : 'view';
        return view('company::order.'.$view,compact('branch','invoiceData'));
    }

    public function edit(Request $request){
        if($request->ajax()){
            if($this->helper->permission('supplier-edit')){
                $params       = $request->except('_token');
                $this->output = $this->order->editOrder($params);
            }else{
                $this->output = $this->access_blocked();
            }
            return response()->json($this->output);
        }
        
    }


    public function destroy(Request $request)
    {
        if($request->ajax()){
            if($this->helper->permission('sale-delete')){
                $params       = $request->except('_token');
                $params['id'] = $request->id;
                $this->output = $this->order->deleteOrder($params);
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
                $this->output = $this->order->bulk_action_delete($params);
            }else{
                $this->output = $this->access_blocked();
            }
            return response()->json($this->output);
        }
    }

}
