<?php

namespace Modules\Admin\Entities;

use Illuminate\Database\Eloquent\Model;
use App\Helpers\Helper;
use DB;

class CompanyMethod extends Model
{
    protected $fillable = [
        'module_id','method_name','method_slug'
    ];

    public function module() {
        return $this->belongsTo('Modules\Admin\Entities\CompanyModule');
    }

    public function roleMethodPermission() {
        return $this->hasMany('Modules\Admin\Entities\CompanyRoleMethodPermission');
    }

    /***********************************************
     * ==== Start :: DataTable Server Side ==== *
     **********************************************/
    protected $_table_name = 'company_methods'; //set table name
    var $order             = array('me.id' => 'desc'); //set column order by
    var $column_order; //set data table column sorting key

    //Start :: Custom Property
    private $_methodName;
    private $_moduleID;

    //End :: Custom Property

    //Start :: Default Property
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
        if(Helper::permission('company-method-bulk-action')){
            $this->column_order = array('','me.id', 'me.method_name', 'me.method_slug','me.module_id','');
        }else{
            $this->column_order = array('me.id', 'me.method_name', 'me.method_slug','me.module_id','');
        }
        $query = DB::table($this->_table_name.' as me')
        ->select('me.*','m.module_name','m.module_icon')
        ->leftjoin('company_modules as m','me.module_id','=','m.id');

        if (!empty($this->_methodName)) {
            $query->where('me.method_name', 'like','%'.$this->_methodName.'%');
        }
        if (!empty($this->_moduleID)) {
            $query->where('me.module_id',$this->_moduleID);
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
