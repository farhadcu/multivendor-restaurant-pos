<?php

namespace Modules\Admin\Repositories\Company;

use Modules\Admin\Contracts\Company\CompanyContract;
use App\Repositories\BaseRepository;
use Validator;
use Modules\Admin\Entities\Company;
use Modules\Admin\Entities\Subscription;
use Modules\Admin\Entities\CompanySubscription;
use Modules\Admin\Entities\CompanySubscriptionPayment;
use Illuminate\Support\Facades\Crypt;
use App\Rules\ValidPhone;

class CompanyRepository extends BaseRepository implements CompanyContract
{
    private $rules = [
        'owner_name'           => 'required|string',
        'type'                 => 'required|string',
        'total_branch_account' => 'required|numeric',
        'total_user_account'   => 'required|numeric',
        'amount'               => 'required|numeric',
        'start_date'           => 'required|date',
        'end_date'             => 'required|date|after:start_date',
        'payment_type'         => 'required|string',
        'paid_amount'          => 'required|numeric',
    ];
    private $message = [
        'amount.required'         => 'The subscription fee field is required.',
        'amount.numeric'          => 'The subscription fee field value must be numeric.',
        'duration_month.required' => 'The subscription duration field is required.',
        'duration_month.numeric'  => 'The subscription duration field value must be numeric.',
    ];
    private $id;

