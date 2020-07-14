<?php

namespace Modules\Company\Entities;

use Illuminate\Database\Eloquent\Model;
use DB;

class Order extends Model
{
    protected $fillable = ['company_id', 'branch_id','customer_id', 'table_no', 'total_item', 'total_qty',
    'total_amount', 'discount_amount', 'vat_type', 'vat', 'adjustment_type',
     'adjusted_amount', 'grand_total', 'recevied_amount', 'changed_amount', 'status', 'history'];


    public function variation()
    {
        return $this->hasMany('Modules\Company\Entities\OrderHasProduct');
    }



    /***********************************************
     * ==== Start :: DataTable Server Side ==== *
     **********************************************/
    protected $_table_name     = 'orders'; //set table name
    protected $order         = array('id' => 'desc'); //set column order by
    protected $column_order; //set data table column sorting key

    //Start :: Custom Property
    private $from_date;
    private $to_date;
    private $customer_id;
    private $table_no;
    private $status;
    private $_type;

    //End :: Custom Property

    //Start :: Default Property
    private $_orderValue;
    private $_dirValue;
    private $_startValue;
    private $_lengthValue;
    //End :: Default Property

    //Start :: Set custom properties value methods 
    public function setFromDate($from_date)
    {
        $this->from_date = $from_date.' 00:00:01';
    }
    public function setToDate($to_date)
    {
        $this->to_date = $to_date.' 23:59:59';
    }

    public function setCustomerID($customer_id)
    {
        $this->customer_id = $customer_id;
    }
    public function setTableNo($table_no)
    {
        $this->table_no = $table_no;
    }
    public function setStatus($status)
    {
        $this->status = $status;
    }
    public function orderType($type)
    {
        $this->_type = $type;
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

        $this->column_order = array('', 'id','orders.table_no','customers.name', 'total_item','total_qty','total_amount','discount_amount',
        'adjusted_amount','vat','grand_total','status','orders.created_at');

        $query = DB::table($this->_table_name)->select('orders.*','orders.table_no as order_table_no','order_tables.table_no','orders.total_amount','customers.name')
                ->where(['orders.company_id' => auth()->user()->company_id,'orders.branch_id' => session()->get('branch')])
                ->leftjoin('customers', 'orders.customer_id', '=', 'customers.id')
                ->leftjoin('order_tables', 'orders.table_no', '=', 'order_tables.id');
        
        if($this->_type == "pending"){
            $query = $query->where(['orders.status' => 2]);
        }

        if (!empty($this->from_date)) {
            $query->where('orders.created_at', '>=',$this->from_date);
        }
        if (!empty($this->to_date)) {
            $query->where('orders.created_at', '<=',$this->to_date);
        }
        if (!empty($this->customer_id)) {
            $query->where('customers.id', $this->customer_id);
        }
        if (!empty($this->table_no)) {
            $query->where('order_tables.id', $this->table_no);
        }
        if (!empty($this->status)) {
            $query->where('orders.status', $this->status);
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
                    ->where(['company_id'=>auth()->user()->company_id,'branch_id'=>session()
                    ->get('branch')]);

        if($this->_type == "pending"){
            $query = $query->where(['orders.status' => 2]);
        }
        return $query->get()->count();
    }
    /***********************************************
     * ==== End :: DataTable Server Side ==== *
     **********************************************/
}
