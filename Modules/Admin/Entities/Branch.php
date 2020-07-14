<?php

namespace Modules\Admin\Entities;

use Illuminate\Database\Eloquent\Model;
use App\Helpers\Helper;
use DB;

class Branch extends Model
{
    protected $fillable = [
        'company_id','branch_name','branch_slug','branch_email','branch_mobile',
        'branch_phone','branch_address','status'
    ];

    public function company(){
        return $this->belongsTo('Modules\Admin\Entities\Compnay');
    }

    /***********************************************
     * ==== Start :: DataTable Server Side ==== *
     **********************************************/
    protected $_table_name    = 'branches'; //set table name
    var $order                = array('b.id' => 'desc'); //set column order by
    var $column_order;//set data table column sorting key

    //Start :: Custom Property
    private $branchName;
    private $companyID;

    //End :: Custom Property

    //Start :: Default Property
    private $_orderValue;
    private $_dirValue;
    private $_startValue;
    private $_lengthValue;
    //End :: Default Property

    //Start :: Set custom properties value methods 
    public function setBranchName($branchName)
    {
        $this->branchName = $branchName;
    }
    public function setCompanyID($companyID)
    {
        $this->companyID = $companyID;
    }

    //End :: Set custom properties value methods

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
        if(Helper::permission('branch-bulk-action-delete') && Helper::permission('branch-change-status')){
            $this->column_order = array('','b.id', '','b.company_name', 'b.branch_name','b.branch_email','b.branch_mobile','b.branch_phone','b.status','');
        }else{
            $this->column_order = array('b.id', '','b.company_name', 'b.branch_name','b.branch_email','b.branch_mobile','b.branch_phone','b.status','');
        }
        $query = DB::table($this->_table_name.' as b')
        ->select('b.*','c.company_name')
        ->leftjoin('companies as c','b.company_id','=','c.id');

        if (!empty($this->branchName)) {
            $query->where('b.branch_name', 'like','%'.$this->branchName.'%');
        }
        if (!empty($this->companyID)) {
            $query->where('b.company_id', $this->companyID);
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
