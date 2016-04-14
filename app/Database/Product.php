<?php namespace Boodschappen\Database;

use DB, Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Boodschappen\Domain\Quantity;

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
        'url',
    ];

    public $guarded = [
        'id',
    ];

    public $casts = [
        'extended_attributes' => 'json',
        'bulk' => 'integer',
        'unit_amount' => 'float',
        'price' => 'float',
        'price_per_piece' => 'float',
        'company_id' => 'int',
    ];

    private static $categories = null;

    public function prices() {
        return $this->hasMany('Boodschappen\Database\Price');
    }

    public function category() {
        return $this->belongsTo('Boodschappen\Database\Category', 'generic_product_id');
    }

    public function getAmountAttribute() {
        $bulk = $this->bulk;
        $unit = $this->unit_size;
        $amount = $this->unit_amount;
        if(is_null($bulk) || $bulk < 1)
            $bulk = 1;

        if($amount <= 0)
            $amount = 1;

        if($bulk > 1)
            return "$bulk x $amount $unit";
        else
            return "$amount $unit";
    }

    public function getPricePerPieceAttribute() {
        if(array_key_exists('price_per_piece', $this->attributes)) {
            return $this->attributes['price_per_piece'];
        } else {
            return $this->price / ($this->bulk ?? 1);
        }
    }

    public function comparableProducts(Quantity $quantity = null) {
        $gid = $this->category->id;
        $generic_ids = DB::table(DB::raw("generic_products_subtree($gid)"))
            ->select('id')->pluck('id');

        $unit = $quantity ? $quantity->unit_size[0] : $this->unit_size[0];
        $amount = $quantity ? $quantity->unit_amount : $this->unit_amount;
        $margin = $amount / 8;

        $query = Product::select('id', 'title', 'brand', 'unit_amount', 'unit_size', 'bulk')
            ->whereIn('generic_product_id', $generic_ids)
            ->where('id', '!=', $this->id)
            ->where('unit_size', 'ilike', "$unit%");
        if($amount > 0) {
            $query->where('unit_amount', '<', $amount + $margin)
                ->where('unit_amount', '>', $amount - $margin);
        }
        return $query;
    }

    public function renderImage(): string {
        try {
            $attrs = $this->extended_attributes;
            if (is_array($attrs)) {
                if (array_key_exists('image', $attrs) && is_string($attrs['image'])) {
                    return '<img src="' . $attrs['image'] . '"/>';
                } else if (array_key_exists('image', $attrs) && is_array($attrs['image'])) {
                    return '<img src="' . $attrs['image']['link']['href'] . '"/>';
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
        } catch(\Throwable $e) {
            Log::warning($e);
        }

        return null;
    }

    public function updatePrice(float $price, int $company_id): bool {
        $table = $this->getConnection()->table('prices');
        $last_price = $table->where('product_id', '=', $this->getKey())
            ->where('company_id', '=', $company_id)
            ->orderBy('created_at', 'desc')
            ->first(['price']);

        if($last_price && $price == $last_price->price) {
            return true;
        } else {
            Log::notice("Saving price €$price for product $this->title");
            return $table->insert([
                'product_id' => $this->getKey(),
                'company_id' => $company_id,
                'price' => $price,
            ]);
        }
    }

    public static function cacheCategories() {
        static::$categories = Category::select('id', 'title')
            ->orderBy('depth', 'desc')->limit(2000)->get();
    }

    public function guessCategory(string $input = null, $categories = null) {
        if(!static::$categories) {
            $this->cacheCategories();
        }

        if(is_null($categories)) {
            $categories = static::$categories;
        }

        if(is_null($input)) {
            $input = $this->title;
        }

        try {
            $this->guessExactMatch($input, $categories);
            $guessed = $this->guessLevenshtein($input, $categories);
            // echo "Guessed category: $guessed->title\n";
            return $guessed;
        } catch(CategoryWasFound $result) {
            // echo "Category was matched: {$result->category->title}\n";
            return $result->category;
        }
    }

    private static $parts_regex = '/[\s,\.\/]+/';

    /**
     * @param string $input
     * @param $categories
     * @throws CategoryWasFound
     */
    private function guessLevenshtein(string $input, $categories) {
        // no shortest distance found, yet
        $shortest = -1;
        $closest = null;
        $parts = preg_split(static::$parts_regex, $input);

        // loop through words to find the closest
        foreach ($categories as $category) {
            foreach($parts as $word) {

                // calculate the distance between the input word,
                // and the current word
                $lev = levenshtein($category->title, $word);

                // check for an exact match
                if ($lev == 0) {

                    // closest word is this one (exact match)
                    $closest = $category;
                    $shortest = 0;
                    throw new CategoryWasFound($closest);
                }

                // if this distance is less than the next found shortest
                // distance, OR if a next shortest word has not yet been found
                if ($lev <= $shortest || $shortest < 0) {
                    // set the closest match, and shortest distance
                    $closest = $category;
                    $shortest = $lev;
                }
            }
        }

        return $closest;
    }

    /**
     * @param string $input
     * @param array $categories
     * @throws CategoryWasFound
     */
    private function guessExactMatch($input, $categories) {
        $parts = preg_split(static::$parts_regex, strtolower($input));

        foreach($categories as $category) {
            if(in_array(strtolower($category->title), $parts)) {
                throw new CategoryWasFound($category);
            }
        }
    }
}

final class CategoryWasFound extends \Exception {
    public $category;

    public function __construct($category) {
        $this->category = $category;
    }
}
