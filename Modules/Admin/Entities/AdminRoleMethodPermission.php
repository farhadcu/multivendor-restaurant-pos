<?php

namespace Modules\Admin\Entities;

use Illuminate\Database\Eloquent\Model;

class AdminRoleMethodPermission extends Model
{
    protected $fillable = ['role_id','method_id'];

    public function method() {
        return $this->belongsToMany('Modules\Admin\Entities\Method');
    }
}
