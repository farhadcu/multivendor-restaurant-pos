<?php

namespace Modules\Company\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Cart;
use Modules\Company\Entities\Product\ProductHasVariation;
use DB;
class PurchaseCartController extends BaseController
{

    public function index()
    {
        $units = DB::table('units')->OrderBy('unit_short','asc')->get();
        return view('company::purchase.cart-content',compact('units'))->render();
        
    }

    public function store(Request $request)
    {
        if($request->ajax())
        {
            
            $product = ProductHasVariation::find($request->product_variation_id);
            
            $output = "";
            $pid = $request->product_variation_id;
            if(!empty($product)){
                // $cart = Cart::get($request->product_variation_id);
                $product_exist = Cart::instance('purchase')->search(function ($cart, $key) use($pid) {
                    return $cart->id == $pid;
                })->first();
                if($product_exist){
                    $output = 'Exist';
                }else{
                    $data                                   = [];
                    $data['id']                             = $request->product_variation_id;
                    $data['name']                           = $product->variation_name;
                    $data['price']                          = $product->product->purchase_price ?? 0;
                    $data['qty']                            = 1;
                    $data['weight'] = 0;
                    $data['options']['product_id']          = $product->product_id;
                    $data['options']['variation_model']     = $product->variation_model;
                    $data['options']['received_qty']        = 1;
                    $data['options']['purchase_unit']       = $product->product->stock_unit;
                    
                    Cart::instance('purchase')->add($data);
                    $output = 'Added';
                }
            }
            return response()->json($output);
        }
    }



    public function update(Request $request)
    {
        if($request->ajax())
        {
            // $data = [];
            $data['qty']          = $request->qty;
            $data['price']        = $request->price;
            $data['weight'] = 0;
            $data['options']['product_id']         = $request->product_id;
            $data['options']['variation_model']    = $request->variation_model;
            $data['options']['received_qty']       = $request->received_qty;
            $data['options']['purchase_unit']      = $request->purchase_unit;
            Cart::instance('purchase')->update($request->product_variation_id,$data);
            $output = 'Updated';
            return response()->json($output);
        }

    }

    public function destroy(Request $request)
    {
        if($request->ajax())
        {
            Cart::instance('purchase')->remove($request->product_variation_id);
            $output = 'Removed';
            return response()->json($output);
        }

    }

    public function clear(Request $request)
    {
        if($request->ajax())
        {
            Cart::instance('purchase')->destroy();
            $output = 'Cleared';
            return response()->json($output);
        }

    }



}
