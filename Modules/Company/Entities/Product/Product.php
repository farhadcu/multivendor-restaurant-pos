<?php

namespace Modules\Company\Entities\Product;

use Illuminate\Database\Eloquent\Model;
use App\Helpers\Helper;
use DB;

class Product extends Model
{
    protected $fillable = [
        'company_id', 'branch_id', 'supplier_id', 'name', 'model', 'sku',
        'upc', 'mpn', 'image', 'purchase_price', 'selling_price', 'qty',
        'min_qty', 'max_qty', 'stock_unit', 'rack_no', 'length', 'width',
        'height', 'weight','mpg_date', 'exp_date', 'subtract_stock',
        'returnable', 'status', 'history'
    ];

    public function discount()
    {
        return $this->hasOne('Modules\Company\Entities\Product\ProductHasDiscount');
    }

    public function categories()
    {
        return $this->hasMany('Modules\Company\Entities\Product\ProductHasCategory');
    }
    public function variation()
    {
        return $this->hasMany('Modules\Company\Entities\Product\ProductHasVariation');
    }

    /***********************************************
     * ==== Start :: DataTable Server Side ==== *
     **********************************************/
    protected $_table_name     = 'products'; //set table name
    protected $order         = array('id' => 'desc'); //set column order by
    protected $column_order; //set data table column sorting key

    //Start :: Custom Property
    private $_name;
    private $_model;
    private $_returnable;
    private $_rackNo;
    private $_status;

    //End :: Custom Property

    //Start :: Default Property
    private $_orderValue;
    private $_dirValue;
    private $_startValue;
    private $_lengthValue;
    //End :: Default Property

    //Start :: Set custom properties value methods 
    public function setName($name)
    {
        $this->_name = $name;
    }
    public function setModel($model)
    {
        $this->_model = $model;
    }
    public function setReturnable($returnable)
    {
        $this->_returnable = $returnable;
    }
    public function setRackNo($rackNo)
    {
        $this->_rackNo = $rackNo;
    }
    public function setStatus($status)
    {
        $this->_status = $status;
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
        if(Helper::permission('product-bulk-action-delete') && Helper::permission('product-change-status')){
            $this->column_order = array('','id', 'id','model','name', 'qty','min_qty','max_qty','purchase_price','selling_price','returnable','rack_no','status','');
        }elseif(!Helper::permission('product-bulk-action-delete') && Helper::permission('product-change-status')){
            $this->column_order = array('id', 'id','model','name', 'qty','min_qty','max_qty','purchase_price','selling_price','returnable','rack_no','status','');
        }elseif(Helper::permission('product-bulk-action-delete') && !Helper::permission('product-change-status')){
            $this->column_order = array('','id', 'id','model','name', 'qty','min_qty','max_qty','purchase_price','selling_price','returnable','rack_no','');
        }else{
            $this->column_order = array('id', 'id','model','name', 'qty','min_qty','max_qty','purchase_price','selling_price','returnable','rack_no','');
        }
        $query = DB::table($this->_table_name)
        ->where(['company_id'=>auth()->user()->company_id,'branch_id'=>session()->get('branch')]);

        if (!empty($this->_name)) {
            $query->where('name', 'like','%'.$this->_name.'%');
        }
        if (!empty($this->_model)) {
            $query->where('model', 'like','%'.$this->_model.'%');
        }
        if (!empty($this->_returnable)) {
            $query->where('returnable', $this->_returnable);
        }
        if (!empty($this->_rackNo)) {
            $query->where('rack_no', 'like','%'.$this->_rackNo.'%');
        }
        if (!empty($this->_status)) {
            $query->where('status', $this->_status);
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
                    ->get('branch')])->get()->count();
        return $query;
    }
    /***********************************************
     * ==== End :: DataTable Server Side ==== *
     **********************************************/
}
