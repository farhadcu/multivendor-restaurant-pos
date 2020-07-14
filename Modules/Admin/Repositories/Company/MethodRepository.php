<?php

namespace Modules\Admin\Repositories\Company;

use Modules\Admin\Contracts\Company\MethodContract;
use App\Repositories\BaseRepository;
use Validator;
use Modules\Admin\Entities\CompanyMethod;
use Modules\Admin\Entities\CompanyRoleMethodPermission;
use Modules\Admin\Entities\UserMethodPermission;
use Illuminate\Support\Facades\Crypt;

class MethodRepository extends BaseRepository implements MethodContract
{
    private $rules = [
        'method_name'  => 'required|string',
        'module_id'    => 'required|numeric',
    ];
    private $id;

    public function __construct(CompanyMethod $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function getList(array $params)
    {
        if(!empty($params['method'])){
            $this->model->setMethodName($params['method']);
        }
        if(!empty($params['module'])){
            $this->model->setModuleID($params['module']);
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
            if($this->helper->permission('company-method-edit')){
                $action .= '<li class="kt-nav__item"><a class="kt-nav__link edit_data" data-id="' .Crypt::encrypt($value->id). '" >'.EDIT_ICON.'</a></li>';
            }

            if($this->helper->permission('company-method-delete')){
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

            // dd($this->software($value->software));
            $row    = array();
            if($this->helper->permission('company-method-bulk-action-delete')){
            $row[]  = '<label class="kt-checkbox kt-checkbox--single kt-checkbox--all kt-checkbox--solid"><input type="checkbox" name="did[]" value="' . $value->id . '" class="select_data">&nbsp;<span></span></label> ';
            }
            $row[]  = $no;
            $row[]  = $value->method_name;
            $row[]  = $value->method_slug;
            $row[]  = '<i class="'.$value->module_icon.'"></i> '.$value->module_name;
            $row[]  = $btngroup;
            $data[] = $row;

        }
        return $this->dataTableDraw($params['draw'], $this->model->count_all(), $this->model->count_filtered(),$data);
    }
    public function createMethod(array $params)
    {
        $this->rules['method_slug']  = 'required|string|unique:company_methods';
        $validator = Validator::make($params, $this->rules);
        if ($validator->fails()) {
            $output = array(
                'errors' => $validator->errors()
            );
        } else {
            $collection = collect($params);
            $result     = $this->create($collection->all());
            if ($result) {
                $output = ['status' => 'success','message' => 'Data has been stored successfully.'];
                if(!in_array($params['method_slug'],session()->get('permission'))){
                    session()->push('permission',  $params['method_slug']);
                }
            }else{
                $output  = ['status' => 'danger','message' => 'Data can not store.'];
            }
        }
        return $output;
        
    }

    public function editMethod(array $params)
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

    public function updateMethod(array $params)
    {
            if(!empty($params['method_id'])){
                $this->id   = Crypt::decrypt($params['method_id']);
                $method     = $this->find($this->id);
                $this->rules['method_slug']  = 'required|string|unique:company_methods,method_slug,'.$method->id;
                $validator  = Validator::make($params, $this->rules);
                if ($validator->fails()) {
                    $output = array(
                        'errors' => $validator->errors()
                    );
                } else {
                    $old_slug   = $method->method_slug;//store old slug data to remove from session permission array
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

    public function deleteMethod(array $params)
    {
        if(!empty($params['id'])){
            $this->id     = Crypt::decrypt($params['id']);
            CompanyRoleMethodPermission::where('method_id',$this->id)->delete();
            UserMethodPermission::where('method_id',$this->id)->delete();
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
            CompanyRoleMethodPermission::whereIn('method_id',$this->id)->delete();
            UserMethodPermission::whereIn('method_id',$this->id)->delete();
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
}