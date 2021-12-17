<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttributeOption extends Model
{
    //pendefinisian field fillable
    protected $fillable = ['attribute_id','name'];

    //relasi dengan model attribute
    public function attribute()
    {
        return $this->belongsTo('App\Models\Attribute');
    }
}
