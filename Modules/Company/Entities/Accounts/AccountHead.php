<?php

namespace Modules\Company\Entities\Accounts;

use Illuminate\Database\Eloquent\Model;
use App\Helpers\Helper;
use DB;

class AccountHead extends Model
{
    protected $fillable = [
        'company_id', 'branch_id', 'account_type_id', 'account_title', 'account_number', 
        'balance', 'description', 'phone', 'address', 'status', 'history'
    ];

    public function account_type()
    {
        return $this->belongsTo('Modules\Company\Entities\Accounts\AccountType','account_type_id');
    }

    /***********************************************
     * ==== Start :: DataTable Server Side ==== *
     **********************************************/
    protected $_table_name    = 'account_heads'; //set table name
    protected $order          = array('id' => 'desc'); //set column order by
    protected $column_order; //set data table column sorting key

    

    //Start :: Custom Property
    private $account_title;
    private $accountTypeID;
    private $account_number;
    private $balance_from;
    private $balance_to;
    private $status;

    //End :: Custom Property

    //Start :: Default Property
    private $_orderValue;
    private $_dirValue;
    private $_startValue;
    private $_lengthValue;
    //End :: Default Property

    //Start :: Set custom properties value methods 
    public function setAccountTitle($account_title)
    {
        $this->account_title = $account_title;
    }
    public function setAccountTypeID($accountTypeID)
    {
        $this->accountTypeID = $accountTypeID;
    }
    public function setAccountNumber($account_number)
    {
        $this->account_number = $account_number;
    }
    public function setBalanceFrom($balance_from)
    {
        $this->balance_from = $balance_from;
    }
    public function setBalanceTo($balance_to)
    {
        $this->balance_to = $balance_to;
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
        if(Helper::permission('chart-of-account-bulk-action-delete')){
            $this->column_order = array('','id', 'account_type_id', 'account_title','account_number','balance','status','');
        }else{
            $this->column_order = array('id', 'account_type_id', 'account_title','account_number','balance','status','');
        }
        $query = self::where(['company_id'=>auth()->user()->company_id,'branch_id'=>session()->get('branch')]);

        if (!empty($this->accountTypeID)) {
            $query->where('account_type_id', $this->accountTypeID);
        }
        if (!empty($this->account_title)) {
            $query->where('account_title', 'like','%'.$this->account_title.'%');
        }
        if (!empty($this->account_number)) {
            $query->where('account_number', $this->account_number);
        }
        if (!empty($this->balance_from)) {
            $query->where('balance', '>=',$this->balance_from);
        }
        if (!empty($this->balance_to)) {
            $query->where('balance', '<=',$this->balance_to);
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
        $query = self::where(['company_id'=>auth()->user()->company_id,'branch_id'=>session()->get('branch')])
                ->get()->count();
        return $query;
    }
    /***********************************************
     * ==== End :: DataTable Server Side ==== *
     **********************************************/
}
