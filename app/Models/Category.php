<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable=['name','slug','parent_id'];

    //relasi untuk multi level categori

    public function childs(){
        return $this->hasMany('App\Models\Category','parent_id');
    }

    public function parent(){
        return $this->belongsTo('App\Models\Category','parent_id');
    }

    //relasi 1-N dengan product melalui product categories
    public function product(){
        return $this->belongsToMany('App\Models\Product','product_categories');
    }
}
