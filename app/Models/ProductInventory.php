<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductInventory extends Model
{
    //deklarasi field fillable
    protected $fillable= [
        'product_id',
        'qty',
    ];

    //relasi dengan model product
    public function product()
    {
        //
        return $this->belongsTo('App\Models\Product');
    }
}
