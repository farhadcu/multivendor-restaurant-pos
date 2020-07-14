<?php

namespace Modules\Company\Entities\Product;

use Illuminate\Database\Eloquent\Model;

class ProductHasDiscount extends Model
{
    protected $fillable = [
        'product_id', 'discount_qty', 'discount_amount', 'start_date', 'end_date'
    ];

    public function product()
    {
        return $this->belongsTo('Modules\Company\Entities\Product\Product');
    }
}
