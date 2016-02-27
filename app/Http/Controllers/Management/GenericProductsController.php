<?php

namespace Boodschappen\Http\Controllers\Management;

use Boodschappen\Database\GenericProduct;
use Boodschappen\Database\Product;
use Illuminate\Http\Request;

use Boodschappen\Http\Requests;
use Boodschappen\Http\Controllers\Controller;

class GenericProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $count = GenericProduct::count();
        $products = GenericProduct::select('id', 'title')
            ->whereIn('depth', [0])->get();

        return view('generic_products.index')
            ->withProducts($products)->withCount($count);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if(GenericProduct::create($request->only('title', 'parent_id'))) {
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
        $product = GenericProduct::find($id);
        return view('generic_products.show')->withProduct($product);
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
