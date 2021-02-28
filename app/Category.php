<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'description', 'image'];

    public function media() {
        return $this->belongsTo('App\Media', 'image', 'id');
    }

    public function products() {
        return $this->hasMany('App\Product');
    }
}
