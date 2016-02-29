<?php namespace Boodschappen\Database;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Category extends Model
{
    public $table = 'generic_products';
    public $timestamps = false;

    public $fillable = [
        'parent_id',
        'title',
    ];

    public function subcategoryIds() {
        return DB::table(DB::raw("generic_products_subtree($this->id)"))
            ->select('id')->pluck('id');
    }

    /**
     * @return \Illuminate\Database\Query\Builder
     */
    public function subcategories() {
        return DB::table(DB::raw("generic_products_full_subtree($this->id)"));
    }

    public function children() {
        return $this->hasMany('Boodschappen\Database\Category', 'parent_id', 'id');
    }

    public function parent() {
        if(empty($this->parent_id)) {
            return null;
        } else {
            return $this->belongsTo('Boodschappen\Database\Category', 'parent_id', 'id');
        }
    }
}
