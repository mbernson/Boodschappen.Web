<?php

namespace Boodschappen\Http\Controllers\Api;

use Illuminate\Http\Request;

use Boodschappen\Http\Requests;
use Boodschappen\Http\Controllers\Controller;

class ProductsController extends Controller
{
    public function info($barcode) {
        $output = [];
        return response()->json($output);
    }
}
