<?php

namespace Modules\Admin\Entities;

use Illuminate\Database\Eloquent\Model;
use App\Helpers\Helper;
use DB;

class Subscription extends Model
{
    protected $fillable = [
        'type','total_branch_account','total_user_account','amount','duration_month'
    ];

    /***********************************************
     * ==== Start :: DataTable Server Side ==== *
     **********************************************/
    protected $_table_name     = 'subscriptions'; //set table name
    protected $_primary_key    = 'id'; //set primary key
    protected $_primary_filter = 'intval'; //set primary filter
    protected $_order_by       = "id desc"; //set order by

    var $column_order; //set data table column sorting key
    var $order         = array('id' => 'desc'); //set column order by

    //Start :: Custom Property
    private $type;

    //End :: Custom Property

    //Start :: Default Property
    private $_searchValue;
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

    //Start :: Set custom properties value methods

    //Start :: Set default properties value methods [Do Not Touch This Section]
    public function setSearchValue($searchValue)
    {
        $this->_searchValue = $searchValue;
    }
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
        if(Helper::permission('subscription-bulk-action-delete')){
            $this->column_order = array('','id', 'type', 'total_branch_account','total_user_account','amount','duration_month','');
        }else{
            $this->column_order = array('id',  'type', 'total_branch_account','total_user_account','amount','duration_month','');
        }
        $query = DB::table($this->_table_name);

        if (!empty($this->type)) {
            $query->where('type', 'like','%'.$this->type.'%');
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
