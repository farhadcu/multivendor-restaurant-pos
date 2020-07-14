<?php

namespace Modules\Admin\Entities;

use Illuminate\Database\Eloquent\Model;
use App\Helpers\Helper;
use DB;
class Method extends Model
{
    protected $fillable = [
        'module_id','method_name','method_slug'
    ];

    public function module() {
        return $this->belongsTo('Modules\Admin\Entities\Module');
    }

    public function roleMethodPermission() {
        return $this->hasMany('Modules\Admin\Entities\AdminRoleMethodPermission');
    }

    /***********************************************
     * ==== Start :: DataTable Server Side ==== *
     **********************************************/
    protected $_table_name     = 'methods'; //set table name
    protected $_primary_key    = 'id'; //set primary key
    protected $_primary_filter = 'intval'; //set primary filter
    protected $_order_by       = "id desc"; //set order by

    var $column_order; //set data table column sorting key
    var $column_search = array('method_name');
    var $order         = array('id' => 'desc'); //set column order by

    //Start :: Custom Property
    private $_methodName;
    private $_moduleID;

    //End :: Custom Property

    //Start :: Default Property
    private $_searchValue;
    private $_orderValue;
    private $_dirValue;
    private $_startValue;
    private $_lengthValue;
    //End :: Default Property

    //Start :: Set custom properties value methods 
    public function setMethodName($methodName)
    {
        $this->_methodName = $methodName;
    }
    public function setModuleID($moduleID)
    {
        $this->_moduleID = $moduleID;
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
        if(Helper::permission('method-bulk-action')){
            $this->column_order = array('','id', 'method_name', 'method_slug','module_id','');
        }else{
            $this->column_order = array('id', 'method_name', 'method_slug','module_id','');
        }
        $query = DB::table($this->_table_name)
        ->select('methods.*','modules.module_name','modules.module_icon')
        ->leftjoin('modules','methods.module_id','=','modules.id');

        if (!empty($this->_methodName)) {
            $query->where('methods.method_name', 'like','%'.$this->_methodName.'%');
        }
        if (!empty($this->_moduleID)) {
            $query->where('methods.module_id',$this->_moduleID);
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
}
