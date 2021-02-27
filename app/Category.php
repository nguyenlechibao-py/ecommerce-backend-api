<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'description', 'media_id'];

    public function media() {
        return $this->belongsTo('App\Media');
    }
}
