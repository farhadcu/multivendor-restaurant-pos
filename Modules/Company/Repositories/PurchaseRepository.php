<?php

namespace Modules\Company\Repositories;

use App\Repositories\BaseRepository;
use Validator;
use Modules\Company\Entities\Purchase;
use Modules\Company\Entities\PurchaseProduct;
use Modules\Company\Entities\PurchasePayment;
use Illuminate\Support\Facades\Crypt;
use Modules\Company\Contracts\PurchaseContract;
use Cart;
use Modules\Company\Entities\Product\ProductHasVariation;
use App\Traits\UploadAble;
use Illuminate\Http\UploadedFile;

class PurchaseRepository extends BaseRepository implements PurchaseContract
{
    use UploadAble;
    private $rules = [
        'status'           => 'required|numeric',
        'order_discount'   => 'required|numeric|min:0',
        'order_tax_amount' => 'required|numeric|min:0',
        'shipping_cost'    => 'required|numeric|min:0',
        'grand_total'      => 'required|numeric|min:1',
        'document'         => 'mimes:jpeg,jpg,png,pdf,doc,docx,csv,xlsx'
    ];
    private $id;
    private $purchase_prefix = 100;
    

    public function __construct(Purchase $model)
    {
        parent::__construct($model);
        $this->model  = $model;
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
        if(!empty($params['payment_status'])){
            $this->model->setPaymentStatus($params['payment_status']);
        }
        if(!empty($params['supplier_id'])){
            $this->model->setSupplierID($params['supplier_id']);
        }
        
        $this->model->setOrderValue($params['order']);
        $this->model->setDirValue($params['direction']);
        $this->model->setLengthValue($params['length']);
        $this->model->setStartValue($params['start']);

        $list   = $this->model->getList();
        $data   = array();
        $no     = $params['start'];
        foreach ($list as $value) {
            $no++;
            $action = '';
            if($this->helper->permission('purchase-edit')){
                $action .= '<li class="kt-nav__item"><a class="kt-nav__link edit_data" href="' .url('purchase/edit',Crypt::encrypt($value->id)). '" >'.EDIT_ICON.'</a></li>';
            }
            if($this->helper->permission('purchase-view')){
                $action .= '<li class="kt-nav__item"><a class="kt-nav__link" href="' .url('purchase/view',Crypt::encrypt($value->id)). '" >'.VIEW_ICON.'</a></li>';
            }
            if($this->helper->permission('purchase-delete')){
                $action .= '<li class="kt-nav__item"><a class="kt-nav__link delete_data" data-id="'.Crypt::encrypt($value->id).'" >'.DELETE_ICON.'</a></li>';
            }
            if($value->payment_status == 2){
                if($this->helper->permission('purchase-add')){
                    $action .= '<li class="kt-nav__item"><a class="kt-nav__link add_payment" data-amount="'.$value->due_amount.'" data-id="'.Crypt::encrypt($value->id).'" ><i class="fas fa-plus text-warning kt-nav__link-icon"></i> <span class="kt-nav__link-text">Add Payment</span></a></li>';
                }
            }
            if($this->helper->permission('purchase-view')){
                $action .= '<li class="kt-nav__item"><a class="kt-nav__link payment_list" data-id="'.Crypt::encrypt($value->id).'" ><i class="fas fa-money-bill-alt kt-nav__link-icon"></i> <span class="kt-nav__link-text">View Payment</span></a></li>';
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
            if($this->helper->permission('purchase-bulk-action-delete')){
            $row[]  = '<label class="kt-checkbox kt-checkbox--single kt-checkbox--all kt-checkbox--solid"><input type="checkbox" name="did[]" value="' . $value->id . '" class="select_data">&nbsp;<span></span></label> ';
            }
            $row[]  = $no;
            $row[]  = $value->purchase_no;
            $row[]  = !empty($value->supplier->supplier_name) ? $value->supplier->supplier_name : '';
            $row[]  = $value->total_item;
            $row[]  = PURCHASE_STATUS_LABEL[$value->status];
            $row[]  = number_format($value->grand_total,2);
            $row[]  = number_format($value->paid_amount,2);
            $row[]  = number_format($value->due_amount,2);
            $row[]  = PAYMENT_STATUS_LABEL[$value->payment_status];
            $row[]  = date('j-m-Y',strtotime($value->created_at));
            $row[]  = $btngroup;
            $data[] = $row;

        }
        return $this->dataTableDraw($params['draw'], $this->model->count_all(), $this->model->count_filtered(),$data);
    }

    public function createPurchase(array $params)
    {

        $validator  = Validator::make($params, $this->rules);
        if ($validator->fails()) {
            $output = array(
                'errors' => $validator->errors()
            );
        } else {

            $total_item       = Cart::instance('purchase')->content()->count();
            if ($total_item > 0)
            {
                $company_id = auth()->user()->company_id;
                $branch_id  = session()->get('branch');
                $history      = [];
                $history[] = [
                    'title'  => 'Purchase invoice created',
                    'text'   => 'Purchase invoice created by '.auth()->user()->name,
                    'date'   => DATE_FORMAT,
                    'mobile' => auth()->user()->mobile
                ];
                $history      = json_encode($history);
                $collection   = collect($params)->except(['payment_method']);
                $document = '';
                if($collection->has('document') && ($params['document'] instanceof  UploadedFile))
                {
                    $document = $this->upload_file($params['document'], PURCHASE_DOCUMENT);
                }
                $supplier_id = !empty($params['supplier_id']) ? $params['supplier_id'] : NULL;

                $merge        = $collection->merge([
                    'company_id'=> $company_id,
                    'branch_id' => $branch_id,
                    'supplier_id' => $supplier_id,
                    'total_item' => $total_item,
                    'total_cost' => str_replace(',', '', Cart::instance('purchase')->subtotal()),
                    'total_qty'  => Cart::instance('purchase')->count(),
                    'due_amount' => $params['grand_total'],
                    'history' => $history
                ]);
                // dd($merge->all());
                $purchase_id          = $this->create($merge->all())->id;

                if($purchase_id){

                    $this->update(['purchase_no' => ($this->purchase_prefix + $purchase_id)],$purchase_id);
                    $purchase_product = [];
                    foreach(Cart::instance('purchase')->content() as  $item)
                    {
                        $purchase_product[] = [
                            'company_id'     => $company_id,
                            'branch_id'      => $branch_id,
                            'purchase_id'    => $purchase_id,
                            'product_id'     => $item->options->product_id,
                            'product_variation_id' => $item->id,
                            'qty' => $item->qty,
                            'recieved' => ($params['status'] == 1) ? $item->qty : $item->options->received_qty,
                            'purchase_unit' => $item->options->purchase_unit,
                            'net_unit_cost' => $item->price,
                            'total' => $item->qty * $item->price,
                            'created_at' => DATE,
                            'updated_at' => DATE,
                        ];
                        $product_variation = ProductHasVariation::find($item->id);
                        if($params['status'] == 1){
                            $product_variation->variation_qty = $item->qty + $product_variation->variation_qty;
                        }else{
                            $product_variation->variation_qty = $item->options->received_qty + $product_variation->variation_qty;
                        }
                        $product_variation->update();
                    }
                    PurchaseProduct::insert($purchase_product);
                    // PurchasePayment::create([
                    //     'company_id'     => $company_id,
                    //     'branch_id'      => $branch_id,
                    //     'purchase_id'    => $purchase_id,
                    //     'payment_method' => $params['payment_method'],
                    //     'amount'         => $params['paid_amount'],
                    // ]);
                    Cart::instance('purchase')->destroy();
                    $output = ['status' => 'success','message' => 'Data has been saved successfully'];
                }else{
                    $this->delete_file($document,PURCHASE_DOCUMENT);
                    $output = ['status' => 'danger','message' => 'Data can not save'];
                }
            }else{
                $output = ['status' => 'danger','message' => 'Please add at least one product'];
            }
            
        }
        return $output;
        
    }

    public function showPurchase($id)
    {
        if($id)
        {
            $this->id             = Crypt::decrypt($id);
            $this->data           = $this->find((int)$this->id);
            $output['purchase']   = $this->data;
            $output['products']   = PurchaseProduct::select('purchase_products.*','pv.variation_name','pv.variation_model','p.name')
                ->leftjoin('product_has_variations as pv','purchase_products.product_variation_id','=','pv.id')
                ->leftjoin('products as p','pv.product_id','=','p.id')
                ->where('purchase_products.purchase_id',$this->id)->get();
            $output['payments']   = $this->data->payments;
            $output['supplier']   = $this->data->supplier;
            return $output;
        }
    }

    public function editPurchase($id)
    {
        if(!empty($id)){
            Cart::instance('purchase')->destroy();
            $this->id               = Crypt::decrypt($id);
            $this->data             = $this->find((int)$this->id);
            $collection             = collect($this->data)->except(['id','created_at','updated_at']);
            $merge                  = $collection->merge(compact('id'));
            if(!empty($merge))
            {
                $output = $merge->all();
                $data = [];
                $products = PurchaseProduct::select('purchase_products.*','pv.variation_name','pv.variation_model')
                ->leftjoin('product_has_variations as pv','purchase_products.product_variation_id','=','pv.id')
                ->where('purchase_products.purchase_id',$this->id)->get();
                if(!empty($products)){
                    foreach ($products as $key => $value) {
                        $data[$key]['id']                             = $value->product_variation_id;
                        $data[$key]['name']                           = $value->variation_name;
                        $data[$key]['price']                          = $value->net_unit_cost;
                        $data[$key]['qty']                            = $value->qty;
                        $data[$key]['options']['product_id']          = $value->product_id;
                        $data[$key]['options']['variation_model']     = $value->variation_model;
                        $data[$key]['options']['received_qty']        = $value->recieved ?? 1;
                        $data[$key]['options']['purchase_unit']       = $value->purchase_unit;
                    }
                }
                Cart::instance('purchase')->add($data);
            }else{
                $output             = '';
            }
        }else {
            $output                 = '';
        }
        return $output;
        
    }

    public function updatePurchase(array $params)
    {
            if(!empty($params['update_id'])){
                $this->id   = Crypt::decrypt($params['update_id']);
                $validator  = Validator::make($params, $this->rules);
                if ($validator->fails()) {
                    $output = array(
                        'errors' => $validator->errors()
                    );
                } else {
                    $purchase                                                   = $this->find($this->id);
                    $total_item                                                 = Cart::instance('purchase')->content()->count();
                    if ($total_item > 0)
                    {
                        $company_id                                             = auth()->user()->company_id;
                        $branch_id                                              = session()->get('branch');
                        $history                                                = [];
                        $history[]                                              = json_decode($purchase->history);
                        $history[] = [
                            'title'  => 'Purchase invoice updated',
                            'text'   => 'Purchase invoice updated by '.auth()->user()->name,
                            'date'   => DATE_FORMAT,
                            'mobile' => auth()->user()->mobile
                        ];
                        $history                                                = json_encode($history);
                        $collection                                             = collect($params)->except(['payment_method']);
                        $document                                               = $purchase->document;
                        if($collection->has('document') && ($params['document'] instanceof  UploadedFile))
                        {
                            $document                                           = $this->upload_file($params['document'], PURCHASE_DOCUMENT);
                            $this->delete_file($purchase->document,PURCHASE_DOCUMENT);
                        }
                        $supplier_id                                            = !empty($params['supplier_id']) ? $params['supplier_id'] : NULL;
                        $due_amount = $params['grand_total'] - $purchase->paid_amount;
                        $merge        = $collection->merge([
                            'company_id'=> $company_id,
                            'branch_id' => $branch_id,
                            'supplier_id' => $supplier_id,
                            'total_item' => $total_item,
                            'total_cost' => str_replace(',', '', Cart::instance('purchase')->subtotal()),
                            'total_qty'  => Cart::instance('purchase')->count(),
                            'due_amount' => $due_amount,
                            'payment_status' => ($due_amount > 0) ? 2 : 1,
                            'history' => $history
                        ]);
                        // dd($merge->all());
                        $purchase_update                                        = $this->update($merge->all(),$this->id);
        
                        if($purchase_update){

                            foreach(Cart::instance('purchase')->content() as  $item)
                            {
                                $existance_product                              = PurchaseProduct::where(['purchase_id' => $this->id,'product_id' => $item->options->product_id, 'product_variation_id'=>$item->id])->first();
  
                                if(!empty($existance_product))
                                {
                                    
                                    $product_variation                          = ProductHasVariation::find($item->id);
                                    $old_qty                                    = $product_variation->variation_qty - $existance_product->recieved;
                                    if($params['status'] == 1){
                                        $product_variation->variation_qty       = $item->qty + $old_qty;
                                    }else{
                                        $product_variation->variation_qty       = $item->options->received_qty + $old_qty;
                                    }
                                    $product_variation->update();

                                    $existance_product->company_id              = $company_id;
                                    $existance_product->branch_id               = $branch_id;
                                    $existance_product->purchase_id             = $this->id;
                                    $existance_product->product_id              = $item->options->product_id;
                                    $existance_product->product_variation_id    = $item->id;
                                    $existance_product->qty                     = $item->qty;
                                    $existance_product->recieved                = ($params['status'] == 1) ? $item->qty : $item->options->received_qty;
                                    $existance_product->purchase_unit           = $item->options->purchase_unit;
                                    $existance_product->net_unit_cost           = $item->price;
                                    $existance_product->total                   = $item->qty * $item->price;
                                    $existance_product->updated_at              = DATE;
                                    $existance_product->update();
                                    
                                }else{
                                    $purchase_product                           = new PurchaseProduct();
                                    $purchase_product->company_id               = $company_id;
                                    $purchase_product->branch_id                = $branch_id;
                                    $purchase_product->purchase_id              = $this->id;
                                    $purchase_product->product_id               = $item->options->product_id;
                                    $purchase_product->product_variation_id     = $item->id;
                                    $purchase_product->qty                      = $item->qty;
                                    $purchase_product->recieved                 = ($params['status'] == 1) ? $item->qty : $item->options->received_qty;
                                    $purchase_product->purchase_unit            = $item->options->purchase_unit;
                                    $purchase_product->net_unit_cost            = $item->price;
                                    $purchase_product->total                    = $item->qty * $item->price;
                                    $purchase_product->created_at               = DATE;
                                    $purchase_product->updated_at               = DATE;
                                    $purchase_product->save();
                                    
                                    $product_variation                          = ProductHasVariation::find($item->id);
                                    if($params['status'] == 1){
                                        $product_variation->variation_qty       = $item->qty + $product_variation->variation_qty;
                                    }else{
                                        $product_variation->variation_qty       = $item->options->received_qty + $product_variation->variation_qty;
                                    }
                                    $product_variation->update();
                                }
                            }
                            Cart::instance('purchase')->destroy();
                            $output   = ['status' => 'success','message' => 'Data has been saved successfully'];
                        }else{
                            $output   = ['status' => 'danger','message' => 'Data can not save'];
                        }
                    }else{
                        $output       = ['status' => 'danger','message' => 'Please add at least one product'];
                    }
                }
            }else{
                $output           = ['status' => 'danger','message' => 'Data can not update.'];
            }
            return $output;
    }



    public function deletePurchase(array $params)
    {
        if(!empty($params['id'])){
            $this->id     = Crypt::decrypt($params['id']);
            PurchasePayment::where([
                'purchase_id'=> $this->id,
                'company_id'=> auth()->user()->company_id,
                'branch_id' => session()->get('branch'),
                ])->delete();
            PurchaseProduct::where([
                'purchase_id'=> $this->id,
                'company_id'=> auth()->user()->company_id,
                'branch_id' => session()->get('branch'),
                ])->delete();
            $this->data   = $this->delete((int)$this->id);
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
            foreach ($this->id as $value) {
                PurchasePayment::where([
                    'purchase_id'=> $value,
                    'company_id'=> auth()->user()->company_id,
                    'branch_id' => session()->get('branch'),
                    ])->delete();
                PurchaseProduct::where([
                    'purchase_id'=> $value,
                    'company_id'=> auth()->user()->company_id,
                    'branch_id' => session()->get('branch'),
                    ])->delete();
            }
            $result       = $this->destroy($this->id);
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

    public function payment_list(array $params)
    {
        if($params['purchase_id']){
            $purchase = PurchasePayment::where([
                'purchase_id'=> Crypt::decrypt($params['purchase_id']),
                'company_id'=> auth()->user()->company_id,
                'branch_id' => session()->get('branch'),
                ])->get();
            $output = '';
            if(!empty($purchase))
            {
                $i=1;
                foreach ($purchase as $value) {
                    $action = '';
                    $output .= '<tr>';
                    $output .= '<td>'.$i++.'</td>';
                    $output .= '<td>'.date('j-M-Y',strtotime($value->created_at)).'</td>';
                    $output .= '<td>'.number_format($value->amount,2).'</td>';
                    $output .= '<td>'.PAYMENT_TYPE[$value->payment_method].'</td>';
                    if($this->helper->permission('purchase-edit')){
                        $action .= '<li class="kt-nav__item"><a class="kt-nav__link edit_payment" data-id="'.Crypt::encrypt($value->id).'" >'.EDIT_ICON.'</a></li>';
                    }
                    if($this->helper->permission('purchase-delete')){
                        $action .= '<li class="kt-nav__item"><a class="kt-nav__link delete_payment" data-purchase="'.Crypt::encrypt($value->purchase_id).'" data-id="'.Crypt::encrypt($value->id).'" >'.DELETE_ICON.'</a></li>';
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
                    $output .= '<td>'.$btngroup.'</td>';
                    $output .= '</tr>';
                }
                
            }
            return $output;
        }
    }

    public function add_payment(array $params)
    {
        if($params['purchase_id']){
            $rules['amount']            = 'required|numeric';
            $rules['payment_method']    = 'required|numeric';
            $validator                        = Validator::make($params, $rules);
            if ($validator->fails()) {
                $output                       = array(
                    'errors' => $validator->errors()
                );
            } else {
                $purchase                     = new PurchasePayment();
                $purchase->company_id         = auth()->user()->company_id;
                $purchase->branch_id          = session()->get('branch');
                $purchase->purchase_id        = Crypt::decrypt($params['purchase_id']);
                $purchase->payment_method     = $params['payment_method'];
                $purchase->amount             = $params['amount'];
                $purchase->created_at         = DATE;
                $purchase->updated_at         = DATE;
                if($purchase->save())
                {
                    $purchase                 = $this->find(Crypt::decrypt($params['purchase_id']));
                    $purchase->paid_amount    = $purchase->paid_amount + $params['amount'];
                    $due_amount               = $purchase->due_amount - $params['amount'];
                    $purchase->due_amount     = $due_amount;
                    $purchase->payment_status = ($due_amount > 0) ? 2 : 1;
                    $purchase->update();
                    $output                   = ['status' => 'success','message' => 'Payment data saved successfully.'];
                }else{
                    $output                   = ['status' => 'danger','message' => 'Payment data failed tp save.'];
                }
            }
            return $output;
        }
        
    }

    public function edit_payment(array $params)
    {
        if($params['id'])
        {
            $payment                        = PurchasePayment::find(Crypt::decrypt($params['id']));//fetch selected payment data
            $collection = collect($payment)->only(['purchase_id','amount','payment_method']);
            $id  = $params['id'];
            $merge = $collection->merge(compact('id'));
            if($payment)
            {
                $output['payment'] = $merge->all();
            }else{
                $output             = ['status' => 'danger','message' => 'No data found.'];
            }
        }else{
            $output             = ['status' => 'danger','message' => 'No data found.'];
        }
        return $output;
    }

    public function update_payment(array $params)
    {
        if($params['payment_id'] && $params['purchase_id'])
        {
            $payment                        = PurchasePayment::find(Crypt::decrypt($params['payment_id']));//fetch selected payment data
            $purchase                       = $this->find($params['purchase_id']);// fetch selected payment purcahse data
            $paid_amount                    = $purchase->paid_amount - $payment->amount;//old paid amount
            $new_paid_amount                = $paid_amount + $params['amount']; //updated paid amount
            $due_amount                     = $purchase->grand_total - $new_paid_amount;//update due amound
            $purchase->paid_amount          = $new_paid_amount;
            $purchase->due_amount           = $due_amount;
            $purchase->payment_status       = ($due_amount > 0) ? 2 : 1; //update payment status
            if($purchase->update())
            {
                $payment->amount            = $params['amount'];
                $payment->payment_method    = $params['payment_method'];
                $payment->update();
                $output                     = ['status' => 'success','message' => 'Payment data has been deleted successfully.'];
            }else{
                $output                     = ['status' => 'danger','message' => 'Unable to delete payment data.'];
            }
        }else{
            $output                     = ['status' => 'danger','message' => 'Unable to delete payment data.'];
        }
        return $output;
    }
    
    public function delete_payment(array $params)
    {
        if($params['id'] && $params['purchase_id'])
        {
            $payment                  = PurchasePayment::find(Crypt::decrypt($params['id']));//fetch selected payment data
            $purchase                 = $this->find(Crypt::decrypt($params['purchase_id']));// fetch selected payment purcahse data
            $paid_amount              = $purchase->paid_amount - $payment->amount;//update paid amount
            $due_amount               = $purchase->grand_total - $paid_amount;//update due amound 
            $purchase->paid_amount    = $paid_amount;
            $purchase->due_amount     = $due_amount;
            $purchase->payment_status = ($due_amount > 0) ? 2 : 1; //update payment status
            if($purchase->update())
            {
                $payment->delete();
                $output   = ['status' => 'success','message' => 'Payment data has been deleted successfully.'];
            }else{
                $output   = ['status' => 'danger','message' => 'Unable to delete payment data.'];
            }
        }else{
            $output   = ['status' => 'danger','message' => 'Unable to delete payment data.'];
        }
        return $output;
    }
}