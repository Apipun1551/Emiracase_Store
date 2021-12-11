<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Http\Requests\CategoryRequest;
use Str;
use Session;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //menampilkan view
        $this->data['categories']=Category::orderBy('name','ASC')->paginate(10);
        return view('admin.categories.index',$this->data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //untuk menambahkan data
        return view('admin.categories.form', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CategoryRequest $request)
    {
        //Menyimpan Input kecuali token
        $params = $request->except('_token');
        $params['slug'] = Str::slug($params['name']);
        $params['parent_id']= 0;

        if (Category::create($params)) {
            Session::flash('success', 'Kategori telah di tambah');
        }
        return redirect('admin\categories');
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
        //Menampilkan form category yang ingin di edit
        $category = Category::findOrFail($id);
        $this->data['category']=$category;
        return view('admin.categories.form',$this->data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CategoryRequest $request, $id)
    {
        //
        $params = $request->except('_token');
        $params['slug']=Str::slug($params['name']);
        $category=Category::findOrFail($id);
        if($category->update($params)){
            $request->session()->flash('success', 'Kategori telah di edit');
        }
        return redirect('admin/categories');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //Menghapus data
        $category = Category::findOrFail($id);
        if($category->delete()){
           Session::flash('success', 'Kategori telah di hapus');
        }
        return redirect('admin/categories');
    }
}
