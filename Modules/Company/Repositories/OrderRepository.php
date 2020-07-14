<?php

namespace Modules\Company\Repositories;

use Cart;
use App\Repositories\BaseRepository;
use Validator;
use Modules\Company\Entities\Order;
use Modules\Company\Entities\OrderHasProduct;
use Modules\Company\Entities\OrderHasPayment;
use Modules\Company\Entities\Product\ProductHasVariation;
use Modules\Company\Entities\Customer;
use Modules\Company\Entities\OrderTable;
use Illuminate\Support\Facades\Crypt;
use Modules\Company\Contracts\OrderContract;

class OrderRepository extends BaseRepository implements OrderContract
{
    private $rules = [
        // 'customer'  => 'required|integer',
    ];
    private $id;
    

    public function __construct(Order $model)
    {
        parent::__construct($model);
        $this->model  = $model;
    }

    public function index()
    {
        $data['customer'] = Customer::where(['company_id'=>auth()->user()->company_id,'branch_id'=>session()->get('branch')])
                            ->orderBy('name','asc')->get();
        $data['tables'] = OrderTable::where(['company_id'=>auth()->user()->company_id,'branch_id'=>session()->get('branch')])
                        ->orderBy('table_no','asc')->get();
        return $data;
    }


    public function getList(array $params)
    {
        if(!empty($params['from_date'])){
            $this->model->setFromDate($params['from_date']);
        }
        if(!empty($params['to_date'])){
            $this->model->setToDate($params['to_date']);
        }
        if(!empty($params['status'])){
            $this->model->setStatus($params['status']);
        }
        if(!empty($params['table_no'])){
            $this->model->setTableNo($params['table_no']);
        }
        if(!empty($params['customer_id'])){
            $this->model->setCustomerID($params['customer_id']);
        }

        $this->model->setOrderValue($params['order']);
        $this->model->setDirValue($params['direction']);
        $this->model->setLengthValue($params['length']);
        $this->model->setStartValue($params['start']);
        $this->model->orderType($params['order_type']);

        $list = $this->model->getList();;

        $data   = array();
        $no     = $params['start'];
        foreach ($list as $order) {
            $customer = empty($order->name)?"Walking Customer":$order->name;
            $no++;
            $action = '';
            if($params['order_type'] == "pending"){
                $action .= '<li class="kt-nav__item"><a class="kt-nav__link" onclick="edit_pending_order('.$order->id.')"><i class="kt-nav__link-icon flaticon2-contract text-info"></i> <span class="kt-nav__link-text">Edit</span></a></li>';
                $action .= '<li class="kt-nav__item"><a class="kt-nav__link" onclick="delete_pending_order('.$order->id.')"><i class="kt-nav__link-icon flaticon2-trash text-danger"></i> <span class="kt-nav__link-text">Delete</span></a></li>';
                $action .= '<li class="kt-nav__item"><a class="kt-nav__link" onclick="checkout_pending_order('.$order->id.',\''.$customer.'\',\''.$order->order_table_no.'\','.$order->total_amount.')"><i class="kt-nav__link-icon fab fa-opencart text-primary"></i> <span class="kt-nav__link-text">Checkout</span></a></li>';
            }else{
                $action .= '<li class="kt-nav__item"><a class="kt-nav__link" href="'.url('sale/sale-view',$order->id). '")"><i class="kt-nav__link-icon flaticon2-expand text-success"></i> <span class="kt-nav__link-text">View</span></a></li>';
            }
            $btngroup = '<span style="overflow: visible; position: relative;">   
                            <div class="dropdown"> 
                                <a data-toggle="dropdown" class="btn btn-sm btn-clean btn-icon btn-icon-lg cursor-pointer"> <i class="flaticon-more-1 text-brand"></i> </a>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <ul class="kt-nav">
                                        '.$action.'
                                    </ul>
                                </div>
                            </div>
                        </span>';


            $row    = array();
            $row[]  = '<label class="kt-checkbox kt-checkbox--single kt-checkbox--all kt-checkbox--solid"><input type="checkbox" name="did[]" value="' . $order->id . '" class="select_data">&nbsp;<span></span></label> ';
            $row[]  = $no;
            $row[]  = $order->table_no;
            $row[]  = $customer;
            if($params['order_type'] != "pending"){
                $row[]  = $order->total_item;
                $row[]  = $order->total_qty;
                $row[]  = $order->total_amount;
                $row[]  = $order->discount_amount;
                $row[]  = $order->adjusted_amount;
                $row[]  = $order->vat;
                $row[]  = $order->grand_total;
                $row[]  = $order->status == 1 ? '<span class="kt-badge kt-badge--success kt-badge--inline kt-badge--pill kt-badge--rounded">Complete</span>' :
                ($order->status == 2?'<span class="kt-badge kt-badge--warning kt-badge--inline kt-badge--pill kt-badge--rounded">Pending</span>':
                 '<span class="kt-badge kt-badge--danger kt-badge--inline kt-badge--pill kt-badge--rounded">Cancel</span>');
                $row[]  = date('Y-m-d', strtotime($order->created_at));
            }else{
                $row[]  = $order->grand_total;
            }
            $row[]  = $btngroup;
            $data[] = $row;
        }
        return $this->dataTableDraw($params['draw'], $this->model->count_all(), $this->model->count_filtered(),$data);
    }

