<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductAttributeValue;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $this->data['products']=Product::active()->paginate(9); //Memanggil product status active 9 perhalaman

        return $this->load_theme('products.index',$this->data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

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
            $this->data['bahan'] = ProductAttributeValue::getAttributeOptions($product, 'warna')->pluck('text_value', 'text_value');
            $this->data['warna'] = ProductAttributeValue::getAttributeOptions($product, 'bahan')->pluck('text_value', 'text_value');
        }
        $this->data['product']=$product;

        return $this->load_theme('products.show',$this->data);
    }

}
