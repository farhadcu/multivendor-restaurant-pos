<?php

namespace Modules\Company\Http\Controllers;
use Cart;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Modules\Company\Entities\Product\ProductHasVariation;

class OrderCartController extends BaseController
{
    private $order;

    public function __construct()
    {
        parent::__construct();
    }

   
    public function store(Request $request){
        if($request->ajax()){
            if($this->helper->permission('sale-add')){
                $row_id = (int)$request->input('variation_id');
                $stock = (int)$request->input('stock');
                $result = '';
                $product_exist = Cart::instance('shopping')->search(function($cart,$key) use($row_id){
                    return $cart->id == $row_id;
                })->first();

                if($product_exist){
                    if($stock > $product_exist->qty || $stock == -1){
                        $result = Cart::instance('shopping')->update($product_exist->rowId,['qty'=>$product_exist->qty + 1]);
                    }else{
                        $stock_out = true;
                    }
                }else {
                    if($stock > 0 || $stock == -1){
                        $result = Cart::instance('shopping')->add(array(
                            'id'      => $row_id,
                            'name'    => $request->input('product_name'),
                            'price'   => $request->input('price'),
                            'qty'     => 1,
                            'weight'  => 0,
                            'options' => array(
                                'product_id'=> (int)$request->input('product_id'),
                                'discount'  => (int)$request->input('discount') != Null ? $request->input('discount') : 0
                            ),
                        ));
                    }else{
                        $stock_out = true;
                    }
                }

                if (!empty($result)) {
                    $sub_total = floatval(str_replace(',', '', Cart::instance('shopping')->subtotal()));
                    $this->output   = ['status' => 'success','message' => 'Item added into cart successfully.','row_id' => $result->rowId, 'sub_total' => $sub_total, 'result_qty' => $result->qty];
                }elseif($stock_out){
                    $this->output   = ['status' => 'danger','message' => 'Item is out of stock.'];
                }else{
                    $this->output   = ['status' => 'danger','message' => 'Failed to add item into cart.'];
                }
            }else{
                $this->output = $this->access_blocked();
            }
            return response()->json($this->output);
        }
    }

    public function update(Request $request){
        if($request->ajax()){
            $item = Cart::instance('shopping')->get($request->item_row_id);
            $product = ProductHasVariation::select('variation_qty')->where('id', $item->id)->first();

            $discount_change = 0;
            $result = '';
            if($request->type == "default"){
                if($product->variation_qty >= $request->quantity){
                    $discount_change = ($request->quantity - $item->qty) * $item->options->discount;
                    $result = Cart::instance('shopping')->update($request->item_row_id,['qty' => (int)$request->quantity]);
                }else{
                    $stock_out = true;
                }
            }else{
                $qty = $request->type == "plus"?(+$request->quantity):(-$request->quantity);
                if($product->variation_qty >= ($item->qty + $qty)){
                    $result = Cart::instance('shopping')->update($request->item_row_id, $item->qty + $qty);
                    $discount_change = $qty * $item->options->discount;
                }else{
                    $stock_out = true;
                }
            }

            if ($result) {
                $sub_total = floatval(str_replace(',', '', Cart::instance('shopping')->subtotal()));
                $row_sum = $result->qty * $result->price;
                $this->output   = ['status' => 'success','message' => 'Item updated successfully.','row_sum' => $row_sum, 'sub_total' => $sub_total, 'discount_change' => $discount_change];
            }elseif($stock_out){
                $this->output   = ['status' => 'danger','message' => 'Item is out of stock.'];
            }else{
                $this->output   = ['status' => 'danger','message' => 'Failed to update item.'];
            }
            return response()->json($this->output);
        }
    }


    public function removeItem(Request $request)
    {
        if($request->ajax()){

            $result = Cart::instance('shopping')->remove($request->item_row_id);
            $sub_total = floatval(str_replace(',', '', Cart::instance('shopping')->subtotal()));

            if (empty($result)) {
                $this->output   = ['status' => 'success','message' => 'Item removed from cart successfully.', 'sub_total' => $sub_total];
            }else{
                $this->output   = ['status' => 'danger','message' => 'Failed to remove item from cart.'];
            }
            return response()->json($this->output);
        }
    }

    public function destroy(Request $request)
    {
        if($request->ajax()){
            if($this->helper->permission('supplier-delete')){
                $params       = $request->except('_token');
                session(['update_order'=> []]);
                $result = Cart::instance('shopping')->destroy();
                if (empty($result)) {
                    $this->output   = ['status' => 'success','message' => 'Cart cleared successfully.'];
                }else{
                    $this->output   = ['status' => 'danger','message' => 'Failed to clear cart.'];
                }
            }else{
                $this->output = $this->access_blocked();
            }
            return response()->json($this->output);
        }
    }
}
