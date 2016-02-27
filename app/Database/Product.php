<?php namespace Boodschappen\Database;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;

class Product extends Model
{
    public $table = 'products';

    public $fillable = ['title', 'brand', 'price', 'unit_size',
        'generic_product_id',
    'extended_attributes',
        'barcode', 'barcode_type'];

    public $casts = [
        'extended_attributes' => 'json',
    ];

    private static $categories = null;

    public function prices() {
        return $this->hasMany('Boodschappen\Database\Price');
    }

    public function renderImage() {
        try {
            if (is_array($this->extended_attributes)) {
                $attrs = $this->extended_attributes;
                if (array_key_exists('image', $attrs) && is_string($attrs['image'])) {
                    return '<img src="' . $attrs['image'] . '"/>';
                } elseif (array_key_exists('images', $attrs)) {
                    $images = $attrs['images'];
                    return '<img src="' . $images[0] . '"/>';
                }
            }
        } catch(\ErrorException $e) {}
        return '';
    }

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

    public function guessCategory($input = null) {
        if(is_null($input))
            $input = $this->title;

        if(!static::$categories) {
            echo "Fetching categories...\n";
            static::$categories = GenericProduct::select('id', 'title')
                ->orderBy('depth', 'asc')
                ->limit(1000)->get();
            echo "Done.\n";
        }

        // no shortest distance found, yet
        $shortest = -1;

        // loop through words to find the closest
        foreach (static::$categories as $category) {

            // calculate the distance between the input word,
            // and the current word
            $lev = levenshtein($input, $category['title']);

            // check for an exact match
            if ($lev == 0) {

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
