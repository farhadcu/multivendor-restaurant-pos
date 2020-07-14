<?php

namespace Modules\Company\Entities;

use Illuminate\Database\Eloquent\Model;

class OrderHasProduct extends Model
{
    protected $fillable = ['company_id', 'branch_id','order_id', 'product_id', 'product_variation_id',
    'qty', 'price', 'discount', 'subtotal'];

    public function product()
    {
        return $this->belongsTo('Modules\Company\Entities\Order');
    }
}
