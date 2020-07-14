<?php

namespace Modules\Admin\Entities;

use Illuminate\Database\Eloquent\Model;

class CompanySubscription extends Model
{
    protected $fillable = [
        'company_id','type','total_branch_account', 'total_user_account','amount','start_date','end_date',
    ];
}
