<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    //definisi field yang dapat di isi
    protected $fillable = [
        'code',
        'name',
        'type',
        'validation',
        'is_required',
        'is_unique',
        'is_filterable',
        'is_configurable',
    ];

    //Mendefinisikan tipe attribute
    public static function types()
    {
        return [
            'text' => 'Text',
            'textarea' => 'Textarea',
            'price' => 'Price',
            'boolean' => 'Boolean',
            'select' => 'Select',
            'datetime' => 'Datetime',
            'date' => 'Date',
        ];
    }

    //mendefinisikan boolean option
    public static function booleanOptions()
    {
        return [
            1 => 'Yes',
            0 => 'No',
        ];
    }

    //mendefinisikan validasi
    public static function validations()
    {
        return [
            'number' => 'Number',
            'decimal' => 'Decimal',
            'email' => 'Email',
            'url' => 'URL',
        ];
    }

    //relasi model atribut dengan atribute option
    public function attributeOptions()
    {
        return $this->hasMany('App\Models\AttributeOption');
    }
}
