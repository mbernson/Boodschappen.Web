<?php namespace Boodschappen\Http\Controllers\Api;

use Boodschappen\Database\Product;
use Illuminate\Http\Request;
use DB;

class ProductsController extends Controller
{
    public function index(Request $request) {
        $cols = ['id', 'title', 'brand', 'unit_amount', 'unit_size', 'bulk', 'prices.price', 'company_id'];
        $query = Product::query();
        $products = $query->join('prices', 'prices.product_id', '=', 'id')
            ->select(...$cols)
            ->orderBy('products.created_at', 'desc');

        if($request->has('q')) {
            $query = join('', ['%', $request->get('q'), '%']);
            $products->where('title', 'ilike', $query)
                ->orWhere('brand', 'ilike', $query);
        }

        $products->limit(100);

        $results = $products->get();

        return response()->json([
            'count' => count($results),
            'total' => Product::count(),
            'results' => $results,
        ]);
    }

    public function show(Request $request, $id) {
        /** @var Product $product */
        $product = Product::findOrFail($id);

        $response = $product->toArray();
        $response['prices'] = $product->prices()
            ->select('price', DB::raw('title company_title'), 'company_id', 'prices.created_at')
            ->join('companies', 'company_id', '=', 'id')
            ->orderBy('created_at', 'desc')
            ->get()->toArray();

        return response()->json([
            'product' => $response
        ]);
    }
}
