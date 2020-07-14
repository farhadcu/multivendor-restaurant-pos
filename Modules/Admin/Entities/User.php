<?php

namespace Modules\Admin\Entities;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Helpers\Helper;
use DB;

class User extends Authenticatable
{
    use Notifiable;
    protected $fillable = [
        'company_id','branch_id','role_id', 'name', 'email', 'mobile', 'gender', 'photo', 'address','password', 'status',
         'last_login_at', 'last_login_ip', 'history', 'created_at', 'updated_at'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function company() {
        return $this->belongsTo('Modules\Admin\Entities\Company');
    }
    public function branch() {
        return $this->belongsTo('Modules\Admin\Entities\Branch');
    }
    public function role() {
        return $this->belongsTo('Modules\Admin\Entities\CompanyRole');
    }


    /*********DataTable Server Side Begin************/
    protected $table_name   = 'users';
    var $order              = array('u.id' => 'DESC');
    var $column_order;

    private $name;
    private $company_id;
    private $branch_id;
    private $role_id;
    private $email;
    private $mobile;


    private $type;//form 1=System Super Admin and 2=Company Admin

    private $_orderValue;
    private $_dirValue;
    private $_startValue;
    private $_lengthValue;


    public function setName($name){  $this->name = $name; }
    public function setCompany($company_id){ $this->company_id = $company_id;}
    public function setBranch($branch_id){ $this->branch_id = $branch_id;}
    public function setRole($role_id){ $this->role_id = $role_id;}
    public function setEmail($email){ $this->email = $email;}
    public function setMobile($mobile){ $this->mobile = $mobile;}



    public function setType($type){ $this->type = $type;}

    //set datatable eliments
    public function setOrderValue($orderValue){ $this->_orderValue = $orderValue;}
    public function setDirValue($dirValue){ $this->_dirValue = $dirValue;}
    public function setLengthValue($lengthValue){ $this->_lengthValue = $lengthValue;}
    public function setStartValue($startValue){ $this->_startValue = $startValue;}


    private function _get_datatables_query()
    {
        if($this->type == 1){
            if(Helper::permission('company-user-bulk-action-delete') && Helper::permission('company-user-change-status')){
                $this->column_order = array('u.id', 'u.id','u.id', 'u.name', 'u.email','u.mobile','u.company_id','u.branch_id','u.role_id','u.last_login_at','u.last_login_ip','u.status','');
            }elseif (!Helper::permission('company-user-bulk-action-delete') && Helper::permission('company-user-change-status')) {
                $this->column_order = array('u.id','u.id', 'u.name', 'u.email','u.mobile','u.company_id','u.branch_id','u.role_id','u.last_login_at','u.last_login_ip','u.status','');
            }elseif (Helper::permission('company-user-bulk-action-delete') && !Helper::permission('company-user-change-status')) {
                $this->column_order = array('u.id', 'u.id','u.id', 'u.name', 'u.email','u.mobile','u.company_id','u.branch_id','u.role_id','u.last_login_at','u.last_login_ip','');
            }else{
                $this->column_order = array('u.id','u.id', 'u.name', 'u.email','u.mobile','u.company_id','u.branch_id','u.role_id','u.last_login_at','u.last_login_ip','');
            }
        }elseif ($this->type == 2) {
            if(Helper::permission('user-bulk-action-delete') && Helper::permission('user-change-status')){
                $this->column_order = array('u.id', 'u.id','u.id', 'u.name', 'u.email','u.mobile','u.role_id','u.last_login_at','u.last_login_ip','u.status','');
            }elseif (!Helper::permission('user-bulk-action-delete') && Helper::permission('user-change-status')) {
                $this->column_order = array('u.id','u.id', 'u.name', 'u.email','u.mobile','u.role_id','u.last_login_at','u.last_login_ip','u.status','');
            }elseif (Helper::permission('user-bulk-action-delete') && !Helper::permission('user-change-status')) {
                $this->column_order = array('u.id', 'u.id','u.id', 'u.name', 'u.email','u.mobile','u.role_id','u.last_login_at','u.last_login_ip','');
            }else{
                $this->column_order = array('u.id','u.id', 'u.name', 'u.email','u.mobile','u.role_id','u.last_login_at','u.last_login_ip','');
            }
        }
        

        $query = DB::table($this->table_name.' as u')
        ->select('u.*','c.company_name','b.branch_name','r.role')
        ->leftjoin('company_roles as r','u.role_id','=','r.id')
        ->leftjoin('companies as c','u.company_id','=','c.id')
        ->leftjoin('branches as b','u.branch_id','=','b.id');

        if (!empty($this->company_id)) {
            $query->where('u.company_id',$this->company_id);
        }
        if (!empty($this->branch_id)) {
            $query->where('u.branch_id',$this->branch_id);
        }
        
        if (!empty($this->name)) {
            $query->where('u.name', 'like','%'.$this->name.'%');
        }
        
        if (!empty($this->email)) {
            $query->where('u.email', 'like','%'.$this->email.'%');
        }
        if (!empty($this->mobile)) {
            $query->where('u.mobile', 'like','%'.$this->mobile.'%');
        }
        

        if (isset($this->_orderValue) && isset($this->_dirValue)) // here order processing
        {
            $query->orderBy($this->column_order[$this->_orderValue], $this->_dirValue);

        } else if (isset($this->order)) {

            $order = $this->order;
            $query->orderBy(key($order), $order[key($order)]);
        }

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
        $query = DB::table($this->table_name.' as u')
        ->select('u.*','c.company_name','b.branch_name','r.role')
        ->leftjoin('company_roles as r','u.role_id','=','r.id')
        ->leftjoin('companies as c','u.company_id','=','c.id')
        ->leftjoin('branches as b','u.branch_id','=','b.id');

        if (!empty($this->company_id)) {
            $query->where('u.company_id',$this->company_id);
        }
        if (!empty($this->branch_id)) {
            $query->where('u.branch_id',$this->branch_id);
        }
        return $query->get()->count();
    }
    /*********DataTable Server Side End************/
}
