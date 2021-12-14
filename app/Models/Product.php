<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    //pendefinisian field yang bisa di isi
    protected $fillable = [
        'user_id',
        'sku',
        'name',
        'slug',
        'price',
        'weight',
        'lenght',
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

    //relasi ke table categories dengan penghubungnya adalah product categories
    public function categories()
    {
        return $this->belongsToMany('App\Models\Category','product_categories');
    }
    //relasi ke table status
    public function statuses ()
    {
        return [
            // 3 jenis status
            0 => 'draft',
            1 => 'active',
            2 => 'inactive',
        ];
    }
}
