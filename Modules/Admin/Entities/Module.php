<?php

namespace Modules\Admin\Entities;

use Illuminate\Database\Eloquent\Model;
use App\Helpers\Helper;
use DB;
use TypiCMS\NestableTrait;

class Module extends Model
{
    use NestableTrait;

    protected $fillable = [
        'module_name','module_link','module_icon','module_sequence','parent_id'
    ];

    public function method() {
        return $this->hasMany('Modules\Admin\Entities\Method');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children()
    {
        return $this->hasMany(Module::class, 'parent_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(Module::class, 'parent_id');
    }

    public function roleModulePermission() {
        return $this->hasMany('Modules\Admin\Entities\AdminRoleModulePermission');
    }

    /***********************************************
     * ==== Start :: DataTable Server Side ==== *
     **********************************************/
    protected $_table_name     = 'modules'; //set table name
    protected $_primary_key    = 'id'; //set primary key
    protected $_primary_filter = 'intval'; //set primary filter
    protected $_order_by       = "id desc"; //set order by

    var $column_order; //set data table column sorting key
    var $column_search = array('module_name');
    var $order         = array('id' => 'desc'); //set column order by

    //Start :: Custom Property
    private $_moduleName;

    //End :: Custom Property

    //Start :: Default Property
    private $_searchValue;
    private $_orderValue;
    private $_dirValue;
    private $_startValue;
    private $_lengthValue;
    //End :: Default Property

    //Start :: Set custom properties value methods 
    public function setModuleName($moduleName)
    {
        $this->_moduleName = $moduleName;
    }
    //Start :: Set custom properties value methods

    //Start :: Set default properties value methods [Do Not Touch This Section]
    public function setSearchValue($searchValue)
    {
        $this->_searchValue = $searchValue;
    }
    public function setOrderValue($orderValue)
    {
        $this->_orderValue = $orderValue;
    }
    public function setDirValue($dirValue)
    {
        $this->_dirValue = $dirValue;
    }
    public function setLengthValue($lengthValue)
    {
        $this->_lengthValue = $lengthValue;
    }
    public function setStartValue($startValue)
    {
        $this->_startValue = $startValue;
    }
    //End :: Set default properties value methods


    private function _get_datatables_query()
    {
        if(Helper::permission('module-bulk-action')){
            $this->column_order = array('','id', 'module_name', 'module_link', 'module_icon','parent_id','module_sequence','');
        }else{
            $this->column_order = array('id', 'module_name', 'module_link', 'module_icon','parent_id','module_sequence','');
        }
        $query = DB::table($this->_table_name);

        if (!empty($this->_moduleName)) {
            $query->where('module_name', 'like','%'.$this->_moduleName.'%');
        }

        //Do Not Touch This Block Section
        /********************************/
        if (isset($this->_orderValue) && isset($this->_dirValue)) // here order processing
        {
            $query->orderBy($this->column_order[$this->_orderValue], $this->_dirValue);

        } else if (isset($this->order)) {

            $order = $this->order;
            $query->orderBy(key($order), $order[key($order)]);
        }
        /********************************/

        return $query;

    }

    public function getList()
    {
        $query = $this->_get_datatables_query();
        if ($this->_lengthValue != -1)
            $query->offset($this->_startValue)->limit($this->_lengthValue);
        return $query = $query->get();

    }

    public function count_filtered()
    {
        $query = $this->_get_datatables_query();
        $query = $query->get();
        return $query->count();
    }

    public function count_all()
    {
        $query = DB::table($this->_table_name)->get()->count();
        return $query;
    }
    /***********************************************
     * ==== End :: DataTable Server Side ==== *
     **********************************************/

     public function parent_name($parent_id){
        $parent = Module::select('module_name')->where('id',$parent_id)->first();
        if(!empty($parent)){
            return $parent->module_name;
        }else{
            return '<span class="kt-badge kt-badge--danger kt-badge--inline kt-badge--pill kt-badge--rounded">No Parent</span>';
        }
     }
}
