<?php

namespace Modules\Company\Entities;

use Illuminate\Database\Eloquent\Model;
use App\Helpers\Helper;
class Purchase extends Model
{
    protected $fillable = [
        'company_id', 'branch_id','purchase_no', 'supplier_id', 'total_item', 'total_qty',
        'total_cost', 'order_tax_amount', 'order_discount', 'shipping_cost', 'grand_total',
         'paid_amount', 'due_amount', 'status', 'payment_status', 'document', 'note', 'history'
    ];

    public function supplier()
    {
        return $this->belongsTo('Modules\Company\Entities\Supplier');
    }
    public function branch()
    {
        return $this->belongsTo('Modules\Admin\Entities\Branch');
    }

    public function products()
    {
        return $this->hasMany('Modules\Company\Entities\PurchaseProduct');
    }
    public function payments()
    {
        return $this->hasMany('Modules\Company\Entities\PurchasePayment');
    }

    /***********************************************
     * ==== Start :: DataTable Server Side ==== *
     **********************************************/
    protected $table = 'purchases'; //set table name
    protected $order = array('id' => 'desc'); //set column order by
    protected $column_order; //set data table column sorting key

    

    //Start :: Custom Property
    private $from_date;
    private $to_date;
    private $supplier_id;
    private $payment_status;
    private $status;

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

    public function setSupplierID($supplier_id)
    {
        $this->supplier_id = $supplier_id;
    }
    public function setPaymentStatus($payment_status)
    {
        $this->payment_status = $payment_status;
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
        if(Helper::permission('purchase-bulk-action-delete')){
            $this->column_order = array('','id', 'purchase_no', 'supllier_id','total_item','status','grand_total','paid_amount','due_amount','payment_status','created_at','');
        }else{
            $this->column_order = array('id',  'purchase_no', 'supllier_id','total_item','status','grand_total','paid_amount','due_amount','payment_status','created_at','');
        }
        $query = self::where(['company_id'=>auth()->user()->company_id,'branch_id'=>session()->get('branch')]);

        if (!empty($this->from_date)) {
            $query->where('created_at', '>=',$this->from_date);
        }
        if (!empty($this->to_date)) {
            $query->where('created_at', '<=',$this->to_date);
        }
        if (!empty($this->supplier_id)) {
            $query->where('supplier_id', $this->supplier_id);
        }
        if (!empty($this->payment_status)) {
            $query->where('payment_status', $this->payment_status);
        }
        if (!empty($this->status)) {
            $query->where('status', $this->status);
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
        if ($this->_lengthValue != -1){
            $query->offset($this->_startValue)->limit($this->_lengthValue);
        }
        return $query = $query->get();
    }

    public function count_filtered()
    {
        return $this->_get_datatables_query()->get()->count();
    }

    public function count_all()
    {
        return self::where(['company_id'=>auth()->user()->company_id,'branch_id'=>session()->get('branch')])->get()->count();
    }
    /***********************************************
     * ==== End :: DataTable Server Side ==== *
     **********************************************/
}
