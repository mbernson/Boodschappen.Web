<?php namespace Boodschappen\Database;

use Illuminate\Database\Eloquent\Model;

class GenericProduct extends Model
{
    public $table = 'generic_products';
    public $timestamps = false;

    public function children() {
        return $this->hasMany('Boodschappen\Database\GenericProduct', 'parent_id', 'id');
    }

    public function parent() {
        if(empty($this->parent_id)) {
            return null;
        } else {
            return $this->belongsTo('Boodschappen\Database\GenericProduct', 'parent_id', 'id');
        }
    }
}
