<?php

namespace Modules\Company\Entities\Product;

use Illuminate\Database\Eloquent\Model;

class ProductHasVariation extends Model
{
    protected $fillable = [
        'product_id', 'variation_name', 'variation_model', 'variation_qty', 'variation_weight', 'variation_price', 'price_prefix'
    ];

    public function product()
    {
        return $this->belongsTo('Modules\Company\Entities\Product\Product');
    }
}
