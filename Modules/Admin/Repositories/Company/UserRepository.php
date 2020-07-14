<?php

namespace Modules\Admin\Repositories\Company;

use Modules\Admin\Contracts\Company\UserContract;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Hash;
use DB;
use Validator;
use Modules\Admin\Entities\User;
use Modules\Admin\Entities\CompanyModule;
use Modules\Admin\Entities\CompanyMethod;
use Modules\Admin\Entities\UserModulePermission;
use Modules\Admin\Entities\UserMethodPermission;
use App\Rules\ValidPhone;
use App\Rules\StrongPassword;
use Illuminate\Support\Facades\Crypt;

class UserRepository extends BaseRepository implements UserContract
{
    private $rules = [
        'name'         => 'required|string',
        'gender'       => 'required|numeric',
        'company_id'   => 'required|numeric',
        'branch_id'    => 'numeric',
        'role_id'      => 'required|numeric',
    ];

    private $id;
    private $type = 1; //type = 1 is for admin


    public function __construct(User $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function index(int $company_id)
    {
        return User::where('company_id',$company_id)->get();
    }

    public function getList(array $params)
    {
        

        if(!empty($params['uname'])){
            $this->model->setName($params['uname']);
        }
        if(!empty($params['uemail'])){
            $this->model->setEmail($params['uemail']);
        }
        if(!empty($params['umobile'])){
            $this->model->setMobile($params['umobile']);
        }
        if(!empty($params['urole'])){
            $this->model->setRole($params['urole']);
        }
        if(!empty($params['ustatus'])){
            $this->model->setStatus($params['ustatus']);
        }

        $this->model->setType($this->type);

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
            if($this->helper->permission('company-user-edit')){
                $action .= '<li class="kt-nav__item"><a class="kt-nav__link" href="' . url('admin/company/user/edit',Crypt::encrypt($value->id)) . '" >'.EDIT_ICON.'</a></li>';
            }
            if($this->helper->permission('company-user-view')){
                $action .= '<li class="kt-nav__item"><a class="kt-nav__link view_data" data-id="' . Crypt::encrypt($value->id) . '" >'.VIEW_ICON.'</a></li>';
            }
            if($this->helper->permission('company-user-delete')){
                $action .= '<li class="kt-nav__item"><a class="kt-nav__link delete_data" data-id="'.Crypt::encrypt($value->id).'" >'.DELETE_ICON.'</a></li>';
            }

            if($this->helper->permission('company-user-password-change')){
                $action .= '<li class="kt-nav__item"><a class="kt-nav__link change_password" data-id="'.Crypt::encrypt($value->id).'" ><i class="kt-nav__link-icon fas fa-key text-brand"></i> <span class="kt-nav__link-text">Change Password</span></a></li>';
            }
            $btngroup = '<span style="overflow: visible; position: relative; width: 20px;">
                            <div class="dropdown"> 
                                <a data-toggle="dropdown" class="btn btn-sm btn-clean btn-icon btn-icon-md"  aria-expanded="false"> <i class="flaticon-more-1"></i> </a>
                                <div class="dropdown-menu dropdown-menu-right"  x-placement="bottom-end" style="min-width:205px;">
                                    <ul class="kt-nav">
                                    '.$action.'
                                    </ul>
                                </div>
                            </div>
                        </span>';

            
            
            $user = [
                'photo'  => $value->photo,
                'gender' => $value->gender,
            ];
            $status = [
                'id'      => Crypt::encrypt($value->id),
                'checked' => SWITCH_STATUS[$value->status]
            ];

            $row    = array();
            if($this->helper->permission('company-user-bulk-action-delete')){
            $row[]  = '<label class="kt-checkbox kt-checkbox--single kt-checkbox--all kt-checkbox--solid"><input type="checkbox" name="did[]" value="' . $value->id . '" class="select_data">&nbsp;<span></span></label> ';
            }
            $row[]  = $no;
            $row[]  = $value->photo;
            $row[]  = $value->name;
            $row[]  = $value->email;
            $row[]  = $value->mobile;
            $row[]  = $value->company_name;
            $row[]  = $value->branch_name;
            $row[]  = $value->role;
            $row[]  = !empty($value->last_login_at) ? date('d-M-Y h:i:sA',strtotime($value->last_login_at)) : '';
            $row[]  = $value->last_login_ip ?? '';
            $row[]  = $this->switch_view($status);
            $row[]  = $btngroup;
            $data[] = $row;

        }
        return $this->dataTableDraw($params['draw'], $this->model->count_all(),$this->model->count_filtered(),$data);

    }


