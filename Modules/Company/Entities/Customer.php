<?php

namespace Modules\Company\Entities;

use Illuminate\Database\Eloquent\Model;
use App\Helpers\Helper;
use DB;


class Customer extends Model
{
    protected $fillable = [
        'company_id', 'branch_id', 'customer_group', 'name', 'email', 'mobile', 'address', 'city', 'postal_code'
    ];

     /***********************************************
     * ==== Start :: DataTable Server Side ==== *
     **********************************************/
    protected $_table_name    = 'customers'; //set table name
    protected $order          = array('id' => 'desc'); //set column order by
    protected $column_order; //set data table column sorting key

    //Start :: Custom Property
    private $_customer_group;
    private $_name;
    private $_email;
    private $_mobile;
    //End :: Custom Property

    //Start :: Default Property
    private $_orderValue;
    private $_dirValue;
    private $_startValue;
    private $_lengthValue;
    //End :: Default Property

    //Start :: Set custom properties value suppliers 
    public function setCustomerGroup($customer_group)
    {
        $this->_customer_group = $customer_group;
    }
    public function setName($name)
    {
        $this->_name = $name;
    }
    public function setEmail($email)
    {
        $this->_email = $email;
    }
    public function setMobile($mobile)
    {
        $this->_mobile = $mobile;
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
        if(Helper::permission('customer-bulk-action-delete')){
            $this->column_order = array('','id', 'customer_group','name', 'mobile','email','city','postal_code','');
        }else{
            $this->column_order = array('id', 'customer_group','name', 'mobile','email','city','postal_code','');
        }
        $query = DB::table($this->_table_name)
        ->where(['company_id'=>auth()->user()->company_id,'branch_id'=>session()->get('branch')]);

        if (!empty($this->_customer_group)) {
            $query->where('customer_group', $this->_customer_group);
        }
        if (!empty($this->_name)) {
            $query->where('name', 'like','%'.$this->_name.'%');
        }
        if (!empty($this->_email)) {
            $query->where('email', 'like','%'.$this->_email.'%');
        }
        if (!empty($this->_mobile)) {
            $query->where('mobile', 'like','%'.$this->_mobile.'%');
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
