<?php

namespace Boodschappen\Http\Controllers;

use Illuminate\Http\Request;

use Boodschappen\Http\Requests;
use Boodschappen\Http\Controllers\Controller;
use Boodschappen\Database\ShoppingList;

use Auth, Input, Log;

class ShoppingListsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        $list = ShoppingList::where('user_id', $user->id)->first();
        return view('lists.show')->with('shopping_list', $list);
    }

    public function add() {
        $user_id = Auth::user()->id;
        $list_id = Input::get('list_id');
        $product_id = Input::get('product_id');

        $succ = DB::table('shopping_list_has_product')->insert(compact('list_id', 'product_id'));
        if($succ) {
            return response()->json(compact('list_id', 'product_id'));
        } else {
            return abort(500);
        }
    }

    public function remove() {
        $user_id = Auth::user()->id;
        $list_id = Input::get('list_id');
        $product_id = Input::get('product_id');

        $succ = DB::table('shopping_list_has_product')->delete(compact('list_id', 'product_id'));
        if($succ) {
            return response()->json(compact('list_id', 'product_id'));
        } else {
            return abort(500);
        }
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
        $user = Auth::user();
        $list = ShoppingList::where('user_id', $user->id)
            ->where('id', $id)
            ->first();
        return view('lists.show')->with('shopping_list', $list);
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
