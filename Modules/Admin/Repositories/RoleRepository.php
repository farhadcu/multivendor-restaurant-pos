<?php

namespace Modules\Admin\Repositories;

use Modules\Admin\Contracts\RoleContract;
use App\Repositories\BaseRepository;
use Validator;
use App\Models\Role;
use Modules\Admin\Entities\Admin;
use Modules\Admin\Entities\AdminRoleMethodPermission;
use Modules\Admin\Entities\AdminRoleModulePermission;
use Illuminate\Support\Facades\Crypt;

class RoleRepository extends BaseRepository implements RoleContract
{
    private $rules;
    private $id;
    /**
     * RoleRepository constructor.
     * @param Role $model
     */
    public function __construct(Role $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function getList(array $params)
    {
        if(!empty($params['role_name'])){
            $this->model->setRoleName($params['role_name']);
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
            if($this->helper->permission('role-edit')){
                $action .= '<li class="kt-nav__item"><a class="kt-nav__link edit_data" data-id="' . Crypt::encrypt($value->id) . '" >'.EDIT_ICON.'</a></li>';
            }

            if($this->helper->permission('role-delete')){
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
            if($this->helper::permission('role-bulk-action-delete')){
            $row[]  = '<label class="kt-checkbox kt-checkbox--single kt-checkbox--all kt-checkbox--solid"><input type="checkbox" name="did[]" value="' . $value->id . '" class="select_data">&nbsp;<span></span></label> ';
            }
            $row[]  = $no;
            $row[]  = $value->role;
            $row[]  = $btngroup;
            $data[] = $row;

        }
        return  $this->dataTableDraw($params['draw'], $this->model->count_all(),
                                        $this->model->count_filtered(),$data);
    }


    public function createRole(array $params)
    {
        $this->rules['role']    = 'required|string|unique:roles';
        $validator              = Validator::make($params, $this->rules);
        if ($validator->fails()) 
        {
            $output             = array( 'errors' => $validator->errors() );
        } else {
            $collection         = collect($params);
            $result             = $this->create($collection->all());
            if ($result) {
                $output         = ['status' => 'success', 'message' => 'Data has been saved successfully.'];
            }else{
                $output         = ['status' => 'danger', 'message' => 'Data can not save.'];
            }
        }
        return $output;
    }

     /**
     * @param array $params
     * @return mixed
     */
    public function editRole(array $params)
    {
        if(!empty($params['id'])){
            $this->data           = $this->find((int) Crypt::decrypt($params['id']));
            $collection           = collect($this->data)->except(['id','created_at','updated_at']);
            $id                   = $params['id'];
            $merge                = $collection->merge(compact('id'));
            if(!empty($merge))
            {
                $output['role']   = $merge->all();
            }else{
                $output           = ['status' => 'danger', 'message' => 'No data found'];
            }
        }else {
            $output               = ['status' => 'danger', 'message' => 'No data found'];
        }
        return $output;
    }

    /**
     * @param array $params
     * @return mixed
     */
    public function updateRole(array $params)
    {
        if(!empty($params['role_id'])){
            $this->id               = Crypt::decrypt($params['role_id']);
            $role                   = $this->find($this->id);
            $this->rules['role']    = 'required|string|unique:roles,role,'.$role->id;
            $validator              = Validator::make($params, $this->rules);
            if ($validator->fails()) 
            {
                $output = array( 'errors' => $validator->errors() );

            } else {
                $role->role             = $params['role'];
                $role->updated_at       = DATE;
                if ($role->update()) {
                    $output = ['status' => 'success', 'message' => 'Data has been updated successfully.'];
                }else{
                    $output = ['status' => 'danger', 'message' => 'Data can not update'];
                }
            }
        }else{
            $output = ['status' => 'danger', 'message' => 'Data can not update'];
        }

        return $output;
    }

    /**
     * @param $id
     * @return bool|mixed
     */
    public function deleteRole(array $params)
    {
        if(!empty($params['id'])){
            $this->id   = Crypt::decrypt($params['id']);
            $total_data = Admin::where('role_id', $this->id)->get()->count();
            if ($total_data > 0) {
                $output = ['status'  => 'danger','message' => 'Data can\'t delete. It is related with other data. At first delete those data. '];
            } else {
                AdminRoleMethodPermission::where('role_id', $this->id)->delete();
                AdminRoleModulePermission::where('role_id', $this->id)->delete();
                $result       = $this->delete((int)$this->id);
                if ($result) {
                    $output   = ['status' => 'success', 'message' => 'Data has been deleted successfully.'];
                } else {
                    $output   = ['status' => 'danger', 'message' => 'Data can not delete.'];
                }
            }
        }else{
            $output           = ['status' => 'danger', 'message' => 'Data can not delete.'];
        }

        return $output;
    }

    /**
     * @param array $params
     * @return mixed
     */
    public function bulk_action_delete(array $params)
    {
        if(is_array($params['id'])){
            foreach ($params['id'] as $value) {
                $total_data       = Admin::where('role_id', $value)->get()->count();
                if ($total_data > 0) {
                    $output       = ['status'  => 'danger','message' => 'Data can\'t delete. It is related with other data. At first delete those data. '];
                }else{
                    AdminRoleMethodPermission::where('role_id',$value)->delete();
                    AdminRoleModulePermission::where('role_id', $value)->delete();
                    $this->data   = $this->delete($value);
                }
            }
            if($this->data){
                $output           = ['status' => 'success', 'message' => 'Data has been deleted successfully.'];
            }else{
                $output           = ['status' => 'danger', 'message' => 'Data can not delete.'];
            }
        }else{
            $output               = ['status' => 'danger', 'message' => 'Data can not delete.'];
        }
        return $output;
    }
}