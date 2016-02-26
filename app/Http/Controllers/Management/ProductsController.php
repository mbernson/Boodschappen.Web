<?php

namespace Boodschappen\Http\Controllers\Management;

use Boodschappen\Database\Product;
use Illuminate\Http\Request;

use Boodschappen\Http\Requests;
use Boodschappen\Http\Controllers\Controller;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $cols = ['id', 'title', 'brand', 'unit_size', 'prices.price', 'company_id'];
        $query = Product::query();
        $products = $query->join('prices', 'prices.product_id', '=', 'id')
            ->select(...$cols)
            ->orderBy('products.created_at', 'desc');
        if($request->has('q')) {
            $query = join('', ['%', $request->get('q'), '%']);
            $products->where('title', 'ilike', $query)
                ->orWhere('brand', 'ilike', $query);
        }
        $count = Product::count();

        $products = $products->paginate(100);
        $items = $products->toArray()['data'];
        return view('products.index')
            ->withProducts($products)
            ->withItems($items)->withCount($count);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
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
        $product = Product::find($id);
        $prices = $product->prices()->orderBy('created_at', 'desc')->get();
        return view('products.show')
            ->withProduct($product)->withPrices($prices);
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
