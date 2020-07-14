<?php

namespace Modules\Company\Entities\Accounts;

use Illuminate\Database\Eloquent\Model;
use App\Helpers\Helper;
use DB;

class AccountType extends Model
{
    protected $fillable = [
        'company_id', 'branch_id', 'account_type', 'account_id', 'status', 'history'
    ];

 /***********************************************
     * ==== Start :: DataTable Server Side ==== *
     **********************************************/
    protected $_table_name    = 'account_types'; //set table name
    protected $order          = array('id' => 'desc'); //set column order by
    protected $column_order; //set data table column sorting key

    

    //Start :: Custom Property
    private $accountType;
    private $accountID;
    private $status;

    //End :: Custom Property

    //Start :: Default Property
    private $_orderValue;
    private $_dirValue;
    private $_startValue;
    private $_lengthValue;
    //End :: Default Property

    //Start :: Set custom properties value methods 
    public function setAccountType($accountType)
    {
        $this->accountType = $accountType;
    }
    public function setAccountID($accountID)
    {
        $this->accountID = $accountID;
    }
    public function setStatus($status)
    {
        $this->status = $status;
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
        if(Helper::permission('acount-type-bulk-action-delete')){
            $this->column_order = array('','id', 'account_type', 'account_id','status','');
        }else{
            $this->column_order = array('id', 'account_type', 'account_id','status','');
        }
        $query = DB::table($this->_table_name)
        ->where(['company_id'=>auth()->user()->company_id,'branch_id'=>session()->get('branch')]);

        if (!empty($this->accountType)) {
            $query->where('account_type', 'like','%'.$this->accountType.'%');
        }
        if (!empty($this->accountID)) {
            $query->where('account_id', $this->accountID);
        }
        if (!empty($this->status)) {
            $query->where('status',$this->status);
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
