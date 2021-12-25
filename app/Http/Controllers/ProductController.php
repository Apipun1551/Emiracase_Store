<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductAttributeValue;
use App\Models\AttributeOption;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        //mendeklarasikan nilai q dari header
        $this->data['q']=null;
        //Memunculkan kategori dari database
        $this->data['categories'] = Category::parentCategories()
                                    ->orderBy('name', 'asc')
                                    ->get();

        $this->data['minPrice'] = Product::min('price');
        $this->data['maxPrice'] = Product::max('price');

        $this->data['bahans'] = AttributeOption::whereHas('attribute', function ($query) {
                                            $query->where('code', 'bahan')
                                                ->where('is_filterable', 1);
                                })->orderBy('name', 'asc')->get();

        $this->data['warnas'] = AttributeOption::whereHas('attribute', function ($query) {
									$query->where('code', 'warna')
										->where('is_filterable', 1);
                                })->orderBy('name', 'asc')->get();

        //MEMATIKAN FUNGSI SORT
        /*$this->data['sorts'] = [
            url('products') => 'Default',
            url('products?sort=price-asc') => 'Price - Low to High',
            url('products?sort=price-desc') => 'Price - High to Low',
            url('products?sort=created_at-desc') => 'Newest to Oldest',
            url('products?sort=created_at-asc') => 'Oldest to Newest',
        ];*/

        //$this->data['selectedSort'] = url('products');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $products=Product::active();

        $products=$this->searchProducts($products, $request);
        $product=$this->filterProductsByPriceRange($products,$request);
        $products=$this->filterProductsByAttribute($products, $request);
        //$product=$this->sortProducts($products,$request);
        //deklarasi nilai

        //Fungsi sort


        $this->data['products'] = $products->paginate(9); //Memanggil product status active 9 perhalaman
        return $this->load_theme('products.index',$this->data);
    }

    private function searchProducts($products, $request)
    {
        //menangkap imputan user untuk query
        if ($q = $request->query('q')) {
            $q = str_replace('-', ' ', Str::slug($q));

            $products = $products->whereRaw('MATCH(name, slug, short_description, description) AGAINST (? IN NATURAL LANGUAGE MODE)', [$q]);

            $this->data['q'] = $q;
        }
        //untuk memunculkan product dengan category yang di klik di sidebar user
        if ($categorySlug = $request->query('category')) {
            $category = Category::where('slug', $categorySlug)->firstOrFail();//jika tidak ditemukan maka 404
            //untuk mendapatkan id anak category
            $childIds = Category::childIds($category->id);
            //untuk memunculkan semua category (parent dan child)
            $categoryIds = array_merge([$category->id], $childIds);
            //menarik produk dengan category di klik
            $products = $products->whereHas('categories', function ($query) use ($categoryIds) {
                            $query->whereIn('categories.id', $categoryIds);
            });
        }
        return $products;
    }

    private function filterProductsByPriceRange($products,$request)
    {
        $lowPrice = null;
        $highPrice = null;

        if ($priceSlider = $request->query('price')) {
            $prices = explode('-', $priceSlider);

            $lowPrice = !empty($prices[0]) ? (float)$prices[0] : $this->data['minPrice'];//jika tidak ada filter low maka harga minimal barang yang ada
            $highPrice = !empty($prices[1]) ? (float)$prices[1] : $this->data['maxPrice'];//jika tidak ada filter max maka harga maksimal barang yang ada

            if ($lowPrice && $highPrice) {
                $products = $products->where('price', '>=', $lowPrice)
                                ->where('price', '<=', $highPrice)
                                //pengecekan yang varian untuk configurable
                                ->orWhereHas('variants', function ($query) use ($lowPrice, $highPrice) {
                                    $query->where('price', '>=', $lowPrice)
                                        ->where('price', '<=', $highPrice);
                                });

                //mengembalikan nilai untuk filter harga
                $this->data['minPrice'] = $lowPrice;
                $this->data['maxPrice'] = $highPrice;
            }
        }
        return $products;
    }
    private function filterProductsByAttribute($products,$request)
    {
        if ($attributeOptionID = $request->query('option')) {
            $attributeOption = AttributeOption::findOrFail($attributeOptionID);

            //menarik produk yang memiliki product atribute tertentu
            $products = $products->whereHas('ProductAttributeValues', function ($query) use ($attributeOption) {
                                    $query->where('attribute_id', $attributeOption->attribute_id)
                                        ->where('text_value', $attributeOption->name);
            });
        }
        return $products;
    }
    //DIMATIKAN KARENA TIDAK BISA JALAN YANG SORT
    /*private function sortProducts($products,$request)
    {
        if ($sort = preg_replace('/\s+/', '',$request->query('sort'))) {
            $availableSorts = ['price', 'created_at'];
            $availableOrder = ['asc', 'desc'];
            $sortAndOrder = explode('-', $sort);

            $sortBy = strtolower($sortAndOrder[0]);
            $orderBy = strtolower($sortAndOrder[1]);

            if (in_array($sortBy, $availableSorts) && in_array($orderBy, $availableOrder)) {
                $products = $products->orderBy($sortBy, $orderBy);
            }

            $this->data['selectedSort'] = url('products?sort='. $sort);
        }
    }*/
    /**
     * Display the specified resource.
     *
     * @param  string slug
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {
        //
        $product= Product::active()->where('slug',$slug)->first();

        if(!$product){
            return redirect('products');
        }

        if($product->type == 'configurable'){
            $this->data['bahans'] = ProductAttributeValue::getAttributeOptions($product, 'bahan')->pluck('text_value', 'text_value');
            $this->data['warnas'] = ProductAttributeValue::getAttributeOptions($product, 'warna')->pluck('text_value', 'text_value');
        }
        $this->data['product']=$product;

        return $this->load_theme('products.show',$this->data);
    }

}
