<?php namespace Boodschappen\Database;

use DB;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    public $table = 'generic_products';
    public $timestamps = false;

    public function subcategoryIds() {
        return DB::table(DB::raw("generic_products_subtree($this->id)"))
            ->select('id')->pluck('id');
    }

    public function subcategories() {
        return DB::table(DB::raw("generic_products_full_subtree($this->id)"))->get();
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
