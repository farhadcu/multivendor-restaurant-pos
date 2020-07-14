<?php 

namespace Modules\Company\Repositories\Accounts;


use App\Repositories\BaseRepository;
use Validator;
use Modules\Company\Entities\Accounts\AccountHead;
use Illuminate\Support\Facades\Crypt;
use Modules\Company\Contracts\Accounts\ChartOfAccountContract;

class ChartOfAccountRepository extends BaseRepository implements ChartOfAccountContract
{
    private $rules = [
        'account_title'   => 'required|string',
        'account_type_id' => 'required|numeric',
        'account_number'  => 'numeric',
        'balance'         => 'numeric',
        'description'     => 'string',
        'status'          => 'required|numeric',
    ];
    private $message = [
        'account_type_id.required' => 'This account type field is required',
        'account_type_id.string'   => 'This account type field value must be string',
    ];
    private $id;

    public function __construct(AccountHead $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function index()
    {
        return $this->model->where(['company_id'=>auth()->user()->company_id,'branch_id'=>session()->get('branch')])
                            ->orderBy('id','asc')->get();
    }

    public function getList(array $params)
    {
        
        if(!empty($params['account_title'])){
            $this->model->setAccountTitle($params['account_title']);
        }
        if(!empty($params['account_type_id'])){
            $this->model->setAccountTypeID($params['account_type_id']);
        }
        if(!empty($params['account_number'])){
            $this->model->setAccountNumber($params['account_number']);
        }
        if(!empty($params['balance_from'])){
            $this->model->setBalanceFrom($params['balance_from']);
        }
        if(!empty($params['balance_to'])){
            $this->model->setBalanceTo($params['balance_to']);
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
            if($this->helper->permission('chart-of-account-edit')){
                $action .= '<li class="kt-nav__item"><a class="kt-nav__link edit_data" data-id="' . Crypt::encrypt($value->id) . '" >'.EDIT_ICON.'</a></li>';
            }
            if($this->helper->permission('chart-of-account-view')){
                $action .= '<li class="kt-nav__item"><a class="kt-nav__link view_data" data-id="' . Crypt::encrypt($value->id) . '" >'.VIEW_ICON.'</a></li>';
            }
            if($this->helper->permission('chart-of-account-delete')){
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

            $row    = array();
            if($this->helper->permission('chart-of-account-bulk-action-delete')){
            $row[]  = '<label class="kt-checkbox kt-checkbox--single kt-checkbox--all kt-checkbox--solid"><input type="checkbox" name="did[]" value="' . $value->id . '" class="select_data">&nbsp;<span></span></label> ';
            }
            $row[]  = $no;
            $row[]  = $value->account_type->account_type;
            $row[]  = $value->account_title;
            $row[]  = $value->account_number;
            $row[]  = $value->balance;
            $row[]  = STATUS[$value->status];
            $row[]  = $btngroup;
            $data[] = $row;

        }
        return $this->dataTableDraw($params['draw'], $this->model->count_all(),
        $this->model->count_filtered(),$data);
        
    }


    public function createAccount(array $params)
    {      
        $validator        = Validator::make($params, $this->rules,$this->message);
        if ($validator->fails()) {
            $output       = array(
                'errors' => $validator->errors()
            );
        } else {
            $collection   = collect($params);
            $company_id   = auth()->user()->company_id;
            $branch_id    = session()->get('branch');
            $history      = [];
            $history[] = [
                'title'  => 'Account created',
                'text'   => 'Account created by '.auth()->user()->name,
                'date'   => DATE_FORMAT,
                'mobile' => auth()->user()->mobile
            ];
            $history      = json_encode($history);
            $merge        = $collection->merge(compact('company_id','branch_id','history'));
            $result       = $this->create($merge->all());
            if ($result) {
                $output   = ['status' => 'success', 'message' => 'Data has been saved successfully.'];
            }else{
                $output   = ['status' => 'danger', 'message' => 'Data can not save.'];
            }
        }
        return $output;
    }

    public function editAccount(array $params)
    {
        if(!empty($params['id'])){
            $this->id                     = Crypt::decrypt($params['id']);
            $this->data                   = $this->find((int) $this->id);
            $collection                   = collect($this->data)->except(['id','company_id','branch_id','history'.'created_at','updated_at']);
            $id                           = $params['id'];
            $merge                        = $collection->merge(compact('id'));
            if(!empty($merge))
            {
                $output['account']   = $merge->all();
            }else{
                $output                   = ['status' => 'danger','message' => 'No Data Found'];
            }
        }else{
            $output                       = ['status' => 'danger','message' => 'No Data Found'];
        }
        return $output;
    }
    public function showAccount(array $params)
    {
        if(!empty($params['id'])){
            $this->id                     = Crypt::decrypt($params['id']);
            $this->data                   = $this->find((int) $this->id);
            $collection                   = collect($this->data)->except(['id','company_id','branch_id','history'.'created_at','updated_at']);
            $id                           = $params['id'];
            $merge                        = $collection->merge(compact('id'));
            if(!empty($merge))
            {
                $output['account_type']   = $merge->all();
            }else{
                $output                   = ['status' => 'danger','message' => 'No Data Found'];
            }
        }else{
            $output                       = ['status' => 'danger','message' => 'No Data Found'];
        }
        return $output;
    }

    public function updateAccount(array $params)
    {
        if(!empty($params['update_id'])){
            $this->id         = Crypt::decrypt($params['update_id']);
            $validator        = Validator::make($params, $this->rules, $this->message);
            if ($validator->fails()) {
                $output       = array( 'errors' => $validator->errors());
            } else {
                $collection   = collect($params)->except(['update_id']);
                $this->data   = $this->find($this->id);
                $history      = [];
                $history[]    = json_decode($this->data->history);
                $history[] = [
                    'title'  => 'Account updated',
                    'text'   => 'Account updated by '.auth()->user()->name,
                    'date'   => DATE_FORMAT,
                    'mobile' => auth()->user()->mobile
                ];
                $history      = json_encode($history);
                $updated_at   = DATE;
                $company_id   = auth()->user()->company_id;
                $branch_id    = session()->get('branch');
                $merge        = $collection->merge(compact('company_id','branch_id','history','updated_at'));
                $result       = $this->update($merge->all(),(int)$this->id);
                if ($result) {
                    $output   = ['status'  => 'success','message' => 'Data has been updated successfully'];
                }else{
                    $output   = ['status' => 'danger','message' => 'Data can not update'];
                }
            }
        }else{
            $output           = ['status' => 'danger','message' => 'Data can not update'];
        }
        return $output;
    }

    public function deleteAccount(array $params)
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


}
