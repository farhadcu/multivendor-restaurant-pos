<?php

namespace Modules\Admin\Entities;

use Illuminate\Database\Eloquent\Model;

class AdminMethodPermission extends Model
{
    protected $fillable = ['admin_id','method_id'];
}
