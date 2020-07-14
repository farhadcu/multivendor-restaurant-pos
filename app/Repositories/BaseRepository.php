<?php

namespace App\Repositories;

use App\Contracts\BaseContract;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\Helper;
/**
 * Class BaseRepository
 *
 * @package \App\Repositories
 */
class BaseRepository implements BaseContract
{
    /**
     * @var Model
     */
    protected $model;
    protected $helper;
    protected $data;
    protected $permission_reserved_keywords = [
        'List','Manage','Add','Edit','View','Delete','Bulk Action Delete',
        'Report','Status Change','Password Change','Permission',
        'General','SMTP','SMS','API','Change Status','Print','Report Manage'
    ];
    /**
     * BaseRepository constructor.
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->helper = new Helper(); 
        $this->model = $model;
    }

    /**
     * @param array $attributes
     * @return mixed
     */
    public function create(array $attributes)
    {
        return $this->model->create($attributes);
    }

    /**
     * @param array $attributes
     * @param int $id
     * @return bool
     */
    public function update(array $attributes, int $id) : bool
    {
        return $this->find($id)->update($attributes);
    }

    /**
     * @param array $columns
     * @param string $orderBy
     * @param string $sortBy
     * @return mixed
     */
    public function all($columns = array('*'), string $orderBy = 'id', string $sortBy = 'asc')
    {
        return $this->model->orderBy($orderBy, $sortBy)->get($columns);
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function find(int $id)
    {
        return $this->model->find($id);
    }

    /**
     * @param int $id
     * @return mixed
     * @throws ModelNotFoundException
     */
    public function findOneOrFail(int $id)
    {
        return $this->model->findOrFail($id);
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function findBy(array $data)
    {
        return $this->model->where($data)->all();
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function findOneBy(array $data)
    {
        return $this->model->where($data)->first();
    }

    /**
     * @param array $data
     * @return mixed
     * @throws ModelNotFoundException
     */
    public function findOneByOrFail(array $data)
    {
        return $this->model->where($data)->firstOrFail();
    }

    /**
     * @param int $id
     * @return bool
     */
    public function delete(int $id) : bool
    {
        return $this->model->find($id)->delete();
    }

    public function destroy(array $data)
    {
        return $this->model->destroy($data);
    }


    /**
     * @param int $draw
     * @param int $count_all
     * @param int $count_filtered
     * @param array $data
     */
    protected function dataTableDraw($draw,$count_all,$count_filtered, $data){
        return $output= array(
            "draw"            => $draw,//draw data
            "recordsTotal"    => $count_all,//total record
            "recordsFiltered" => $count_filtered,//total filtered record
            "data"            => $data//showing data
        );
    }

    //switch view method. required two data one is the changing data id and the other one is for status
    protected function switch_view(array $data){
        ($data['checked'] == 'checked')? $color = 'kt-switch--brand': $color = 'kt-switch--danger';
        $output = '<span class="kt-switch '.$color.'">
                    <label>
                        <input type="checkbox" name="change_status" data-id="' . $data['id'] . '" class="change_status" '.$data['checked'].'>
                        <span></span>
                    </label>
                </span>';
        return $output;
    }


}
