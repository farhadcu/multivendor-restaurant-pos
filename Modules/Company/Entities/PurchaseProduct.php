<?php

namespace Modules\Company\Entities;

use Illuminate\Database\Eloquent\Model;

class PurchaseProduct extends Model
{
    protected $fillable = [
        'purchase_id', 'product_id', 'product_variation_id', 'qty', 
        'recieved', 'purchase_unit', 'net_unit_cost', 'total'
    ];
}
