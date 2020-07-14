<?php

namespace Modules\Company\Entities;

use Illuminate\Database\Eloquent\Model;

class OrderHasPayment extends Model
{
    protected $fillable = ['company_id', 'branch_id','order_id', 'payment_method', 'card_no',
    'card_cvc', 'card_expire_at', 'mobile_no'];
}