    public function createUser(array $params)
    {
        if($this->helper->permission('company-user-add')){
            $this->rules['email']                 = 'required|string|email|max:100|unique:users';
            $this->rules['mobile']                = ['required', new ValidPhone, 'unique:users'];
            $this->rules['password']              = ['required','confirmed', new StrongPassword];
            $this->rules['password_confirmation'] = 'required'; 
            $validator = Validator::make($params, $this->rules);
            if ($validator->fails()) {
                $output = array(
                    'errors' => $validator->errors()
                );
            } else {
                $company_subscription = DB::table('company_subscriptions')->where('company_id',$params['company_id'])->first();
                $total_user = $this->model->where('company_id',$params['company_id'])->count();

                if($total_user >= $company_subscription->total_user_account){
                    $output   = ['status' => 'danger', 'message' => 'This company already filled it\'s user account plan. Upgrade plan to add more branch'];
                }else{
                    $params         = collect($params);
                    if($params->has('module') && $params->has('method')){
                        $history    = [];
                        $collection = collect($params)->except(['branch_id','password','password_confirmation','module','method']);
                        $password   = Hash::make($params['password']);
                        $branch_id = !empty($params['branch_id']) ? $params['branch_id'] : null;
                        $history[]  = [
                            'title' => 'User Data Created',
                            'text'  => "Data created by ".auth()->guard('admin')->user()->name,
                            'date'  => date('d-M-Y',strtotime(DATE)),
                            'mobile'=> auth()->guard('admin')->user()->mobile
                        ];
                        $history    = json_encode($history);
                        $merge      = $collection->merge(compact('branch_id','password','history'));
                        $this->id   = $this->create($merge->all())->id;
                        
                        if (!empty($this->id)) {

                            $user_module_permission  = [];
                            $user_method_permission  = [];
                            foreach ($params['module'] as $module) {
                                $user_module_permission[] = [
                                    'company_id' => $params['company_id'],
                                    'user_id'    => $this->id,
                                    'module_id'  => $module,
                                    'created_at' => DATE,
                                    'updated_at' => DATE
                                ];
                            }
                            
                            foreach ($params['method'] as $method) {
                                $user_method_permission[] = [
                                    'company_id' => $params['company_id'],
                                    'user_id'    => $this->id,
                                    'method_id'  => $method,
                                    'created_at' => DATE,
                                    'updated_at' => DATE
                                ];
                            }
                            if(!empty($user_module_permission) && count($user_module_permission) > 0){
                                UserModulePermission::insert($user_module_permission); //insert user module arrray data in database
                            }
                            if(!empty($user_method_permission) && count($user_method_permission) > 0){
                                UserMethodPermission::insert($user_method_permission); //insert userb method arrray data in database
                            }

                            $output   = ['status'  => 'success','message' => 'Data has been saved successfully.'];
                        }else{
                            $output   = ['status' => 'danger','message' => 'Data Can not save'];
                        }
                    }else{
                        $output       = ['status' => 'danger','message' => 'Please checked at least one module and method checkbox.'];
                    }
                }
            }
            return $output;
        }
    }


    public function showUser($id)
    {
        if(!empty($id)){
            $this->id   = Crypt::decrypt($id);
            $this->data = $this->find((int)$this->id);
            $collection = collect($this->data)->except(['id','history','created_at','updated_at','status','last_login_at','last_login_ip','photo']);
            $merge      = $collection->merge(compact('id'));
            
            if(!empty($merge))
            {
                $output = $merge->all();
            }else{
                $output = '';
            }
        }else {
            $output     = '';
        }
        return $output;
    }


