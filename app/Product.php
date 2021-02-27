<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\TagDetail;
use App\Category;
use App\Media;

class Product extends Model
{
    protected $fillable = ['name', 'price', 'description', 'media_id', 'tag_id', 'category_id', 'is_show', 'count'];

    protected $casts = ['category_id' => 'integer', 'media_id' => 'integer', 'tag_id' => 'array'];

    public function category() {
        return $this->belongsTo(Category::class);
    }

    public function media() {
        return $this->belongsTo(Media::class);
    }

    public function tags() {
        return $this->belongsToMany(Tag::class);
    }
}