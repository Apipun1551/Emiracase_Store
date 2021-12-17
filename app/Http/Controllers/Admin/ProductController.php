<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Http\Requests\ProductRequest;
use App\Http\Requests\ProductImageRequest;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;//deklarasi str yang benar
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
        return view('admin.products.index',$this->data);
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
        $this->data['productID']=0;
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
    public function store(ProductRequest $request) //setiap request memiliki file di folder equest
    {
        //Menangkap value dari form product dengan kecuali token
        $params = $request->except('_token');
        //Mengkonversi slug dari name
        $params['slug']=Str::slug($params['name']);
        //Id user saat login sebagai user id
        $params['user_id']=Auth::user()->id;

        //proses penyimpanan
        $saved = false; //inisialisasi
        //proses penyimpanan product dalam table product
        $saved = DB::transaction(function()use ($params){
            $product = Product::create($params); //Menyimpan data yang di tambah
            $product->categories()->sync($params['category_ids']); //relasikan dengan kategori yang dipilih

            return true;
        });

        if ($saved) { //saved true
            Session::flash('success','Produk telah disimpan');
        }else { //saved false
            Session::flash('error','Produk tidak berhasil disimpan');
        }

        return redirect('admin/products'); //kembali ke halaman admin product
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
        if (empty($id)){
            return redirect('admin/products/create');
        }
        //Fungsi tombol edit

        $product = Product::findOrFail($id);//cari product dari id
        $categories = Category::orderBy('name','ASC')->get();//cari categories diurutkan ASC nama

        //definisi variable
        $this->data ['categories'] = $categories->toArray(); //menampilkan categories dalam bentuk array
        $this->data ['product'] = $product; //menampilkan data product
        $this->data ['productID']= $product->id;//mendefinisikan variabel productId untuk form
        //memanggil categori terakhir dipilih
        $this->data ['categoryIDs'] = $product->categories->pluck('id')->toArray(); //objek product dengan relasi categories dan ambil idnya
        //kembali ke form product
        return view('admin.products.form',$this->data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ProductRequest $request, $id)
    {
        //Menyimpan isi form kecuali token
        $params=$request->except('_token');
        $params['slug']=Str::slug($params['name']);//mengambil slug dari konversi name

        $product = Product::findOrFail($id); //mengambil product dari id

        $saved= false;//belum di simpan
        //menyimpan produk dari update
        $saved = DB::transaction(function () use ($product,$params) {
            $product->update($params);
            $product->categories()->sync($params['category_ids']);//menyingkronkan category berdasarkan id

            return true;
        });
        if ($saved) { //saved true
            Session::flash('success','Produk telah disimpan');
        }else { //saved false
            Session::flash('error','Produk tidak berhasil disimpan');
        }

        return redirect('admin/products'); //kembali ke halaman admin product
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
        $product = Product::findOrFail($id);
        if($product->delete()){
           Session::flash('success', 'Product telah di hapus');
        }
        return redirect('admin/products');
    }

    public function images($id)
    {
        if (empty($id)){
            return redirect('admin/products/create');
        }

        $product = Product::findOrFail($id);

        $this->data['productID']=$product->id;
        $this->data['productImages'] = $product->productImages;

        return view('admin.products.images',$this->data);
    }

    public function add_image($id)
    {
        if (empty($id)){
            return redirect('admin/products');
        }
        $product = Product::findOrFail($id);

        $this->data['productID'] =$product->id;
        $this->data['product']=$product;

        return view('admin.products.image_form',$this->data);
    }

    public function upload_image(ProductImageRequest $request, $id)
    {
        $product = Product::findOrFail($id);

        if ($request->has('image')) {
			$image = $request->file('image');
			$name = $product->slug . '_' . time();
			$fileName = $name . '.' . $image->getClientOriginalExtension();

			$folder = '/uploads/images';

			$filePath = $image->storeAs($folder, $fileName, 'public');

			$params =[
				'product_id' => $product->id,
				'path' => $filePath,
			];

			if (ProductImage::create($params)) {
				Session::flash('success', 'Gambar berhasil diupload');
			} else {
				Session::flash('error', 'Gambar tidak berhasil diupload');
			}

			return redirect('admin/products/' . $id . '/images');
		}
    }

    public function remove_image($id)
    {
        $image = ProductImage::findOrFail($id);

        if($image->delete()){
            Session::flash('success','Gambar berhasil dihapus');
        }

        return redirect('admin/products/'.$image->product->id.'/images');
    }
}