    public function createOrder(array $params)
    {
        if($params['status'] == 1){
            $this->rules['table']  = 'required';
            $this->rules['payment_type']  = 'required';
            if($params['payment_type'] == 2){
                $this->rules['card_no']  = 'required|numeric|digits_between:12,16';
                $this->rules['cvc_no']  = 'required|digits:3';
                $this->rules['expire_date']  = 'required';
            }else if($params['payment_type'] == 4){
                $this->rules['m_banking_no']  = 'required|numeric|digits_between:11,12';
            }
        }
        $validator  = Validator::make($params, $this->rules);
        if ($validator->fails()) {
            $output = array(
                'errors' => $validator->errors()
            );
        } else {
            $order_id = '';
            $company_id = auth()->user()->company_id;
            $branch_id  = session()->get('branch');
            $total_item       = Cart::instance('shopping')->content()->count();
            if ($total_item > 0)
            {
                if(!empty(session()->get('update_order'))){
                    $old_order = session()->get('update_order')[0];
                    $old_orders = OrderHasProduct::select('product_variation_id','qty')->where(['order_id' => $old_order])->get();
                    OrderHasProduct::where(['order_id' => $old_order])->delete();
                    $this->delete($old_order);
                    foreach($old_orders as $old_order)
                    {
                        ProductHasVariation::where('id', $old_order->product_variation_id)->increment('variation_qty', $old_order->qty);
                    }
                }

                $history      = json_encode([
                    'title'  => 'Shopping invoice created',
                    'text'   => 'Shopping invoice created by '.auth()->user()->name,
                    'date'   => DATE_FORMAT,
                    'mobile' => auth()->user()->mobile
                ]);
                $collection   = collect($params);
                
                $merge        = $collection->merge([
                    'company_id'=> $company_id,
                    'branch_id' => $branch_id,
                    'customer_id' => !empty($params['customer']) ? $params['customer'] : null,
                    'table_no' => $params['table'],
                    'total_item' => $total_item,
                    'total_qty'  => Cart::instance('shopping')->count(),
                    'total_amount' => str_replace(',', '', Cart::instance('shopping')->subtotal()),
                    'discount_amount' => $params['discount'],
                    'vat_type' => $params['vat_type'],
                    'vat' => $params['vat'],
                    'adjustment_type' => $params['adjustment_type'],
                    'adjusted_amount' => $params['adjustment'],
                    'grand_total' => $params['total'],
                    'recevied_amount' => $params['recevied'],
                    'changed_amount' => $params['changed'],
                    'status' => $params['status'],
                    'history' => $history
                ]);
                $order_id          = $this->create($merge->all())->id;

                if($order_id){
                    $order_list = [];
                    foreach(Cart::instance('shopping')->content() as $item)
                    {
                        $discount = $item->options->discount > 0?($item->price * $item->options->discount/100) * $item->qty:0;
                        $sub_total = ($item->price * $item->qty) - $discount;
                        $order_list[] = [
                            'company_id'     => $company_id,
                            'branch_id'      => $branch_id,
                            'order_id'    => $order_id,
                            'product_id'     => $item->options->product_id,
                            'product_variation_id' => $item->id,
                            'qty' => $item->qty,
                            'price' => $item->price,
                            'discount' => $discount,
                            'subtotal' => $sub_total,
                            'created_at' => DATE,
                            'updated_at' => DATE,
                        ];

                        ProductHasVariation::where('id', $item->id)->decrement('variation_qty', $item->qty);
                    }
                    OrderHasProduct::insert($order_list);                  
                    Cart::instance('shopping')->destroy();
                    session(['update_order'=> []]);
                    $output = ['status' => 'success','message' => 'Order has been saved successfully', 'order_id' => $order_id];
                }else{
                    $output = ['status' => 'danger','message' => 'Order can not save'];
                }

            }else if($params['order_id'] != 0){
                Order::where('id', $params['order_id'])->update([
                    'table_no' => $params['table'],
                    'status' => $params['status']
                    ]);

                $order_id = $params['order_id'];
                $output = ['status' => 'success','message' => 'Order has been updated successfully', 'pending' => true];

            }
            else{
                $output = ['status' => 'danger','message' => 'Please add at least one product'];
            }

            if($order_id){
                if($params['status'] == 1){
                    OrderHasPayment::create([
                        'company_id'     => $company_id,
                        'branch_id'      => $branch_id,
                        'order_id'       => $order_id,
                        'payment_method' => $params['payment_type'],
                        'card_no'        => $params['card_no'],
                        'card_cvc'       => $params['cvc_no'],
                        'card_expire_at' => $params['expire_date'],
                        'mobile_no'      => $params['m_banking_no']
                    ]);
                }
            }
        }
        return $output;
    }

