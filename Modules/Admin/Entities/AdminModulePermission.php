<?php

namespace Modules\Admin\Entities;

use Illuminate\Database\Eloquent\Model;
use DB;
class AdminModulePermission extends Model
{
    protected $fillable = ['admin_id','module_id'];

    public static function user_permitted_module($admin_id = NULL, $parent_id = NULL){

        if(!empty($admin_id)){
            $query = DB::table('admin_module_permissions as am')
                        ->select('am.*','m.id','m.module_name','m.module_link','m.module_icon','m.parent_id','m.module_sequence')
                        ->leftjoin('modules as m','am.module_id','=','m.id')
                        ->where('am.admin_id', $admin_id);
            if(empty($parent_id)){    
                $query = $query->where('m.parent_id',0);
            }else{
                $query = $query->where('m.parent_id',$parent_id);
            }
            
            $query = $query->orderBy('m.module_sequence','asc')
                        ->get();
            
        }else{
            $query = DB::table('modules');
            if(empty($parent_id)){    
                $query = $query->where('parent_id',0);
            }else{
                $query = $query->where('parent_id',$parent_id);
            }
            $query = $query->orderBy('module_sequence','asc')
                        ->get();
        }   
            
        
        return  $query;
    }
}
