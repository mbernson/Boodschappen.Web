<?php namespace Boodschappen\Http\Controllers\Api;

use Boodschappen\Database\Product;
use Illuminate\Http\Request;
use DB;

class ProductsController extends Controller
{
    private $allowed_ordering = [
        'created_at' => 'products.created_at',
        'id' => 'products.id',
        'brand' => 'products.brand',
        'price' => 'price'
    ];

    public function index(Request $request) {
        $cols = ['id', 'title', 'brand', 'unit_amount', 'unit_size', 'bulk', 'prices.price', 'company_id', 'url'];
        $query = Product::query();
        $products = $query->join('prices', 'prices.product_id', '=', 'id')
            ->select(...$cols);

        if($request->has('q')) {
            $query = join('', ['%', $request->get('q'), '%']);
            $products->where('title', 'ilike', $query)
                ->orWhere('brand', 'ilike', $query);
        }
    $max_limit = 100;
    $limit = $request->get('limit', $max_limit);
    $limit = $limit > $max_limit ? $max_limit : $limit;
        $products->limit($limit);

    $default_order = 'created_at';
    $order_by = $request->get('orderBy', $default_order);
    $order_by = in_array($order_by, $this->allowed_ordering) ? $order_by : $default_order;
    $direction = $request->get('direction', 'desc');
    $direction = in_array($direction, ['asc', 'desc']) ? $direction : 'desc';
    $products->orderBy($this->allowed_ordering[$order_by], $direction);

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
            ->select('price', DB::raw('title company_name'), 'company_id', 'prices.created_at')
            ->join('companies', 'company_id', '=', 'id')
            ->orderBy('created_at', 'desc')
            ->get()->toArray();

        return response()->json([
            'product' => $response
        ]);
    }
}