    public function updateUser(array $params)
    {
        if(!empty($params['user_id'])){
            $this->id                             = Crypt::decrypt($params['user_id']);
            $this->data                           = $this->find((int)$this->id);
            $this->rules['email']                 = 'required|string|email|max:100|unique:users,email,'.$this->data->id;
            $this->rules['mobile']                = ['required', new ValidPhone, 'unique:users,mobile,'.$this->data->id];
            $validator                            = Validator::make($params, $this->rules);
            if ($validator->fails()) {
                $output                           = array(
                    'errors' => $validator->errors()
                );
            } else {
                $params                           = collect($params);
                if($params->has('module') && $params->has('method')){
                    $collection                   = collect($params)->except(['branch_id','user_id','module','method']);
                    $branch_id = !empty($params['branch_id']) ? $params['branch_id'] : null;
                    $history                      = [];
                    $history                      = json_decode($this->data->history);
                    $history[]   = [
                        'title' => 'User Data Updated',
                        'text'  => "Data updated by ".auth()->guard('admin')->user()->name,
                        'date'  => date('d-M-Y',strtotime(DATE)),
                        'mobile'=> auth()->guard('admin')->user()->mobile
                    ];
                    $history                      = json_encode($history);
                    $updated_at                   = DATE;
                    $merge                        = $collection->merge(compact('branch_id','history','updated_at'));
                    $result                       = $this->update($merge->all(),(int)$this->id);
                    if ($result) {

                        UserModulePermission::where(['company_id'=>$this->data->company_id,'user_id'=>$this->id])->delete();
                        UserMethodPermission::where(['company_id'=>$this->data->company_id,'user_id'=>$this->id])->delete();
                        $user_module_permission   = [];
                        $user_method_permission   = [];
                        foreach ($params['module'] as $module) {
                            $user_module_permission[] = [
                                'company_id' => $params['company_id'],
                                'user_id'    => $this->id,
                                'module_id'  => $module,
                                'created_at' => DATE,
                                'updated_at' => DATE
                            ];
                        }
                        
                        foreach ($params['method'] as $method) {
                            $user_method_permission[] = [
                                'company_id' => $params['company_id'],
                                'user_id'    => $this->id,
                                'method_id'  => $method,
                                'created_at' => DATE,
                                'updated_at' => DATE
                            ];
                        }
                        if(!empty($user_module_permission) && count($user_module_permission) > 0){
                            UserModulePermission::insert($user_module_permission); //insert user module arrray data in database
                        }
                        if(!empty($user_method_permission) && count($user_method_permission) > 0){
                            UserMethodPermission::insert($user_method_permission); //insert userb method arrray data in database
                        }

                        $output['status']         = 'success';
                        $output['message']        = 'Data has been updated successfully';
                    }else{
                        $output['status']         = 'danger';
                        $output['message']        = 'Data can not update';
                    }
                }else{
                    $output                       = ['status' => 'danger','message' => 'Please checked at least one module and method checkbox.'];
                }
                
            }
            
        }else{
            $output['status']                     = 'danger';
            $output['message']                    = 'Data can not update';
        }
        return $output;
    }

    public function change_status(array $params)
    {
        if(!empty($params['id'])){
            $this->id = Crypt::decrypt($params['id']);
            if((int)$params['status']){

                $user                   = $this->model->find($this->id);
                $history                = [];
                $history                = json_decode($user->history);
                $history[]    = [
                    'title' => 'User Status Make '.STATUS[$params['status']],
                    'text'  => "Status changed by ".auth()->guard('admin')->user()->name,
                    'date'  => date('d-M-Y',strtotime(DATE)),
                    'mobile'=> auth()->guard('admin')->user()->mobile
                ];
                $user->status           = $params['status'];
                $user->history          = json_encode($history);
                $user->updated_at       = DATE;

                if($user->update()){
                    $output['status']   = 'success';
                    $output['message']  = 'Status changed successfully';
                }else{
                    $output['status']   = 'danger';
                    $output['message']  = 'Status can not change';
                }
            }else{
                $output['status']       = 'danger';
                $output['message']      = 'Status can not change';
            }
        }else{
            $output['status']           = 'danger';
            $output['message']          = 'Status can not change';
        }
        return $output;
        
    }


