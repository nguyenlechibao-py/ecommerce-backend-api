<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Product;

class Order extends Model
{
    protected $fillable = ['name', 'address', 'phone', 'email', 'user_id', 'total', 'quantity'];

    public function products() {
        return $this->belongsToMany(Product::class);
    }
}
