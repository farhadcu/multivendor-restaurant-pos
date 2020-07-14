<?php

namespace Modules\Admin\Entities;

use Illuminate\Database\Eloquent\Model;
use App\Helpers\Helper;
use DB;

class Company extends Model
{
    protected $fillable = [
        'company_name','owner_name','email','mobile','phone','address','logo','favicon','invoice_logo',
        'invoice_prefix','vat','status','history'
    ];

    public function branch(){
        return $this->hasMany('Modules\Admin\Entities\Branch');
    }
    public function company_subscription(){
        return $this->hasMany('Modules\Admin\Entities\CompnaySubscription');
    }

    /***********************************************
     * ==== Start :: DataTable Server Side ==== *
     **********************************************/
    protected $_table_name    = 'companies'; //set table name
    var $order                = array('c.id' => 'desc'); //set column order by
    var $column_order;//set data table column sorting key

    //Start :: Custom Property
    private $type;

    //End :: Custom Property

    //Start :: Default Property
    private $_orderValue;
    private $_dirValue;
    private $_startValue;
    private $_lengthValue;
    //End :: Default Property

    //Start :: Set custom properties value methods 
    public function setType($type)
    {
        $this->type = $type;
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
        if(Helper::permission('company-bulk-action-delete') && Helper::permission('company-change-status')){
            $this->column_order = array('','c.id', '','c.company_name', 'c.owner_name','c.email','c.mobile','s.type','s.start_date','s.end_date','c.status','');
        }else{
            $this->column_order = array('c.id', '','c.company_name', 'c.owner_name','c.email','c.mobile','s.type','s.start_date','s.end_date','c.status','');
        }
        $query = DB::table($this->_table_name.' as c')
        ->select('c.*','s.type','s.start_date','s.end_date')
        ->leftjoin('company_subscriptions as s','c.id','=','s.company_id');

        if (!empty($this->type)) {
            $query->where('s.type', 'like','%'.$this->type.'%');
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
