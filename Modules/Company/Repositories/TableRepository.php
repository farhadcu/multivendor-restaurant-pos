<?php
namespace Modules\Company\Repositories;

use Modules\Company\Contracts\TableContract;
use App\Repositories\BaseRepository;
use Validator;
use Modules\Company\Entities\OrderTable;
use Illuminate\Support\Facades\Crypt;

class TableRepository extends BaseRepository implements TableContract
{
    private $rules = [
        'table_no'  => 'required|string',
    ];
    private $id;
    

    public function __construct(OrderTable $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function index()
    {
        return $this->model->where(['company_id'=>auth()->user()->company_id,'branch_id'=>session()->get('branch')])
                            ->orderBy('table_no','asc')->get();
    }

    public function getList(array $params)
    {
        if(!empty($params['table_no'])){
            $this->model->setTableNo($params['table_no']);
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
            if($this->helper->permission('table-edit')){
                $action .= '<li class="kt-nav__item"><a class="kt-nav__link edit_data" data-id="' .Crypt::encrypt($value->id). '" >'.EDIT_ICON.'</a></li>';
            }

            if($this->helper->permission('table-delete')){
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
            if($this->helper->permission('table-bulk-action-delete')){
            $row[]  = '<label class="kt-checkbox kt-checkbox--single kt-checkbox--all kt-checkbox--solid"><input type="checkbox" name="did[]" value="' . $value->id . '" class="select_data">&nbsp;<span></span></label> ';
            }
            $row[]  = $no;
            $row[]  = $value->table_no;
            $row[]  = $btngroup;
            $data[] = $row;

        }
        return $this->dataTableDraw($params['draw'], $this->model->count_all(), $this->model->count_filtered(),$data);
    }
    public function createTable(array $params)
    {
        $validator               = Validator::make($params, $this->rules);
        if ($validator->fails()) {
            $output = array(
                'errors' => $validator->errors()
            );
        } else {
            $collection   = collect($params);
            $merge        = $collection->merge([
                                'company_id'=> auth()->user()->company_id,
                                'branch_id' => session()->get('branch')
                            ]);
            $result       = $this->create($merge->all());
            if ($result) {
                $output   = ['status' => 'success','message' => 'Data has been stored successfully.'];
            }else{
                $output   = ['status' => 'danger','message' => 'Data can not store.'];
            }
        }
        return $output;
        
    }

    public function editTable(array $params)
    {
        if(!empty($params['id'])){
            $this->id               = Crypt::decrypt($params['id']);
            $this->data             = $this->find((int)$this->id);
            $collection             = collect($this->data)->except(['id','created_at','updated_at']);
            $id                     = $params['id'];
            $merge                  = $collection->merge(compact('id'));
            if(!empty($merge))
            {
                $output['table'] = $merge->all();
            }else{
                $output             = ['status' => 'danger','message' => 'No data found.'];
            }
        }else {
            $output                 = ['status' => 'danger','message' => 'No data found.'];
        }
        return $output;
        
    }

    public function updateTable(array $params)
    {
            if(!empty($params['update_id'])){
                $this->id   = Crypt::decrypt($params['update_id']);
                $validator  = Validator::make($params, $this->rules);
                if ($validator->fails()) {
                    $output = array(
                        'errors' => $validator->errors()
                    );
                } else {
                    $collection   = collect($params)->except(['update_id']);
                    $merge        = $collection->merge([
                                        'company_id' => auth()->user()->company_id,
                                        'branch_id'  => session()->get('branch'),
                                        'updated_at' => DATE,
                                    ]);
                    $result       = $this->update($merge->all(),(int)$this->id);
                    if ($result) {
                        $output   = ['status' => 'success','message' => 'Data has been updated successfully.'];
                    }else{
                        $output   = ['status' => 'danger','message' => 'Data can not update.'];
                    }
                }
            }else{
                $output           = ['status' => 'danger','message' => 'Data can not update.'];
            }
            return $output;
    }

    public function deleteTable(array $params)
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
        $this->id         = $params['id'];
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
}