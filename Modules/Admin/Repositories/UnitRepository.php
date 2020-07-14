<?php

namespace Modules\Admin\Repositories;

use Modules\Admin\Contracts\UnitContract;
use App\Repositories\BaseRepository;
use Validator;
use Modules\Admin\Entities\Unit;
use Illuminate\Support\Facades\Crypt;

class UnitRepository extends BaseRepository implements UnitContract
{
    private $rules = [];
    private $id;

    public function __construct(Unit $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function index()
    {
        return $this->model->orderBy('unit_name','asc')->get();
    }

    public function getList(array $params)
    {
        if(!empty($params['unit_name'])){
            $this->model->setUnitName($params['unit_name']);
        }
         
        $this->model->setOrderValue($params['order']);
        $this->model->setDirValue($params['direction']);
        $this->model->setLengthValue($params['length']);
        $this->model->setStartValue($params['start']);

        $list = $this->model->getList();
        
        $data = array();
        $no = $params['start'];
        foreach ($list as $value) {
            $no++;
            $action = '';
            if($this->helper->permission('unit-edit')){
                $action .= '<li class="kt-nav__item"><a class="kt-nav__link edit_data" data-id="' . Crypt::encrypt($value->id) . '" >'.EDIT_ICON.'</a></li>';
            }

            if($this->helper->permission('unit-delete')){
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
             if($this->helper->permission('unit-bulk-action-delete')){
             $row[]  = '<label class="kt-checkbox kt-checkbox--single kt-checkbox--all kt-checkbox--solid"><input type="checkbox" name="did[]" value="' . $value->id . '" class="select_data">&nbsp;<span></span></label> ';
             }
             $row[]  = $no;
             $row[]  = $value->unit_name;
             $row[]  = $value->unit_short;
             $row[]  = $btngroup;
             $data[] = $row;

         }
         return $this->dataTableDraw($params['draw'],$this->model->count_all(),
                                        $this->model->count_filtered(),$data);

    }

    public function createUnit(array $params)
    {
        $this->rules['unit_name']  = 'required|string|unique:units|max:50';
        $this->rules['unit_short'] = 'required|string|unique:units|max:10';
        $validator = Validator::make($params, $this->rules);
        if ($validator->fails()) {
            $output = array(
                'errors' => $validator->errors()
            );
        } else {
            $collection = collect($params);
            $result     = $this->create($collection->all());
            if ($result) {
                $output['status']  = 'success';
                $output['message'] = 'Data has been stored successfully.';
            }else{
                $output['status']   = 'danger';
                $output['message']  = 'Data can not store.';
            }
        }
        return $output;
    }

    public function editUnit(array $params)
    {
        if(!empty($params['id'])){
            $this->id = Crypt::decrypt($params['id']);
            $this->data = $this->find($this->id);
            if(!empty($this->data))
            {
                $output['unit'] = [
                    'id'         => Crypt::encrypt($this->data->id),
                    'unit_name'  => $this->data->unit_name,
                    'unit_short' => $this->data->unit_short,
                ];
            }else{
                $output['status']   = 'danger';
                $output['message']  = 'No Data Found';
            }
        } else {
            $output['status']   = 'danger';
            $output['message']  = 'Unknown format data rejected';
        }
        return $output;
    }

    public function updateUnit(array $params)
    {
        if(!empty($params['unit_id'])){
            $this->id = Crypt::decrypt($params['unit_id']);
            $this->rules['unit_name']  = 'required|string';
            $this->rules['unit_short'] = 'required|string'; 
            $validator = Validator::make($params, $this->rules);
            if ($validator->fails()) {
                $output = array(
                    'errors' => $validator->errors()
                );
            } else {
                $collection = collect($params);
                $updated_at = DATE;
                $merge      = $collection->merge(compact('updated_at'));
                $result     = $this->update($merge->all(),$this->id);
                if ($result) {
                    $output['status']  = 'success';
                    $output['message'] = 'Data has been updated successfully';
                }else{
                    $output['status']   = 'danger';
                    $output['message']  = 'Data can not update.';
                }
            }
        }else{
            $output['status']   = 'danger';
            $output['message']  = 'Data can not update.';
        }
        return $output;
    }

    public function deleteUnit(array $params)
    {
        if(!empty($params['id'])){
            $this->id = Crypt::decrypt($params['id']);
            $result = $this->delete($this->id);
            if ($result) {
                $output['status']  = 'success';
                $output['message'] = 'Data has been deleted successfully.';
            } else {
                $output['status']  = 'error';
                $output['message'] = 'Unable to delete data.';
            }  
        }else{
            $output['status']   = 'error';
            $output['message']  = 'Unknown format data rejected';
        }
        return  $output;
    }

    public function bulk_action_delete(array $params)
    {
        $this->id = $params['id'];
        if(!empty($this->id) && count($this->id) > 0){
            $result = $this->destroy($this->id);
            if($result){
                $output['status']  = 'success';
                $output['message'] = 'Data has been deleted successfully.';
            }else{
                $output['status']  = 'error';
                $output['message'] = 'Unable to delete data.';
            }
        }else{
            $output['status']  = 'error';
            $output['message'] = 'Unable to delete data.';
        }
        return  $output;
    }
}