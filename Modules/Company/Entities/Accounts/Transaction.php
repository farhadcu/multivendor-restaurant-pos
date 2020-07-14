<?php

namespace Modules\Company\Entities\Accounts;

use Illuminate\Database\Eloquent\Model;
use App\Helpers\Helper;
use DB;
class Transaction extends Model
{
    protected $fillable = [
        'company_id', 'branch_id', 'transaction_no','transaction_type_id', 'transaction_type', 'account_id', 'transaction_category_id',
        'amount', 'description', 'payment_method', 'reference', 'balance', 'transfer_reference', 'document', 'history'
    ];

    /***********************************************
     * ==== Start :: DataTable Server Side ==== *
     **********************************************/
    protected $_table_name    = 'transactions'; //set table name
    protected $order          = array('t.id' => 'desc'); //set column order by
    protected $column_order; //set data table column sorting key

    

    //Start :: Custom Property
    private $from_date;
    private $to_date;
    private $transaction_type;
    private $account;

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

    public function setTransactionType($transaction_type)
    {
        $this->transaction_type = $transaction_type;
    }
    public function setAccount($account)
    {
        $this->account = $account;
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
        if(Helper::permission('transaction-bulk-action-delete')){
            $this->column_order = array('','id', 't.transaction_no', 'a.account_title','t.transaction_type','c.category_name','t.created_at','t.amount','t.amount','t.balance','');
        }else{
            $this->column_order = array('id',  't.transaction_no', 'a.account_title','t.transaction_type','c.category_name','t.created_at','t.amount','t.amount','t.balance','');
        }
        $query = DB::table($this->_table_name.' as t')
        ->select('t.*','a.account_title','c.category_name')
        ->leftjoin('account_heads as a','t.account_id','=','a.id')
        ->leftjoin('transaction_categories as c','t.transaction_category_id','=','c.id')
        ->where(['t.company_id'=>auth()->user()->company_id,'t.branch_id'=>session()->get('branch')]);

        if (!empty($this->from_date)) {
            $query->where('t.created_at', '>=',$this->from_date);
        }
        if (!empty($this->to_date)) {
            $query->where('t.created_at', '<=',$this->type);
        }
        if (!empty($this->transaction_type)) {
            $query->where('t.transaction_type', 'like','%'.$this->transaction_type.'%');
        }
        if (!empty($this->account)) {
            $query->where('t.account_id', $this->account);
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
        $query = self::where(['company_id'=>auth()->user()->company_id,'branch_id'=>session()->get('branch')]);
        if (!empty($this->type)) {
            $query->where('type', $this->type);
        }
        return $query->get()->count();
    }
    /***********************************************
     * ==== End :: DataTable Server Side ==== *
     **********************************************/
}
