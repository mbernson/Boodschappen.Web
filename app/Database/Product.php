<?php namespace Boodschappen\Database;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;

class Product extends Model
{
    public $table = 'products';

    public $fillable = ['title', 'brand', 'price', 'unit_size',
    'extended_attributes',
        'barcode', 'barcode_type'];

    public $casts = [
        'extended_attributes' => 'json',
    ];

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
}
