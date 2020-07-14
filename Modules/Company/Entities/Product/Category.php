<?php

namespace Modules\Company\Entities\Product;

use Illuminate\Database\Eloquent\Model;
use App\Helpers\Helper;
use DB;
use TypiCMS\NestableTrait;

class Category extends Model
{
    use NestableTrait;
    
    protected $fillable = ['company_id', 'branch_id', 'category_name', 'parent_id', 'status'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /***********************************************
     * ==== Start :: DataTable Server Side ==== *
     **********************************************/
    protected $_table_name     = 'categories'; //set table name
    protected $order         = array('id' => 'desc'); //set column order by
    protected $column_order; //set data table column sorting key

    

    //Start :: Custom Property
    private $_categoryName;

    //End :: Custom Property

    //Start :: Default Property
    private $_orderValue;
    private $_dirValue;
    private $_startValue;
    private $_lengthValue;
    //End :: Default Property

    //Start :: Set custom properties value methods 
    public function setCategoryName($categoryName)
    {
        $this->_categoryName = $categoryName;
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
        if(Helper::permission('category-bulk-action-delete')){
            $this->column_order = array('','id', 'category_name', 'parent_id','status','');
        }else{
            $this->column_order = array('id', 'category_name', 'parent_id','status','');
        }
        $query = DB::table($this->_table_name)
        ->where(['company_id'=>auth()->user()->company_id,'branch_id'=>session()->get('branch')]);

        if (!empty($this->_categoryName)) {
            $query->where('category_name', 'like','%'.$this->_categoryName.'%');
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

     public function parent_name($parent_id){
        $parent = self::select('category_name')->where('id',$parent_id)->first();
        if(!empty($parent)){
            return $parent->category_name;
        }else{
            return '<span class="kt-badge kt-badge--danger kt-badge--inline kt-badge--pill kt-badge--rounded">No Parent</span>';
        }
     }
}
