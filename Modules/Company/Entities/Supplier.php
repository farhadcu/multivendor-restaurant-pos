<?php

namespace Modules\Company\Entities;

use Illuminate\Database\Eloquent\Model;
use App\Helpers\Helper;
use DB;

class Supplier extends Model
{
    protected $fillable = [
        'company_id', 'branch_id', 'supplier_company_name','supplier_name', 'supplier_email', 'supplier_mobile', 'supplier_address'
    ];

    /***********************************************
     * ==== Start :: DataTable Server Side ==== *
     **********************************************/
    protected $_table_name    = 'suppliers'; //set table name
    protected $order          = array('id' => 'desc'); //set column order by
    protected $column_order; //set data table column sorting key

    //Start :: Custom Property
    private $_supplierCompanyName;
    private $_supplierName;
    private $_supplierEmail;
    private $_supplierMobile;
    //End :: Custom Property

    //Start :: Default Property
    private $_orderValue;
    private $_dirValue;
    private $_startValue;
    private $_lengthValue;
    //End :: Default Property

    //Start :: Set custom properties value suppliers 
    public function setSupplierCompanyName($supplierCompanyName)
    {
        $this->_supplierCompanyName = $supplierCompanyName;
    }
    public function setSupplierName($supplierName)
    {
        $this->_supplierName = $supplierName;
    }
    public function setSupplierEmail($supplierEmail)
    {
        $this->_supplierEmail = $supplierEmail;
    }
    public function setSupplierMobile($supplierMobile)
    {
        $this->_supplierMobile = $supplierMobile;
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
        if(Helper::permission('supplier-bulk-action-delete')){
            $this->column_order = array('','id', 'supplier_company_name','supplier_name', 'supplier_mobile','supplier_email','');
        }else{
            $this->column_order = array('id', 'supplier_company_name','supplier_name', 'supplier_mobile','supplier_email','');
        }
        $query = DB::table($this->_table_name)
        ->where(['company_id'=>auth()->user()->company_id,'branch_id'=>session()->get('branch')]);

        if (!empty($this->_supplierCompanyName)) {
            $query->where('supplier_company_name', 'like','%'.$this->_supplierCompanyName.'%');
        }
        if (!empty($this->_supplierName)) {
            $query->where('supplier_name', 'like','%'.$this->_supplierName.'%');
        }
        if (!empty($this->_supplierEmail)) {
            $query->where('supplier_email', 'like','%'.$this->_supplierEmail.'%');
        }
        if (!empty($this->_supplierMobile)) {
            $query->where('supplier_mobile', 'like','%'.$this->_supplierMobile.'%');
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
