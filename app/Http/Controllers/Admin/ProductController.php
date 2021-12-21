<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductInventory;
use App\Models\Attribute;
use App\Models\Category;
use App\Models\Product;
use App\Models\AttributeOption;
use App\Models\ProductAttributeValue;
use App\Http\Requests\ProductRequest;
use App\Http\Requests\ProductImageRequest;
use App\Models\ProductImage;
use Attribute as GlobalAttribute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;//deklarasi str yang benar
use League\CommonMark\Extension\Attributes\Node\Attributes;



use function PHPSTORM_META\type;

class ProductController extends Controller
{
    public function __construct()
    {
        parent::__construct();//untuk membaca contoller di Controller.php


        $this->data['currentAdminMenu']='catalog';
        $this->data['currentAdminSubMenu']='product';
        $this->data['statuses']=Product::statuses(); //karena penggunaannya dibanyak tempat jadi ditaruh di construct
        $this->data['types']=Product::types();
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
        $configurableAttributes =$this->getConfigurableAttributes();

        $this->data['categories']= $categories->toArray();//Ditampilkan sebagai array
        $this->data['product']=null; //Saat ini kolom productnya masih kosong
        $this->data['productID']=0;
        $this->data['categoryIDs']=null; //Saat ini id kategori productnya masih kosong
        $this->data['configurableAttributes']=$configurableAttributes; //mengembalikan ke view di atas
        //Mengembalikan tampilan dengan data ke view form
        return view('admin.products.form',$this->data);
    }

    //hanya di pakai di beberapa tempat
    private function getConfigurableAttributes()
    {
        //ketika yang di pilih adalah configurable maka dikembalikan ke view di atas
        return Attribute::where('is_configurable',true)->get();
    }

    private function generateAttributeCombinations($arrays)
    {
        //nasted loop untuk mengkombinasikan attribute yang di pilih menjadi varian
        $result = [[]];
        foreach ($arrays as $property => $property_values) {
            $tmp = [];
            foreach ($result as $result_item) {
                foreach ($property_values as $property_value) {
                    $tmp[] = array_merge($result_item, array($property => $property_value));
                }
            }
            $result = $tmp;
        }
        return $result;
    }

    private function convertVariantAsName($variant)
    {
        $variantName = '';

        foreach (array_keys($variant) as $key => $code) {
            $attributeOptionID = $variant[$code];
            $attributeOption = AttributeOption::find($attributeOptionID);

            if ($attributeOption) {
                $variantName .= ' - ' . $attributeOption->name;
            }
        }

        return $variantName;
    }

    private function generateProductVarians($product,$params)
    {
        //pemanggilan configurable attributes
        $configurableAttributes =$this->getConfigurableAttributes();
        //deklarasi variable
        $variantAttributes=[];
        foreach ($configurableAttributes as $attribute){
            $variantAttributes[$attribute->code]=$params[$attribute->code];
        }
        //mengenerate varian dari attribute yang dipilih
        $variants =$this-> generateAttributeCombinations($variantAttributes);

        //menyimpan ke table product
        if ($variants){
            foreach ($variants as $variant){
                $variantParams=[
                    'parent_id'=>$product->id, //reference ke product induk
                    'user_id' =>Auth::user()->id,//dari id user yang login
                    'sku'=>$product->sku.'-'.implode('-', array_values($variant)),//ambil sku dari induk+karakter varian
                    'type' => 'simple',//selalu simpel karena anak product dari product lain
                    'name' => $product->name . $this->convertVariantAsName($variant), //nama dari induk + nama varian
                ];

                $variantParams['slug'] = Str::slug($variantParams['name']);//konversi slug dari name

                $newProductVariant = Product::create($variantParams);//menyimpan ke table product

                $categoryIds = !empty($params['category_ids']) ? $params['category_ids'] : [];//ambil dari yang dipilih oleh user
                $newProductVariant->categories()->sync($categoryIds);//relasi dengan varian awal

                $this->saveProductAttributeValues($newProductVariant, $variant);//Menyimpan informasi attribute dari varian yang di buat
            }
        }
    }
    //meyimpan value dari attribute yang dipilih
    private function saveProductAttributeValues($product, $variant)
    {
        foreach (array_values($variant) as $attributeOptionID) {
            $attributeOption = AttributeOption::find($attributeOptionID);

            $attributeValueParams = [
                'product_id' => $product->id,
                'attribute_id' => $attributeOption->attribute_id,
                'text_value' => $attributeOption->name,
            ];

            ProductAttributeValue::create($attributeValueParams);
         }
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

        //proses penyimpanan product dalam table product
        $product = DB::transaction(function()use ($params){
            $categoryIDs=!empty($params['category_ids']) ? $params['category_ids'] :[];
            $product = Product::create($params); //Menyimpan data yang di tambah
            $product->categories()->sync($categoryIDs); //relasikan dengan kategori yang dipilih
            //jika type yang di pilih adalah configurable
            if ($params['type'] == 'configurable'){
                $this->generateProductVarians($product,$params);//generete variant product
            }
            return $product;
        });

        if ($product) { //saved true
            Session::flash('success','Produk telah disimpan');
        }else { //saved false
            Session::flash('error','Produk tidak berhasil disimpan');
        }

        return redirect('admin/products/'.$product->id.'/edit/'); //kembali ke halaman admin product berdasarkan id
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
            $categoryIDs=!empty($params['category_ids']) ? $params['category_ids'] :[];
            $product->update($params);
            $product->categories()->sync($categoryIDs);//menyingkronkan category berdasarkan id
            //cek simple atau configurable
            if ($product->type == 'configurable') {
                $this->updateProductVariants($params);//perlu tambahan 1 parameter
            } else {
                ProductInventory::updateOrCreate(['product_id' => $product->id], ['qty' => $params['qty']]);//langsung simpan
            }

            return true;
        });
        if ($saved) { //saved true
            Session::flash('success','Produk telah disimpan');
        }else { //saved false
            Session::flash('error','Produk tidak berhasil disimpan');
        }

        return redirect('admin/products'); //kembali ke halaman admin product
    }

    private function updateProductVariants($params)
    {
        if ($params['variants']) {
            //menyimpan semua varian yang ada
            foreach ($params['variants'] as $productParams) {
                $product = Product::find($productParams['id']);
                $product->update($productParams);

                $product->status = $params['status'];
                $product->save();

                ProductInventory::updateOrCreate(['product_id' => $product->id], ['qty' => $productParams['qty']]);
            }
        }
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
