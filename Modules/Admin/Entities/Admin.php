<?php

namespace Modules\Admin\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Helpers\Helper;
use DB;
class Admin extends Authenticatable
{
    use Notifiable;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'role_id','name','email', 'mobile','gender','photo','password','address',
        'status','last_login_at','last_login_ip'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function role() {
        return $this->belongsTo('App\Models\Admin\Role');
    }

     /*********DataTable Server Side Begin************/
     protected $table_name      = 'admins';
     protected $_primary_key    = 'id';
     protected $_primary_filter = 'intval';
     protected $_order_by       = "id desc";
 
     var $column_order;
     var $column_search = array('u.name','u.email','u.mobile','u.gender','u.role_name');
     var $order         = array('u.id' => 'DESC');
 
     private $name;
     private $email;
     private $mobile;
     private $role_id;
     private $status;
 
     private $_searchValue;
     private $_orderValue;
     private $_dirValue;
     private $_startValue;
     private $_lengthValue;
 
 
     public function setName($name)
     {
         $this->name = $name;
     }
     public function setEmail($email)
     {
         $this->email = $email;
     }
     public function setMobile($mobile)
     {
         $this->mobile = $mobile;
     }
     public function setRole($role_id)
     {
         $this->role_id = $role_id;
     }
     public function setStatus($status)
     {
         $this->status = $status;
     }
 
     //set datatable eliments
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
 
 
     private function _get_datatables_query()
     {
         if(Helper::permission('user-bulk-action')){
             $this->column_order = array('u.id', 'u.id', 'u.name', 'u.email','u.mobile','u.status','');
         }else{
             $this->column_order = array('u.id', 'u.name', 'u.email','u.mobile','u.status','');
         }
 
         $query = DB::table($this->table_name.' as u')
         ->select('u.*','r.role')
         ->leftjoin('roles as r','u.role_id','=','r.id')
         ->where('u.id','!=',1);
 
 
         if (!empty($this->name)) {
             $query->where('u.name', 'like','%'.$this->name.'%');
         }
         if (!empty($this->email)) {
             $query->where('u.email', 'like','%'.$this->email.'%');
         }
         if (!empty($this->mobile)) {
             $query->where('u.mobile', $this->mobile);
         }
         if (!empty($this->role_id)) {
             $query->where('u.role_id', $this->role_id);
         }
         if (!empty($this->gender)) {
             $query->where('u.gender', $this->gender);
         }
         if (!empty($this->status)) {
             $query->where('u.status', $this->status);
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
         $query = DB::table($this->table_name)->select('*')->where('id','!=',1)->get();
         return $query->count();
     }
     /*********DataTable Server Side End************/
 
     public function user_data_by_id(int $id){
         return  DB::table($this->table_name.' as u')
                 ->select('u.*','r.role','c.name as creator_name','m.name as modifier_name')
                 ->leftjoin('roles as r','u.role_id','=','r.id')
                 ->leftjoin('admins as c','u.creator_id','=','c.id')
                 ->leftjoin('admins as m','u.modifier_id','=','m.id')
                 ->where('u.id',$id)
                 ->first();
     }
}
