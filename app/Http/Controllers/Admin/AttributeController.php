<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use Illuminate\Http\Request;
use App\Models\AttributeOption;

class AttributeController extends Controller
{
    public function __construct()
    {
        $this->data['types']= Attribute::types();
        $this->data['booleanOptions']= Attribute::booleanOptions();
        $this->data['validations']= Attribute::validations();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //Menampilkan index attribute
        $this->data['attributes']=Attribute::orderBy('name','ASC')->paginate(10);
        return view ('admin.attributes.index',$this->data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $this->data['attribute']=null;

        return view('admin.attributes.form',$this->data);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AttributeRequest $request)
    {
        //
        $params = $request->except('_token');
        $params ['is_required']=(bool)$params['is_required'];
        $params ['is_unique']=(bool)$params['is_unique'];
        $params ['is_configurable']=(bool)$params['is_configurable'];
        $params ['is_filterable']=(bool)$params['is_filterable'];

        if (Attribute::create($params))
        {
            Session:flash('success','Attribute berhasil disimpan');
        }

        return redirect('admin/attributes');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