    public function invoice(array $params)
    {
        if(!empty($params['id'])){
            $data   = OrderHasProduct::select('products.name','order_has_products.qty','order_has_products.subtotal','order_has_products.price',
                        'order_has_products.discount','orders.total_amount','orders.vat_type','orders.vat','product_has_variations.variation_name',
                        'orders.grand_total','orders.discount_amount','orders.adjustment_type','orders.adjusted_amount','orders.id','orders.created_at',
                        'orders.status','customers.name as cname','customers.email','customers.mobile','customers.address')
                        ->where(['order_has_products.order_id' => $params['id']])
                        ->leftjoin('orders', 'order_has_products.order_id', '=', 'orders.id')
                        ->leftjoin('product_has_variations', 'order_has_products.product_variation_id', '=', 'product_has_variations.id')
                        ->leftjoin('products', 'product_has_variations.product_id', '=', 'products.id')
                        ->leftjoin('customers', 'orders.customer_id', '=', 'customers.id')
                        ->get();
                  
            if(!empty($data))
            {
                $output             = $data;
            }else{
                $output             = ['status' => 'danger','message' => 'No data found.'];
            }
        }else {
            $output                 = ['status' => 'danger','message' => 'No data found.'];
        }
        return $output;
        
    }

    public function editOrder(array $params)
    {
        if(!empty($params['id'])){
            $data   = OrderHasProduct::select('products.name as pname','order_has_products.qty','order_has_products.price',
                'product_has_discounts.discount_amount','product_has_discounts.discount_amount','product_has_discounts.discount_amount',
                'product_has_variations.variation_name','orders.id as order_id','orders.table_no','order_has_products.product_variation_id','products.id','orders.customer_id')
                ->where(['order_has_products.order_id' => $params['id']])
                ->leftjoin('orders', 'order_has_products.order_id', '=', 'orders.id')
                ->leftjoin('product_has_variations', 'order_has_products.product_variation_id', '=', 'product_has_variations.id')
                ->leftjoin('products', 'product_has_variations.product_id', '=', 'products.id')
                ->leftjoin('product_has_discounts', 'product_has_discounts.product_id', '=', 'products.id')->get();

            if(!empty($data))
            {
                Cart::instance('shopping')->destroy();
                session(['update_order'=> []]);
                foreach ($data as $order) {
                    $dicount = 0;
                    if($order->discount){
                        $todayDate = date("Y-m-d");
                        $startDate = $order->discount->start_date;
                        $endDate = $order->discount->end_date;
                        if($todayDate > $startDate && $todayDate < $endDate){
                            $dicount = $order->discount->discount_amount;
                        }
                    }

                    $product_name = $order->pname == $order->variation_name ? $order->pname : $order->pname.'('.$order->variation_name.')';
                    Cart::instance('shopping')->add(array(
                        'id' => $order->product_variation_id,
                        'name' => $product_name,
                        'price' => $order->price,
                        'qty' => $order->qty,
                        'weight' => 0,
                        'options' => array(
                            'product_id'=> $order->id,
                            'discount'=> $dicount
                        ),
                    ));
                }
                $cartData = Cart::instance('shopping')->content();
                session(['update_order'=> [$data[0]->order_id,$data[0]->customer_id!= Null?$data[0]->customer_id:'',$data[0]->table_no]]);
                $output = ['status' => 'success','order' => $cartData, 'update_order' => session()->get('update_order')];
            }else{
                $output = ['status' => 'danger','message' => 'No data found.'];
            }
        }else {
            $output     = ['status' => 'danger','message' => 'No data found.'];
        }
        return $output;
        
    }

