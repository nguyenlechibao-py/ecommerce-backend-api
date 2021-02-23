<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TagDetail extends Model
{
    protected $fillable = ['tag_id', 'product_id'];

    public $timestamps = false;
}
