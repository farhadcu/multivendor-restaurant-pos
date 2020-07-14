<?php

namespace Modules\Company\Entities;

use Illuminate\Database\Eloquent\Model;
use App\Helpers\Helper;
use DB;

class OrderTable extends Model
{
    protected $fillable = ['company_id', 'branch_id', 'table_no'];

    /***********************************************
     * ==== Start :: DataTable Server Side ==== *
     **********************************************/
    protected $_table_name    = 'order_tables'; //set table name
    protected $order          = array('id' => 'desc'); //set column order by
    protected $column_order; //set data table column sorting key

    //Start :: Custom Property
    private $_tableNo;
    //End :: Custom Property

    //Start :: Default Property
    private $_orderValue;
    private $_dirValue;
    private $_startValue;
    private $_lengthValue;
    //End :: Default Property

    //Start :: Set custom properties value suppliers 
    public function setTableNo($tableNo)
    {
        $this->_tableNo = $tableNo;
    }

    //Start :: Set custom properties value suppliers

    //Start :: Set default properties value suppliers [Do Not Touch This Section]

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
    //End :: Set default properties value suppliers


    private function _get_datatables_query()
    {
        if(Helper::permission('table-bulk-action-delete')){
            $this->column_order = array('','id', 'table_no','');
        }else{
            $this->column_order = array('id', 'table_no','');
        }
        $query = DB::table($this->_table_name)
        ->where(['company_id'=>auth()->user()->company_id,'branch_id'=>session()->get('branch')]);

        if (!empty($this->_tableNo)) {
            $query->where('table_no', 'like','%'.$this->_tableNo.'%');
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
        $query = DB::table($this->_table_name)
                ->where(['company_id'=>auth()->user()->company_id,'branch_id'=>session()->get('branch')])
                ->get()->count();
        return $query;
    }
    /***********************************************
     * ==== End :: DataTable Server Side ==== *
     **********************************************/
}