    public function __construct(Company $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function index()
    {
        return $this->model->all();
    }

    public function getList(array $params)
    {
        if(!empty($params['type'])){
            $this->model->setType($params['type']);
        }
        if(!empty($params['module'])){
            $this->model->setModuleID($params['module']);
        }
        
        // $this->model->setSearchValue($params['search']['regex']);
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
            if($this->helper->permission('company-edit')){
                $action .= '<li class="kt-nav__item"><a class="kt-nav__link edit_data" data-id="' .Crypt::encrypt($value->id). '" >'.EDIT_ICON.'</a></li>';
            }

            if($this->helper->permission('company-delete')){
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
            if($this->helper->permission('company-bulk-action-delete')){
            $row[]  = '<label class="kt-checkbox kt-checkbox--single kt-checkbox--all kt-checkbox--solid"><input type="checkbox" name="did[]" value="' . $value->id . '" class="select_data">&nbsp;<span></span></label> ';
            }
            $row[]  = $no;
            $row[]  = $this->image_view($value->logo);
            $row[]  = $value->company_name;
            $row[]  = $value->owner_name;
            $row[]  = $value->email;
            $row[]  = $value->mobile;
            $row[]  = $value->type;
            $row[]  = $value->start_date;
            $row[]  = $value->end_date;
            $row[]  = $this->switch_view($status);
            $row[]  = $btngroup;
            $data[] = $row;

        }
        return $this->dataTableDraw($params['draw'], $this->model->count_all(), $this->model->count_filtered(),$data);
    }
    public function createCompany(array $params)
    {
        $this->rules['company_name']    = 'required|string|unique:companies';
        $this->rules['email']           = 'required|email|unique:companies';
        $this->rules['mobile']          = ['required', new ValidPhone, 'unique:companies'];
        $this->rules['phone']           = 'string|unique:companies';
        $validator = Validator::make($params, $this->rules,$this->message);
        if ($validator->fails()) {
            $output = array(
                'errors' => $validator->errors()
            );
        } else {
            $company_collection                     = collect($params)->except(['type', 'total_branch_account','total_user_account',
            'amount','start_date', 'end_date','payment_type', 'paid_amount']);
            $history                                = [];
            $history[]  = [
                'title' => 'Company Data Created',
                'text'  => "Data created by ".auth()->guard('admin')->user()->name,
                'date'  => date('d-M-Y',strtotime(DATE)),
                'mobile'=> auth()->guard('admin')->user()->mobile
            ];
            $history                                = json_encode($history);
            $company_merge                          = $company_collection->merge(compact('history'));
            $company_id                             = $this->create($company_merge->all())->id;
            if ($company_id) {

                $company_subscription_collection    = collect($params)->except(['company_name',
                'owner_name','email','mobile','phone','address','payment_type', 'paid_amount']);
                $company_subscription_merge         = $company_subscription_collection->merge(compact('company_id'));
                $company_subscription_id            = CompanySubscription::create($company_subscription_merge ->all())->id;
                $payment_collection                 = collect($params)->only(['payment_type', 'paid_amount']);
                $payment_history                    = [];
                $payment_history[]  = [
                    'title' => 'Company Data Created',
                    'text'  => "Data created by ".auth()->guard('admin')->user()->name,
                    'date'  => date('d-M-Y',strtotime(DATE)),
                    'mobile'=> auth()->guard('admin')->user()->mobile
                ];
                $payment_history   = json_encode($payment_history);
                $payment_date      = date('Y-m-d');
                $payment_merge     = $payment_collection->merge(compact('company_id','company_subscription_id','payment_history','payment_date'));
                CompanySubscriptionPayment::create($payment_merge->all());
                $output  = ['status' => 'success','message' => 'Data has been stored successfully.'];
                
            }else{
                $output  = ['status' => 'danger','message' => 'Data can not store.'];
            }
        }
        return $output;
        
    }

    public function editCompany(array $params)
    {
        if(!empty($params['id'])){
            $this->id               = Crypt::decrypt($params['id']);
            $this->data             = $this->find((int)$this->id);
            $collection             = collect($this->data)->except(['id','created_at','updated_at']);
            $id                     = $params['id'];
            $merge                  = $collection->merge(compact('id'));
            if(!empty($merge))
            {
                $output['method']   = $merge->all();
            }else{
                $output             = ['status' => 'danger','message' => 'No data found.'];
            }
        }else {
            $output                 = ['status' => 'danger','message' => 'No data found.'];
        }
        return $output;
        
    }

    public function updateCompany(array $params)
    {
            if(!empty($params['method_id'])){
                $this->id   = Crypt::decrypt($params['method_id']);
                $method     = $this->find($this->id);
                $this->rules['method_slug']  = 'required|string|unique:methods,method_slug,'.$company->id;
                $validator  = Validator::make($params, $this->rules);
                if ($validator->fails()) {
                    $output = array(
                        'errors' => $validator->errors()
                    );
                } else {
                    $old_slug   = $company->method_slug;//store old slug data to remove from session permission array
                    $collection = collect($params)->except(['method_id']);
                    $updated_at = DATE;
                    $merge      = $collection->merge(compact('updated_at'));
                    $result     = $this->update($merge->all(),(int)$this->id);
                    if ($result) {
                        $output = ['status' => 'success','message' => 'Data has been updated successfully.'];
                        if($old_slug != $params['method_slug']){
                            $permission = session()->pull('permission', []); // Second argument is a default value
                            if(($value  = array_search($old_slug, $permission)) !== false) {
                                unset($permission[$value]);
                            }
                            session()->put('permission', $permission);
                            if(!in_array($params['method_slug'],session()->get('permission'))){
                                session()->push('permission', $params['method_slug']);
                            }
                        }
                    }else{
                        $output   = ['status' => 'danger','message' => 'Data can not update.'];
                    }
                }
            }else{
                $output           = ['status' => 'danger','message' => 'Data can not update.'];
            }
            return $output;
    }

    public function deleteCompany(array $params)
    {
        if(!empty($params['id'])){
            $this->id     = Crypt::decrypt($params['id']);
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
        $this->id = $params['id'];
        if(!empty($this->id) && count($this->id) > 0){
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

    private function image_view(string $image = null)
    {
        if(!empty($image))
        {
            $img =  "<img src='".asset(FOLDER_PATH.COMPANY_PHOTO.$image)."' style='width:80px;' />";
        }else{
            $img =  "<img src='./public/img/no-image.png' style='width:80px;' />";
        }
        return $img;
    }
}