<?php 

namespace Modules\Admin\Repositories\Company;

use Modules\Admin\Contracts\Company\BranchContract;
use App\Repositories\BaseRepository;
use Validator;
use Modules\Admin\Entities\Branch;
use Modules\Admin\Entities\User;
use Illuminate\Support\Facades\Crypt;
use App\Rules\ValidPhone;
use DB;
class BranchRepository extends BaseRepository implements BranchContract
{
    private $rules = [
        'company_id'      => 'required|numeric',
        'branch_name'     => 'required|string',
        'branch_phone'    => 'string', 
        'branch_address'  => 'string',
    ];
    private $message = [
        'company_id.required'         => 'The company field is required.',
        'company_id.numeric'          => 'The company field value must be numeric.',
    ];
    private $id;

    public function __construct(Branch $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function index(int $company_id)
    {
        return $this->model->where('company_id',$company_id)->get();
    }

    public function getList(array $params)
    {
        
        if(!empty($params['branch_name'])){
            $this->model->setBranchName($params['branch_name']);
        }
        if(!empty($params['companyID'])){
            $this->model->setCompanyID($params['companyID']);
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
            if($this->helper->permission('branch-edit')){
                $action .= '<li class="kt-nav__item"><a class="kt-nav__link edit_data" data-id="' . Crypt::encrypt($value->id) . '" >'.EDIT_ICON.'</a></li>';
            }

            if($this->helper->permission('branch-delete')){
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
            if($this->helper->permission('branch-bulk-action-delete')){
            $row[]  = '<label class="kt-checkbox kt-checkbox--single kt-checkbox--all kt-checkbox--solid"><input type="checkbox" name="did[]" value="' . $value->id . '" class="select_data">&nbsp;<span></span></label> ';
            }
            $row[]  = $no;
            $row[]  = $value->company_name;
            $row[]  = $value->branch_name;
            $row[]  = $value->branch_email;
            $row[]  = $value->branch_mobile;
            $row[]  = $value->branch_phone;
            $row[]  = $this->switch_view($status);
            $row[]  = $btngroup;
            $data[] = $row;

        }
        return $this->dataTableDraw($params['draw'], $this->model->count_all(),
        $this->model->count_filtered(),$data);
        
    }


    public function createBranch(array $params)
    {
        $this->rules['branch_email']  = 'email|unique:branches';
        $this->rules['branch_mobile'] = ['required', new ValidPhone, 'unique:branches'];

        $validator = Validator::make($params, $this->rules,$this->message);
        if ($validator->fails()) {
            $output = array(
                'errors' => $validator->errors()
            );
        } else {
            $company_subscription = DB::table('company_subscriptions')->where('company_id',$params['company_id'])->first();
           
            $total_branch = $this->model->where('company_id',$params['company_id'])->count();

            if($total_branch >= $company_subscription->total_branch_account){
                $output   = ['status' => 'danger', 'message' => 'This company already filled it\'s branch account plan. Upgrade plan to add more branch'];
            }else{
                $collection   = collect($params);
                $result       = $this->create($collection->all());
                if ($result) {
                    $output   = ['status' => 'success', 'message' => 'Data has been saved successfully.'];
                }else{
                    $output   = ['status' => 'danger', 'message' => 'Data can not save.'];
                }
    
            }
            
        }
        return $output;
    }

    public function editBranch(array $params)
    {
        if(!empty($params['id'])){
            $this->id               = Crypt::decrypt($params['id']);
            $this->data             = $this->find((int) $this->id);
            $collection             = collect($this->data)->except(['id','created_at','updated_at']);
            $id                     = $params['id'];
            $merge                  = $collection->merge(compact('id'));
            if(!empty($merge))
            {
                $output['branch']   = $merge->all();
            }else{
                $output             = ['status' => 'danger','message' => 'No Data Found'];
            }
        }else{
            $output                 = ['status' => 'danger','message' => 'No Data Found'];
        }
        return $output;
    }

    public function updateBranch(array $params)
    {
        if(!empty($params['branch_id'])){
            $this->id                     = Crypt::decrypt($params['branch_id']);
            $branch                       = $this->find($this->id);
            $this->rules['branch_email']  = 'email|unique:branches,branch_email,'.$branch->id;
            $this->rules['branch_mobile'] = ['required', new ValidPhone, 'unique:branches,branch_mobile,'.$branch->id];
            $validator                    = Validator::make($params, $this->rules);
            if ($validator->fails()) {
                $output                   = array( 'errors' => $validator->errors());
            } else {
                $collection               = collect($params)->except('branch_id');
                $updated_at               = DATE;
                $merge                    = $collection->merge(compact('updated_at'));
                $result                   = $this->update($merge->all(),(int)$this->id);
                if ($result) {
                    $output               = ['status'  => 'success','message' => 'Data has been updated successfully'];
                }else{
                    $output               = ['status' => 'danger','message' => 'Data can not update'];
                }
            }
        }else{
            $output                       = ['status' => 'danger','message' => 'Data can not update'];
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

    public function deleteBranch(array $params)
    {
        if(!empty($params['id'])){
            $this->id = Crypt::decrypt($params['id']);

            $total_user = User::where('branch_id',$this->id)->count();
            if($total_user > 0){
                $output   = ['status'  => 'danger','message' => 'This data is related with others. At first delete those data.'];
            }else{
                $result       = $this->delete((int)$this->id);
                if ($result) {
                    $output   = ['status'  => 'success','message' => 'Data has been deleted successfully.'];
                } else {
                    $output   = ['status'  => 'danger','message' => 'Unable to delete data.'];
                }   
            }
             
        } else {
            $output       = ['status'  => 'danger','message' => 'Unable to delete data.'];
        }
        return $output;
    }

    public function bulk_action_delete(array $params)
    {
        $this->id = $params['id'];
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

    
    public function parent_module_list(){
        $modules =  $this->model->orderByRaw('-module_sequence DESC') //sequence according to module sequence number in desc order
            ->get()
            ->nest()
            ->setIndent('–– ') //append before child module
            ->listsFlattened('module_name'); //name that will show in frontend

        $output = '<option value="">Select Please</option>';
        foreach ($modules as $key => $value) {
            $output .= "<option value='$key'>$value</option>";
        }
        return $output;
    }

}
