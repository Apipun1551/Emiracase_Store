<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductAttributeValue extends Model
{
    //field fillable
    protected $fillable= [
        'product_id',
        'attribute_id',
        'text_value',
        'boolean_value',
        'integer_value',
        'float_value',
        'datetime_value',
        'date_value',
        'json_value',
    ];

    //relasi dengan model product
    public function product()
    {
        return $this->belongsTo('App\Models\Product');
    }
}
