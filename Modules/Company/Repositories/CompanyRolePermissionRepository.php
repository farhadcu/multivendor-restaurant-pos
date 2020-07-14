<?php
namespace Modules\Company\Repositories;

use Modules\Company\Contracts\CompanyRolePermissionContract;
use App\Repositories\BaseRepository;
use Validator;
use Modules\Admin\Entities\CompanyRole AS Role;
use Modules\Admin\Entities\CompanyModule AS Module;
use Modules\Admin\Entities\CompanyMethod AS Method;
use Modules\Admin\Entities\CompanyRoleMethodPermission;
use Modules\Admin\Entities\CompanyRoleModulePermission;
use Illuminate\Support\Facades\Crypt;

class CompanyRolePermissionRepository extends BaseRepository implements CompanyRolePermissionContract
{

    public function __construct(Role $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function index()
    {
        return $this->model->where(['company_id'=>auth()->user()->company_id])->orderBy('id','asc')->get();
    }
    public function store(array $params)
    {
        $params = collect($params);
        if($params->has('module') && $params->has('method')){
            if(is_array($params['module']) && is_array($params['method'])){

                CompanyRoleModulePermission::where(['company_id'=>auth()->user()->company_id,'role_id'=>$params['role_id']])->delete(); //delete previous selected role module permission
                CompanyRoleMethodPermission::where(['company_id'=>auth()->user()->company_id,'role_id'=>$params['role_id']])->delete(); //delete previous selected role method permission
                
                $module_permnission   = array();
                $method_permnission   = array();

                foreach ($params['module'] as $module) {
                    $module_permnission[] = [
                        'company_id' => auth()->user()->company_id,
                        'role_id'    => $params['role_id'],
                        'module_id'  => $module,
                        'created_at' => DATE,
                        'updated_at' => DATE
                    ];
                }
                
                foreach ($params['method'] as $method) {
                    $method_permnission[] = [
                        'company_id' => auth()->user()->company_id,
                        'role_id'    => $params['role_id'],
                        'method_id'  => $method,
                        'created_at' => DATE,
                        'updated_at' => DATE
                    ];
                }
                $module_save    = CompanyRoleModulePermission::insert($module_permnission); //insert module arrray data in database
                $method_save    = CompanyRoleMethodPermission::insert($method_permnission); //insert method arrray data in database
                if($module_save && $method_save){
                    $output     = ['status' => 'success','message' => 'Data has been saved successfully.'];
                }else{
                    $output     = ['status' => 'danger','message' => 'Data can not save.'];
                } 
            }else{
                $output         = ['status' => 'danger','message' => 'Please checked at least one module and method checkbox.'];
            }
        }else{
            $output         = ['status' => 'danger','message' => 'Please checked at least one module and method checkbox.'];
        }
        return $output;
    }

    public function get_role_permission(int $role_id)
    {
        $module  = '';
        $module .= $this->multilevel_permission($parent_id = '',$role_id);
        return $module;
    }

    private function multilevel_permission($parent_id = NULL,$role_id)
    {

        $module                 = '';
        if($parent_id == 0){
            $modules            = Module::where(['parent_id' => 0])->orderBy('module_sequence','asc')->get(); //get module list whose parent id is 0
        }else{
            $modules            = Module::where(['parent_id' => $parent_id])->orderBy('module_sequence','asc')->get(); //get module list whose parent id is the given id
        }

        if(!empty($modules)){
            foreach ($modules as $value) {
                $rmp            = CompanyRoleModulePermission::where(['company_id' => auth()->user()->company_id,'module_id'=>$value->id,'role_id'=>$role_id])->first(); //check module existance in role_module_permissins table
                if($rmp){
                    $checked    = 'checked';
                }else{
                    $checked    = '';
                }
                $module .= '<li><input type="checkbox" name="module[]" '.$checked.' value="'.$value->id.'"> '.$value->module_name;
                // <i class="'.$value->module_icon.'" style="margin-right:5px;margin-left:-5px;"></i>
                $module .= '<ul>'.$this->multilevel_permission($value->id,$role_id);
                $methodlist                       = Method::where('module_id',$value->id)->orderBy('id','asc')->get(); //get method list
                foreach ($methodlist as $method) {
                    $rmetp                        = CompanyRoleMethodPermission::where(['company_id' => auth()->user()->company_id,'method_id'=>$method->id,'role_id'=>$role_id])->first(); //check method existance in role_method_permissions table
                    if($rmetp){
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
                    $module .= '<li><input type="checkbox" name="method[]" '.$methodChecked.' value="'.$method->id.'">'.$method_name.'</li>';
                }
                $module .= '</ul>';
                $module .= '</li>'; 
            }
        }
        return $module;
    }
    
}