<?php namespace Boodschappen\Database;

use Illuminate\Database\Eloquent\Model;
use DB;

class ShoppingList extends Model
{
    public $table = 'shopping_lists';

    public $fillable = [
        'title',
    ];

    public $guarded = [
        'id',
    ];

    public $casts = [
        'count' => 'integer',
    ];

    public function products() {
        return $this->belongsToMany('Boodschappen\Database\Product', 'shopping_list_has_product', 'list_id');
    }

    public function user() {
        return $this->belongsTo('Boodschappen\User');
    }
    
    public function totalPrice() {
        return $this->products()
            ->join('prices', 'prices.product_id', '=', 'id')
            ->orderBy('products.created_at', 'desc')
            ->sum('prices.price');
    }
}
