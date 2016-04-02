<?php

namespace Boodschappen\Http\Controllers;

use Boodschappen\Database\Category;
use Boodschappen\Database\Product;
use Illuminate\Http\Request;

use Boodschappen\Http\Requests;

class CategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $count = Category::count();
        $products = Category::select('id', 'title')
            ->whereIn('depth', [0])->get();

        return view('categories.index')
            ->withProducts($products)->withCount($count);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if(Category::create($request->only('title', 'parent_id'))) {
            return redirect()->back(201);
        } else {
            return 'Some data was misssing.';
        }
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
        $category = Category::find($id);
        $generic_ids = \DB::table(\DB::raw("generic_products_subtree($category->id)"))
            ->select('id')->pluck('id');
        $products = Product::
            select('id', 'title', 'brand', 'price', 'company_id', 'unit_amount', 'unit_size')
            ->join('prices', 'prices.product_id', '=', 'id')
            ->whereIn('generic_product_id', $generic_ids)
            ->orderBy('unit_size', 'asc')
            ->orderBy('unit_amount', 'asc')
            ->orderBy('bulk', 'asc')
            ->orderBy('price', 'asc')
        ->get();
        return view('categories.show')
            ->withProducts($products)
            ->withCategory($category);
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