    public function updateOrder(array $params)
    {
            if(!empty($params['update_id'])){
                $this->id   = Crypt::decrypt($params['update_id']);
                $method     = $this->find($this->id);
                $this->rules['supplier_email']  = 'email|unique:suppliers,supplier_email,'.$method->id;
                $validator  = Validator::make($params, $this->rules);
                if ($validator->fails()) {
                    $output = array(
                        'errors' => $validator->errors()
                    );
                } else {
                    $collection   = collect($params)->except(['update_id']);
                    $merge        = $collection->merge([
                                        'company_id' => auth()->user()->company_id,
                                        'branch_id'  => session()->get('branch'),
                                        'updated_at' => DATE,
                                    ]);
                    $result       = $this->update($merge->all(),(int)$this->id);
                    if ($result) {
                        $output   = ['status' => 'success','message' => 'Data has been updated successfully.'];
                    }else{
                        $output   = ['status' => 'danger','message' => 'Data can not update.'];
                    }
                }
            }else{
                $output           = ['status' => 'danger','message' => 'Data can not update.'];
            }
            return $output;
    }

    public function deleteOrder(array $params)
    {
        if(!empty($params['id'])){
            $old_orders = OrderHasProduct::select('product_variation_id','order_has_products.qty','subtract_stock')
                            ->leftjoin('products', 'order_has_products.product_id', '=', 'products.id')
                            ->where(['order_has_products.order_id' => $params['id'],'subtract_stock' => 1])->get();
     
            OrderHasProduct::where(['order_id' => $params['id']])->delete();
            $this->data   = $this->delete($params['id']);
            foreach($old_orders as $old_order)
            {
                ProductHasVariation::where('id', $old_order->product_variation_id)->increment('variation_qty', $old_order->qty);
            }
            
            if ($this->data) {
                $output   = ['status' => 'success','message' => 'Data has been deleted successfully.'];
            } else {
                $output   = ['status' => 'danger','message' => 'Unable to delete data.'];
            }  
        }else {
            $output       = ['status' => 'danger','message' => 'Unable to delete data.'];
        }
        return $output;
    }

    public function bulk_action_delete(array $params)
    {
        $this->id         = $params['id'];
        if(!empty($this->id) && count($this->id) > 0){
            $old_orders = OrderHasProduct::select('product_variation_id','order_has_products.qty','subtract_stock')
                            ->leftjoin('products', 'order_has_products.product_id', '=', 'products.id')
                            ->whereIn('order_id', $params['id'])->where(['subtract_stock' => 1])->get();

            OrderHasProduct::whereIn('order_id', $params['id'])->delete();
            $result       = $this->destroy($this->id);
            foreach($old_orders as $old_order)
            {
                ProductHasVariation::where('id', $old_order->product_variation_id)->increment('variation_qty', $old_order->qty);
            }

            if($result){
                $output   = ['status' => 'success','message' => 'Data has been deleted successfully.'];
            }else{
                $output   = ['status' => 'danger','message' => 'Unable to delete data.'];
            }
        }else{
            $output       = ['status' => 'danger','message' => 'Unable to delete data.'];
        }
        return $output;
    }
}