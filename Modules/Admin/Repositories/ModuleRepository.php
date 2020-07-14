<?php 

namespace Modules\Admin\Repositories;

use Modules\Admin\Contracts\ModuleContract;
use App\Repositories\BaseRepository;
use Validator;
use Modules\Admin\Entities\Module;
use Modules\Admin\Entities\Method;
use Modules\Admin\Entities\AdminRoleModulePermission;
use Modules\Admin\Entities\AdminModulePermission;
use Illuminate\Support\Facades\Crypt;

class ModuleRepository extends BaseRepository implements ModuleContract
{
    private $rules = [
        'module_name'     => 'required|string',
        'module_icon'     => 'required|string', 
        'module_sequence' => 'required|numeric',
        'parent_id'       => 'numeric|nullable',
    ];
    private $id;

    public function __construct(Module $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function getList(array $params)
    {
        
        if(!empty($params['module'])){
            $this->model->setModuleName($params['module']);
        }
        
        $this->model->setSearchValue($params['search']['regex']);
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
            if($this->helper->permission('module-edit')){
                $action .= '<li class="kt-nav__item"><a class="kt-nav__link edit_data" data-id="' . Crypt::encrypt($value->id) . '" >'.EDIT_ICON.'</a></li>';
            }

            if($this->helper->permission('module-delete')){
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
            if($this->helper->permission('module-bulk-action-delete')){
            $row[]  = '<label class="kt-checkbox kt-checkbox--single kt-checkbox--all kt-checkbox--solid"><input type="checkbox" name="did[]" value="' . $value->id . '" class="select_data">&nbsp;<span></span></label> ';
            }
            $row[]  = $no;
            $row[]  = '<i class="'.$value->module_icon.'"></i> '.$value->module_name;
            $row[]  = $value->module_link;
            $row[]  = $value->module_icon;
            $row[]  = $this->model->parent_name($value->parent_id);
            $row[]  = $value->module_sequence;
            $row[]  = $btngroup;
            $data[] = $row;

        }
        return $this->dataTableDraw($params['draw'], $this->model->count_all(),
        $this->model->count_filtered(),$data);
        
    }


    public function createModule(array $params)
    {

        if($params['module_link'] == 'javascript:void(0);'){
            $this->rules['module_link']  = 'required|string';
        }else{
            $this->rules['module_link']  = 'required|string|unique:modules';
        }
        $validator = Validator::make($params, $this->rules);
        if ($validator->fails()) {
            $output = array(
                'errors' => $validator->errors()
            );
        } else {
            $collection   = collect($params)->except(['parent_id']);
            $parent_id    = !empty($params['parent_id']) ? $params['parent_id'] : 0;
            $merge        = $collection->merge(compact('parent_id'));
            $result       = $this->create($merge->all());
            if ($result) {
                $output   = ['status' => 'success', 'message' => 'Data has been saved successfully.'];
            }else{
                $output   = ['status' => 'danger', 'message' => 'Data can not save.'];
            }
        }
        return $output;
    }

    public function editModule(array $params)
    {
        if(!empty($params['id'])){
            $this->id               = Crypt::decrypt($params['id']);
            $this->data             = $this->find((int) $this->id);
            $collection             = collect($this->data)->except(['id','created_at','updated_at']);
            $id                     = $params['id'];
            $merge                  = $collection->merge(compact('id'));
            if(!empty($merge))
            {
                $output['module']   = $merge->all();
            }else{
                $output             = ['status' => 'danger','message' => 'No Data Found'];
            }
        }else{
            $output                 = ['status' => 'danger','message' => 'No Data Found'];
        }
        return $output;
    }

    public function updateModule(array $params)
    {
        if(!empty($params['module_id'])){
            $this->id                     = Crypt::decrypt($params['module_id']);
            $module                       = $this->find($this->id);
          
            $this->rules['module_link']   = 'required|string|unique:modules,module_link,'.$module->id;
            $validator                    = Validator::make($params, $this->rules);
            if ($validator->fails()) {
                $output                   = array( 'errors' => $validator->errors());
            } else {
                $collection               = collect($params)->except(['module_id','parent_id']);
                $parent_id                = !empty($params['parent_id']) ? $params['parent_id'] : 0;
                $updated_at               = DATE;
                $merge                    = $collection->merge(compact('parent_id','updated_at'));
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

    public function deleteModule(array $params)
    {
        if(!empty($params['id'])){
            $this->id = Crypt::decrypt($params['id']);
            Method::where('module_id',$this->id)->delete();
            AdminRoleModulePermission::where('module_id',$this->id)->delete();
            AdminModulePermission::where('module_id',$this->id)->delete();
            $result       = $this->delete((int)$this->id);
            if ($result) {
                $output   = ['status'  => 'success','message' => 'Data has been deleted successfully.'];
            } else {
                $output   = ['status'  => 'error','message' => 'Unable to delete data.'];
            }    
        } else {
            $output       = ['status'  => 'error','message' => 'Unable to delete data.'];
        }
        return $output;
    }

    public function bulk_action_delete(array $params)
    {
        $this->id = $params['id'];
        if(!empty($this->id) && count($this->id) > 0){
            Method::whereIn('module_id',$this->id)->delete();
            AdminRoleModulePermission::whereIn('module_id',$this->id)->delete();
            AdminModulePermission::whereIn('module_id',$this->id)->delete();
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
        $modules =  Module::orderByRaw('-module_sequence DESC') //sequence according to module sequence number in desc order
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
