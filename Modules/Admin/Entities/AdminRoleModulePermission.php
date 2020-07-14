<?php

namespace Modules\Admin\Entities;

use Illuminate\Database\Eloquent\Model;

class AdminRoleModulePermission extends Model
{
    protected $fillable = ['role_id','module_id'];

    public function module() {
        return $this->belongsToMany('Modules\Admin\Entities\Module');
    }
}