    public function deleteUser(array $params)
    { 
        //
    }

    public function bulk_action_delete(array $params)
    {
        //
    }
    public function change_password(array $params)
    {
        if(!empty($params['user_id'])){
            $this->id                       = Crypt::decrypt($params['user_id']);
            $rules['password']              = ['required','confirmed', new StrongPassword];
            $rules['password_confirmation'] = 'required'; 
            $validator = Validator::make($params, $rules);
            if ($validator->fails()) {
                $output = array(
                    'errors' => $validator->errors()
                );
            } else {
                $collection  = collect($params)->except(['user_id','password','password_confirmation']);
                $this->data  = $this->find((int)$this->id);
                $password    = Hash::make($params['password']);
                $history     = [];
                $history     = json_decode($this->data->history);
                $history[]   = [
                    'title' => 'User Password Updated',
                    'text'  => "Password updated by ".auth()->user()->name,
                    'date'  => date('d-M-Y',strtotime(DATE)),
                    'emp_id'=> auth()->user()->employee_id
                ];
                $history = json_encode($history);
                $updated_at  = DATE;
                $merge       = $collection->merge(compact('password','history','updated_at'));
                // dd($merge);
                $result      = $this->update($merge->all(),(int)$this->id);
                if ($result) {
                    $output['status']  = 'success';
                    $output['message'] = 'Password has been updated successfully';
                }else{
                    $output['status']   = 'danger';
                    $output['message']  = 'Password can not update';
                }
                
            }
            
        }else{
            $output['status']   = 'danger';
            $output['message']  = 'Data can not update';
        }
        return $output;
    }

    public function get_permission($user_id = null)
    {

        $user_id = !empty($user_id) ? Crypt::decrypt($user_id) : NULL;
        $module  = '';
        $module .= $this->multilevel_permission($parent_id = '',$user_id);
        return $module;
    }

    private function multilevel_permission($parent_id = NULL,$user_id=NULL)
    {

        $module                 = '';
        if($parent_id == 0){
            $modules            = CompanyModule::where(['parent_id' => 0])->orderBy('module_sequence','asc')->get(); //get module list whose parent id is 0
        }else{
            $modules            = CompanyModule::where(['parent_id' => $parent_id])->orderBy('module_sequence','asc')->get(); //get module list whose parent id is the given id
        }

        if(!empty($modules)){
            foreach ($modules as $value) {
                $user_permitted_module = UserModulePermission::where(['module_id'=>$value->id,'user_id'=>$user_id])->first(); //check module existance in role_module_permissins table
                if($user_permitted_module){
                    $checked    = 'checked';
                }else{
                    $checked    = '';
                }
                $module .= '<li><input type="checkbox" name="module[]" '.$checked.'  value="'.$value->id.'"> '.$value->module_name;
                // <i class="'.$value->module_icon.'" style="margin-right:5px;margin-left:-5px;"></i>
                $module .= '<ul>'.$this->multilevel_permission($value->id,$user_id);
                $methodlist  = CompanyMethod::where('module_id',$value->id)->orderBy('id','asc')->get(); //get method list
                foreach ($methodlist as $method) {
                    $user_permitted_method  = UserMethodPermission::where(['method_id'=>$method->id,'user_id'=>$user_id])->first(); //check method existance in role_method_permissions table
                    if($user_permitted_method){
                        $methodChecked            = 'checked';
                    }else{
                        $methodChecked            = '';
                    }
                    $remove_module_name           = '';
                    foreach ($this->permission_reserved_keywords as $keyword) {
                        if (strpos(strtolower($method->method_name), strtolower($keyword)) !== false) {
                            $remove_module_name   = $keyword;
                        }
                    }
                    $method_name                  = $remove_module_name;
                    $module .= '<li><input type="checkbox" name="method[]" '.$methodChecked.'  value="'.$method->id.'">'.$method_name.'</li>';
                }
                $module .= '</ul>';
                $module .= '</li>'; 
            }
        }
        return $module;
    }

}