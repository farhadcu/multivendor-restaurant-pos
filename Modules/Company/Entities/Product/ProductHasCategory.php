<?php

namespace Modules\Company\Entities\Product;

use Illuminate\Database\Eloquent\Model;

class ProductHasCategory extends Model
{
    protected $fillable = ['product_id', 'category_id'];

    public function product()
    {
        return $this->belongsTo('Modules\Company\Entities\Product\Product');
    }
}
