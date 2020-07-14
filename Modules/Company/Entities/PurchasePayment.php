<?php

namespace Modules\Company\Entities;

use Illuminate\Database\Eloquent\Model;

class PurchasePayment extends Model
{
    protected $fillable = [
        'company_id', 'branch_id', 'purchase_id', 'payment_method','amount'
    ];
}
