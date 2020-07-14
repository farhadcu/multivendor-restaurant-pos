<?php

namespace Modules\Admin\Entities;

use Illuminate\Database\Eloquent\Model;

class CompanySubscriptionPayment extends Model
{
    protected $fillable = [
        'company_id','company_subscription_id','paid_amount','payment_type','payment_date','payment_history'
    ];
}
