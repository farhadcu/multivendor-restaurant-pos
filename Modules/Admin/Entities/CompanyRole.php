<?php

namespace Modules\Admin\Entities;

use Illuminate\Database\Eloquent\Model;
use App\Helpers\Helper;
use DB;

class CompanyRole extends Model
{
    protected $fillable = ['company_id','role'];

    public function user() {
        return $this->belongsToMany('Modules\Admin\Entities\User');
    }

    public function roleModulePermission() {
        return $this->hasMany('Modules\Admin\Entities\CompanyRoleModulePermission');
    }
    public function roleMethodPermission() {
        return $this->hasMany('Modules\Admin\Entities\CompanyRoleMethodPermission');
    }
    /***********************************************
     * ==== Start :: DataTable Server Side ==== *
     **********************************************/
    
    protected $_table_name = 'company_roles'; //set table name
    var $order             = array('id' => 'desc'); //set column order by
    var $column_order;//set data table column sorting key
    //Start :: Custom Property
    private $_companyID;
    private $_roleName;
    private $type;//form 1=System Super Admin and 2=Company Admin

    //End :: Custom Property

    //Start :: Default Property
    private $_orderValue;
    private $_dirValue;
    private $_startValue;
    private $_lengthValue;
    //End :: Default Property

    //Start :: Set custom properties value methods 
    public function setCompanyID($companyID)
    {
        $this->_companyID = $companyID;
    }
    public function setRoleName($roleName)
    {
        $this->_roleName = $roleName;
    }
    public function setType($type)
    {
        $this->type = $type;
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
        if($this->type == 1){
            if(Helper::permission('company-role-bulk-action')){
                $this->column_order = array('','r.id', 'r.company_id','r.role', '');
            }else{
                $this->column_order = array('r.id', 'r.company_id','r.role', '');
            }
        }elseif ($this->type == 2) {
            if(Helper::permission('company-role-bulk-action')){
                $this->column_order = array('','r.id', 'r.role', '');
            }else{
                $this->column_order = array('r.id', 'r.role', '');
            }
        }
        $query = DB::table($this->_table_name.' as r')
        ->select('r.*','c.company_name')->leftjoin('companies as c','r.company_id','=','c.id');

        if ($this->type == 2) {
            $query->where('r.company_id', auth()->user()->company_id);
        }else{
            if (!empty($this->_companyID)) {
                $query->where('r.company_id', $this->_companyID);
            }
        }
        dd(auth()->user()->company_id);
        
        if (!empty($this->_roleName)) {
            $query->where('r.role', 'like','%'.$this->_roleName.'%');
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
        $query = DB::table($this->_table_name);
        if ($this->type == 2) {
            $query->where('company_id', auth()->user()->company_id);
        }
        
        return $query->get()->count();
    }
    /***********************************************
     * ==== End :: DataTable Server Side ==== *
     **********************************************/
}
