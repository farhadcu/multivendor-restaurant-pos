<?php 
namespace App\Helpers;

use Route;
use Modules\Admin\Entities\AdminModulePermission;
use Modules\Admin\Entities\UserModulePermission;

class Helper
{
    public static function admin_menu()
    {
        $menu = '<ul class="kt-menu__nav " id="sidemenu">';
        $menu .= self::admin_multilevel_menu();
        $menu .= '</ul>';
        return $menu;
    }
    public static function admin_multilevel_menu($parent_id = NULL)
    {
        $module         = ''; //initialized return module data
        $current_url    = url()->current();

        //to check the auth person is superadmin or not
        if(auth()->guard('admin')->user()->role_id == 1){
            $user_id = ''; //for superadmin
        }else{
            $user_id = auth()->guard('admin')->user()->id; //for other user
        }
        // dd($user_id);
        if($parent_id == 0){
            //get module list whose parent id 0
            $modules = AdminModulePermission::user_permitted_module($user_id, $parent_id); 
        }else{
            //get module list whose parent id is the given id
            $modules = AdminModulePermission::user_permitted_module($user_id, $parent_id);  

        }
        if(!empty($modules)){
            foreach ($modules as $value) { 
                //to check ths module has child or not
                $sub_modules = AdminModulePermission::user_permitted_module($user_id, $value->id); 
                
                //to check current route and module link is similar or not
                if($current_url == url('admin/'.$value->module_link)){
                    $menu_active = 'kt-menu__item--active'; //list li will show active
                }else{
                    $menu_active = '';
                }

                if(!empty($sub_modules) && count($sub_modules) > 0){
                    //if module have submodule then this submodule list append to parent module list
                    $submodule    = '<div class="kt-menu__submenu ">
                                        <span class="kt-menu__arrow"></span>
                                        <ul class="kt-menu__subnav">'.self::admin_multilevel_menu($value->id).'</ul>
                                    </div>';

                    //added in parent module li tag if have submenu
                    $submodule_have = 'kt-menu__item--submenu'; 
                    $toggle_hover   = 'data-ktmenu-submenu-toggle="hover"';
                    $toggele        = 'kt-menu__toggle';
                    $angle_right    = '<i class="kt-menu__ver-arrow la la-angle-right font-weight-bold"></i>';
                }else{
                    //if module have no submodule
                    $submodule      = self::admin_multilevel_menu($value->id); 
                    $submodule_have = $toggle_hover = $toggele = $angle_right = '';
                }

                //menu li appending start
                $module .= '<li class="kt-menu__item '.$submodule_have.' '.$menu_active.'" aria-haspopup="true" '.$toggle_hover.'>
                                <a href="'.url('admin/'.$value->module_link).'" class="kt-menu__link '.$toggele.'">
                                <i class="kt-menu__link-icon '.$value->module_icon.'"></i>
                                <span class="kt-menu__link-text">'.$value->module_name.'</span>'.$angle_right.'
                                </a>';
                $module .= $submodule; //for submodule if have
                $module .= '</li>'; 
            }
        }
        return $module;
    }
    public static function permission($method_slug)
    {
        
        if(in_array($method_slug,session()->get('permission'))){       
            return true;
        }
        return false;
    }


    public static function company_menu()
    {
        $menu = '<ul class="kt-menu__nav " id="sidemenu">';
        $menu .= self::company_multilevel_menu();
        $menu .= '</ul>';
        return $menu;
    }
    public static function company_multilevel_menu($parent_id = NULL)
    {
        $module         = ''; //initialized return module data
        $current_url    = url()->current();

        $user_id = auth()->user()->id;
        
        // dd($user_id);
        if($parent_id == 0){
            //get module list whose parent id 0
            $modules = UserModulePermission::user_permitted_module($user_id, $parent_id); 
        }else{
            //get module list whose parent id is the given id
            $modules = UserModulePermission::user_permitted_module($user_id, $parent_id);  

        }
        if(!empty($modules)){
            foreach ($modules as $value) { 
                //to check ths module has child or not
                $sub_modules = UserModulePermission::user_permitted_module($user_id, $value->id); 
                
                //to check current route and module link is similar or not
                if($current_url == url($value->module_link)){
                    $menu_active = 'kt-menu__item--active'; //list li will show active
                }else{
                    $menu_active = '';
                }

                if(!empty($sub_modules) && count($sub_modules) > 0){
                    //if module have submodule then this submodule list append to parent module list
                    $submodule    = '<div class="kt-menu__submenu ">
                                        <span class="kt-menu__arrow"></span>
                                        <ul class="kt-menu__subnav">'.self::company_multilevel_menu($value->id).'</ul>
                                    </div>';

                    //added in parent module li tag if have submenu
                    $submodule_have = 'kt-menu__item--submenu'; 
                    $toggle_hover   = 'data-ktmenu-submenu-toggle="hover"';
                    $toggele        = 'kt-menu__toggle';
                    $angle_right    = '<i class="kt-menu__ver-arrow la la-angle-right font-weight-bold"></i>';
                }else{
                    //if module have no submodule
                    $submodule      = self::company_multilevel_menu($value->id); 
                    $submodule_have = $toggle_hover = $toggele = $angle_right = '';
                }

                //menu li appending start
                $module .= '<li class="kt-menu__item '.$submodule_have.' '.$menu_active.'" aria-haspopup="true" '.$toggle_hover.'>
                                <a href="'.url($value->module_link).'" class="kt-menu__link '.$toggele.'">
                                <i class="kt-menu__link-icon '.$value->module_icon.'"></i>
                                <span class="kt-menu__link-text">'.$value->module_name.'</span>'.$angle_right.'
                                </a>';
                $module .= $submodule; //for submodule if have
                $module .= '</li>'; 
            }
        }
        return $module;
    }

    public static function readMore($text, $limit = 400){
        $text = $text." ";
        $text = substr($text, 0, $limit);
        $text = substr($text, 0, strrpos($text, ' '));
        $text = $text."...";
        return $text;
    
    }

    // public static function company_permission($method_slug)
    // {
        
    //     if(in_array($method_slug,session()->get('company_permission'))){       
    //         return true;
    //     }
    //     return false;
    // }


}