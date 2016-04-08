<?php

namespace Boodschappen\Http\Controllers;

use Boodschappen\Database\Product;
use Boodschappen\Database\Category;
use Illuminate\Http\Request;
use Boodschappen\Http\Requests;

use Auth;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $cols = ['id', 'title', 'brand', 'unit_amount', 'unit_size', 'prices.price', 'company_id'];
        $query = Product::query();
        $products = $query->join('prices', 'prices.product_id', '=', 'id')
            ->select(...$cols)
            ->orderBy('products.created_at', 'desc');

        if($request->has('q')) {
        if($request->has('update')) {
            $this->dispatchSearch($request->get('q'));
        }
            $query = join('', ['%', $request->get('q'), '%']);
            $products->where('title', 'ilike', $query)
                ->orWhere('brand', 'ilike', $query);
        }

        $products = $products->paginate(100);
        return view('products.index')
            ->with([
                'products' => $products,
                'products_count' => Product::count(),
                'categories_count' => Category::count(),
            ]);
    }


    private function dispatchSearch($query) {
        $product_sources = [
            \Boodschappen\Crawling\DataSources\Hoogvliet::class,
            \Boodschappen\Crawling\DataSources\Jumbo::class,
            \Boodschappen\Crawling\DataSources\AlbertHeijn::class,
            \Boodschappen\Crawling\DataSources\Dekamart::class,
        ];
        foreach($product_sources as $klass) {
            $job = new \Boodschappen\Jobs\QueryProductsJob($klass, $query);
            $this->dispatch($job);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        /** @var Product $product */
        $product = Product::find($id);

        $prices = $product->prices()
            ->select('title', 'price', 'prices.created_at')
            ->join('companies', 'company_id', '=', 'id')
            ->orderBy('created_at', 'desc')
            ->get();

        $related = $product->comparableProducts()
            ->select('id', 'title', 'brand', 'price', 'company_id', 'unit_amount', 'unit_size')
            ->join('prices', 'prices.product_id', '=', 'id')
            ->orderBy('price', 'asc')
            ->get();

        return view('products.show')
            ->withProduct($product)
            ->withRelated($related)
            ->withPrices($prices);
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
