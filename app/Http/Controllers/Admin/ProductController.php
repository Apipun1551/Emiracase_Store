<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Http\Request\ProductRequest;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->data['statuses']=Product::statuses(); //karena penggunaannya dibanyak tempat jadi ditaruh di construct
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //Menarik data dari table melalui model berdasarkan urutan asc dengan 10 data perhalaman
        $this->data['products'] = Product::orderBy('name','ASC')->paginate(10);
        //Mengembalikan data ke view index products
        return view ('admin.products.index',$this->data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //Menarik data dari table category untuk dipilih saat menambahkan product
        $categories= Category::orderBy('name','ASC')->get();
        $this->data['categories']= $categories->toArray();//Ditampilkan sebagai array
        $this->data['product']=null; //Saat ini kolom productnya masih kosong
        $this->data['categoryIDs']=null; //Saat ini id kategori productnya masih kosong
        //Mengembalikan tampilan dengan data ke view form
        return view('admin.products.form',$this->data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductRequest $request)
    {
        //
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
