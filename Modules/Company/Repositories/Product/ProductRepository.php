<?php 

namespace Modules\Company\Repositories\Product;


use App\Repositories\BaseRepository;
use Validator;
use Modules\Company\Entities\Product\Product;
use Modules\Company\Entities\Product\ProductHasCategory;
use Modules\Company\Entities\Product\ProductHasDiscount;
use Modules\Company\Entities\Product\ProductHasVariation;
use Illuminate\Support\Facades\Crypt;
use Modules\Company\Contracts\Product\ProductContract;
use App\Traits\UploadAble;
use Illuminate\Http\UploadedFile;
use DB;

class ProductRepository extends BaseRepository implements ProductContract
{
    use UploadAble;
    private $rules = [
        'name'             => 'required|string',
        'model'            => 'required|numeric|max:12',
        'image'            => 'image|mimes:jpeg,jpg,png',
        'purchase_price'   => 'numeric',
        'selling_price'    => 'required|numeric',
        'qty'              => 'numeric',
        'min_qty'          => 'numeric',
        'max_qty'          => 'numeric',
        'discount_qty'     => 'numeric',
        'discount_amount'  => 'numeric',
        
    ];
    private $id;

    public function __construct(Product $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function index(array $params)
    {
        $query = Product::select('products.*','product_has_categories.category_id')
        ->join('product_has_categories', 'products.id', '=', 'product_has_categories.product_id')
        ->where(['products.company_id'=>auth()->user()->company_id,'products.branch_id'=>session()->get('branch'),
                'products.status'=>1])
                ->orderBy('products.name','asc')
                ->offset($params['start'])
                ->limit($params['limit']);
        
        if($params['name'] != ""){
            $query = $query->where('products.name','like', '%'.$params['name'].'%')
            ->orWhere('products.model', 'like', '%'.$params['name'].'%');
        }
        if($params['category'] != ""){
            $query = $query->where(['product_has_categories.category_id'=>$params['category']]);
        }
        return $query->get(); 
    }

    public function getList(array $params)
    {
        
        if(!empty($params['name'])){
            $this->model->setName($params['name']);
        }
        if(!empty($params['model'])){
            $this->model->setModel($params['model']);
        }
        if(!empty($params['returnable'])){
            $this->model->setReturnable($params['returnable']);
        }
        if(!empty($params['rack_no'])){
            $this->model->setRackNo($params['rack_no']);
        }
        if(!empty($params['status'])){
            $this->model->setStatus($params['status']);
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
            if($this->helper->permission('product-edit')){
                $action .= '<li class="kt-nav__item"><a class="kt-nav__link edit_data" data-id="' . Crypt::encrypt($value->id) . '" >'.EDIT_ICON.'</a></li>';
                $action .= '<li class="kt-nav__item"><a class="kt-nav__link generate_barcode" data-id="' . Crypt::encrypt($value->id) . ')"><i class="kt-nav__link-icon fa fa-barcode text-success"></i> <span class="kt-nav__link-text">Barcode</span></a></li>';
            }

            if($this->helper->permission('product-delete')){
                $action .= '<li class="kt-nav__item"><a class="kt-nav__link delete_data" data-id="'.Crypt::encrypt($value->id).'" >'.DELETE_ICON.'</a></li>';
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
            $status = [
                'id'      => Crypt::encrypt($value->id),
                'checked' => SWITCH_STATUS[$value->status]
            ];
            $row    = array();
            if($this->helper->permission('product-bulk-action-delete')){
            $row[]  = '<label class="kt-checkbox kt-checkbox--single kt-checkbox--all kt-checkbox--solid"><input type="checkbox" name="did[]" value="' . $value->id . '" class="select_data">&nbsp;<span></span></label> ';
            }
            $row[]  = $no;
            $row[]  = $this->image_view($value->image);
            $row[]  = $value->model;
            $row[]  = $value->name;
            $row[]  = $value->qty.$value->stock_unit;
            $row[]  = $value->min_qty.$value->stock_unit;
            $row[]  = $value->max_qty.$value->stock_unit;
            $row[]  = number_format($value->purchase_price,2);
            $row[]  = number_format($value->selling_price,2);
            $row[]  = ($value->returnable == 1) ? '<span class="kt-badge kt-badge--success kt-badge--inline kt-badge--pill kt-badge--rounded">YES</span>' : '<span class="kt-badge kt-badge--danger kt-badge--inline kt-badge--pill kt-badge--rounded">NO</span>';
            $row[]  = !empty($value->rack_no) ? $value->rack_no : '<span class="kt-badge kt-badge--info kt-badge--inline kt-badge--pill kt-badge--rounded">NO</span>';
            if($this->helper->permission('product-change-status')) {  
            $row[]  = $this->switch_view($status);
            }
            $row[]  = $btngroup;
            $data[] = $row;

        }
        return $this->dataTableDraw($params['draw'], $this->model->count_all(),
        $this->model->count_filtered(),$data);
        
    }

    private function image_view(string $image = null)
    {
        if(!empty($image))
        {
            $img =  "<img src='".asset(FOLDER_PATH.PRODUCT_IMAGE.$image)."' style='width:80px;' />";
        }else{
            $img =  "<img src='./public/img/no-image.png' style='width:80px;' />";
        }
        return $img;
    }


    public function createProduct(array $params)
    {

        $this->rules['start_date']  = ['date','date_format:Y-m-d','after_or_equal:'.date('Y-m-d')];
        $this->rules['end_date']    = 'date|date_format:Y-m-d|after_or_equal:start_date';
        
        $validator = $this->variation_rules($params);
        if ($validator->fails()) {
            $output = array(
                'errors' => $validator->errors()
            );
        } else {

            $collection                           = collect($params)->except(['supplier_id','category_id','discount_qty','purchase_price','qty','min_qty','max_qty',
            'subtract_stock','returnable','discount_amount','start_date','end_date','is_variant','variant','variation']);
            $supplier_id                          = !empty($params['supplier_id']) ? $params['supplier_id'] : NULL;
            $purchase_price                       = !empty($params['purchase_price']) ? $params['purchase_price'] : NULL;
            $qty                                  = !empty($params['qty']) ? $params['qty'] : NULL;
            $min_qty                              = !empty($params['min_qty']) ? $params['min_qty'] : NULL;
            $max_qty                              = !empty($params['max_qty']) ? $params['max_qty'] : NULL;
            $subtract_stock                       = !empty($params['subtract_stock']) ? $params['subtract_stock'] : 2;
            $returnable                           = !empty($params['returnable']) ? $params['returnable'] : 2;
            $image                                = null;
            if ($collection->has('image') && ($params['image'] instanceof  UploadedFile)) {
                $image                            = $this->upload_file($params['image'], PRODUCT_IMAGE);
            }
            $history                              = [];
            $history[] = [
                'title'  => 'Product created',
                'text'   => 'Product created by '.auth()->user()->name,
                'date'   => DATE_FORMAT,
                'mobile' => auth()->user()->mobile
            ];
            $history                              = json_encode($history);
            $company_id                           = auth()->user()->company_id;
            $branch_id                            = session()->get('branch');
            $merge                                = $collection->merge(compact('supplier_id','company_id','branch_id','image','history','purchase_price','qty','min_qty','max_qty','subtract_stock','returnable'));
            $result                               = $this->create($merge->all());
            if ($result) {
                if (!empty($params['discount_amount'])) {
                    $discount                     = new ProductHasDiscount();
                    $discount->product_id         = $result->id;
                    $discount->discount_qty       = !empty($params['discount_qty']) ? $params['discount_qty'] : NULL;
                    $discount->discount_amount    = $params['discount_amount'];
                    $discount->start_date         = !empty($params['start_date']) ? $params['start_date'] : NULL;
                    $discount->end_date           = !empty($params['end_date']) ? $params['end_date'] : NULL;
                    $discount->created_at         = DATE;
                    $discount->updated_at         = DATE;
                    $discount->save();
                }
                if(!empty($params['category_id']) && count($params['category_id']) > 0){
                    $product_categories           = [];
                    foreach ($params['category_id'] as $value) {
                        $product_categories[] = [
                            'product_id'    => $result->id,
                            'category_id'   => $value,
                            'created_at'    => DATE,
                            'updated_at'    => DATE
                        ];
                    }
                    ProductHasCategory::insert($product_categories);
                }
                $variatio_collection              = collect($params);
                if($variatio_collection->has('variation')){
                    $variation                    = [];
                    foreach($params['variation'] as $key => $value)
                    {
                        $variation[] = [
                            'product_id'       => $result->id, 
                            'variation_name'   => $value['variation_name'], 
                            'variation_model'  => $value['variation_model'], 
                            'variation_qty'    => !empty($value['variation_qty']) ? $value['variation_qty'] : NULL, 
                            'price_prefix'     => $value['price_prefix'], 
                            'variation_price'  => !empty($value['variation_price']) ? $value['variation_price'] : NULL, 
                            'variation_weight' => $value['variation_weight'], 
                            'is_primary'       => $value['primary'], 
                            'created_at'       => DATE, 
                            'updated_at'       => DATE
                        ];
                    }
                    ProductHasVariation::insert($variation);
                }else{
                    $variation                    = new ProductHasVariation();
                    $variation->product_id        = $result->id;
                    $variation->variation_name    = $params['name'];
                    $variation->variation_model   = $params['model'];
                    $variation->variation_qty     = $qty;
                    $variation->price_prefix      = NULL;
                    $variation->variation_price   = $params['selling_price'];
                    $variation->variation_weight  = $params['weight'];
                    $variation->is_primary        = 1;
                    $variation->created_at        = DATE;
                    $variation->updated_at        = DATE;
                    $variation->save();
                }
                $output                           = ['status' => 'success', 'message' => 'Data has been saved successfully.'];
            }else{
                if($image != NULL){
                    $this->delete_file($image,PRODUCT_IMAGE);
                }
                $output                           = ['status' => 'danger', 'message' => 'Data can not save.'];
            }
        }
        return $output;
    }

    public function showProduct(array $params)
    {
        if(!empty($params['id']))
        {
            $this->id         = Crypt::decrypt($params['id']);
            $data             = $this->find((int) $this->id);
        }
    }

    public function editProduct(array $params)
    {
        if(!empty($params['id'])){
            $this->id               = Crypt::decrypt($params['id']);
            $this->data             = $this->find((int) $this->id);
            $collection             = collect($this->data)->except(['id','created_at','updated_at']);
            $id                     = $params['id'];
            $merge                  = $collection->merge(compact('id'));
            $variation = '';
            $categories               = [];
            $discount                 = collect($this->data->discount);
            if(!empty($this->data->categories) && count($this->data->categories) > 0){
                foreach ($this->data->categories as  $value) {
                    array_push($categories,$value->category_id);
                }
            }
            if(count($this->data->variation) > 1){
                foreach($this->data->variation as $key => $value) {
                    $prefix_plus = ($value->price_prefix == "+") ? "selected" : " ";
                    $prefix_minus = ($value->price_prefix == "-") ? "selected" : " ";
                    $primary_yes  = ($value->is_primary == 1) ? "selected" : " ";
                    $primary_no  = ($value->is_primary == 2) ? "selected" : " ";
                    $variation .= '<tr>';
                    $variation .= '<td><input type="text" class="form-control" value="'.$value->variation_name.'" id="variation_'.($key+1).'_variation_name" name="variation['.($key+1).'][variation_name]"  /></td>';
                    $variation .= '<td><input type="text" class="form-control" value="'.$value->variation_model.'" id="variation_'.($key+1).'_variation_model" name="variation['.($key+1).'][variation_model]"  /></td>';
                    $variation .= '<td  class="text-center"><input type="text" value="'.$value->variation_qty.'" id="variation_'.($key+1).'_variation_qty" class="form-control text-center" name="variation['.($key+1).'][variation_qty]" value="" step="any" /></td>';
                    $variation .= '<td  class="text-center"><select class="form-control text-center" id="variation_'.($key+1).'_price_prefix" name="variation['.($key+1).'][price_prefix]">';
                    $variation .= '    <option '.$prefix_plus.' value="+">+</option>';
                    $variation .= '    <option '.$prefix_minus.'  value="-">-</option>';
                    $variation .= '</select></td>';
                    $variation .= '<td class="text-right"><input type="text" value="'.$value->variation_price.'" class="form-control text-right" id="variation_'.($key+1).'_variation_price" name="variation['.($key+1).'][variation_price]" value="" step="any" /></td>';
                    $variation .= '<td class="text-right"><input type="text" value="'.$value->variation_weight.'" class="form-control text-right" id="variation_'.($key+1).'_variation_weight" name="variation['.($key+1).'][variation_weight]" value="" step="any" /></td>';
                    $variation .= '<td  class="text-center"><select class="form-control text-center" id="variation_'.($key+1).'_primary" name="variation['.($key+1).'][primary]">';
                    $variation .= '    <option '.$primary_yes.'  value="1">Yes</option>';
                    $variation .= '    <option '.$primary_no.'  value="2">No</option>';
                    $variation .= '</select></td>';
                    $variation .= '<td class="text-right"><button type="button" class="vbtnDel btn btn-icon-sm btn-sm btn-danger" style="margin-top: 4px;"><i class="fas fa-trash"></i></button></td>';
                    $variation .= '</tr>';$variation .= '<tr>';
                }
            }
            if(!empty($merge))
            {
                $output['product']   = $merge->all();
                $output['variation'] = $variation;
                $output['discount']  = $discount->all();
                $output['category']  = $categories;
                $output['row'] = count($this->data->variation);
                
            }else{
                $output             = ['status' => 'danger','message' => 'No Data Found'];
            }
        }else{
            $output                 = ['status' => 'danger','message' => 'No Data Found'];
        }

        return $output;
    }

    public function updateProduct(array $params)
    {
        if(!empty($params['update_id'])){
            $this->id                       = Crypt::decrypt($params['update_id']);
            $product                       = $this->find($this->id);
            $this->rules['start_date']  = ['date','date_format:Y-m-d'];
            $this->rules['end_date']    = 'date|date_format:Y-m-d|after_or_equal:start_date';
            $validator = $this->variation_rules($params);
            $validator                      = Validator::make($params, $this->rules);
            if ($validator->fails()) {
                $output                     = array( 'errors' => $validator->errors());
            } else {
                $collection                           = collect($params)->except(['update_id','supplier_id','category_id','discount_qty','purchase_price','qty','min_qty','max_qty',
                'subtract_stock','returnable','discount_amount','start_date','end_date','is_variant','variant','variation']);
                $supplier_id                          = !empty($params['supplier_id']) ? $params['supplier_id'] : NULL;
                $purchase_price                       = !empty($params['purchase_price']) ? $params['purchase_price'] : NULL;
                $qty                                  = !empty($params['qty']) ? $params['qty'] : NULL;
                $min_qty                              = !empty($params['min_qty']) ? $params['min_qty'] : NULL;
                $max_qty                              = !empty($params['max_qty']) ? $params['max_qty'] : NULL;
                $subtract_stock                       = !empty($params['subtract_stock']) ? $params['subtract_stock'] : 2;
                $returnable                           = !empty($params['returnable']) ? $params['returnable'] : 2;
                $image                                = $product->image;
                if ($collection->has('image') && ($params['image'] instanceof  UploadedFile)) {
                    $image                            = $this->upload_file($params['image'], PRODUCT_IMAGE);
                    if(!empty($product->image)){
                        $this->delete_file($product->image,PRODUCT_IMAGE);
                    }
                }
                $history                              = [];
                $history[] = json_decode($product->history);
                $history[] = [
                    'title'  => 'Product updated',
                    'text'   => 'Product updated by '.auth()->user()->name,
                    'date'   => DATE_FORMAT,
                    'mobile' => auth()->user()->mobile
                ];
                $history                              = json_encode($history);
                $company_id                           = auth()->user()->company_id;
                $branch_id                            = session()->get('branch');
                $merge                                = $collection->merge(compact('supplier_id','company_id','branch_id','image','history','purchase_price','qty','min_qty','max_qty','subtract_stock','returnable'));

                $result                     = $this->update($merge->all(),(int)$this->id);
                if ($result) {
                    if (!empty($params['discount_amount'])) {
                        $discount                     = ProductHasDiscount::where('product_id',$this->id)->first();
                        $discount->product_id         = $this->id;
                        $discount->discount_qty       = !empty($params['discount_qty']) ? $params['discount_qty'] : NULL;
                        $discount->discount_amount    = $params['discount_amount'];
                        $discount->start_date         = !empty($params['start_date']) ? $params['start_date'] : NULL;
                        $discount->end_date           = !empty($params['end_date']) ? $params['end_date'] : NULL;
                        $discount->created_at         = DATE;
                        $discount->updated_at         = DATE;
                        $discount->update();
                    }
                    if(!empty($params['category_id']) && count($params['category_id']) > 0){
                        $product_categories           = [];
                        foreach ($params['category_id'] as $value) {
                            $product_categories[] = [
                                'product_id'    => $this->id,
                                'category_id'   => $value,
                                'created_at'    => DATE,
                                'updated_at'    => DATE
                            ];
                        }
                        ProductHasCategory::where('product_id',$this->id)->delete();
                        ProductHasCategory::insert($product_categories);
                    }
                    $variatio_collection              = collect($params);
                    if($variatio_collection->has('variation')){
                        $variation                    = [];
                        foreach($params['variation'] as $key => $value)
                        {
                            $variation[] = [
                                'product_id'       => $this->id, 
                                'variation_name'   => $value['variation_name'], 
                                'variation_model'  => $value['variation_model'], 
                                'variation_qty'    => !empty($value['variation_qty']) ? $value['variation_qty'] : NULL, 
                                'price_prefix'     => $value['price_prefix'], 
                                'variation_price'  => !empty($value['variation_price']) ? $value['variation_price'] : NULL, 
                                'variation_weight' => $value['variation_weight'], 
                                'is_primary'       => $value['primary'], 
                                'created_at'       => DATE, 
                                'updated_at'       => DATE
                            ];
                        }
                        ProductHasVariation::where('product_id',$this->id)->delete();
                        ProductHasVariation::insert($variation);
                    }else{
                        $variation                    = ProductHasVariation::where(['product_id'=>$this->id,'is_primary'=>1])->first();
                        $variation->product_id        = $this->id;
                        $variation->variation_name    = $params['name'];
                        $variation->variation_model   = $params['model'];
                        $variation->variation_qty     = $qty;
                        $variation->price_prefix      = NULL;
                        $variation->variation_price   = $params['selling_price'];
                        $variation->variation_weight  = $params['weight'];
                        $variation->is_primary        = 1;
                        $variation->created_at        = DATE;
                        $variation->updated_at        = DATE;
                        $variation->update();
                    }
                    $output                 = ['status'  => 'success','message' => 'Data has been updated successfully'];
                }else{
                    $output                 = ['status' => 'danger','message' => 'Data can not update'];
                }
            }
        }else{
            $output                         = ['status' => 'danger','message' => 'Data can not update'];
        }
        return $output;
    }

    public function change_status(array $params)
    {
        if(!empty($params['id'])){
            $this->id = Crypt::decrypt($params['id']);
            if((int)$params['status']){
                $collection  = collect($params)->except('id');
                $updated_at  = DATE;
                $merge       = $collection->merge(compact('updated_at'));
                $result      = $this->update($merge->all(),(int)$this->id);
                if($result){
                    $output['status']  = 'success';
                    $output['message'] = 'Status changed successfully';
                }else{
                    $output['status']   = 'danger';
                    $output['message']  = 'Status can not change';
                }
            }else{
                $output['status']   = 'danger';
                $output['message']  = 'Status can not change';
            }
        }else{
            $output['status']   = 'danger';
            $output['message']  = 'Status can not change';
        }
        return $output;
    }

    public function deleteProduct(array $params)
    {
        if(!empty($params['id'])){
            $this->id   = Crypt::decrypt($params['id']);

            $result     = $this->delete((int)$this->id);
            if ($result) {
                $output = ['status'  => 'success','message' => 'Data has been deleted successfully.'];
            } else {
                $output = ['status'  => 'error','message' => 'Unable to delete data.'];
            }    
        } else {
            $output     = ['status'  => 'error','message' => 'Unable to delete data.'];
        }
        return $output;
    }

    public function bulk_action_delete(array $params)
    {
        $this->id         = $params['id'];
        if(!empty($this->id) && count($this->id) > 0){
            $delete       = $this->destroy($this->id);
            if($delete){
                $output   = ['status'  => 'success','message' => 'Data has been deleted successfully.'];
            }else{
                $output   = ['status'  => 'error','message' => 'Unable to delete data.'];
            }
        }else{
            $output       = ['status'  => 'error','message' => 'Unable to delete data.'];
        }
        return $output;
    }

    
    private function variation_rules(array $params)
    {
        $collection = collect($params);
        $message = [];
        if($collection->has('variation'))
        {
            foreach($params['variation'] as $key => $val)
            {
                $this->rules['variation.'.$key.'.variation_name']         = 'required|string';
                $this->rules['variation.'.$key.'.variation_model']        = 'required|numeric|max:12';
                $this->rules['variation.'.$key.'.variation_qty']          = 'numeric';
                $this->rules['variation.'.$key.'.variation_price']          = 'numeric';

                $message['variation.'.$key.'.variation_name.required']    = 'The name field is required';
                $message['variation.'.$key.'.variation_name.string']      = 'The name field value must be string';
                $message['variation.'.$key.'.variation_model.required']   = 'The model is required';
                $message['variation.'.$key.'.variation_model.string']     = 'The model field value must be string';
                $message['variation.'.$key.'.variation_qty.numeric']      = 'The qty field value must be numeric';
                $message['variation.'.$key.'.variation_price.numeric']      = 'The qty field value must be numeric';

            }

        }

       return  Validator::make($params, $this->rules, $message);
    }

    public function autocomplete_search_product($params)
    {
        if(!empty($params)){
            $data = DB::table('product_has_variations as pv')->select('pv.*','p.company_id','p.branch_id','p.name',
            'p.image','p.purchase_price','p.selling_price','p.qty','p.min_qty','p.max_qty','p.stock_unit','d.discount_amount')
            ->join('products as p', 'pv.product_id', '=', 'p.id')
            ->leftjoin('product_has_discounts as d', 'pv.product_id', '=', 'd.product_id')
                            ->where(['p.company_id'=>auth()->user()->company_id,
                            'p.branch_id'=>session()->get('branch'),'p.status'=>1])
                            ->where('pv.variation_name', 'like','%'.$params.'%')
                            ->orWhere('pv.variation_model', 'like','%'.$params.'%')
                            ->get();

            $output = array();
            if(!empty($data) && count($data) > 0)
            {
                foreach($data as $row)
                {
                    $temp_array             = array();
                    if(!empty($row->image)){
                        $img                = asset(FOLDER_PATH.PRODUCT_IMAGE.$row->image);
                    }else{
                        $img                = './public/img/no-image.png';
                    }
                    
                    $product_price = $row->selling_price;
                    if($row->price_prefix == '+'){
                        $product_price = $row->selling_price + $row->variation_price;
                    }elseif($row->price_prefix == '-'){
                        $product_price = $row->selling_price - $row->variation_price;
                    }

                    $temp_array['id']         = $row->id;
                    $temp_array['product_id'] = $row->product_id;
                    $temp_array['value']      = $row->variation_name.' - '.$row->variation_model;
                    $temp_array['name']       = $row->name.'('.$row->variation_name.')';
                    $temp_array['price']      = $product_price;
                    $temp_array['qty']        = $row->variation_qty;
                    $temp_array['dicount']    = $row->discount_amount != Null?$row->discount_amount:0;
                    $temp_array['weight']     = $row->variation_weight != Null?$row->variation_weight:0;
                    $temp_array['label']      = '<img src="'.$img.'" width="70" />&nbsp;&nbsp;&nbsp;'.$row->variation_model.' | '.$row->variation_name;
                    $output[]                 = $temp_array;
                }
            } else{
                $output['value']            = '';
                $output['label']            = 'No Record Found';
            }

            return $output;
            
        }
    }


    public function variation_product($params)
    {
        if(!empty($params)){
            $query = DB::table('product_has_variations as pv')->select('pv.*','p.name','p.selling_price','p.qty','d.discount_amount')
            ->join('products as p', 'pv.product_id', '=', 'p.id')
            ->leftjoin('product_has_discounts as d', 'pv.product_id', '=', 'd.product_id')
            ->where(['p.company_id'=>auth()->user()->company_id,'p.branch_id'=>session()
            ->get('branch'),'p.status'=>1]);

            if($params['variation_id'] == 0){
                $this->id = Crypt::decrypt($params['id']);
                $data = $query->where(['pv.product_id'=>$this->id])->get();
            }else{
                $data = $query->where(['pv.id'=>$params['variation_id']])->get();
            }

            $output = array();
            if(!empty($data) && count($data) > 0)
            {
                foreach($data as $row)
                {
                    $temp_array             = array();
                    
                    $product_price = $row->selling_price;
                    if($row->price_prefix == '+'){
                        $product_price = $row->selling_price + $row->variation_price;
                    }elseif($row->price_prefix == '-'){
                        $product_price = $row->selling_price - $row->variation_price;
                    }

                    $temp_array['id']         = $row->id;
                    $temp_array['product_id'] = $row->product_id;
                    $temp_array['model']      = $row->variation_model;
                    $temp_array['name']       = $row->name.'('.$row->variation_name.')';
                    $temp_array['price']      = $product_price;
                    $temp_array['discount']    = $row->discount_amount != Null?$row->discount_amount:0;
                    $output[]                 = $temp_array;
                }
            } else{
                $output['value']            = '';
                $output['label']            = 'No Record Found';
            }

            return $output;
            
        }
    }
}
