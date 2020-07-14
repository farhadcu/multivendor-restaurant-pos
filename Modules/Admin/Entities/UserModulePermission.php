<?php

namespace Modules\Admin\Entities;

use Illuminate\Database\Eloquent\Model;
use DB;
class UserModulePermission extends Model
{
    protected $fillable = ['company_id','user_id','module_id'];

    public static function user_permitted_module($user_id = NULL, $parent_id = NULL){

        $query = '';
        if(!empty($user_id)){
            $query = DB::table('user_module_permissions as um')
                        ->select('um.*','m.id','m.module_name','m.module_link','m.module_icon','m.parent_id','m.module_sequence')
                        ->leftjoin('company_modules as m','um.module_id','=','m.id')
                        ->where(['um.company_id'=>auth()->user()->company_id,'um.user_id'=> $user_id,'m.status'=>1]);
            if(empty($parent_id)){    
                $query = $query->where('m.parent_id',0);
            }else{
                $query = $query->where('m.parent_id',$parent_id);
            }
            
            $query = $query->orderBy('m.module_sequence','asc')
                        ->get();
            
            
        }
        // else{
        //     $query = DB::table('company_modules')->where('status',1);
        //     if(empty($parent_id)){    
        //         $query = $query->where('parent_id',0);
        //     }else{
        //         $query = $query->where('parent_id',$parent_id);
        //     }
        //     $query = $query->orderBy('module_sequence','asc')
        //                 ->get();
        // }   
            
        return  $query;
        
    }
}
