<?php 

namespace Modules\Company\Repositories\Product;


use App\Repositories\BaseRepository;
use Validator;
use Modules\Company\Entities\Product\Category;
use Illuminate\Support\Facades\Crypt;
use Modules\Company\Contracts\Product\CategoryContract;

class CategoryRepository extends BaseRepository implements CategoryContract
{
    private $rules = [
        'parent_id'       => 'numeric|nullable',
    ];
    private $id;

    public function __construct(Category $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function getList(array $params)
    {
        
        if(!empty($params['category_name'])){
            $this->model->setCategoryName($params['category_name']);
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
            if($this->helper->permission('category-edit')){
                $action .= '<li class="kt-nav__item"><a class="kt-nav__link edit_data" data-id="' . Crypt::encrypt($value->id) . '" >'.EDIT_ICON.'</a></li>';
            }

            if($this->helper->permission('category-delete')){
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
            if($this->helper->permission('category-bulk-action-delete')){
            $row[]  = '<label class="kt-checkbox kt-checkbox--single kt-checkbox--all kt-checkbox--solid"><input type="checkbox" name="did[]" value="' . $value->id . '" class="select_data">&nbsp;<span></span></label> ';
            }
            $row[]  = $no;
            $row[]  = $value->category_name;
            $row[]  = $this->model->parent_name($value->parent_id);
            $row[]  = $this->switch_view($status);
            $row[]  = $btngroup;
            $data[] = $row;

        }
        return $this->dataTableDraw($params['draw'], $this->model->count_all(),
        $this->model->count_filtered(),$data);
        
    }


    public function createCategory(array $params)
    {

        $this->rules['category_name']  = 'required|string|unique:categories';
        
        $validator = Validator::make($params, $this->rules);
        if ($validator->fails()) {
            $output = array(
                'errors' => $validator->errors()
            );
        } else {
            $collection   = collect($params)->except(['parent_id']);
            $parent_id    = !empty($params['parent_id']) ? $params['parent_id'] : 0;
            $company_id   = auth()->user()->company_id;
            $branch_id    = session()->get('branch');
            $merge        = $collection->merge(compact('company_id','branch_id','parent_id'));
            $result       = $this->create($merge->all());
            if ($result) {
                $output   = ['status' => 'success', 'message' => 'Data has been saved successfully.'];
            }else{
                $output   = ['status' => 'danger', 'message' => 'Data can not save.'];
            }
        }
        return $output;
    }

    public function editCategory(array $params)
    {
        if(!empty($params['id'])){
            $this->id               = Crypt::decrypt($params['id']);
            $this->data             = $this->find((int) $this->id);
            $collection             = collect($this->data)->except(['id','created_at','updated_at']);
            $id                     = $params['id'];
            $merge                  = $collection->merge(compact('id'));
            if(!empty($merge))
            {
                $output['category']   = $merge->all();
            }else{
                $output             = ['status' => 'danger','message' => 'No Data Found'];
            }
        }else{
            $output                 = ['status' => 'danger','message' => 'No Data Found'];
        }
        return $output;
    }

    public function updateCategory(array $params)
    {
        if(!empty($params['update_id'])){
            $this->id                       = Crypt::decrypt($params['update_id']);
            $category                       = $this->find($this->id);
            $this->rules['category_name']   = 'required|string|unique:categories,category_name,'.$category->id;
            $validator                      = Validator::make($params, $this->rules);
            if ($validator->fails()) {
                $output                     = array( 'errors' => $validator->errors());
            } else {
                $collection                 = collect($params)->except(['update_id','parent_id']);
                $parent_id                  = !empty($params['parent_id']) ? $params['parent_id'] : 0;
                $updated_at                 = DATE;
                $company_id                 = auth()->user()->company_id;
                $branch_id                  = session()->get('branch');
                $merge                      = $collection->merge(compact('company_id','branch_id','parent_id','updated_at'));
                $result                     = $this->update($merge->all(),(int)$this->id);
                if ($result) {
                    $output                 = ['status'  => 'success','message' => 'Data has been updated successfully'];
                }else{
                    $output                 = ['status' => 'danger','message' => 'Data can not update'];
                }
            }
        }else{
            $output                         = ['status' => 'danger','message' => 'Data can not update'];
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

    public function deleteCategory(array $params)
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

    
    public function category_list(){
        $category =  $this->model->orderByRaw('category_name ASC') //sequence according to name ascending order
            ->where(['company_id'=>auth()->user()->company_id,'branch_id'=>session()->get('branch')])    
            ->get()
            ->nest()
            ->setIndent('–– ') //append before child module
            ->listsFlattened('category_name'); //name that will show in frontend

        $output = '<option value="">Select Please</option>';
        foreach ($category as $key => $value) {
            $output .= "<option value='$key'>$value</option>";
        }
        return $output;
    }

}
