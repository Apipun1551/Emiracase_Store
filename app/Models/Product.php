<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    //pendefinisian field yang bisa di isi
    protected $fillable = [
        'parent_id',
        'user_id',
        'sku',
        'type',
        'name',
        'slug',
        'price',
        'weight',
        'length',
        'width',
        'height',
        'short_description',
        'description',
        'status',
    ];

    //relasi ke table user
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    //relasi dengan product inventory
    public function productInventory()
    {
        //satu product hanya 1 data di product di inventory (1-1)
        return $this->hasOne('App\Models\ProductInventory');
    }

    //relasi ke table categories dengan penghubungnya adalah product categories
    public function categories()
    {
        return $this->belongsToMany('App\Models\Category','product_categories');
    }
    //relasi ke table product sendiri yang menghubungkan parent_id
    public function variants()
    {
        //product memiliki banyak variant
        return $this->hasMany('App\Models\Product','parent_id');
    }
    //relasi ke table product sendiri yang menghubungkan parent_id
    public function parent()
    {
        return $this->belongsToMany('App\Models\Product','parent_id');
    }
    //relasi ke table product sendiri yang menghubungkan parent_id
    public function productAttributeValues()
    {
        //product memiliki banyak product attribute values
        return $this->hasMany('App\Models\ProductAttributeValues');
    }
    //relasi ke table product image
    public function productImages()
    {
        return $this->hasMany('App\Models\ProductImage');
    }
    //relasi ke table status
    public static function statuses ()
    {
        return [
            // 3 jenis status
            0 => 'draft',
            1 => 'active',
            2 => 'inactive',
        ];
    }
    public static function types()
    {
        return [
            'simple' => 'Simple',
            'configurable' => 'Configurable',
        ];
    }
}
