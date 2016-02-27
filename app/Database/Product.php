<?php namespace Boodschappen\Database;

use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;

class Product extends Model
{
    public $table = 'products';

    public $fillable = [
        'title', 'brand',
        'unit_size',
        'unit_amount',
        'bulk',
        'generic_product_id',
        'sku',
        'barcode', 'barcode_type',
        'extended_attributes',
    ];

    public $guarded = [
        'id',
    ];

    public $casts = [
        'extended_attributes' => 'json',
        'bulk' => 'integer',
        'unit_amount' => 'float',
        'price' => 'float',
        'company_id' => 'int',
    ];

    private static $categories = null;

    public function prices() {
        return $this->hasMany('Boodschappen\Database\Price');
    }

    public function genericProduct() {
        return $this->belongsTo('Boodschappen\Database\GenericProduct');
    }

    public function getAmountAttribute() {
        $bulk = $this->bulk;
        $unit = $this->unit_size;
        $amount = $this->unit_amount;
        if(is_null($bulk) || $bulk < 1)
            $bulk = 1;

        if($amount <= 0)
            $amount = 1;

        $total = $bulk * $amount;
        return "$total $unit";
    }

    public function comparableProducts() {
        $gid = $this->genericProduct->id;
        $generic_ids = DB::table(DB::raw("generic_products_subtree($gid)"))
            ->select('id')->pluck('id');

        $unit = $this->unit_size[0];
        $amount = $this->unit_amount;
        $margin = $amount / 8;

        $query = Product::select('id', 'title', 'brand', 'unit_amount', 'unit_size')
            ->whereIn('generic_product_id', $generic_ids)
            ->where('id', '!=', $this->id)
            ->where('unit_size', 'ilike', "%$unit%");
        if($amount > 0) {
            $query->where('unit_amount', '<', $amount + $margin)
                  ->where('unit_amount', '>', $amount - $margin);
        }
        return $query;
    }

    public function renderImage() {
        try {
            $attrs = $this->extended_attributes;
            if (is_array($attrs)) {
                if (array_key_exists('image', $attrs) && is_string($attrs['image'])) {
                    return '<img src="' . $attrs['image'] . '"/>';
                } elseif (array_key_exists('images', $attrs)) {
                    $images = $attrs['images'];
                    return '<img src="' . $images[0] . '"/>';
                }
            } elseif(is_object($attrs)) {
                if (property_exists($attrs, 'image') && is_string($attrs->image)) {
                    return '<img src="' . $attrs->image . '"/>';
                } elseif (array_key_exists($attrs, 'images') && count($attrs->images) > 0) {
                    return '<img src="' . $attrs->images[0] . '"/>';
                }
            }
        } catch(\ErrorException $e) {}

        return '';
    }

    /**
     * @param float $price
     * @param integer $company_id
     * @return bool
     */
    public function updatePrice($price, $company_id) {
        $table = $this->getConnection()->table('prices');
        $last_price = $table->where('product_id', '=', $this->getKey())
            ->where('company_id', '=', $company_id)
            ->orderBy('created_at', 'desc')
            ->first(['price']);

        if($last_price && $price == $last_price->price) {
            return true;
        } else {
            try {
                echo "Saving price €$price for product $this->title\n";
                return $table->insert([
                    'product_id' => $this->getKey(),
                    'company_id' => $company_id,
                    'price' => $price,
                ]);
            } catch(QueryException $e) {
                echo 'Caught an invalid insert.';
                echo $e->getMessage();
                echo '====================';
            }
            return false;

        }
    }

    public function categoryFromGenericProduct(GenericProduct $genericProduct) {
        return $this->guessCategory($genericProduct->title, $genericProduct->subcategories());
    }

    public function guessCategory($input = null, array $categories = null) {
        if(is_null($input))
            $input = $this->title;

        if(!static::$categories) {
            echo "Caching categories...\n";
            static::$categories = GenericProduct::find(1)->subcategories();
            echo "Done.\n";
        }

        if(is_null($categories)) {
            $categories = static::$categories;
        }

        // no shortest distance found, yet
        $shortest = -1;

        // loop through words to find the closest
        foreach ($categories as $category) {

            // calculate the distance between the input word,
            // and the current word
            $lev = levenshtein($input, $category->title);

            // check for an exact match
            if ($lev == 0) { // } || str_contains(strtolower($category->title), strtolower($input))) {

                // closest word is this one (exact match)
                $closest = $category;
                $shortest = 0;

                // break out of the loop; we've found an exact match
                break;
            }

            // if this distance is less than the next found shortest
            // distance, OR if a next shortest word has not yet been found
            if ($lev <= $shortest || $shortest < 0) {
                // set the closest match, and shortest distance
                $closest  = $category;
                $shortest = $lev;
            }
        }

        if ($shortest == 0) {
            echo "Exact category found: $closest->title\n";
        } else {
            echo "Category may be: $closest->title\n";
        }

        return $closest;

    }
}
